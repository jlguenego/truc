<?php
	class Guest {
		public $id = -1;
		public $email;

		public static function get_from_id($id) {
			$guest = new Guest();
			$guest->load($id);
			return $guest;
		}

		public static function get_from_email($email) {
			global $g_pdo;

			$request = <<<EOF
SELECT * FROM `guest`
WHERE `email`= :email
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$pst->execute(array(":email" => $email));
			$record = $pst->fetch(PDO::FETCH_ASSOC);
			if (!isset($record['id'])) {
				return NULL;
			}
			$guest = new Guest();
			$guest->hydrate($record);
			return $guest;
		}

		public function load($id) {
			global $g_pdo;

			$request = <<<EOF
SELECT * FROM `guest`
WHERE `id`= :id
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$pst->execute(array(":id" => $id));
			$record = $pst->fetch(PDO::FETCH_ASSOC);
			if (!isset($record['id'])) {
				throw new Exception(_t("Cannot load the guest with id=") . $id);
			}
			$this->hydrate($record);
		}

		public function hydrate($array) {
			foreach ($array as $key => $value) {
				debug($key."=>".$value);
				$this->$key = $value;
			}
		}

		public function store() {
			global $g_pdo;

			if (($guest = Guest::get_from_email($this->email)) != null) {
				$this->id = $guest->id;
				return;
			}

			$this->id = create_id();
			$created_t = time();
			$mod_t = $created_t;

			$request = <<<EOF
INSERT INTO `guest`
SET
	`id`= :id,
	`created_t`= :created_t,
	`mod_t`= :mod_t,
	`email`= :email
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$array = array(
				":id" => $this->id,
				":created_t" => $created_t,
				":mod_t" => $mod_t,
				":email" => $this->email,
			);
			$pst->execute($array);
		}


		public static function list_all($event_id = NULL) {
			global $g_pdo;
			$result = array();
			$where_clause = "";
			if ($event_id != NULL) {
				$where_clause = "AND eg.id_event= :id";
			}


			$request = <<<EOF
SELECT
	g.id as id
FROM
  guest g,
  event_guest eg
WHERE
	g.id = eg.id_guest
    ${where_clause} ORDER BY g.id
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			if ($event_id != NULL) {
				$pst->execute(array(":id" => $event_id));
			} else {
				$pst->execute();
			}
			$guest_record = $pst->fetch();
			while (isset($guest_record["id"])) {
				$guest = Guest::get_from_id($guest_record["id"]);
				$result[] = $guest;
				$guest_record = $pst->fetch();
			}
			return $result;
		}

		public function hydrate_from_form() {
			$this->email = $_GET['email'];
			debug("hydrate_from_form=".sprint_r($this));
		}

		public function link_to_event($event_id) {
			global $g_pdo;

			$request = <<<EOF
INSERT IGNORE INTO `event_guest`
SET
	`id_guest`= :id_guest,
	`id_event`= :id_event
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$array = array(
				":id_guest" => $this->id,
				":id_event" => $event_id,
			);
			debug('array: '.sprint_r($array));
			$pst->execute($array);
		}

		public static function valid_line($line) {
			if (!filter_var($line, FILTER_VALIDATE_EMAIL)) {
				debug("not a valid email: ".$line);
				debug(sprint_r(filter_var($line, FILTER_VALIDATE_EMAIL)));
				return false;
			}
			return true;
		}
	}
?>