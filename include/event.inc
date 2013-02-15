<?php
	require_once('include/misc.inc');
	require_once("include/user.inc");
	
	function event_title_exists($title) {
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
	
	function event_exists($id) {
		try {
			global $g_pdo;
			$dbname = MYSQL_DBNAME;
			$request = <<<EOF
SELECT COUNT(*) FROM `${dbname}`.`event` WHERE `id`= :id
EOF;
			$q = $g_pdo->prepare($request);
			$q->execute(array(":id" => $id));
			$count = $q->fetch();
			return $count[0] > 0;
		} catch (Exception $e) {
			println($e->getMessage());
			throw $e;
		}
	}
	
	function add_event($id, $title, $content, $date, $person) {
		try {
			global $g_pdo;
			
			if (event_title_exists($title)) {
				return FALSE;
			}
			list($day, $month, $year) = explode(".", $date);
			$timestamp = mktime(0, 0, 0, $month, $day, $year);
			$dbname = MYSQL_DBNAME;
			$content = check($content);
			$author = get_user_by_login($_SESSION["login"]);
			if ($author == NULL) {
				throw new Exception("Not logged");
			}
			$author_id = $author["id"];
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
	
	function update_event($id, $title, $content, $date, $person) {
		try {
			global $g_pdo;
			
			if (!event_exists($id)) {
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
			$request = <<<EOF
UPDATE `${dbname}`.`event` 
SET 
	`author_id`="${author_id}", 
	`title`="${title}", 
	`event_date`="${timestamp}", 
	`content`="${content}", 
	`nbr_person_wanted`="${person}"
WHERE `id`="${id}"
EOF;
			$g_pdo->exec($request);
			return TRUE;
		} catch (Exception $e) {
			println($e->getMessage());
			throw $e;
		}
	}
	
	function add_person_to_event($id, $nbr) {
		try {
			global $g_pdo;
			
			if (!event_exists($id)) {
				return FALSE;
			}
			$dbname = MYSQL_DBNAME;
			$event = get_event($id);
			$nbr += $event['nbr_person_registered'];
			$request = <<<EOF
UPDATE `${dbname}`.`event` 
SET 
	`nbr_person_registered`="${nbr}"
WHERE `id`="${id}"
EOF;
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
	
	function delete_event($id) {
		try {
			global $g_pdo;
			$dbname = MYSQL_DBNAME;
			$request = <<<EOF
DELETE FROM `${dbname}`.`event`
WHERE `id`= ${id}
EOF;
			$g_pdo->exec($request);
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
	
	function user_events($user_id) {
		try {
			global $g_pdo;
			$dbname = MYSQL_DBNAME;
			$request = <<<EOF
SELECT `id`, `title`, `event_date` FROM `${dbname}`.`event` 
WHERE `author_id`=${user_id}
ORDER BY `event_date`
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
	
	function add_rate($label, $rate, $id) {
		debug("$label|$rate|$id");
	}
?>