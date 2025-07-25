<?php

/**
 * Questions index.
 *
 * @copyright 2013-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class InquisitionQuestionIndex extends AdminSearch
{
    // init phase

    protected function initInternal()
    {
        parent::initInternal();

        $this->ui->loadFromXML($this->getUiXml());

        $view = $this->ui->getWidget('index_view');
        $view->setDefaultOrderbyColumn(
            $view->getColumn('bodytext'),
            SwatTableViewOrderableColumn::ORDER_BY_DIR_ASCENDING
        );
    }

    protected function getUiXml()
    {
        return __DIR__ . '/index.xml';
    }

    // process phase

    protected function processActions(SwatView $view, SwatActions $actions)
    {
        switch ($actions->selected->id) {
            case 'delete':
                $this->app->replacePage('Question/Delete');
                $this->app->getPage()->setItems($view->getSelection());
                break;
        }
    }

    protected function processInternal()
    {
        parent::processInternal();

        $pager = $this->ui->getWidget('pager');
        $pager->total_records = SwatDB::queryOne(
            $this->app->db,
            sprintf(
                'select count(id) from InquisitionQuestion where %s',
                $this->getWhereClause()
            )
        );

        $pager->process();
    }

    // build phase

    protected function getTableModel(SwatView $view): ?SwatTableModel
    {
        switch ($view->id) {
            case 'index_view':
                return $this->getQuestionTableModel($view);
        }

        return null;
    }

    protected function getQuestionTableModel(SwatView $view): SwatTableStore
    {
        $sql = sprintf(
            'select InquisitionQuestion.*
			from InquisitionQuestion
			where %s
			order by %s',
            $this->getWhereClause(),
            $this->getOrderByClause($view, 'bodytext asc')
        );

        $pager = $this->ui->getWidget('pager');
        $this->app->db->setLimit($pager->page_size, $pager->current_record);

        $questions = SwatDB::query(
            $this->app->db,
            $sql,
            SwatDBClassMap::get(InquisitionQuestionWrapper::class)
        );

        if (count($questions) > 0) {
            $this->ui->getWidget('results_message')->content =
                $pager->getResultsMessage();
        }

        $store = new SwatTableStore();
        foreach ($questions as $question) {
            $ds = new SwatDetailsStore($question);
            $ds->bodytext = SwatString::condense($question->bodytext, 100);
            $store->add($ds);
        }

        return $store;
    }

    protected function getWhereClause()
    {
        $where = '1 = 1';

        // email
        $clause = new AdminSearchClause('bodytext');
        $clause->table = 'InquisitionQuestion';
        $clause->value = $this->ui->getWidget('search_keywords')->value;
        $clause->operator = AdminSearchClause::OP_CONTAINS;
        $where .= $clause->getClause($this->app->db);

        return $where;
    }
}
