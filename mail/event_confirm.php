<?php
	function mail_html_event_confirm($devis_label, $invoice, $event, $user) {
		ob_start();
		include("header.php");
?>
		<p>
			Dear <?php echo $user->get_name(); ?>,<br/>
			You have made an authorization for the following quotation.<br/>
			<br/>
			<?php echo $devis_label; ?>
			<br/>
			<br/>
			The event has been confirmed and your payment authorization will be captured.<br/>
			Here is the invoice for this payment.<br/>
			<br/>
			<?php echo $invoice->label; ?>
			<table border="1px">
				<tr>
					<th>Billing entity:</th>
					<td><?php echo $invoice->username; ?></td>
				</tr>
				<tr>
					<th>Billing address: </th>
					<td><?php echo $invoice->address; ?></td>
				</tr>
			</table>
			<br/>
			<table border="1px">
				<tr>
					<th>Event name</th>
					<th>Rate name</th>
					<th>Amount</th>
					<th>tax</th>
		<?php if ($event->type == EVENT_TYPE_ANONYMOUS) { ?>
					<th>Quantity</th>
					<th>Total ticket</th>
		<?php } ?>
					<th>Total taxes</th>
					<th>Total due</th>
		<?php if ($event->type == EVENT_TYPE_NOMINATIVE) { ?>
					<th>Title</th>
					<th>Firsname</th>
					<th>Lastname</th>
		<?php }
			$i = 0;
			foreach ($invoice->items as $item) {
		?>
				<tr>
					<td><?php echo $item->event_name; ?></td>
					<td><?php echo $item->event_rate_name; ?></td>
					<td><?php echo curr($item->event_rate_amount); ?>€</td>
					<td><?php echo curr($item->event_rate_tax); ?>%</td>
		<?php
				if ($event->type == EVENT_TYPE_ANONYMOUS) {
		?>
					<td><?php echo $item->quantity; ?></td>
					<td><?php echo curr($item->total_ht); ?>€</td>
		<?php
				}
		?>
					<td><?php echo curr($item->total_tax); ?>€</td>
					<td><?php echo curr($item->total_ttc); ?>€</td>
		<?php
				if ($event->type == EVENT_TYPE_NOMINATIVE) {
		?>
					<td><?php echo default_str($item->attendee_title, "&nbsp;"); ?></td>
					<td><?php echo $item->attendee_firstname; ?></td>
					<td><?php echo $item->attendee_lastname; ?></td>
		<?php
				}
		?>
				</tr>
		<?php
				$i++;
			}
		?>
			</table>
			<br/>
			<table border="1px">
				<tr>
					<th>Total</th>
					<td><?php echo curr($invoice->total_ht); ?>€</td>
				</tr>
				<tr>
					<th>Total taxes</th>
					<td><?php echo curr($invoice->total_tax); ?>€</td>
				</tr>
				<tr>
					<th>Total due</th>
					<td><b><?php echo curr($invoice->total_ttc); ?>€</b></td>
				</tr>
			</table>
			<br/>
			You can access to this quotation via this permalink: <a href="<?php echo $invoice->url(); ?>"><?php echo $invoice->url(); ?></a>
		</p>
<?php
		include("footer.php");
		$result = ob_get_contents();
		ob_end_clean();
		return $result;
	}
?>