<?php
	global $g_display;
	$devis = $g_display["devis"];
	$event = $devis->get_event();
?>
<div class="evt_title inline"><p><?php echo format_participate_title($event); ?> - {{Confirmation}}</p></div>
<?php
	if ($event->is_confirmed()) {
		echo _t("Invoice ID: ").$devis->label;
	} else {
		echo _t("Quotation ID: ").$devis->label;
	}
?>
<table class="evt_table inline">
	<tr>
		<th>{{Event}}</th>
		<th>{{Rate name}}</th>
		<th>{{Unit price}} (€)</th>
		<th>{{Tax rate}} (%)</th>
		<th>{{Tax amount}} (€)</th>
		<th>{{Total}} (€)</th>
		<th>{{Title}}</th>
		<th>{{Firstname}}</th>
		<th>{{Lastname}}</th>
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
		$attendee_firstname = $item->attendee_firstname;
		$attendee_lastname = $item->attendee_lastname;
		$attendee_title = $item->attendee_title;
?>
	<tr>
		<td><?php echo $event_name; ?></td>
		<td><?php echo $event_rate_name; ?></td>
		<td class="evt_curr"><?php echo $event_rate_amount; ?></td>
		<td class="evt_curr"><?php echo $event_rate_tax; ?></td>
		<td class="evt_curr"><?php echo $total_tax; ?></td>
		<td class="evt_curr"><?php echo $total_ttc; ?></td>
		<td><?php echo $attendee_title; ?></td>
		<td><?php echo $attendee_firstname; ?></td>
		<td><?php echo $attendee_lastname; ?></td>
	</tr>
<?php
	}
?>
</table>
<table class="evt_table inline">
	<tr>
		<th class="th_left">{{Total}} (€)</th>
		<td class="evt_curr"><?php echo curr($devis->total_ht); ?></td>
	</tr>
	<tr>
		<th class="th_left">{{Total taxes}} (€)</th>
		<td class="evt_curr"><?php echo curr($devis->total_tax); ?></td>
	</tr>
	<tr>
		<th class="th_left">{{Total due}} (€)</th>
		<td class="evt_curr"><?php echo curr($devis->total_ttc); ?></td>
	</tr>
</table>

<table class="evt_table inline">
	<tr>
		<th class="th_left">{{Billing Entity name}}</th>
		<td><?php echo $devis->username; ?></td>
	</tr>
	<tr>
		<th class="th_left">{{Billing address}}</th>
		<td><?php echo $devis->address; ?></td>
	</tr>
<?php
	if ($devis->is_for_company()) {
?>
	<tr>
		<th class="th_left">{{VAT #}}</th>
		<td><?php echo $devis->vat; ?></td>
	</tr>
<?php
	}
?>
</table>
<br/>
<table class="evt_payment">
	<tr>
		<td class="evt_pay" width="500"><?php payment_button(); ?></td>
		<td width="200">&nbsp;</td>
		<td align="right" class="evt_pay"><?php bitcoin_button(); ?></td>
</tr>
</table>