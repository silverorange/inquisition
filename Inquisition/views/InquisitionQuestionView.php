<?php

require_once 'Inquisition/dataobjects/InquisitionQuestion.php';
require_once 'Inquisition/dataobjects/InquisitionResponseValue.php';

/*
 * @package   Inquisition
 * @copyright 2011 silverorange
 */
abstract class InquisitionQuestionView
{
	// {{{ protected properties

	protected $question;
	protected $response_value;

	// }}}
	// {{{ public function __construct()

	public function __construct(InquisitionQuestion $question)
	{
		$this->question = $question;
	}

	// }}}
	// {{{ abstract public function getWidget()

	abstract public function getWidget();

	// }}}
	// {{{ public function setResponseValue()

	public function setResponseValue(InquisitionResponseValue $value)
	{
		$this->response_value = $value;
	}

	// }}}
}

?>
