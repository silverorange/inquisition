<?php

require_once 'SwatDB/SwatDBRecordsetWrapper.php';
require_once 'SwatDB/SwatDBClassMap.php';
require_once 'Inquisition/dataobjects/InquisitionQuestionHint.php';

/**
 * A recordset wrapper class for InquisitionQuestionHint objects
 *
 * @package   Inquisition
 * @copyright 2013 silverorange
 * @see       Inquisition
 */
class InquisitionQuestionHintWrapper extends SwatDBRecordsetWrapper
{
	// {{{ protected function init()

	protected function init()
	{
		parent::init();

		$this->row_wrapper_class =
			SwatDBClassMap::get('InquisitionQuestionHint');

		$this->index_field = 'id';
	}

	// }}}
}

?>
