<?php

require_once 'SwatDB/SwatDB.php';
require_once 'SwatDB/SwatDBClassMap.php';
require_once 'Admin/pages/AdminDBEdit.php';
require_once 'Admin/exceptions/AdminNotFoundException.php';
require_once 'Inquisition/dataobjects/InquisitionQuestion.php';
require_once 'Inquisition/dataobjects/InquisitionQuestionImage.php';
require_once 'Inquisition/admin/components/Inquisition/ImageUpload.php';

/**
 * Upload page for question images
 *
 * @package   Inquisition
 * @copyright 2012-2013 silverorange
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
	// {{{ abstract protected function getImageClass()

	protected function getImageClass()
	{
		return SwatDBClassMap::get('InquisitionQuestionImage');
	}

	// }}}
	// {{{ protected function updateBindings()

	protected function updateBindings(SiteImage $image)
	{
		// set displayorder so the new image appears at the end of the
		// list of the current questions by default.
		$sql = sprintf(
			'select coalesce(max(displayorder), 0)+10
			from InquisitionQuestionImageBinding where question = %s',
			$this->app->db->quote($this->question->id, 'integer')
		);

		$displayorder = SwatDB::queryOne($this->app->db, $sql);

		$sql = sprintf(
			'insert into InquisitionQuestionImageBinding
			(question, image, displayorder) values (%s, %s, %s)',
			$this->app->db->quote($this->question->id, 'integer'),
			$this->app->db->quote($image->id, 'integer'),
			$this->app->db->quote($displayorder, 'integer')
		);

		SwatDB::exec($this->app->db, $sql);
	}

	// }}}
	// {{{ protected function relocate()

	protected function relocate()
	{
		$this->app->relocate(
			sprintf(
				'Question/Details?id=%s%s',
				$this->question->id,
				$this->getLinkSuffix()
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

		$this->navbar->createEntry(
			$this->getQuestionTitle(),
			sprintf(
				'Question/Details?id=%s%s',
				$this->question->id,
				$this->getLinkSuffix()
			)
		);

		$this->navbar->createEntry(Inquisition::_('Add Image'));
	}

	// }}}
	// {{{ protected function buildFrame()

	protected function buildFrame()
	{
		$frame = $this->ui->getWidget('edit_frame');
		$frame->title = $this->getQuestionTitle();

		$frame->subtitle = Inquisition::_('Add Image');
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
}

?>
