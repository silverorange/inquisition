<?php

require_once 'SwatDB/SwatDBRecordsetWrapper.php';
require_once 'SwatDB/SwatDBClassMap.php';
require_once 'Inquisition/dataobjects/InquisitionQuestion.php';

/**
 * A recordset wrapper class for InquisitionQuestion objects
 *
 * @package   Inquisition
 * @copyright 2011 silverorange
 * @see       InquisitionQuestion
 */
class InquisitionQuestionWrapper extends SwatDBRecordsetWrapper
{
	// {{{ protected function init()

	protected function init()
	{
		parent::init();

		$this->row_wrapper_class = SwatDBClassMap::get('InquisitionQuestion');
		$this->index_field = 'id';
	}

	// }}}
}

?>
