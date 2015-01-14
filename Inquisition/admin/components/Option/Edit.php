<?php

require_once 'Admin/pages/AdminDBEdit.php';
require_once 'Admin/exceptions/AdminNotFoundException.php';
require_once 'Inquisition/dataobjects/InquisitionQuestion.php';
require_once 'Inquisition/dataobjects/InquisitionQuestionOption.php';

/**
 * Edit page for an option
 *
 * @package   Inquisition
 * @copyright 2012-2015 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class InquisitionOptionEdit extends AdminDBEdit
{
	// {{{ protected properties

	/**
	 * @var InquisitionQuestionOption
	 */
	protected $option;

	/**
	 * @var InquisitionQuestion
	 */
	protected $question;

	/**
	 * @var InquisitionInquisition
	 */
	protected $inquisition;

	// }}}

	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		parent::initInternal();

		$this->ui->loadFromXML($this->getUiXml());

		$this->initOption();
		$this->initQuestion();
		$this->initInquisition();
	}

	// }}}
	// {{{ protected function initOption()

	protected function initOption()
	{
		$class_name = SwatDBClassMap::get('InquisitionQuestionOption');

		$this->option = new $class_name;
		$this->option->setDatabase($this->app->db);

		if ($this->id !== null && !$this->option->load($this->id)) {
			throw new AdminNotFoundException(
				sprintf(
					'Question option with id ‘%s’ not found.',
					$this->id
				)
			);
		}
	}

	// }}}
	// {{{ protected function initQuestion()

	protected function initQuestion()
	{
		if ($this->option->id != null) {
			$this->question = $this->option->question;
		} else {
			$question_id = SiteApplication::initVar('question');

			if (is_numeric($question_id)) {
				$question_id = intval($question_id);
			}

			$class = SwatDBClassMap::get('InquisitionQuestion');
			$this->question = new $class;
			$this->question->setDatabase($this->app->db);

			if (!$this->question->load($question_id)) {
				throw new AdminNotFoundException(
					sprintf(
						'A question with the id of “%s” does not exist',
						$id
					)
				);
			}
		}
	}

	// }}}
	// {{{ protected function initInquisition()

	protected function initInquisition()
	{
		$inquisition_id = SiteApplication::initVar('inquisition');

		if ($inquisition_id !== null) {
			$this->inquisition = $this->loadInquisition($inquisition_id);
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
	// {{{ protected function getUiXml()

	protected function getUiXml()
	{
		return 'Inquisition/admin/components/Option/edit.xml';
	}

	// }}}

	// process phase
	// {{{ protected function saveDBData()

	protected function saveDBData()
	{
		$this->updateOption();

		$this->option->save();

		$this->app->messages->add(
			new SwatMessage(Inquisition::_('Option has been saved.'))
		);
	}

	// }}}
	// {{{ protected function updateHint()

	protected function updateOption()
	{
		$values = $this->ui->getValues(
			array(
				'title',
			)
		);

		$this->option->title = $values['title'];
		$this->option->question = $this->question->id;

		if ($this->option->id === null) {
			$this->option->question = $this->question;

			// set displayorder so the new question appears at the end of the
			// list of the current options by default.
			$sql = sprintf(
				'select max(displayorder) from InquisitionQuestionOption
				where question = %s',
				$this->app->db->quote($this->question->id, 'integer')
			);

			$max_displayorder = SwatDB::queryOne($this->app->db, $sql);
			$new_displayorder = floor(($max_displayorder + 10) / 10) * 10;
			$this->option->displayorder = $new_displayorder;
		}
	}

	// }}}
	// {{{ protected function relocate()

	protected function relocate()
	{
		$this->app->relocate(
			sprintf(
				'Option/Details?id=%s%s',
				$this->option->id,
				$this->getLinkSuffix()
			)
		);
	}

	// }}}

	// build phase
	// {{{ protected function loadDBData()

	protected function loadDBData()
	{
		$this->ui->setValues(get_object_vars($this->option));
	}

	// }}}
	// {{{ protected function buildForm()

	protected function buildForm()
	{
		parent::buildForm();

		$form = $this->ui->getWidget('edit_form');
		$form->addHiddenField('question', $this->question->id);

		if ($this->inquisition instanceof InquisitionInquisition) {
			$form->addHiddenField('inquisition', $this->inquisition->id);
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

		if ($this->option->id !== null) {
			$this->navbar->createEntry(
				$this->getOptionTitle(),
				sprintf(
					'Option/Details?id=%s%s',
					$this->option->id,
					$this->getLinkSuffix()
				)
			);
		}

		$this->navbar->createEntry($this->getTitle());
	}

	// }}}
	// {{{ protected function getQuestionTitle()

	protected function getQuestionTitle()
	{
		// TODO: Update this with some version of getPosition().
		return Inquisition::_('Question');
	}

	// }}}
	// {{{ protected function getOptionTitle()

	protected function getOptionTitle()
	{
		return sprintf(
			Inquisition::_('Option %s'),
			$this->option->position
		);
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
	// {{{ protected function getTitle()

	protected function getTitle()
	{
		return ($this->option->id === null)
			? Inquisition::_('New Option')
			: Inquisition::_('Edit Option');
	}

	// }}}
}

?>
