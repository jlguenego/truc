<?php
	require_once(BASE_DIR . '/include/misc.inc');

	function is_logged() {
		if (!is_null_or_empty($_SESSION['user_id'])) {
			if (User::get_from_id() != null) {
				return TRUE;
			} else {
				unset($_SESSION['user_id']);
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
			redirect_to(HOST."?action=sign_in");
		} else {
			return User::get_from_session();
		}
	}

	function is_admin_logged() {
		if (!is_logged()) {
			return FALSE;
		}
		$user = need_authentication();
		return $user->role == ROLE_ADMIN;
	}
?>
