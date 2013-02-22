<?php
	require_once("include/constants.inc");
	require_once("include/globals.inc");
	require_once("include/misc.inc");
	require_once("include/user.inc");
	require_once("include/actions.inc");
	
	// If not installed, goto installation page
	if (!is_installed()) {
		redirect_to("install.php");
	}
	
	if (!admin_exists()) {
		redirect_to("createadmin.php");
	}
	
	// If installed
	require_once(SETTINGS_INI);
	
	session_start();
		
	$_GET = array_merge($_GET, $_POST);
	if (is_null_or_empty($_SESSION["state"])) {
		$_SESSION["state"] = "root";
	}
	debug("Session before: ".$_SESSION["state"]);
	
	if (is_null_or_empty($_GET["action"])) {
		$_SESSION["state"] = "root";
		$_GET["action"] = "none";
	}
	
	switch ($_GET["action"]) {
		case "sign_out":
			action_signout();
			break;
		case "sign_in":
			$_SESSION["state"] = "sign_in";
			break;
		case "create":
			switch ($_GET["type"]) {
				case "event":
					need_authentication();
					$_SESSION["state"] = "event_create";
					break;
				case "account":
					need_authentication();
					$_SESSION["state"] = "account_create";
					break;
				default:
					$_SESSION["state"] = "not_allowed";
					break;
			}
			break;
		case "retrieve":
			switch ($_GET["type"]) {
				case "events":
					$_SESSION["state"] = "events_list";
					break;
				case "event":
					$_SESSION["state"] = "event_retrieve";
					break;
				case "account":
					need_authentication();
					if (is_null_or_empty($_GET["id"])) {
						$_GET["id"] = get_id_from_account();
					}
					$_SESSION["state"] = "account_retrieve";
					break;
				default:
					$_SESSION["state"] = "not_allowed";
					break;
			}
			break;
		case "update":
			switch ($_GET["type"]) {
				case "account":
					need_authentication();
					if (is_null_or_empty($_GET["id"])) {
						$_GET["id"] = get_id_from_account();
					}
					$_SESSION["state"] = "account_update";
					break;
				case "event":
					need_authentication();
					$_SESSION["state"] = "event_update";
					break;
				default:
					$_SESSION["state"] = "not_allowed";
					break;
			}
			break;
		case "delete":
			switch ($_GET["type"]) {
				case "event":
					need_authentication();
					$_SESSION["state"] = "event_delete";
					break;
				default:
					$_SESSION["state"] = "not_allowed";
					break;
			}
			break;
		default:
			switch ($_SESSION["state"]) {
				case "sign_in":
					switch ($_GET["action"]) {
						case "authenticate":
								action_authenticate();
								break;
						default:
							$_SESSION["state"] = "root";
							break;
					}
					break;
				case "not_allowed":
					switch ($_GET["action"]) {
						default:
							$_SESSION["state"] = "not_allowed";
							break;
					}
					break;
			}
	}
	
	if (!in_array($_SESSION["state"], $g_states)) {
		$g_error_msg = "Undeclared state: ".$_SESSION["state"].".";
		$_SESSION["state"] = "not_allowed";
		
	}
	
	switch ($_SESSION["state"]) {
		default:
			$page = $_SESSION["state"];
			break;
	}
	
	debug("Session after: ".$_SESSION["state"]);
	debug("<hr/>");
	include_once("content/${page}.php");
?>