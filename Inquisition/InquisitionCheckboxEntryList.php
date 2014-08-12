<?php

require_once 'Swat/SwatFormField.php';
require_once 'Swat/SwatEntry.php';
require_once 'Swat/SwatYUI.php';
require_once 'Swat/SwatCheckboxList.php';

/**
 * A checkbox list with text entries
 *
 * @package   Inquisition
 * @copyright 2014 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class InquisitionCheckboxEntryList extends SwatCheckboxList
{
	// {{{ private properties

	private $entry_option_values = array();

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new checkboxlist
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);

		$this->addJavaScript(
			'packages/inquisition/javascript/inquisition-checkbox-entry-list.js'
		);

		$yui = new SwatYUI(array('dom', 'event'));
		$this->html_head_entry_set->addEntrySet($yui->getHtmlHeadEntrySet());

		$this->classes[] = 'inquisition-checkbox-entry-list';
	}

	// }}}
	// {{{ public function process()

	/**
	 * Processes this checkbox list
	 */
	public function process()
	{
		parent::process();

		foreach ($this->values as $value) {
			if ($this->hasEntry($value) &&
				$this->getEntryValue($value) == '') {
				$message = Inquisition::_(
					'The selected option requires a value to be entered.'
				);

				$this->addMessage(new SwatMessage($message, 'error'));
			}
		}
	}

	// }}}
	// {{{ public function getEntryValue()

	public function getEntryValue($option_value)
	{
		$value = null;

		if ($this->hasEntry($option_value)) {
			$value = $this->getCompositeWidget('entry_'.$option_value)->value;
		}

		return $value;
	}

	// }}}
	// {{{ public function setEntryValue()

	public function setEntryValue($option_value, $text)
	{
		if ($this->hasEntry($option_value)) {
			$this->getCompositeWidget('entry_'.$option_value)->value = $text;
		}
	}

	// }}}
	// {{{ public function setEntryOption()

	public function setEntryOption($value)
	{
		$this->entry_option_values[] = $value;
	}

	// }}}
	// {{{ public function hasEntry()

	public function hasEntry($value)
	{
		return in_array($value, $this->entry_option_values);
	}

	// }}}
	// {{{ public function display()

	public function display()
	{
		parent::display();
		Swat::displayInlineJavaScript($this->getInlineJavaScript());
	}

	// }}}
	// {{{ protected function displayOptionLabel()

	protected function displayOptionLabel(SwatOption $option, $index)
	{
		parent::displayOptionLabel($option, $index);

		if ($this->hasEntry($option->value)) {
			echo '<span class="inquisition-checkbox-entry-entry">';
			$this->getCompositeWidget('entry_'.$option->value)->display();
			echo '</span>';
		}
	}

	// }}}
	// {{{ protected function createCompositeWidgets()

	protected function createCompositeWidgets()
	{
		parent::createCompositeWidgets();

		$options = $this->getOptions();

		foreach ($this->entry_option_values as $value) {
			// get index of checkbox
			$index = 0;
			foreach ($options as $option) {
				if ($option->value === $value) {
					break;
				}
				$index++;
			}

			$entry = new SwatEntry($this->id.'_entry_'.$index);
			$entry->maxlength = 255;
			$this->addCompositeWidget($entry, 'entry_'.$value);
		}
	}

	// }}}
	// {{{ protected function getInlineJavaScript()

	protected function getInlineJavaScript()
	{
		return sprintf(
			"var %s_obj = new InquisitionCheckboxEntryList(%s);",
			$this->id,
			SwatString::quoteJavaScriptString($this->id)
		);
	}

	// }}}
}

?>
