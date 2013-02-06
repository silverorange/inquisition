<?php

require_once 'Swat/SwatDate.php';
require_once 'Swat/SwatTableStore.php';
require_once 'Swat/SwatDetailsStore.php';
require_once 'Admin/exceptions/AdminNotFoundException.php';
require_once 'Admin/pages/AdminDBEdit.php';
require_once 'Inquisition/dataobjects/InquisitionQuestion.php';
require_once 'Inquisition/admin/InquisitionCorrectOptionRadioButton.php';

/**
 * Edit page for a selecting the correct option to a question
 *
 * @package   Inquisition
 * @copyright 2012-2013 silverorange
 */
class InquisitionQuestionCorrectOption extends AdminDBEdit
{
	// {{{ protected properties

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

		$this->initQuestion();
		$this->initInquisition();

		$this->ui->loadFromXML($this->getUiXml());
	}

	// }}}
	// {{{ protected function initQuestion()

	protected function initQuestion()
	{
		$class = SwatDBClassMap::get('InquisitionQuestion');
		$this->question = new $class;
		$this->question->setDatabase($this->app->db);

		if ($this->id == '') {
			throw new AdminNotFoundException(
				'Question id not provided.'
			);
		}

		if (!$this->question->load($this->id)) {
			throw new AdminNotFoundException(
				sprintf(
					'Question with id ‘%s’ not found.',
					$this->id
				)
			);
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
		return 'Inquisition/admin/components/Question/correct-option.xml';
	}

	// }}}

	// process phase
	// {{{ protected function saveDBData()

	protected function saveDBData()
	{
		$values = $this->ui->getValues(array(
			'correct_option',
		));

		$this->question->correct_option = $values['correct_option'];
		$this->question->save();

		$this->app->messages->add(
			new SwatMessage(
				Inquisition::_('Correct option has been updated.')
			)
		);
	}

	// }}}
	// {{{ protected function relocate()

	protected function relocate()
	{
		$uri = sprintf(
			'Question/Details?id=%s',
			$this->question->id
		);

		if ($this->inquisition instanceof InquisitionInquisition) {
			$uri.= sprintf(
				'&inquisition=%s',
				$this->inquisition->id
			);
		}

		$this->app->relocate($uri);
	}

	// }}}

	// build phase
	// {{{ protected function buildInternal()

	protected function buildInternal()
	{
		parent::buildInternal();

		$list = $this->ui->getWidget('correct_option');

		foreach ($this->question->options as $option) {
			$list->addOption(
				$option->id,
				sprintf(
					'%s. %s',
					$option->position,
					$option->title
				)
			);
		}
	}

	// }}}
	// {{{ protected function buildForm()

	protected function buildForm()
	{
		parent::buildForm();

		if ($this->inquisition instanceof InquisitionInquisition) {
			$form = $this->ui->getWidget('edit_form');
			$form->addHiddenField('inquisition', $this->inquisition->id);
		}
	}

	// }}}
	// {{{ protected function loadDBData()

	protected function loadDBData()
	{
		if ($this->question->correct_option instanceof
			InquisitionQuestionOption) {
			$this->ui->setValues(
				array(
					'correct_option' => $this->question->correct_option->id
				)
			);
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

		$this->navbar->createEntry(Inquisition::_('Edit Correct Question'));
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
