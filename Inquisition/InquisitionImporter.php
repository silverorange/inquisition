<?php

require_once 'Inquisition/exceptions/InquisitionImportException.php';
require_once 'Inquisition/dataobjects/InquisitionInquisition.php';
require_once 'Inquisition/dataobjects/InquisitionQuestion.php';
require_once 'Inquisition/dataobjects/InquisitionInquisitionQuestionBinding.php';
require_once 'Inquisition/dataobjects/InquisitionQestionOption.php';

/**
 * @package   Inquisition
 * @copyright 2014 silverorange
 */
class InquisitionImporter
{
	// inquisition
	// {{{ public function importInquisition()

	public function importInquisition(InquisitionInquisition $inquisition,
		$filename)
	{
		$file = new SplFileObject($filename, 'r');
		$file->setFlags($this->getFileFlags());

		$this->defuseBOM($file);

		$this->importInquisitionProperties($inquisition, $file);
		$this->importQuestions($inquisition, $file);
	}

	// }}}
	// {{{ protected function importInquisitionProperties()

	protected function importInquisitionProperties(
		InquisitionInquisition $inquisition, SplFileObject $file)
	{
	}

	// }}}

	// questions
	// {{{ protected function importQuestions()

	protected function importQuestions(
		InquisitionInquisition $inquisition, SplFileObject $file)
	{
		while (!$file->eof()) {
			$question_class = SwatDBClassMap::get('InquisitionQuestion');

			$question = new $question_class();
			$question->setDatabase($this->db);
			$this->importQuestion($question, $file);

			$binding_class = SwatDBClassMap::get(
				'InquisitionInquisitionQuestionBinding'
			);

			$binding = new $binding_class();
			$binding->setDatabase($this->db);

			$binding->question = $question;
			$binding->inquisition = $inquisition;

			$previous_binding = $this->question_bindings->getLast();

			if ($previous_binding instanceof $binding_class) {
				$binding->displayorder = $previous_binding->displayorder + 1;
			} else {
				$binding->displayorder = 1;
			}

			$this->question_bindings->add($binding);
		}
	}

	// }}}
	// {{{ protected function importQuestion()

	protected function importQuestion(InquisitionQuestion $question,
		SplFileObject $file)
	{
		$num = $file->key() + 1;

		$this->importQuestionProperties($question, $file);
		$this->importOptions($question, $file);

		if (count($question->options) < 2) {
			throw new InquisitionImportException(
				sprintf(
					Inquisition::_(
						'Question on line %s must have at least two options.'
					),
					$num
				),
				0,
				$file
			);
		}

		if ($this->correct_option === null) {
			throw new InquisitionImportException(
				sprintf(
					Inquisition::_(
						'Question on line %s must have a correct answer.',
						$num
					)
				),
				0,
				$file
			);
		}
	}

	// }}}
	// {{{ protected function importQuestionProperties()

	protected function importQuestionProperties(InquisitionQuestion $question,
		SplFileObject $file)
	{
		$num = $file->key() + 1;
		$row = $file->current();

		$question->required = true;
		$question->question_type = InquisitionQuestion::TYPE_RADIO_LIST;

		if (!isset($row[0]) || $row[0] == '') {
			throw new InquisitionImportException(
				sprintf(
					Inquisition::_('Line %s has no question text.'),
					$num
				),
				0,
				$file
			);
		}

		$question->bodytext = $row[0];
	}

	// }}}

	// question options
	// {{{ protected function importOptions()

	protected function importOptions(InquisitionQuestion $question,
		SplFileObject $file)
	{
		$file->next();

		while (!$file->eof() && $this->isOptionLine($file)) {
			$option_class = SwatDBClassMap::get('InquisitionQuestionOption');

			$option = new $option_class();
			$option->setDatabase($this->db);
			$this->importOption($option, $file);

			$previous_option = $question->options->getLast();

			if ($previous_option instanceof $option_class) {
				$option->displayorder = $previous_option->displayorder + 1;
			} else {
				$option->displayorder = 1;
			}

			$question->options->add($option);

			if ($this->isCorrectOptionLine($file)) {
				$num = $file->key() + 1;

				if ($question->correct_option instanceof $option_class) {
					throw new InquisitionImportException(
						sprintf(
							Inquisition::_(
								'Line %s contains a second correct answer.',
								$num
							)
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

	protected function importOption(InquisitionOption $option,
		SplFileObject $file)
	{
		$num = $file->key() + 1;
		$row = $file->current();

		if (!isset($row[1]) || $row[1] == '') {
			throw new InquisitionImportException(
				sprintf(
					Rap::_('Line %s has no option text.'),
					$num
				),
				0,
				$file
			);
		}

		$option->title = $row[1];
	}

	// }}}

	// helper methods
	// {{{ protected function isOptionLine()

	protected function isOptionLine(SplFileObject $file)
	{
		$line = $file->current();

		return (isset($line[0]) && $line[0] === '');
	}

	// }}}
	// {{{ protected function isCorrectOptionLine()

	protected function isCorrectOptionLine(SplFileObject $file)
	{
		$line = $file->current();

		$marker = '';

		if (isset($line[2])) {
			$marker = strtolower(trim($line[2]));
		}

		return ($marker === 'x');
	}

	// }}}
	// {{{ protected function getFileFlags()

	protected function getFileFlags()
	{
		$flags  = SplFileObject::DROP_NEW_LINE;
		$flags |= SplFileObject::SKIP_EMPTY;
		$flags |= SplFileObject::READ_CSV;

		return $flags;
	}

	// }}}
	// {{{ protected function defuseBOM()

	/**
	 * Seeks ahead of the byte-order-mark if it exists in the file
	 *
	 * If there is a BOM at the start of the current line, then move the file
	 * pointer past the BOM. This will cause subsequent calls to
	 * $file->current() to skip the BOM.
	 *
	 * @param SplFileObject $file
	 */
	protected function defuseBOM(SplFileObject $file)
	{
		$bom = "\xef\xbb\xbf";
		$line = $file->current();
		$encoding = '8bit';

		if (mb_strpos($line[0], $bom, 0, $encoding) === 0) {
			$file->fseek(mb_strlen($bom, $encoding));
		}
	}

	// }}}
}

?>
