<?php


require_once 'Swat/SwatTableStore.php';
require_once 'Swat/SwatDetailsStore.php';
require_once 'SwatDB/SwatDB.php';
require_once 'SwatDB/SwatDBClassMap.php';
require_once 'Admin/pages/AdminDBDelete.php';
require_once 'Admin/AdminListDependency.php';
require_once 'Inquisition/dataobjects/InquisitionQuestionHintWrapper.php';

/**
 * Delete confirmation page for question hints
 *
 * @package   Inquisition
 * @copyright 2013 silverorange
 */
class InquisitionQuestionHintDelete extends AdminDBDelete
{
	// {{{ protected properties

	/**
	 * @var InquisitonQuestionHintWrapper
	 */
	protected $hints;

	/**
	 * @var InquisitonQuestion
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
			throw new AdminNotFoundException(
				'Question id not provided.'
			);
		}

		if (!$this->question->load($id)) {
			throw new AdminNotFoundException(
				sprintf(
					'Question with id ‘%s’ not found.',
					$id
				)
			);
		}

		$form = $this->ui->getWidget('confirmation_form');
		$form->addHiddenField('id', $id);
	}

	// }}}
	// {{{ public function setItems()

	public function setItems($items, $extended_selected = false)
	{
		parent::setItems($items, $extended_selected);

		$sql = sprintf(
			'select InquisitionQuestionHint.*
			from InquisitionQuestionHint where id in (%s)',
			$this->getItemList('integer')
		);

		$this->hints = SwatDB::query(
			$this->app->db,
			$sql,
			SwatDBClassMap::get('InquisitionQuestionHintWrapper')
		);
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

	protected function processDBData()
	{
		parent::processDBData();

		$sql = sprintf(
			'delete from InquisitionQuestionHint where id in (%s)',
			$this->getItemList('integer')
		);

		$delete_count = SwatDB::exec($this->app->db, $sql);
		$locale = SwatI18NLocale::get();

		$this->app->messages->add(
			new SwatMessage(
				sprintf(
					Inquisition::ngettext(
						'One hint has been deleted.',
						'%s hints have been deleted.',
						$delete_count
					),
					$locale->formatNumber($delete_count)
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
			Inquisition::_('hint'),
			Inquisition::_('hints')
		);

		$dep->entries = AdminListDependency::queryEntries(
			$this->app->db,
			'InquisitionQuestionHint', 'id', null, 'text:bodytext',
			'displayorder, id', 'id in ('.$item_list.')',
			AdminDependency::DELETE
		);

		foreach ($dep->entries as $entry) {
			$entry->title = SwatString::condense($entry->title, 50);
		}

		$message = $this->ui->getWidget('confirmation_message');
		$message->content = $dep->getMessage();
		$message->content_type = 'text/xml';

		if ($dep->getStatusLevelCount(AdminDependency::DELETE) == 0) {
			$this->switchToCancelButton();
		}
	}

	// }}}
	// {{{ protected function buildForm()

	protected function buildForm()
	{
		parent::buildForm();

		$yes_button = $this->ui->getWidget('yes_button');
		$yes_button->title = Inquisition::_('Delete');
	}

	// }}}
	// {{{ protected function buildNavBar()

	protected function buildNavBar()
	{
		parent::buildNavBar();

		$this->navbar->popEntries(2);

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
				'Delete Hint',
				'Delete Hints',
				count($this->hints)
			)
		);
	}

	// }}}
	// {{{ protected function getQuestionTitle()

	protected function getQuestionTitle()
	{
		return ($this->inquisition instanceof InquisitionInquisition) ?
			sprintf(
				Inquisition::_('Question %s'),
				$this->question->getPosition($this->inquisition)
			) :
			Inquisition::_('Question');
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
