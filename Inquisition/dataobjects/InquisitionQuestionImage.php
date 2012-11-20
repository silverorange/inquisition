<?php

require_once 'Site/dataobjects/SiteImage.php';

/**
 * An image data object for inquisition questions
 *
 * @package   Inquisition
 * @copyright 2012 silverorange
 */
class InquisitionQuestionImage extends SiteImage
{
	// {{{ protected function init()

	protected function init()
	{
		parent::init();

		$this->image_set_shortname = 'inquisition-question';
	}

	// }}}
}

?>
