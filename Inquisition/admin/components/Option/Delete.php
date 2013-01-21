<?php

require_once 'SwatDB/SwatDB.php';
require_once 'SwatI18N/SwatI18NLocale.php';
require_once 'Admin/AdminListDependency.php';
require_once 'Admin/AdminSummaryDependency.php';
require_once 'Admin/pages/AdminDBDelete.php';

/**
 * Delete confirmation page for options
 *
 * @package   Inquisition
 * @copyright 2012-2013 silverorange
 */
class InquisitionOptionDelete extends AdminDBDelete
{
	// {{{ protected properties

	/**
	 * @var AcademyQuestion
	 */
	protected $question;

	// }}}

	// helper methods
	// {{{ public function setId()

	public function setId($id)
	{
		$class_name = SwatDBClassMap::get('InquisitionQuestion');

		$this->question = new $class_name();
		$this->question->setDatabase($this->app->db);

		if ($id == '') {
			throw new AdminNotFoundException('Question id not provided.');
		}

		if (!$this->question->load($id)) {
			throw new AdminNotFoundException(
				sprintf('Question with id ‘%s’ not found.', $id)
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

		$sql = sprintf('delete from InquisitionQuestionOption where id in (%s)',
			$this->getItemList('integer'));

		$num = SwatDB::exec($this->app->db, $sql);

		$this->app->messages->add(
			new SwatMessage(
				sprintf(
					ngettext(
						'One option has been deleted.',
						'%s options have been deleted.',
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
		AdminDBConfirmation::relocate();
	}

	// }}}

	// build phase
	// {{{ protected function buildInternal()

	protected function buildInternal()
	{
		parent::buildInternal();

		$item_list = $this->getItemList('integer');

		$dep = new AdminListDependency();
		$dep->setTitle('option', 'options');
		$dep->entries = AdminListDependency::queryEntries($this->app->db,
			'InquisitionQuestionOption', 'id', null, 'text:title',
			'displayorder, id', 'id in ('.$item_list.')',
			AdminDependency::DELETE);

		// check images dependencies
		$dep_images = new AdminSummaryDependency();
		$dep_images->setTitle('image', 'images');
		$dep_images->summaries = AdminSummaryDependency::querySummaries(
			$this->app->db, 'InquisitionQuestionOptionImageBinding',
			'integer:image', 'integer:question_option',
			'question_option in ('.$item_list.')', AdminDependency::DELETE);

		$dep->addDependency($dep_images);

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

		$this->navbar->createEntry(
			$this->question->inquisition->title,
			sprintf(
				'Inquisition/Details?id=%s',
				$this->question->inquisition->id
			)
		);

		$this->navbar->createEntry(
			sprintf(
				Inquisition::_('Question %s'),
				$this->question->getPosition($this->inquisition)
			),
			sprintf(
				'Question/Details?id=%s',
				$this->question->id
			)
		);

		$this->navbar->createEntry(
			ngettext(
				'Delete Option',
				'Delete Options',
				$this->getItemCount()
			)
		);
	}

	// }}}
}

?>
