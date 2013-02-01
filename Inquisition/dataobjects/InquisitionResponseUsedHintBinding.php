<?php

require_once 'SwatDB/SwatDBDataObject.php';

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
	}

	// }}}
}

?>
