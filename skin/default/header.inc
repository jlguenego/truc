<div id="evt_header">
	<div id="evt_logo">
		<a href="index.php"><img src="<?php echo SKIN_DIR ?>/images/logo.png"/></a>
	</div>
	<div id="evt_user_info">
<?php
	if (is_logged()) {
		$user = User::get_from_email($_SESSION['email']);
		$email = mb_strtolower($user->email, "UTF-8");
?>



		<?php echo $user->get_name() ?> - <?php echo "${email}" ?>
<?php
		if (is_admin_logged()) {
?>
			[Admin]
<?php
		}
?>
		<ul>
			<li><a href="?action=sign_out">Sign out</a></li>
			<li><a href="?action=retrieve&amp;type=account">Account info</a></li>
		</ul>
<?php
	} else {
?>
		<ul>
			<li><a href="?action=sign_in">Sign in</a></li>
		</ul>
		<a class="help" href="index.php?action=get_form&amp;type=account">Don't have an account ?</a>
<?php
	}
?>
	</div>
	<div id="evt_index"><a href="index.php">Main page</a></div>
</div>

<div class="evt_hr"></div>
