<?php

require_once 'Inquisition/views/InquisitionQuestionView.php';
require_once 'Swat/SwatRadioList.php';

/*
 * @package   Inquisition
 * @copyright 2011 silverorange
 */
class InquisitionMultipleChoiceQuestionView extends InquisitionQuestionView
{
	// {{{ public function getWidget()

	public function getWidget()
	{
		$control = new SwatRadioList('question'.$this->question->id);
		$control->required = true;

		foreach ($this->question->options as $option)
			$control->addOption($option->id, $option->title);

		if ($this->response_value !== null)
			$control->value = intval(
				$this->response_value->getInternalValue('question_option'));

		return $control;
	}

	// }}}
}

?>
