<?php

/**
 * Inquisition Question Importer
 *
 * @package   Inquisition
 * @copyright 2014-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class InquisitionQuestionImport extends AdminObjectEdit
{


	protected $imported_question_count = 0;





	protected function getUiXml()
	{
		return __DIR__.'/import.xml';
	}




	protected function getObjectClass()
	{
		return 'InquisitionInquisition';
	}



	// process phase


	protected function validate(): void
	{
		parent::validate();

		// Import questions file in validate step so we can show error
		// messages. The importer only modifies the inquisition object and does
		// not save it to the database.
		$questions_file = $this->ui->getWidget('questions_file');
		if ($questions_file->isUploaded()) {
			$this->importInquisition($questions_file->getTempFileName());
		}
	}




	protected function importInquisition($filename)
	{
		$inquisition = $this->getObject();
		$initial_questions_count = count($inquisition->question_bindings);

		try {
			$importer = $this->getImporter();
			$importer->importInquisition(
				$inquisition,
				$this->getFileParser($filename)
			);
		} catch (InquisitionImportException $e) {
			$this->ui->getWidget('questions_file')->addMessage(
				new SwatMessage($e->getMessage())
			);
		}

		$final_question_count = count($inquisition->question_bindings);
		$this->imported_question_count = $final_question_count -
			$initial_questions_count;
	}




	protected function getSavedMessage()
	{
		$locale = SwatI18NLocale::get();

		return new SwatMessage(
			sprintf(
				Inquisition::ngettext(
					'One question has been imported.',
					'%s questions have been imported.',
					$this->imported_question_count
				),
				$locale->formatNumber($this->imported_question_count)
			)
		);
	}




	protected function getFileParser($filename)
	{
		return new InquisitionFileParser($filename);
	}




	protected function getImporter()
	{
		return new InquisitionImporter($this->app);
	}



	// build phase


	protected function buildFrame()
	{
		parent::buildFrame();

		$this->ui->getWidget('edit_frame')->title =
			Inquisition::_('Import Questions');
	}




	protected function buildNavBar()
	{
		parent::buildNavBar();

		$this->navbar->createEntry(Inquisition::_('Import Questions'));
	}


}

?>
