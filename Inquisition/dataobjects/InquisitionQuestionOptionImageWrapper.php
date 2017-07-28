<?php

/**
 * A recordset wrapper class for InquisitionQuestionOptionImage objects
 *
 * @package   Inquisition
 * @copyright 2013-2016 silverorange
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
