<?php

require_once 'SwatDB/SwatDB.php';
require_once 'SwatI18N/SwatI18NLocale.php';
require_once 'Admin/AdminListDependency.php';
require_once 'Admin/pages/AdminDBDelete.php';
require_once 'Inquisition/dataobjects/InquisitionInquisition.php';
require_once 'Inquisition/dataobjects/InquisitionQuestion.php';

/**
 * Delete confirmation page for questions hints
 *
 * @package   Inquisition
 * @copyright 2013 silverorange
 */
class InquisitionQuestionHintDelete extends AdminDBDelete
{
	// {{{ protected properties

	/**
	 * @var InquisitionInquisition
	 */
	protected $inquisition;

	/**
	 * @var InquisitionQuestion
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

		parent::setId($id);
	}

	// }}}
	// {{{ public function setInquisition()

	public function setInquisition(InquisitionInquisition $inquisition)
	{
		$this->inquisition = $inquisition;
		$this->inquisition->setDatabase($this->app->db);

		// TODO - load by id
		return;
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
		// TODO
		// $form->addHiddenField('inquisition', $this->inquisition->id);
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
			'delete from InquisitionQuestionHint where id in (%s)',
			$this->getItemList('integer')
		);

		$num = SwatDB::exec($this->app->db, $sql);

		$this->app->messages->add(
			new SwatMessage(
				sprintf(
					ngettext(
						'One hint has been deleted.',
						'%s hints have been deleted.',
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
		$this->app->relocate(
			sprintf(
				'Question/Details?id=%s',
				$this->question->id
			)
		);
	}

	// }}}

	// build phase
	// {{{ protected function buildInternal()

	protected function buildInternal()
	{
		parent::buildInternal();

		$item_list = $this->getItemList('integer');

		$dep = new AdminListDependency();
		$dep->setTitle('hint', 'hints');
		$dep->entries = AdminListDependency::queryEntries(
			$this->app->db,
			'InquisitionQuestionHint', 'id', null, 'text:bodytext',
			'displayorder, id', 'id in ('.$item_list.')',
			AdminDependency::DELETE
		);

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

		// TODO
		if (1==0) {
			$this->navbar->createEntry(
				$this->inquisition->title,
				sprintf(
					'Inquisition/Details?id=%s',
					$this->inquisition->id
				)
			);

			$this->navbar->createEntry(
				ngettext(
					'Delete Question',
					'Delete Questions',
					$this->getItemCount()
				)
			);
		}
	}

	// }}}
}

?>
