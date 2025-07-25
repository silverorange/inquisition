<?php

/**
 * A recordset wrapper class for InquisitionResponse objects
 *
 * @package   Inquisition
 * @copyright 2011-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @see       InquisitionResponse
 */
class InquisitionResponseWrapper extends SwatDBRecordsetWrapper
{


	protected function init()
	{
		parent::init();

		$this->row_wrapper_class = SwatDBClassMap::get('InquisitionResponse');
		$this->index_field = 'id';
	}


}

?>
