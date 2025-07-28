<?php

/**
 * Change order page for question hints.
 *
 * @copyright 2013-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class InquisitionQuestionHintOrder extends AdminDBOrder
{
    /**
     * @var InquisitionQuestion
     */
    protected $question;

    /**
     * @var InquisitionInquisition
     */
    protected $inquisition;

    // init phase

    protected function initInternal()
    {
        parent::initInternal();

        $this->initQuestion();
        $this->initInquisition();
    }

    protected function initQuestion()
    {
        $id = SiteApplication::initVar('id');

        if ($id == '') {
            throw new AdminNotFoundException(
                'No question id specified.'
            );
        }

        if (is_numeric($id)) {
            $id = intval($id);
        }

        $this->question = SwatDBClassMap::new(InquisitionQuestion::class);
        $this->question->setDatabase($this->app->db);

        if (!$this->question->load($id)) {
            throw new AdminNotFoundException(
                sprintf(
                    'A question with the id of “%s” does not exist',
                    $id
                )
            );
        }
    }

    protected function initInquisition()
    {
        $inquisition_id = SiteApplication::initVar('inquisition');

        if ($inquisition_id !== null) {
            $this->inquisition = $this->loadInquisition($inquisition_id);
        }
    }

    protected function loadInquisition($inquisition_id)
    {
        $inquisition = SwatDBClassMap::new(InquisitionInquisition::class);
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

    // process phase

    protected function saveIndex($id, $index)
    {
        SwatDB::updateColumn(
            $this->app->db,
            'InquisitionQuestionHint',
            'integer:displayorder',
            $index,
            'integer:id',
            [$id]
        );
    }

    protected function getUpdatedMessage()
    {
        return new SwatMessage(Inquisition::_('Hint order has been updated.'));
    }

    protected function relocate()
    {
        $this->app->relocate(
            sprintf(
                'Question/Details?id=%s%s',
                $this->question->id,
                $this->getLinkSuffix()
            )
        );
    }

    // build phase

    protected function loadData()
    {
        $sum = 0;
        $order_widget = $this->ui->getWidget('order');

        foreach ($this->question->hints as $hint) {
            $sum += $hint->displayorder;

            $order_widget->addOption(
                $hint->id,
                SwatString::condense($hint->bodytext, 50),
                'text/xml'
            );
        }

        $options_list = $this->ui->getWidget('options');
        $options_list->value = ($sum == 0) ? 'auto' : 'custom';
    }

    protected function buildInternal()
    {
        $this->ui->getWidget('order_frame')->title = $this->getTitle();

        $this->ui->getWidget('order')->width = '500px';
        $this->ui->getWidget('order')->height = '200px';

        parent::buildInternal();
    }

    protected function buildForm()
    {
        parent::buildForm();

        $form = $this->ui->getWidget('order_form');
        $form->addHiddenField('id', $this->question->id);

        if ($this->inquisition instanceof InquisitionInquisition) {
            $form->addHiddenField('inquisition', $this->inquisition->id);
        }
    }

    protected function buildNavBar()
    {
        parent::buildNavBar();

        $this->navbar->popEntry();

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
                $this->question->id,
                $this->getLinkSuffix()
            )
        );

        $this->navbar->createEntry($this->getTitle());
    }

    protected function getQuestionTitle()
    {
        // TODO: Update this with some version of getPosition().
        return Inquisition::_('Question');
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

    protected function getTitle()
    {
        return Inquisition::_('Change Hint Order');
    }
}
