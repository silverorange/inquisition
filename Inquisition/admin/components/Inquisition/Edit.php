<?php

require_once 'Swat/SwatDate.php';
require_once 'Admin/exceptions/AdminNotFoundException.php';
require_once 'Admin/pages/AdminDBEdit.php';
require_once 'Inquisition/dataobjects/Inquisition.php';

/**
 * @package   Inquisition
 * @copyright 2011 silverorange
 */
class InquisitionInquisitionEdit extends AdminDBEdit
{
	// {{{ protected properties

	/**
	 * @var Inquisition
	 */
	protected $inquisition;

	protected $ui_xml = 'Inquisition/admin/components/Inquisition/edit.xml';

	// }}}

	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		parent::initInternal();
		$this->ui->loadFromXML($this->ui_xml);
		$this->initInquisition();
	}

	// }}}
	// {{{ protected function initInquisition()

	protected function initInquisition()
	{
		$class = SwatDBClassMap::get('Inquisition');
		$this->inquisition = new $class;
		$this->inquisition->setDatabase($this->app->db);

		if ($this->id !== null) {
			if (!$this->inquisition->load($this->id)) {
				throw new AdminNotFoundException(
					sprintf('Inquisition with id ‘%s’ not found.', $this->id));
			}
		}
	}

	// }}}

	// process phase
	// {{{ protected function saveDBData()

	protected function saveDBData()
	{
		$this->updateInquisition();

		if ($this->inquisition->isModified()) {
			$this->inquisition->save();
			$this->app->messages->add($this->getSavedMessage());
		}
	}

	// }}}
	// {{{ protected function updateInquisition()

	protected function updateInquisition()
	{
		$values = $this->ui->getValues(array(
			'title',
		));

		if ($this->inquisition->id === null) {
			$now = new SwatDate();
			$now->toUTC();
			$this->inquisition->createdate = $now;
		}

		$this->inquisition->title         = $values['title'];
	}

	// }}}
	// {{{ protected function getSavedMessage()

	protected function getSavedMessage()
	{
		return new SwatMessage('Inquisition has been saved.');
	}

	// }}}

	// build phase
	// {{{ protected function loadDBData()

	protected function loadDBData()
	{
		$this->ui->setValues(get_object_vars($this->inquisition));
	}

	// }}}
}

?>
