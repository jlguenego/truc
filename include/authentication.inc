<?php
	require_once(BASE_DIR . '/include/misc.inc');

	function is_logged() {
		if (!is_null_or_empty($_SESSION['login'])) {
			if (User::get_from_login($_SESSION['login']) != null) {
				return TRUE;
			} else {
				unset($_SESSION['login']);
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}

	function need_authentication() {
		if (!is_logged()) {
			debug("\$_SERVER[\"REQUEST_URI\"]=".$_SERVER["REQUEST_URI"]);
			$_SESSION["url"] = $_SERVER["REQUEST_URI"];
			redirect_to("?action=sign_in&redirect=yes");
		}
	}

	function is_admin_logged() {
		if (!is_logged()) {
			return FALSE;
		}
		$user = User::get_from_login($_SESSION['login']);
		return $user->role == ROLE_ADMIN;
	}
?>
