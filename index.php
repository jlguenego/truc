<?php
	require_once("include/constants.inc");
	require_once("include/globals.inc");
	require_once("include/misc.inc");
	require_once("include/layout.inc");
	require_once("include/user.inc");
	require_once("include/event.inc");
	require_once("include/rate.inc");
	require_once("include/actions.inc");
	require_once("include/manage.inc");
	require_once("include/payment.inc");

	// If not installed, goto installation page
	if (!is_installed()) {
		redirect_to("install.php");
	}

	if (!admin_exists()) {
		redirect_to("deploy.php");
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
					if (is_null_or_empty($_GET["id"])) {
					$_SESSION["state"] = "account_create";
					} else {
						$g_display["user"] = get_user($_GET["id"]);
						$_SESSION["state"] = "account_update";
					}
					break;
				case "participation":
					need_authentication();
					if (!is_null_or_empty($_GET["event_id"])) {
						$g_display["user"] = get_user(get_id_from_account());
						$g_display["event"] = get_event($_GET["event_id"]);
						$g_display["rates"] = events_rates($_GET["event_id"]);
						if ($g_display["event"]["nominative"] == 1) {
							$_SESSION["state"] = "nominative_participation";
						} else {
							$_SESSION["state"] = "regular_participation";
						}
					} else {
						$_SESSION["state"] = "not_allowed";
						$g_error_msg = "No event selected.";
					}
					break;
			}
			break;
		case "create":
			switch ($_GET["type"]) {
				case "event":
					need_authentication();
					try {
						debug("Tax_rate array: ".sprint_r($_GET['tax_rates']));
						valid_event();
						$id = create_id();
						$nominative = 0;
						if (isset($_GET['nominative'])) {
							$nominative = 1;
						}
						add_event($id, $_GET['title'], $_GET['date'],
							$_GET['deadline'], $_GET['funding_wanted'],
							$_GET['location'], $_GET['link'],
							$_GET['short_description'], $_GET['long_description'],
							$nominative);

						$i = 0;
						foreach ($_GET['labels'] as $label) {
							$rate = $_GET['rates'][$i];
							$tax_rate = $_GET['tax_rates'][$i];
							add_rate($label, $rate, $tax_rate, $id);
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
							$_GET['password'], $_GET['email'], $_GET['address']);
						$g_info_msg = "Account successfully created. Check your email for activation.";
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
					$_GET["id"] = get_id_from_account();
					$g_display["user"] = get_user($_GET["id"]);
					$g_display["events_organized"] = user_events($_GET["id"]);
					$g_display["participations"] = user_participations($_GET["id"]);
					foreach ($g_display["participations"] as $participation) {
						$g_display["participations"]["event"] =
							get_event($participation["id_event"]);
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
					try {
						need_authentication();
						if (is_null_or_empty($_GET["id"])) {
							$_GET["id"] = get_id_from_account();
						}
						if (user_exists($_GET["id"])) {
							$g_display["user"] = get_user($_GET["id"]);
							valid_user_update();
							update_user($user['id'], $user['password'],
								$user['email']);
							redirect_to("?action=retrieve&type=account&id=".$_GET["id"]);
						} else {
							throw new Exception(_t("This user does not exists."));
						}
					} catch (Exception $e) {
						$g_error_msg = $e->getMessage();
						$_SESSION["state"] = "account_update";
					}
					break;
				case "event":
					try {
						need_authentication();
						debug("event_id=".$_GET["id"]);
						if (!is_null_or_empty($_GET["id"])) {
							if (event_exists($_GET["id"])) {
								$_SESSION["state"] = "event_update";
								$g_display["event"] = get_event($_GET["id"]);
								$g_display["rates"] = events_rates($_GET["id"]);
								check_owner($g_display["event"]);
								valid_event(TRUE);
								debug("PLOUF");
								update_event($_GET["id"], $_GET['title'],
									$_GET['content'], $_GET['date'], $_GET['funding_wanted']);
								$i = 0;
								foreach ($_GET['labels'] as $label) {
									$rate = $_GET['rates'][$i];
									$tax_rate = $_GET['tax_rates'][$i];
									update_rate($_GET["id"], $label, $tax_rate, $rate);
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
		case "participate":
			need_authentication();
			try {
				action_participate();
			} catch (Exception $e) {
				$g_error_msg = $e->getMessage();
				$g_display["user"] = get_user(get_id_from_account());
				$g_display["event"] = get_event($_GET["event_id"]);
				$g_display["rates"] = events_rates($_GET["event_id"]);
				if ($g_display["event"]["nominative"] == 1) {
					$_SESSION["state"] = "nominative_participation";
				} else {
					$_SESSION["state"] = "regular_participation";
				}
			}
			break;
		case "activation":
			try {
				$user = user_activate($_GET["key"]);
				$g_info_msg = "Account successfully activated";
				$_GET["login"] = $user["login"];
				$_SESSION["state"] = "sign_in";
			} catch (Exception $e) {
				$g_error_msg = $e->getMessage();
				$_SESSION["state"] = "error";
			}
			break;
		case "cancel_payment":
			$_SESSION["state"] = "cancel_payment";
			break;
		case "success_payment":
			action_success_payment();
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
	include_once(SKIN_DIR."/layout.php");
?>