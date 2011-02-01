<?php

require_once 'Swat/SwatDate.php';
require_once 'Admin/exceptions/AdminNotFoundException.php';
require_once 'Admin/pages/AdminDBEdit.php';
require_once 'Inquisition/dataobjects/InquisitionQuestion.php';

/**
 * @package   Inquisition
 * @copyright 2011 silverorange
 */
class InquisitionInquisitionEditQuestion extends AdminDBEdit
{
	// {{{ protected properties

	/**
	 * @var Inquisition
	 */
	protected $inquisition;

	/**
	 * @var InquisitionQuestion
	 */
	protected $question;

	// }}}

	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		parent::initInternal();
		$this->ui->loadFromXML(dirname(__FILE__).'/edit-question.xml');
		$this->initQuestion();

		$form = $this->ui->getWidget('edit_form');
		$form->addHiddenField('inquisition', $this->inquisition->id);

		$view = $this->ui->getWidget('table_view');
		$view->model = $this->getOptionsTableStore();
	}

	// }}}
	// {{{ protected function initQuestion()

	protected function initQuestion()
	{
		$class = SwatDBClassMap::get('InquisitionQuestion');
		$this->question = new $class;
		$this->question->setDatabase($this->app->db);

		if ($this->id !== null) {
			if (!$this->question->load($this->id)) {
				throw new AdminNotFoundException(
					sprintf('Question with id ‘%s’ not found.', $this->id));
			}

			$this->inquisition = $this->question->inquisition;
		} else {
			$class = SwatDBClassMap::get('Inquisition');
			$this->inquisition = new $class;
			$this->inquisition->setDatabase($this->app->db);

			$inquisition_id = intval(SiteApplication::initVar('inquisition'));

			if (!$this->inquisition->load($inquisition_id)) {
				throw new AdminNotFoundException('Unable to load inquisition.');
			}
		}
	}

	// }}}
	// {{{ protected function getOptionsTableStore()

	protected function getOptionsTableStore()
	{
		$store = new SwatTableStore();

		foreach ($this->question->options as $option) {
			$ds = new SwatDetailsStore($option);
			$store->add($ds);
		}

		return $store;
	}

	// }}}

	// process phase
	// {{{ protected function saveDBData()

	protected function saveDBData()
	{
		$values = $this->ui->getValues(array(
			'bodytext',
			//'correct_option',
		));

		if ($this->question->id === null) {
			$now = new SwatDate();
			$now->toUTC();
			$this->question->inquisition = $this->inquisition->id;
		}

		$this->question->bodytext       = $values['bodytext'];
		$this->question->question_type  = InquisitionQuestion::TYPE_RADIO_LIST;
		//$this->question->correct_option = $values['correct_option'];

		if ($this->question->isModified()) {
			$this->question->save();

			$message = new SwatMessage('Question has been saved.');
			$this->app->messages->add($message);
		}

		$this->updateOptions();
		$this->removeOptions();
		$this->addOptions();
	}

	// }}}
	// {{{ protected function updateOptions()

	protected function updateOptions()
	{
		$count = 0;

		$view = $this->ui->getWidget('table_view');
		$replicator = $view->getColumn('title')->getRenderer('title_widget');

		foreach ($this->question->options as $option) {
			$widget = $replicator->getWidget($option->id);
			if ($widget !== null && $widget->value != $option->title) {
				$option->title = $widget->value;
			}
		}

		foreach ($this->question->options as $option)
			$option->save();
	}

	// }}}
	// {{{ protected function removeOptions()

	protected function removeOptions()
	{
		$items = $this->ui->getWidget('table_view')->getSelection();

		$ids = array();
		foreach ($items as $id)
			$ids[] = $id;

		$sql = sprintf('delete from InquisitionQuestionOption where id in (%s)',
				$this->app->db->datatype->implodeArray($ids, 'integer'));

		SwatDB::exec($this->app->db, $sql);
	}

	// }}}
	// {{{ protected function addOptions()

	protected function addOptions()
	{
		$count = 0;

		$view = $this->ui->getWidget('table_view');
		$input_row = $view->getRow('input_row');

		$displayorder_base = SwatDB::queryOne($this->app->db,
			sprintf('select coalesce(max(displayorder), 0)
				from InquisitionQuestionOption where question = %s',
				$this->app->db->quote($this->question->id, 'integer')));

		$replicators = $input_row->getReplicators();
		foreach ($replicators as $replicator_id) {
			if (!$input_row->rowHasMessage($replicator_id)) {
				$count++;

				$title = $input_row->getWidget(
					'title', $replicator_id)->value;

				if ($title == '')
					continue;

				$class = SwatDBClassMap::get('InquisitionQuestionOption');
				$option = new $class;
				$option->setDatabase($this->app->db);
				$option->displayorder = $displayorder_base + $count;
				$option->question = $this->question->id;
				$option->title = $title;

				$option->save();

				$input_row->removeReplicatedRow($replicator_id);
			}
		}

		return $count;
	}

	// }}}

	// build phase
	// {{{ protected function loadDBData()

	protected function loadDBData()
	{
		$this->ui->setValues(get_object_vars($this->question));

		$form = $this->ui->getWidget('edit_form');
		$form->addHiddenField('inquisition', $this->inquisition->id);
	}

	// }}}
}

?>
