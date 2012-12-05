<?php

require_once 'SwatDB/SwatDB.php';
require_once 'SwatDB/SwatDBClassMap.php';
require_once 'Admin/pages/AdminDBEdit.php';
require_once 'Admin/exceptions/AdminNotFoundException.php';
require_once 'Inquisition/dataobjects/InquisitionQuestionOption.php';
require_once 'Inquisition/admin/components/Inquisition/ImageUpload.php';

/**
 * Upload page for option images
 *
 * @package   Inquisition
 * @copyright 2012 silverorange
 */
class InquisitionOptionImageUpload extends InquisitionInquisitionImageUpload
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
		if ($this->id == '') {
			throw new AdminNotFoundException(
				Inquisition::_('Unable to load a option.')
			);
		}

		$class_name = SwatDBClassMap::get('InquisitionQuestionOption');

		$this->option = new $class_name();
		$this->option->setDatabase($this->app->db);

		if (!$this->option->load($this->id)) {
			throw new AdminNotFoundException(
				sprintf(
					Inquisition::_(
						'Unable to load option with id of “%s”.'
					),
					$this->id
				)
			);
		}
	}

	// }}}

	// process phase
	// {{{ protected function updateBindings()

	protected function updateBindings(SiteImage $image)
	{
		$sql = sprintf('insert into InquisitionQuestionOptionImageBinding
			(question_option, image) values (%s, %s)',
			$this->app->db->quote($this->option->id, 'integer'),
			$this->app->db->quote($image->id, 'integer'));

		SwatDB::exec($this->app->db, $sql);
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
	// {{{ protected function loadDBData()

	protected function loadDBData()
	{
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

		$this->navbar->createEntry('Add Image');
	}

	// }}}
	// {{{ protected function buildFrame()

	protected function buildFrame()
	{
		$frame = $this->ui->getWidget('edit_frame');
		$frame->title = sprintf('Option %s', $this->option->position);
		$frame->subtitle = 'Add Image';
	}

	// }}}
}

?>
