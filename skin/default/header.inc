<div id="evt_header_top"></div>
<div id="evt_header">
	<div id="evt_i18n"><?php I18n::menu(); ?></div>
	<div id="evt_header_table">
		<div id="evt_logo">
			<a href="index.php"><img src="<?php echo SKIN_DIR ?>/images/logo.png"/></a>
		</div>
		<div id="evt_user_info">
<?php
	if (is_logged()) {
		$user = User::get_from_id();
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
		<br/>
		<a href="?action=sign_out">{{Sign out}}</a>&nbsp;|&nbsp;<a href="?action=retrieve&amp;type=account">{{Account info}}</a>
<?php
	} else {
?>
		<a href="?action=sign_in">{{Sign in}}</a> | <a href="index.php?action=get_form&amp;type=account">{{Create account}}</a>
<?php
	}
?>
		</div>
	</div>
	<div id="evt_header_menu">
		<ul>
			<li><a href=".">{{Home}}</a></li>
			<li><a href="info/about_us">{{About us}}</a></li>
			<li><a href="info/what_we_do">{{What we do}}</a></li>
			<li><a href="info/contact">Contact</a></li>
<?php
		if (is_admin_logged()) {
?>
			<li><a href="uninstall.php">Uninstall</a></li>
<?php
		}
?>
		</ul>
	</div>
</div>
