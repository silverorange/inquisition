<?php

/**
 * Edit page for a question.
 *
 * @copyright 2011-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class InquisitionQuestionEdit extends AdminDBEdit
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

        $this->ui->loadFromXML($this->getUiXml());

        $this->initQuestion();
        $this->initInquisition();
    }

    protected function initQuestion()
    {
        $this->question = SwatDBClassMap::new(InquisitionQuestion::class);
        $this->question->setDatabase($this->app->db);

        if ($this->id !== null && !$this->question->load($this->id)) {
            throw new AdminNotFoundException(
                sprintf(
                    'Question with id ‘%s’ not found.',
                    $this->id
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

    protected function getUiXml()
    {
        return __DIR__ . '/edit.xml';
    }

    // process phase

    protected function saveDBData(): void
    {
        $this->updateQuestion();
        $this->question->save();

        $this->app->messages->add(
            new SwatMessage(
                Inquisition::_('Question has been saved.')
            )
        );
    }

    protected function updateQuestion()
    {
        $values = $this->ui->getValues(
            [
                'bodytext',
                'enabled',
            ]
        );

        $this->question->bodytext = $values['bodytext'];
        $this->question->enabled = $values['enabled'];
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

    protected function loadDBData()
    {
        $this->ui->setValues($this->question->getAttributes());
    }

    protected function buildForm()
    {
        parent::buildForm();

        if ($this->inquisition instanceof InquisitionInquisition) {
            $form = $this->ui->getWidget('edit_form');
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

        $this->navbar->createEntry(Inquisition::_('Edit Question'));
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

    // finalize phase

    public function finalize()
    {
        parent::finalize();

        $this->layout->addHtmlHeadEntry(
            'packages/inquisition/admin/styles/inquisition-question-edit.css'
        );
    }
}
