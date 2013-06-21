<?php
	class Event {
		public $id = -1;
		public $title;
		public $organizer_name;
		public $happening_t;
		public $confirmation_t = "";
		public $funding_needed;
		public $funding_acquired;
		public $location;
		public $link;
		public $phone;
		public $short_description;
		public $long_description;
		public $type;
		public $status = EVENT_STATUS_PLANNED;
		public $flags;
		public $publish_flag;
		public $facebook_event_id;
		public $user_id;
		public $tickets = array();

		public static function get_from_id($id) {
			$event = new Event();
			$event->load($id);
			return $event;
		}

		public function load($id) {
			global $g_pdo;

			$request = <<<EOF
SELECT * FROM `event`
WHERE `id`= :id
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$pst->execute(array(":id" => $id));
			$record = $pst->fetch(PDO::FETCH_ASSOC);
			if (!isset($record['id'])) {
				throw new Exception(_t("Cannot load the event with id=") . $id);
			}
			$this->hydrate($record);
		}

		public function load_default() {
			global $g_i18n;

			$this->title = "";
			$this->happening_t = "";
			$this->confirmation_t = "";
			$this->funding_needed = 0.00;
			$this->funding_acquired = 0;
			$this->location = "";
			$this->link = "http://";
			$this->short_description = file_get_contents($g_i18n->filename(BASE_DIR . "/etc/short_description.html"));
			$this->long_description = file_get_contents($g_i18n->filename(BASE_DIR . "/etc/long_description.html"));
			$this->type = EVENT_TYPE_NOMINATIVE;
			$this->status = EVENT_STATUS_PLANNED;
			$this->publish_flag = EVENT_PUBLISH_FLAG_NO;
			$this->flags = 0;
			$this->user_id = User::get_id_from_account();;
		}

		public function hydrate($array) {
			foreach ($array as $key => $value) {
				if (ESCAPE_QUOTE) {
					//$value = str_replace("\\'", "'", $value);
					//$value = str_replace('\\"', '"', $value);
					$value = stripcslashes($value);
					$value = str_replace("%5C%22", "", $value);
				}
				if ($key == "id_user") {
					$this->user_id = $value;
				}
				debug($key."=>".$value);
				$this->$key = $value;
			}
		}

		public function store() {
			global $g_pdo;

			$this->id = create_id();
			$created_t = time();
			$mod_t = $created_t;
			$publish_flag = EVENT_PUBLISH_FLAG_NO;

			$request = <<<EOF
INSERT INTO `event`
SET
	`id`= :id,
	`created_t`= :created_t,
	`mod_t`= :mod_t,
	`id_user`= :id_user,
	`title`= :title,
	`organizer_name`= :organizer_name,
	`location`= :location,
	`link`= :link,
	`short_description`= :short_description,
	`long_description`= :long_description,
	`happening_t`= :happening_t,
	`confirmation_t`= :confirmation_t,
	`funding_needed`= :funding_needed,
	`funding_acquired`=0,
	`type`= :type,
	`status`= :status,
	`publish_flag`= :publish_flag,
	`flags`= :flags,
	`phone`= :phone
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$array = array(
				":id" => $this->id,
				":created_t" => $created_t,
				":mod_t" => $mod_t,
				":id_user" => $this->user_id,
				":title" => $this->title,
				":organizer_name" => $this->organizer_name,
				":location" => $this->location,
				":link" => $this->link,
				":short_description" => $this->short_description,
				":long_description" => $this->long_description,
				":happening_t" => $this->happening_t,
				":confirmation_t" => $this->confirmation_t,
				":funding_needed" => $this->funding_needed,
				":type" => $this->type,
				":status" => $this->status,
				":publish_flag" => $publish_flag,
				":flags" => $this->flags,
				":phone" => $this->phone,
			);
			$pst->execute($array);
		}

		public function update() {
			global $g_pdo;

			$mod_t = time();
			$request = <<<EOF
UPDATE `event`
SET
	`mod_t`= :mod_t,
	`title`= :title,
	`organizer_name`= :organizer_name,
	`location`= :location,
	`link`= :link,
	`short_description`= :short_description,
	`long_description`= :long_description,
	`happening_t`= :happening_t,
	`confirmation_t`= :confirmation_t,
	`funding_needed`= :funding_needed,
	`flags`= :flags,
	`type`= :type,
	`phone`= :phone,
	`facebook_event_id`= :facebook_event_id
WHERE `id`= :id
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$array = array(
				":mod_t" => $mod_t,
				":title" => $this->title,
				":organizer_name" => $this->organizer_name,
				":location" => $this->location,
				":link" => $this->link,
				":short_description" => $this->short_description,
				":long_description" => $this->long_description,
				":happening_t" => $this->happening_t,
				":confirmation_t" => $this->confirmation_t,
				":funding_needed" => $this->funding_needed,
				":id" => $this->id,
				":flags" => $this->flags,
				":type" => $this->type,
				":phone" => $this->phone,
				":facebook_event_id" => $this->facebook_event_id,
			);
			$pst->execute($array);
		}

		public function delete_try() {
			global $g_pdo;

			if ($this->has_accountancy_activity() ) {
				$this->set_publish_flag(EVENT_PUBLISH_FLAG_NO);
				$this->set_status(EVENT_STATUS_INACTIVATED);
				return;
			}

			foreach ($this->get_tickets() as $ticket) {
				$ticket->delete();
			}

			$request = <<<EOF
DELETE FROM `event`
WHERE `id`= :id
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$pst->execute(array(":id" => $this->id));
		}

		public function is_published() {
			return $this->publish_flag == EVENT_PUBLISH_FLAG_YES;
		}

		public function is_confirmed() {
			return $this->status == EVENT_STATUS_CONFIRMED;
		}

		public function is_cancelled() {
			return $this->status == EVENT_STATUS_CANCELLED;
		}

		public function is_inactivated() {
			return $this->status == EVENT_STATUS_INACTIVATED;
		}

		public function is_ready_for_publication() {
			return ($this->flags & EVENT_FLAG_READY_FOR_PUBLICATION) != 0;
		}

		public function is_ticket_office_open() {
			return true;
		}

		public function get_confirmation_date() {
			$first_ticket_t = $this->get_first_booked_ticket_ts();
			if (is_null_or_empty($first_ticket_t)) {
				if (is_null_or_empty($this->confirmation_t)) {
					return $this->happening_t;
				} else {
					return $this->confirmation_t;
				}
			}
			$first_ticket_29_t = strtotime('+29 days', $first_ticket_t);
			$confirmation_t = "";
			if (is_null_or_empty($this->confirmation_t)) {
				$confirmation_t = min($first_ticket_29_t, s2t($this->happening_t));
			} else {
				$confirmation_t = min($first_ticket_29_t, s2t($this->happening_t), s2t($this->confirmation_t));
			}
			return t2s($confirmation_t);
		}

		public function get_first_booked_ticket_ts() {
			// if no booked ticket return the happening date.
			global $g_pdo;

			$request = <<<EOF
SELECT * FROM `bill`
WHERE `type`= :type AND `id_event`= :id
ORDER BY created_t DESC
LIMIT 1
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$array = array(
				":id" => $this->id,
				":type" => DEVIS_TYPE_QUOTATION,
			);
			$pst->execute($array);
			if (($record = $pst->fetch(PDO::FETCH_ASSOC)) != NULL) {
				return $record["created_t"];
			}
			return NULL;
		}

		public function has_already_happened() {
			return s2t($this->happening_t) > time();
		}

		public function add_flag($flag) {
			$this->flags |= $flag;
		}

		public function remove_flag($flag) {
			$this->flags &= ~$flag;
		}

		public function has_flag($flag) {
			return ($this->flags & $flag) != 0;
		}

		public function check_owner() {
			if ($this->user_id != User::get_id_from_account() && !is_admin_logged()) {
				throw new Exception(_t("You are not the creator of this event"));
			}
		}

		public function has_accountancy_activity() {
			global $g_pdo;

			$request = "SELECT COUNT(*) FROM `bill` WHERE `id_event`= :id";
			debug($request);
			$pst = $g_pdo->prepare($request);
			$pst->execute(array(":id" => $this->id));
			$count = $pst->fetch();
			return $count[0] > 0;
		}

		public function can_participate() {
			if (!$this->is_ticket_office_open()) {
				return FALSE;
			}
			if (!$this->is_published()) {
				return FALSE;
			}
			if ($this->is_cancelled()) {
				return FALSE;
			}
			if ($this->is_inactivated()) {
				return FALSE;
			}
			if ($this->get_remaining_tickets_amount() <= 0) {
				return FALSE;
			}
			return TRUE;
		}

		public function get_remaining_tickets_amount() {
			$ticket_remaining = 0;
			foreach ($this->get_tickets() as $ticket) {
				$ticket_remaining += $ticket->get_remaining();
			}
			return $ticket_remaining;
		}

		function add_funding_acquired($amount) {
			global $g_pdo;

			$funding_acquired = $this->funding_acquired + $amount;
			$mod_t = time();
			$request = <<<EOF
UPDATE `event`
SET
	`mod_t`= :mod_t,
	`funding_acquired`= :funding_acquired
WHERE `id`= :id
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$array = array(
				":mod_t" => $mod_t,
				":funding_acquired" => $funding_acquired,
				":id" => $this->id,
			);
			return $pst->execute($array);
		}

		public static function exists($id) {
			global $g_pdo;

			$request = "SELECT COUNT(*) FROM `event` WHERE `id`= :id";
			$q = $g_pdo->prepare($request);
			$q->execute(array(":id" => $id));
			$count = $q->fetch();
			return $count[0] > 0;
		}

		public static function list_all($user_id = NULL) {
			global $g_pdo;
			$events = array();
			$where_clause = "";
			if ($user_id != NULL) {
				$where_clause = "WHERE `id_user`= :id";
			}

			if (!is_admin_logged()) {
				if ($where_clause == "") {
					$where_clause = "WHERE `status`!=" . EVENT_STATUS_INACTIVATED;
				} else {
					$where_clause .= " AND `status`!=" . EVENT_STATUS_INACTIVATED;
				}
			}

			$request = <<<EOF
SELECT `id` FROM `event` ${where_clause} ORDER BY `happening_t`
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			if ($user_id != NULL) {
				$pst->execute(array(":id" => $user_id));
			} else {
				$pst->execute();
			}
			$event_record = $pst->fetch();
			while (isset($event_record["id"])) {
				$event = Event::get_from_id($event_record["id"]);
				$events[] = $event;
				$event_record = $pst->fetch();
			}
			return $events;
		}

		// Planned, Confirmed, Cancelled.
		public function set_status($status) {
			global $g_pdo;

			$this->status = $status;
			$confirmation_t = "";
			if ($status == EVENT_STATUS_CONFIRMED) {
				$confirmation_t = t2s(time());
			}
			$request = <<<EOF
UPDATE `event`
SET	`status`= :status, confirmation_t= :confirmation_t
WHERE `id`= :id
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$array = array(
				":status" => $this->status,
				":id" => $this->id,
				":confirmation_t" => $confirmation_t,
			);
			$pst->execute($array);
		}

		public function set_publish_flag($flag) {
			global $g_pdo;

			$this->publish_flag = $flag;

			$request = <<<EOF
UPDATE `event`
SET	`publish_flag`= :flag
WHERE `id`= :id
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$array = array(
				":flag" => $this->publish_flag,
				":id" => $this->id,
			);
			$pst->execute($array);
		}

		public function get_devis($type = DEVIS_TYPE_AUTODETECT) {
			global $g_pdo;
			if ($type == DEVIS_TYPE_AUTODETECT) {
				$type = DEVIS_TYPE_QUOTATION;
				if ($this->is_confirmed()) {
					$type = DEVIS_TYPE_INVOICE;
				}
			}
			$request = <<<EOF
SELECT `id` FROM `bill`
WHERE `id_event`= :id_event AND `type`= :type
EOF;
			debug($request);
			$q = $g_pdo->prepare($request);
			$array = array(
				":id_event" => $this->id,
				":type" => $type,
			);
			$q->execute($array);

			$devis_array = array();
			while (($record = $q->fetch()) != NULL) {
				debug("record=".sprint_r($record));
				$devis = Devis::get_from_id($record["id"]);
				$devis_array[] = $devis;
			}
			return $devis_array;
		}

		public function set_devis_status($status) {
			$devis_array = $this->get_devis(DEVIS_TYPE_QUOTATION);
			foreach ($devis_array as $devis) {
				$devis->set_status($status);
			}
		}

		public function display_status() {
			switch ($this->status) {
				case EVENT_STATUS_PLANNED:
					return "Planned";
					break;
				case EVENT_STATUS_CONFIRMED:
					return "Confirmed";
					break;
				case EVENT_STATUS_CANCELLED:
					return "Cancelled";
					break;
				case EVENT_STATUS_INACTIVATED:
					return "Inactivated";
					break;
			}
		}

		public function get_participations() {
			$admin = false;
			if (is_admin_logged()) {
				$admin = true;
			}
			$devis_array = $this->get_devis();
			$participations = array();
			foreach ($devis_array as $devis) {
				if (!$admin && !$devis->is_really_paid()) {
					continue;
				}
				foreach ($devis->get_items() as $item) {
					$participations[] = array($item, $devis);
				}
			}
			return $participations;
		}

		public function hydrate_from_form() {
			$this->title = $_GET['title'];
			$this->organizer_name = $_GET['organizer_name'];
			$this->happening_t = $_GET['happening_t'];
			$this->funding_needed = $_GET['funding_needed'];
			$this->location = $_GET['location'];
			$this->link = $_GET['link'];
			$this->short_description = $_GET['short_description'];
			$this->long_description = $_GET['long_description'];
			$this->type = $_GET['event_type'];
			$this->phone = $_GET['phone'];
			if (isset($_GET["is_confirmed"])) {
				$this->status = EVENT_STATUS_CONFIRMED;
				$this->confirmation_t = t2s(time());
			} else {
				$this->confirmation_t = $_GET['confirmation_t'];
			}
			debug("hydrate_from_form=".sprint_r($this));
		}

		public function has_rate($label) {
			global $g_pdo;

			$request = <<<EOF
SELECT COUNT(*) FROM `rate`
WHERE `label`= :label AND `id_event`= :event_id
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$pst->execute(array(
				":label" => $label,
				":event_id" => $this->id)
			);
			$count = $pst->fetch();
			return $count[0] > 0;
		}

		public function update_rate($label, $tax_rate, $amount) {
			global $g_pdo;

			if (!$this->has_rate($label)) {
				return $this->add_rate($label, $amount, $tax_rate);
			}

			$request = <<<EOF
UPDATE `rate`
SET
	`amount`= :amount,
	`tax_rate`= :tax_rate
WHERE
	`label`= :label
	AND `id_event`= :id_event;
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);

			$pst->execute(array(
				":amount" => $amount,
				":tax_rate" => $tax_rate,
				":label" => $label,
				":id_event" => $this->id,
			));

		}

		public function delete_unused_tickets($names) {
			$tickets = $this->get_tickets();
			foreach ($tickets as $ticket) {
				if (!in_array($ticket->name, $names)) {
					debug("Deleted ".$ticket->name);
					$ticket->delete();
				}
			}
		}

		public function get_ticket($name) {
			global $g_pdo;

			$request = <<<EOF
SELECT `id` FROM `ticket`
WHERE `id_event`= :id AND name = :name
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$pst->execute(array(":id" => $this->id, ":name" => $name));
			if (($record = $pst->fetch()) != NULL) {
				return Ticket::get_from_id($record["id"]);
			}
			return NULL;
		}

		public function get_tickets() {
			global $g_pdo;

			$request = <<<EOF
SELECT `id` FROM `ticket`
WHERE `id_event`= :id
ORDER BY tax_rate DESC
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$pst->execute(array(":id" => $this->id));
			$result = array();
			while (($record = $pst->fetch()) != NULL) {
				$result[] = Ticket::get_from_id($record["id"]);
			}
			return $result;
		}

		public function is_organized_by($user_id = null) {
			if (!$user_id) {
				$user_id = User::get_id_from_account();
			}
			return $this->user_id == $user_id;
		}

		public function can_be_administrated() {
			if (is_admin_logged() || $this->user_id == User::get_id_from_account()) {
				debug("Can administrate.");
				return TRUE;
			}
			debug("Cannot administrate.");
			return FALSE;
		}

		public function get_guests() {
			$result = array();
			foreach (Guest::select_all() as $r) {
				$guest = new Guest();
				$guest->hydrate();
				$guest->set_value("email", $r["email"]);
				$result[] = $guest;
			}
			return $result;
		}

		public function get_url() {
			return HOST.'event/'.$this->id."/".str_replace("+", "-", urlencode($this->title));
		}
	}
?>