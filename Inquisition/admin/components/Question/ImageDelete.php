<?php

require_once 'Inquisition/dataobjects/InquisitionQuestion.php';
require_once 'Inquisition/dataobjects/InquisitionQuestionImageWrapper.php';
require_once 'Inquisition/admin/components/Inquisition/ImageDelete.php';

/**
 * Delete confirmation page for question images
 *
 * @package   Inquisition
 * @copyright 2012-2013 silverorange
 */
class InquisitionQuestionImageDelete extends InquisitionInquisitionImageDelete
{
	// {{{ protected properties

	/**
	 * @var InquisitonQuestion
	 */
	protected $question;

	// }}}

	// helper methods
	// {{{ public function setId()

	public function setId($id)
	{
		$class_name = SwatDBClassMap::get('InquisitionQuestion');

		$this->question = new $class_name();
		$this->question->setDatabase($this->app->db);

		if ($id == '') {
			throw new AdminNotFoundException(
				'Question id not provided.'
			);
		}

		if (!$this->question->load($id)) {
			throw new AdminNotFoundException(
				sprintf(
					'Question with id ‘%s’ not found.',
					$id
				)
			);
		}

		parent::setId($id);
	}

	// }}}
	// {{{ protected function getImageWrapper()

	protected function getImageWrapper()
	{
		return SwatDBClassMap::get('InquisitionQuestionImageWrapper');
	}

	// }}}

	// build phase
	// {{{ protected function buildNavBar()

	protected function buildNavBar()
	{
		AdminDBDelete::buildNavBar();

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
				$this->question->id,
				$this->getLinkSuffix()
			)
		);

		$this->navbar->createEntry(
			ngettext(
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
		// TODO: Update this with some version of getPosition().
		return Inquisition::_('Question');
	}

	// }}}
}

?>
