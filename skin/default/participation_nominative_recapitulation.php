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
		<th>Tax rate</th>
		<th>Tax amount</th>
		<th>Total</th>
		<th>Title</th>
		<th>Lastname</th>
		<th>Firstname</th>
	</tr>
<?php
	foreach ($devis->items as $item) {
		$event_name = $item->event_name;
		$event_rate_name = $item->event_rate_name;
		$event_rate_amount = curr($item->event_rate_amount);
		$event_rate_tax = $item->event_rate_tax;
		$total_ht = curr($item->total_ht);
		$total_tax = curr($item->total_tax);
		$total_ttc = curr($item->total_ttc);
		$participant_firstname = $item->participant_firstname;
		$participant_lastname = $item->participant_lastname;
		$participant_title = $item->participant_title;
?>
	<tr>
		<td><?php echo $event_name; ?></td>
		<td><?php echo $event_rate_name; ?></td>
		<td><?php echo $event_rate_amount; ?></td>
		<td><?php echo $event_rate_tax; ?></td>
		<td><?php echo $total_tax; ?></td>
		<td><?php echo $total_ttc; ?></td>
		<td><?php echo $participant_title; ?></td>
		<td><?php echo $participant_lastname; ?></td>
		<td><?php echo $participant_firstname; ?></td>
	</tr>
<?php
	}
?>
	<tr>
		<th colspan="4">TOTAL HT</th>
		<th><?php echo curr($devis->total_ht); ?></th>
	</tr>
</table>
<br/>

<table>
	<tr>
		<td>TOTAL TAX</td>
		<td><?php echo $devis->total_tax; ?></td>
	</tr>
	<tr>
		<th>TOTAL DUE</th>
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