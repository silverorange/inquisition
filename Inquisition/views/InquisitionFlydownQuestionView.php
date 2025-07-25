<?php

/**
 * Flydown question view
 *
 * @package   Inquisition
 * @copyright 2011-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class InquisitionFlydownQuestionView extends InquisitionQuestionView
{


	private $flydown;




	public function getWidget(InquisitionResponseValue $value = null)
	{
		$binding = $this->question_binding;
		$question = $this->question_binding->question;

		if ($this->flydown === null) {
			$id = sprintf('question%s_%s', $binding->id, $question->id);

			$this->flydown = new SwatFlydown($id);
			$this->flydown->required = $question->required;

			foreach ($question->options as $option)
				$this->flydown->addOption($option->id, $option->title);
		}

		if ($value !== null) {
			$this->flydown->value = intval(
				$value->getInternalValue('question_option'));
		}

		return $this->flydown;
	}




	public function getResponseValue()
	{
		$value = parent::getResponseValue();
		$value->question_option = $this->flydown->value;
		return $value;
	}


}

?>
