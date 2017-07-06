<?php

/**
 * An inquisition group
 *
 * @package   Inquisition
 * @copyright 2011-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class InquisitionQuestionGroup extends SwatDBDataObject
{
	// {{{ public properties

	/**
	 * @var integer
	 */
	public $id;

	/**
	 * @var string
	 */
	public $title;

	/**
	 * @var string
	 */
	public $bodytext;

	// }}}
	// {{{ protected function init()

	protected function init()
	{
		$this->table = 'InquisitionQuestionGroup';
		$this->id_field = 'integer:id';
	}

	// }}}
}

?>
