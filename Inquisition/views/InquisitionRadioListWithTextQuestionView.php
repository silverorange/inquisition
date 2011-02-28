<?php

require_once 'Inquisition/views/InquisitionQuestionView.php';
require_once 'Swat/SwatContainer.php';
require_once 'Swat/SwatEntry.php';

/*
 * @package   Inquisition
 * @copyright 2011 silverorange
 */
class InquisitionRadioListWithTextQuestionView extends InquisitionRadioListQuestionView
{
	// {{{ private properties

	private $entry;

	// }}}
	// {{{ public function getWidget()

	public function getWidget(InquisitionResponseValue $value = null)
	{
		$container = new SwatContainer();
		$entry = new SwatEntry('question'.$this->question->id.'_entry');
		$entry->required = true;

		if ($value !== null)
			$entry->value = $value->text_value;

		$container->add(parent::getWidget($value));
		$container->add($entry);

		$this->entry = $entry;

		return $container;
	}

	// }}}
	// {{{ public function getResponseValue()

	public function getResponseValue() {
		$value = parent::getResponseValue();

		$value->text_value = $this->entry->value;

		return $value;
	}

	// }}}
}

?>
