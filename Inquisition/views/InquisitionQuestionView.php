<?php

require_once 'MDB2.php';
require_once 'Inquisition/dataobjects/InquisitionQuestion.php';
require_once 'Inquisition/dataobjects/InquisitionResponseValue.php';

/**
 * Base class for question views
 *
 * @package   Inquisition
 * @copyright 2011 silverorange
 */
abstract class InquisitionQuestionView
{
	// {{{ protected properties

	/**
	 * @var InquisitionQuestion
	 */
	protected $question;

	/**
	 * @var MDB2_Driver_Common
	 */
	protected $db;

	// }}}
	// {{{ public function __construct()

	public function __construct(InquisitionQuestion $question,
		MDB2_Driver_Common $db = null)
	{
		$this->question = $question;
		$this->db = $db;
	}

	// }}}
	// {{{ abstract public function getWidget()

	abstract public function getWidget(InquisitionResponseValue $value = null);

	// }}}
	// {{{ public function getResponseValue()

	public function getResponseValue()
	{
		$class_name = SwatDBClassMap::get('InquisitionResponseValue');
		$value = new $class_name();
		$value->question = $this->question->id;
		return $value;
	}

	// }}}
}

?>
