<a href="index.php">Go back to index</a><br/><br/>
<?php
	global $g_display;
	$devis = $g_display["devis"];
?>
<table>
	<tr>
		<th>Event</th>
		<th>Rate name</th>
		<th>Unit price</th>
		<th>Quantity</th>
		<th>Total tax excluded</th>
		<th>Taxe rate</th>
		<th>Taxe amount</th>
		<th>TTC</th>
	</tr>
<?php
	foreach ($devis->items as $item) {
		$event_name = $item->event_name;
		$event_rate_name = $item->event_rate_name;
		$event_rate_amount = $item->event_rate_amount;
		$event_rate_tax = $item->event_rate_tax;
		$quantity = $item->quantity;
		$total_ht = $item->total_ht;
		$total_tax = $item->total_tax;
		$total_ttc = $item->total_ttc;
?>
	<tr>
		<td><?php echo $event_name; ?></td>
		<td><?php echo $event_rate_name; ?></td>
		<td><?php echo $event_rate_amount; ?></td>
		<td><?php echo $quantity; ?></td>
		<td><?php echo $event_rate_amount; ?></td>
		<td><?php echo $event_rate_tax; ?></td>
		<td><?php echo $total_tax; ?></td>
		<td><?php echo $total_ttc; ?></td>
	</tr>
<?php
	}
?>
	<tr>
		<th colspan="4">TOTAL HT</th>
		<th><?php echo $devis->total_ht; ?></th>
	</tr>
</table>
<br/>

<table>
	<tr>
		<td>TOTAL TAX</td>
		<td><?php echo $devis->total_tax; ?></td>
	</tr>
	<tr>
		<th>TOTAL TTC</th>
		<th><?php echo $devis->total_ttc; ?></th>
	</tr>
</table>
<br/>

<table>
	<tr>
		<td>Billing Entity name: </td>
		<td><?php echo $devis->username; ?></td>
	</tr>
	<tr>
		<td>Billing address: </td>
		<td><?php echo $devis->address; ?></td>
	</tr>
</table>
<br/>

<?php
	payment_button();
?>