<?php

require_once 'Inquisition/dataobjects/InquisitionQuestionOption.php';
require_once 'Inquisition/admin/components/Inquisition/ImageDelete.php';

/**
 * Delete confirmation page for option images
 *
 * @package   Inquisition
 * @copyright 2012-2013 silverorange
 */
class InquisitionOptionImageDelete extends InquisitionInquisitionImageDelete
{
	// {{{ protected properties

	/**
	 * @var InquisitonQuestionOption
	 */
	protected $option;

	// }}}

	// helper methods
	// {{{ public function setId()

	public function setId($id)
	{
		$class_name = SwatDBClassMap::get('InquisitionQuestionOption');

		$this->option = new $class_name();
		$this->option->setDatabase($this->app->db);

		if ($id == '') {
			throw new AdminNotFoundException(
				'Option id not provided.'
			);
		}

		if (!$this->option->load($id)) {
			throw new AdminNotFoundException(
				sprintf(
					'Option with id ‘%s’ not found.',
					$id
				)
			);
		}

		parent::setId($id);
	}

	// }}}

	// build phase
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

		if ($this->option instanceof InquisitionQuestionOption) {
			$this->navbar->createEntry(
				$this->getOptionTitle(),
				sprintf(
					'Option/Details?id=%s%s',
					$this->option->id,
					$this->getLinkSuffix()
				)
			);
		}

		$this->navbar->createEntry(
			Inquisition::ngettext(
				'Delete Image',
				'Delete Images',
				count($this->images)
			)
		);
	}

	// }}}
	// {{{ protected function getQuestionTitle()

	protected function getQuestionTitle()
	{
		return ($this->inquisition instanceof InquisitionInquisition) ?
			sprintf(
				Inquisition::_('Question %s'),
				$this->question->getPosition($this->inquisition)
			) :
			Inquisition::_('Question');
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
