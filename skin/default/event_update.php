<?php
	$event = $g_display["event"];
	$rates = $g_display["rates"];
?>
<script type="text/javascript" src="jscript/misc.js"></script>

<a href="index.php">Go back to index</a><br/><br/>
<span style="color:red;">
	<?php echo "$g_error_msg<br/>"; ?>
</span>
<form name="input" action="?action=update&amp;type=event&amp;id=<?php echo $event['id']; ?>" method="POST">
	<table>
	<tr>
		<td>Title: </td>
		<td><input type="text" name="title" value="<?php echo $event['title']; ?>"></td>
		<td>
			<span class="help">The name of your event.</span>
		</td>
	</tr>
	<tr>
		<td>Required funding: </td>
		<td><input type="text" name="persons" value="<?php echo $event['funding_wanted']; ?>"></td>
		<td>
			<span class="help">Minimum funding wanted in Euro.</span>
		</td>
	</tr>
	<tr>
		<td>Event date: </td>
		<td><input type="text" name="date" id="calendar"
			value="<?php echo date("Y-m-d", $event["event_date"]); ?>"></td>
		<td>
			<span class="help">The date of your event (yyyy-mm-dd).</span>
		</td>
	</tr>
	<tr>
		<td>Event deadline: </td>
		<td><input type="text" name="date" id="calendar"
			value="<?php echo date("Y-m-d", $event["event_deadline"]); ?>"></td>
		<td>
			<span class="help">Deadline for validation (yyyy-mm-dd).</span>
		</td>
	</tr>
		<tr>
			<td>Location: </td>
			<td>
				<input type="text" name="location"
					value="<?php echo $event["location"] ?>"/>
			</td>
			<td>
				<span class="help">Where this event will happend.</span>
			</td>
		</tr>
		<tr>
			<td>Link:</td>
			<td>
				<input type="text" name="link" 
					value="<?php echo $event["link"] ?>"/>
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
<?php echo $event["short_description"] ?></textarea>
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
<?php echo $event["long_description"]; ?></textarea>
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
		$taxe_rate = $rate["taxe_rate"];
		echo "<script language=\"javascript\" type=\"text/javascript\">";
		echo "addRate('rates', '$label', '$amount', '$taxe_rate');";
		echo "</script>";
		$i++;
	}
	echo "<script language=\"javascript\" type=\"text/javascript\">";
	echo "setCounter($i);";
	echo "</script>";
?>
	<input type="button" value="Add a rate" onClick="addRate('rates');">
	<input type="hidden" name="id" value="<?php echo $event['id']; ?>"/>
	<input type="submit" value="Submit"/>
</form>