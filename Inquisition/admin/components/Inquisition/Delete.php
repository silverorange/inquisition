<?php

require_once 'SwatDB/SwatDB.php';
require_once 'Admin/AdminListDependency.php';
require_once 'Admin/AdminSummaryDependency.php';
require_once 'Admin/pages/AdminDBDelete.php';

/**
 * Delete confirmation page for inquisitions
 *
 * @package   Inquisition
 * @copyright 2011 silverorange
 */
class InquisitionInquisitionDelete extends AdminDBDelete
{
	// process phaes
	// {{{ protected function processDBData()

	protected function processDBData()
	{
		parent::processDBData();

		$sql = sprintf('delete from Inquisition where id in (%s)',
			$this->getItemList('integer'));

		$num = SwatDB::exec($this->app->db, $sql);

		$message = new SwatMessage(sprintf(ngettext(
			'One inquisition has been deleted.',
			'%d inquisitions have been deleted.', $num),
			SwatString::numberFormat($num)), 'notice');

		$this->app->messages->add($message);
	}

	// }}}

	// build phase
	// {{{ protected function buildInternal()

	protected function buildInternal()
	{
		parent::buildInternal();

		$item_list = $this->getItemList('integer');

		$dep = new AdminListDependency();
		$dep->setTitle('inquisition', 'inquisitions');
		$dep->entries = AdminListDependency::queryEntries($this->app->db,
			'Inquisition', 'id', null, 'text:title', 'id',
			'id in ('.$item_list.')', AdminDependency::DELETE);

		// check inquisition dependencies
		$dep_questions = new AdminSummaryDependency();
		$dep_questions->setTitle('question', 'questions');
		$dep_questions->summaries = AdminSummaryDependency::querySummaries(
			$this->app->db, 'InquisitionQuestion', 'integer:id', 'integer:inquisition',
			'inquisition in ('.$item_list.')', AdminDependency::DELETE);

		$dep->addDependency($dep_questions);

		$message = $this->ui->getWidget('confirmation_message');
		$message->content = $dep->getMessage();
		$message->content_type = 'text/xml';

		if ($dep->getStatusLevelCount(AdminDependency::DELETE) == 0) {
			$this->switchToCancelButton();
		}
	}

	// }}}
}

?>
