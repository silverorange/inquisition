<?php

/**
 * An inquisition group.
 *
 * @copyright 2011-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class InquisitionQuestionGroup extends SwatDBDataObject
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $bodytext;

    protected function init()
    {
        $this->table = 'InquisitionQuestionGroup';
        $this->id_field = 'integer:id';
    }
}
