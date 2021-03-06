<?php
	class Guest extends Record {
		public function __construct() {
			$this->type = "guest";
		}

		public static function get_from_email($email) {
			global $g_pdo;

			$request = <<<EOF
SELECT id FROM `guest`
WHERE `email`= :email
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$pst->execute(array(":email" => $email));
			$record = $pst->fetch(PDO::FETCH_ASSOC);
			if (!isset($record['id'])) {
				return NULL;
			}
			$guest = Record::get_from_id("guest", $record['id']);
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

		public function store() {
			global $g_pdo;

			if (($guest = Guest::get_from_email($this->get_value("email"))) != null) {
				$this->id = $guest->id;
			} else {
				parent::store();
			}
			$this->link_to_event();
		}

		public static function select_all($type = "") {
			global $g_pdo;

			$event_id = $_SESSION["event_id"];

			$request = <<<EOF
SELECT
  g.*
FROM
  guest g,
  event_guest eg
WHERE
  g.id = eg.id_guest
  AND eg.id_event = :id
ORDER BY g.id
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$pst->execute(array(":id" => $event_id));

			$result = array();
			while (($record = $pst->fetch(PDO::FETCH_ASSOC)) != null) {
				$result[] = $record;
			}
			debug(sprint_r($result));
			return $result;
		}

		public function link_to_event() {
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
				":id_event" => $_SESSION["event_id"],
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

		public function delete() {
			global $g_pdo;
			$request = <<<EOF
DELETE FROM
  event_guest
WHERE
  id_guest = :id
EOF;
			debug($request);
			debug("array=".sprint_r(array(":id" => $this->id)));
			$pst = $g_pdo->prepare($request);
			$pst->execute(array(":id" => $this->id));
			parent::delete();
		}

		public static function delete_all() {
			global $g_pdo;
			$request = <<<EOF
SELECT
  id_guest
FROM
  event_guest
WHERE
  id_event = :id
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);

			$array = array(":id" => $_SESSION["event_id"]);
			debug("array=".sprint_r($array));
			$pst->execute($array);

			while(($record = $pst->fetch()) != null) {
				$guest = new Guest();
				$guest->id = $record["id_guest"];
				$guest->delete();
			}
		}

		public static function get_dialog_content($type) {
			$result = parent::get_dialog_content($type);
			$result .= <<<EOF
<div id="dialog_import" style="display: none;" title="">
	<form name="form_execute_global_action_import" action="?action=import&amp;type=$type" method="post" enctype="multipart/form-data">
		<input type="file" name="guest_filename" placeholder="File with email address" />
	</form>
</div>
<div id="dialog_delete_all" style="display: none;" title="">
	{{Are you sure you want to delete all guests?}}
	<form name="form_execute_global_action_delete_all" action="?action=delete_all&amp;type=$type" method="post">
	</form>
</div>
EOF;
			return $result;
		}

		public static function import() {
			$event = Event::get_from_id($_SESSION["event_id"]);
			$file = Form::get_file("guest_filename");
			$guest_array = file($file, FILE_IGNORE_NEW_LINES);
			debug('guest_array: '. sprint_r($guest_array));
			foreach ($guest_array as $line) {
				if (!Guest::valid_line($line)) {
					continue;
				}
				$guest = new Guest();
				$guest->hydrate();
				$guest->set_value("email", $line);
				$guest->store();
				$guest->link_to_event($event->id);
			}
		}
	}
?>