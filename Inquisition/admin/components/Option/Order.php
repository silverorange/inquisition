<?php

require_once 'SwatDB/SwatDB.php';
require_once 'Admin/pages/AdminDBOrder.php';

/**
 * Change order page for options
 *
 * @package   Inquisition
 * @copyright 2011-2012 silverorange
 */
class InquisitionOptionOrder extends AdminDBOrder
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
		$id = SiteApplication::initVar('id');

		if ($id == '') {
			throw new AdminNotFoundException(
				'No question id specified.'
			);
		}

		if (is_numeric($id)) {
			$id = intval($id);
		}

		$class = SwatDBClassMap::get('InquisitionQuestion');

		$this->question = new $class;
		$this->question->setDatabase($this->app->db);

		if (!$this->question->load($id)) {
			throw new AdminNotFoundException(
				sprintf(
					'An question with the id of “%s” does not exist', $id
				)
			);
		}
	}

	// }}}

	// process phase
	// {{{ protected function saveIndex()

	protected function saveIndex($id, $index)
	{
		SwatDB::updateColumn($this->app->db, 'InquisitionQuestionOption',
			'integer:displayorder', $index, 'integer:id', array($id));
	}

	// }}}
	// {{{ protected function getUpdatedMessage()

	protected function getUpdatedMessage()
	{
		return new SwatMessage('Option order has been updated.');
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
		$this->ui->getWidget('order_frame')->title = 'Change Option Order';

		$this->ui->getWidget('order')->width = '500px';
		$this->ui->getWidget('order')->height = '200px';

		parent::buildInternal();
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
			sprintf(Inquisition::_('Question %s'), $this->question->position),
			sprintf(
				'Question/Details?id=%s',
				$this->question->id
			)
		);

		$this->navbar->createEntry(Inquisition::_('Change Option Order'));
	}

	// }}}
	// {{{ protected function buildForm()

	protected function buildForm()
	{
		parent::buildForm();

		$form = $this->ui->getWidget('order_form');
		$form->addHiddenField('id', $this->question->id);
	}

	// }}}
	// {{{ protected function loadData()

	protected function loadData()
	{
		$sum = 0;
		$order_widget = $this->ui->getWidget('order');

		foreach ($this->question->options as $option) {
			$sum += $option->displayorder;

			$order_widget->addOption(
				$option->id,
				$option->title,
				'text/xml'
			);
		}

		$options_list = $this->ui->getWidget('options');
		$options_list->value = ($sum == 0) ? 'auto' : 'custom';
	}

	// }}}
}

?>
