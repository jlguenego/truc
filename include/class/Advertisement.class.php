<?php
	class Advertisement extends Record {
		public function __construct() {
			$this->type = "advertisement";
		}

		public function send() {
			debug("Action Send");
			$this->set_status(ADVERTISEMENT_STATUS_SENT);
		}

		public function send_to_me() {
			debug("Action Send to me");
			$user = need_authentication();
			mail_advertise($_SESSION["event_id"], $user->email, $this->id);
		}

		public static function select_all($type) {
			global $g_pdo;

			$event_id = $_SESSION["event_id"];

			$request = <<<EOF
SELECT * FROM $type
WHERE id_event = :event_id
ORDER BY id
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$pst->execute(array(":event_id" => $event_id));

			$result = array();
			while (($record = $pst->fetch(PDO::FETCH_ASSOC)) != null) {
				$result[] = $record;
			}
			return $result;
		}

		public function accept($action) {
			if ($action->name == "delete") {
				debug("value=".$this->get_field("status")->value);
				if ($this->get_field("status")->value == ADVERTISEMENT_STATUS_SENT) {
					return false;
				}
			}
			return true;
		}

		public function set_status($status) {
			global $g_pdo;

			$this->status = $status;
			$request = <<<EOF
UPDATE advertisement
SET	`status`= :status, mod_t= :mod_t
WHERE `id`= :id
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$array = array(
				":status" => $this->status,
				":id" => $this->id,
				":mod_t" => time(),
			);
			$pst->execute($array);
		}
	}
?>