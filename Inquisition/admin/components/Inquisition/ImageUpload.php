<?php

require_once 'SwatDB/SwatDB.php';
require_once 'SwatDB/SwatDBClassMap.php';
require_once 'Admin/pages/AdminDBEdit.php';
require_once 'Admin/exceptions/AdminNotFoundException.php';

/**
 * Upload page for inquisition images
 *
 * @package   Inquisition
 * @copyright 2012 silverorange
 */
abstract class InquisitionInquisitionImageUpload extends AdminDBEdit
{
	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		parent::initInternal();

		$this->ui->loadFromXML($this->getUiXml());
	}

	// }}}
	// {{{ protected function getUiXml()

	protected function getUiXml()
	{
		return 'Inquisition/admin/components/Inquisition/image-upload.xml';
	}

	// }}}

	// process phase
	// {{{ protected function saveDBData()

	protected function saveDBData()
	{
		$original = $this->ui->getWidget('original_image');

		$class_name = SwatDBClassMap::get('InquisitionQuestionImage');

		$image = new $class_name();
		$image->setDatabase($this->app->db);
		$image->setFileBase('../images');
		$image->process($original->getTempFileName());

		$this->updateBindings($image);

		$this->app->messages->add(
			new SwatMessage(
				sprintf(
					Inquisition::_('Image has been saved.'),
					$image->title
				)
			)
		);
	}

	// }}}
	// {{{ abstract protected function updateBindings()

	abstract protected function updateBindings(SiteImage $image);

	// }}}
}

?>
