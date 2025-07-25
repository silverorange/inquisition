<?php

/**
 * A inquisition question option.
 *
 * @copyright 2011-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class InquisitionQuestionOption extends SwatDBDataObject
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
     * @var int
     */
    public $displayorder;

    /**
     * @var bool
     */
    public $include_text;

    protected function init()
    {
        $this->table = 'InquisitionQuestionOption';
        $this->id_field = 'integer:id';

        $this->registerInternalProperty(
            'question',
            SwatDBClassMap::get('InquisitionQuestion')
        );
    }

    // loader methods

    protected function loadValues()
    {
        $sql = sprintf(
            'select * from InquisitionResponseValue
			where question_option = %s order by id',
            $this->db->quote($this->id, 'integer')
        );

        $wrapper = SwatDBClassMap::get('InquisitionResponseValueWrapper');

        return SwatDB::query($this->db, $sql, $wrapper);
    }

    protected function loadImages()
    {
        $sql = sprintf(
            'select * from Image
			inner join InquisitionQuestionOptionImageBinding
				on InquisitionQuestionOptionImageBinding.image = Image.id
			where InquisitionQuestionOptionImageBinding.question_option = %s
			order by InquisitionQuestionOptionImageBinding.displayorder,
				InquisitionQuestionOptionImageBinding.image',
            $this->db->quote($this->id, 'integer')
        );

        $wrapper = SwatDBClassMap::get('InquisitionQuestionOptionImageWrapper');

        return SwatDB::query($this->db, $sql, $wrapper);
    }

    protected function loadPosition()
    {
        $sql = sprintf(
            'select position from (
				select id, rank() over (
					partition by question order by displayorder, id
				) as position from InquisitionQuestionOption
			) as temp where id = %s',
            $this->id
        );

        return SwatDB::queryOne($this->db, $sql);
    }
}
