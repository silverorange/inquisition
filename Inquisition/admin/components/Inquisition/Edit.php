<?php

/**
 * Edit page for inquisitions.
 *
 * @copyright 2011-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class InquisitionInquisitionEdit extends AdminDBEdit
{
    /**
     * @var InquisitionInquisition
     */
    protected $inquisition;

    // init phase

    protected function initInternal()
    {
        parent::initInternal();
        $this->ui->loadFromXML($this->getUiXml());
        $this->initInquisition();
    }

    protected function initInquisition()
    {
        $this->inquisition = SwatDBClassMap::new(InquisitionInquisition::class);
        $this->inquisition->setDatabase($this->app->db);

        if ($this->id != '') {
            if (!$this->inquisition->load($this->id)) {
                throw new AdminNotFoundException(
                    sprintf('Inquisition with id ‘%s’ not found.', $this->id)
                );
            }
        }
    }

    protected function getUiXml()
    {
        return __DIR__ . '/edit.xml';
    }

    // process phase

    protected function saveDBData(): void
    {
        $this->updateInquisition();

        if ($this->inquisition->isModified()) {
            $this->inquisition->save();
            $this->app->messages->add($this->getSavedMessage());
        }
    }

    protected function updateInquisition()
    {
        $values = $this->ui->getValues(
            [
                'title',
            ]
        );

        $this->inquisition->title = $values['title'];

        if ($this->ui->hasWidget('enabled')) {
            $this->inquisition->enabled =
                $this->ui->getWidget('enabled')->value;
        }

        if ($this->inquisition->id === null) {
            $now = new SwatDate();
            $now->toUTC();
            $this->inquisition->createdate = $now;
        }
    }

    protected function getSavedMessage()
    {
        return new SwatMessage(Inquisition::_('Inquisition has been saved.'));
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

    protected function loadDBData()
    {
        $this->ui->setValues($this->inquisition->getAttributes());
    }

    protected function buildNavBar()
    {
        parent::buildNavBar();

        $last = $this->navbar->popEntry();

        if ($this->id != '') {
            $this->navbar->createEntry(
                $this->inquisition->title,
                sprintf(
                    'Inquisition/Details?id=%s',
                    $this->inquisition->id
                )
            );
        }

        $this->navbar->addEntry($last);
    }
}
