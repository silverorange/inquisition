<?php

require_once 'SwatDB/SwatDBDataObject.php';
require_once 'SwatDB/SwatDBClassMap.php';
require_once 'Inquisition/dataobjects/InquisitionInquisition.php';
require_once 'Inquisition/dataobjects/InquisitionQuestion.php';

/**
 * A binding between an inquisition and an inquisition question
 *
 * @package   Inquisition
 * @copyright 2013-2014 silverorange
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
