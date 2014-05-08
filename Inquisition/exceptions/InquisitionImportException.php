<?php

/**
 * @package   Inquisition
 * @copyright 2014 silverorange
 */
class InquisitionImportException extends Exception
{
	// {{{ protected properties

	/**
	 * @var SplFileObject
	 */
	protected $file;

	// }}}
	// {{{ public function __construct()

	public function __construct($message, $code, SplFileObject $file = null)
	{
		parent::__construct($message, $code);
		$this->file = $file;
	}

	// }}}
	// {{{ public function getSplFile()

	public function getSplFile()
	{
		return $this->file;
	}

	// }}}
}

?>
