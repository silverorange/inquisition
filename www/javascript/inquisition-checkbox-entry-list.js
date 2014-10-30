function InquisitionCheckboxEntryList(id)
{
	this.id = id;
	this.entries = [];
	this.checkboxs_by_entry = {};
	YAHOO.util.Event.onDOMReady(this.init, this, true);
}

InquisitionCheckboxEntryList.prototype.init = function()
{
	this.list = document.getElementById(this.id);
	this.checkboxes = YAHOO.util.Dom.getElementsBy(
		function (el) {
			return (el.type == 'checkbox');
		},
		'input',
		this.list
	);

	var id_parts, entry_id, entry;
	for (var i = 0; i < this.checkboxes.length; i++) {
		id_parts = this.checkboxes[i].id.split('_');
		entry_id = id_parts[0] + '_' + id_parts[1] + '_entry_' + id_parts[2];
		entry = document.getElementById(entry_id);
		if (entry) {
			this.entries.push(entry);
			this.checkboxs_by_entry[entry.id] = this.checkboxes[i];
		}
	}

	YAHOO.util.Event.on(
		this.checkboxes,
		'click',
		this.updateEntries,
		this,
		true
	);

	this.updateEntries();
};

InquisitionCheckboxEntryList.prototype.updateEntries = function()
{
	var checkbox, entry;
	for (var i = 0; i < this.entries.length; i++) {
		entry = this.entries[i];
		checkbox = this.checkboxs_by_entry[entry.id];
		if (checkbox.checked) {
			YAHOO.util.Dom.removeClass(entry, 'swat-insensitive');
			entry.disabled = false;
		} else {
			YAHOO.util.Dom.addClass(entry, 'swat-insensitive');
			entry.disabled = true;
		}
	}
};
