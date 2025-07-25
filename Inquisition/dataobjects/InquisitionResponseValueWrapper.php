<?php

/**
 * A recordset wrapper class for InquisitionResponseValue objects.
 *
 * @copyright 2011-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 *
 * @see       InquisitionResponseValue
 */
class InquisitionResponseValueWrapper extends SwatDBRecordsetWrapper
{
    protected function init()
    {
        parent::init();

        $this->row_wrapper_class =
            SwatDBClassMap::get('InquisitionResponseValue');

        $this->index_field = 'id';
    }
}
