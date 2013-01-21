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
			$class = SwatDBClassMap::get('Inquisition');
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
			)
		);

		$this->question->bodytext = $values['bodytext'];
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

		$question_link_extra = null;
		if ($this->inquisition instanceof InquisitionInquisition) {
			$this->navbar->createEntry(
				$this->inquisition->title,
				sprintf(
					'Inquisition/Details?id=%s',
					$this->inquisition->id
				)
			);

			$question_link_extra = sprintf(
				'&instance=%s',
				$this->inquisition->id
			);
		}

		$this->navbar->createEntry(
			$this->getQuestionTitle(),
			sprintf(
				'Question/Details?id=%s%s',
				$this->question->id,
				$question_link_extra
			)
		);

		$this->navbar->createEntry(Inquisition::_('Edit Question'));
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
