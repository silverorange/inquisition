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
		if ($this->textarea === null) {
			$this->textarea = new SwatTextarea('question'.$this->question->id);
			$this->textarea->required = $this->question->required;
		}

		if ($value !== null) {
			$this->textarea->value = intval(
				$value->getInternalValue('question_option'));
		}

		return $this->textarea;
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
