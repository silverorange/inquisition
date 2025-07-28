<?php

/**
 * Delete confirmation page for inquisitions.
 *
 * @copyright 2011-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class InquisitionInquisitionDelete extends AdminDBDelete
{
    /**
     * @var InquisitionInquisition
     */
    protected $inquisition;

    // init phase

    protected function initInternal()
    {
        parent::initInternal();

        $this->initInquisition();
    }

    protected function initInquisition()
    {
        $this->inquisition = SwatDBClassMap::new(InquisitionInquisition::class);
        $this->inquisition->setDatabase($this->app->db);

        $id = $this->getFirstItem();

        if (!$this->inquisition->load($id)) {
            throw new AdminNotFoundException(sprintf(
                'A inquisition with the id of “%s” does not exist',
                $id
            ));
        }
    }

    // process phase

    protected function processDBData(): void
    {
        parent::processDBData();

        $item_list = $this->getItemList('integer');

        $this->deleteQuestions($item_list);

        $sql = sprintf(
            'delete from Inquisition where id in (%s)',
            $item_list
        );

        $num = SwatDB::exec($this->app->db, $sql);
        $this->app->messages->add($this->getDeletedMessage($num));
    }

    protected function deleteQuestions($item_list)
    {
        // By default delete questions that don't belong to other inquisitions
        // instead of leaving orphan questions.
        $sql = sprintf(
            'delete from InquisitionQuestion where id in (
				select question from InquisitionInquisitionQuestionBinding
				where %s
			)',
            $this->getSingleQuizQuestionsWhere($item_list)
        );

        SwatDB::exec($this->app->db, $sql);
    }

    protected function getDeletedMessage($num)
    {
        return new SwatMessage(
            sprintf(
                Inquisition::ngettext(
                    'One inquisition has been deleted.',
                    '%s inquisitions have been deleted.',
                    $num
                ),
                SwatString::numberFormat($num)
            )
        );
    }

    // build phase

    protected function buildInternal()
    {
        parent::buildInternal();

        $item_list = $this->getItemList('integer');

        $dep = new AdminListDependency();
        $dep->entries = AdminListDependency::queryEntries(
            $this->app->db,
            'Inquisition',
            'id',
            null,
            'text:title',
            'id',
            'id in (' . $item_list . ')',
            AdminDependency::DELETE
        );

        // check inquisition dependencies
        $dep_questions = new AdminSummaryDependency();
        $dep_questions->setTitle('question', 'questions');
        $dep_questions->summaries = AdminSummaryDependency::querySummaries(
            $this->app->db,
            'InquisitionInquisitionQuestionBinding',
            'integer:question',
            'integer:inquisition',
            $this->getSingleQuizQuestionsWhere($item_list),
            AdminDependency::DELETE
        );

        $dep->addDependency($dep_questions);

        $message = $this->ui->getWidget('confirmation_message');
        $message->content = $dep->getMessage();
        $message->content_type = 'text/xml';

        if ($dep->getStatusLevelCount(AdminDependency::DELETE) == 0) {
            $this->switchToCancelButton();
        }
    }

    protected function buildNavBar()
    {
        parent::buildNavBar();

        $last = $this->navbar->popEntry();

        $this->navbar->createEntry(
            $this->inquisition->title,
            sprintf(
                'Inquisition/Details?id=%s',
                $this->inquisition->id
            )
        );

        $this->navbar->addEntry($last);
    }

    // helper methods

    protected function getSingleQuizQuestionsWhere($item_list)
    {
        return sprintf(
            'inquisition in (%1$s)
			and question not in (
				select question from InquisitionInquisitionQuestionBinding
				where inquisition not in (%1$s)
			)',
            $item_list
        );
    }
}
