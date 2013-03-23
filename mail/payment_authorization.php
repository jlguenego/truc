<?php
	function mail_html_payment_authorization($devis, $event, $user) {
		ob_start();
		include("header.php");
?>
		<p>
			Dear <?php echo ucfirst(strtolower($user->firstname))." ".strtoupper($user->lastname); ?>,<br/>
			You have made a payment authorization for the following quotation.<br/>
			If the event is confirmed, you will receive an invoice and your payment
			will be captured. <br/>
			If the event is cancelled, your payment authorization
			will be also cancelled and you will receive a notification by email.<br/>
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
			foreach ($devis->items as $item) {
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
					<td><?php echo curr($devis->total_ht); ?>€</td>
				</tr>
				<tr>
					<th>Total tax</th>
					<td><?php echo curr($devis->total_tax); ?>€</td>
				</tr>
				<tr>
					<th>Total Due</th>
					<td><b><?php echo curr($devis->total_ttc); ?>€</b></td>
				</tr>
			</table>
			<br/>
			Please make sure that the amount of <b><?php echo curr($devis->total_ttc); ?>€</b> will be avalable for at least <?php echo AUTHORIZATION_DELAY; ?> days.<br/>
			You can access to this quotation via this permalink: <a href="<?php echo $devis->url(); ?>"><?php echo $devis->url(); ?></a>
		</p>
<?php
		include("footer.php");
		$result = ob_get_contents();
		ob_end_clean();
		return $result;
	}
?>