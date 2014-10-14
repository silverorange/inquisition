function InquisitionRadioEntryList(id)
{
	this.id = id;
	this.entries = [];
	this.radios_by_entry = {};
	YAHOO.util.Event.onDOMReady(this.init, this, true);
}

InquisitionRadioEntryList.prototype.init = function()
{
	this.list = document.getElementById(this.id);
	this.radio_buttons = YAHOO.util.Dom.getElementsBy(
		function (el) {
			return (el.type == 'radio');
		},
		'input',
		this.list
	);

	var id_parts, entry_id, entry;
	for (var i = 0; i < this.radio_buttons.length; i++) {
		id_parts = this.radio_buttons[i].id.split('_');
		entry_id = id_parts[0] + '_' + id_parts[1] + '_entry_' + id_parts[2];
		entry = document.getElementById(entry_id);
		if (entry) {
			this.entries.push(entry);
			this.radios_by_entry[entry.id] = this.radio_buttons[i];
		}
	}

	YAHOO.util.Event.on(
		this.radio_buttons,
		'click',
		this.updateEntries,
		this,
		true
	);

	this.updateEntries();
};

InquisitionRadioEntryList.prototype.updateEntries = function()
{
	var radio, entry;
	for (var i = 0; i < this.entries.length; i++) {
		entry = this.entries[i];
		radio = this.radios_by_entry[entry.id];
		if (radio.checked) {
			YAHOO.util.Dom.removeClass(entry, 'swat-insensitive');
			entry.disabled = false;
		} else {
			YAHOO.util.Dom.addClass(entry, 'swat-insensitive');
			entry.disabled = true;
		}
	}
};
