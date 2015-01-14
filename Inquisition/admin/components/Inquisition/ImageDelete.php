<?php


require_once 'Swat/SwatTableStore.php';
require_once 'Swat/SwatDetailsStore.php';
require_once 'SwatDB/SwatDB.php';
require_once 'SwatDB/SwatDBClassMap.php';
require_once 'Admin/pages/AdminDBDelete.php';

/**
 * Delete confirmation page for inquisition images
 *
 * @package   Inquisition
 * @copyright 2012-2015 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class InquisitionInquisitionImageDelete extends AdminDBDelete
{
	// {{{ protected properties

	/**
	 * @var InquisitonQuestionImageWrapper
	 */
	protected $images;

	/**
	 * @var InquisitionInquisition
	 */
	protected $inquisition;

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
	// {{{ public function setInquisition()

	public function setInquisition(InquisitionInquisition $inquisition = null)
	{
		if ($inquisition instanceof InquisitionInquisition) {
			$this->inquisition = $inquisition;

			$form = $this->ui->getWidget('confirmation_form');
			$form->addHiddenField('inquisition_id', $this->inquisition->id);
		}
	}

	// }}}
	// {{{ abstract protected function getImageWrapper()

	abstract protected function getImageWrapper();

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

		$inquisition_id = $form->getHiddenField('inquisition_id');
		if ($inquisition_id != '') {
			$inquisition = $this->loadInquisition($inquisition_id);
			$this->setInquisition($inquisition);
		}
	}

	// }}}
	// {{{ protected function loadInquisition()

	protected function loadInquisition($inquisition_id)
	{
		$class = SwatDBClassMap::get('InquisitionInquisition');
		$inquisition = new $class;
		$inquisition->setDatabase($this->app->db);

		if (!$inquisition->load($inquisition_id)) {
			throw new AdminNotFoundException(
				sprintf(
					'Inquisition with id ‘%s’ not found.',
					$inquisition_id
				)
			);
		}

		return $inquisition;
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
					Inquisition::ngettext(
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
		$message->content = sprintf(
			'<strong>%s</strong>',
			Inquisition::ngettext(
				'Are you sure you want to delete the following image?',
				'Are you sure you want to delete the following images?',
				count($this->images)
			)
		);
	}

	// }}}
	// {{{ protected function buildForm()

	protected function buildForm()
	{
		parent::buildForm();

		$yes_button = $this->ui->getWidget('yes_button');
		$yes_button->title = Inquisition::_('Delete');
	}

	// }}}
	// {{{ protected function buildNavBar()

	protected function buildNavBar()
	{
		parent::buildNavBar();

		if ($this->inquisition instanceof InquisitionInquisition) {
			$this->navbar->createEntry(
				$this->inquisition->title,
				sprintf(
					'Inquisition/Details?id=%s',
					$this->inquisition->id
				)
			);
		}
	}

	// }}}
	// {{{ protected function getLinkSuffix()

	protected function getLinkSuffix()
	{
		$suffix = null;
		if ($this->inquisition instanceof InquisitionInquisition) {
			$suffix = sprintf(
				'&inquisition=%s',
				$this->inquisition->id
			);
		}

		return $suffix;
	}

	// }}}
}

?>
