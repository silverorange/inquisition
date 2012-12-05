<?php


require_once 'Swat/SwatTableStore.php';
require_once 'Swat/SwatDetailsStore.php';
require_once 'SwatDB/SwatDB.php';
require_once 'SwatDB/SwatDBClassMap.php';
require_once 'Admin/pages/AdminDBDelete.php';
require_once 'Inquisition/dataobjects/InquisitionQuestionImageWrapper.php';

/**
 * Delete confirmation page for inquisition images
 *
 * @package   Inquisition
 * @copyright 2012 silverorange
 */
abstract class InquisitionInquisitionImageDelete extends AdminDBDelete
{
	// {{{ protected properties

	/**
	 * @var InquisitonQuestionImageWrapper
	 */
	protected $images;

	// }}}

	// helper methods
	// {{{ public function setId()

	public function setId($id)
	{
		$form = $this->ui->getWidget('confirmation_form');
		$form->addHiddenField('id', $id);
	}

	// }}}
	// {{{ public function setItems()

	public function setItems($items, $extended_selected = false)
	{
		parent::setItems($items, $extended_selected);

		$sql = sprintf('select Image.* from Image where id in (%s)',
			$this->getItemList('integer'));

		$this->images = SwatDB::query($this->app->db, $sql,
			SwatDBClassMap::get('InquisitionQuestionImageWrapper'));
	}

	// }}}

	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		$this->ui_xml = dirname(__FILE__).'/image-delete.xml';

		parent::initInternal();

		$form = $this->ui->getWidget('confirmation_form');
		$id = $form->getHiddenField('id');
		if ($id != '') {
			$this->setId($id);
		}
	}

	// }}}

	// process phase
	// {{{ protected function processDBData()

	protected function processDBData()
	{
		parent::processDBData();

		$delete_count = 0;

		foreach ($this->images as $image) {
			$image->setFileBase('../images');
			$image->delete();

			$delete_count++;
		}

		$this->app->messages->add(
			new SwatMessage(
				sprintf(
					ngettext(
						'One image has been deleted.',
						'%s images have been deleted.',
						$delete_count
					),
					$delete_count
				)
			)
		);
	}

	// }}}
	// {{{ protected function relocate()

	protected function relocate()
	{
		AdminDBConfirmation::relocate();
	}

	// }}}

	// build phase
	// {{{ protected function buildInternal()

	protected function buildInternal()
	{
		parent::buildInternal();

		$store = new SwatTableStore();
		foreach ($this->images as $image) {
			$ds = new SwatDetailsStore();

			$ds->image = $image;

			$store->add($ds);
		}

		$delete_view = $this->ui->getWidget('delete_view');
		$delete_view->model = $store;

		$message = $this->ui->getWidget('confirmation_message');
		$message->content_type = 'text/xml';
		$message->content = sprintf('<strong>%s</strong>', ngettext(
			'Are you sure you want to delete the following image?',
			'Are you sure you want to delete the following images?',
			count($this->images)));
	}

	// }}}
	// {{{ protected function buildForm()

	protected function buildForm()
	{
		parent::buildForm();

		$yes_button = $this->ui->getWidget('yes_button');
		$yes_button->title = 'Delete';
	}

	// }}}
}

?>
