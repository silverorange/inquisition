<?php

require_once 'Admin/pages/AdminIndex.php';
require_once 'Inquisition/dataobjects/InquisitionWrapper.php';

/**
 * Inquisition index
 *
 * @package   Inquisition
 * @copyright 2011 silverorange
 */
class InquisitionInquisitionIndex extends AdminIndex
{
	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		$this->ui->loadFromXML(dirname(__FILE__).'/index.xml');

		parent::initInternal();

		$view = $this->ui->getWidget('inquisition_view');
		$view->setDefaultOrderbyColumn(
			$view->getColumn('title'),
			SwatTableViewOrderableColumn::ORDER_BY_DIR_ASCENDING);
	}

	// }}}

	// build phase
	// {{{ protected function getTableModel()

	protected function getTableModel(SwatView $view)
	{
		switch ($view->id) {
		case 'inquisition_view':
			return $this->getInquisitionTableModel($view);
		}
	}

	// }}}
	// {{{ protected function getInquisitionTableModel()

	protected function getInquisitionTableModel(SwatView $view)
	{
		$sql = sprintf('select * from Inquisition
			order by %s',
			$this->getOrderByClause($view, 'title asc'));

		$wrapper = SwatDBClassMap::get('InquisitionWrapper');
		$inquisitions = SwatDB::query($this->app->db, $sql, $wrapper);

		$store = new SwatTableStore();

		foreach ($inquisitions as $inquisition) {
			$ds = new SwatDetailsStore($inquisition);
			$ds->details = sprintf('%s questions', count($inquisition->questions));

			$store->add($ds);
		}

		return $store;
	}

	// }}}
}

?>
