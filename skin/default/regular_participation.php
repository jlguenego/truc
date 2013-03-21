<?php
	global $g_display;
	global $g_error_msg;
	$event = $g_display["event"];
	$rates = $g_display["rates"];
	$user = $g_display["user"];


	//if ($error_msg == "") {
		//$user = get_user_by_login($_SESSION['login']);
		//participate($user['id'], $event["id"], $_POST['person_amount']);
		//redirect_to("event.php?id=".$event["id"]);
	//}
?>
	<a href="index.php">Back to index</a><br/><br/>
<span style="color:red;">
	<?php echo "$g_error_msg<br/>"; ?>
</span>
How many ticket do you want?
<form name="input" action="?action=participate&amp;event_id=<?php echo $event->id; ?>" method="POST">
	<table>
		<tr>
			<th>Event</th>
			<th>Rate</th>
			<th>Unit price</th>
			<th>Quantity</th>
			<th>Total tax excluded</th>
			<th>Taxe rate</th>
			<th>Taxe amount</th>
			<th>TTC</th>
		</tr>
		<?php
			$i = 0;
			$tax_array = array();
			foreach ($rates as $rate) {
				$event_title = $event->title;
				$amount_ht = str_replace(",", "", curr($rate["amount"]));
				$label = $rate["label"];
				$tax_rate = $rate["tax_rate"];
				if (!in_array($tax_rate, $tax_array)) {
					$tax_array[] = $tax_rate;
				}
		?>
		<tr>
			<td><?php echo $event_title; ?></td>
			<td><?php echo $label; ?></td>
			<td id="unit_price_<?php echo $i; ?>"><?php echo $amount_ht; ?></td>
			<td><input id="<?php echo $i; ?>" type="number" name="ticket_<?php echo $i; ?>" value="<?php echo_default_value("ticket_${i}", 1) ?>"/></td>
			<td id="total_ht_<?php echo $i; ?>">0.00</td>
			<td id="tax_rate_<?php echo $i; ?>"><?php echo $tax_rate; ?></td>
			<td id="tax_amount_<?php echo $i; ?>">0.00</td>
			<td id="ttc_<?php echo $i; ?>">0.00</td>
		</tr>
		<?php
				$i++;
			}
			debug(sprint_r($tax_array));
		?>
		<tr>
			<th colspan="4">TOTAL HT</th>
			<td id="sub_total">0.00</td>
		</tr>
	</table>
	<br/>

	<table>
		<?php
			$i2 = 0;
			foreach ($tax_array as $tax_rate) {
		?>
		<tr>
			<td>Tax(<?php echo $tax_rate; ?>)</td>
			<td id="tax_base_<?php echo $i2; ?>">0.00</td>
			<td id="tax_total_<?php echo $i2; ?>">0.00</td>
		</tr>
		<?php
				$i2++;
			}
		?>
		<tr>
			<td colspan="2">Total taxes</td>
			<td id="tax_total">0.00</td>
		</tr>
		<tr>
			<td colspan="2"><b>Total due</b></td>
			<td id="total_due"><b>0.00</b></td>
		</tr>
	</table>
	<br/>

	<table>
		<tr>
			<td>Billing Entity name: </td>
			<td><input type="text" name="username" value="<?php echo $user['firstname'].' '.$user['lastname']; ?>"/></td>
			<td class="help">The person or organisation name to be charged.</td>
		</tr>
		<tr>
			<td>Billing address: </td>
			<td><textarea  rows=3 name="address"><?php echo $user['address']; ?></textarea></td>
			<td class="help">numero - rue - code postal - ville - pays</td>
		</tr>
	</table>
	<input type="checkbox" name="confirm"/> I have read the <a href="CGV.pdf">CGV</a> and accept them.<br/>
	<input type="submit" value="Next" disabled/>
</form>
<form name="input" action="?action=retrieve&amp;type=event&amp;id=<?php echo $event->id; ?>" method="POST">
	<input type="submit" value="Cancel"/>
</form>
<script>
	var rate_nbr = <?php echo $i; ?>;
	var tax_nbr = <?php echo $i2; ?>;
	var taxes = new Array(
		<?php
			$is_first = TRUE;
			$i = 0;
			foreach ($tax_array as $tax_rate) {
				if ($is_first) {
					$is_first = FALSE;
				} else {
					echo ',';
				}
				echo "new Array('${tax_rate}', '${i}')";
				$i++;
			}
		?>
	);
	$('input').change(eb_sync_amount);
	$('input').keyup(eb_sync_amount);
	$('input[id]').each(eb_sync_amount);

	$('input[type=checkbox]').ready(eb_sync_next_button);
	$('input[type=checkbox]').change(eb_sync_next_button);



	function eb_sync_next_button() {
		if ($('input[type=checkbox]').is(':checked')) {
			$('input[value=Next]').removeAttr('disabled');
		} else {
			$('input[value=Next]').attr('disabled', 'disabled');
		}
	}
</script>