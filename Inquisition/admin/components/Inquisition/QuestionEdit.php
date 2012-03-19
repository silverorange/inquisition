<?php

require_once 'Swat/SwatDate.php';
require_once 'Swat/SwatTableStore.php';
require_once 'Swat/SwatDetailsStore.php';
require_once 'Admin/exceptions/AdminNotFoundException.php';
require_once 'Admin/pages/AdminDBEdit.php';
require_once 'Inquisition/dataobjects/InquisitionQuestion.php';
require_once 'Inquisition/admin/InquisitionCorrectOptionRadioButton.php';

/**
 * @package   Inquisition
 * @copyright 2011 silverorange
 */
class InquisitionInquisitionQuestionEdit extends AdminDBEdit
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

	protected $ui_xml =
		'Inquisition/admin/components/Inquisition/question-edit.xml';

	// }}}

	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		parent::initInternal();
		$this->ui->loadFromXML($this->ui_xml);
		$this->initQuestion();

		if ($this->question->id === null) {
			$view = $this->ui->getWidget('question_option_table_view');
			$row  = $view->getRow('input_row');
			$row->number = 4;
		}

		$form = $this->ui->getWidget('edit_form');
		$form->addHiddenField('inquisition', $this->inquisition->id);

		$view = $this->ui->getWidget('question_option_table_view');
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
			$class = SwatDBClassMap::get('InquisitionInquisition');
			$this->inquisition = new $class;
			$this->inquisition->setDatabase($this->app->db);

			$inquisition_id = intval(SiteApplication::initVar('inquisition'));

			if (!$this->inquisition->load($inquisition_id)) {
				throw new AdminNotFoundException('Unable to load inquisition.');
			}

			$this->ui->getWidget('another_button')->visible = true;
			$this->ui->getWidget('submit_button')->title = 'Done';
		}
	}

	// }}}
	// {{{ protected function getOptionsTableStore()

	protected function getOptionsTableStore()
	{
		$store = new SwatTableStore();

		foreach ($this->question->options as $option) {
			$ds = new SwatDetailsStore($option);

			$ds->correct_option =
				($option->id === $this->question->getInternalValue('correct_option'));

			$store->add($ds);
		}

		return $store;
	}

	// }}}

	// process phase
	// {{{ protected function validate()

	protected function validate()
	{
		parent::validate();

		$field = $this->ui->getWidget('options_field');

		$view = $this->ui->getWidget('question_option_table_view');
		$items = $view->getSelection();

		$has_correct   = false;
		$current_count = count($this->question->options);
		$removed_count = count($items);
		$added_count   = 0;

		// check for correct option in current options
		$correct_column = $view->getColumn('correct_option');
		if ($correct_column->isVisible()) {
			$correct_option_renderer = $correct_column->getFirstRenderer();
			foreach ($this->question->options as $option) {
				// don't check removed options
				if (!$items->contains($option->id)) {
					$correct = $correct_option_renderer->getWidget($option->id);
					if ($correct->isVisible() && !$has_correct) {
						$has_correct = $correct->value;
					}
				}
			}
		} else {
			// correct column is hidden, don't bother checking
			$has_correct = true;
		}

		// get added added row count and check for correct option in added rows
		$input_row = $view->getRow('input_row');
		$replicators = $input_row->getReplicators();
		foreach ($replicators as $replicator_id) {
			if (!$input_row->rowHasMessage($replicator_id)) {
				$title = $input_row->getWidget(
					'title', $replicator_id)->value;

				if ($title == '') {
					continue;
				}

				$added_count++;
				$correct = $input_row->getWidget(
					'correct_option', $replicator_id);

				if ($correct->isVisible() && !$has_correct) {
					$has_correct = $correct->value;
				}
			}
		}

		if ($current_count + $added_count - $removed_count <= 0) {
			$message = new SwatMessage(
				Inquisition::_(
					'Question requires at least one question option.'));

			$field->addMessage($message);
		} elseif (!$has_correct) {
			$message = new SwatMessage(
				Inquisition::_(
					'Question requires a correct option to be selected.'));

			$field->addMessage($message);
		}

	}

	// }}}
	// {{{ protected function saveDBData()

	protected function saveDBData()
	{
		$values = $this->ui->getValues(array(
			'bodytext',
		));

		if ($this->question->id === null) {
			$now = new SwatDate();
			$now->toUTC();
			$this->question->inquisition = $this->inquisition->id;

			// set displayorder so the new question appears at the end of the
			// list of the current questions by default.
			$sql = sprintf(
				'select max(displayorder) from InquisitionQuestion
				where inquisition = %s',
				$this->app->db->quote($this->inquisition->id, 'integer'));

			$max_displayorder = SwatDB::queryOne($this->app->db, $sql);
			$new_displayorder = floor(($max_displayorder + 10) / 10) * 10;
			$this->question->displayorder = $new_displayorder;
		}

		$this->question->bodytext      = $values['bodytext'];
		$this->question->question_type = InquisitionQuestion::TYPE_RADIO_LIST;

		$this->updateOptions();
		$this->removeOptions();

		if ($this->question->isModified()) {
			$this->question->save();

			$message = new SwatMessage(
				Inquisition::_('Question has been saved.'));

			$this->app->messages->add($message);
		}

		$this->addOptions();

		// save again so that the correct option is saved from addOptions
		$this->question->save();
	}

	// }}}
	// {{{ protected function updateOptions()

	protected function updateOptions()
	{
		$count = 0;

		$view = $this->ui->getWidget('question_option_table_view');
		$title_column = $view->getColumn('title');
		$title_renderer = $title_column->getFirstRenderer();
		$correct_column = $view->getColumn('correct_option');
		$correct_option_renderer = $correct_column->getFirstRenderer();

		foreach ($this->question->options as $option) {
			$title_widget = $title_renderer->getWidget($option->id);
			if ($title_widget !== null && $title_widget->value != $option->title)
				$option->title = $title_widget->value;

			if ($correct_column->isVisible()) {
				$radio = $correct_option_renderer->getWidget($option->id);
				if ($radio !== null && $radio->value) {
					$this->question->correct_option = $option;
				}
			}
		}

		foreach ($this->question->options as $option) {
			$option->save();
		}
	}

	// }}}
	// {{{ protected function removeOptions()

	protected function removeOptions()
	{
		$view  = $this->ui->getWidget('question_option_table_view');
		$items = $view->getSelection();

		$ids = array();
		foreach ($items as $id) {
			$ids[] = $id;
		}

		$sql = sprintf('delete from InquisitionQuestionOption where id in (%s)',
			$this->app->db->datatype->implodeArray($ids, 'integer'));

		SwatDB::exec($this->app->db, $sql);
	}

	// }}}
	// {{{ protected function addOptions()

	protected function addOptions()
	{
		$count = 0;

		$view = $this->ui->getWidget('question_option_table_view');
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

				$correct_widget = $input_row->getWidget(
					'correct_option', $replicator_id);

				$is_correct_option = ($correct_widget->isVisible() &&
					$correct_widget->value);

				if ($is_correct_option) {
					$this->question->correct_option = $option;
				}

				$input_row->removeReplicatedRow($replicator_id);
			}
		}

		return $count;
	}

	// }}}
	// {{{ protected function relocate()

	protected function relocate()
	{
		$button = $this->ui->getWidget('another_button');

		if ($button->hasBeenClicked()) {
			$url = sprintf('%s?inquisition=%s',
				$this->source, $this->inquisition->id);
		} else {
			$url = sprintf('%s/Details?id=%s',
				$this->getComponentName(),
				$this->inquisition->id);
		}

		$this->app->relocate($url);
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
	// {{{ protected function buildNavBar()

	protected function buildNavBar()
	{
		parent::buildNavBar();

		$this->navbar->popEntry();

		$this->navbar->addEntry(new SwatNavBarEntry($this->inquisition->title,
			sprintf('%s/Details?id=%s',
				$this->getComponentName(),
				$this->inquisition->id)));

		if ($this->id === null) {
			$this->navbar->createEntry(Inquisition::_('New Question'));
		} else {
			$this->navbar->createEntry(Inquisition::_('Edit Question'));
		}
	}

	// }}}

	// finalize phase
	// {{{ public function finalize()

	public function finalize()
	{
		parent::finalize();
		$this->layout->addHtmlHeadEntry(
			'packages/inquisition/admin/styles/inquisition-question-edit.css');
	}

	// }}}
}

?>
