<a href="index.php">Go back to index</a><br/><br/>
<?php
	global $g_display;
	$purchases = $g_display["purchase"];
?>
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
	$total = 0;
	foreach ($purchases as $purchase) {
		$event_title = $purchase["event_title"];
		$label = $purchase["label"];
		$tax = $purchase["tax"];
		$rate = $purchase["rate"];
		$quantity = $purchase["quantity"];
		
		$rate_ht = $rate * $quantity;
		$tax_amount = number_format((($tax/100) * $rate_ht), 2);
		$sub_total = $tax_amount + $rate_ht;
		$total += $sub_total;
?>
	<tr>
		<td><?php echo $event_title; ?></td>
		<td><?php echo $label; ?></td>
		<td><?php echo $rate; ?></td>
		<td><?php echo $quantity; ?></td>
		<td><?php echo number_format($rate_ht, 2); ?></td>
		<td><?php echo number_format($tax, 2); ?></td>
		<td><?php echo number_format($tax_amount, 2); ?></td>
		<td><?php echo number_format($sub_total, 2); ?></td>
	</tr>
<?php
	}
	$_SESSION["Payment_Amount"] = $total;
?>
	<tr>
		<th colspan="7">TOTAL</th>
		<th><?php echo number_format($total, 2); ?></th>
	</tr>
</table>
<br/>

<input type="checkbox" name="confirm"/> I have read the GCU and accept it.<br/>
<?php
	paypal_button();
?>