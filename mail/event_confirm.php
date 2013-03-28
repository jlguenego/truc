<?php
	function mail_html_event_confirm($devis_label, $invoice, $event, $user) {
		ob_start();
		include("header.php");
?>
		<p>
			Dear <?php echo ucfirst(mb_strtolower($user->firstname, "UTF-8")).
				" ".mb_strtoupper($user->lastname, "UTF-8"); ?>,<br/>
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
					<th>Amount HT</th>
					<th>Rate tax</th>
		<?php if ($event->nominative == 0) { ?>
					<th>Quantity</th>
					<th>Total ticket HT</th>
		<?php } ?>
					<th>Total ticket tax</th>
					<th>Total ticket TTC</th>
		<?php if ($event->nominative == 1) { ?>
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
				if ($event->nominative == 0) {
		?>
					<td><?php echo $item->quantity; ?></td>
					<td><?php echo curr($item->total_ht); ?>€</td>
		<?php
				}
		?>
					<td><?php echo curr($item->total_tax); ?>€</td>
					<td><?php echo curr($item->total_ttc); ?>€</td>
		<?php
				if ($event->nominative == 1) {
		?>
					<td><?php echo default_str($item->participant_title, "&nbsp;"); ?></td>
					<td><?php echo $item->participant_firstname; ?></td>
					<td><?php echo $item->participant_lastname; ?></td>
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
					<th>Total HT</th>
					<td><?php echo curr($invoice->total_ht); ?>€</td>
				</tr>
				<tr>
					<th>Total tax</th>
					<td><?php echo curr($invoice->total_tax); ?>€</td>
				</tr>
				<tr>
					<th>Total Due</th>
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