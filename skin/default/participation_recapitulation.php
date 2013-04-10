<?php
	global $g_display;
	$devis = $g_display["devis"];

	echo "Label: ".$devis->label;
?>
<table>
	<tr>
		<th>Event</th>
		<th>Rate name</th>
		<th>Unit price</th>
		<th>Quantity</th>
		<th>Total tax excluded</th>
		<th>Tax rate</th>
		<th>Tax amount</th>
		<th>Total</th>
	</tr>
<?php
	foreach ($devis->items as $item) {
		$event_name = $item->event_name;
		$event_rate_name = $item->event_rate_name;
		$event_rate_amount = curr($item->event_rate_amount);
		$event_rate_tax = $item->event_rate_tax;
		$quantity = $item->quantity;
		$total_ht = curr($item->total_ht);
		$total_tax = curr($item->total_tax);
		$total_ttc = curr($item->total_ttc);
?>
	<tr>
		<td><?php echo $event_name; ?></td>
		<td><?php echo $event_rate_name; ?></td>
		<td><?php echo $event_rate_amount; ?></td>
		<td><?php echo $quantity; ?></td>
		<td><?php echo $total_ht; ?></td>
		<td><?php echo $event_rate_tax; ?></td>
		<td><?php echo $total_tax; ?></td>
		<td><?php echo $total_ttc; ?></td>
	</tr>
<?php
	}
?>
	<tr>
		<th colspan="4">Total</th>
		<th><?php echo curr($devis->total_ht); ?></th>
	</tr>
</table>
<br/>

<table>
	<tr>
		<td>Total taxes</td>
		<td><?php echo $devis->total_tax; ?></td>
	</tr>
	<tr>
		<th>Total due</th>
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