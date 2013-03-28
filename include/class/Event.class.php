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
		public $nominative;
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
WHERE `id`= ${id}
EOF;
			debug($request);
			$q = $g_pdo->prepare($request);
			$q->execute();
			$event = $q->fetch(PDO::FETCH_ASSOC);
			if (!isset($event['id'])) {
				throw new Exception("Cannot load the event with id=" . $id);
			}
			$this->hydrate($event);
			$this->short_description = html_entity_decode($this->short_description);
			$this->long_description = html_entity_decode($this->long_description);
		}

		public function load_default() {
			$this->title = "";
			$this->confirmation_t = "";
			$this->happening_t = "";
			$this->open_t = "";
			$this->funding_needed = "";
			$this->funding_authorized = 0;
			$this->location = "";
			$this->link = "http://";
			$this->short_description = "";
			$this->long_description = "";
			$this->nominative = 1;
			$this->status = EVENT_STATUS_PLANNED;
			$this->publish_flag = EVENT_PUBLISH_FLAG_NO;
			$this->user_id = get_id_from_account();;
		}

		private function hydrate($array) {
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
			$this->prepare_for_db();

			$request = <<<EOF
INSERT INTO `event`
SET
	`id`="{$this->id}",
	`created_t`="${created_t}",
	`mod_t`='${mod_t}',
	`id_user`="{$this->user_id}",
	`title`="{$this->title}",
	`organizer_name`="{$this->organizer_name}",
	`location`="{$this->location}",
	`link`="{$this->link}",
	`short_description`="{$this->short_description}",
	`long_description`="{$this->long_description}",
	`happening_t`="{$this->happening_t}",
	`confirmation_t`="{$this->confirmation_t}",
	`open_t`="{$this->open_t}",
	`funding_needed`="{$this->funding_needed}",
	`funding_acquired`=0,
	`nominative`={$this->nominative},
	`status`=${status},
	`publish_flag`=${publish_flag}
EOF;
			debug($request);
			if(!$g_pdo->exec($request)) {
				throw new Exception("Get event participants mails: ".sprint_r($g_pdo->errorInfo())." InnoDB?");
			}
		}

		public function update() {
			global $g_pdo;

			$mod_t = time();
			$this->prepare_for_db();
			$request = <<<EOF
UPDATE `event`
SET
	`mod_t`="${mod_t}",
	`title`="{$this->title}",
	`organizer_name`="{$this->organizer_name}",
	`location`="{$this->location}",
	`link`="{$this->link}",
	`short_description`='{$this->short_description}',
	`long_description`='{$this->long_description}',
	`happening_t`="{$this->happening_t}",
	`confirmation_t`="{$this->confirmation_t}",
	`open_t`="{$this->open_t}",
	`funding_needed`="{$this->funding_needed}"
WHERE `id`="{$this->id}"
EOF;
			debug($request);
			if (!$g_pdo->exec($request)) {
				throw new Exception("Event update: " . sprint_r($g_pdo->errorInfo()));
			}
		}

		public function delete() {
			global $g_pdo;

			$this->check_linked_devis();

			$request = <<<EOF
DELETE FROM `rate`
WHERE `id_event`={$this->id}
EOF;
			debug($request);
			$g_pdo->exec($request);

			$request = <<<EOF
DELETE FROM `event`
WHERE `id`={$this->id}
EOF;
			debug($request);
			if(!$g_pdo->exec($request)) {
				throw new Exception("Event delete:".sprint_r($g_pdo->errorInfo()));
			}
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

		public function check_owner() {
			if ($this->user_id != get_id_from_account() && !is_admin()) {
				throw new Exception("You are not the creator of this event");
			}
		}

		public function check_linked_devis() {
			global $g_pdo;

			$request = "SELECT COUNT(*) FROM `bill` WHERE `id_event`=" . $this->id;
			debug($request);
			$q = $g_pdo->prepare($request);
			$q->execute();
			$count = $q->fetch();
			if($count[0] > 0) {
				throw new Exception("This event can not be deleted for accountancy reasons.");
			}
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
			return TRUE;
		}

		function add_funding_acquired($amount) {
			global $g_pdo;

			$funding_acquired = $this->funding_acquired + $amount;
			$mod_t = time();
			$request = <<<EOF
UPDATE `event`
SET
	`mod_t`="${mod_t}",
	`funding_acquired`="${funding_acquired}"
WHERE `id`="{$this->id}"
EOF;
			debug($request);
			return $g_pdo->exec($request);
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
			$where_clause = "";
			if ($user_id != NULL) {
				$where_clause = " WHERE `id_user`=" . $user_id;
			}

			$request = <<<EOF
SELECT `id` FROM `event`${where_clause} ORDER BY `happening_t`
EOF;
			debug($request);
			$q = $g_pdo->prepare($request);
			$q->execute();
			$event_record = $q->fetch();
			$events = array();
			while (isset($event_record["id"])) {
				$event = Event::get_from_id($event_record["id"]);
				$events[] = $event;
				$event_record = $q->fetch();
			}
			return $events;
		}

		// Planned, Confirmed, Cancelled.
		public function set_status($status) {
			global $g_pdo;

			$request = <<<EOF
UPDATE `event`
SET	`status`=${status}
WHERE `id`="{$this->id}"
EOF;
			debug($request);
			return $g_pdo->exec($request);
		}

		public function set_publish_flag($flag) {
			global $g_pdo;

			$request = <<<EOF
UPDATE `event`
SET	`publish_flag`=${flag}
WHERE `id`="{$this->id}"
EOF;
			debug($request);
			return $g_pdo->exec($request);
		}

		public function get_devis() {
			global $g_pdo;

			$request = <<<EOF
SELECT `id` FROM `bill`
WHERE `id_event`={$this->id}
EOF;
			debug($request);
			$q = $g_pdo->prepare($request);
			if ($q->execute() === FALSE) {
				debug($request);
				throw new Exception("Get devis: ".sprint_r($g_pdo->errorInfo()));
			};

			$devis_array = array();
			while (($record = $q->fetch()) != NULL) {
				debug("record=".sprint_r($record));
				$devis = Devis::get_from_id($record["id"]);
				$devis_array[] = $devis;
			}
			return $devis_array;
		}

		public function set_devis_status($status) {
			$devis_array = $this->get_devis();
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
			}
		}

		private function prepare_for_db() {
			$this->short_description = handle_html($this->short_description);
			$this->long_description = handle_html($this->long_description);
		}
	}
?>