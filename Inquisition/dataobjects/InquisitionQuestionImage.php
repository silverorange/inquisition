<?php

/**
 * An image data object for inquisition questions
 *
 * @package   Inquisition
 * @copyright 2012-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
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
