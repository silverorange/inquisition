<?php

require_once 'Inquisition/views/InquisitionQuestionView.php';
require_once 'Swat/SwatRadioList.php';

/**
 * Radio list question view
 *
 * @package   Inquisition
 * @copyright 2011 silverorange
 */
class InquisitionRadioListQuestionView extends InquisitionQuestionView
{
	// {{{ private properties

	private $radio_list;

	// }}}
	// {{{ public function getWidget()

	public function getWidget(InquisitionResponseValue $value = null)
	{
		$list = new SwatRadioList('question'.$this->question->id);
		$list->required = true;

		foreach ($this->question->options as $option)
			$list->addOption($option->id, $option->title);

		if ($value !== null)
			$list->value = intval(
				$value->getInternalValue('question_option'));

		$this->radio_list = $list;

		return $list;
	}

	// }}}
	// {{{ public function getResponseValue()

	public function getResponseValue()
	{
		$class_name = SwatDBClassMap::get('InquisitionResponseValue');
		$value = new $class_name();

		$value->question_option = $this->radio_list->value;

		return $value;
	}

	// }}}
}

?>
