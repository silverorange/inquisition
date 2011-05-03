<?php

require_once 'Inquisition/views/InquisitionQuestionView.php';
require_once 'Swat/SwatTextarea.php';

/**
 * Text question view
 *
 * @package   Inquisition
 * @copyright 2011 silverorange
 */
class InquisitionTextQuestionView extends InquisitionQuestionView
{
	// {{{ private properties

	private $textarea;

	// }}}
	// {{{ public function getWidget()

	public function getWidget(InquisitionResponseValue $value = null)
	{
		$textarea = new SwatTextarea('question'.$this->question->id);
		$textarea->required = $this->question->required;

		if ($value !== null)
			$textarea->value = intval(
				$value->getInternalValue('question_option'));

		$this->textarea = $textarea;

		return $textarea;
	}

	// }}}
	// {{{ public function getResponseValue();

	public function getResponseValue()
	{
		$value = parent::getResponseValue();
		$value->text_value = $this->textarea->value;
		return $value;
	}

	// }}}
}

?>
