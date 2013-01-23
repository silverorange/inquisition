<?php

require_once 'Swat/SwatTableStore.php';
require_once 'Swat/SwatDetailsStore.php';
require_once 'SwatDB/SwatDB.php';
require_once 'Admin/pages/AdminIndex.php';
require_once 'Admin/exceptions/AdminNotFoundException.php';

/**
 * Details page for a question
 *
 * @package   Inquisition
 * @copyright 2012-2013 silverorange
 */
class InquisitionQuestionDetails extends AdminIndex
{
	// {{{ protected properties

	/**
	 * @var InquisitionQuestion
	 */
	protected $question;

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

		$this->ui->loadFromXML($this->getUiXml());

		$this->initQuestion();
		$this->initInquisition();
	}

	// }}}
	// {{{ protected function initQuestion()

	protected function initQuestion()
	{
		$id = SiteApplication::initVar('id');

		if (is_numeric($id)) {
			$id = intval($id);
		}

		$class = SwatDBClassMap::get('InquisitionQuestion');
		$this->question = new $class;
		$this->question->setDatabase($this->app->db);

		if (!$this->question->load($id)) {
			throw new AdminNotFoundException(
				sprintf(
					'A question with the id of “%s” does not exist', $id
				)
			);
		}
	}

	// }}}
	// {{{ protected function initInquisition()

	protected function initInquisition()
	{
		$inquisition_id = SiteApplication::initVar('inquisition');

		if ($inquisition_id !== null) {
			$this->inquisition = $this->loadInquisition($inquisition_id);
		}
	}

	// }}}
	// {{{ protected function loadInquisition()

	protected function loadInquisition($inquisition_id)
	{
		$class = SwatDBClassMap::get('InquisitionInquisition');
		$inquisition = new $class;
		$inquisition->setDatabase($this->app->db);

		if (!$inquisition->load($inquisition_id)) {
			throw new AdminNotFoundException(
				sprintf(
					'Inquisition with id ‘%s’ not found.',
					$inquisition_id
				)
			);
		}

		return $inquisition;
	}

	// }}}
	// {{{ protected function getUiXml()

	protected function getUiXml()
	{
		return 'Inquisition/admin/components/Question/details.xml';
	}

	// }}}

	// process phase
	// {{{ protected function processActions()

	protected function processActions(SwatView $view, SwatActions $actions)
	{
		switch ($view->id) {
		case 'image_view':
			switch ($actions->selected->id) {
			case 'image_delete':
				$this->app->replacePage('Question/ImageDelete');

				$this->app->getPage()->setId($this->question->id);
				$this->app->getPage()->setInquisition($this->inquisition);
				$this->app->getPage()->setItems($view->getSelection());
				break;
			}
			break;

		case 'hint_view':
			switch ($actions->selected->id) {
			case 'hint_delete':
				$this->app->replacePage('Question/HintDelete');

				$this->app->getPage()->setId($this->question->id);
				$this->app->getPage()->setInquisition($this->inquisition);
				$this->app->getPage()->setItems($view->getSelection());
				break;
			}
			break;

		case 'option_view':
			switch ($actions->selected->id) {
			case 'option_delete':
				$this->app->replacePage('Option/Delete');

				$this->app->getPage()->setId($this->question->id);
				$this->app->getPage()->setInquisition($this->inquisition);
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

		$this->buildFrame();
		$this->buildToolbars();
		$this->buildViewRendererLinks();

		$view = $this->ui->getWidget('details_view');
		$view->data = $this->getDetailsStore($this->question);
	}

	// }}}
	// {{{ protected function getDetailsStore()

	protected function getDetailsStore(InquisitionQuestion $question)
	{
		$ds = new SwatDetailsStore($question);

		return $ds;
	}

	// }}}
	// {{{ protected function getTableModel()

	protected function getTableModel(SwatView $view)
	{
		$model = null;

		switch ($view->id) {
		case 'image_view':
			$model = $this->getImageTableModel($view);
			break;
		case 'hint_view':
			$model = $this->getHintTableModel($view);
			break;
		case 'option_view':
			$model = $this->getOptionTableModel($view);
			break;
		}

		return $model;
	}

	// }}}
	// {{{ protected function getImageTableModel()

	protected function getImageTableModel(SwatView $view)
	{
		$store = new SwatTableStore();

		foreach ($this->question->images as $image) {
			$store->add($this->getImageDetailsStore($image));
		}

		$this->ui->getWidget('image_order')->sensitive = (count($store) > 1);

		return $store;
	}

	// }}}
	// {{{ protected function getHintTableModel()

	protected function getHintTableModel(SwatView $view)
	{
		$store = new SwatTableStore();

		foreach ($this->question->hints as $hint) {
			$ds = new SwatDetailsStore($hint);
			$ds->bodytext = SwatString::condense($hint->bodytext, 50);

			$store->add($ds);
		}

		$this->ui->getWidget('hint_order')->sensitive = (count($store) > 1);

		return $store;
	}

	// }}}
	// {{{ protected function getOptionTableModel()

	protected function getOptionTableModel(SwatView $view)
	{
		$store = new SwatTableStore();

		foreach ($this->getOptions($view) as $option) {
			$store->add($this->getOptionDetailsStore($option));
		}

		$sensitive = (count($store) > 1);

		$this->ui->getWidget('option_order')->sensitive = $sensitive;
		$this->ui->getWidget('correct_option')->sensitive = $sensitive;

		return $store;
	}

	// }}}
	// {{{ protected function getImageDetailsStore()

	protected function getImageDetailsStore(SiteImage $image)
	{
		$ds = new SwatDetailsStore($image);

		$ds->image = $image->getUri('thumb', '../');
		$ds->width = $image->getWidth('thumb', '../');
		$ds->height = $image->getHeight('thumb', '../');

		$ds->preview_image = $image->getUri('small', '../');
		$ds->preview_width = $image->getWidth('small', '../');
		$ds->preview_height = $image->getHeight('small', '../');

		return $ds;
	}

	// }}}
	// {{{ protected function getOptionDetailsStore()

	protected function getOptionDetailsStore(InquisitionQuestionOption $option)
	{
		$correct_option = $this->question->correct_option;

		$ds = new SwatDetailsStore($option);

		$ds->title = sprintf('%s. %s', $option->position, $option->title);
		$ds->image_count = count($option->images);
		$ds->correct =
			($correct_option instanceof InquisitionQuestionOption) &&
			($correct_option->id === $option->id);

		return $ds;
	}

	// }}}
	// {{{ protected function getOptions()

	protected function getOptions(SwatTableView $view)
	{
		$sql = sprintf(
			'select * from InquisitionQuestionOption
			where InquisitionQuestionOption.question = %s
			order by InquisitionQuestionOption.displayorder,
				InquisitionQuestionOption.question',
			$this->app->db->quote($this->question->id, 'integer')
		);

		return SwatDB::query(
			$this->app->db,
			$sql,
			SwatDBClassMap::get('InquisitionQuestionOptionWrapper')
		);
	}

	// }}}
	// {{{ protected function buildFrame()

	protected function buildFrame()
	{
		$frame = $this->ui->getWidget('details_frame');
		$frame->title = sprintf($this->getQuestionTitle());
	}

	// }}}
	// {{{ protected function buildToolbars()

	protected function buildToolbars()
	{
		foreach ($this->ui->getRoot()->getDescendants('SwatToolBar') as
			$toolbar) {
			$toolbar->setToolLinkValues(
				array(
					$this->question->id,
				)
			);

			if ($this->inquisition instanceof InquisitionInquisition) {
				$link_suffix = $this->getLinkSuffix();
				foreach ($toolbar->getToolLinks() as $tool_link) {
					if (substr($tool_link->link, -5) === 'id=%s' ||
					substr($tool_link->link, -11) === 'question=%s') {
						$tool_link->link.= $link_suffix;
					}
				}
			}
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

		if ($this->inquisition instanceof InquisitionInquisition) {
			$this->navbar->createEntry(
				$this->inquisition->title,
				sprintf(
					'Inquisition/Details?id=%s',
					$this->inquisition->id
				)
			);
		}

		$this->navbar->createEntry(
			$this->getQuestionTitle()
		);
	}

	// }}}
	// {{{ protected function getQuestionTitle()

	protected function getQuestionTitle()
	{
		return ($this->inquisition instanceof InquisitionInquisition) ?
			sprintf(
				Inquisition::_('Question %s'),
				$this->question->getPosition($this->inquisition)
			) :
			Inquisition::_('Question');
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
			'packages/inquisition/admin/styles/inquisition-details.css');
	}

	// }}}
}

?>
