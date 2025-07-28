<?php

/**
 * Checkbox list with text question view.
 *
 * @copyright 2014-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class InquisitionCheckboxEntryQuestionView extends InquisitionQuestionView
{
    private $checkbox_list;

    public function getWidget(?InquisitionResponseValue $value = null)
    {
        $binding = $this->question_binding;
        $question = $this->question_binding->question;

        if (!$this->checkbox_list instanceof InquisitionCheckboxEntryList) {
            $id = sprintf('question%s_%s', $binding->id, $question->id);

            $this->checkbox_list = new InquisitionCheckboxEntryList($id);
            $this->checkbox_list->required = $question->required;
            $this->checkbox_list->show_check_all = false;

            foreach ($question->options as $option) {
                $this->checkbox_list->addOption($option->id, $option->title);
                if ($option->include_text) {
                    $this->checkbox_list->setEntryOption($option->id);
                }
            }
        }

        if ($value instanceof InquisitionResponseValue) {
            $this->checkbox_list->value = intval(
                $value->getInternalValue('question_option')
            );
        }

        return $this->checkbox_list;
    }

    public function getResponseValue()
    {
        $base_value = parent::getResponseValue();
        $value = [];

        foreach ($this->checkbox_list->values as $list_value) {
            $response_value = clone $base_value;
            $response_value->question_option = $list_value;
            $response_value->text_value = $this->checkbox_list->getEntryValue(
                $list_value
            );
            $value[] = $response_value;
        }

        return $value;
    }
}
