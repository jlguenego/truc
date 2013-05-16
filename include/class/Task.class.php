<?php
	require_once(BASE_DIR . "/include/sync.inc");

	class Task {
		public $start_t;
		public $description = "";
		public $command;
		public $parameters;

		public static function get_from_id($id) {
			$task = new Task();
			$task->load($id);
			return $task;
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
			return Task::get_from_id($record['id']);
		}

		public function execute() {
			debug("Execute task id ".$this->id);
			$this->update_status(TASK_STATUS_RUNNING);
			try {
				eval($this->command);
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
			);
			$pst->execute($array);
		}



	}

?>