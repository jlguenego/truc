<?php
	require_once(BASE_DIR . "/include/sync.inc");

	class Task extends Record {
		public function __construct() {
			$this->type = "task";
		}
		public static function run($max_duration) {
			if (sync_lock() === false) {
				return;
			}
			$start = time();
			while (time() - $start < $max_duration) {
				$task = Task::pop();
				if ($task == null) {
					debug("No task to execute anymore.");
					break;
				}
				$task->execute();
			}
			sync_unlock();
		}

		public static function pop() {
			global $g_pdo;

			$request = <<<EOF
SELECT id FROM task
WHERE start_t < :now AND status = :status
LIMIT 1
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$pst->execute(array(
				":now" => time(),
				":status" => TASK_STATUS_PENDING,
			));
			$record = $pst->fetch(PDO::FETCH_ASSOC);
			if (!isset($record['id'])) {
				return null;
			}
			return Record::get_from_id("task", $record['id']);
		}

		public function execute() {
			if ($this->get_value("start_t") < time()) {
				$this->execute_now();
			} else {
				throw new Exception("Cannot execute this task now (start date = ".$this->get_value("start_t").").");
			}
		}

		public function execute_now() {
			global $g_task_command_list;

			debug("Execute now task id ".$this->id);
			$this->update_status(TASK_STATUS_RUNNING);
			try {
				$this->check_command();
				if ($this->get_value("command") == "mail_advertisement") {
					list($event_id, $guest_mail, $advertisement_id) = explode(",", $this->get_value("parameters"));
					mail_advertise($event_id, $guest_mail, $advertisement_id);
				} else {
					debug("Task not understood.");
				}
				$this->update_status(TASK_STATUS_SUCCESS);
			} catch (Exception $e) {
				$this->update_status(TASK_STATUS_ERROR, $e->getMessage());
			}
		}

		public function update_status($status, $error_msg = "") {
			global $g_pdo;

			$mod_t = time();
			$request = <<<EOF
UPDATE `task`
SET
	`mod_t`= :mod_t,
	`status`= :status,
	`error_msg`= :error_msg
WHERE `id`= :id
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$array = array(
				":mod_t" => $mod_t,
				":status" => $status,
				":error_msg" => $error_msg,
				":id" => $this->id,
			);
			$pst->execute($array);
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

		public function check_command() {
			global $g_task_command_list;
			if (!in_array($this->get_value("command"), $g_task_command_list)) {
				throw new Exception("Forbidden command: ".$this->get_value("command"));
			}
		}
	}
?>