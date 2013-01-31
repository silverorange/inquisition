<?php

require_once 'Site/dataobjects/SiteImageWrapper.php';
require_once 'Inquisition/dataobjects/InquisitionQuestionOptionImage.php';

/**
 * A recordset wrapper class for InquisitionQuestionOptionImage objects
 *
 * @package   Store
 * @copyright 2013 silverorange
 * @see       InquisitionQuestionOptionImage
 */
class InquisitionQuestionOptionImageWrapper extends SiteImageWrapper
{
	// {{{ protected function init()

	protected function init()
	{
		parent::init();

		$this->row_wrapper_class =
			SwatDBClassMap::get('InquisitionQuestionOptionImage');
	}

	// }}}
}

?>
