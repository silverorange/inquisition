<?php

require_once 'SwatDB/SwatDBDataObject.php';
require_once 'SwatDB/SwatDBClassMap.php';
require_once 'Inquisition/dataobjects/InquisitionQuestionWrapper.php';
require_once 'Inquisition/dataobjects/InquisitionResponseWrapper.php';

/**
 * An inquisition
 *
 * @package   Inquisition
 * @copyright 2011 silverorange
 */
class Inquisition extends SwatDBDataObject
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
	 * @var SwatDate
	 */
	public $createdate;

	// }}}
	// {{{ protected function init()

	protected function init()
	{
		$this->table = 'Inquisition';
		$this->id_field = 'integer:id';
		$this->registerDateProperty('createdate');
	}

	// }}}

	// loader methods
	// {{{ protected function loadQuestions()

	protected function loadQuestions()
	{
		$sql = sprintf('select * from InquisitionQuestion
			where inquisition = %s
			order by displayorder, id',
			$this->db->quote($this->id, 'integer'));

		return SwatDB::query($this->db, $sql, 'InquisitionQuestionWrapper');
	}

	// }}}
	// {{{ protected function loadResponses()

	protected function loadResponses()
	{
		$sql = sprintf('select * from InquisitionResponse
			where inquisition = %s
			order by createdate, id',
			$this->db->quote($this->id, 'integer'));

		return SwatDB::query($this->db, $sql, 'InquisitionResponseWrapper');
	}

	// }}}
}

?>
