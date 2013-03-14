<?php
	function action_authenticate() {
		global $g_error_msg;
		if (isset($_GET['login']) && isset($_GET['password'])) {
			if (authenticate($_GET['login'], $_GET['password'])) {
				$user = get_user_by_login($_GET['login']);
				if (is_activated($user)) {
					$_SESSION['login'] = $_GET['login'];
					if (is_null_or_empty($_SESSION["url"])) {
						redirect_to("index.php");
					} else {
						$url = $_SESSION["url"];
						unset($_SESSION["url"]);
						redirect_to($url);
					}
				} else {
					$g_error_msg = _t("Your account is not activated");
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

	function action_activate_account() {
		return activate($_GET["key"]);
	}

	function action_participate() {
		global $g_display;

		if (!isset($_GET['confirm'])) {
			throw new Exception("You must read and accept the policies.");
		}

		$devis = new Devis();
		$devis->username = $_GET['username'];
		$devis->address = $_GET['address'];
		$devis->event_id = $_GET["event_id"];
		$rates = events_rates($_GET["event_id"]);
		$event = get_event($_GET["event_id"]);
		$total_ttc = 0.00;
		$i = 0;
		while (isset($_GET["ticket_${i}"])) {
			if (!is_number($_GET["ticket_${i}"])) {
				throw new Exception("Please enter a number for the amount of person");
			}
			if ($_GET["ticket_${i}"] > 0) {
				$item = new Item();

				$item->event_name = $event["title"];
				$item->event_rate_name = $rates[$i]["label"];
				$item->event_rate_amount = $rates[$i]["amount"];
				$item->event_rate_tax = $rates[$i]["tax_rate"];
				$item->quantity = $_GET["ticket_${i}"];

				$item->compute();

				$devis->items[] = $item;
			}
			$i++;
		}
		$devis->compute();
		$g_display["devis"] = $devis;
		$_SESSION["devis"] = $devis;
		$g_display["event_confirmed"] = $event["funding_wanted"] <= $event["funding_acquired"];
		$_SESSION["state"] = "participation_recapitulation";
	}

	function action_nominative_participate() {
		global $g_display;

		if (!isset($_GET['confirm'])) {
			throw new Exception("You must read and accept the policies.");
		}
		$devis = new Devis();
		$devis->username = $_GET['username'];
		$devis->address = $_GET['address'];
		$devis->event_id = $_GET["event_id"];
		$event = get_event($_GET["event_id"]);
		if (event_is_confirmed($event)) {
			$devis->status = DEVIS_STATUS_CAPTURED;
		} else {
			$devis->status = DEVIS_STATUS_AUTHORIZED;
		}

		$total_ttc = 0.00;

		$labels = $_GET['labels'];
		$amount_ht = $_GET['amount_ht'];
		$taxes = $_GET['taxes'];
		$lastnames = $_GET['lastnames'];
		$titles = $_GET['titles'];
		$i = 0;
		foreach ($_GET['firstnames'] as $firstname) {
			$item = new Item();

			$item->event_name = $event["title"];
			$item->event_rate_name = $labels[$i];
			$item->event_rate_amount = $amount_ht[$i];
			$item->event_rate_tax = $taxes[$i];
			$item->quantity = 1;
			$item->participant_firstname = $firstname;
			$item->participant_lastname = $lastnames[$i];
			$item->participant_title = $titles[$i];

			$item->compute();

			$devis->items[] = $item;
			$i++;
		}

		$devis->compute();

		$g_display["devis"] = $devis;
		$_SESSION["devis"] = $devis;
		$g_display["event_confirmed"] = $event["funding_wanted"] <= $event["funding_acquired"];
		$_SESSION["state"] = "participation_nominative_recapitulation";
	}

	function action_success_payment() {
		global $g_error_message;
		try {
			if ($_SESSION['state'] == "success_payment") {
				return;
			}
			$devis = $_SESSION['devis'];
			$devis->store();
			$user = get_user(get_id_from_account());
			debug($user["email"]);
			mail_payment($user["email"]);
			$_SESSION['state'] = "success_payment";
		} catch (Exception $e) {
			$g_error_message = $e->getMessage();
			$_SESSION["state"] = "error";
		}
	}

	function action_event_delete() {
		if (isset($_GET['confirm'])) {
			if (is_null_or_empty($_GET['id'])) {
				throw new Exception("No event given.");
			}
			if (!event_exists($_GET['id'])) {
				throw new Exception("Event does not exists.");
			}
			$event = get_event($_GET['id']);
			$user = get_user_by_login($_SESSION["login"]);
			event_check_owner($event);
			if($_GET['confirm'] != "yes") {
				redirect_to("index.php");
			} else {
				delete_events_rates($_GET['id']);
				event_delete($_GET['id']);
				redirect_to("index.php");
			}
		}
	}

	function action_supervision() {
		global $g_display;
		need_authentication();
		if (!is_admin()) {
			$g_error_msg = "You are not admin.";
			return;
		}
		$g_display["events"] = event_list();
		$_SESSION["state"] = "supervision";
	}

	function action_event_valid($update = FALSE) {
		if (is_null_or_empty($_GET["title"])) {
			throw new Exception("Event must have a title");
		}
		if (!$update && event_title_exists($_GET["title"])) {
			throw new Exception("Event already exists");
		}

		foreach ($_GET as $value) {
			if (is_null_or_empty($value)) {
				throw new Exception("Please fill all fields");
			}
		}

		foreach ($_GET['rates'] as $rate) {
			if (is_null_or_empty($rate)
				|| (!is_null_or_empty($rate) && !is_number($rate))) {

				throw new Exception("Please enter a number for the rates");
			}
		}

		foreach ($_GET['tax_rates'] as $tax_rate) {
			if (is_null_or_empty($tax_rate)
				|| (!is_null_or_empty($tax_rate) && !is_number($tax_rate))) {

				throw new Exception("Please enter a number for the tax rates");
			}
		}
		foreach ($_GET['labels'] as $label) {
			if (is_null_or_empty($label)) {
				throw new Exception("Please enter a label for each rate");
			}
		}
	}

	function action_get_form_account() {
		if (is_null_or_empty($_GET["id"])) {
		$_SESSION["state"] = "account_create";
		} else {
			$g_display["user"] = get_user($_GET["id"]);
			$_SESSION["state"] = "account_update";
		}
	}

	function action_get_form_event() {
		need_authentication();
		if (is_null_or_empty($_GET["id"])) {
			$_SESSION["state"] = "event_create";
		} else {
			$g_display["event"] = get_event($_GET["id"]);
			$g_display["rates"] = events_rates($_GET["id"]);
			$_SESSION["state"] = "event_update";
		}
	}

	function action_get_form_participation() {
		global $g_display;

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
	}

	function action_create_event() {
		global $g_error_msg;

		need_authentication();
		try {
			debug("Tax_rate array: ".sprint_r($_GET['tax_rates']));
			action_event_valid();
			$id = create_id();
			$nominative = 0;
			if (isset($_GET['nominative'])) {
				$nominative = 1;
			}
			event_create($id, $_GET['title'], $_GET['date'],
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
			redirect_to("?action=retrieve&type=event&id=${id}");
		} catch (Exception $e) {
			$g_error_msg = $e->getMessage();
			$_SESSION["state"] = "event_create";
		}
	}

	function action_publish_event() {
		global $g_error_msg;

		need_authentication();
		if (!is_admin()) {
			$g_error_msg = "You are not admin.";
			return;
		}
		if (!isset($_GET['id'])) {
			$g_error_msg = "No event selected.";
			return;
		}
		event_set_publish_flag($_GET['id'], EVENT_PUBLISH_FLAG_YES);
		redirect_to("?action=supervision");
	}

	function action_unpublish_event() {
		global $g_error_msg;

		need_authentication();
		if (!is_admin()) {
			$g_error_msg = "You are not admin.";
			return;
		}
		if (!isset($_GET['id'])) {
			$g_error_msg = "No event selected.";
			return;
		}
		event_set_publish_flag($_GET['id'], EVENT_PUBLISH_FLAG_NO);
		redirect_to("?action=supervision");
	}

	function action_confirm_event() {
		global $g_error_msg;

		need_authentication();
		if (!is_admin()) {
			$g_error_msg = "You are not admin.";
			return;
		}
		if (!isset($_GET['id'])) {
			$g_error_msg = "No event selected.";
			return;
		}
		event_set_status($_GET['id'], EVENT_STATUS_CONFIRMED);
		mail_event_confirm($_GET['id']);
		redirect_to("?action=supervision");
	}

	function action_cancel_event() {
		global $g_error_msg;

		need_authentication();
		if (!is_admin()) {
			$g_error_msg = "You are not admin.";
			return;
		}
		if (!isset($_GET['id'])) {
			$g_error_msg = "No event selected.";
			return;
		}
		event_set_status($_GET['id'], EVENT_STATUS_CANCELLED);
		redirect_to("?action=supervision");
	}
?>