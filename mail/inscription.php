<?php
	function mail_html_inscription($key, $login) {
		ob_start();
		$lien = HOST.'/?action=activation&amp;key='.$key;
		include("header.php");
?>
		<p>
			Dear <?php echo $login; ?>,<br/>
			Thanks for subscribe. Your are almost ready to create or participate to events.<br/>
			Please click the link below to activate your account:<br/>
			<a href="<?php echo $lien; ?>"><?php echo $lien; ?></a>
		</p>
<?php
		include("footer.php");
		$result = ob_get_contents();
		ob_end_clean();
		return $result;
	}
?>
