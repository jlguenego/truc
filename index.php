<?php
	require_once("include/constants.inc");
	require_once("include/globals.inc");
	require_once("include/misc.inc");
	require_once("include/layout.inc");
	require_once("include/user.inc");
	require_once("include/event.inc");
	require_once("include/rate.inc");
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
		case "get_form":
			switch ($_GET["type"]) {
				case "event":
					need_authentication();
					if (is_null_or_empty($_GET["id"])) {
						$_SESSION["state"] = "event_create";
					} else {
						$g_display["event"] = get_event($_GET["id"]);
						$g_display["rates"] = events_rates($_GET["id"]);
						$_SESSION["state"] = "event_update";
					}
					break;
				case "account":
					$_SESSION["state"] = "account_create";
					break;
			}
			break;
		case "create":
			switch ($_GET["type"]) {
				case "event":
					need_authentication();	
					try {
						valid_event();
						$id = create_id();
						add_event($id, $_POST['title'], $_POST['content'],
							$_POST['date'], $_POST['person']);
							
						$i = 0;
						foreach ($_POST['labels'] as $label) {
							$rate = $_POST['rates'][$i];
							add_rate($label, $rate, $id);
							$i++;
						}
						// $_GET["id"] = $id;
						// $_SESSION["state"] = "event_retrieve";
						redirect_to("?action=retrieve&type=event&id=${id}");
					} catch (Exception $e) {
						$g_error_msg = $e->getMessage();
						$_SESSION["state"] = "event_create";
					}
					break;
				case "account":
					try {
						valid_user();
						add_user($_GET['firstname'], $_GET['lastname'], $_GET['login'],
							$_GET['password'], $_GET['email']);
						action_authenticate();
						$_SESSION["state"] = "root";
					} catch (Exception $e) {
						$g_error_msg = $e->getMessage();
					}
					break;
				default:
					$_SESSION["state"] = "not_allowed";
					break;
			}
			break;
		case "retrieve":
			switch ($_GET["type"]) {
				case "events":
					$g_display["events"] = list_events();
					$_SESSION["state"] = "events_list";
					break;
				case "event":
					if (!is_null_or_empty($_GET["id"])) {
						if (event_exists($_GET["id"])) {
							$_SESSION["state"] = "event_retrieve";
							$g_display["event"] = get_event($_GET["id"]);
							$g_display["author"] = get_user($g_display["event"]["id_user"]);
						} else {
							$_SESSION["state"] = "not_allowed";
							$g_error_msg = "Event does not exists.";
						}
					} else {
						$_SESSION["state"] = "not_allowed";
						$g_error_msg = "No event selected.";
					}
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
					try {
						need_authentication();
						if (!is_null_or_empty($_GET["id"])) {
							if (event_exists($_GET["id"])) {
								$_SESSION["state"] = "event_update";
								$g_display["event"] = get_event($_GET["id"]);
								check_owner($g_display["event"]);
								valid_event();
								update_event($_GET["id"], $_GET['title'], 
									$_GET['content'], $_GET['date'], $_GET['persons']);
								$i = 0;
								foreach ($_GET['labels'] as $label) {
									$rate = $_GET['rates'][$i];
									update_rate($_GET["id"], $label, $rate);
									$i++;
								}
								delete_unuse_rates($_GET["id"], $_GET['labels']);
								redirect_to("?action=retrieve&type=event&id=".$_GET["id"]);
							} else {
								redirect_to("?action=create&type=event");
							}
						} else {
							$_SESSION["state"] = "not_allowed";
							$g_error_msg = "No event selected.";
						}
					} catch (Exception $e) {
						$g_error_msg = $e->getMessage();
						$_SESSION["state"] = "event_update";
					}
					break;
				default:
					$_SESSION["state"] = "not_allowed";
					break;
			}
			break;
		case "delete":
			switch ($_GET["type"]) {
				case "event":
					$g_display["event"] = get_event($_GET["id"]);
					if ($_SESSION["state"] == "event_delete") {
						try {
							need_authentication();
							delete_event();
							$_SESSION["state"] = "root";
						} catch (Exception $e) {
							$g_error_msg = $e->getMessage();
						}
					} else {
						$_SESSION["state"] = "event_delete";
					}
					break;
				default:
					$_SESSION["state"] = "not_allowed";
					break;
			}
			break;
		case "authenticate":
			switch ($_SESSION["state"]) {
				case "sign_in":
					action_authenticate();
					break;
				default:
					$_SESSION["state"] = "not_allowed";
					$g_error_msg = "Action not permitted from the state we are.";
					break;
			}
			break;
		default:
			break;
	}
	
	if (!in_array($_SESSION["state"], $g_states)) {
		$g_error_msg = "Undeclared state: ".$_SESSION["state"].".";
		$_SESSION["state"] = "not_allowed";
		
	}
	$page = $_SESSION["state"];
	
	debug("Session after: ".$_SESSION["state"]);
	debug("<hr/>");
	include_once(SKIN_DIR."/${page}.php");
?>