<?php

require_once 'SwatDB/SwatDBDataObject.php';
require_once 'SwatDB/SwatDBClassMap.php';
require_once 'Inquisition/dataobjects/InquisitionInquisition.php';
require_once 'Inquisition/dataobjects/InquisitionQuestion.php';

/**
 * A binding between an inquisition and an inquisition question
 *
 * @package   Inquisition
 * @copyright 2013 silverorange
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
	// {{{ protected function init()

	protected function init()
	{
		$this->table = 'InquisitionInquisitionQuestionBinding';
		$this->id_field = 'integer:id';

		$this->registerInternalProperty(
			'inquisition',
			SwatDBClassMap::get('InquisitionInquisition')
		);

		$this->registerInternalProperty(
			'question',
			SwatDBClassMap::get('InquisitionQuestion')
		);
	}

	// }}}
	// {{{ public function getView()

	public function getView()
	{
		return $this->question->getView($this);
	}

	// }}}
}

?>
