<?php
	$user = $g_display["user"];
	$invoice = $g_display["invoice"];
	$quotation = $g_display["quotation"];
?>
<p>
	Dear <?php echo $user->get_name(); ?>,<br/>
	You have made an authorization for the following quotation.<br/>
	<br/>
	<?php echo $quotation->label; ?>
	<br/>
	<br/>
	The event has been confirmed and your payment authorization will be captured.<br/>
	Here is the invoice for this payment.<br/>
	<br/>
<?php print_bill($invoice); ?>
	<br/>
	You can access to this quotation via this permalink: <a href="<?php echo $invoice->url(); ?>"><?php echo $invoice->url(); ?></a>
</p>