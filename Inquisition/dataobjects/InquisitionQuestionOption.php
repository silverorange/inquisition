<?php

require_once 'SwatDB/SwatDBDataObject.php';
require_once 'SwatDB/SwatDBClassMap.php';
require_once 'Inquisition/dataobjects/InquisitionQuestion.php';
require_once 'Inquisition/dataobjects/InquisitionResponseValueWrapper.php';

/**
 * A question option
 *
 * @package   Inquisition
 * @copyright 2011 silverorange
 */
class InquisitionQuestionOption extends SwatDBDataObject
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
	 * @var integer
	 */
	public $displayorder;

	// }}}
	// {{{ protected function init()

	protected function init()
	{
		$this->table = 'InquisitionQuestionOption';
		$this->id_field = 'integer:id';

		$this->registerInternalProperty('question',
			SwatDBClassMap::get('InquisitionQuestion'));
	}

	// }}}

	// loader methods
	// {{{ protected function loadValues()

	protected function loadValues()
	{
		$sql = sprintf('select * from InquisitionResponseValue
			where question_option = %s order by id',
			$this->db->quote($this->id, 'integer'));

		$wrapper = SwatDBClassMap::get('InquisitionResponseValueWrapper');

		return SwatDB::query($this->db, $sql, $wrapper);
	}

	// }}}
}

?>
