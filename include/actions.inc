<?php
	function action_authenticate() {
		global $g_error_msg;
		$g_error_msg = "";
		if (isset($_GET['login']) && isset($_GET['password'])) {
			if (authenticate($_GET['login'], $_GET['password'])) {
				$_SESSION['login'] = $_GET['login'];
				if (is_null_or_empty($_SESSION["url"])) {
					redirect_to("index.php");
				} else {
					$url = $_SESSION["url"];
					unset($_SESSION["url"]);
					redirect_to($url);
				}
			} else {
				$g_error_msg = _t("Wrong login or password");
			}
		}
		debug("END Action_Authenticate");
	}
	
	function action_signout() {
		unset($_SESSION["login"]);
		$_SESSION["state"] = "root";
	}
?>