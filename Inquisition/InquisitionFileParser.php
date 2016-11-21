<?php

/**
 * @package   Inquisition
 * @copyright 2014-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class InquisitionFileParser implements Iterator
{
	// {{{ protected properties

	/**
	 * @var string
	 */
	protected $filename = '';

	/**
	 * @var integer
	 */
	protected $line = 1;

	/**
	 * @var SplFileObject
	 */
	protected $file;

	// }}}
	// {{{ public function __construct()

	public function __construct($filename)
	{
		$this->filename = $filename;

		$this->file = new SplFileObject($filename, 'r');
		$this->file->setFlags(SplFileObject::READ_CSV);

		$this->defuseBOM();
	}

	// }}}
	// {{{ public function key()

	public function key()
	{
		return $this->file->key();
	}

	// }}}
	// {{{ public function current()

	public function current()
	{
		return $this->file->current();
	}

	// }}}
	// {{{ public function next()

	public function next()
	{
		if (!$this->file->eof()) {
			// count newlines in csv columns
			$this->line += substr_count(implode('', $this->current()), "\n");

			// count next line
			$this->line++;

			$this->file->next();

			// Need to call current to parse next line, otherwise the eof()
			// call will not be valid.
			$current = $this->current();

			// skip blank lines
			while (!$this->file->eof()) {
				if (array_pop($current) === null) {
					$this->line++;
					$this->file->next();
					$current = $this->current();
				} else {
					break;
				}
			}
		}
	}

	// }}}
	// {{{ public function rewind()

	public function rewind()
	{
		$this->file->rewind();
		$this->line = 1;
	}

	// }}}
	// {{{ public function valid()

	public function valid()
	{
		return $this->file->valid();
	}

	// }}}
	// {{{ public function eof()

	public function eof()
	{
		return $this->file->eof();
	}

	// }}}
	// {{{ public function line()

	public function line()
	{
		return $this->line;
	}

	// }}}
	// {{{ public function row()

	public function row()
	{
		return $this->file->key() + 1;
	}

	// }}}
	// {{{ public function getFilename()

	public function getFilename()
	{
		return $this->filename;
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
	protected function defuseBOM()
	{
		$bom = "\xef\xbb\xbf";
		$data = $this->file->current();
		$encoding = '8bit';

		if (mb_strpos($data[0], $bom, 0, $encoding) === 0) {
			$file->fseek(mb_strlen($bom, $encoding));
		}
	}

	// }}}
}

?>
