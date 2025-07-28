<?php

/**
 * An image data object for inquisition questions.
 *
 * @copyright 2013-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class InquisitionQuestionOptionImage extends SiteImage
{
    protected function init()
    {
        parent::init();

        $this->image_set_shortname = 'inquisition-question-option';
    }
}
