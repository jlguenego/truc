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
			sync_lock();
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
WHERE start_t < :now
LIMIT 1
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$pst->execute(array(":now" => time()));
			$record = $pst->fetch(PDO::FETCH_ASSOC);
			if (!isset($record['id'])) {
				return null;
			}
			return Task::get_from_id($record['id']);
		}

		public function execute() {
			debug("Execute task id ".$this->id);
		}



	}

?>