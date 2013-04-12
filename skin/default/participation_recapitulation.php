<?php
	global $g_display;
	$devis = $g_display["devis"];

	echo "Label: ".$devis->label;
?>
<table class="evt_table">
	<tr>
		<th>Event</th>
		<th>Rate name</th>
		<th>Unit price (€)</th>
		<th>Quantity</th>
		<th>Total tax excluded (€)</th>
		<th>Tax rate (%)</th>
		<th>Tax amount (€)</th>
		<th>Total (€)</th>
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
		<td class="evt_curr"><?php echo $event_rate_amount; ?></td>
		<td class="evt_curr"><?php echo $quantity; ?></td>
		<td class="evt_curr"><?php echo $total_ht; ?></td>
		<td class="evt_curr"><?php echo $event_rate_tax; ?></td>
		<td class="evt_curr"><?php echo $total_tax; ?></td>
		<td class="evt_curr"><?php echo $total_ttc; ?></td>
	</tr>
<?php
	}
?>
	<tr>
		<th class="th_left" colspan="4">Total</th>
		<th class="evt_curr"><?php echo curr($devis->total_ht); ?></th>
	</tr>
</table>
<br/>

<table class="evt_table">
	<tr>
		<th class="th_left">Total (€)</th>
		<td class="evt_curr"><?php echo curr($devis->total_ht); ?></td>
	</tr>
	<tr>
		<th class="th_left">Total taxes (€)</th>
		<td class="evt_curr"><?php echo curr($devis->total_tax); ?></td>
	</tr>
	<tr>
		<th class="th_left">Total due (€)</th>
		<td class="evt_curr"><?php echo curr($devis->total_ttc); ?></td>
	</tr>
</table>
<br/>

<table class="evt_table">
	<tr>
		<th class="th_left">Billing Entity name</th>
		<td><?php echo $devis->username; ?></td>
	</tr>
	<tr>
		<th class="th_left">Billing address</th>
		<td><?php echo $devis->address; ?></td>
	</tr>
</table>
<br/>

<?php
	payment_button();
?>