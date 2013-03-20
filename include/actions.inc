<?php
	function action() {
		switch ($_GET["action"]) {
			case "sign_out":
				action_signout();
				break;
			case "sign_in":
				action_signin();
				break;
			case "get_form":
				action_get_form();
				break;
			case "create":
				action_create();
				break;
			case "retrieve":
				action_retrieve();
				break;
			case "update":
				action_update();
				break;
			case "delete":
				action_delete();
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
					if ($_SESSION["state"] == "nominative_participation"
						|| $_SESSION["state"] == "participation_nominative_recapitulation") {
						debug("nominative_participation");
						action_participate_nominative();
					} else {
						debug("regular_participation");
						action_participate();
					}
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
			case "payment_cancel":
				$_SESSION["state"] = "payment_cancel";
				break;
			case "payment_success":
				action_payment_success();
				break;
			case "supervision" :
				action_supervision();
				break;
			case "publish_event":
				action_event_publish();
				break;
			case "unpublish_event":
				action_event_unpublish();
				break;
			case "confirm_event":
				action_event_confirm();
				break;
			case "cancel_event":
				action_event_cancel();
				break;
			default:
				break;
		}
	}

	function action_create() {
		switch ($_GET["type"]) {
			case "event":
				action_event_create();
				break;
			case "account":
				action_account_create();
				break;
			default:
				$_SESSION["state"] = "not_allowed";
				break;
		}
	}

	function action_retrieve() {
		switch ($_GET["type"]) {
			case "events":
				action_event_list();
				break;
			case "event":
				action_event_retrieve();
				break;
			case "account":
				action_account_retrieve();
				break;
			case "devis":
				action_devis_retrieve();
				break;
			default:
				$_SESSION["state"] = "not_allowed";
				break;
		}

	}

	function action_update() {
		switch ($_GET["type"]) {
			case "account":
				action_account_update();
				break;
			case "event":
				action_event_update();
				break;
			default:
				$_SESSION["state"] = "not_allowed";
				break;
		}
	}

	function action_delete() {
		switch ($_GET["type"]) {
			case "event":
				action_event_delete();
				break;
			default:
				$_SESSION["state"] = "not_allowed";
				break;
		}
	}

	function action_signin() {
		$_SESSION["state"] = "sign_in";
	}

	function action_signout() {
		unset($_SESSION["login"]);
		$_SESSION["state"] = "root";
	}

	function action_get_form() {
		switch ($_GET["type"]) {
			case "event":
				action_event_get_form();
				break;
			case "account":
				action_account_get_form();
				break;
			case "participation":
				action_participation_get_form();
				break;
		}
	}

	function action_account_create() {
		global $g_info_msg;
		global $g_error_msg;

		try {
			valid_user();
			add_user($_GET['firstname'], $_GET['lastname'], $_GET['login'],
				$_GET['password'], $_GET['email'], $_GET['address']);
			$g_info_msg = "Account successfully created. Check your email for activation.";
			$_SESSION["state"] = "root";
		} catch (Exception $e) {
			$g_error_msg = $e->getMessage();
		}
	}

	function action_account_retrieve() {
		global $g_display;

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
	}

	function action_account_update() {
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
	}

	function action_account_get_form() {
		global $g_display;

		if (is_null_or_empty($_GET["id"])) {
		$_SESSION["state"] = "account_create";
		} else {
			$g_display["user"] = get_user($_GET["id"]);
			$_SESSION["state"] = "account_update";
		}
	}

	function action_event_create() {
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
			$event = new Event();
			$event->id = $id;
			$event->title = $_GET['title'];
			$event->confirmation_t = $_GET['confirmation_t'];
			$event->happening_t = $_GET['happening_t'];
			$event->funding_needed = $_GET['funding_needed'];
			$event->location = $_GET['location'];
			$event->link = $_GET['link'];
			$event->description_short = $_GET['short_description'];
			$event->description_long = $_GET['long_description'];
			$event->nominative = $nominative;
			$event->user_id = get_id_from_account();
			$event->store();
			debug(sprint_r($event));

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

	function action_event_retrieve() {
		global $g_display;
		global $g_error_msg;

		if (!is_null_or_empty($_GET["id"])) {
			if (event_exists($_GET["id"])) {
				$event = new Event();
				$event->load($_GET["id"]);
				if (!$event->is_published() && !user_can_administrate_event($event)) {
					$_SESSION["state"] = "not_allowed";
					$g_error_msg = "This event is not published yet.";
				} else {
					$_SESSION["state"] = "event_retrieve";
					$g_display["event"] = $event;
					$g_display["author"] = get_user($event->user_id);
				}
			} else {
				$_SESSION["state"] = "not_allowed";
				$g_error_msg = "Event does not exists.";
			}
		} else {
			$_SESSION["state"] = "not_allowed";
			$g_error_msg = "No event selected.";
		}
	}

	function action_event_update() {
		global $g_display;
		global $g_error_msg;

		try {
			need_authentication();
			debug("event_id=".$_GET["id"]);
			if (!is_null_or_empty($_GET["id"])) {
				if (event_exists($_GET["id"])) {
					$_SESSION["state"] = "event_update";
					$g_display["event"] = get_event($_GET["id"]);
					$g_display["rates"] = events_rates($_GET["id"]);
					action_event_valid(TRUE);
					$event = new Event();
					$event->load($_GET["id"]);
					$event->id = $_GET["id"];
					$event->title = $_GET['title'];
					$event->confirmation_t = $_GET['confirmation_t'];
					$event->date = $_GET['happening_t'];
					$event->funding_needed = $_GET['funding_needed'];
					$event->location = $_GET['location'];
					$event->link = $_GET['link'];
					$event->description_short = $_GET['short_description'];
					$event->description_long = $_GET['long_description'];
					//$event->nominative = $_GET['nominative'];

					$event->check_owner();

					$event->update();

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
	}

	function action_event_delete() {
		global $g_display;
		global $g_error_msg;

		$event = new Event();
		$event->load($_GET["id"]);
		$g_display["event"] = $event;
		if ($_SESSION["state"] != "event_delete") {
			$_SESSION["state"] = "event_delete";
			return;
		}
		try {
			need_authentication();
			if (isset($_GET['confirm'])) {
				if (is_null_or_empty($_GET['id'])) {
					throw new Exception("No event given.");
				}
				if (!event_exists($_GET['id'])) {
					throw new Exception("Event does not exists.");
				}
				$event = new Event();
				$event->load($_GET['id']);
				//$user = get_user_by_login($_SESSION["login"]);
				$event->check_owner();
				if($_GET['confirm'] != "yes") {
					//redirect_to("index.php");
				} else {
					$event->delete();
					//redirect_to("index.php");
				}
			}
			$_SESSION["state"] = "root";
		} catch (Exception $e) {
			$_SESSION["state"] = "error";
			$g_error_msg = $e->getMessage();
		}
	}

	function action_event_get_form() {
		global $g_display;

		need_authentication();
		if (is_null_or_empty($_GET["id"])) {
			$_SESSION["state"] = "event_create";
		} else {
			$g_display["event"] = get_event($_GET["id"]);
			$g_display["rates"] = events_rates($_GET["id"]);
			$_SESSION["state"] = "event_update";
		}
	}

	function action_event_valid($update = FALSE) {
		if (is_null_or_empty($_GET["title"])) {
			throw new Exception("Event must have a title");
		}
		if (!$update && event_title_exists($_GET["title"])) {
			throw new Exception("Event already exists");
		}
		if (!check_date($_GET["happening_t"])) {
			throw new Exception("The given date is wrong");
		}
		if (!check_date($_GET["confirmation_t"])) {
			throw new Exception("Please choose a date for your event");
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

	function action_event_publish() {
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

	function action_event_unpublish() {
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

	function action_event_confirm() {
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
		devis_all_set_status_for_event($_GET['id'], DEVIS_STATUS_CAPTURED);
		mail_event_confirm($_GET['id']);
		redirect_to("?action=supervision");
	}

	function action_event_list() {
		global $g_display;
		$g_display["events"] = event_list();
		$_SESSION["state"] = "event_list";
	}

	function action_event_cancel() {
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
		mail_event_cancel($_GET['id']);
		redirect_to("?action=supervision");
	}

	function action_devis_retrieve() {
		global $g_display;

		if (!isset($_GET['id'])) {
			throw new Exception("No devis given.");
		}
		if (!devis_exists($_GET['id'])) {
			throw new Exception("Devis does not exists.");
		}
		$devis = new Devis();
		$devis->load($_GET['id']);

		$event = new Event();
		$event->load($devis->event_id);

		$g_display["devis"] = $devis;
		$g_display["event"] = $event;
		$_SESSION["state"] = "devis_retrieve";
	}

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
		$event = new Event();
		$event->load($_GET["event_id"]);
		$total_ttc = 0.00;
		$i = 0;
		while (isset($_GET["ticket_${i}"])) {
			if (!is_number($_GET["ticket_${i}"])) {
				throw new Exception("Please enter a number for the amount of person");
			}
			if ($_GET["ticket_${i}"] > 0) {
				$item = new Item();

				$item->event_name = $event->title;
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
		$g_display["event_confirmed"] = $event->is_confirmed();
		$_SESSION["state"] = "participation_recapitulation";
	}

	function action_participate_nominative() {
		global $g_display;

		if (!isset($_GET['confirm'])) {
			throw new Exception("You must read and accept the policies.");
		}
		$devis = new Devis();
		$devis->username = $_GET['username'];
		$devis->address = $_GET['address'];
		$devis->event_id = $_GET["event_id"];
		$event = new Event();
		$event->load($_GET["event_id"]);
		if ($event->is_confirmed()) {
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

			$item->event_name = $event->title;
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
		$g_display["event_confirmed"] = $event->is_confirmed();
		$_SESSION["state"] = "participation_nominative_recapitulation";
	}

	function action_participation_get_form() {
		global $g_display;
		global $g_error_msg;

		need_authentication();
		if (!is_null_or_empty($_GET["event_id"])) {
			$event = new Event();
			$event->load($_GET["event_id"]);
			if (!$event->is_published()) {
				$_SESSION["state"] = "not_allowed";
				$g_error_msg = "This event is not published yet.";
				return;
			}
			$g_display["user"] = get_user(get_id_from_account());
			$g_display["event"] = $event;
			$g_display["rates"] = events_rates($_GET["event_id"]);
			if ($g_display["event"]->nominative == 1) {
				$_SESSION["state"] = "nominative_participation";
			} else {
				$_SESSION["state"] = "regular_participation";
			}
		} else {
			$_SESSION["state"] = "not_allowed";
			$g_error_msg = "No event selected.";
		}
	}

	function action_payment_success() {
		global $g_error_message;
		try {
			if ($_SESSION['state'] == "payment_success") {
				return;
			}
			$devis = $_SESSION['devis'];
			$devis->store();
			$user = get_user(get_id_from_account());
			debug($user["email"]);
			mail_payment($user["email"], $devis);
			$_SESSION['state'] = "payment_success";
		} catch (Exception $e) {
			$g_error_message = $e->getMessage();
			$_SESSION["state"] = "error";
		}
	}

	function action_supervision() {
		global $g_display;
		need_authentication();
		if (!is_admin()) {
			$g_display["events"] = event_list(get_id_from_account());
			$_SESSION["state"] = "supervision";
			return;
		}
		$g_display["events"] = event_list();
		$_SESSION["state"] = "supervision";
	}
?>