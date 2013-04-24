<?php
	$user = $g_display["user"];
	$bill = $g_display["bill"];
?>
<p>
	Chèr(e) <?php echo $user->get_name(); ?>,<br/>
	Vous avez effectué une autorisation de paiement pour le devis suivant.<br/>
	Si l'évènement est confirmé, vous recevrez une facture et votre paiement
	sera prélevé.<br/>
	Si l'évènement est annulé, votre autorisation de paiement le sera aussi et
	vous en serez informé par mail.<br/>
</p>
<?php print_bill($bill); ?>
<br/>
<br/>
<p>
	Veuillez vous assurer que la quantité de
	<b><?php echo curr($bill->total_ttc); ?>€</b>
	sera disponible pour au moins
	<?php echo AUTHORIZATION_DELAY; ?> jours.<br/>
	Vous pouvez accéder à ce devis via ce lien permanent : <a href="<?php echo $bill->url(); ?>"><?php echo $bill->url(); ?></a>
</p>