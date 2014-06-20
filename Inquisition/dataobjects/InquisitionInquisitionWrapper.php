<?php

require_once 'SwatDB/SwatDBRecordsetWrapper.php';
require_once 'SwatDB/SwatDBClassMap.php';
require_once 'Inquisition/dataobjects/InquisitionInquisition.php';

/**
 * A recordset wrapper class for InquisitionInquisition objects
 *
 * @package   Inquisition
 * @copyright 2011-2014 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @see       Inquisition
 */
class InquisitionInquisitionWrapper extends SwatDBRecordsetWrapper
{
	// {{{ protected function init()

	protected function init()
	{
		parent::init();

		$this->row_wrapper_class =
			SwatDBClassMap::get('InquisitionInquisition');

		$this->index_field = 'id';
	}

	// }}}
}

?>
