<?php

require_once 'Swat/SwatTableStore.php';
require_once 'Swat/SwatDetailsStore.php';
require_once 'SwatDB/SwatDB.php';
require_once 'Admin/pages/AdminIndex.php';
require_once 'Admin/exceptions/AdminNotFoundException.php';
require_once 'Inquisition/dataobjects/InquisitionQuestionOption.php';

/**
 * Details page for an option
 *
 * @package   Inquisition
 * @copyright 2012 silverorange
 */
class InquisitionOptionDetails extends AdminIndex
{
	// {{{ protected properties

	/**
	 * @var InquisitionQuestionOption
	 */
	protected $option;

	// }}}

	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		parent::initInternal();

		$this->ui->loadFromXML($this->getUiXml());

		$this->initOption();
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
	// {{{ protected function getUiXml()

	protected function getUiXml()
	{
		return 'Inquisition/admin/components/Option/details.xml';
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
		$this->buildToolbar();

		$view = $this->ui->getWidget('details_view');
		$view->data = $this->getDetailsStore($this->option);
	}

	// }}}
	// {{{ protected function getDetailsStore()

	protected function getDetailsStore(InquisitionQuestionOption $option)
	{
		return new SwatDetailsStore($option);
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
	// {{{ protected function buildNavBar()

	protected function buildNavBar()
	{
		parent::buildNavBar();

		$this->navbar->createEntry(
			$this->option->question->inquisition->title,
			sprintf(
				'Inquisition/Details?id=%s',
				$this->option->question->inquisition->id
			)
		);

		$this->navbar->createEntry(
			sprintf('Question %s', $this->option->question->position),
			sprintf(
				'Question/Details?id=%s',
				$this->option->question->id
			)
		);

		$this->navbar->createEntry(
			sprintf('Option %s', $this->option->position)
		);
	}

	// }}}
	// {{{ protected function buildFrame()

	protected function buildFrame()
	{
		$frame = $this->ui->getWidget('details_frame');
		$frame->title = sprintf('Option %s', $this->option->position);
	}

	// }}}
	// {{{ protected function buildToolbar()

	protected function buildToolbar()
	{
		$toolbar = $this->ui->getWidget('details_toolbar');
		$toolbar->setToolLinkValues(array($this->option->id));

		$toolbar = $this->ui->getWidget('image_toolbar');
		$toolbar->setToolLinkValues(array($this->option->id));
	}

	// }}}
}

?>
