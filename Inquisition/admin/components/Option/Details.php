<?php

/**
 * Details page for an option
 *
 * @package   Inquisition
 * @copyright 2012-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class InquisitionOptionDetails extends AdminIndex
{
	// {{{ protected properties

	/**
	 * @var InquisitionQuestionOption
	 */
	protected $option;

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

		$this->initOption();
		$this->initInquisition();
	}

	// }}}
	// {{{ protected function initOption()

	protected function initOption()
	{
		$id = SiteApplication::initVar('id');

		if (is_numeric($id)) {
			$id = intval($id);
		}

		$class = SwatDBClassMap::get('InquisitionQuestionOption');
		$this->option = new $class;
		$this->option->setDatabase($this->app->db);

		if (!$this->option->load($id)) {
			throw new AdminNotFoundException(
				sprintf(
					'An option with the id of “%s” does not exist', $id
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
		return __DIR__.'/details.xml';
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
				$this->app->replacePage('Option/ImageDelete');

				$this->app->getPage()->setId($this->option->id);
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
		$this->buildImageFrame();
		$this->buildToolbar();
		$this->buildViewRendererLinks();

		$view = $this->ui->getWidget('details_view');
		$view->data = $this->getDetailsStore($this->option);
	}

	// }}}
	// {{{ protected function buildImageFrame()

	protected function buildImageFrame()
	{
		$class_name = SwatDBClassMap::get('InquisitionQuestionOptionImage');
		$image_class = new $class_name();
		$image_class->setDatabase($this->app->db);

		$this->ui->getWidget('images_frame')->visible =
			$image_class->hasImageSet();
	}

	// }}}
	// {{{ protected function getDetailsStore()

	protected function getDetailsStore(InquisitionQuestionOption $option)
	{
		return new SwatDetailsStore($option);
	}

	// }}}
	// {{{ protected function getTableModel()

	protected function getTableModel(SwatView $view): ?SwatTableModel
	{
		$model = null;

		switch ($view->id) {
		case 'image_view':
			$model = $this->getImageTableModel($view);
			break;
		}

		return $model;
	}

	// }}}
	// {{{ protected function getImageTableModel()

	protected function getImageTableModel(SwatView $view)
	{
		$store = new SwatTableStore();

		foreach ($this->option->images as $image) {
			$store->add($this->getImageDetailsStore($image));
		}

		$this->ui->getWidget('image_order')->sensitive = (count($store) > 1);

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
	// {{{ protected function buildToolbar()

	protected function buildToolbar()
	{
		foreach ($this->ui->getRoot()->getDescendants('SwatToolBar') as
			$toolbar) {
			$toolbar->setToolLinkValues(
				array(
					$this->option->id,
				)
			);

			if ($this->inquisition instanceof InquisitionInquisition) {
				$link_suffix = $this->getLinkSuffix();
				foreach ($toolbar->getToolLinks() as $tool_link) {
					if (mb_substr($tool_link->link, -5) === 'id=%s' ||
						mb_substr($tool_link->link, -9) === 'option=%s'
					) {
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
			$this->getQuestionTitle(),
			sprintf(
				'Question/Details?id=%s%s',
				$this->option->question->id,
				$this->getLinkSuffix()
			)
		);

		$this->navbar->createEntry($this->getTitle());
	}

	// }}}
	// {{{ protected function buildFrame()

	protected function buildFrame()
	{
		$frame = $this->ui->getWidget('details_frame');
		$frame->title = $this->getTitle();
	}

	// }}}
	// {{{ protected function getTitle()

	protected function getTitle()
	{
		return sprintf(
			Inquisition::_('Option %s'),
			$this->option->position
		);
	}

	// }}}
	// {{{ protected function getQuestionTitle()

	protected function getQuestionTitle()
	{
		// TODO: Update this with some version of getPosition().
		return Inquisition::_('Question');
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
}

?>
