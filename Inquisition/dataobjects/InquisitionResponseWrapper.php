<?php

require_once 'SwatDB/SwatDBRecordsetWrapper.php';
require_once 'SwatDB/SwatDBClassMap.php';
require_once 'Inquisition/dataobjects/InquisitionResponse.php';

/**
 * A recordset wrapper class for InquisitionResponse objects
 *
 * @package   Inquisition
 * @copyright 2011 silverorange
 * @see       InquisitionResponse
 */
class InquisitionResponseWrapper extends SwatDBRecordsetWrapper
{
	// {{{ protected function init()

	protected function init()
	{
		parent::init();

		$this->row_wrapper_class = SwatDBClassMap::get('InquisitionResponse');
		$this->index_field = 'id';
	}

	// }}}
}

?>
