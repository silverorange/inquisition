<?php

require_once 'Inquisition/views/InquisitionQuestionView.php';
require_once 'Swat/SwatFlydown.php';

/**
 * Flydown question view
 *
 * @package   Inquisition
 * @copyright 2011 silverorange
 */
class InquisitionFlydownQuestionView extends InquisitionQuestionView
{
	// {{{ private properties

	private $flydown;

	// }}}
	// {{{ public function getWidget()

	public function getWidget(InquisitionResponseValue $value = null)
	{
		$flydown = new SwatFlydown('question'.$this->question->id);
		$flydown->required = $this->question->required;

		foreach ($this->question->options as $option)
			$flydown->addOption($option->id, $option->title);

		if ($value !== null)
			$flydown->value = intval(
				$value->getInternalValue('question_option'));

		$this->flydown = $flydown;

		return $flydown;
	}

	// }}}
	// {{{ public function getResponseValue()

	public function getResponseValue()
	{
		$value = parent::getResponseValue();
		$value->question_option = $this->flydown->value;
		return $value;
	}

	// }}}
}

?>
