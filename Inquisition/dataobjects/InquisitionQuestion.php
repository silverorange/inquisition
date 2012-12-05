<?php

require_once 'SwatDB/SwatDBDataObject.php';
require_once 'SwatDB/SwatDBClassMap.php';
require_once 'Inquisition/dataobjects/InquisitionInquisition.php';
require_once 'Inquisition/dataobjects/InquisitionQuestionOptionWrapper.php';
require_once 'Inquisition/dataobjects/InquisitionQuestionGroup.php';
require_once 'Inquisition/dataobjects/InquisitionQuestionImageWrapper.php';

/**
 * An inquisition question
 *
 * @package   Inquisition
 * @copyright 2011-2012 silverorange
 */
class InquisitionQuestion extends SwatDBDataObject
{
	// {{{ class constants

	const TYPE_RADIO_LIST = 1;
	const TYPE_FLYDOWN = 2;
	const TYPE_RADIO_ENTRY = 3;
	const TYPE_TEXT = 4;
	const TYPE_CHECKBOX_LIST = 5;

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

	/**
	 * @var boolean
	 */
	public $required;

	// }}}
	// {{{ protected function init()

	protected function init()
	{
		$this->table = 'InquisitionQuestion';
		$this->id_field = 'integer:id';

		$this->registerInternalProperty('inquisition',
			SwatDBClassMap::get('InquisitionInquisition'));

		$this->registerInternalProperty('correct_option',
			SwatDBClassMap::get('InquisitionQuestionOption'));

		$this->registerInternalProperty('question_group',
			SwatDBClassMap::get('InquisitionQuestionGroup'));
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
		case self::TYPE_RADIO_ENTRY:
			require_once 'Inquisition/views/InquisitionRadioEntryQuestionView.php';
			$view = new InquisitionRadioEntryQuestionView($this);
			break;
		case self::TYPE_TEXT:
			require_once 'Inquisition/views/InquisitionTextQuestionView.php';
			$view = new InquisitionTextQuestionView($this);
			break;
		case self::TYPE_CHECKBOX_LIST:
			require_once 'Inquisition/views/InquisitionCheckboxListQuestionView.php';
			$view = new InquisitionCheckboxListQuestionView($this);
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
	// {{{ protected function loadImages()

	protected function loadImages()
	{
		$sql = sprintf('select * from Image
			inner join InquisitionQuestionImageBinding
				on InquisitionQuestionImageBinding.image = Image.id
			where InquisitionQuestionImageBinding.question = %s
			order by InquisitionQuestionImageBinding.displayorder,
				InquisitionQuestionImageBinding.image',
			$this->db->quote($this->id, 'integer'));

		$wrapper = SwatDBClassMap::get('InquisitionQuestionImageWrapper');

		return SwatDB::query($this->db, $sql, $wrapper);
	}

	// }}}
	// {{{ protected function loadPosition()

	protected function loadPosition()
	{
		$sql = sprintf(
			'select position from (
				select id, rank() over (
					partition by inquisition order by displayorder, id
				) as position from InquisitionQuestion
			) as temp where id = %s',
			$this->id
		);

		return SwatDB::queryOne($this->db, $sql);
	}

	// }}}

	// saver methods
	// {{{ protected function saveOptions()

	protected function saveOptions()
	{
		foreach ($this->options as $option) {
			$option->question = $this;
		}

		$this->options->setDatabase($this->db);
		$this->options->save();
	}

	// }}}
}

?>
