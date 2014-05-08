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
 * @copyright 2011-2014 silverorange
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

		$bindings = $this->inquisition->question_bindings;

		// efficiently load questions
		$questions = $bindings->loadAllSubDataObjects(
			'question',
			$this->app->db,
			'select * from InquisitionQuestion where id in (%s)',
			SwatDBClassMap::get('InquisitionQuestionWrapper')
		);

		// efficiently load question options
		if ($questions instanceof InquisitionQuestionWrapper) {
			$questions->loadAllSubRecordsets(
				'options',
				SwatDBClassMap::get('InquisitionQuestionOptionWrapper'),
				'InquisitionQuestionOption',
				'question',
				'',
				'displayorder, id'
			);
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

		$view = $this->ui->getWidget('details_view');
		$view->data = $this->getDetailsStore($this->inquisition);

		$this->ui->getWidget('details_frame')->title =
			$this->inquisition->title;

		$this->buildToolbars();
		$this->buildViewRendererLinks();

		$field = $view->getField('createdate');
		$renderer = $field->getFirstRenderer();
		$renderer->display_time_zone = $this->app->default_time_zone;
	}

	// }}}
	// {{{ protected function getDetailsStore()

	protected function getDetailsStore(InquisitionInquisition $inquisition)
	{
		return new SwatDetailsStore($inquisition);
	}

	// }}}
	// {{{ protected function buildView()

	protected function buildView(SwatView $view)
	{
		parent::buildView($view);

		if ($view->id == 'question_view') {
			$class_name = SwatDBClassMap::get('InquisitionQuestionImage');
			$image_class = new $class_name();
			$image_class->setDatabase($this->app->db);

			$view->getColumn('image_count_column')->visible =
				$image_class->hasImageSet();
		}
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
		$store = new SwatTableStore();

		foreach ($this->inquisition->question_bindings as $question_binding) {
			$store->add($this->getQuestionDetailsStore($question_binding));
		}

		$this->ui->getWidget('question_order')->sensitive = (count($store) > 1);

		return $store;
	}

	// }}}
	// {{{ protected function getQuestionDetailsStore()

	protected function getQuestionDetailsStore(
		InquisitionInquisitionQuestionBinding $question_binding)
	{
		$question = $question_binding->question;
		$correct_id = $question->getInternalValue('correct_option');

		$ds = new SwatDetailsStore($question);

		$ds->title = sprintf(
			Inquisition::_('Question %s'),
			$question_binding->getPosition()
		);

		$ds->image_count = count($question->images);
		$ds->option_count = count($question->options);

		$li_tag = new SwatHtmlTag('li');

		ob_start();

		echo $question->bodytext;
		echo '<ol>';

		foreach ($question->options as $option) {
			$li_tag->class = ($option->id === $correct_id) ?
				'correct' : 'incorrect';

			$li_tag->setContent($option->title);
			$li_tag->display();
		}

		echo '</ol>';

		$ds->bodytext = ob_get_clean();

		return $ds;
	}

	// }}}
	// {{{ protected function buildToolbars()

	protected function buildToolbars()
	{
		foreach ($this->ui->getRoot()->getDescendants('SwatToolBar') as
			$toolbar) {
			$toolbar->setToolLinkValues(
				array(
					$this->inquisition->id,
				)
			);
		}
	}

	// }}}
	// {{{ protected function buildViewRendererLinks()

	protected function buildViewRendererLinks()
	{
		if ($this->inquisition instanceof InquisitionInquisition) {
			$link_suffix = $this->getLinkSuffix();

			foreach ($this->ui->getRoot()->getDescendants('SwatTableView') as
				$view) {
				foreach ($view->getColumns() as $column) {
					foreach ($column->getRenderers() as $renderer) {
						if ($renderer instanceof SwatLinkCellRenderer) {
							$renderer->link.= $link_suffix;
						}
					}
				}
			}
		}
	}

	// }}}
	// {{{ protected function buildNavBar()

	protected function buildNavBar()
	{
		parent::buildNavBar();
		$this->navbar->createEntry($this->inquisition->title);
	}

	// }}}
	// {{{ protected function getLinkSuffix()

	protected function getLinkSuffix()
	{
		$suffix = null;
		if ($this->inquisition instanceof InquisitionInquisition) {
			$suffix = sprintf(
				'&inquisition=%s',
				$this->inquisition->id
			);
		}

		return $suffix;
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
