<?php

/**
 * Change order page for questions.
 *
 * @copyright 2011-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class InquisitionQuestionOrder extends AdminDBOrder
{
    /**
     * @var InquisitionInquisition
     */
    protected $inquisition;

    // init phase

    protected function initInternal()
    {
        parent::initInternal();

        $this->initInquisition();
    }

    protected function initInquisition()
    {
        $id = SiteApplication::initVar('id');

        if ($id == '') {
            throw new AdminNotFoundException(
                'No inquisition id specified.'
            );
        }

        if (is_numeric($id)) {
            $id = intval($id);
        }

        $this->inquisition = SwatDBClassMap::new(InquisitionInquisition::class);
        $this->inquisition->setDatabase($this->app->db);

        if (!$this->inquisition->load($id)) {
            throw new AdminNotFoundException(
                sprintf(
                    'An inquisition with the id of “%s” does not exist',
                    $id
                )
            );
        }
    }

    // process phase

    protected function saveIndex($id, $index)
    {
        SwatDB::updateColumn(
            $this->app->db,
            'InquisitionInquisitionQuestionBinding',
            'integer:displayorder',
            $index,
            'integer:id',
            [$id]
        );
    }

    protected function getUpdatedMessage()
    {
        return new SwatMessage(
            Inquisition::_('Question order has been updated.')
        );
    }

    protected function relocate()
    {
        $this->app->relocate(
            sprintf(
                'Inquisition/Details?id=%s',
                $this->inquisition->id
            )
        );
    }

    // build phase

    protected function buildInternal()
    {
        $this->ui->getWidget('order_frame')->title =
            Inquisition::_('Change Question Order');

        $this->ui->getWidget('order')->width = '500px';
        $this->ui->getWidget('order')->height = '500px';

        parent::buildInternal();
    }

    protected function buildNavBar()
    {
        parent::buildNavBar();

        $this->navbar->popEntries(2);

        $this->navbar->createEntry(
            Inquisition::_('Inquisition'),
            'Inquisition'
        );

        $this->navbar->createEntry(
            $this->inquisition->title,
            sprintf(
                'Inquisition/Details?id=%s',
                $this->inquisition->id
            )
        );

        $this->navbar->createEntry(Inquisition::_('Change Question Order'));
    }

    protected function buildForm()
    {
        parent::buildForm();

        $form = $this->ui->getWidget('order_form');
        $form->addHiddenField('id', $this->inquisition->id);
    }

    protected function loadData()
    {
        $sum = 0;
        $order_widget = $this->ui->getWidget('order');

        foreach ($this->inquisition->question_bindings as $question_binding) {
            $sum += $question_binding->displayorder;

            $order_widget->addOption(
                $question_binding->id,
                $question_binding->question->bodytext,
                'text/xml'
            );
        }

        $options_list = $this->ui->getWidget('options');
        $options_list->value = ($sum == 0) ? 'auto' : 'custom';
    }
}
