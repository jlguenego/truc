<?php
	function mail_html_payment_authorization($devis, $event, $user) {
		ob_start();
		include("header.php");
?>
		<p>
			{{Dear}} <?php echo $user->get_name(); ?>,<br/>
			{{[mail_html_payment_authorization]header}}
		</p>
<?php
		print_bill($devis);
?>
		<p>
			{{Please make sure that the amount of}}
			<b><?php echo curr($devis->total_ttc); ?>€</b>
			{{will be avalable for at least}}
			<?php echo AUTHORIZATION_DELAY; ?> {{days}}.<br/>
			{{You can access to this quotation via this permalink}}: <a href="<?php echo $devis->url(); ?>"><?php echo $devis->url(); ?></a>
		</p>
<?php
		include("footer.php");
		$result = ob_get_contents();
		ob_end_clean();
		return i18n_parse($result);
	}
?>