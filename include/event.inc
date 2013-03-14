<?php
	require_once('include/misc.inc');
	require_once("include/user.inc");

	function event_title_exists($title) {
		global $g_pdo;
		$dbname = MYSQL_DBNAME;
		$request = <<<EOF
SELECT COUNT(*) FROM `${dbname}`.`event` WHERE `title`= :title
EOF;
		$q = $g_pdo->prepare($request);
		$q->execute(array(":title" => $title));
		$count = $q->fetch();
		return $count[0] > 0;
	}

	function event_exists($id) {
		global $g_pdo;
		$dbname = MYSQL_DBNAME;
		$request = <<<EOF
SELECT COUNT(*) FROM `${dbname}`.`event` WHERE `id`= :id
EOF;
		$q = $g_pdo->prepare($request);
		$q->execute(array(":id" => $id));
		$count = $q->fetch();
		return $count[0] > 0;
	}

	function event_create($id, $title, $date, $deadline, $funding_wanted, $location,
						$link, $short, $long, $nominative) {
		global $g_pdo;

		list($year, $month, $day) = explode("-", $date);
		$event_date = mktime(0, 0, 0, $month, $day, $year);
		list($year, $month, $day) = explode("-", $deadline);
		$event_deadline = mktime(0, 0, 0, $month, $day, $year);

		$title = check($title);
		$link = check($link);
		$long = check($long);
		$short = check($short);
		$location = check($location);
		$dbname = MYSQL_DBNAME;
		$id_user = get_id_from_account();
		$created_t = time();
		$mod_t = $created_t;
		$status = EVENT_STATUS_SUBMITTED;
		$request = <<<EOF
INSERT INTO `${dbname}`.`event`
SET
	`id`="${id}",
	`created_t`="${created_t}",
	`mod_t`="${mod_t}",
	`id_user`="${id_user}",
	`title`="${title}",
	`location`="${location}",
	`link`="${link}",
	`short_description`="${short}",
	`long_description`="${long}",
	`event_date`="${event_date}",
	`event_deadline`="${event_deadline}",
	`funding_wanted`="${funding_wanted}",
	`nominative`=${nominative},
	`status`=${status}
EOF;
		debug($request);
		$g_pdo->exec($request);
		return TRUE;
	}

	function event_update($id, $title, $content, $date, $person) {
		global $g_pdo;

		list($day, $month, $year) = explode(".", $date);
		$timestamp = mktime(0, 0, 0, $day, $month, $year);
		$dbname = MYSQL_DBNAME;
		$content = check($content);
		$author = get_user_by_login($_SESSION["login"]);
		if ($author == NULL) {
			throw new Exception("Not logged");
		}
		$id_user = $author["id"];
		$mod_t = time();
		$request = <<<EOF
UPDATE `${dbname}`.`event`
SET
	`id_user`="${id_user}",
	`mod_t`="${id_user}",
	`title`="${title}",
	`event_date`="${timestamp}",
	`content`="${content}",
	`nbr_person_wanted`="${person}"
WHERE `id`="${id}"
EOF;
		debug($request);
		return $g_pdo->exec($request);
	}

	function event_add_person($id, $nbr) {
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
	}

	function get_event($id) {
		global $g_pdo;
		$dbname = MYSQL_DBNAME;
		$request = <<<EOF
SELECT * FROM `${dbname}`.`event`
WHERE `id`= ${id}
EOF;
		debug($request);
		$q = $g_pdo->prepare($request);
		$q->execute();
		$event = $q->fetch();
		if (!isset($event['id'])) {
			return NULL;
		}
		return $event;
	}

	function event_check_owner($event) {
		if ($event['id_user'] != get_id_from_account() && !is_admin()) {
			throw new Exception("You are not the creator of this event");
		}
	}

	function event_delete($id) {
		global $g_pdo;
		$dbname = MYSQL_DBNAME;
		$request = <<<EOF
DELETE FROM `${dbname}`.`event`
WHERE `id`= ${id}
EOF;
		debug($request);
		$g_pdo->exec($request);
	}

	function event_list() {
		global $g_pdo;
		$dbname = MYSQL_DBNAME;
		$request = <<<EOF
SELECT * FROM `${dbname}`.`event` ORDER BY `event_date`
EOF;
		debug($request);
		$q = $g_pdo->prepare($request);
		$q->execute();
		$event = $q->fetch();
		$events = array();
		while (isset($event["id"])) {
			$events[] = $event;
			$event = $q->fetch();
		}
		return $events;
	}

	function user_events($user_id) {
		global $g_pdo;
		$dbname = MYSQL_DBNAME;
		$request = <<<EOF
SELECT `id`, `title`, `event_date` FROM `${dbname}`.`event`
WHERE `id_user`=${user_id}
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
	}

	function event_is_confirmed($event) {
		if ($event['funding_wanted'] <= $event['funding_acquired']) {
			return TRUE;
		}
		return FALSE;
	}

	function event_valid($id) {
		global $g_pdo;

		$status = EVENT_STATUS_VALIDATED;
		$request = <<<EOF
UPDATE `event`
SET
	`status`=${status}
WHERE `id`="${id}"
EOF;
		debug($request);
		return $g_pdo->exec($request);
	}
?>