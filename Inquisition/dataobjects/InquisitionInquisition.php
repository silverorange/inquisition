<?php

/**
 * An inquisition.
 *
 * @copyright 2011-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class InquisitionInquisition extends SwatDBDataObject
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
     * Whether or not this inquisition should be considered enabled.
     *
     * The enabled field is not respected by default. It can be used
     * for sites/packages that implement the enabled flag for
     * inquisition visibility.
     *
     * @var bool
     */
    public $enabled;

    /**
     * @var SwatDate
     */
    public $createdate;

    protected array $question_dependencies = [];

    public function getResponseByAccount(SiteAccount $account)
    {
        $this->checkDB();

        $sql = sprintf(
            'select * from InquisitionResponse
			where account = %s and inquisition = %s',
            $this->db->quote($account->id, 'integer'),
            $this->db->quote($this->id, 'integer')
        );

        $wrapper = $this->getResolvedResponseWrapperClass();
        $response = SwatDB::query($this->db, $sql, $wrapper)->getFirst();

        if ($response instanceof InquisitionResponse) {
            $response->inquisition = $this;
        }

        return $response;
    }

    public function addQuestionDependency(
        InquisitionInquisitionQuestionBinding $dependent_question_binding,
        InquisitionInquisitionQuestionBinding $question_binding,
        InquisitionQuestionOption $option
    ) {
        $this->question_dependencies[] = [
            'dependent_question_binding' => $dependent_question_binding,
            'question_binding'           => $question_binding,
            'option'                     => $option,
        ];
    }

    protected function init()
    {
        $this->table = 'Inquisition';
        $this->id_field = 'integer:id';
        $this->registerDateProperty('createdate');
    }

    protected function getSerializableSubDataObjects()
    {
        return array_merge(
            parent::getSerializableSubDataObjects(),
            [
                'question_bindings',
                'visible_question_bindings',
            ]
        );
    }

    protected function getResolvedResponseWrapperClass()
    {
        return SwatDBClassMap::get($this->getResponseWrapperClass());
    }

    protected function getResponseWrapperClass()
    {
        return InquisitionResponseWrapper::class;
    }

    // saver methods

    protected function saveQuestionBindings()
    {
        foreach ($this->question_bindings as $question_binding) {
            $question_binding->inquisition = $this;
        }

        $this->question_bindings->setDatabase($this->db);
        $this->question_bindings->save();

        foreach ($this->question_dependencies as $question_dependency) {
            $dependent_binding = $question_dependency['dependent_question_binding'];
            $binding = $question_dependency['question_binding'];
            $option = $question_dependency['option'];

            SwatDB::exec(
                $this->db,
                sprintf(
                    'insert into InquisitionQuestionDependency
						(dependent_question_binding, question_binding, option)
					values
						(%s, %s, %s)
					',
                    $this->db->quote($dependent_binding->id, 'integer'),
                    $this->db->quote($binding->id, 'integer'),
                    $this->db->quote($option->id, 'integer')
                )
            );
        }
    }

    // loader methods

    protected function loadResponses()
    {
        $sql = sprintf(
            'select * from InquisitionResponse
			where inquisition = %s
			order by createdate, id',
            $this->db->quote($this->id, 'integer')
        );

        return SwatDB::query(
            $this->db,
            $sql,
            $this->getResolvedResponseWrapperClass()
        );
    }

    protected function loadQuestionBindings()
    {
        $sql = sprintf(
            'select * from InquisitionInquisitionQuestionBinding
			where inquisition = %s order by displayorder, id',
            $this->db->quote($this->id, 'integer')
        );

        return SwatDB::query(
            $this->db,
            $sql,
            SwatDBClassMap::get(InquisitionInquisitionQuestionBindingWrapper::class)
        );
    }

    protected function loadVisibleQuestionBindings()
    {
        $sql = sprintf(
            'select InquisitionInquisitionQuestionBinding.*
			from InquisitionInquisitionQuestionBinding
			inner join VisibleInquisitionQuestionView
				on InquisitionInquisitionQuestionBinding.question =
					VisibleInquisitionQuestionView.question
			where InquisitionInquisitionQuestionBinding.inquisition = %s
			order by InquisitionInquisitionQuestionBinding.displayorder,
				InquisitionInquisitionQuestionBinding.id',
            $this->db->quote($this->id, 'integer')
        );

        return SwatDB::query(
            $this->db,
            $sql,
            SwatDBClassMap::get(InquisitionInquisitionQuestionBindingWrapper::class)
        );
    }
}
