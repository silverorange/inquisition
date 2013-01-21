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
 * @copyright 2011-2013 silverorange
 */
class InquisitionInquisitionDetails extends AdminIndex
{
	// {{{ protected properties

	/**
	 * @var integer
	 */
	protected $id;

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

		$this->id = SiteApplication::initVar('id');

		if (is_numeric($this->id)) {
			$this->id = intval($this->id);
		}

		$this->initInquisition();

		$this->ui->loadFromXML($this->getUiXml());
	}

	// }}}
	// {{{ protected function initInquisition()

	protected function initInquisition()
	{
		$class = SwatDBClassMap::get('InquisitionInquisition');
		$this->inquisition = new $class;
		$this->inquisition->setDatabase($this->app->db);

		if (!$this->inquisition->load($this->id)) {
			throw new AdminNotFoundException(sprintf(
				'A inquisition with the id of “%s” does not exist', $this->id));
		}
	}

	// }}}
	// {{{ protected function getUiXml()

	protected function getUiXml()
	{
		return 'Inquisition/admin/components/Inquisition/details.xml';
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
				$this->app->replacePage('Question/Delete');

				$this->app->getPage()->setId($this->inquisition->id);
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

		$this->ui->getWidget('details_frame')->title =
			$this->inquisition->title;

		$toolbar = $this->ui->getWidget('question_toolbar');
		$toolbar->setToolLinkValues(array($this->inquisition->id));

		$toolbar = $this->ui->getWidget('details_toolbar');
		$toolbar->setToolLinkValues(array($this->inquisition->id));

		$view = $this->ui->getWidget('details_view');
		$view->data = $this->getDetailsStore($this->inquisition);

		$field = $view->getField('createdate');
		$renderer = $field->getFirstRenderer();
		$renderer->display_time_zone = $this->app->default_time_zone;
	}

	// }}}
	// {{{ protected function getDetailsStore()

	protected function getDetailsStore(InquisitionInquisition $inquisition)
	{
		$ds = new SwatDetailsStore($inquisition);
		$ds->description = SwatString::ellipsizeRight(
			$inquisition->description, 300);

		return $ds;
	}

	// }}}
	// {{{ protected function getTableModel()

	protected function getTableModel(SwatView $view)
	{
		$model = null;

		switch ($view->id) {
		case 'question_view':
			$model = $this->getOptionTableModel($view);
			break;
		}

		return $model;
	}

	// }}}
	// {{{ protected function getOptionTableModel()

	protected function getOptionTableModel(SwatTableView $view)
	{
		$store = new SwatTableStore();

		foreach ($this->inquisition->questions as $question) {
			$store->add($this->getQuestionDetailsStore($question));
		}

		$this->ui->getWidget('question_order')->sensitive = (count($store) > 1);

		return $store;
	}

	// }}}
	// {{{ protected function getQuestionDetailsStore()

	protected function getQuestionDetailsStore(InquisitionQuestion $question)
	{
		$ds = new SwatDetailsStore($question);

		$ds->title = sprintf(
			Inquisition::_('Question %s'),
			$question->position
		);

		$ds->image_count = count($question->images);
		$ds->option_count = count($question->options);

		return $ds;
	}

	// }}}
	// {{{ protected function buildNavBar()

	protected function buildNavBar()
	{
		parent::buildNavBar();
		$this->navbar->createEntry($this->inquisition->title);
	}

	// }}}

	// finalize phase
	// {{{ public function finalize()

	public function finalize()
	{
		parent::finalize();

		$this->layout->addHtmlHeadEntry(
			'packages/inquisition/admin/styles/inquisition-details.css'
		);
	}

	// }}}
}

?>
