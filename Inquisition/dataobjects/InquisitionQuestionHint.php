<?php

require_once 'SwatDB/SwatDBDataObject.php';
require_once 'SwatDB/SwatDBClassMap.php';
require_once 'Inquisition/dataobjects/InquisitionQuestion.php';

/**
 * An inquisition question hint
 *
 * @package   Inquisition
 * @copyright 2013-2014 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class InquisitionQuestionHint extends SwatDBDataObject
{
	// {{{ public properties

	/**
	 * @var integer
	 */
	public $id;

	/**
	 * @var text
	 */
	public $bodytext;

	/**
	 * @var integer
	 */
	public $displayorder;

	// }}}
	// {{{ protected function init()

	protected function init()
	{
		$this->table = 'InquisitionQuestionHint';
		$this->id_field = 'integer:id';

		$this->registerInternalProperty(
			'question',
			SwatDBClassMap::get('InquisitionQuestion')
		);
	}

	// }}}
}

?>
