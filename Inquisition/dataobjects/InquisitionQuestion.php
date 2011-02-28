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

	const TYPE_RADIO_LIST = 1;
	const TYPE_FLYDOWN = 2;
	const TYPE_RADIO_LIST_WITH_TEXT = 3;
	const TYPE_TEXT = 4;

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

		$this->registerInternalProperty('correct_option',
			SwatDBClassMap::get('InquisitionQuestionOption'));
	}

	// }}}
	// {{{ protected function getSerializableSubDataObjects()

	protected function getSerializableSubDataObjects()
	{
		return array_merge(
			parent::getSerializableSubDataObjects(),
			array('options'));
	}

	// }}}
	// {{{ public function getView()

	public function getView($response_value = null)
	{
		switch ($this->question_type) {
		default:
		case self::TYPE_RADIO_LIST:
			require_once 'Inquisition/views/InquisitionRadioListQuestionView.php';
			$view = new InquisitionRadioListQuestionView($this);
			break;
		case self::TYPE_FLYDOWN:
			require_once 'Inquisition/views/InquisitionFlydownQuestionView.php';
			$view = new InquisitionFlydownQuestionView($this);
			break;
		case self::TYPE_RADIO_LIST_WITH_TEXT:
			require_once 'Inquisition/views/InquisitionRadioListWithTextQuestionView.php';
			$view = new InquisitionRadioListWithTextQuestionView($this);
			break;
		case self::TYPE_TEXT:
			require_once 'Inquisition/views/InquisitionTextQuestionView.php';
			$view = new InquisitionTextQuestionView($this);
			break;
		}

		return $view;
	}

	// }}}

	// loader methods
	// {{{ protected function loadOptions()

	protected function loadOptions()
	{
		$sql = sprintf('select * from InquisitionQuestionOption
			where question = %s
			order by displayorder',
			$this->db->quote($this->id, 'integer'));

		$wrapper = SwatDBClassMap::get('InquisitionQuestionOptionWrapper');

		return SwatDB::query($this->db, $sql, $wrapper);
	}

	// }}}
}

?>
