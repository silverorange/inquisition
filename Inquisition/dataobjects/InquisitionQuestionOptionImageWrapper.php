<?php

require_once 'Site/dataobjects/SiteImageWrapper.php';
require_once 'Inquisition/dataobjects/InquisitionQuestionOptionImage.php';

/**
 * A recordset wrapper class for InquisitionQuestionOptionImage objects
 *
 * @package   Inquisition
 * @copyright 2013-2015 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
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
