<?php

require_once 'SwatDB/SwatDBDataObject.php';
require_once 'SwatDB/SwatDBClassMap.php';
require_once 'Inquisition/dataobjects/InquisitionInquisition.php';
require_once 'Inquisition/dataobjects/InquisitionQuestion.php';

/**
 * A binding between an inquisition and an inquisition question
 *
 * @package   Inquisition
 * @copyright 2013-2015 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class InquisitionInquisitionQuestionBinding extends SwatDBDataObject
{
	// {{{ public properties

	/**
	 * @var integer
	 */
	public $id;

	/**
	 * @var integer
	 */
	public $displayorder;

	// }}}
	// {{{ protected properties

	/*
	 * @var array
	 */
	protected $dependent_options;

	// }}}
	// {{{ public function getView()

	public function getView()
	{
		return $this->question->getView($this);
	}

	// }}}
	// {{{ public function getPosition()

	public function getPosition()
	{
		$sql = sprintf(
			'select position from (
				select id, rank() over (
					partition by inquisition order by displayorder, id
				) as position from InquisitionInquisitionQuestionBinding
				where inquisition = %s
			) as temp where id = %s',
			$this->getInternalValue('inquisition'),
			$this->id
		);

		return SwatDB::queryOne($this->db, $sql);
	}

	// }}}
	// {{{ public function getDependentOptions()

	public function getDependentOptions()
	{
		if (!is_array($this->dependent_options)) {
			$sql = sprintf(
				'select InquisitionQuestionDependency.option,
					InquisitionQuestionDependency.question_binding,
					InquisitionInquisitionQuestionBinding.question
				from InquisitionQuestionDependency
				inner join InquisitionInquisitionQuestionBinding on
					InquisitionQuestionDependency.question_binding =
					InquisitionInquisitionQuestionBinding.id
				where dependent_question_binding = %s',
				$this->db->quote($this->id, 'integer')
			);

			$rs = SwatDB::query($this->db, $sql);

			$dependent_options = array();

			foreach ($rs as $row) {
				$option = array();

				$id = $row->question_binding.'_'.$row->question;

				$option['binding'] = $row->question_binding;
				$option['question'] = $row->question;
				$option['options'] = array($row->option);

				if (array_key_exists($id, $dependent_options)) {
					$dependent_options[$id]['options'][] = $row->option;
				} else {
					$dependent_options[$id] = $option;
				}
			}

			$this->dependent_options = array_values($dependent_options);
		}

		return $this->dependent_options;
	}

	// }}}
	// {{{ protected function init()

	protected function init()
	{
		$this->table = 'InquisitionInquisitionQuestionBinding';
		$this->id_field = 'integer:id';

		$this->registerInternalProperty(
			'inquisition',
			SwatDBClassMap::get('InquisitionInquisition')
		);

		// We set autosave so that questions are saved before the binding.
		$this->registerInternalProperty(
			'question',
			SwatDBClassMap::get('InquisitionQuestion'),
			true
		);
	}

	// }}}
	// {{{ protected function getSerializableSubDataObjects()

	protected function getSerializableSubDataObjects()
	{
		return array_merge(
			parent::getSerializableSubDataObjects(),
			array('question')
		);
	}

	// }}}
}

?>
