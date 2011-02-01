<?php

require_once 'SwatDB/SwatDB.php';
require_once 'Admin/AdminListDependency.php';
require_once 'Admin/AdminSummaryDependency.php';
require_once 'Admin/pages/AdminDBDelete.php';

/**
 * Delete confirmation page for questions
 *
 * @package   Inquisition
 * @copyright 2011 silverorange
 */
class InquisitionInquisitionQuestionDelete extends AdminDBDelete
{
	// process phase
	// {{{ protected function processDBData()

	protected function processDBData()
	{
		parent::processDBData();

		$sql = sprintf('delete from InquisitionQuestion where id in (%s)',
			$this->getItemList('integer'));

		$num = SwatDB::exec($this->app->db, $sql);

		$message = new SwatMessage(sprintf(ngettext(
			'One question has been deleted.',
			'%d questions have been deleted.', $num),
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
		$dep->setTitle('question', 'questions');
		$dep->entries = AdminListDependency::queryEntries($this->app->db,
			'InquisitionQuestion', 'id', null, 'text:bodytext', 'id',
			'id in ('.$item_list.')', AdminDependency::DELETE);

		foreach ($dep->entries as $entry)
			$entry->content_type = 'text/xml';

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
