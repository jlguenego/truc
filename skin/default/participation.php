<?php
	global $g_display;
	global $g_error_msg;
	$event = $g_display["event"];
	$rates = $g_display["rates"];
	
	
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
	<form name="input" action="?action=participate&amp;event_id=<?php echo $event['id']; ?>" method="POST">
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
					$event_title = $event["title"];
					$amount = $rate["amount"];
					$label = $rate["label"];
					$tax_rate = $rate["tax_rate"];
					if (!in_array($tax_rate, $tax_array)) {
						$tax_array[] = $tax_rate;
					}
			?>
			<tr>
				<td><?php echo $event_title; ?></td>
				<td><?php echo $label; ?></td>
				<td id="unit_price_<?php echo $i; ?>"><?php echo $amount; ?></td>
				<td><input id="<?php echo $i; ?>" type="number" name="ticket_<?php echo $i; ?>" value="<?php echo_default_value("ticket_${i}", 1) ?>"/></td>
				<td id="total_ht_<?php echo $i; ?>">0.00</td>
				<td id="tax_rate_<?php echo $i; ?>"><?php echo number_format($tax_rate, 2); ?></td>
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
				<td>Tax(<?php echo number_format($tax_rate, 2); ?>)</td>
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
		<input type="checkbox" name="confirm"/> I engage myself to participate to this event.<br/>
		<input type="submit" value="Yes"/>		
	</form>
	<form name="input" action="event.php?id=<?php echo $event['id']; ?>" method="POST">
		<input type="hidden" name="confirm" value="no"/>
		<input type="submit" value="No"/>		
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
		$('input').change(update);
		$('input').keyup(update);
		$('input[id]').each(update);
		
		
		function update() {
			var amount = Math.abs($(this).val());
			$(this).val(amount);
			
			var id = $(this).attr('id');
			var unit_price = $('#unit_price_' + id).html();
			var total_ht = amount * unit_price;
			$('#total_ht_' + id).html(total_ht.toFixed(2));
			
			var tax_rate = $('#tax_rate_' + id).html();
			var tax_amount = (tax_rate/100) * total_ht;
			$('#tax_amount_' + id).html(tax_amount.toFixed(2));
			
			$('#ttc_' + id).html((tax_amount + total_ht).toFixed(2));
			
			var sub_total = 0;
			for (i = 0; i < rate_nbr; i++) {
				var current_ttc = $('#total_ht_' + i).html();
				sub_total = parseFloat(sub_total) + parseFloat(current_ttc);
			}
			$('#sub_total').html(sub_total.toFixed(2));
			
			update_total(tax_rate);
		}
		
		function update_total(tax_rate) {
			for (i = 0; i < taxes.length; i++) {
				var tax = taxes[i][0];
				var id = taxes[i][1];
				if (tax == tax_rate) {
					var sub_total = 0;
					for (i = 0; i < rate_nbr; i++) {
						var tax_rate2 = $('#tax_rate_' + i).html();
						if (tax_rate2 == tax) {
							var current_total = $('#total_ht_' + i).html();
							sub_total = parseFloat(sub_total) + parseFloat(current_total);
						}
					}
					$('#tax_base_' + id).html(sub_total.toFixed(2));
					sub_total *= (tax/100);
					$('#tax_total_' + id).html(sub_total.toFixed(2));
				}
			}
			
			sub_total = 0;
			for (i = 0; i < tax_nbr; i++) {
				var current_total = $('#tax_total_' + i).html();
				sub_total = parseFloat(sub_total) + parseFloat(current_total);
			}
			$('#tax_total').html(sub_total.toFixed(2));
			var total = parseFloat($('#sub_total').html()) + parseFloat($('#tax_total').html());
			$('#total_due').html('<b>' + total.toFixed(2) + '</b>');
		}
	</script>
</html>