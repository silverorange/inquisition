<?php

require_once 'SwatDB/SwatDB.php';
require_once 'Admin/pages/AdminDBOrder.php';

/**
 * Order page for questions
 *
 * @package   Inquisition
 * @copyright 2011 silverorange
 */
class InquisitionInquisitionQuestionOrder extends AdminDBOrder
{
	// process phase
	// {{{ protected function saveIndex()

	protected function saveIndex($id, $index)
	{
		SwatDB::updateColumn($this->app->db, 'InquisitionQuestion',
			'integer:displayorder', $index, 'integer:id', array($id));
	}

	// }}}
	// {{{ protected function getUpdatedMessage()

	protected function getUpdatedMessage()
	{
		return new SwatMessage('Question order has been updated.');
	}

	// }}}

	// build phase
	// {{{ protected function buildInternal()
	protected function buildInternal()
	{
		$this->ui->getWidget('order_frame')->title = 'Change Question Order';

		$this->ui->getWidget('order')->width = '500px';
		$this->ui->getWidget('order')->height = '500px';

		parent::buildInternal();
	}

	// }}}
	// {{{ protected function loadData()

	protected function loadData()
	{
		$inquisition_id = SiteApplication::initVar('inquisition');

		if ($inquisition_id === null) {
			throw new AdminNotFoundException('No inquisition specified.');
		}

		$order_widget = $this->ui->getWidget('order');
		$order_widget->addOptionsByArray(SwatDB::getOptionArray($this->app->db,
			'InquisitionQuestion', 'bodytext', 'id', 'displayorder',
			sprintf('inquisition = %s', $inquisition_id)), 'text/xml');

		$sql = sprintf('select sum(displayorder) from InquisitionQuestion
			where inquisition = %s', $inquisition_id);

		$sum = SwatDB::queryOne($this->app->db, $sql, 'integer');

		$options_list = $this->ui->getWidget('options');
		$options_list->value = ($sum == 0) ? 'auto' : 'custom';
	}

	// }}}
}

?>
