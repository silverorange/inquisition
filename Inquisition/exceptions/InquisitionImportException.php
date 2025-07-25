<?php

/**
 * @package   Inquisition
 * @copyright 2014-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class InquisitionImportException extends Exception
{


	/**
	 * @var InquisitionFileParser
	 */
	protected $file_parser;




	public function __construct(
		$message,
		$code,
		InquisitionFileParser $file_parser = null
	) {
		parent::__construct($message, $code);
		$this->file_parser = $file_parser;
	}




	public function getFileParser()
	{
		return $this->file_parser;
	}


}

?>
