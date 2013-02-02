<?php

require_once 'Swat/SwatDate.php';
require_once 'Swat/SwatTableStore.php';
require_once 'Swat/SwatDetailsStore.php';
require_once 'Admin/exceptions/AdminNotFoundException.php';
require_once 'Admin/pages/AdminDBEdit.php';
require_once 'Inquisition/dataobjects/InquisitionQuestion.php';
require_once 'Inquisition/admin/InquisitionCorrectOptionRadioButton.php';

/**
 * Page for creating new questions
 *
 * @package   Inquisition
 * @copyright 2011-2013 silverorange
 */
class InquisitionQuestionAdd extends AdminDBEdit
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

	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		parent::initInternal();

		$this->ui->loadFromXML($this->getUiXml());

		$this->initInquisition();
		$this->initQuestion();

		// Empty table store needed or the input rows won't display.
		$view = $this->ui->getWidget('question_option_table_view');
		$view->model = new SwatTableStore();

		$row  = $view->getRow('input_row');
		$row->number = 4;
	}

	// }}}
	// {{{ protected function initInquisition()

	protected function initInquisition()
	{
		if ($this->id != '') {
			$class = SwatDBClassMap::get('InquisitionInquisition');
			$this->inquisition = new $class;
			$this->inquisition->setDatabase($this->app->db);

			if (!$this->inquisition->load($this->id)) {
				throw new AdminNotFoundException(
					sprintf(
						'Inquisition with id ‘%s’ not found.',
						$this->id
					)
				);
			}
		}
	}

	// }}}
	// {{{ protected function initQuestion()

	protected function initQuestion()
	{
		$class_name = SwatDBClassMap::get('InquisitionQuestion');
		$this->question = new $class_name();
		$this->question->setDatabase($this->app->db);
	}

	// }}}
	// {{{ protected function getUiXml()

	protected function getUiXml()
	{
		return 'Inquisition/admin/components/Question/add.xml';
	}

	// }}}

	// process phase
	// {{{ protected function validate()

	protected function validate()
	{
		parent::validate();

		$field = $this->ui->getWidget('options_field');

		$view = $this->ui->getWidget('question_option_table_view');

		$has_correct   = false;
		$added_count   = 0;

		// check for correct option in current options
		$correct_column = $view->getColumn('correct_option');
		if ($correct_column->isVisible()) {
			$correct_option_renderer = $correct_column->getFirstRenderer();
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

		if ($added_count <= 0) {
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
		$this->updateQuestion();
		$this->addOptions($this->question);

		$this->question->save();

		if ($this->inquisition instanceof InquisitionInquisition) {
			$this->inquisition->questions->add($this->question);
			$this->inquisition->save();
		}

		$this->app->messages->add(
			new SwatMessage(
				Inquisition::_('Question has been saved.')
			)
		);
	}

	// }}}
	// {{{ protected function updateQuestion()

	protected function updateQuestion()
	{
		$values = $this->ui->getValues(array(
			'bodytext',
		));

		$now = new SwatDate();
		$now->toUTC();

		$this->question->bodytext      = $values['bodytext'];
		$this->question->question_type = InquisitionQuestion::TYPE_RADIO_LIST;
	}

	// }}}
	// {{{ protected function addOptions()

	protected function addOptions(InquisitionQuestion $question)
	{
		$count = 0;

		$view = $this->ui->getWidget('question_option_table_view');
		$input_row = $view->getRow('input_row');

		$displayorder_base = SwatDB::queryOne(
			$this->app->db,
			sprintf(
				'select coalesce(max(displayorder), 0)
				from InquisitionQuestionOption where question = %s',
				$this->app->db->quote($question->id, 'integer')
			)
		);

		$replicators = $input_row->getReplicators();
		foreach ($replicators as $replicator_id) {
			if (!$input_row->rowHasMessage($replicator_id)) {
				$count++;

				$title = $input_row->getWidget('title', $replicator_id)->value;

				if ($title == '')
					continue;

				$class = SwatDBClassMap::get('InquisitionQuestionOption');
				$option = new $class;
				$option->setDatabase($this->app->db);
				$option->displayorder = $displayorder_base + $count;
				$option->title = $title;

				$question->options->add($option);

				$correct_widget = $input_row->getWidget(
					'correct_option', $replicator_id);

				$is_correct_option = ($correct_widget->isVisible() &&
					$correct_widget->value);

				if ($is_correct_option) {
					$question->correct_option = $option;
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
			$url = $this->source;
		} elseif ($this->inquisition instanceof InquisitionInquisition) {
			$url = 'Inquisition/Details';
		} else {
			$url = sprintf(
				'Question/Details?id=%s',
				$this->question->id
			);
		}

		$url.= $this->getLinkSuffix();

		$this->app->relocate($url);
	}

	// }}}

	// build phase
	// {{{ protected function loadDBData()

	protected function loadDBData()
	{
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

		$this->navbar->createEntry(Inquisition::_('New Question'));
	}

	// }}}
	// {{{ protected function buildFrame()

	protected function buildFrame()
	{
		parent::buildFrame();

		$frame = $this->ui->getWidget('edit_frame');
		$frame->title = Inquisition::_('New Question');
	}

	// }}}
	// {{{ protected function getLinkSuffix()

	protected function getLinkSuffix()
	{
		$suffix = null;

		if ($this->inquisition instanceof InquisitionInquisition) {
			$suffix = sprintf(
				'?id=%s',
				$this->inquisition->id
			);
		}

		return $suffix;
	}

	// }}}

	// finalize phase
	// {{{ public function finalize()

	public function finalize()
	{
		parent::finalize();

		$this->layout->addHtmlHeadEntry(
			'packages/inquisition/admin/styles/inquisition-question-edit.css',
			Inquisition::PACKAGE_ID
		);
	}

	// }}}
}

?>
