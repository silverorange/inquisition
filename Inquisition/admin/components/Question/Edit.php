<?php

require_once 'Swat/SwatDate.php';
require_once 'Swat/SwatTableStore.php';
require_once 'Swat/SwatDetailsStore.php';
require_once 'Admin/exceptions/AdminNotFoundException.php';
require_once 'Admin/pages/AdminDBEdit.php';
require_once 'Inquisition/dataobjects/InquisitionQuestion.php';
require_once 'Inquisition/admin/InquisitionCorrectOptionRadioButton.php';

/**
 * Edit page for a question
 *
 * @package   Inquisition
 * @copyright 2011-2013 silverorange
 */
class InquisitionQuestionEdit extends AdminDBEdit
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

		$this->ui->loadFromXML($this->getUiXml());

		$this->initQuestion();
		$this->initInquisition();
		$this->initEnabledField();
	}

	// }}}
	// {{{ protected function initQuestion()

	protected function initQuestion()
	{
		$class = SwatDBClassMap::get('InquisitionQuestion');
		$this->question = new $class;
		$this->question->setDatabase($this->app->db);

		if ($this->id !== null && !$this->question->load($this->id)) {
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
	// {{{ protected function initEnabledField()

	protected function initEnabledField()
	{
		$note = null;

		if (count($this->question->options) === 0) {
			$note = sprintf(
				Inquisition::_(
					'This question has no options and can’t be shown '.
					'on the site until %soptions have been added%s.'
				),
				sprintf(
					'<a href="Option/Edit?question=%s%s">',
					$this->question->id,
					$this->getLinkSuffix()
				),
				'</a>'
			);
		} elseif (!($this->question->correct_option instanceof
			InquisitionQuestionOption)) {
			$note = sprintf(
				Inquisition::_(
					'This question has no correct option and can’t be shown '.
					'on the site until a %scorrect option is selected%s.'
				),
				sprintf(
					'<a href="Question/CorrectOption?id=%s%s">',
					$this->question->id,
					$this->getLinkSuffix()
				),
				'</a>'
			);
		}

		if ($note !== null) {
			$this->ui->getWidget('enabled')->sensitive = false;
			$this->ui->getWidget('enabled_field')->note_content_type =
				'text/xml';

			$this->ui->getWidget('enabled_field')->note = $note;
		}
	}

	// }}}
	// {{{ protected function getUiXml()

	protected function getUiXml()
	{
		return 'Inquisition/admin/components/Question/edit.xml';
	}

	// }}}

	// process phase
	// {{{ protected function saveDBData()

	protected function saveDBData()
	{
		$this->updateQuestion();
		$this->question->save();

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
		$values = $this->ui->getValues(
			array(
				'bodytext',
				'enabled',
			)
		);

		$this->question->bodytext = $values['bodytext'];
		$this->question->enabled  = $values['enabled'];
	}

	// }}}
	// {{{ protected function relocate()

	protected function relocate()
	{
		$this->app->relocate(
			sprintf(
				'Question/Details?id=%s%s',
				$this->question->id,
				$this->getLinkSuffix()
			)
		);
	}

	// }}}

	// build phase
	// {{{ protected function loadDBData()

	protected function loadDBData()
	{
		$this->ui->setValues(get_object_vars($this->question));
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

		$this->navbar->createEntry(Inquisition::_('Edit Question'));
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
