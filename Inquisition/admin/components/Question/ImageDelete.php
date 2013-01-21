<?php

require_once 'Inquisition/dataobjects/InquisitionQuestion.php';
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

	// build phase
	// {{{ protected function buildNavBar()

	protected function buildNavBar()
	{
		parent::buildNavBar();

		$this->navbar->createEntry(
			$this->question->inquisition->title,
			sprintf(
				'Inquisition/Details?id=%s',
				$this->question->inquisition->id
			)
		);

		$this->navbar->createEntry(
			sprintf(
				Inquisition::_('Question %s'),
				$this->question->getPosition($this->inquisition)
			),
			sprintf(
				'Question/Details?id=%s',
				$this->question->id
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
}

?>
