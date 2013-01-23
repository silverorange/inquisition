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
		if ($this->hint->id != null) {
			$this->question = $this->hint->question;
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
			$uri = '%1$s?question=%2$s%3$s';
		} else {
			$uri = 'Question/Details?id=%2$s%3$s';
		}

		$this->app->relocate(
			sprintf(
				$uri,
				$this->source,
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
		$this->ui->setValues(get_object_vars($this->hint));
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
	// {{{ protected function buildFrame()

	protected function buildFrame()
	{
		parent::buildFrame();

		$frame = $this->ui->getWidget('edit_frame');
		$frame->title = $this->getTitle();
	}

	// }}}
	// {{{ protected function buildNavBar()

	protected function buildNavBar()
	{
		parent::buildNavBar();

		$this->navbar->popEntries(2);

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

		$this->navbar->createEntry($this->getTitle());
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
		return ($this->hint->id === null) ?
			Inquisition::_('New Hint') :
			Inquisition::_('Edit Hint');
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
