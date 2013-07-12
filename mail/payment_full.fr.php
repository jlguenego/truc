<?php
	$user = $g_display["user"];
	$bill = $g_display["bill"];
?>
<p>
	Chèr(e) <?php echo $user->get_name(); ?>,<br/>
	Vous venez de régler la facture suivante.<br/>
</p>
<?php print_bill($bill); ?>
<br/>
<br/>
<p>
	Cette facture fait office de billet.
</p>
<p>
	Cette facture respecte la loi de la République française (article L441-3 du Code du Commerce).<br/>
	Vous pouvez accéder à cette facture via ce lien permanent : <a href="<?php echo $bill->url(); ?>"><?php echo $bill->url(); ?></a><br/>
	Une version PDF est aussi disponible: <a href="<?php echo $bill->url_pdf(); ?>"><?php echo $bill->url_pdf(); ?></a>
</p>