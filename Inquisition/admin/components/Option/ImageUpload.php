<?php

require_once 'SwatDB/SwatDB.php';
require_once 'SwatDB/SwatDBClassMap.php';
require_once 'Admin/exceptions/AdminNotFoundException.php';
require_once 'Inquisition/dataobjects/InquisitionQuestionOption.php';
require_once 'Inquisition/dataobjects/InquisitionQuestionOptionImage.php';
require_once 'Inquisition/admin/components/Inquisition/ImageUpload.php';

/**
 * Upload page for option images
 *
 * @package   Inquisition
 * @copyright 2012-2014 silverorange
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
	// {{{ abstract protected function getImageClass()

	protected function getImageClass()
	{
		return SwatDBClassMap::get('InquisitionQuestionOptionImage');
	}

	// }}}
	// {{{ protected function updateBindings()

	protected function updateBindings(SiteImage $image)
	{
		$sql = sprintf(
			'insert into InquisitionQuestionOptionImageBinding
			(question_option, image) values (%s, %s)',
			$this->app->db->quote($this->option->id, 'integer'),
			$this->app->db->quote($image->id, 'integer')
		);

		SwatDB::exec($this->app->db, $sql);
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
	// {{{ protected function loadDBData()

	protected function loadDBData()
	{
	}

	// }}}
	// {{{ protected function buildFrame()

	protected function buildFrame()
	{
		$frame = $this->ui->getWidget('edit_frame');
		$frame->title = sprintf($this->getOptionTitle());
		$frame->subtitle = $this->getTitle();
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
	// {{{ protected function getTitle()

	protected function getTitle()
	{
		return Inquisition::_('Add Image');
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
}

?>
