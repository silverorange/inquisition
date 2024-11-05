<?php

/**
 * Details page for a question
 *
 * @package   Inquisition
 * @copyright 2012-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class InquisitionQuestionDetails extends AdminIndex
{
	// {{{ protected properties

	/**
	 * @var InquisitionQuestion
	 */
	protected $question;

	/**
	 * @var InquisitionQuestion
	 */
	protected $prev_question;

	/**
	 * @var InquisitionQuestion
	 */
	protected $next_question;

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

		$this->initPrevQuestion();
		$this->initNextQuestion();
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
	// {{{ protected function initPrevQuestion()

	protected function initPrevQuestion()
	{
		$previous = null;

		if ($this->inquisition instanceof InquisitionInquisition) {
			foreach ($this->inquisition->question_bindings as $binding) {
				$question_id = $binding->getInternalValue('question');

				if (($previous instanceof InquisitionInquisitionQuestionBinding)
					&& ($question_id === $this->question->id)) {

					$this->prev_question = $previous->question;
				}

				$previous = $binding;
			}
		}
	}

	// }}}
	// {{{ protected function initNextQuestion()

	protected function initNextQuestion()
	{
		$next = false;

		if ($this->inquisition instanceof InquisitionInquisition) {
			foreach ($this->inquisition->question_bindings as $binding) {
				if ($next) {
					$this->next_question = $binding->question;
					break;
				}

				$question_id = $binding->getInternalValue('question');

				if ($question_id === $this->question->id) {
					$next = true;
				}
			}
		}
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
		$this->buildImageFrame();
		$this->buildToolbars();
		$this->buildViewRendererLinks();

		$view = $this->ui->getWidget('details_view');
		$view->data = $this->getDetailsStore($this->question);
	}

	// }}}
	// {{{ protected function buildImageFrame()

	protected function buildImageFrame()
	{
		$class_name = SwatDBClassMap::get('InquisitionQuestionImage');
		$image_class = new $class_name();
		$image_class->setDatabase($this->app->db);

		$this->ui->getWidget('images_frame')->visible =
			$image_class->hasImageSet();
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

	protected function getTableModel(SwatView $view): ?SwatTableModel
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
		$toolbars = $this->ui->getRoot()->getDescendants('SwatToolBar');
		foreach ($toolbars as $toolbar) {
			$toolbar->setToolLinkValues(
				array(
					$this->question->id,
				)
			);

			if ($this->inquisition instanceof InquisitionInquisition) {
				$link_suffix = $this->getLinkSuffix();
				foreach ($toolbar->getToolLinks() as $tool_link) {
					if (mb_substr($tool_link->link, -5) === 'id=%s' ||
						mb_substr($tool_link->link, -11) === 'question=%s'
					) {
						$tool_link->link.= $link_suffix;
					}
				}
			}
		}

		// Hide the next/prev links if there is no inquisiton.
		if (!($this->inquisition instanceof InquisitionInquisition)) {
			$this->ui->getWidget('prev_question')->visible = false;
			$this->ui->getWidget('next_question')->visible = false;
		} else {
			$has_prev = ($this->prev_question instanceof InquisitionQuestion);
			$has_next = ($this->next_question instanceof InquisitionQuestion);

			$link = $this->ui->getWidget('prev_question');
			$link->sensitive = ($has_prev);
			$link->value = array(
				($has_prev) ? $this->prev_question->id : null,
			);

			$link = $this->ui->getWidget('next_question');
			$link->sensitive = ($has_next);
			$link->value = array(
				($has_next) ? $this->next_question->id : null,
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
