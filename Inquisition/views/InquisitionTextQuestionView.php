<?php

require_once 'Inquisition/views/InquisitionQuestionView.php';
require_once 'Swat/SwatTextarea.php';

/**
 * Text question view
 *
 * @package   Inquisition
 * @copyright 2011-2015 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class InquisitionTextQuestionView extends InquisitionQuestionView
{
	// {{{ private properties

	private $textarea;

	// }}}
	// {{{ public function getWidget()

	public function getWidget(InquisitionResponseValue $value = null)
	{
		$binding = $this->question_binding;
		$question = $this->question_binding->question;

		if ($this->textarea === null) {
			$id = sprintf('question%s_%s', $binding->id, $question->id);

			$this->textarea = new SwatTextarea($id);
			$this->textarea->required = $question->required;
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
