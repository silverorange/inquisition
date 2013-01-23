<?php

require_once 'SwatDB/SwatDBRecordsetWrapper.php';
require_once 'SwatDB/SwatDBClassMap.php';
require_once 'Inquisition/dataobjects/InquisitionInquisition.php';

/**
 * A recordset wrapper class for InquisitionInquisition objects
 *
 * @package   Inquisition
 * @copyright 2011-2013 silverorange
 * @see       Inquisition
 */
class InquisitionInquisitionWrapper extends SwatDBRecordsetWrapper
{
	// {{{ public function attachSubRecordset()

	public function attachSubRecordset($name, $wrapper, $binding_field,
		SwatDBRecordsetWrapper $recordset)
	{
		$this->checkDB();

		// assign empty recordsets for all records in this set
		foreach ($this as $record) {
			$empty_recordset = new $wrapper();
			$record->$name = $empty_recordset;
		}

		// split records into separate recordsets for records in this set
		$current_record_id = null;
		$current_recordset = null;
		foreach ($recordset as $record) {
			// Note: this is why we're subclassing this method.
			// SwatDBRecordsetWrapper calls getInternalValue which returns null.
			// There must be a better way to do this, but it works for now.
			$record_id = $record->$binding_field;

			if ($record_id !== $current_record_id) {
				$current_record_id = $record_id;
				$current_recordset = $this[$record_id]->$name;
			}

			$current_recordset->add($record);
		}

		return $recordset;
	}

	// }}}

	// {{{ protected function init()

	protected function init()
	{
		parent::init();

		$this->row_wrapper_class =
			SwatDBClassMap::get('InquisitionInquisition');

		$this->index_field = 'id';
	}

	// }}}
}

?>
