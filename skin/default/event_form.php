<?php
	$event = $g_display["event"];
	$rates = $g_display["rates"];
?>
<div id="error_msg" style="color:red;">
	<?php echo "$g_error_msg<br/>"; ?>
</div>
<form name="input" action="<?php echo $g_display["form_action"]; ?>" method="POST">
	<table>
		<tr>
			<td>Title: </td>
			<td><input type="text" name="title" value="<?php echo_default_value("title", $event->title); ?>" title="toto"/></td>
			<td class="help">The name of your event.</td>
		</tr>
		<tr>
			<td>Required funding: </td>
			<td><input type="number" name="funding_needed" value="<?php echo_default_value("funding_needed", $event->funding_needed); ?>"/></td>
			<td class="help">Minimum funding wanted in Euro.</td>
		</tr>
		<tr>
			<td>Event date: </td>
			<td><input type="text" name="happening_t" id="happening_t"
				value="<?php echo_default_value("happening_t", $event->happening_t); ?>"></td>
			<td class="help">The date of your event (yyyy-mm-dd).</td>
		</tr>
		<tr>
			<td>Confirmation date:</td>
			<td><input type="text" name="confirmation_t" id="confirmation_t"
					value="<?php echo_default_value("confirmation_t", $event->confirmation_t); ?>"/></td>
			<td class="help">Deadline for validation (yyyy-mm-dd).</td>
		</tr>
		<tr>
			<td>Participation open date: </td>
			<td><input type="text" name="open_t" id="open_t"
				value="<?php echo_default_value("open_t", $event->open_t); ?>"></td>
			<td class="help">The date of your event (yyyy-mm-dd).</td>
		</tr>
		<tr>
			<td>Location: </td>
			<td>
				<input type="text" name="location"
					value="<?php echo_default_value("location", $event->location) ?>"/>
			</td>
			<td class="help">Where this event will happend.</td>
		</tr>
		<tr>
			<td>Link:</td>
			<td>
				<input type="text" name="link"
					value="<?php echo_default_value("link", $event->link) ?>"/>
			</td>
			<td class="help">Link to the event page, if any.</td>
		</tr>
	</table>
	<table>
		<tr>
			<td>Short description: </td>
			<td>
				<textarea name="short_description" maxlength="255"
					title="Up to 255 characters"
					style="width: 500px; height: 100; resize: none;"><?php echo_default_value("short_description", $event->short_description) ?></textarea>
			</td>
			<td class="help">Give a short description of your event.</td>
		</tr>
		<tr>
			<td>Long description: </td>
			<td>
				<textarea name="long_description" maxlength="1000"
					title="Up to 1000 characters"
					style="width: 500; height: 450; resize: none;"><?php echo_default_value("long_description", $event->long_description); ?></textarea>
			</td>
			<td class="help">Give a complete description of your event.</td>
		</tr>
	</table>
	<br/>
	<input type="checkbox" name="nominative" checked/>This event is nominative.<br/>
	<div id="rates">
	</div>
	<a href="JavaScript:addRate('rates');">Add another rate</a><br/>
	<input type="hidden" name="id" value="<?php echo $event->id; ?>"/>
	<input type="submit" value="Submit"/>
</form>
<script>
	function update_form() {
		console.log("update_form");
		console.log("date=" + $("#happening_t").val());

		if ($("#happening_t").val() == "") {
			$("#confirmation_t").val("");
			$( "#confirmation_t" ).attr("disabled", "");
		} else {
			$( "#confirmation_t" ).removeAttr("disabled");
		}

		if ($("#confirmation_t").val() == "") {
			$("#open_t").val("");
			$( "#open_t" ).attr("disabled", "");
		} else {
			$( "#open_t" ).removeAttr("disabled");
		}

		$( "#open_t" ).datepicker('option', 'maxDate', $("#confirmation_t").val());
		var date = $("#confirmation_t").datepicker('getDate');
		if (date) {
			date.setDate(date.getDate() - 29);
			var today = new Date();
			if (date < today) {
				date = today;
			}
		}
		$( "#open_t" ).datepicker('option', 'minDate', date);
		$( "#confirmation_t" ).datepicker('option', 'maxDate', $("#happening_t").val());

		$( "#happening_t" ).datepicker({ minDate: "+0d", dateFormat: "yy-mm-dd"});
		$( "#confirmation_t" ).datepicker({ minDate: "+0d", dateFormat: "yy-mm-dd"});
		$( "#open_t" ).datepicker({ minDate: "+0d", dateFormat: "yy-mm-dd"});
	}

	$("form").change(update_form);
	$(document).ready(update_form);


	// Combobox for the tax rates.
	taxes = new Array(
	<?php
		$is_first = TRUE;
		foreach ($g_tax_rates as $name => $rate) {
			if ($is_first) {
				$is_first = FALSE;
			} else {
				echo ',';
			}
			echo "new Array('${name}', '${rate}')";
		}
	?>
	);
<?php
	$i = 0;
	if ($rates != NULL) {
		foreach ($rates as $rate) {
			$label = $rate["label"];
			$amount = $rate["amount"];
			echo "addRate('rates', '$label', '$amount');";
			$i++;
		}
	}
?>
	setCounter(<?php echo $i; ?>);
	if (getCounter() < 1) {
		console.log("No rate.");
		addRate('rates');
	}
</script>
