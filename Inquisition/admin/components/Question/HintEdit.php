<?php

require_once 'Swat/SwatDate.php';
require_once 'Swat/SwatTableStore.php';
require_once 'Swat/SwatDetailsStore.php';
require_once 'Admin/exceptions/AdminNotFoundException.php';
require_once 'Admin/pages/AdminDBEdit.php';
require_once 'Inquisition/dataobjects/InquisitionQuestion.php';
require_once 'Inquisition/dataobjects/InquisitionQuestionHint.php';

/**
 * Page for creating new question hints
 *
 * @package   Inquisition
 * @copyright 2013 silverorange
 */
class InquisitionQuestionHintEdit extends AdminDBEdit
{
	// {{{ protected properties

	/**
	 * @var InquisitionQuestion
	 */
	protected $hint;

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

		$this->initHint();
		$this->initQuestion();
		$this->initInquisition();
	}

	// }}}
	// {{{ protected function initHint()

	protected function initHint()
	{
		$class = SwatDBClassMap::get('InquisitionQuestionHint');
		$this->hint = new $class;
		$this->hint->setDatabase($this->app->db);

		if ($this->id !== null && !$this->hint->load($this->id)) {
			throw new AdminNotFoundException(
				sprintf(
					'Inquisition Question Hint with id ‘%s’ not found.',
					$this->id
				)
			);
		}
	}

	// }}}
	// {{{ protected function initQuestion()

	protected function initQuestion()
	{
		// TODO
		return;
		$class_name = SwatDBClassMap::get('InquisitionQuestion');
		$this->question = new $class_name();
		$this->question->setDatabase($this->app->db);
	}

	// }}}
	// {{{ protected function initInquisition()

	protected function initInquisition()
	{
		// TODO
		return;
		$class = SwatDBClassMap::get('InquisitionInquisition');
		$this->inquisition = new $class;
		$this->inquisition->setDatabase($this->app->db);

		if ($this->id == '') {
			throw new AdminNotFoundException(
				'Inquisition id not provided.'
			);
		}

		if (!$this->inquisition->load($this->id)) {
			throw new AdminNotFoundException(
				sprintf(
					'Inquisition with id ‘%s’ not found.',
					$this->id
				)
			);
		}
	}

	// }}}
	// {{{ protected function getUiXml()

	protected function getUiXml()
	{
		return 'Inquisition/admin/components/Question/hint-edit.xml';
	}

	// }}}

	// process phase
	// {{{ protected function saveDBData()

	protected function saveDBData()
	{
		$this->updateHint();
		$this->hint->save();

		$this->app->messages->add(
			new SwatMessage(
				Inquisition::_('Hint has been saved.')
			)
		);
	}

	// }}}
	// {{{ protected function updateHint()

	protected function updateHint()
	{
		$values = $this->ui->getValues(
			array(
				'bodytext',
			)
		);

		$this->hint->bodytext = $values['bodytext'];
		$this->hint->question = $this->question->id;

		// set displayorder so the new question appears at the end of the
		// list of the current hints by default.
		$sql = sprintf(
			'select max(displayorder) from InquisitionQuestionHint
			where question = %s',
			$this->app->db->quote($this->question->id, 'integer')
		);

		$max_displayorder = SwatDB::queryOne($this->app->db, $sql);
		$new_displayorder = floor(($max_displayorder + 10) / 10) * 10;
		$this->hint->displayorder = $new_displayorder;
	}

	// }}}
	// {{{ protected function relocate()

	protected function relocate()
	{
		$button = $this->ui->getWidget('another_button');

		if ($button->hasBeenClicked()) {
			$url = sprintf(
				'%s?question=%s',
				$this->source,
				$this->question->id
			);
		} else {
			$url = sprintf(
				'Question/Details?id=%s',
				$this->question->id
			);
		}

		$this->app->relocate($url);
	}

	// }}}

	// build phase
	// {{{ protected function loadDBData()

	protected function loadDBData()
	{
		$this->ui->setValues(get_object_vars($this->hint));
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
		}

		$this->navbar->createEntry($this->getTitle());
	}

	// }}}
	// {{{ protected function buildFrame()

	protected function buildFrame()
	{
		parent::buildFrame();

		$frame = $this->ui->getWidget('edit_frame');
		$frame->title = $this->getTitle();
	}

	// }}}
	// {{{ protected function getTitle()

	protected function getTitle()
	{
		return Inquisition::_('New Hint');
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
