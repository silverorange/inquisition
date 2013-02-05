<?php

require_once 'SwatDB/SwatDBDataObject.php';
require_once 'SwatDB/SwatDBClassMap.php';
require_once 'Inquisition/dataobjects/InquisitionResponse.php';
require_once 'Inquisition/dataobjects/InquisitionQuestionHint.php';
require_once 'Inquisition/dataobjects/InquisitionInquisitionQuestionBinding.php';

/**
 * An binding for responses to used hints
 *
 * @package   Inquisition
 * @copyright 2013 silverorange
 */
class InquisitionResponseUsedHintBinding extends SwatDBDataObject
{
	// {{{ public properties

	/**
	 * @var SwatDate
	 */
	public $createdate;

	// }}}
	// {{{ protected function init()

	protected function init()
	{
		$this->table = 'InquisitionResponseUsedHintBinding';

		$this->registerDateProperty('createdate');

		$this->registerInternalProperty('response',
			SwatDBClassMap::get('InquisitionResponse'));

		$this->registerInternalProperty('question_hint',
			SwatDBClassMap::get('InquisitionQuestionHint'));

		$this->registerInternalProperty('question_binding',
			SwatDBClassMap::get('InquisitionInquisitionQuestionBinding'));
	}

	// }}}
}

?>
