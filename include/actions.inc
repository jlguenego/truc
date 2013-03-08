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

	function action_success_payment() {
		global $g_error_message;
		try {
			if ($_SESSION['state'] == "success_payment") {
				return;
			}
			$devis = $_SESSION['devis'];
			$devis->store();
			//send_success_payment_mail();
			$_SESSION['state'] = "success_payment";
		} catch (Exception $e) {
			$g_error_message = $e->getMessage();
			$_SESSION["state"] = "error";
		}
	}
?>