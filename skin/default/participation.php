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
				<th>Total taxe excluded</th>
				<th>Taxe rate</th>
				<th>Taxe amount</th>
				<th>TTC</th>
			</tr>
			<?php
				$i = 0;
				$taxe_array = array();
				foreach ($rates as $rate) {
					$event_title = $event["title"];
					$amount = $rate["amount"];
					$label = $rate["label"];
					$taxe_rate = $rate["taxe_rate"];
			?>
			<tr>
				<td><?php echo $event_title; ?></td>
				<td><?php echo $label; ?></td>
				<td id="unit_price_<?php echo $i; ?>"><?php echo $amount; ?></td>
				<td><input id="<?php echo $i; ?>" type="number" name="ticket_<?php echo $i; ?>" value="<?php echo_default_value("ticket_${i}", 0) ?>"/></td>
				<td id="total_ht_<?php echo $i; ?>">0</td>
				<td id="taxe_rate_<?php echo $i; ?>"><?php echo number_format($taxe_rate, 1); ?></td>
				<td id="taxe_amount_<?php echo $i; ?>">0</td>
				<td id="ttc_<?php echo $i; ?>">0</td>
			</tr>
			<?php
					$i++;
				}
			?>
			<tr>
				<th colspan="4">TOTAL HT</th>
				<td id="sub_total">0</td>
			</tr>
		</table>
		<br/>
		<table>
			<tr>
				<td>Taxe(19.6)</td>
				<td>1000</td>
				<td>196</td>
			</tr>
			<tr>
				<td>Taxe(5.5)</td>
				<td>1000</td>
				<td>55</td>
			</tr>
			<tr>
				<td>Total taxes</td>
				<td></td>
				<td>251</td>
			</tr>
			<tr>
				<td>Total due</td>
				<td></td>
				<td>2251</td>
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
		$('input').change(update);
		$('input').keyup(update);
		
		
		function update() {
			var amount = Math.abs($(this).val());
			$(this).val(amount);
			
			var id = $(this).attr('id');
			var unit_price = $('#unit_price_' + id).html();
			var total_ht = amount * unit_price;
			$('#total_ht_' + id).html(total_ht.toFixed(2));
			
			var taxe_rate = $('#taxe_rate_' + id).html();
			var taxe_amount = (taxe_rate/100) * total_ht;
			$('#taxe_amount_' + id).html(taxe_amount.toFixed(2));
			
			$('#ttc_' + id).html((taxe_amount + total_ht).toFixed(2));
			
			var sub_total = 0;
			for (i = 0; i < rate_nbr; i++) {
				var current_ttc = $('#total_ht_' + i).html();
				sub_total = parseFloat(sub_total) + parseFloat(current_ttc);
			}
			$('#sub_total').html(sub_total.toFixed(2));
		}
	</script>
</html>