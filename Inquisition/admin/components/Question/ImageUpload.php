<?php

require_once 'SwatDB/SwatDB.php';
require_once 'SwatDB/SwatDBClassMap.php';
require_once 'Admin/pages/AdminDBEdit.php';
require_once 'Admin/exceptions/AdminNotFoundException.php';
require_once 'Inquisition/admin/components/Inquisition/ImageUpload.php';

/**
 * Upload page for question images
 *
 * @package   Inquisition
 * @copyright 2012 silverorange
 */
class InquisitionQuestionImageUpload extends InquisitionInquisitionImageUpload
{
	// {{{ protected properties

	/**
	 * @var InquisitionQuestion
	 */
	protected $question;

	// }}}

	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		parent::initInternal();

		$this->initQuestion();
	}

	// }}}
	// {{{ protected function initQuestion()

	protected function initQuestion()
	{
		if ($this->id == '') {
			throw new AdminNotFoundException(
				Inquisition::_('Unable to load a question.')
			);
		}

		$class_name = SwatDBClassMap::get('InquisitionQuestion');

		$this->question = new $class_name();
		$this->question->setDatabase($this->app->db);

		if (!$this->question->load($this->id)) {
			throw new AdminNotFoundException(
				sprintf(
					Inquisition::_(
						'Unable to load question with id of “%s”.'
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
		$sql = sprintf('insert into InquisitionQuestionImageBinding
			(question, image) values (%s, %s)',
			$this->app->db->quote($this->question->id, 'integer'),
			$this->app->db->quote($image->id, 'integer'));

		SwatDB::exec($this->app->db, $sql);
	}

	// }}}
	// {{{ protected function relocate()

	protected function relocate()
	{
		$this->app->relocate(
			sprintf(
				'Question/Details?id=%s',
				$this->question->id
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
			$this->question->inquisition->title,
			sprintf(
				'Inquisition/Details?id=%s',
				$this->question->inquisition->id
			)
		);

		$this->navbar->createEntry(
			sprintf('Question %s', $this->question->position),
			sprintf(
				'Question/Details?id=%s',
				$this->question->id
			)
		);

		$this->navbar->createEntry('Add Image');
	}

	// }}}
	// {{{ protected function buildFrame()

	protected function buildFrame()
	{
		$frame = $this->ui->getWidget('edit_frame');
		$frame->title = sprintf('Question %s', $this->question->position);
		$frame->subtitle = 'Add Image';
	}

	// }}}
}

?>
