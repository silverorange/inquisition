<?php

/**
 * A recordset wrapper class for InquisitionQuestionOption objects.
 *
 * @copyright 2011-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class InquisitionQuestionOptionWrapper extends SwatDBRecordsetWrapper
{
    protected function init()
    {
        parent::init();

        $this->row_wrapper_class =
            SwatDBClassMap::get(InquisitionQuestionOption::class);

        $this->index_field = 'id';
    }
}
