<?php
	function action() {
		if (action_entity_execute()) {
			return;
		}
		switch ($_GET["action"]) {
			case "request_for_publication":
				action_request_for_publication();
				break;
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
						$g_error_msg = _t("Action not permitted from the state we are.");
						break;
				}
				break;
			case "participate":
				action_participate();
				break;
			case "activation":
				action_activation();
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
			case "promote_event":
				action_promote_event();
				break;
			case "handle_forgotten_password":
				action_handle_forgotten_password();
				break;
			case "handle_reset_password":
				action_handle_reset_password();
				break;
			case "list":
				action_list();
				break;
			case "display":
				action_display();
				break;
			case "set_locale":
				action_set_locale();
				break;
			case "merge":
				action_merge();
			case "manage":
				action_manage();
				break;
			case "none":
				break;
			default:
				throw new Exception(_t("This action does not exists."));
				break;
		}
	}

	function action_entity_execute() {
		if (isset($_GET["type"])) {
			$type = $_GET["type"];
			if (dd()->has_entity($type)) {
				$e = dd()->get_entity($type);
				if ($e->is_valid_action($_GET["action"])) {
					$e->execute_action($_GET["action"]);
					if (TEST_MODE) {
						action_manage();
					} else {
						redirect_to("?action=manage&type=".$_GET["type"]);
					}
					return true;
				}
			}
		}
		return false;
	}

	function action_manage() {
		global $g_display;

		if (!isset($_GET["type"])) {
			throw new Exception("No type specified.");
		}
		$type = $_GET["type"];
		if (!dd()->has_entity($type)) {
			throw new Exception("Bad type: ".$type);
		}
		$g_display["type"] = $type;
		$_SESSION["state"] = "record_manage";
	}

	function action_merge() {
		switch ($_GET["type"]) {
			case "guest_file":
				action_guest_file_merge();
				break;
			default:
				$_SESSION["state"] = "not_allowed";
				break;
		}
	}

	function action_guest_file_merge() {
		// take the file content and create guest if not existing.
		$event = Event::get_from_id($_SESSION["event_id"]);
		$file = Form::get_file("guest_filename");
		$event->import_guest($file);
		if (TEST_MODE) {
			action_guest_file_get_form();
		} else {
			redirect_to("?action=get_form&type=guest_file");
		}
	}

	function action_promote_event() {
		global $g_display;
		global $g_error_msg;

		$user = need_authentication();
		if (is_null_or_empty($_GET["id"])) {
			throw new Exception(_t("No event selected."));
		}
		if (!Event::exists($_GET["id"])) {
			throw new Exception(_t("Event does not exists."));
		}
		$event = Event::get_from_id($_GET["id"]);

		if (!$event->can_be_administrated()) {
			throw new Exception(_t("You cannot access to this event."));
		}
		$g_display["event"] = $event;
		$user = User::get_from_id($event->user_id);
		$g_display["author"] = $user;
		$_SESSION["state"] = "event_promote";
		$_SESSION["event_id"] = $event->id;
	}

	function action_set_locale() {
		global $g_info_msg, $g_locales;
		if (!in_array($_GET["locale"], $g_locales)) {
			throw new Exception(_t("unknown locale: ").$_GET["locale"]);
		}
		$_SESSION["locale"] = $_GET["locale"];
		// get the page we come from.
		redirect_to($_SERVER['HTTP_REFERER']);
	}

	function action_display() {
		global $g_page;
		$g_page = $_GET["page"];
	}

	function action_list() {
		switch ($_GET["type"]) {
			case "event":
				action_event_list();
				break;
			case "participation":
				action_participation_list();
				break;
			default:
				$_SESSION["state"] = "not_allowed";
				break;
		}
	}

	function action_create() {
		debug("action_create()");
		switch ($_GET["type"]) {
			case "event":
				action_event_create();
				break;
			case "account":
				action_account_create();
				break;
			default:
				if (dd()->has_entity($_GET["type"])) {
					action_record_create();
					break;
				}
				$_SESSION["state"] = "not_allowed";
		}
	}

	function action_retrieve() {
		switch ($_GET["type"]) {
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
				throw new Exception("Cannot retrieve object of type ".$_GET["type"]);
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
			case "account":
				action_account_delete();
				break;
		}
		debug("type=".$_GET["type"]);
		if (dd()->has_entity($_GET["type"])) {
			action_record_delete();
			return;
		}
		$_SESSION["state"] = "not_allowed";
	}

	function action_signin() {
		$_SESSION["state"] = "sign_in";
	}

	function action_signout() {
		$_SESSION["user_id"] = null;
		debug("SESSION=".sprint_r($_SESSION));
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
			case "forgotten_password":
				action_forgotten_password_get_form();
				break;
			case "reset_password":
				action_reset_password_get_form();
				break;
			case "guest_file":
				action_guest_file_get_form();
				break;
			default:
				throw new Exception("Cannot get form for type ".$_GET["type"]);
		}
	}

	function action_guest_file_get_form() {
		global $g_display;
		$event = Event::get_from_id($_SESSION["event_id"]);
		$g_display["guests"] = $event->get_guests();
		$_SESSION["state"] = "guest_file_form";
	}

	function action_record_create() {
		global $g_info_msg;
		global $g_error_msg;

		debug(sprint_r($_GET));

		try {
			$record = new Record($_GET["type"]);
			$record->check_form();
			$record->id = create_id();
			$record->hydrate_from_form();
			$record->store();
			$g_info_msg = _t("Record successfully created.");
			if (TEST_MODE) {
				action_manage();
			} else {
				redirect_to("?action=manage&type=".$_GET["type"]);
			}
		} catch (Exception $e) {
			$g_error_msg = $e->getMessage();
		}
	}

	function action_record_delete() {
		global $g_info_msg;
		global $g_error_msg;

		debug(sprint_r($_GET));
		debug("Delete record");

		try {
			$record = Record::get_from_id($_GET["type"], $_GET["id"]);
			$record->delete();
			$g_info_msg = _t("Record successfully deleted.");
			if (TEST_MODE) {
				action_manage();
			} else {
				redirect_to("?action=manage&type=".$_GET["type"]);
			}
		} catch (Exception $e) {
			$g_error_msg = $e->getMessage();
		}
	}

	function action_account_create() {
		global $g_info_msg;
		global $g_error_msg;

		debug(sprint_r($_GET));

		try {
			form_valid_user();
			$user = new User();
			$user->id = create_id();
			$user->hydrate_from_form();
			$user->locale = $_SESSION["locale"];
			$user->role = ROLE_USER;
			$user->activation_status = ACTIVATION_STATUS_TO_BE_ACTIVATED;
			$user->generate_activation_key();
			$user->store();
			$g_info_msg = _t("Account successfully created. Check your email for activation.");
			$_SESSION["state"] = "root";
		} catch (Exception $e) {
			$g_error_msg = $e->getMessage();
		}
	}

	function action_account_retrieve() {
		global $g_display;

		$user = need_authentication();
		$g_display["user"] = $user;
		$g_display["events_organized"] = $user->get_organized_events();
		$_SESSION["state"] = "account_retrieve";
	}

	function action_account_update() {
		global $g_display;
		global $g_error_msg;
		global $g_info_msg;
		try {
			need_authentication();
			if (is_null_or_empty($_GET["id"])) {
				throw new Exception(_t("No id given."));
			}
			if (User::exists($_GET["id"])) {
				$user = User::get_from_id($_GET["id"]);
				form_valid_user_update($user);
				$user->locale = $_SESSION["locale"];
				$user->update();

				$g_info_msg = _t("Your personal informations has been changed.");
				if (!is_null_or_empty($_GET["new_pass"])) {
					$g_info_msg .= _t(" Your password has changed. We just sent you a mail for information.");
					debug("password changed => mail send");
					mail_password_changed($user);
				}
				action_account_retrieve();
			} else {
				throw new Exception(_t("This user does not exists."));
			}
		} catch (Exception $e) {
			$g_error_msg = $e->getMessage();
			$g_display["user"] = $user;
			$_SESSION["state"] = "account_update";
		}
	}

	function action_account_delete() {
		global $g_display;
		global $g_error_msg;
		global $g_info_msg;

		$user = User::get_from_id($_GET["id"]);
		$g_display["user"] = $user;
		if ($_SESSION["state"] != "account_delete_confirm") {
			$_SESSION["state"] = "account_delete_confirm";
			return;
		}
		try {
			need_authentication();
			if (isset($_GET['confirm'])) {
				if (is_null_or_empty($_GET['id'])) {
					throw new Exception(_t("No account given."));
				}
				if (!User::exists($_GET['id'])) {
					throw new Exception(_t("Account does not exists."));
				}
				$user = User::get_from_id($_GET['id']);
				if (!$user->check_owner()) {
					debug(sprint_r($_SESSION));
					throw new Exception(_t("This account is not yours."));
				}
				if($_GET['confirm'] == "yes") {
					$user->delete_try();
				}
			}
			$g_info_msg = _t("Account successfully deleted");
			action_signout();
		} catch (Exception $e) {
			$_SESSION["state"] = "error";
			$g_error_msg = $e->getMessage();
		}
	}

	function action_account_get_form() {
		global $g_display;

		if (is_null_or_empty($_GET["id"])) {
			$_SESSION["state"] = "account_create";
		} else {
			$user = User::get_from_id($_GET["id"]);
			$g_display["user"] = $user;
			$_SESSION["state"] = "account_update";
		}
	}

	function action_event_create() {
		global $g_error_msg;

		$user = need_authentication();
		try {
			debug("Tax_rate array: ".sprint_r($_GET['ticket_tax_a']));
			form_event_valid();

			$event = new Event();
			$event->hydrate_from_form();

			$event->user_id = User::get_id_from_account();
			$event->store();
			$user->sync_phone($event->phone);
			debug(sprint_r($event));

			debug("Ticket=".sprint_r($_GET['ticket_name_a']));
			$i = 0;
			foreach ($_GET['ticket_name_a'] as $label) {
				$ticket = new Ticket();
				$ticket->hydrate_from_form($i);
				$ticket->event_id = $event->id;
				$ticket->store();
				$i++;
			}
			$_GET["id"] = $event->id;
			if (TEST_MODE) {
				action_event_retrieve();
			} else {
				redirect_to("?action=retrieve&type=event&id=".$event->id);
			}
		} catch (Exception $e) {
			$g_error_msg = $e->getMessage();
			action_event_get_form();
		}
	}

	function action_event_retrieve() {
		global $g_display;
		global $g_error_msg;

		if (is_null_or_empty($_GET["id"])) {
			throw new Exception(_t("No event selected."));
		}
		if (!Event::exists($_GET["id"])) {
			throw new Exception(_t("Event does not exists."));
		}
		$event = Event::get_from_id($_GET["id"]);
		if (!$event->is_published()) {
			$user = need_authentication();
			if (!$event->can_be_administrated()) {
				throw new Exception(_t("This event is not yet published."));
			}
		}
		$_SESSION["state"] = "event_retrieve";
		$g_display["event"] = $event;
		$user = User::get_from_id($event->user_id);
		$g_display["author"] = $user;
	}

	function action_event_update() {
		global $g_display;
		global $g_error_msg;

		try {
			$user = need_authentication();
			debug("event_id=".$_GET["id"]);
			if (!is_null_or_empty($_GET["id"])) {
				if (Event::exists($_GET["id"])) {
					$_SESSION["state"] = "event_form";
					form_event_valid(TRUE);
					$event = Event::get_from_id($_GET["id"]);
					$event->hydrate_from_form();

					$event->check_owner();

					$event->update();
					$user->sync_phone($event->phone);

					$i = 0;
					foreach ($_GET['ticket_name_a'] as $name) {
						$ticket = $event->get_ticket($name);
						if ($ticket != NULL) {
							$ticket->hydrate_from_form($i);
							$ticket->event_id = $event->id;
							$ticket->update();
						} else {
							$ticket = new Ticket();
							$ticket->hydrate_from_form($i);
							$ticket->event_id = $event->id;
							$ticket->store();
						}
						$i++;
					}
					$event->delete_unused_tickets($_GET['ticket_name_a']);
					if (TEST_MODE) {
						action_event_retrieve();
					} else {
						redirect_to("?action=retrieve&type=event&id=".$event->id);
					}
				} else {
					action_event_create();
				}
			} else {
				throw new Exception(_t("No event selected."));
			}
		} catch (Exception $e) {
			$g_error_msg = $e->getMessage();
			action_event_get_form();
		}
	}

	function action_event_delete() {
		global $g_display;
		global $g_error_msg;
		global $g_info_msg;

		$user = need_authentication();

		$event = Event::get_from_id($_GET["id"]);
		if (!$event->can_be_administrated()) {
			throw new Exception(_t("Cannot delete an event if you cannot administrate it."));
		}
		$g_display["event"] = $event;
		if ($_SESSION["state"] != "event_delete") {
			$_SESSION["state"] = "event_delete";
			return;
		}
		try {
			if (!isset($_GET['confirm']) || $_GET['confirm'] != "yes") {
				$_SESSION["state"] = "root";
				$g_info_msg = _t("Event not deleted.");
				return;
			}
			if (is_null_or_empty($_GET['id'])) {
				throw new Exception(_t("No event given."));
			}
			if (!Event::exists($_GET['id'])) {
				throw new Exception(_t("Event does not exists."));
			}
			$event = Event::get_from_id($_GET['id']);
			$event->check_owner();
			if ((!$event->is_cancelled()) && ($event->has_already_happened())) {
				$event->set_status(EVENT_STATUS_CANCELLED);
				mail_event_cancel($_GET['id']);
			}
			$event->delete_try();
			$_SESSION["state"] = "root";
			$g_info_msg = _t("Event succesfully deleted.");
		} catch (Exception $e) {
			$_SESSION["state"] = "error";
			$g_error_msg = $e->getMessage();
		}
	}

	function action_event_get_form() {
		global $g_display;

		$user = need_authentication();
		if (is_null_or_empty($_GET["id"]) || $_GET["id"] == "-1") {
			$event = new Event();
			$event->load_default();
			$g_display["event"] = $event;
			$g_display["user"] = $user;
			$g_display["tickets"] = NULL;
			$g_display["scenario"] = "create";
			$g_display["form_action"] = "?action=create&amp;type=event";
			$_SESSION["state"] = "event_form";
		} else {
			$event = Event::get_from_id($_GET["id"]);
			if (!$event->can_be_administrated()) {
				throw new Exception(_t("Cannot update an event if you cannot administrate it."));
			}

			$g_display["event"] = $event;
			$g_display["user"] = $user;
			$g_display["tickets"] = $event->get_tickets();
			$g_display["scenario"] = "update";
			$g_display["form_action"] = "?action=update&amp;type=event&amp;id=".$_GET["id"];
			$_SESSION["state"] = "event_form";
		}
	}

	function action_event_publish() {
		global $g_error_msg;

		$user = need_authentication();
		if (!is_admin_logged()) {
			throw new Exception(_t("You are not admin."));
		}
		if (!isset($_GET['id'])) {
			throw new Exception(_t("No event selected."));
		}
		$event = Event::get_from_id($_GET['id']);
		$event->set_publish_flag(EVENT_PUBLISH_FLAG_YES);
		mail_notify_publication($event);
		if (TEST_MODE) {
			action_supervision();
		} else {
			redirect_to("?action=supervision");
		}
	}

	function action_event_unpublish() {
		global $g_error_msg;

		$user = need_authentication();
		if (!is_admin_logged()) {
			throw new Exception(_t("You are not admin."));
		}
		if (!isset($_GET['id'])) {
			throw new Exception(_t("No event selected."));
		}
		$event = Event::get_from_id($_GET['id']);
		$event->set_publish_flag(EVENT_PUBLISH_FLAG_NO);
		mail_notify_publication($event, $_GET["reason"]);
		if (TEST_MODE) {
			action_supervision();
		} else {
			redirect_to("?action=supervision");
		}
	}

	function action_event_confirm() {
		global $g_error_msg;

		need_authentication();
		if (!isset($_GET['id'])) {
			throw new Exception(_t("No event selected."));
		}
		$event = Event::get_from_id($_GET['id']);
		$event->set_status(EVENT_STATUS_CONFIRMED);
		$event->set_devis_status(DEVIS_STATUS_CONFIRMED);
		debug("Send Confirmation mail.");
		mail_event_confirm($_GET['id']);
		action_supervision();
	}

	function action_event_list() {
		global $g_display;
		$g_display["events"] = Event::list_all();
		$_SESSION["state"] = "event_list";
	}

	function action_event_cancel() {
		global $g_error_msg;

		need_authentication();
		if (!isset($_GET['id'])) {
			throw new Exception(_t("No event selected."));
		}
		$event = Event::get_from_id($_GET['id']);
		$event->set_status(EVENT_STATUS_CANCELLED);
		mail_event_cancel($_GET['id']);
		action_supervision();
	}

	function action_devis_retrieve() {
		global $g_display;

		$user = need_authentication();
		if (!isset($_GET['id'])) {
			throw new Exception(_t("No devis given."));
		}
		if (!Devis::exists($_GET['id'])) {
			throw new Exception(_t("Bill does not exists."));
		}
		$devis = Devis::get_from_id($_GET['id']);
		if (!$devis->can_be_retrieved()) {
			throw new Exception(_t("Cannot access to this object. Permission denied."));
		}
		$event = Event::get_from_id($devis->event_id);

		$g_display["devis"] = $devis;
		$g_display["event"] = $event;
		$_SESSION["state"] = "devis_retrieve";
	}

	function action_authenticate() {
		global $g_error_msg;
		if (isset($_GET['email']) && isset($_GET['password'])) {
			if (User::authenticate($_GET['email'], $_GET['password'])) {
				$user = User::get_from_email($_GET['email']);
				debug("user=".sprint_r($user));
				if ($user->is_activated()) {
					$_SESSION['user_id'] = $user->id;
					if (isset($_GET["redirect"])) {
						if (is_null_or_empty($_SESSION["url"])) {
							redirect_to("index.php");
						} else {
							$url = $_SESSION["url"];
							unset($_SESSION["url"]);
							redirect_to($url);
						}
					} else {
						redirect_to("index.php");
					}
				} else {
					$g_error_msg = _t("Your account is not activated");
				}
			} else {
				$g_error_msg = _t("Wrong email or password");
			}
		}
		debug("END Action_Authenticate");
	}

	function action_participate_regular() {
		global $g_display;

		if (!isset($_GET['confirm'])) {
			throw new Exception(_t("You must read and accept the policies."));
		}

		$devis = new Devis();
		$devis->username = $_GET['username'];
		$devis->address = nl2br($_GET['address']);
		$devis->vat = $_GET["vat"];
		$devis->event_id = $_GET["event_id"];
		$event = Event::get_from_id($_GET["event_id"]);
		$label = "Event-Biller-";
		if ($event->is_confirmed()) {
			$devis->status = DEVIS_STATUS_CONFIRMED;
			$devis->type = DEVIS_TYPE_INVOICE;
			$label .= "I-";
		} else {
			$devis->status = DEVIS_STATUS_PLANNED;
			$devis->type = DEVIS_TYPE_QUOTATION;
			$label .= "Q-";
		}
		$devis->label = $label."EVT-".sprintf("%06d", $event->id);

		$tickets = $event->get_tickets();
		$total_ttc = 0.00;
		$i = 0;
		while (isset($_GET["ticket_${i}"])) {
			if (!is_number($_GET["ticket_${i}"])) {
				throw new Exception(_t("Please enter a number for the amount of person"));
			}
			if ($_GET["ticket_${i}"] > 0) {
				$item = new Item();

				$item->event_name = $event->title;
				$item->event_rate_name = $tickets[$i]["label"];
				$item->event_rate_amount = $tickets[$i]["amount"];
				$item->event_rate_tax = $tickets[$i]["tax_rate"];
				$item->quantity = $_GET["ticket_${i}"];

				$item->compute();

				$devis->items[] = $item;
			}
			$i++;
		}
		$devis->compute();
		$g_display["devis"] = $devis;
		$_SESSION["devis"] = $devis;
		$g_display["event"] = $event;
		$g_display["event_confirmed"] = $event->is_confirmed();
		$_SESSION["state"] = "participation_recapitulation";
	}

	function action_participate_nominative() {
		global $g_display;

		if (!isset($_GET['confirm'])) {
			throw new Exception(_t("You must read and accept the policies."));
		}
		$devis = new Devis();
		$devis->username = $_GET['username'];
		$devis->address = nl2br($_GET['address']);
		$devis->vat = $_GET["vat"];
		$devis->event_id = $_GET["event_id"];
		$event = Event::get_from_id($_GET["event_id"]);
		$label = "Event-Biller-";
		if ($event->is_confirmed()) {
			$devis->status = DEVIS_STATUS_CONFIRMED;
			$devis->type = DEVIS_TYPE_INVOICE;
			$label .= "I-";
		} else {
			$devis->status = DEVIS_STATUS_PLANNED;
			$devis->type = DEVIS_TYPE_QUOTATION;
			$label .= "Q-";
		}
		$devis->label = $label."EVT-".sprintf("%06d", $event->id);

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
			$item->attendee_firstname = $firstname;
			$item->attendee_lastname = $lastnames[$i];
			$item->attendee_title = $titles[$i];

			$item->compute();

			$devis->items[] = $item;
			$i++;
		}

		$devis->compute();

		$g_display["devis"] = $devis;
		$_SESSION["devis"] = $devis;
		debug("devis=".sprint_r($devis));
		$g_display["event_confirmed"] = $event->is_confirmed();
		$g_display["event"] = $event;
		$_SESSION["state"] = "participation_nominative_recapitulation";
	}

	function action_participation_get_form() {
		global $g_display;
		global $g_error_msg;

		$user = need_authentication();
		if (is_null_or_empty($_GET["event_id"])) {
			throw new Exception(_t("No event selected."));
		}
		$event = Event::get_from_id($_GET["event_id"]);
		if (!$event->is_published()) {
			throw new Exception(_t("This event is not yet published."));
		}
		$g_display["user"] = $user;
		$g_display["event"] = $event;
		$g_display["tickets"] = $event->get_tickets();
		if ($g_display["event"]->type == EVENT_TYPE_NOMINATIVE) {
			$_SESSION["state"] = "nominative_participation";
		} else {
			$_SESSION["state"] = "regular_participation";
		}
	}

	function action_payment_success() {
		global $g_error_msg;
		$user = need_authentication();
		debug($user->email);
		try {
			if ((!TEST_MODE) && $_SESSION['state'] == "payment_success") {
				return;
			}
			$devis = $_SESSION['devis'];
			$devis->payment_info = $_SERVER["QUERY_STRING"];
			$devis->store();
			mail_payment($user->email, $devis);
			$_SESSION['state'] = "payment_success";
		} catch (Exception $e) {
			$g_error_msg = $e->getMessage();
			$_SESSION["state"] = "error";
		}
	}

	function action_supervision() {
		global $g_display;
		need_authentication();
		if (!is_admin_logged()) {
			$g_display["events"] = Event::list_all(User::get_id_from_account());
			$_SESSION["state"] = "supervision";
			return;
		}
		$g_display["events"] = Event::list_all();
		$_SESSION["state"] = "supervision";
	}

	function action_participate() {
		global $g_display;
		global $g_error_msg;

		$user = need_authentication();
		try {
			if ($user->has_no_address()) {
				$user->fill_address_from_participation();
			}
			if ($_SESSION["state"] == "nominative_participation"
				|| $_SESSION["state"] == "participation_nominative_recapitulation") {
				debug("nominative_participation");
				action_participate_nominative();
			} else {
				debug("regular_participation");
				action_participate_regular();
			}
		} catch (Exception $e) {
			$g_error_msg = $e->getMessage();
			$g_display["user"] = $user;
			$event = Event::get_from_id($_GET["event_id"]);
			$g_display["event"] = $event;
			$g_display["tickets"] = $event->get_tickets();
			if ($event->type == EVENT_TYPE_NOMINATIVE) {
				$_SESSION["state"] = "nominative_participation";
			} else {
				$_SESSION["state"] = "regular_participation";
			}
		}
	}

	function action_activation() {
		global $g_info_msg;
		global $g_error_msg;
		try {
			$user = User::activate($_GET["key"]);
			$g_info_msg = _t("Account successfully activated");
			$_GET["email"] = $user->email;
			$_SESSION["state"] = "sign_in";
		} catch (Exception $e) {
			$g_error_msg = $e->getMessage();
			$_SESSION["state"] = "error";
		}
	}

	function action_forgotten_password_get_form() {
		$_SESSION["state"] = "forgotten_password_form";
	}

	function action_handle_forgotten_password() {
		$_SESSION["state"] = "forgotten_password_mail_sent";
		if (is_null_or_empty($_GET["email"])) {
			return;
		}
		$user = User::get_from_email($_GET["email"]);
		if ($user == NULL) {
			return;
		}
		$token = $user->set_reset_password_token();
		mail_forgotten_password($user, $token);
	}

	function action_handle_reset_password() {
		global $g_error_msg;
		global $g_info_msg;

		if (is_null_or_empty($_GET["password1"]) || is_null_or_empty($_GET["password2"])) {
			$g_error_msg = _t("Please fill all field.");
			$_SESSION["state"] = "reset_password_form";
			return;
		}
		if ($_GET["password1"] != $_GET["password2"]) {
			$g_error_msg = _t("Passwords are different.");
			$_SESSION["state"] = "reset_password_form";
			return;
		}
		$user = need_authentication();
		$user->password = $_GET["password1"];
		$user->update();
		$user->force_activation();
		$user->reset_token();
		$g_info_msg = _t("Your password has been updated.");
		$_SESSION["state"] = "root";
	}

	function action_reset_password_get_form() {
		$user = User::get_from_token($_GET["permission_token"]);
		$_SESSION['user_id'] = $user->id;
		$_SESSION["state"] = "reset_password_form";
	}

	function action_participation_list() {
		global $g_display;

		$user = need_authentication();
		if (is_null_or_empty($_GET["id"])) {
			throw new Exception(_t("No event selected"));
		}
		$event = new Event();
		$event->load($_GET["id"]);
		if (!$event->can_be_administrated()) {
			throw new Exception(_t("Cannot view attendees of an event if you cannot administrate it."));
		}
		if ($event->type != EVENT_TYPE_NOMINATIVE) {
			throw new Exception(_t("This event cannot have attendee list."));
		}
		$_SESSION["state"] = "participation_list_nominative";
		$g_display["event"] = $event;
		$g_display["participations"] = $event->get_participations();
	}

	function action_request_for_publication() {
		if (is_null_or_empty($_GET["id"])) {
			throw new Exception(_t("No event selected"));
		}
		$event = Event::get_from_id($_GET["id"]);
		$event->set_ready_for_publication(true);
		mail_publication_request($event);
		action_event_retrieve();
	}
?>