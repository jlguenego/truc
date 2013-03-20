<?php
	$event = $g_display["event"];
	$rates = $g_display["rates"];
?>
<script type="text/javascript">
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
</script>

<a href="index.php">Go back to index</a><br/><br/>
<span style="color:red;">
	<?php echo "$g_error_msg<br/>"; ?>
</span>
<form name="input" action="?action=update&amp;type=event&amp;id=<?php echo $event->id; ?>" method="POST">
	<table>
		<tr>
			<td>Title: </td>
			<td><input type="text" name="title" value="<?php echo $event->title; ?>"></td>
			<td>
				<span class="help">The name of your event.</span>
			</td>
		</tr>
		<tr>
			<td>Required funding: </td>
			<td><input type="text" name="funding_needed" value="<?php echo $event->funding_needed; ?>"></td>
			<td>
				<span class="help">Minimum funding wanted in Euro.</span>
			</td>
		</tr>
		<tr>
			<td>Event date: </td>
			<td><input type="text" name="happening_t" id="happening_t"
				value="<?php echo $event->happening_t; ?>"></td>
			<td>
				<span class="help">The date of your event (yyyy-mm-dd).</span>
			</td>
		</tr>
		<tr>
			<td>Confirmation date: </td>
			<td><input type="text" name="confirmation_t" id="confirmation_t"
				value="<?php echo $event->confirmation_t; ?>"></td>
			<td>
				<span class="help">Deadline for validation (yyyy-mm-dd).</span>
			</td>
		</tr>
		<tr>
			<td>Participation open date: </td>
			<td><input type="text" name="open_t" id="open_t"
				value="<?php echo_default_value("happening_t", $event->confirmation_t); ?>"></td>
			<td>
				<span class="help">The date of your event (yyyy-mm-dd).</span>
			</td>
		</tr>
		<tr>
			<td>Location: </td>
			<td>
				<input type="text" name="location"
					value="<?php echo $event->location ?>"/>
			</td>
			<td>
				<span class="help">Where this event will happend.</span>
			</td>
		</tr>
		<tr>
			<td>Link:</td>
			<td>
				<input type="text" name="link"
					value="<?php echo $event->link ?>"/>
			</td>
			<td>
				<span class="help">Link to the event page, if any.</span>
			</td>
		</tr>
	</table>
	<table>
		<tr>
			<td>Short description: </td>
			<td>
				<textarea name="short_description" maxlength="255"
					title="Up to 255 characters"
					style="width: 500px; height: 100; resize: none;">
<?php echo $event->short_description ?></textarea>
			</td>
			<td>
				<span class="help">Give a short description of your event.</span>
			</td>
		</tr>
		<tr>
			<td>Long description: </td>
			<td>
				<textarea name="long_description" maxlength="1000"
					title="Up to 1000 characters"
					style="width: 500; height: 450; resize: none;">
<?php echo $event->long_description; ?></textarea>
			</td>
			<td>
				<span class="help">Give a complete description of your event.</span>
			</td>
		</tr>
	</table>
	<div id="rates">
	</div>
<?php
	$i = 0;
	foreach ($rates as $rate) {
		$label = $rate["label"];
		$amount = $rate["amount"];
		echo "<script language=\"javascript\" type=\"text/javascript\">";
		echo "addRate('rates', '$label', '$amount');";
		echo "</script>";
		$i++;
	}
	echo "<script language=\"javascript\" type=\"text/javascript\">";
	echo "setCounter($i);";
	echo "</script>";
?>
	<input type="button" value="Add a rate" onClick="addRate('rates');">
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
</script>