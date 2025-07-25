<?php

/**
 * Base class for question views.
 *
 * @copyright 2011-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class InquisitionQuestionView
{
    /**
     * @var InquisitionInquisitionQuestionBinding
     */
    protected $question_binding;

    /**
     * @var MDB2_Driver_Common
     */
    protected $db;

    public function __construct(
        InquisitionInquisitionQuestionBinding $question_binding,
        ?MDB2_Driver_Common $db = null
    ) {
        $this->question_binding = $question_binding;
        $this->db = $db;
    }

    abstract public function getWidget(?InquisitionResponseValue $value = null);

    public function getResponseValue()
    {
        $class_name = SwatDBClassMap::get('InquisitionResponseValue');
        $value = new $class_name();
        $value->question_binding = $this->question_binding->id;

        return $value;
    }
}
