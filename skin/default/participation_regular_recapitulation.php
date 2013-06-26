<?php
	global $g_display;
	$bill = $g_display["bill"];
	$event = $bill->get_event();
?>
<div class="evt_title inline"><p><?php echo format_participate_title($event); ?> - {{Confirmation}}</p></div>
<?php
	if ($event->is_confirmed()) {
		echo _t("Invoice ID: ").$bill->label;
	} else {
		echo _t("Quotation ID: ").$bill->label;
	}
?>
<table class="evt_table inline">
	<tr>
		<th>{{Event}}</th>
		<th>{{Rate name}}</th>
		<th>{{Unit price}} (€)</th>
		<th>{{Quantity}}</th>
		<th>{{Total tax excluded}} (€)</th>
		<th>{{Tax rate}} (%)</th>
		<th>{{Tax amount}} (€)</th>
		<th>{{Total}} (€)</th>
	</tr>
<?php
	foreach ($bill->items as $item) {
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
		<th class="th_left" colspan="4">{{Total}}</th>
		<th class="evt_curr"><?php echo curr($bill->total_ht); ?></th>
	</tr>
</table>

<table class="evt_table inline">
	<tr>
		<th class="th_left">{{Total}} (€)</th>
		<td class="evt_curr"><?php echo curr($bill->total_ht); ?></td>
	</tr>
	<tr>
		<th class="th_left">{{Total taxes}} (€)</th>
		<td class="evt_curr"><?php echo curr($bill->total_tax); ?></td>
	</tr>
	<tr>
		<th class="th_left">{{Total due}} (€)</th>
		<td class="evt_curr"><?php echo curr($bill->total_ttc); ?></td>
	</tr>
</table>

<table class="evt_table inline">
	<tr>
		<th class="th_left">{{Billing Entity name}}</th>
		<td><?php echo $bill->username; ?></td>
	</tr>
	<tr>
		<th class="th_left">{{Billing address}}</th>
		<td><?php echo $bill->address; ?></td>
	</tr>
<?php
	if ($bill->is_for_company()) {
?>
	<tr>
		<th class="th_left">{{VAT #}}</th>
		<td><?php echo $bill->vat; ?></td>
	</tr>
<?php
	}
?>
</table>
</table>
<br/>
<table class="evt_payment">
	<tr>
		<td class="evt_pay" width="500"><?php payment_button(); ?></td>
		<td width="200">&nbsp;</td>
		<td align="right" class="evt_pay"><?php bitcoin_button(); ?></td>
</tr>
</table>