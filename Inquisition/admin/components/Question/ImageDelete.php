<?php

/**
 * Delete confirmation page for question images.
 *
 * @copyright 2012-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class InquisitionQuestionImageDelete extends InquisitionInquisitionImageDelete
{
    /**
     * @var InquisitonQuestion
     */
    protected $question;

    // helper methods

    public function setId($id)
    {
        $class_name = SwatDBClassMap::get('InquisitionQuestion');

        $this->question = new $class_name();
        $this->question->setDatabase($this->app->db);

        if ($id == '') {
            throw new AdminNotFoundException(
                'Question id not provided.'
            );
        }

        if (!$this->question->load($id)) {
            throw new AdminNotFoundException(
                sprintf(
                    'Question with id ‘%s’ not found.',
                    $id
                )
            );
        }

        parent::setId($id);
    }

    protected function getImageWrapper()
    {
        return SwatDBClassMap::get('InquisitionQuestionImageWrapper');
    }

    // build phase

    protected function buildNavBar()
    {
        AdminDBDelete::buildNavBar();

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

        $this->navbar->createEntry(
            ngettext(
                'Delete Image',
                'Delete Images',
                count($this->images)
            )
        );
    }

    protected function getQuestionTitle()
    {
        // TODO: Update this with some version of getPosition().
        return Inquisition::_('Question');
    }
}
