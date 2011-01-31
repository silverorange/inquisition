<?php

require_once 'SwatDB/SwatDBDataObject.php';
require_once 'SwatDB/SwatDBClassMap.php';
require_once 'Inquisition/dataobjects/Inquisition.php';
require_once 'Inquisition/dataobjects/InquisitionQuestionOptionWrapper.php';

/**
 * An inquisition question
 *
 * @package   Inquisition
 * @copyright 2011 silverorange
 */
class InquisitionQuestion extends SwatDBDataObject
{
	// {{{ class constants

	// Choose one (list of radio buttons)
	const TYPE_RADIO_LIST = 1;

	// True or False
	const TYPE_BOOLEAN = 2;

	// }}}
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
	public $question_type;

	/**
	 * @var integer
	 */
	public $displayorder;

	// }}}
	// {{{ protected function init()

	protected function init()
	{
		$this->table = 'InquisitionQuestion';
		$this->id_field = 'integer:id';
		$this->registerInternalProperty('inquisition',
			SwatDBClassMap::get('Inquisition'));
	}

	// }}}

	// loader methods
	// {{{ protected function loadOptions()

	protected function loadOptions()
	{
		$sql = sprintf('select * from InquisitionQuestionOption
			where inquisition_question = %s
			order by displayorder',
			$this->db->quote($this->id, 'integer'));

		$wrapper = SwatDBClassMap::get('InquisitionQuestionOptionWrapper');

		return SwatDB::query($this->db, $sql, $wrapper);
	}

	// }}}
}

?>
