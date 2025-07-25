<?php

/**
 * A inquisition response
 *
 * @package   Inquisition
 * @copyright 2011-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class InquisitionResponse extends SwatDBDataObject
{


	/**
	 * @var integer
	 */
	public $id;

	/**
	 * @var SwatDate
	 */
	public $createdate;

	/**
	 * @var float
	 */
	public $grade;

	/**
	 * @var SwatDate
	 */
	public $complete_date;




	public function getUsedHintBindingsByQuestionBinding(
		InquisitionInquisitionQuestionBinding $question_binding
	) {
		$class_name = SwatDBClassMap::get(
			'InquisitionResponseUsedHintBindingWrapper'
		);

		$wrapper = new $class_name();

		foreach ($this->used_hint_bindings as $hint_binding) {
			$question_binding_id = $hint_binding->getInternalValue(
				'question_binding'
			);

			if ($question_binding_id === $question_binding->id) {
				$wrapper->add($hint_binding);
			}
		}

		return $wrapper;
	}




	protected function init()
	{
		$this->table = 'InquisitionResponse';
		$this->id_field = 'integer:id';

		$this->registerDateProperty('createdate');
		$this->registerDateProperty('complete_date');

		$this->registerInternalProperty(
			'inquisition',
			SwatDBClassMap::get('InquisitionInquisition')
		);
	}




	protected function getSerializableSubDataObjects()
	{
		return array_merge(
			parent::getSerializableSubDataObjects(),
			array(
				'values',
				'visible_question_values',
			)
		);
	}



	// loader methods


	protected function loadValues()
	{
		$sql = sprintf(
			'select InquisitionResponseValue.*
			from InquisitionResponseValue
			inner join InquisitionResponse on
				InquisitionResponseValue.response = InquisitionResponse.id
			inner join InquisitionInquisitionQuestionBinding on
				InquisitionInquisitionQuestionBinding.id =
					InquisitionResponseValue.question_binding
				and InquisitionInquisitionQuestionBinding.inquisition =
					InquisitionResponse.inquisition
			where InquisitionResponseValue.response = %s
			order by InquisitionInquisitionQuestionBinding.displayorder',
			$this->db->quote($this->id, 'integer')
		);

		$wrapper = SwatDBClassMap::get('InquisitionResponseValueWrapper');

		return SwatDB::query($this->db, $sql, $wrapper);
	}




	protected function loadVisibleQuestionValues()
	{
		$sql = sprintf(
			'select InquisitionResponseValue.*
			from InquisitionResponseValue
			inner join InquisitionResponse on
				InquisitionResponseValue.response = InquisitionResponse.id
			inner join InquisitionInquisitionQuestionBinding on
				InquisitionInquisitionQuestionBinding.id =
					InquisitionResponseValue.question_binding
				and InquisitionInquisitionQuestionBinding.inquisition =
					InquisitionResponse.inquisition
			inner join VisibleInquisitionQuestionView on
				InquisitionInquisitionQuestionBinding.question =
					VisibleInquisitionQuestionView.question
			where InquisitionResponseValue.response = %s
			order by InquisitionInquisitionQuestionBinding.displayorder',
			$this->db->quote($this->id, 'integer')
		);

		$wrapper = SwatDBClassMap::get('InquisitionResponseValueWrapper');

		return SwatDB::query($this->db, $sql, $wrapper);
	}




	protected function loadUsedHintBindings()
	{
		$sql = sprintf(
			'select * from InquisitionResponseUsedHintBinding
			where InquisitionResponseUsedHintBinding.response = %s
			order by InquisitionResponseUsedHintBinding.createdate',
			$this->db->quote($this->id, 'integer')
		);

		$bindings = SwatDB::query(
			$this->db,
			$sql,
			SwatDBClassMap::get('InquisitionResponseUsedHintBindingWrapper')
		);

		$bindings->loadAllSubDataObjects(
			'question_hint',
			$this->db,
			'select * from InquisitionQuestionHint where id in (%s)',
			SwatDBClassMap::get('InquisitionQuestionHintWrapper')
		);

		return $bindings;
	}



	// saver methods


	protected function saveValues()
	{
		foreach ($this->values as $value) {
			$value->response = $this;
		}

		$this->values->setDatabase($this->db);
		$this->values->save();
	}


}

?>
