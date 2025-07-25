<?php

/**
 * A recordset wrapper class for InquisitionQuestionHint objects.
 *
 * @copyright 2013-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 *
 * @see       Inquisition
 */
class InquisitionQuestionHintWrapper extends SwatDBRecordsetWrapper
{
    protected function init()
    {
        parent::init();

        $this->row_wrapper_class =
            SwatDBClassMap::get(InquisitionQuestionHint::class);

        $this->index_field = 'id';
    }
}
