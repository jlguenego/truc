<div id="header">
	<a href="index.php">Go back to index</a><br/><br/>
<?php
	if (is_logged()) {
		$user = get_user_by_login($_SESSION['login']);
		$login = $user["login"];
		$lastname = strtoupper($user["lastname"]);
		$firstname = ucfirst($user["firstname"]);
?>
	<div id="user_info">


		Welcome <?php echo $login ?><br/>
		You are <?php echo "${firstname} ${lastname}" ?>
<?php
		if (is_admin()) {
?>
			, role Admin.
<?php
		}
?>
	<br/>
	<a href="?action=sign_out">Sign out<a/><br/>
	</div>
<?php
	} else {
?>
	<a href="?action=sign_in">Sign in</a><br/>
<?php
	}
?>
<hr/>
</div>