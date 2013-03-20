<span style="color:red;">
	<?php echo "$g_error_msg<br/>"; ?>
</span>

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

<form name="input" action="?action=create&amp;type=event" method="POST" id="form">
	<table>
		<tr>
			<td>Title: </td>
			<td><input type="text" name="title" value="<?php echo_default_value("title"); ?>" title="toto"/></td>
			<td>
				<span class="help">The name of your event.</span>
			</td>
		</tr>
		<tr>
			<td>Required funding: </td>
			<td><input type="number" name="funding_needed" value="<?php echo_default_value("funding_needed"); ?>"/></td>
			<td>
				<span class="help">Minimum funding wanted in Euro.</span>
			</td>
		</tr>
		<tr>
			<td>Event date: </td>
			<td><input type="text" name="happening_t" id="happening_t"
				value="<?php echo_default_value("happening_t"); ?>"></td>
			<td>
				<span class="help">The date of your event (yyyy-mm-dd).</span>
			</td>
		</tr>
		<tr>
			<td>Confirmation date:</td>
			<td><input type="text" name="confirmation_t" id="confirmation_t"
					value="<?php echo_default_value("confirmation_t"); ?>"/></td>
			<td>
				<span class="help">Deadline for validation (yyyy-mm-dd).</span>
			</td>
		</tr>
		<tr>
			<td>Location: </td>
			<td>
				<input type="text" name="location"
					value="<?php echo_default_value("location") ?>"/>
			</td>
			<td>
				<span class="help">Where this event will happend.</span>
			</td>
		</tr>
		<tr>
			<td>Link:</td>
			<td>
				<input type="text" name="link"
					value="<?php echo_default_value("link", "http://") ?>"/>
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
<?php echo_default_value("short_description") ?></textarea>
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
<?php echo_default_value("long_description"); ?></textarea>
			</td>
			<td>
				<span class="help">Give a complete description of your event.</span>
			</td>
		</tr>
	</table>
	<br/><br/>
	<input type="checkbox" name="nominative" />This event is nominative.<br/>
	<div id="rates">
	</div>
<?php
	if (isset($_GET["rates"])) {
		$i = 0;

		foreach ($_GET["rates"] as $rate) {
			$label = $_GET["labels"][$i];
			$amount = $rate;
			echo "<script language=\"javascript\" type=\"text/javascript\">";
			echo "addRate('rates', '$label', '$amount');";
			echo "</script>";
			$i++;
		}
		echo "<script language=\"javascript\" type=\"text/javascript\">";
		echo "setCounter($i);";
		echo "</script>";
	} else {
		echo "<script language=\"javascript\" type=\"text/javascript\">";
		echo "addRate('rates', '', '');";
		echo "</script>";
	}
?>
	<a href="JavaScript:addRate('rates');">Add another rate</a><br/>
	<input type="submit" value="Submit"/>
</form>
<script>
	function update_form() {
		console.log("update_form");
		console.log("date=" + $("#happening_t").val());
		var max_date = $("#happening_t").val();
		$( "#confirmation_t" ).datepicker({ minDate: "+0d", dateFormat: "yy-mm-dd"});
		if ($("#happening_t").val() == "") {
			$("#confirmation_t").val("");
			$( "#confirmation_t" ).attr("disabled", "");
		} else {
			$( "#confirmation_t" ).removeAttr("disabled");
		}
		$( "#confirmation_t" ).datepicker('option', 'maxDate', max_date);
		$( "#happening_t" ).datepicker({ minDate: "+0d", dateFormat: "yy-mm-dd"});
	}

	$("form").change(update_form);
	$(document).ready(update_form);
</script>