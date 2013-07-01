/*!
 * jQuery UI textarea_addresspicker 1.10.3
 * http://jqueryui.com
 *
 * Copyright 2013 jQuery Foundation and other contributors
 * Released under the MIT license.
 * http://jquery.org/license
 *
 *
 * Depends:
 *   jquery.ui.core.js
 *   jquery.ui.widget.js
 */
(function( $, undefined ) {

	$.widget("ui.textarea_addresspicker", {
		version: "1.10.3",
		options: {
			showBlockMap: true,
			appendAddressString: "",
			draggableMarker: true,
        	regionBias: null,
			mapOptions: {
				zoom: 5,
				center: new google.maps.LatLng(46, 2),
				scrollwheel: false,
				mapTypeId: google.maps.MapTypeId.ROADMAP
			},
	        elements: {
	        	map_block: false,
				map: false,
				lat: false,
				lng: false,
				street_number: false,
				route: false,
				locality: false,
				administrative_area_level_2: false,
				administrative_area_level_1: false,
				country: false,
				postal_code: false,
				type: false
	        }
		},

		_addressParts: {
			street_number: null,
			route: null,
			locality: null,
			administrative_area_level_2: null,
			administrative_area_level_1: null,
			country: null,
			postal_code:null,
			type: null
		},

		_create: function() {
			var self = this;
			this.geocoder = new google.maps.Geocoder();

			this._initBlockMap();

			this.lat                         = $(this.options.elements.lat);
			this.lng                         = $(this.options.elements.lng);
			this.street_number               = $(this.options.elements.street_number);
			this.route                       = $(this.options.elements.route);
			this.locality                    = $(this.options.elements.locality);
			this.administrative_area_level_2 = $(this.options.elements.administrative_area_level_2);
			this.administrative_area_level_1 = $(this.options.elements.administrative_area_level_1);
			this.country                     = $(this.options.elements.country);
			this.postal_code                 = $(this.options.elements.postal_code);
			this.type                        = $(this.options.elements.type);
			this.map_block                   = $(this.options.elements.map_block);
			if (this.options.elements.map) {
				this.mapElement = $(this.options.elements.map);
				this._initMap();
			}

			this.element.attr('rows', '4');

			this._on(this.element, {
				keyup: function(event) {
					this.syncAddress();
				},

				focusin:  function(event) {
					if (!this.options.showBlockMap) {
						return;
					}
					this.map_block.offset({top: this.top});
				},

				focusout:  function(event) {
					if (!this.options.showBlockMap) {
						return;
					}

					this.map_block.offset({top: -2000});
				}
			});

			var gmarker = this.marker();
			gmarker.setVisible(true);

			this.syncAddress();
		},

		_initBlockMap: function() {
			var fields = [
				{ label: 'Street Number', name: 'street_number' },
				{ label: 'Route', name: 'route' },
				{ label: 'Postal Code', name: 'postal_code' },
				{ label: 'Locality', name: 'locality' },
				{ label: 'District', name: 'administrative_area_level_2' },
				{ label: 'State/Province', name: 'administrative_area_level_1' },
				{ label: 'Country', name: 'country' },
				{ label: 'Lat', name: 'lat' },
				{ label: 'Lng', name: 'lng' },
			];

			var textarea = this.element;
			var name = textarea.attr('name');
			var left = textarea.offset().left + textarea.width() + 10;
			this.top = textarea.offset().top;
			var top = this.top;
			var content = '	<div id="' + name + '_eb_map_block" class="eb_map_block">\
								<table>\
									<tr>\
										<td>\
								<table class="ap_input_positioned">';
			for (i in fields) {
				var field = fields[i];
				content += '<tr><th>' + field.label + '</th>' +
					'<td><input type="text" id="' + name + '_' + field.name + '" name="' + name + '_' + field.name + '" readonly /></td></tr>';
			}

			content += '		</table>\
										</td>\
										<td>\
								<div class="ap_map_wrapper">\
									<div id="' + name + '_map" class="ap_map"></div>\
									</div>\
								</div>\
										</td>\
									</tr>\
								</table>\
							</div>';
			textarea.after(content);
			$('#' + name + '_eb_map_block').offset({top: -2000, left: left});

			this.options.elements.map_block = '#' + name + '_eb_map_block';
			this.options.elements.map = '#' + name + '_map';
			this.options.elements.lat = '#' + name + '_lat';
			this.options.elements.lng = '#' + name + '_lng';
			this.options.elements.street_number = '#' + name + '_street_number';
			this.options.elements.route = '#' + name + '_route';
			this.options.elements.locality = '#' + name + '_locality';
			this.options.elements.administrative_area_level_2 = '#' + name + '_administrative_area_level_2';
			this.options.elements.administrative_area_level_1 = '#' + name + '_administrative_area_level_1';
			this.options.elements.country = '#' + name + '_country';
			this.options.elements.postal_code = '#' + name + '_postal_code';
			this.options.elements.type = '#' + name + '_type';

			this.options.regionBias = "fr";
		},

		_initMap: function() {
			if (this.lat && this.lat.val()) {
				this.options.mapOptions.center = new google.maps.LatLng(this.lat.val(), this.lng.val());
			}

			this.gmap = new google.maps.Map(this.mapElement[0], this.options.mapOptions);
			this.gmarker = new google.maps.Marker({
				position: this.options.mapOptions.center,
				map:this.gmap,
				draggable: this.options.draggableMarker
			});
			google.maps.event.addListener(this.gmarker, 'dragend', $.proxy(this._markerMoved, this));
			this.gmarker.setVisible(false);
		},

		_markerMoved: function() {
			this._updatePosition(this.gmarker.getPosition());
		},

		_updatePosition: function(location) {
			if (this.lat) {
				this.lat.val(location.lat());
			}
			if (this.lng) {
				this.lng.val(location.lng());
			}
		},

		_geocode: function(request, response) {
			var address = request.term;
			var self = this;
			this.geocoder.geocode({
				'address': address + this.options.appendAddressString,
				'region': this.options.regionBias
			}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK && results) {
					for (var i = 0; i < results.length; i++) {
					results[i].label =  results[i].formatted_address;
					};
				}
				response(results);
			})
		},

		_focusAddress: function(event, ui) {
			var address = ui.item;
			if (!address) {
				return;
			}

			if (this.gmarker) {
				this.gmarker.setPosition(address.geometry.location);
				this.gmarker.setVisible(true);
				this.gmap.fitBounds(address.geometry.viewport);
			}

			this._updatePosition(address.geometry.location);
			this._updateAddressParts(address);
		},

		_updateAddressParts: function(geocodeResult){
			parsedResult = this._parseGeocodeResult(geocodeResult);
			for (addressPart in this._addressParts){
				if (this[addressPart]){
					this[addressPart].val(parsedResult[addressPart]);
				}
			}
		},

		_resetAddressParts: function() {
			for (addressPart in this._addressParts){
				if (this[addressPart]){
					this[addressPart].val(null);
				}
			}
			this.lat.val(null);
			this.lng.val(null);
		},

		_parseGeocodeResult: function(geocodeResult){
			var parsed = {
				lat: geocodeResult.geometry.location.lat(),
				lng: geocodeResult.geometry.location.lng()
			};

			for (var addressPart in this._addressParts){
				parsed[addressPart] = this._findInfo(geocodeResult, addressPart);
			}

			parsed.type = geocodeResult.types[0];
			return parsed;
		},

		_findInfo: function(result, type) {
			for (var i = 0; i < result.address_components.length; i++) {
				var component = result.address_components[i];
				if (component.types.indexOf(type) != -1) {
					return component.long_name;
				}
			}
			return false;
		},

		syncAddress: function() {
			address = this.element.val();
			if (address == "") {
				this._resetAddressParts();
				return;
			}
	    	var self = this;
	    	var request = {term: address};
	    	var response = function(result) {
	    		if (!result || !result[0]) {
	    			return;
	    		}
	    		var ui = {};
	    		ui.item = result[0];
	    		self._focusAddress(null, ui);
	    	};
	      	this._geocode(request, response);
	  	},

		marker: function() {
			return this.gmarker;
		}
	});
})( jQuery );
