<?php

require_once 'SwatDB/SwatDBDataObject.php';
require_once 'SwatDB/SwatDBClassMap.php';

/**
 * An inquisition group
 *
 * @package   Inquisition
 * @copyright 2011 silverorange
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
