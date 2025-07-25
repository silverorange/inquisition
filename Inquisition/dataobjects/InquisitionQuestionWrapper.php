<?php

/**
 * A recordset wrapper class for InquisitionQuestion objects.
 *
 * @copyright 2011-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 *
 * @see       InquisitionQuestion
 */
class InquisitionQuestionWrapper extends SwatDBRecordsetWrapper
{
    protected function init()
    {
        parent::init();

        $this->row_wrapper_class = SwatDBClassMap::get(InquisitionQuestion::class);
        $this->index_field = 'id';
    }
}
