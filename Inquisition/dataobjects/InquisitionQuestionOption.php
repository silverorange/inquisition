<?php

require_once 'SwatDB/SwatDBDataObject.php';
require_once 'SwatDB/SwatDBClassMap.php';
require_once 'Inquisition/dataobjects/InquisitionQuestion.php';
require_once 'Inquisition/dataobjects/InquisitionResponseValueWrapper.php';
require_once
	'Inquisition/dataobjects/InquisitionQuestionOptionImageWrapper.php';

/**
 * A inquisition question option
 *
 * @package   Inquisition
 * @copyright 2011-2014 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class InquisitionQuestionOption extends SwatDBDataObject
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
	 * @var integer
	 */
	public $displayorder;

	/**
	 * @var boolean
	 */
	public $include_text;

	// }}}
	// {{{ protected function init()

	protected function init()
	{
		$this->table = 'InquisitionQuestionOption';
		$this->id_field = 'integer:id';

		$this->registerInternalProperty('question',
			SwatDBClassMap::get('InquisitionQuestion'));
	}

	// }}}

	// loader methods
	// {{{ protected function loadValues()

	protected function loadValues()
	{
		$sql = sprintf('select * from InquisitionResponseValue
			where question_option = %s order by id',
			$this->db->quote($this->id, 'integer'));

		$wrapper = SwatDBClassMap::get('InquisitionResponseValueWrapper');

		return SwatDB::query($this->db, $sql, $wrapper);
	}

	// }}}
	// {{{ protected function loadImages()

	protected function loadImages()
	{
		$sql = sprintf('select * from Image
			inner join InquisitionQuestionOptionImageBinding
				on InquisitionQuestionOptionImageBinding.image = Image.id
			where InquisitionQuestionOptionImageBinding.question_option = %s
			order by InquisitionQuestionOptionImageBinding.displayorder,
				InquisitionQuestionOptionImageBinding.image',
			$this->db->quote($this->id, 'integer'));

		$wrapper = SwatDBClassMap::get('InquisitionQuestionOptionImageWrapper');

		return SwatDB::query($this->db, $sql, $wrapper);
	}

	// }}}
	// {{{ protected function loadPosition()

	protected function loadPosition()
	{
		$sql = sprintf(
			'select position from (
				select id, rank() over (
					partition by question order by displayorder, id
				) as position from InquisitionQuestionOption
			) as temp where id = %s',
			$this->id
		);

		return SwatDB::queryOne($this->db, $sql);
	}

	// }}}
}

?>
