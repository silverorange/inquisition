<?php

require_once 'Inquisition/views/InquisitionQuestionView.php';
require_once 'Inquisition/InquisitionRadioEntryList.php';
require_once 'Swat/SwatContainer.php';
require_once 'Swat/SwatEntry.php';

/**
 * Radio list with text question view
 *
 * @package   Inquisition
 * @copyright 2011-2012 silverorange
 */
class InquisitionRadioEntryQuestionView extends InquisitionQuestionView
{
	// {{{ private properties

	private $radio_table;

	// }}}
	// {{{ public function getWidget()

	public function getWidget(InquisitionResponseValue $value = null)
	{
		if ($this->radio_table === null) {
			$this->radio_table = new InquisitionRadioEntryList(
				'question'.$this->question->id);

			$this->radio_table->required = $this->question->required;

			foreach ($this->question->options as $option) {
				$this->radio_table->addOption($option->id, $option->title);
				if ($option->include_text)
					$this->radio_table->setEntryOption($option->id);
			}
		}

		if ($value !== null) {
			$this->radio_table->value = intval(
				$value->getInternalValue('question_option'));
		}

		return $this->radio_table;
	}

	// }}}
	// {{{ public function getResponseValue()

	public function getResponseValue()
	{
		$value = parent::getResponseValue();
		$value->question_option = $this->radio_table->value;
		$value->text_value = $this->radio_table->getEntryValue(
			$this->radio_table->value);

		return $value;
	}

	// }}}
}

?>
