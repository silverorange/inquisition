<?php

require_once 'SwatDB/SwatDBRecordsetWrapper.php';
require_once 'SwatDB/SwatDBClassMap.php';
require_once 'Inquisition/dataobjects/InquisitionResponseValue.php';

/**
 * A recordset wrapper class for InquisitionResponseValue objects
 *
 * @package   Inquisition
 * @copyright 2011 silverorange
 * @see       InquisitionResponseValue
 */
class InquisitionResponseValueWrapper extends SwatDBRecordsetWrapper
{
	// {{{ protected function init()

	protected function init()
	{
		parent::init();

		$this->row_wrapper_class =
			SwatDBClassMap::get('InquisitionResponseValue');

		$this->index_field = 'id';
	}

	// }}}
}

?>
