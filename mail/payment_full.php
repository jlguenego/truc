<?php
	function mail_html_payment_full($devis, $event, $user) {
		ob_start();
		include("header.php");
?>
		<p>
			Dear <?php echo $user->get_name(); ?>,<br/>
			You have made a payment for the following invoice.<br/>
			<br/>
			<?php echo $devis->label; ?>
			<table border="1px">
				<tr>
					<th>Billing entity:</th>
					<td><?php echo $devis->username; ?></td>
				</tr>
				<tr>
					<th>Billing address: </th>
					<td><?php echo $devis->address; ?></td>
				</tr>
			</table>
			<br/>
			<table border="1px">
				<tr>
					<th>Event name</th>
					<th>Rate name</th>
					<th>Amount</th>
					<th>Tax</th>
		<?php if ($event->type == EVENT_TYPE_ANONYMOUS) { ?>
					<th>Quantity</th>
					<th>Total</th>
		<?php } ?>
					<th>Total tax</th>
					<th>Total due</th>
		<?php if ($event->type == EVENT_TYPE_NOMINATIVE) { ?>
					<th>Title</th>
					<th>Firsname</th>
					<th>Lastname</th>
		<?php }
			$i = 0;
			foreach ($devis->items as $item) {
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
					<th>Total</th>
					<td><?php echo curr($devis->total_ht); ?>€</td>
				</tr>
				<tr>
					<th>Total tax</th>
					<td><?php echo curr($devis->total_tax); ?>€</td>
				</tr>
				<tr>
					<th>Total due</th>
					<td><b><?php echo curr($devis->total_ttc); ?>€</b></td>
				</tr>
			</table>
			<br/>
			You can access to this invoice via this permalink: <a href="<?php echo $devis->url(); ?>"><?php echo $devis->url(); ?></a>
		</p>
<?php
		include("footer.php");
		$result = ob_get_contents();
		ob_end_clean();
		return $result;
	}
?>