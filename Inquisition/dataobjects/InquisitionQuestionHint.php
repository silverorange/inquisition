<?php

/**
 * An inquisition question hint.
 *
 * @copyright 2013-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class InquisitionQuestionHint extends SwatDBDataObject
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var text
     */
    public $bodytext;

    /**
     * @var int
     */
    public $displayorder;

    protected function init()
    {
        $this->table = 'InquisitionQuestionHint';
        $this->id_field = 'integer:id';

        $this->registerInternalProperty(
            'question',
            SwatDBClassMap::get('InquisitionQuestion')
        );
    }
}
