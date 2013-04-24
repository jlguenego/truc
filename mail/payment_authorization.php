<?php
	$user = $g_display["user"];
	$bill = $g_display["bill"];
?>
<p>
	Dear <?php echo $user->get_name(); ?>,<br/>
	You have made a payment authorization for the following quotation.<br/>
	If the event is confirmed, you will receive an invoice and your payment
	will be captured.<br/>
	If the event is cancelled, your payment authorization
	will be also cancelled and you will receive a notification by email.<br/>
</p>
<?php print_bill($bill); ?>
<br/>
<br/>
<p>
	Please make sure that the amount of
	<b><?php echo curr($bill->total_ttc); ?>€</b>
	will be avalable for at least
	<?php echo AUTHORIZATION_DELAY; ?> days.<br/>
	You can access to this quotation via this permalink: <a href="<?php echo $bill->url(); ?>"><?php echo $bill->url(); ?></a>
</p>