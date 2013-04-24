<?php
	$user = $g_display["user"];
	$bill = $g_display["bill"];
?>
<p>
	Chèr(e) <?php echo $user->get_name(); ?>,<br/>
	Vous avez fait une autorisation pour le devis suivant.<br/>
	<br/>
	<?php echo $bill->label; ?><br/>
	Vous pouvez accèder à ce devis via ce lien permanent : <a href="<?php echo $bill->url(); ?>"><?php echo $bill->url(); ?></a><br/>
	<br/>
	Nous avons le regret de vous informer que l'évènement a été annulé.
	Votre autorisation de paiement ne sera pas prélevée.<br/>
	Cordialement,
</p>