<?php

require_once 'SwatDB/SwatDBRecordsetWrapper.php';
require_once 'SwatDB/SwatDBClassMap.php';
require_once 'Inquisition/dataobjects/InquisitionResponseUsedHintBinding.php';

/**
 * A recordset wrapper class for InquisitionResponseUsedHintBinding objects
 *
 * @package   Inquisition
 * @copyright 2013-2015 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @see       InquisitionResponseUsedHintBinding
 */
class InquisitionResponseUsedHintBindingWrapper extends SwatDBRecordsetWrapper
{
	// {{{ protected function init()

	protected function init()
	{
		parent::init();

		$this->index_field = 'question_hint';
		$this->row_wrapper_class =
			SwatDBClassMap::get('InquisitionResponseUsedHintBinding');
	}

	// }}}
}

?>
