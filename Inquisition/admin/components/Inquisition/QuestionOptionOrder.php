<?php

require_once 'SwatDB/SwatDB.php';
require_once 'Admin/pages/AdminDBOrder.php';

/**
 * Order page for question options
 *
 * @package   Inquisition
 * @copyright 2011 silverorange
 */
class InquisitionInquisitionQuestionOptionOrder extends AdminDBOrder
{
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
		return new SwatMessage('Question option order has been updated.');
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
	// {{{ protected function loadData()

	protected function loadData()
	{
		$question_id = SiteApplication::initVar('question');

		if ($question_id === null) {
			throw new AdminNotFoundException('No question specified.');
		}

		$order_widget = $this->ui->getWidget('order');
		$order_widget->addOptionsByArray(SwatDB::getOptionArray(
			$this->app->db, 'InquisitionQuestionOption', 'title', 'id',
			'displayorder', sprintf('question = %s', $question_id)));

		$sql = sprintf('select sum(displayorder) from InquisitionQuestionOption
			where question = %s', $question_id);

		$sum = SwatDB::queryOne($this->app->db, $sql, 'integer');

		$options_list = $this->ui->getWidget('options');
		$options_list->value = ($sum == 0) ? 'auto' : 'custom';
	}

	// }}}
}

?>
