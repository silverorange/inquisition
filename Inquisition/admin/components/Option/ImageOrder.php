<?php

require_once 'SwatDB/SwatDB.php';
require_once 'Admin/pages/AdminDBOrder.php';
require_once 'Inquisition/dataobjects/InquisitionQuestionOption.php';

/**
 * Change order page for option images
 *
 * @package   Inquisition
 * @copyright 2012 silverorange
 */
class InquisitionOptionImageOrder extends AdminDBOrder
{
	// {{{ protected properties

	/**
	 * @var InquisitionQuestionOption
	 */
	protected $option;

	// }}}

	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		parent::initInternal();

		$this->initOption();
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
		return new SwatMessage('Image order has been updated.');
	}

	// }}}
	// {{{ protected function relocate()

	protected function relocate()
	{
		$this->app->relocate(
			sprintf(
				'Option/Details?id=%s',
				$this->option->id
			)
		);
	}

	// }}}

	// build phase
	// {{{ protected function buildInternal()

	protected function buildInternal()
	{
		$this->ui->getWidget('order_frame')->title =
			Inquisition::_('Change Image Order');

		$this->ui->getWidget('order')->width = '150px';
		$this->ui->getWidget('order')->height = '300px';

		parent::buildInternal();
	}

	// }}}
	// {{{ protected function buildNavBar()

	protected function buildNavBar()
	{
		parent::buildNavBar();

		$this->navbar->popEntry();

		$this->navbar->createEntry(
			$this->option->question->inquisition->title,
			sprintf(
				'Inquisition/Details?id=%s',
				$this->option->question->inquisition->id
			)
		);

		$this->navbar->createEntry(
			sprintf('Question %s', $this->option->question->position),
			sprintf(
				'Question/Details?id=%s',
				$this->option->question->id
			)
		);

		$this->navbar->createEntry(
			sprintf('Option %s', $this->option->position),
			sprintf(
				'Option/Details?id=%s',
				$this->option->id
			)
		);

		$this->navbar->createEntry(Inquisition::_('Change Image Order'));
	}

	// }}}
	// {{{ protected function buildForm()

	protected function buildForm()
	{
		parent::buildForm();

		$form = $this->ui->getWidget('order_form');
		$form->addHiddenField('id', $this->option->id);
	}

	// }}}
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
			'select sum(displayorder) from InquisitionQuestionOptionImageBinding
				where question_option = %s',
			$this->option->id
		);

		$sum = SwatDB::queryOne($this->app->db, $sql, 'integer');

		$options_list = $this->ui->getWidget('options');
		$options_list->value = ($sum == 0) ? 'auto' : 'custom';
	}

	// }}}
}

?>
