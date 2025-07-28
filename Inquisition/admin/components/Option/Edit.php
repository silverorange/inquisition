<?php

/**
 * Edit page for an option.
 *
 * @copyright 2012-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class InquisitionOptionEdit extends AdminDBEdit
{
    /**
     * @var InquisitionQuestionOption
     */
    protected $option;

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

        $this->initOption();
        $this->initQuestion();
        $this->initInquisition();
    }

    protected function initOption()
    {
        $this->option = SwatDBClassMap::new(InquisitionQuestionOption::class);
        $this->option->setDatabase($this->app->db);

        if ($this->id !== null && !$this->option->load($this->id)) {
            throw new AdminNotFoundException(
                sprintf(
                    'Question option with id ‘%s’ not found.',
                    $this->id
                )
            );
        }
    }

    protected function initQuestion()
    {
        if ($this->option->id != null) {
            $this->question = $this->option->question;
        } else {
            $question_id = SiteApplication::initVar('question');

            if (is_numeric($question_id)) {
                $question_id = intval($question_id);
            }

            $this->question = SwatDBClassMap::new(InquisitionQuestion::class);
            $this->question->setDatabase($this->app->db);

            if (!$this->question->load($question_id)) {
                throw new AdminNotFoundException(
                    sprintf(
                        'A question with the id of “%s” does not exist',
                        $question_id
                    )
                );
            }
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
        $this->updateOption();

        $this->option->save();

        $this->app->messages->add(
            new SwatMessage(Inquisition::_('Option has been saved.'))
        );
    }

    protected function updateOption()
    {
        $values = $this->ui->getValues(
            [
                'title',
            ]
        );

        $this->option->title = $values['title'];
        $this->option->question = $this->question->id;

        if ($this->option->id === null) {
            $this->option->question = $this->question;

            // set displayorder so the new question appears at the end of the
            // list of the current options by default.
            $sql = sprintf(
                'select max(displayorder) from InquisitionQuestionOption
				where question = %s',
                $this->app->db->quote($this->question->id, 'integer')
            );

            $max_displayorder = SwatDB::queryOne($this->app->db, $sql);
            $new_displayorder = floor(($max_displayorder + 10) / 10) * 10;
            $this->option->displayorder = $new_displayorder;
        }
    }

    protected function relocate()
    {
        $this->app->relocate(
            sprintf(
                'Option/Details?id=%s%s',
                $this->option->id,
                $this->getLinkSuffix()
            )
        );
    }

    // build phase

    protected function loadDBData()
    {
        $this->ui->setValues($this->option->getAttributes());
    }

    protected function buildForm()
    {
        parent::buildForm();

        $form = $this->ui->getWidget('edit_form');
        $form->addHiddenField('question', $this->question->id);

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

        if ($this->option->id !== null) {
            $this->navbar->createEntry(
                $this->getOptionTitle(),
                sprintf(
                    'Option/Details?id=%s%s',
                    $this->option->id,
                    $this->getLinkSuffix()
                )
            );
        }

        $this->navbar->createEntry($this->getTitle());
    }

    protected function getQuestionTitle()
    {
        // TODO: Update this with some version of getPosition().
        return Inquisition::_('Question');
    }

    protected function getOptionTitle()
    {
        return sprintf(
            Inquisition::_('Option %s'),
            $this->option->position
        );
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
        return ($this->option->id === null)
            ? Inquisition::_('New Option')
            : Inquisition::_('Edit Option');
    }
}
