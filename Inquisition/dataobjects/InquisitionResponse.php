<?php

require_once 'SwatDB/SwatDBDataObject.php';
require_once 'SwatDB/SwatDBClassMap.php';
require_once 'Inquisition/dataobjects/Inquisition.php';
require_once 'Inquisition/dataobjects/InquisitionResponseValueWrapper.php';

/**
 * A inquisition response
 *
 * @package   Inquisition
 * @copyright 2011 silverorange
 */
class InquisitionResponse extends SwatDBDataObject
{
	// {{{ public properties

	/**
	 * @var integer
	 */
	public $id;

	/**
	 * @var Date
	 */
	public $createdate;

	// }}}
	// {{{ public function getGrade()

	public function getGrade()
	{
		$correct = 0;

		foreach ($this->values as $value) {
			$question           = $value->question_option->question;
			$correct_option_id  = $question->getInternalValue('correct_option');
			$response_option_id = $value->question_option->id;
			if ($response_option_id == $correct_option_id) {
				$correct++;
			}
		}

		return $correct / count($this->inquisition->questions);
	}

	// }}}
	// {{{ protected function init()

	protected function init()
	{
		$this->table = 'InquisitionResponse';
		$this->id_field = 'integer:id';

		$this->registerDateProperty('createdate');

		$this->registerInternalProperty('inquisition',
			SwatDBClassMap::get('Inquisition'));
	}

	// }}}
	// {{{ protected function getSerializableSubDataObjects()

	protected function getSerializableSubDataObjects()
	{
		return array_merge(parent::getSerializableSubDataObjects(), array(
			'values',
		));
	}

	// }}}

	// loader methods
	// {{{ protected function loadValues()

	protected function loadValues()
	{
		$sql = sprintf('select * from InquisitionResponseValue
			where response = %s',
			$this->db->quote($this->id, 'integer'));

		$wrapper = SwatDBClassMap::get('InquisitionResponseValueWrapper');

		return SwatDB::query($this->db, $sql, $wrapper);
	}

	// }}}

	// saver methods
	// {{{ protected function saveValues()

	protected function saveValues()
	{
		$this->checkDB();

		foreach ($this->values as $value) {
			$value->response = $this;
		}

		$this->values->setDatabase($this->db);
		$this->values->save();
	}

	// }}}
}

?>
