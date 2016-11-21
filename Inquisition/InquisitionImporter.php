<?php

require_once 'Site/SiteApplication.php';
require_once 'Inquisition/InquisitionFileParser.php';
require_once 'Inquisition/dataobjects/InquisitionInquisition.php';
require_once 'Inquisition/dataobjects/InquisitionInquisitionQuestionBinding.php';
require_once 'Inquisition/InquisitionQuestionImporter.php';

/**
 * @package   Inquisition
 * @copyright 2014-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class InquisitionImporter
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

	// inquisition
	// {{{ public function importInquisition()

	public function importInquisition(InquisitionInquisition $inquisition,
		InquisitionFileParser $file)
	{
		$this->importInquisitionProperties($inquisition, $file);
		$this->importQuestions($inquisition, $file);
	}

	// }}}
	// {{{ protected function importInquisitionProperties()

	protected function importInquisitionProperties(
		InquisitionInquisition $inquisition, InquisitionFileParser $file)
	{
	}

	// }}}

	// questions
	// {{{ protected function importQuestions()

	protected function importQuestions(
		InquisitionInquisition $inquisition, InquisitionFileParser $file)
	{
		$importer = $this->getQuestionImporter();
		$questions = $importer->importQuestions($file);

		foreach ($questions as $question) {
			$binding_class = SwatDBClassMap::get(
				'InquisitionInquisitionQuestionBinding'
			);

			$binding = new $binding_class();
			$binding->setDatabase($this->app->db);

			$binding->question = $question;
			$binding->inquisition = $inquisition;

			$previous_binding = $inquisition->question_bindings->getLast();

			if ($previous_binding instanceof $binding_class) {
				$binding->displayorder = $previous_binding->displayorder + 1;
			} else {
				$binding->displayorder = 1;
			}

			$inquisition->question_bindings->add($binding);
		}
	}

	// }}}
	// {{{ protected function getQuestionImporter()

	protected function getQuestionImporter()
	{
		return new InquisitionQuestionImporter($this->app);
	}

	// }}}
}

?>
