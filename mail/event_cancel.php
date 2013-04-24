<?php
	$user = $g_display["user"];
	$bill = $g_display["bill"];
?>
<p>
	Dear <?php echo $user->get_name(); ?>,<br/>
	You have made an authorization for the following quotation.<br/>
	<br/>
	<?php echo $bill->label; ?><br/>
	You can access to this quotation via this permalink: <a href="<?php echo $bill->url(); ?>"><?php echo $bill->url(); ?></a>

	<br/>
	<br/>
	Unfortunately the event has been cancelled so your payment
	authorization will be cancelled. You will not be charged.<br/>
</p>