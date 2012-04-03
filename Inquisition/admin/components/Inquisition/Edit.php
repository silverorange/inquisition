<?php

require_once 'Swat/SwatDate.php';
require_once 'Admin/exceptions/AdminNotFoundException.php';
require_once 'Admin/pages/AdminDBEdit.php';
require_once 'Inquisition/dataobjects/InquisitionInquisition.php';

/**
 * @package   Inquisition
 * @copyright 2011 silverorange
 */
class InquisitionInquisitionEdit extends AdminDBEdit
{
	// {{{ protected properties

	/**
	 * @var InquisitionInquisition
	 */
	protected $inquisition;

	// }}}

	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		parent::initInternal();
		$this->ui->loadFromXML($this->getUiXml());
		$this->initInquisition();
	}

	// }}}
	// {{{ protected function initInquisition()

	protected function initInquisition()
	{
		$class = SwatDBClassMap::get('InquisitionInquisition');
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
	// {{{ protected function getUiXml()

	protected function getUiXml()
	{
		return 'Inquisition/admin/components/Inquisition/edit.xml';
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
	// {{{ protected function relocate()

	protected function relocate()
	{
		$uri = $this->getComponentName().'/Details?id='.$this->inquisition->id;
		$this->app->relocate($uri);
	}

	// }}}

	// build phase
	// {{{ protected function loadDBData()

	protected function loadDBData()
	{
		$this->ui->setValues(get_object_vars($this->inquisition));
	}

	// }}}
	// {{{ protected function buildNavBar()

	protected function buildNavBar()
	{
		parent::buildNavBar();

		$last = $this->navbar->popEntry();

		if ($this->id !== null)
			$this->navbar->addEntry(new SwatNavBarEntry(
				$this->inquisition->title,
				sprintf('%s/Details?id=%s',
					$this->getComponentName(),
					$this->inquisition->id)));

		$this->navbar->addEntry($last);
	}

	// }}}
}

?>
