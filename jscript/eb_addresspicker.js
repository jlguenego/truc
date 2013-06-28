/*
 *
 */

$.widget("ui.eb_addresspicker", $.ui.addresspicker, {

	isValid: true,

    syncAddress: function(address) {
    	var self = this;
    	var request = {term: address};
    	var response = function(result) {
    		if (!result[0]) {
    			self._setValid(false);
    			return;
    		}
    		self._setValid(true);
    		var ui = {};
    		ui.item = result[0];
    		self._focusAddress(null, ui);
    	};
      	this._geocode(request, response);

      	this.element.keyup(function() {
      		self._setValid(false);
      	});
  	},

  	_selectAddress: function(event, ui) {
		this.selectedResult = ui.item;
		if (this.options.updateCallback) {
			this.options.updateCallback(this.selectedResult, this._parseGeocodeResult(this.selectedResult));
		}
		this._setValid(true);
	},

	_setValid: function(bool) {
		if (this.isValid != bool) {
			if (bool) {
				this._trigger('valid');
			} else {
				this._trigger('invalid');
			}
		}
		this.isValid = bool;
	}
});