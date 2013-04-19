<?php
	function mail_html_payment_full($devis, $event, $user) {
		ob_start();
		include("header.php");
?>
		<p>
			{{Dear}} <?php echo $user->get_name(); ?>,<br/>
			{{You have made a payment for the following invoice.}}<br/>
			<br/>
<?php
		print_bill($devis);
?>
			{{This invoice is compliant with the French Republic law}} (article L441-3 du Code du Commerce).<br/>
			{{You can access to this invoice via this permalink:}} <a href="<?php echo $devis->url(); ?>"><?php echo $devis->url(); ?></a>
		</p>
<?php
		include("footer.php");
		$result = ob_get_contents();
		ob_end_clean();
		return i18n_parse($result);
	}
?>