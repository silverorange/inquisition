<?php

/**
 * An binding for responses to used hints.
 *
 * @copyright 2013-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class InquisitionResponseUsedHintBinding extends SwatDBDataObject
{
    /**
     * @var SwatDate
     */
    public $createdate;

    protected function init()
    {
        $this->table = 'InquisitionResponseUsedHintBinding';

        $this->registerDateProperty('createdate');

        $this->registerInternalProperty(
            'response',
            SwatDBClassMap::get(InquisitionResponse::class)
        );

        $this->registerInternalProperty(
            'question_hint',
            SwatDBClassMap::get(InquisitionQuestionHint::class)
        );

        $this->registerInternalProperty(
            'question_binding',
            SwatDBClassMap::get(InquisitionInquisitionQuestionBinding::class)
        );
    }
}
