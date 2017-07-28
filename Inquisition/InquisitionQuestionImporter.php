<?php

/**
 * @package   Inquisition
 * @copyright 2014-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class InquisitionQuestionImporter
{
	// {{{ protected properties

	/**
	 * @var SiteApplication
	 */
	protected $app;

	// }}}
	// {{{ public function __construct()

	public function __construct(SiteApplication $app)
	{
		$this->app = $app;
	}

	// }}}

	// questions
	// {{{ public function importQuestions()

	public function importQuestions(InquisitionFileParser $file)
	{
		$questions = array();

		while (!$file->eof()) {
			$question_class = SwatDBClassMap::get('InquisitionQuestion');

			$question = new $question_class();
			$question->setDatabase($this->app->db);
			$this->importQuestion($question, $file);

			$questions[] = $question;
		}

		return $questions;
	}

	// }}}
	// {{{ protected function importQuestion()

	protected function importQuestion(
		InquisitionQuestion $question,
		InquisitionFileParser $file
	) {
		$line = $file->line();
		$row  = $file->row();

		$this->importQuestionProperties($question, $file);
		$this->importOptions($question, $file);

		if (count($question->options) < 2) {
			throw new InquisitionImportException(
				sprintf(
					Inquisition::_(
						'Question on line %s (CSV row %s) must have at '.
						'least two options.'
					),
					$line,
					$row
				),
				0,
				$file
			);
		}

		if (!$question->correct_option instanceof InquisitionQuestionOption) {
			throw new InquisitionImportException(
				sprintf(
					Inquisition::_(
						'Question on line %s (CSV row %s) must have a '.
						'correct answer.'
					),
					$line,
					$row
				),
				0,
				$file
			);
		}
	}

	// }}}
	// {{{ protected function importQuestionProperties()

	protected function importQuestionProperties(
		InquisitionQuestion $question,
		InquisitionFileParser $file
	) {
		$line = $file->line();
		$row  = $file->row();
		$data = $file->current();

		$question->required = true;
		$question->question_type = InquisitionQuestion::TYPE_RADIO_LIST;

		if (!isset($data[0]) || $data[0] == '') {
			throw new InquisitionImportException(
				sprintf(
					Inquisition::_(
						'Line %s (CSV row %s) has no question text.'
					),
					$line,
					$row
				),
				0,
				$file
			);
		}

		$question->bodytext = $data[0];
	}

	// }}}

	// question options
	// {{{ protected function importOptions()

	protected function importOptions(
		InquisitionQuestion $question,
		InquisitionFileParser $file
	) {
		$file->next();

		while (!$file->eof() && $this->isOptionLine($file)) {
			$option_class = SwatDBClassMap::get('InquisitionQuestionOption');

			$option = new $option_class();
			$option->setDatabase($this->app->db);
			$this->importOption($option, $file);

			$previous_option = $question->options->getLast();

			if ($previous_option instanceof $option_class) {
				$option->displayorder = $previous_option->displayorder + 1;
			} else {
				$option->displayorder = 1;
			}

			$question->options->add($option);

			if ($this->isCorrectOptionLine($file)) {
				$line = $file->line();
				$row  = $file->row();

				if ($question->correct_option instanceof $option_class) {
					throw new InquisitionImportException(
						sprintf(
							Inquisition::_(
								'Line %s (CSV row %s) contains a second '.
								'correct answer.'
							),
							$line,
							$row
						),
						0,
						$file
					);
				}

				$question->correct_option = $option;
			}

			$file->next();
		}
	}

	// }}}
	// {{{ protected function importOption()

	protected function importOption(
		InquisitionQuestionOption $option,
		InquisitionFileParser $file
	) {
		$line = $file->line();
		$row  = $file->row();
		$data = $file->current();

		if (!isset($data[1]) || $data[1] == '') {
			throw new InquisitionImportException(
				sprintf(
					Inquisition::_('Line %s (CSV row %s) has no option text.'),
					$line,
					$row
				),
				0,
				$file
			);
		}

		$option->title = $data[1];
	}

	// }}}

	// helper methods
	// {{{ protected function isOptionLine()

	protected function isOptionLine(InquisitionFileParser $file)
	{
		$data = $file->current();
		return (isset($data[0]) && $data[0] === '');
	}

	// }}}
	// {{{ protected function isCorrectOptionLine()

	protected function isCorrectOptionLine(InquisitionFileParser $file)
	{
		$data = $file->current();
		return (isset($data[2]) && strtolower(trim($data[2])) === 'x');
	}

	// }}}
}

?>
