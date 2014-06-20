<?php

require_once 'MDB2.php';
require_once 'Inquisition/dataobjects/InquisitionInquisitionQuestionBinding.php';
require_once 'Inquisition/dataobjects/InquisitionResponseValue.php';

/**
 * Base class for question views
 *
 * @package   Inquisition
 * @copyright 2011-2014 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class InquisitionQuestionView
{
	// {{{ protected properties

	/**
	 * @var InquisitionInquisitionQuestionBinding
	 */
	protected $question_binding;

	/**
	 * @var MDB2_Driver_Common
	 */
	protected $db;

	// }}}
	// {{{ public function __construct()

	public function __construct(
		InquisitionInquisitionQuestionBinding $question_binding,
		MDB2_Driver_Common $db = null)
	{
		$this->question_binding = $question_binding;
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
		$value->question_binding = $this->question_binding->id;
		return $value;
	}

	// }}}
}

?>
