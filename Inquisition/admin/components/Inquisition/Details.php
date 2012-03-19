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
 * @copyright 2011-2012 silverorange
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
		$class = SwatDBClassMap::get('InquisitionInquisition');
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
				$this->app->replacePage($this->getComponentName().
					'/QuestionDelete');

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
		$toolbar->setToolLinkValues(array(
			$this->getComponentName(),
			$this->inquisition->id));

		$toolbar = $this->ui->getWidget('details_toolbar');
		$toolbar->setToolLinkValues(array(
			$this->getComponentName(),
			$this->inquisition->id));

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
		case 'question_option_view':
			$model = $this->getQuestionOptionTableModel($view);
			break;
		}

		return $model;
	}

	// }}}
	// {{{ protected function getQuestionOptionTableModel()

	protected function getQuestionOptionTableModel(SwatTableView $view)
	{
		$store = new SwatTableStore();

		$current_question = null;
		$index = 0;
		foreach ($this->getQuestionOptions($view) as $option) {
			if ($option->question != $current_question) {
				$current_question = $option->question;
				$index++;
			}
			$store->add($this->getQuestionOptionDetailsStore($option, $index));
		}

		$this->ui->getWidget('question_order')->sensitive = (count($store) > 1);

		return $store;
	}

	// }}}
	// {{{ protected function getQuestionOptionDetailsStore()

	protected function getQuestionOptionDetailsStore($option, $index)
	{
		$ds = new SwatDetailsStore($option);
		$ds->bodytext = $index.'. '.SwatString::condense($option->bodytext);
		$ds->correct = ($option->id == $option->correct_option);
		$ds->component = $this->getComponentName();

		return $ds;
	}

	// }}}
	// {{{ protected function getQuestionOptions()

	protected function getQuestionOptions(SwatTableView $view)
	{
		$sql = sprintf(
			'select InquisitionQuestionOption.id, question, bodytext, title,
				correct_option
				from InquisitionQuestionOption
			inner join InquisitionQuestion on
				InquisitionQuestionOption.question = InquisitionQuestion.id
			where InquisitionQuestion.inquisition = %s
			order by InquisitionQuestion.displayorder, InquisitionQuestion.id,
				InquisitionQuestionOption.displayorder,
				InquisitionQuestionOption.id',
			$this->app->db->quote($this->id, 'integer'));

		return SwatDB::query($this->app->db, $sql);
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
			'packages/inquisition/admin/styles/inquisition-details.css');
	}

	// }}}
}

?>
