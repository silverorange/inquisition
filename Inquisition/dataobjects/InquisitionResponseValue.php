<?php

/**
 * A inquisition reponse value.
 *
 * @copyright 2011-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class InquisitionResponseValue extends SwatDBDataObject
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $numeric_value;

    /**
     * @var string
     */
    public $text_value;

    protected function init()
    {
        $this->table = 'InquisitionResponseValue';
        $this->id_field = 'integer:id';

        $this->registerInternalProperty(
            'response',
            SwatDBClassMap::get(InquisitionResponse::class)
        );

        $this->registerInternalProperty(
            'question_option',
            SwatDBClassMap::get(InquisitionQuestionOption::class)
        );

        $this->registerInternalProperty(
            'question_binding',
            SwatDBClassMap::get(InquisitionInquisitionQuestionBinding::class)
        );
    }
}
