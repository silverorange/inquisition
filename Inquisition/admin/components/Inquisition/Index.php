<?php

require_once 'Admin/pages/AdminIndex.php';
require_once 'Inquisition/dataobjects/InquisitionInquisitionWrapper.php';
require_once 'Inquisition/dataobjects/InquisitionQuestionWrapper.php';

/**
 * Inquisition index
 *
 * @package   Inquisition
 * @copyright 2011-2013 silverorange
 */
class InquisitionInquisitionIndex extends AdminIndex
{
	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
SwatDB::setDebug();
		parent::initInternal();

		$this->ui->loadFromXML($this->getUiXml());

		$view = $this->ui->getWidget('inquisition_view');
		$view->setDefaultOrderbyColumn(
			$view->getColumn('title'),
			SwatTableViewOrderableColumn::ORDER_BY_DIR_ASCENDING
		);
	}

	// }}}
	// {{{ protected function getUiXml()

	protected function getUiXml()
	{
		return 'Inquisition/admin/components/Inquisition/index.xml';
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
		$sql = sprintf(
			'select * from Inquisition
			order by %s',
			$this->getOrderByClause($view, 'title asc')
		);

		$wrapper = SwatDBClassMap::get('InquisitionInquisitionWrapper');
		$inquisitions = SwatDB::query($this->app->db, $sql, $wrapper);

		// efficiently load the questions
		$wrapper = SwatDBClassMap::get('InquisitionQuestionWrapper');
		$sql = 'select InquisitionQuestion.*,
				InquisitionInquisitionQuestionBinding.inquisition
			from InquisitionQuestion
			inner join InquisitionInquisitionQuestionBinding on
				InquisitionQuestion.id =
					InquisitionInquisitionQuestionBinding.question
			where InquisitionInquisitionQuestionBinding.inquisition in (%s)
			order by InquisitionInquisitionQuestionBinding.inquisition,
				InquisitionInquisitionQuestionBinding.displayorder,
				InquisitionQuestion.id';

		$sql = sprintf(
			$sql,
			$this->app->db->implodeArray(
				$inquisitions->getIndexes(),
				'integer'
			)
		);

		$questions = SwatDB::query($this->app->db, $sql, $wrapper);
		$inquisitions->attachSubRecordset(
			'questions',
			$wrapper,
			'inquisition',
			$questions
		);

		$locale = SwatI18NLocale::get();
		$store = new SwatTableStore();

		foreach ($inquisitions as $inquisition) {
			$ds = new SwatDetailsStore($inquisition);
			$question_count = count($inquisition->questions);
			$ds->question_count = sprintf(
				Inquisition::ngettext(
					'%s question',
					'%s questions',
					$question_count
				),
				$locale->formatNumber($question_count)
			);

			$store->add($ds);
		}

		return $store;
	}

	// }}}
}

?>
