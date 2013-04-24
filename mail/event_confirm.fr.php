<?php
	$user = $g_display["user"];
	$invoice = $g_display["invoice"];
	$quotation = $g_display["quotation"];
?>
<p>
	Chèr(e) <?php echo $user->get_name(); ?>,<br/>
	Vous avez fait une autorisation de paiement pour le devis suivant.<br/>
	<br/>
	<?php echo $quotation->label; ?>
	<br/>
	<br/>
	L'évènement a été confirmé. Votre autorisation de paiement sera prélevée.<br/>
	Voici une facture pour ce paiement.<br/>
	<br/>
<?php print_bill($invoice); ?>
	<br/>
	Vous pouvez accèder à cette facture via ce lien permanent : <a href="<?php echo $invoice->url(); ?>"><?php echo $invoice->url(); ?></a>
</p>