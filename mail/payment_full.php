<?php
	$user = $g_display["user"];
	$bill = $g_display["bill"];
?>
<p>
	Dear <?php echo $user->get_name(); ?>,<br/>
	You have made a payment for the following invoice.<br/>
</p>
<?php print_bill($bill); ?>
<br/>
<br/>
<p>
	This invoice acts as your ticket.
</p>
<p>
	This invoice is compliant with the French Republic law (article L441-3 du Code du Commerce).<br/>
	You can access to this invoice via this permalink: <a href="<?php echo $bill->url(); ?>"><?php echo $bill->url(); ?></a>
</p>