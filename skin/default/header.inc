<div id="header">
	<a href="index.php"><img src="<?php echo HOST . "/" . SKIN_DIR ?>/images/logo.png"/></a><br/>
	<div id="user_info">
<?php
	if (is_logged()) {
		$user = User::get_from_login($_SESSION['login']);
		$lastname = strtoupper($user->lastname);
		$firstname = ucfirst($user->firstname);
		$email = strtolower($user->email);
?>



		Logged: <?php echo $user->login ?> (<?php echo "${firstname} ${lastname}" ?> - (<?php echo "${email}" ?>)
<?php
		if (is_admin()) {
?>
			[Admin]
<?php
		}
?>
	<ul>
		<li><a href="?action=sign_out">Sign out<a/></li>
		<li><a href="?action=retrieve&amp;type=account">Account info<a/></li>
	</ul>
<?php
	} else {
?>
	<a href="?action=sign_in">Sign in</a><br/>
<?php
	}
?>
	</div>
</div>
<div class="hr"></div>