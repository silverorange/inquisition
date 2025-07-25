<?php

/**
 * A recordset wrapper class for InquisitionResponseUsedHintBinding objects.
 *
 * @copyright 2013-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 *
 * @see       InquisitionResponseUsedHintBinding
 */
class InquisitionResponseUsedHintBindingWrapper extends SwatDBRecordsetWrapper
{
    protected function init()
    {
        parent::init();

        $this->index_field = 'question_hint';
        $this->row_wrapper_class =
            SwatDBClassMap::get('InquisitionResponseUsedHintBinding');
    }
}
