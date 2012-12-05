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
 * @copyright 2011-2012 silverorange
 */
class InquisitionQuestionEdit extends AdminDBEdit
{
	// {{{ protected properties

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

		$this->initQuestion();

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
		$values = $this->ui->getValues(array(
			'bodytext',
		));

		$this->question->bodytext = $values['bodytext'];
		$this->question->save();

		$this->app->messages->add(
			new SwatMessage(
				Inquisition::_('Question has been saved.')
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
	// {{{ protected function loadDBData()

	protected function loadDBData()
	{
		$this->ui->setValues(get_object_vars($this->question));
	}

	// }}}
	// {{{ protected function buildNavBar()

	protected function buildNavBar()
	{
		parent::buildNavBar();

		$this->navbar->popEntry();

		$this->navbar->createEntry(
			$this->question->inquisition->title,
			sprintf(
				'Inquisition/Details?id=%s',
				$this->question->inquisition->id
			)
		);

		$this->navbar->createEntry(
			sprintf(
				Inquisition::_('Question %s'),
				$this->question->position
			),
			sprintf(
				'Question/Details?id=%s',
				$this->question->id
			)
		);

		$this->navbar->createEntry(Inquisition::_('Edit Question'));
	}

	// }}}

	// finalize phase
	// {{{ public function finalize()

	public function finalize()
	{
		parent::finalize();
		$this->layout->addHtmlHeadEntry(
			'packages/inquisition/admin/styles/inquisition-question-edit.css',
			Inquisition::PACKAGE_ID);
	}

	// }}}
}

?>
