<?php

require_once 'Admin/pages/AdminObjectEdit.php';
require_once 'Inquisition/InquisitionFileParser.php';
require_once 'Inquisition/InquisitionImporter.php';
require_once 'Inquisition/dataobjects/InquisitionInquisition.php';

/**
 * Inquisition index
 *
 * @package   Inquisition
 * @copyright 2014 silverorange
 */
class InquisitionQuestionImport extends AdminObjectEdit
{
	// {{{ protected properties

	var $imported_questions = 0;

	// }}}

	// {{{ protected function getUiXml()

	protected function getUiXml()
	{
		return 'Inquisition/admin/components/Question/import.xml';
	}

	// }}}
	// {{{ protected function getObjectClass()

	protected function getObjectClass()
	{
		return 'InquisitionInquisition';
	}

	// }}}

	// process phase
	// {{{ protected function validate()

	protected function validate()
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

	// }}}
	// {{{ protected function importInquisition()

	protected function importInquisition($filename)
	{
		$inquisition = $this->getObject();
		$initial_questions_count = count($inquisition->question_bindings);

		try {
			$file = new InquisitionFileParser($filename);
			$importer = new InquisitionImporter($this->app);
			$importer->importInquisition($inquisition, $file);
		} catch (InquisitionImportException $e) {
			$this->ui->getWidget('questions_file')->addMessage(
				new SwatMessage($e->getMessage())
			);
		}

		$final_question_count = count($inquisition->question_bindings);
		$this->questions_imported = $final_question_count -
			$initial_questions_count;
	}

	// }}}
	// {{{ protected function getSavedMessage()

	protected function getSavedMessage()
	{
		$locale = SwatI18NLocale::get();

		return new SwatMessage(
			sprintf(
				Inquisition::ngettext(
					'One question has been imported.',
					'%s questions have been imported.',
					$this->questions_imported
				),
				$locale->formatNumber($this->questions_imported)
			)
		);
	}

	// }}}

	// build phase
	// {{{ protected function buildFrame()

	protected function buildFrame()
	{
		parent::buildFrame();

		$this->ui->getWidget('edit_frame')->title =
			Inquisition::_('Import Questions');
	}

	// }}}
	// {{{ protected function buildNavBar()

	protected function buildNavBar()
	{
		parent::buildNavBar();

		$this->navbar->createEntry(Inquisition::_('Import Questions'));
	}

	// }}}
}

?>
