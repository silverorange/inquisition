<?php

/**
 * An inquisition question.
 *
 * @copyright 2011-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 *
 * @property ?InquisitionQuestionOption       $correct_option
 * @property ?InquisitionQuestionGroup        $question_group
 * @property InquisitionQuestionOptionWrapper $options
 * @property InquisitionQuestionHintWrapper   $hints
 * @property InquisitionQuestionImageWrapper  $images
 */
class InquisitionQuestion extends SwatDBDataObject
{
    public const TYPE_RADIO_LIST = 1;
    public const TYPE_FLYDOWN = 2;
    public const TYPE_RADIO_ENTRY = 3;
    public const TYPE_TEXT = 4;
    public const TYPE_CHECKBOX_LIST = 5;
    public const TYPE_CHECKBOX_ENTRY = 6;

    /**
     * @var int
     */
    public $id;

    /**
     * @var ?string
     */
    public $bodytext;

    /**
     * @var int
     */
    public $question_type;

    /**
     * @var int
     */
    public $displayorder;

    /**
     * @var bool
     */
    public $required;

    /**
     * @var bool
     */
    public $enabled;

    /**
     * Internal reference to the inquisition this question was loaded for. Not
     * for saving.
     *
     * @var int
     */
    public $inquisition;

    public function getView(InquisitionInquisitionQuestionBinding $binding)
    {
        switch ($this->question_type) {
            default:
            case self::TYPE_RADIO_LIST:
                $view = new InquisitionRadioListQuestionView($binding);
                break;

            case self::TYPE_FLYDOWN:
                $view = new InquisitionFlydownQuestionView($binding);
                break;

            case self::TYPE_RADIO_ENTRY:
                $view = new InquisitionRadioEntryQuestionView($binding);
                break;

            case self::TYPE_TEXT:
                $view = new InquisitionTextQuestionView($binding);
                break;

            case self::TYPE_CHECKBOX_LIST:
                $view = new InquisitionCheckboxListQuestionView($binding);
                break;

            case self::TYPE_CHECKBOX_ENTRY:
                $view = new InquisitionCheckboxEntryQuestionView($binding);
                break;
        }

        return $view;
    }

    protected function init()
    {
        $this->table = 'InquisitionQuestion';
        $this->id_field = 'integer:id';

        $this->registerInternalProperty(
            'correct_option',
            SwatDBClassMap::get(InquisitionQuestionOption::class)
        );

        $this->registerInternalProperty(
            'question_group',
            SwatDBClassMap::get(InquisitionQuestionGroup::class)
        );
    }

    protected function getSerializableSubDataObjects()
    {
        return array_merge(
            parent::getSerializableSubDataObjects(),
            ['options', 'correct_option']
        );
    }

    // loader methods

    protected function loadOptions()
    {
        $sql = sprintf(
            'select * from InquisitionQuestionOption
			where question = %s
			order by displayorder',
            $this->db->quote($this->id, 'integer')
        );

        return SwatDB::query(
            $this->db,
            $sql,
            SwatDBClassMap::get(InquisitionQuestionOptionWrapper::class)
        );
    }

    protected function loadHints()
    {
        $sql = sprintf(
            'select * from InquisitionQuestionHint
			where question = %s
			order by displayorder',
            $this->db->quote($this->id, 'integer')
        );

        return SwatDB::query(
            $this->db,
            $sql,
            SwatDBClassMap::get(InquisitionQuestionHintWrapper::class)
        );
    }

    protected function loadImages()
    {
        $sql = sprintf(
            'select * from Image
			inner join InquisitionQuestionImageBinding
				on InquisitionQuestionImageBinding.image = Image.id
			where InquisitionQuestionImageBinding.question = %s
			order by InquisitionQuestionImageBinding.displayorder,
				InquisitionQuestionImageBinding.image',
            $this->db->quote($this->id, 'integer')
        );

        return SwatDB::query(
            $this->db,
            $sql,
            SwatDBClassMap::get(InquisitionQuestionImageWrapper::class)
        );
    }

    // saver methods

    protected function saveOptions()
    {
        foreach ($this->options as $option) {
            $option->question = $this;
        }

        $this->options->setDatabase($this->db);
        $this->options->save();
    }
}
