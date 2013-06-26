<?php
	$report = $g_display["report"];
	$event = $g_display["event"];
?>
<div class="evt_title"><p>{{Payment report}}</p></div>
{{Event:}} <a href="<?php echo $event->get_url(); ?>"><?php echo $event->title; ?></a><br/>
<br/>
<table width="100%">
	<tr>
		<td valign="top">
<table class="evt_table eb_report inline">
	<tr>
		<th>{{Ticket name}}</th>
		<th>{{Ticket price}} (€)</th>
		<th>{{Total invoice}} (€)</th>
		<th>{{Paypal fee}} (€)</th>
		<th>{{Event-Biller credit}} (€)</th>
	</tr>
<?php
	$invoice_even = 'eb_invoice_odd';
	foreach ($report['invoices'] as $invoice) {
		$rowspan = count($invoice['tickets']);
		$first = true;
?>
	<tr>
<?php
		foreach ($invoice['tickets'] as $ticket) {
?>
		<td class="<?php echo $invoice_even; ?>">
			<?php echo $ticket['name']; ?>
		</td>
		<td class="evt_curr <?php echo $invoice_even; ?>">
			<?php echo curr($ticket['total_ttc']); ?>
		</td>
<?php
			if ($first) {
				$first = false;
?>
		<td class="evt_curr <?php echo $invoice_even; ?>" rowspan="<?php echo $rowspan; ?>">
			<?php echo curr($invoice['total']); ?>
		</td>
		<td class="evt_curr <?php echo $invoice_even; ?>" rowspan="<?php echo $rowspan; ?>">
			<?php echo curr($invoice['paypal_fee']); ?>
		</td>
		<td class="evt_curr <?php echo $invoice_even; ?>" rowspan="<?php echo $rowspan; ?>">
			<?php echo curr($invoice['eb_total']); ?>
		</td>
<?php
			}
?>
	</tr>
<?php
		}
		if ($invoice_even == 'eb_invoice_odd') {
			$invoice_even = 'eb_invoice_even';
		} else {
			$invoice_even = 'eb_invoice_odd';
		}
	}
?>
</table>
		</td>
		<td valign="top" align="right">
<table class="evt_table eb_report">
	<tr>
		<th class="th_left">{{Total}}</th>
		<td class="evt_curr"><?php echo curr($report['total']); ?>€</td>
	</tr>
	<tr>
		<th class="th_left">{{Fees}}</th>
		<td class="evt_curr"><?php echo curr($report['eb_fee']); ?>€</td>
	</tr>
	<tr>
		<th class="th_left">{{Organizer revenue}}</th>
		<td class="evt_curr"><b><?php echo curr($report['organizer_revenue']); ?>€</b></td>
	</tr>
</table>
<br/>
{{For information:}}<br/>
<table class="eb_report">
	<tr>
		<th class="th_left">{{Paypal fees}}</th>
		<td class="evt_curr"><?php echo curr($report['paypal_fee']); ?>€</td>
	</tr>
	<tr>
		<th class="th_left">{{Real Event-Biller fees}}</th>
		<td class="evt_curr"><?php echo curr($report['real_eb_fee']); ?>€</td>
	</tr>
</table>
		</td>
	</tr>
</table>