<?php

/**
 * Details page for inquisitions.
 *
 * @copyright 2011-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class InquisitionInquisitionDetails extends AdminIndex
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var InquisitionInquisition
     */
    protected $inquisition;

    // init phase

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

    protected function initInquisition()
    {
        $this->inquisition = SwatDBClassMap::new(InquisitionInquisition::class);
        $this->inquisition->setDatabase($this->app->db);

        if (!$this->inquisition->load($this->id)) {
            throw new AdminNotFoundException(sprintf(
                'A inquisition with the id of “%s” does not exist',
                $this->id
            ));
        }

        $bindings = $this->inquisition->question_bindings;

        // efficiently load questions
        $questions = $bindings->loadAllSubDataObjects(
            'question',
            $this->app->db,
            'select * from InquisitionQuestion where id in (%s)',
            SwatDBClassMap::get(InquisitionQuestionWrapper::class)
        );

        // efficiently load question options
        if ($questions instanceof InquisitionQuestionWrapper) {
            $questions->loadAllSubRecordsets(
                'options',
                SwatDBClassMap::get(InquisitionQuestionOptionWrapper::class),
                'InquisitionQuestionOption',
                'question',
                '',
                'displayorder, id'
            );
        }
    }

    protected function getUiXml()
    {
        return __DIR__ . '/details.xml';
    }

    // process phase

    protected function processActions(SwatView $view, SwatActions $actions)
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

    // build phase

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

    protected function getDetailsStore(InquisitionInquisition $inquisition)
    {
        return new SwatDetailsStore($inquisition);
    }

    protected function buildView(SwatView $view)
    {
        parent::buildView($view);

        if ($view->id == 'question_view') {
            $image_class = SwatDBClassMap::new(InquisitionQuestionImage::class);
            $image_class->setDatabase($this->app->db);

            $view->getColumn('image_count_column')->visible =
                $image_class->hasImageSet();
        }
    }

    protected function getTableModel(SwatView $view): ?SwatTableModel
    {
        $model = null;

        switch ($view->id) {
            case 'question_view':
                $model = $this->getQuestionTableModel($view);
                break;
        }

        return $model;
    }

    protected function getQuestionTableModel(SwatTableView $view)
    {
        $store = new SwatTableStore();

        foreach ($this->inquisition->question_bindings as $question_binding) {
            $store->add($this->getQuestionDetailsStore($question_binding));
        }

        $this->ui->getWidget('question_order')->sensitive = (count($store) > 1);

        return $store;
    }

    protected function getQuestionDetailsStore(
        InquisitionInquisitionQuestionBinding $question_binding
    ) {
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
            $li_tag->class = ($option->id === $correct_id)
                ? 'correct'
                : 'incorrect';

            $li_tag->setContent($option->title);
            $li_tag->display();
        }

        echo '</ol>';

        $ds->bodytext = ob_get_clean();

        return $ds;
    }

    protected function buildToolbars()
    {
        foreach ($this->ui->getRoot()->getDescendants('SwatToolBar') as $toolbar) {
            $toolbar->setToolLinkValues(
                [
                    $this->inquisition->id,
                ]
            );
        }
    }

    protected function buildViewRendererLinks()
    {
        if ($this->inquisition instanceof InquisitionInquisition) {
            $link_suffix = $this->getLinkSuffix();

            foreach ($this->ui->getRoot()->getDescendants('SwatTableView') as $view) {
                foreach ($view->getColumns() as $column) {
                    foreach ($column->getRenderers() as $renderer) {
                        if ($renderer instanceof SwatLinkCellRenderer) {
                            $renderer->link .= $link_suffix;
                        }
                    }
                }
            }
        }
    }

    protected function buildNavBar()
    {
        parent::buildNavBar();
        $this->navbar->createEntry($this->inquisition->title);
    }

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

    // finalize phase

    public function finalize()
    {
        parent::finalize();

        $this->layout->addHtmlHeadEntry(
            'packages/inquisition/admin/styles/inquisition-details.css'
        );
    }
}
