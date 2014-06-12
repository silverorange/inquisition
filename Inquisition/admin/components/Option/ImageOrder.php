<?php

require_once 'SwatDB/SwatDB.php';
require_once 'Admin/pages/AdminDBOrder.php';
require_once 'Inquisition/dataobjects/InquisitionQuestionOption.php';

/**
 * Change order page for option images
 *
 * @package   Inquisition
 * @copyright 2012-2014 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class InquisitionOptionImageOrder extends AdminDBOrder
{
	// {{{ protected properties

	/**
	 * @var InquisitionQuestionOption
	 */
	protected $option;

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

		$this->initOption();
		$this->initInquisition();
	}

	// }}}
	// {{{ protected function initOption()

	protected function initOption()
	{
		$id = SiteApplication::initVar('id');

		if ($id == '') {
			throw new AdminNotFoundException(
				Inquisition::_('No option id specified.')
			);
		}

		if (is_numeric($id)) {
			$id = intval($id);
		}

		$class = SwatDBClassMap::get('InquisitionQuestionOption');
		$this->option = new $class;
		$this->option->setDatabase($this->app->db);

		if (!$this->option->load($id)) {
			throw new AdminNotFoundException(
				sprintf(
					Inquisition::_(
						'An option with the id of “%s” does not exist'
					),
					$id
				)
			);
		}
	}

	// }}}
	// {{{ protected function initInquisition()

	protected function initInquisition()
	{
		$inquisition_id = SiteApplication::initVar('inquisition');

		if ($inquisition_id !== null) {
			$this->inquisition = $this->loadInquisition($inquisition_id);
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
	// {{{ protected function saveIndex()

	protected function saveIndex($id, $index)
	{
		SwatDB::updateColumn(
			$this->app->db, 'InquisitionQuestionOptionImageBinding',
			'integer:displayorder', $index, 'integer:image', array($id)
		);
	}

	// }}}
	// {{{ protected function getUpdatedMessage()

	protected function getUpdatedMessage()
	{
		return new SwatMessage(Inquisition::_('Image order has been updated.'));
	}

	// }}}
	// {{{ protected function relocate()

	protected function relocate()
	{
		$this->app->relocate(
			sprintf(
				'Option/Details?id=%s%s',
				$this->option->id,
				$this->getLinkSuffix()
			)
		);
	}

	// }}}

	// build phase
	// {{{ protected function loadData()

	protected function loadData()
	{
		$order_widget = $this->ui->getWidget('order');

		foreach ($this->option->images as $image) {
			$order_widget->addOption(
				$image->id,
				strval($image->getImgTag('thumb', '../')),
				'text/xml'
			);
		}

		$sql = sprintf(
			'select sum(displayorder)
			from InquisitionQuestionOptionImageBinding
			where question_option = %s',
			$this->option->id
		);

		$sum = SwatDB::queryOne($this->app->db, $sql, 'integer');

		$options_list = $this->ui->getWidget('options');
		$options_list->value = ($sum == 0) ? 'auto' : 'custom';
	}

	// }}}
	// {{{ protected function buildInternal()

	protected function buildInternal()
	{
		$this->ui->getWidget('order_frame')->title = $this->getTitle();
		$this->ui->getWidget('order')->width = '150px';
		$this->ui->getWidget('order')->height = '300px';

		parent::buildInternal();
	}

	// }}}
	// {{{ protected function buildForm()

	protected function buildForm()
	{
		parent::buildForm();

		$form = $this->ui->getWidget('order_form');
		$form->addHiddenField('id', $this->option->id);

		if ($this->inquisition instanceof InquisitionInquisition) {
			$form->addHiddenField('inquisition', $this->inquisition->id);
		}
	}

	// }}}
	// {{{ protected function buildNavBar()

	protected function buildNavBar()
	{
		parent::buildNavBar();

		$this->navbar->popEntry();

		if ($this->inquisition instanceof InquisitionInquisition) {
			$this->navbar->createEntry(
				$this->inquisition->title,
				sprintf(
					'Inquisition/Details?id=%s',
					$this->inquisition->id
				)
			);
		}

		$this->navbar->createEntry(
			$this->getQuestionTitle(),
			sprintf(
				'Question/Details?id=%s%s',
				$this->option->question->id,
				$this->getLinkSuffix()
			)
		);

		$this->navbar->createEntry(
			$this->getOptionTitle(),
			sprintf(
				'Option/Details?id=%s%s',
				$this->option->id,
				$this->getLinkSuffix()
			)
		);

		$this->navbar->createEntry($this->getTitle());
	}

	// }}}
	// {{{ protected function getOptionTitle()

	protected function getOptionTitle()
	{
		return sprintf(
			Inquisition::_('Option %s'),
			$this->option->position
		);
	}

	// }}}
	// {{{ protected function getQuestionTitle()

	protected function getQuestionTitle()
	{
		// TODO: Update this with some version of getPosition().
		return Inquisition::_('Question');
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
	// {{{ protected function getTitle()

	protected function getTitle()
	{
		return Inquisition::_('Change Image Order');
	}

	// }}}
}

?>
