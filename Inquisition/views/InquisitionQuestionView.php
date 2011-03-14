<?php

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

	protected $question;
	protected $db;

	// }}}
	// {{{ public function __construct()

	public function __construct(InquisitionQuestion $question, $db = null)
	{
		$this->question = $question;
		$this->db = $db;
	}

	// }}}
	// {{{ abstract public function getWidget()

	abstract public function getWidget(InquisitionResponseValue $value = null);

	// }}}
	// {{{ abstract public function getResponseValue()

	abstract public function getResponseValue();

	// }}}
}

?>
