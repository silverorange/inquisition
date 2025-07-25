<?php

/**
 * Upload page for inquisition images.
 *
 * @copyright 2012-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class InquisitionInquisitionImageUpload extends AdminDBEdit
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
        $inquisition_id = SiteApplication::initVar('inquisition');

        if ($inquisition_id !== null) {
            $this->inquisition = $this->loadInquisition($inquisition_id);
        }
    }

    protected function loadInquisition($inquisition_id)
    {
        $class = SwatDBClassMap::get('InquisitionInquisition');
        $inquisition = new $class();
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
        return __DIR__ . '/image-upload.xml';
    }

    // process phase

    protected function saveDBData(): void
    {
        $original = $this->ui->getWidget('original_image');

        $image = $this->getImageObject();
        $image->process($original->getTempFileName());

        $this->updateBindings($image);

        $this->app->messages->add(
            new SwatMessage(
                sprintf(
                    Inquisition::_('Image has been saved.'),
                    $image->title
                )
            )
        );
    }

    protected function getImageObject()
    {
        $class_name = $this->getImageClass();

        $image = new $class_name();
        $image->setDatabase($this->app->db);
        $image->setFileBase('../images');

        return $image;
    }

    abstract protected function getImageClass();

    abstract protected function updateBindings(SiteImage $image);

    // build phase

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
}
