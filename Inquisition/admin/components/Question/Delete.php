<?php

require_once 'SwatDB/SwatDB.php';
require_once 'SwatI18N/SwatI18NLocale.php';
require_once 'Admin/AdminListDependency.php';
require_once 'Admin/AdminSummaryDependency.php';
require_once 'Admin/pages/AdminDBDelete.php';

/**
 * Delete confirmation page for questions
 *
 * @package   Inquisition
 * @copyright 2011-2015 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class InquisitionQuestionDelete extends AdminDBDelete
{
	// {{{ protected properties

	/**
	 * @var AcademyInquisition
	 */
	protected $inquisition;

	// }}}

	// helper methods
	// {{{ public function setId()

	public function setId($id)
	{
		$class_name = SwatDBClassMap::get('InquisitionInquisition');

		$this->inquisition = new $class_name();
		$this->inquisition->setDatabase($this->app->db);

		if ($id == '') {
			throw new AdminNotFoundException(
				'Inquisition id not provided.'
			);
		}

		if (!$this->inquisition->load($id)) {
			throw new AdminNotFoundException(
				sprintf(
					'Inquisition with id ‘%s’ not found.',
					$id
				)
			);
		}

		$form = $this->ui->getWidget('confirmation_form');
		$form->addHiddenField('id', $id);
	}

	// }}}

	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		parent::initInternal();

		$form = $this->ui->getWidget('confirmation_form');
		$id = $form->getHiddenField('id');
		if ($id != '') {
			$this->setId($id);
		}
	}

	// }}}

	// process phase
	// {{{ protected function processDBData()

	protected function processDBData()
	{
		parent::processDBData();

		$locale = SwatI18NLocale::get();

		$sql = sprintf(
			'delete from InquisitionQuestion where id in (%s)',
			$this->getItemList('integer')
		);

		$num = SwatDB::exec($this->app->db, $sql);

		$this->app->messages->add(
			new SwatMessage(
				sprintf(
					Inquisition::ngettext(
						'One question has been deleted.',
						'%s questions have been deleted.',
						$num
					),
					$locale->formatNumber($num)
				)
			)
		);
	}

	// }}}
	// {{{ protected function relocate()

	protected function relocate()
	{
		if ($this->inquisition instanceof InquisitionInquisition) {
			$this->app->relocate(
				sprintf(
					'Inquisition/Details?id=%s',
					$this->inquisition->id
				)
			);
		} else {
			parent::relocate();
		}
	}

	// }}}

	// build phase
	// {{{ protected function buildInternal()

	protected function buildInternal()
	{
		parent::buildInternal();

		$item_list = $this->getItemList('integer');

		$dep = new AdminListDependency();
		$dep->setTitle(
			Inquisition::_('question'),
			Inquisition::_('questions')
		);

		$dep->entries = AdminListDependency::queryEntries(
			$this->app->db,
			'InquisitionQuestion', 'id', null, 'text:bodytext',
			'displayorder, id', 'id in ('.$item_list.')',
			AdminDependency::DELETE
		);

		// check images dependencies
		$dep_images = new AdminSummaryDependency();
		$dep_images->setTitle(
			Inquisition::_('image'),
			Inquisition::_('images')
		);

		$dep_images->summaries = AdminSummaryDependency::querySummaries(
			$this->app->db, 'InquisitionQuestionImageBinding',
			'integer:image', 'integer:question', 'question in ('.$item_list.')',
			AdminDependency::DELETE
		);

		// check option dependencies
		$dep_options = new AdminListDependency();
		$dep_options->setTitle(
			Inquisition::_('option'),
			Inquisition::_('options')
		);

		$dep_options->entries = AdminListDependency::queryEntries(
			$this->app->db, 'InquisitionQuestionOption', 'integer:id',
			'integer:question', 'text:title', 'displayorder, id',
			'question in ('.$item_list.')', AdminDependency::DELETE
		);

		$subquery = 'select id from InquisitionQuestionOption
			where question in ('.$item_list.')';

		$dep_option_images = new AdminSummaryDependency();
		$dep_option_images->setTitle(
			Inquisition::_('image'),
			Inquisition::_('images')
		);

		$dep_option_images->summaries = AdminSummaryDependency::querySummaries(
			$this->app->db, 'InquisitionQuestionOptionImageBinding',
			'integer:image', 'integer:question_option',
			'question_option in ('.$subquery.')', AdminDependency::DELETE
		);

		$dep_options->addDependency($dep_option_images);

		// check option hints
		$dep_hints = new AdminSummaryDependency();
		$dep_hints->setTitle(
			Inquisition::_('hint'),
			Inquisition::_('hints')
		);

		$dep_hints->entries = AdminSummaryDependency::querySummaries(
			$this->app->db, 'InquisitionQuestionHint', 'integer:id',
			'integer:question', 'question in ('.$item_list.')',
			AdminDependency::DELETE
		);

		$dep->addDependency($dep_images);
		$dep->addDependency($dep_options);
		$dep->addDependency($dep_hints);

		foreach ($dep->entries as $entry) {
			$entry->title = SwatString::condense($entry->title);
		}

		$message = $this->ui->getWidget('confirmation_message');
		$message->content = $dep->getMessage();
		$message->content_type = 'text/xml';

		if ($dep->getStatusLevelCount(AdminDependency::DELETE) == 0) {
			$this->switchToCancelButton();
		}
	}

	// }}}
	// {{{ protected function buildNavBar()

	protected function buildNavBar()
	{
		parent::buildNavBar();

		$this->navbar->popEntry();

		if ($this->inquisition instanceof InquisitionInquisition) {
			$this->navbar->createEntry(
				$this->inquisition->title,
				sprintf(
					'Inquisition/Details?id=%s',
					$this->inquisition->id
				)
			);
		}

		$this->navbar->createEntry(
			Inquisition::ngettext(
				'Delete Question',
				'Delete Questions',
				$this->getItemCount()
			)
		);
	}

	// }}}
}

?>
