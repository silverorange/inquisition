<?php

require_once 'SwatDB/SwatDB.php';
require_once 'Admin/pages/AdminDBOrder.php';
require_once 'Inquisition/dataobjects/InquisitionQuestion.php';

/**
 * Change order page for question hints
 *
 * @package   Inquisition
 * @copyright 2013 silverorange
 */
class InquisitionQuestionHintOrder extends AdminDBOrder
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
	}

	// }}}
	// {{{ protected function initQuestion()

	protected function initQuestion()
	{
		$id = SiteApplication::initVar('question');

		if ($id == '') {
			throw new AdminNotFoundException(
				Inquisition::_('No question id specified.')
			);
		}

		if (is_numeric($id)) {
			$id = intval($id);
		}

		$class = SwatDBClassMap::get('InquisitionQuestionHint');
		$this->question = new $class;
		$this->question->setDatabase($this->app->db);

		if (!$this->question->load($id)) {
			throw new AdminNotFoundException(
				sprintf(
					'A question with the id of “%s” does not exist', $id
				)
			);
		}
	}

	// }}}

	// process phase
	// {{{ protected function saveIndex()

	protected function saveIndex($id, $index)
	{
		SwatDB::updateColumn(
			$this->app->db, 'InquisitionQuestionHints',
			'integer:displayorder', $index, 'integer:id', array($id)
		);
	}

	// }}}
	// {{{ protected function getUpdatedMessage()

	protected function getUpdatedMessage()
	{
		return new SwatMessage(Inquisition::_('Hint order has been updated.'));
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
	// {{{ protected function buildInternal()

	protected function buildInternal()
	{
		$this->ui->getWidget('order_frame')->title = 'Change Hint Order';

		parent::buildInternal();
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
				$this->question->inquisition->title,
				sprintf(
					'Inquisition/Details?id=%s',
					$this->question->inquisition->id
				)
			);

			$this->navbar->createEntry(
				sprintf(
					'Question %s',
					$this->question->getPosition($this->inquisition)
				),
				sprintf(
					'Question/Details?id=%s',
					$this->question->id
				)
			);
		}

		$this->navbar->createEntry(Inquisition::_('Change Hint Order'));
	}

	// }}}
	// {{{ protected function buildForm()

	protected function buildForm()
	{
		parent::buildForm();

		$form = $this->ui->getWidget('order_form');
		$form->addHiddenField('question', $this->question->id);
	}

	// }}}
	// {{{ protected function loadData()

	protected function loadData()
	{
		$order_widget = $this->ui->getWidget('order');

		foreach ($this->question->hints as $hint) {
			$order_widget->addOption(
				$hint->id,
				SwatString::condense($hint->bodytext, 50)
			);
		}

		$sql = sprintf(
			'select sum(displayorder) from InquisitionQuestionHint
			where question = %s',
			$this->app->db->quote($this->question->id, 'integer')
		);

		$sum = SwatDB::queryOne($this->app->db, $sql, 'integer');

		$options_list = $this->ui->getWidget('options');
		$options_list->value = ($sum == 0) ? 'auto' : 'custom';
	}

	// }}}
}

?>
