<?php
	require_once('include/misc.inc');
	require_once("include/user.inc");
	
	function event_exists($title) {
		try {
			global $g_pdo;
			$dbname = MYSQL_DBNAME;
			$request = <<<EOF
SELECT COUNT(*) FROM `${dbname}`.`event` WHERE `title`= :title
EOF;
			$q = $g_pdo->prepare($request);
			$q->execute(array(":title" => $title));
			$count = $q->fetch();
			return $count[0] > 0;
		} catch (Exception $e) {
			println($e->getMessage());
			throw $e;
		}
	}
	
	function add_event($title, $content, $date, $person) {
		try {
			global $g_pdo;
			
			if (event_exists($title)) {
				return FALSE;
			}
			list($day, $month, $year) = explode(".", $date);
			$timestamp = mktime(0, 0, 0, $day, $month, $year);
			$dbname = MYSQL_DBNAME;
			$content = check($content);
			$author = get_user_by_login($_SESSION["login"]);
			if ($author == NULL) {
				throw new Exception("Not logged");
			}
			$author_id = $author["id"];
			$id = create_id();
			$request = <<<EOF
INSERT INTO `${dbname}`.`event` 
SET 
	`id`="${id}",
	`author_id`="${author_id}", 
	`title`="${title}", 
	`event_date`="${timestamp}", 
	`content`="${content}", 
	`nbr_person_wanted`="${person}"
EOF;
			//debug($request);
			$g_pdo->exec($request);
			return TRUE;
		} catch (Exception $e) {
			println($e->getMessage());
			throw $e;
		}
	}
	
	function get_event($id) {
		try {
			global $g_pdo;
			$dbname = MYSQL_DBNAME;
			$request = <<<EOF
SELECT * FROM `${dbname}`.`event`
WHERE `id`= :id
EOF;
			$q = $g_pdo->prepare($request);
			$q->execute(array(":id" => $id));
			$event = $q->fetch();
			if (!isset($event['id'])) {
				return NULL;
			}
			return $event;
		} catch (Exception $e) {
			println($e->getMessage());
			throw $e;
		}
	}
	
	function list_events() {
		try {
			global $g_pdo;
			$dbname = MYSQL_DBNAME;
			$request = <<<EOF
SELECT `id`, `title`, `event_date` FROM `${dbname}`.`event` ORDER BY `event_date`
EOF;
			$q = $g_pdo->prepare($request);
			$q->execute();
			$event = $q->fetch();
			$events = array();
			while (isset($event["id"])) {
				$events[] = $event;
				$event = $q->fetch();
			}
			return $events;
		} catch (Exception $e) {
			println($e->getMessage());
			throw $e;
		}
	}
?>