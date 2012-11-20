<?php

require_once 'Site/dataobjects/SiteImageWrapper.php';
require_once 'Inquisition/dataobjects/InquisitionQuestionImage.php';

/**
 * A recordset wrapper class for InquisitionaQuestionImage objects
 *
 * @package   Store
 * @copyright 2012 silverorange
 * @see       InquisitionQuestionImage
 */
class InquisitionQuestionImageWrapper extends SiteImageWrapper
{
	// {{{ protected function init()

	protected function init()
	{
		parent::init();

		$this->row_wrapper_class = SwatDBClassMap::get('InquisitionQuestionImage');
	}

	// }}}
}

?>
