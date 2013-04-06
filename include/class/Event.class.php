<?php
	class Event {
		public $id = -1;
		public $title;
		public $organizer_name;
		public $confirmation_t;
		public $happening_t;
		public $open_t;
		public $funding_needed;
		public $funding_authorized;
		public $location;
		public $link;
		public $short_description;
		public $long_description;
		public $type;
		public $status;
		public $publish_flag;
		public $user_id;
		public $rates = array();

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
				throw new Exception("Cannot load the event with id=" . $id);
			}
			$this->hydrate($record);
			$this->short_description = html_entity_decode($this->short_description);
			$this->long_description = html_entity_decode($this->long_description);
		}

		public function load_default() {
			$this->title = "";
			$this->confirmation_t = "";
			$this->happening_t = "";
			$this->open_t = "";
			$this->funding_needed = 0.00;
			$this->funding_authorized = 0;
			$this->location = "";
			$this->link = "http://";
			$this->short_description = file_get_contents(BASE_DIR . "/etc/short_description.html");
			$this->long_description = file_get_contents(BASE_DIR . "/etc/long_description.html");
			$this->type = EVENT_TYPE_NOMINATIVE;
			$this->status = EVENT_STATUS_PLANNED;
			$this->publish_flag = EVENT_PUBLISH_FLAG_NO;
			$this->user_id = User::get_id_from_account();;
		}

		public function hydrate($array) {
			foreach ($array as $key => $value) {
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
			$status = EVENT_STATUS_PLANNED;
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
	`link`=  :link,
	`short_description`= :short_description,
	`long_description`= :long_description,
	`happening_t`= :happening_t,
	`confirmation_t`= :confirmation_t,
	`open_t`= :open_t,
	`funding_needed`= :funding_needed,
	`funding_acquired`=0,
	`type`= :type,
	`status`= :status,
	`publish_flag`= :publish_flag
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
				":open_t" => $this->open_t,
				":funding_needed" => $this->funding_needed,
				":type" => $this->type,
				":status" => $status,
				":publish_flag" => $publish_flag,
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
	`open_t`= :open_t,
	`funding_needed`= :funding_needed
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
				":open_t" => $this->open_t,
				":funding_needed" => $this->funding_needed,
				":id" => $this->id,
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

			$request = <<<EOF
DELETE FROM `rate`
WHERE `id_event`= :id
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$pst->execute(array(":id" => $this->id));

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

		public function check_owner() {
			if ($this->user_id != User::get_id_from_account() && !is_admin_logged()) {
				throw new Exception("You are not the creator of this event");
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
			if (s2t($this->open_t, "%Y-%m-%d") >= time()) {
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
			return TRUE;
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

			$request = <<<EOF
UPDATE `event`
SET	`status`= :status
WHERE `id`= :id
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$array = array(
				":status" => $status,
				":id" => $this->id,
			);
			$pst->execute($array);
		}

		public function set_publish_flag($flag) {
			global $g_pdo;

			$request = <<<EOF
UPDATE `event`
SET	`publish_flag`= :flag
WHERE `id`= :id
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$array = array(
				":flag" => $flag,
				":id" => $this->id,
			);
			$pst->execute($array);
		}

		public function get_bill() {
			global $g_pdo;

			$request = <<<EOF
SELECT `id` FROM `bill`
WHERE `id_event`= :id
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$pst->execute(array(":id" => $this->id));

			$devis_array = array();
			while (($record = $q->fetch()) != NULL) {
				debug("record=".sprint_r($record));
				$devis = Devis::get_from_id($record["id"]);
				$devis_array[] = $devis;
			}
			return $devis_array;
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
					return "Planned&nbsp;(not&nbsp;confirmed)";
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
			$devis_array = $this->get_devis();
			$participations = array();
			foreach ($devis_array as $devis) {
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
			$this->confirmation_t = $_GET['confirmation_t'];
			$this->open_t = $_GET['open_t'];
			$this->funding_needed = $_GET['funding_needed'];
			$this->location = $_GET['location'];
			$this->link = $_GET['link'];
			$this->short_description = $_GET['short_description'];
			$this->long_description = $_GET['long_description'];
			$this->type = $_GET['event_type'];
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

		public function add_rate($label, $rate, $tax_rate) {
			global $g_pdo;

			if ($this->has_rate($label)) {
				return;
			}
			$id = create_id();

			$request = <<<EOF
INSERT INTO `rate`
SET
	`id`= :id,
	`label`= :label,
	`id_event`= :id_event,
	`tax_rate`= :tax_rate,
	`amount`= :amount;
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$pst->execute(array(
				":id" => $id,
				":label" => $label,
				":id_event" => $this->id,
				":tax_rate" => $tax_rate,
				":amount" => $rate,
			));
		}

		public function delete_unused_rates($labels) {
			$rates = $this->get_rates();
			foreach ($rates as $rate) {
				if (!in_array($rate["label"], $labels)) {
					debug("Deleted ".$rate["label"]);
					$this->delete_rate($rate["label"]);
				}
			}
		}

		public function delete_rate($label) {
			global $g_pdo;

			$request = <<<EOF
DELETE FROM `rate`
WHERE `label`= :label AND `id_event`= :id_event
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$pst->execute(array(
				":label" => $label,
				":id_event" => $this->id,
			));
		}

		public function get_rates() {
			global $g_pdo;

			$request = <<<EOF
SELECT `label`, `amount`, `tax_rate` FROM `rate`
WHERE `id_event`= :id
ORDER BY tax_rate DESC
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$pst->execute(array(":id" => $this->id));
			$rate = $pst->fetch();
			$rates = array();
			while (isset($rate["label"])) {
				$rates[] = $rate;
				$rate = $pst->fetch();
			}
			return $rates;
		}

		public function can_be_administrated() {
		if (is_admin_logged() || $this->id_user == User::get_id_from_account()) {
			debug("Can administrate.");
			return TRUE;
		}
		debug("Cannot administrate.");
		return FALSE;
	}
	}
?>