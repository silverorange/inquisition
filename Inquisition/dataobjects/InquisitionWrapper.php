<?php

require_once 'SwatDB/SwatDBRecordsetWrapper.php';
require_once 'SwatDB/SwatDBClassMap.php';
require_once 'Inquisition/dataobjects/Inquisition.php';

/**
 * A recordset wrapper class for Inquisition objects
 *
 * @package   Inquisition
 * @copyright 2011 silverorange
 * @see       Inquisition
 */
class InquisitionWrapper extends SwatDBRecordsetWrapper
{
	// {{{ protected function init()

	protected function init()
	{
		parent::init();

		$this->row_wrapper_class = SwatDBClassMap::get('Inquisition');
		$this->index_field = 'id';
	}

	// }}}
}

?>
