<a href="index.php">Go back to index</a><br/><br/>
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
			<td><input type="number" name="funding_wanted" value="<?php echo_default_value("funding_wanted"); ?>"/></td>
			<td>
				<span class="help">Minimum funding wanted in Euro.</span>
			</td>
		</tr>
		<tr>
			<td>Event date: </td>
			<td><input type="text" name="date" id="calendar"
				value="<?php echo_default_value("date"); ?>"></td>
			<td>
				<span class="help">The date of your event (yyyy-mm-dd).</span>
			</td>
		</tr>
		<tr>
			<td>Go/NoGo date:</td>
			<td><input type="text" name="deadline" id="deadline"
					value="<?php echo_default_value("deadline"); ?>"/></td>
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
	<input type="button" value="Add a rate" onClick="addRate('rates');"/><br/>
	<input type="submit" value="Submit"/>
</form>
<script>
	$(function() {
		$( "#deadline" ).datepicker({ maxDate: "<?php echo MAX_DEADLINE; ?>", 
			minDate: "+0d", dateFormat: "yy-mm-dd"});
		$( "#calendar" ).datepicker({ minDate: "+0d", dateFormat: "yy-mm-dd"});
	});
</script>