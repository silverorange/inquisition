<?php

require_once 'SwatDB/SwatDBDataObject.php';
require_once 'SwatDB/SwatDBClassMap.php';
require_once 'Inquisition/dataobjects/InquisitionInquisition.php';
require_once 'Inquisition/dataobjects/InquisitionResponseValueWrapper.php';
require_once 'Inquisition/dataobjects/InquisitionQuestionHintWrapper.php';
require_once 'Inquisition/dataobjects/InquisitionResponseUsedHintBindingWrapper.php';

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
	// {{{ public function getUsedHintBindingsByQuestion()

	public function getUsedHintBindingsByQuestion(InquisitionQuestion $question)
	{
		$class_name =
			SwatDBClassMap::get('InquisitionResponseUsedHintBindingWrapper');

		$wrapper = new $class_name();

		foreach ($this->used_hint_bindings as $binding) {
			$question_id =
				$binding->question_hint->getInternalValue('question');

			if ($question_id == $question->id) {
				$wrapper->add($binding);
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
		$sql = sprintf('select InquisitionResponseValue.*
				from InquisitionResponseValue
				inner join InquisitionResponse on
					InquisitionResponseValue.response = InquisitionResponse.id
				inner join InquisitionInquisitionQuestionBinding on
					InquisitionInquisitionQuestionBinding.question =
						InquisitionResponseValue.question
					and InquisitionInquisitionQuestionBinding.inquisition =
						InquisitionResponse.inquisition
			where InquisitionResponseValue.response = %s
			order by InquisitionInquisitionQuestionBinding.displayorder',
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
	// {{{ protected function loadUsedHintBindings()

	protected function loadUsedHintBindings()
	{
		$sql = sprintf(
			'select * from InquisitionResponseUsedHintBinding
			where InquisitionResponseUsedHintBinding.response = %s
			order by InquisitionResponseUsedHintBinding.createdate',
			$this->db->quote($this->id, 'integer'));

		$bindings = SwatDB::query($this->db, $sql,
			SwatDBClassMap::get('InquisitionResponseUsedHintBindingWrapper'));


		$bindings->loadAllSubDataObjects(
			'question_hint',
			$this->db,
			'select * from InquisitionQuestionHint where id in (%s)',
			SwatDBClassMap::get('InquisitionQuestionHintWrapper')
		);

		return $bindings;
	}

	// }}}
}

?>
