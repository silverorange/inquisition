<?php

require_once 'Swat/SwatTableStore.php';
require_once 'Swat/SwatDetailsStore.php';
require_once 'SwatDB/SwatDB.php';
require_once 'Admin/pages/AdminIndex.php';
require_once 'Admin/exceptions/AdminNotFoundException.php';
require_once 'Inquisition/dataobjects/InquisitionQuestionWrapper.php';

/**
 * Details page for inquisitions
 *
 * @package   Inquisition
 * @copyright 2011 silverorange
 */
class InquisitionInquisitionDetails extends AdminIndex
{
	// {{{ protected properties

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var Inquisition
	 */
	protected $inquisition;

	protected $ui_xml = 'Inquisition/admin/components/Inquisition/details.xml';

	// }}}

	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		parent::initInternal();

		$this->id = SiteApplication::initVar('id');

		if (is_numeric($this->id)) {
			$this->id = intval($this->id);
		}

		$this->initInquisition();

		$this->ui->loadFromXML($this->ui_xml);
	}

	// }}}
	// {{{ protected function initInquisition()

	protected function initInquisition()
	{
		$class = SwatDBClassMap::get('Inquisition');
		$this->inquisition = new $class;
		$this->inquisition->setDatabase($this->app->db);

		if (!$this->inquisition->load($this->id)) {
			throw new AdminNotFoundException(sprintf(
				'A inquisition with the id of “%s” does not exist', $this->id));
		}
	}

	// }}}

	// process phase
	// {{{ protected function processActions()

	protected function processActions(SwatTableView $view, SwatActions $actions)
	{
		switch ($view->id) {
		case 'question_view':
			switch ($actions->selected->id) {
			case 'question_delete':
				$this->app->replacePage('Inquisition/QuestionDelete');
				$this->app->getPage()->setItems($view->getSelection());
				break;
			}
			break;
		}
	}

	// }}}

	// build phase
	// {{{ protected function buildInternal()

	protected function buildInternal()
	{
		parent::buildInternal();

		$this->ui->getWidget('details_frame')->title = $this->inquisition->title;

		$toolbar = $this->ui->getWidget('question_toolbar');
		$toolbar->setToolLinkValues($this->inquisition->id);

		$toolbar = $this->ui->getWidget('details_toolbar');
		$toolbar->setToolLinkValues($this->inquisition->id);

		$this->ui->getWidget('details_view')->data = $this->getDetailsStore();
	}

	// }}}
	// {{{ protected function getDetailsStore()

	protected function getDetailsStore()
	{
		$ds = new SwatDetailsStore($this->inquisition);

		$ds->description = SwatString::ellipsizeRight($ds->description, 300);

		return $ds;
	}

	// }}}
	// {{{ protected function getTableModel()

	protected function getTableModel(SwatView $view)
	{
		$model = null;

		switch ($view->id) {
			case 'question_view':
				$model = $this->getQuestionTableModel($view);
				break;
		}

		return $model;
	}

	// }}}
	// {{{ protected function getQuestionTableModel()

	protected function getQuestionTableModel(SwatTableView $view)
	{
		$sql = 'select * from InquisitionQuestion where inquisition = %s order by %s';

		$sql = sprintf($sql,
			$this->app->db->quote($this->id, 'integer'),
			$this->getOrderByClause($view, 'displayorder'));

		$questions = SwatDB::query($this->app->db, $sql,
			SwatDBClassMap::get('InquisitionQuestionWrapper'));

		$store = new SwatTableStore();

		foreach ($questions as $question) {
			$ds = new SwatDetailsStore($question);

			$ds->option_count = sprintf('%s options', count($question->options));
			$ds->bodytext = SwatString::condense($question->bodytext);

			$store->add($ds);
		}

		return $store;
	}

	// }}}
	// {{{ protected function buildNavBar()

	protected function buildNavBar()
	{
		parent::buildNavBar();

		$this->navbar->createEntry($this->inquisition->title);
	}

	// }}}
}

?>
