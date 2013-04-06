<?php
	function mail_html_password_changed($user) {
		ob_start();
		include("header.php");
?>
		<p>
			Your account has benn updated and your password has been changed.<br/>
			If it is not you, please contact our support: <?php echo CONTACT_MAIL ?>
		</p>
<?php
		include("footer.php");
		$result = ob_get_contents();
		ob_end_clean();
		return $result;
	}
?>
