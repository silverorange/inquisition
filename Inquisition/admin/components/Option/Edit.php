<?php

require_once 'Admin/pages/AdminDBEdit.php';
require_once 'Admin/exceptions/AdminNotFoundException.php';
require_once 'Inquisition/dataobjects/InquisitionQuestion.php';
require_once 'Inquisition/dataobjects/InquisitionQuestionOption.php';

/**
 * Edit page for an option
 *
 * @package   Inquisition
 * @copyright 2012-2013 silverorange
 */
class InquisitionOptionEdit extends AdminDBEdit
{
	// {{{ protected properties

	/**
	 * @var InquisitionQuestion
	 */
	protected $question;

	/**
	 * @var InquisitionQuestionOption
	 */
	protected $option;

	// }}}

	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		parent::initInternal();

		$this->ui->loadFromXML($this->getUiXml());

		$this->initOption();
	}

	// }}}
	// {{{ protected function initOption()

	protected function initOption()
	{
		$class_name = SwatDBClassMap::get('InquisitionQuestionOption');

		$this->option = new $class_name;
		$this->option->setDatabase($this->app->db);

		if ($this->id != '') {
			if (!$this->option->load($this->id)) {
				throw new AdminNotFoundException(
					sprintf(
						'Question option with id ‘%s’ not found.',
						$this->id
					)
				);
			}

			$this->question = $this->option->question;
		} else {
			$class_name = SwatDBClassMap::get('InquisitionQuestion');

			$this->question = new $class_name;
			$this->question->setDatabase($this->app->db);

			$question_id = intval(SiteApplication::initVar('question'));

			if ($question_id == '') {
				throw new AdminNotFoundException('No question id provided.');
			}

			if (!$this->question->load($question_id)) {
				throw new AdminNotFoundException(
					sprintf(
						'Question with id ‘%s’ not found.',
						$question_id
					)
				);
			}
		}
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
		$values = $this->ui->getValues(array(
			'title',
		));

		$this->option->title = $values['title'];

		if ($this->option->id === null) {
			$this->option->question = $this->question;
		}

		$this->option->save();

		$this->app->messages->add(
			new SwatMessage(Inquisition::_('Option has been saved.'))
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
				$this->question->getPosition($this->inquisition)
			),
			sprintf(
				'Question/Details?id=%s',
				$this->question->id
			)
		);

		if ($this->option->id != '') {
			$this->navbar->createEntry(
				sprintf(
					Inquisition::_('Option %s'),
					$this->option->position
				),
				sprintf(
					'Option/Details?id=%s',
					$this->option->id
				)
			);

			$this->navbar->createEntry(Inquisition::_('Edit Option'));
		} else {
			$this->navbar->createEntry(Inquisition::_('New Option'));
		}
	}

	// }}}
}

?>
