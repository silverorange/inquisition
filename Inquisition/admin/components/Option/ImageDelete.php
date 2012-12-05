<?php

require_once 'Inquisition/dataobjects/InquisitionQuestionOption.php';
require_once 'Inquisition/admin/components/Inquisition/ImageDelete.php';

/**
 * Delete confirmation page for option images
 *
 * @package   Inquisition
 * @copyright 2012 silverorange
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
			$this->option->question->inquisition->title,
			sprintf(
				'Inquisition/Details?id=%s',
				$this->option->question->inquisition->id
			)
		);

		$this->navbar->createEntry(
			sprintf(
				Inquisition::_('Question %s'),
				$this->option->question->position
			),
			sprintf(
				'Question/Details?id=%s',
				$this->option->question->id
			)
		);

		$this->navbar->createEntry(
			sprintf(
				Inquisition::_('Option %s'),
				$this->option->position
			),
			sprintf(
				'Option/Details?id=%s',
				$this->option->id
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
