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
	protected $spl_file;

	// }}}
	// {{{ public function __construct()

	public function __construct($message, $code, SplFileObject $file = null)
	{
		parent::__construct($message, $code);
		$this->spl_file = $file;
	}

	// }}}
	// {{{ public function getSplFile()

	public function getSplFile()
	{
		return $this->spl_file;
	}

	// }}}
}

?>
