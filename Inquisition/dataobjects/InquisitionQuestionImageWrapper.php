<?php

/**
 * A recordset wrapper class for InquisitionaQuestionImage objects.
 *
 * @copyright 2012-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 *
 * @see       InquisitionQuestionImage
 */
class InquisitionQuestionImageWrapper extends SiteImageWrapper
{
    protected function init()
    {
        parent::init();

        $this->row_wrapper_class =
            SwatDBClassMap::get(InquisitionQuestionImage::class);
    }
}
