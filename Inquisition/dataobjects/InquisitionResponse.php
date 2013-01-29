<?php

require_once 'SwatDB/SwatDBDataObject.php';
require_once 'SwatDB/SwatDBClassMap.php';
require_once 'Inquisition/dataobjects/InquisitionInquisition.php';
require_once 'Inquisition/dataobjects/InquisitionResponseValueWrapper.php';
require_once 'Inquisition/dataobjects/InquisitionQuestionHintWrapper.php';

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
	 * @var SwatDate
	 */
	public $createdate;

	/**
	 * @var SwatDate
	 */
	public $complete_date;

	// }}}
	// {{{ public function getUsedHintsByQuestion()

	public function getUsedHintsByQuestion(InquisitionQuestion $question)
	{
		$class_name = SwatDBClassMap::get('InquisitionQuestionHintWrapper');
		$wrapper = new $class_name();

		foreach ($this->used_hints as $hint) {
			if ($hint->getInternalValue('question') == $question->id) {
				$wrapper->add($hint);
			}
		}

		return $wrapper;
	}

	// }}}
	// {{{ protected function init()

	protected function init()
	{
		$this->table = 'InquisitionResponse';
		$this->id_field = 'integer:id';

		$this->registerDateProperty('createdate');
		$this->registerDateProperty('complete_date');

		$this->registerInternalProperty('inquisition',
			SwatDBClassMap::get('InquisitionInquisition'));
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
	// {{{ protected function saveValues()

	protected function saveValues()
	{
		foreach ($this->values as $value)
			$value->response = $this;

		$this->values->setDatabase($this->db);
		$this->values->save();
	}

	// }}}
	// {{{ protected function loadUsedHints()

	protected function loadUsedHints()
	{
		$sql = sprintf('select * from InquisitionQuestionHint
			inner join InquisitionResponseUsedHintBinding on
				InquisitionResponseUsedHintBinding.question_hint =
				InquisitionQuestionHint.id
			where InquisitionResponseUsedHintBinding.response = %s',
			$this->db->quote($this->id, 'integer'));

		$hints = SwatDB::query($this->db, $sql,
			SwatDBClassMap::get('InquisitionQuestionHintWrapper'));

		return $hints;
	}

	// }}}
}

?>
