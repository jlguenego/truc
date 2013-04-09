<?php
	function mail_html_event_cancel($devis, $event, $user) {
		ob_start();
		include("header.php");
?>
		<p>
			Dear <?php echo $user->get_name(); ?>,<br/>
			You have made an authorization for the following quotation.<br/>
			<br/>
			<?php echo $devis->label; ?><br/>
			You can access to this quotation via this permalink: <a href="<?php echo $devis->url(); ?>"><?php echo $devis->url(); ?></a>

			<br/>
			<br/>
			Unfortunately the event has been cancelled so your payment
			authorization will be cancelled. You will not be charged.<br/>
		</p>
<?php
		include("footer.php");
		$result = ob_get_contents();
		ob_end_clean();
		return $result;
	}
?>