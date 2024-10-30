<?php

/**
 * Delete confirmation page for options
 *
 * @package   Inquisition
 * @copyright 2012-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class InquisitionOptionDelete extends AdminDBDelete
{
	// {{{ protected properties

	/**
	 * @var AcademyQuestion
	 */
	protected $question;

	/**
	 * @var InquisitionInquisition
	 */
	protected $inquisition;

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
	// {{{ public function setInquisition()

	public function setInquisition(InquisitionInquisition $inquisition = null)
	{
		if ($inquisition instanceof InquisitionInquisition) {
			$this->inquisition = $inquisition;

			$form = $this->ui->getWidget('confirmation_form');
			$form->addHiddenField('inquisition_id', $this->inquisition->id);
		}
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

		$inquisition_id = $form->getHiddenField('inquisition_id');
		if ($inquisition_id != '') {
			$inquisition = $this->loadInquisition($inquisition_id);
			$this->setInquisition($inquisition);
		}
	}

	// }}}
	// {{{ protected function loadInquisition()

	protected function loadInquisition($inquisition_id)
	{
		$class = SwatDBClassMap::get('InquisitionInquisition');
		$inquisition = new $class;
		$inquisition->setDatabase($this->app->db);

		if (!$inquisition->load($inquisition_id)) {
			throw new AdminNotFoundException(
				sprintf(
					'Inquisition with id ‘%s’ not found.',
					$inquisition_id
				)
			);
		}

		return $inquisition;
	}

	// }}}

	// process phase
	// {{{ protected function processDBData()

	protected function processDBData(): void
	{
		parent::processDBData();

		$locale = SwatI18NLocale::get();

		$sql = sprintf(
			'delete from InquisitionQuestionOption where id in (%s)',
			$this->getItemList('integer')
		);

		$num = SwatDB::exec($this->app->db, $sql);

		$this->app->messages->add(
			new SwatMessage(
				sprintf(
					Inquisition::ngettext(
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
		$dep->setTitle(
			Inquisition::_('option'),
			Inquisition::_('options')
		);

		$dep->entries = AdminListDependency::queryEntries(
			$this->app->db,
			'InquisitionQuestionOption', 'id', null, 'text:title',
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
			$this->app->db, 'InquisitionQuestionOptionImageBinding',
			'integer:image', 'integer:question_option',
			'question_option in ('.$item_list.')', AdminDependency::DELETE
		);

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
			$this->getQuestionTitle(),
			sprintf(
				'Question/Details?id=%s%s',
				$this->question->id,
				$this->getLinkSuffix()
			)
		);

		$this->navbar->createEntry(
			Inquisition::ngettext(
				'Delete Option',
				'Delete Options',
				$this->getItemCount()
			)
		);
	}

	// }}}
	// {{{ protected function getQuestionTitle()

	protected function getQuestionTitle()
	{
		// TODO: Update this with some version of getPosition().
		return Inquisition::_('Question');
	}

	// }}}
	// {{{ protected function getLinkSuffix()

	protected function getLinkSuffix()
	{
		$suffix = null;
		if ($this->inquisition instanceof InquisitionInquisition) {
			$suffix = sprintf(
				'&inquisition=%s',
				$this->inquisition->id
			);
		}

		return $suffix;
	}

	// }}}
}

?>
