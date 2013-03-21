<?php
	function mail_html_forgotten_password($user, $token) {
		ob_start();
		$link = HOST."/index.php?action=get_form&amp;type=reset_password&amp;permission_token=".$token;
		include("header.php");
?>
		<p>
			A request has been made to reset your password on the web site <a href="<?php echo HOST; ?>"><?php echo HOST; ?></a>.<br/>
			Here is a link to reset your password:<br/>
			<a href="<?php echo $link; ?>"><?php echo $link; ?></a><br/>
			If you never asked for this, please just ignore this email.
		</p>
<?php
		include("footer.php");
		$result = ob_get_contents();
		ob_end_clean();
		return $result;
	}
?>
