<?php

require_once 'Admin/pages/AdminObjectEdit.php';
require_once 'Inquisition/InquisitionFileParser.php';
require_once 'Inquisition/InquisitionImporter.php';
require_once 'Inquisition/dataobjects/InquisitionInquisition.php';

/**
 * Inquisition Question Importer
 *
 * @package   Inquisition
 * @copyright 2014-2015 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class InquisitionQuestionImport extends AdminObjectEdit
{
	// {{{ protected properties

	protected $imported_question_count = 0;

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
					$this->imported_question_count
				),
				$locale->formatNumber($this->imported_question_count)
			)
		);
	}

	// }}}
	// {{{ protected function getFileParser()

	protected function getFileParser($filename)
	{
		return new InquisitionFileParser($filename);
	}

	// }}}
	// {{{ protected function getImporter()

	protected function getImporter()
	{
		return new InquisitionImporter($this->app);
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
