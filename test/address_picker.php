<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>jquery-addresspicker demo (a jQuery UI widget)</title>
	<script src="http://maps.google.com/maps/api/js?sensor=false"></script>


	<link rel="stylesheet" href="../_ext/jquery-addresspicker/demos/demo.css">
	<link href="../_ext/jquery-ui-1.10.3.custom/css/ui-lightness/jquery-ui-1.10.3.custom.css" rel="stylesheet">
	<script src="../_ext/jquery-ui-1.10.3.custom/js/jquery-1.9.1.js"></script>
	<script src="../_ext/jquery-ui-1.10.3.custom/js/jquery-ui-1.10.3.custom.js"></script>
	<script src="../_ext/jquery-addresspicker/src/jquery.ui.addresspicker.js"></script>
	<script>
		$(function() {
			var addresspicker = $( "#addresspicker" ).addresspicker();

			var addresspickerMap = $( "#addresspicker_map" ).addresspicker({
				regionBias: "fr",
				updateCallback: showCallback,
				elements: {
					map:      "#map",
					lat:      "#lat",
					lng:      "#lng",
					street_number: '#street_number',
					route: '#route',
					locality: '#locality',
					administrative_area_level_2: '#administrative_area_level_2',
					administrative_area_level_1: '#administrative_area_level_1',
					country:  '#country',
					postal_code: '#postal_code',
					type:    '#type'
				}
			});

			var gmarker = addresspickerMap.addresspicker( "marker");
			gmarker.setVisible(true);
			addresspickerMap.addresspicker( "updatePosition");

			$('#reverseGeocode').change(function(){
				$("#addresspicker_map").addresspicker("option", "reverseGeocode", ($(this).val() === 'true'));
			});

			function showCallback(geocodeResult, parsedGeocodeResult){
				$('#callback_result').text(JSON.stringify(parsedGeocodeResult, null, 4));
			}
		});
	</script>
</head>
	<body>
		<div class="demo">
			<div class='input'>
				<label>Address : </label><input id="addresspicker" />
			</div>
			<p></p>
			<div class='clearfix'>
				<div class='input input-positioned'>
					<label>Address : </label> <input id="addresspicker_map" />   <br/>
					<label>street_number: </label> <input id="street_number" disabled=disabled> <br/>
					<label>route : </label> <input id="route" disabled=disabled/>   <br/>
					<label>Postal Code: </label> <input id="postal_code" disabled=disabled> <br/>
					<label>Locality: </label> <input id="locality" disabled=disabled> <br/>
					<label>District: </label> <input id="administrative_area_level_2" disabled=disabled> <br/>
					<label>State/Province: </label> <input id="administrative_area_level_1" disabled=disabled> <br/>
					<label>Country:  </label> <input id="country" disabled=disabled> <br/>
					<label>Lat:      </label> <input id="lat" disabled=disabled> <br/>
					<label>Lng:      </label> <input id="lng" disabled=disabled> <br/>
					<label>Type:     </label> <input id="type" disabled=disabled /> <br/>
				</div>

			<div class='map-wrapper'>
				<label id="geo_label" for="reverseGeocode">Reverse Geocode after Marker Drag?</label>
				<select id="reverseGeocode">
					<option value="false" selected>No</option>
					<option value="true">Yes</option>
				</select><br/>

				<div id="map"></div>
				<div id="legend">You can drag and drop the marker to the correct location</div>
				</div>
			</div>

		</div>
	</body>
</html>
