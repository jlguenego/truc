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
	`mod_t`="${mod_t}",
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
		$request = <<<EOF
SELECT `id` FROM `event` ORDER BY `event_date`
EOF;
		debug($request);
		$q = $g_pdo->prepare($request);
		$q->execute();
		$event_record = $q->fetch();
		$events = array();
		while (isset($event_record["id"])) {
			$event = new Event();
			$event->load($event_record["id"]);
			$events[] = $event;
			$event_record = $q->fetch();
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

	// Planned, Confirmed, Cancelled.
	function event_set_status($id, $status) {
		global $g_pdo;

		$request = <<<EOF
UPDATE `event`
SET
	`status`=${status}
WHERE `id`="${id}"
EOF;
		debug($request);
		return $g_pdo->exec($request);
	}

	function event_set_publish_flag($id, $flag) {
		global $g_pdo;

		$request = <<<EOF
UPDATE `event`
SET
	`publish_flag`=${flag}
WHERE `id`="${id}"
EOF;
		debug($request);
		return $g_pdo->exec($request);
	}

	function event_get_participants_mails($event_id) {
		global $g_pdo;

		$request = <<<EOF
SELECT `id_user` FROM `devis`
WHERE `id_event`=${event_id}
EOF;
		$q = $g_pdo->prepare($request);
		if ($q->execute() === FALSE) {
			debug($request);
			throw new Exception("Get event participants mails: ".sprint_r($g_pdo->errorInfo())." InnoDB?");
		};

		$mails = array();
		while ($tbl = $q->fetch()) {
			$user_id = $tbl[0];
			$user = get_user($user_id);
			$mails[] = $user['email'];
		}
		debug("mails=".sprint_r($mails));
		return $mails;
	}

	function event_add_funding_aquired($id, $amount) {
		global $g_pdo;

		$event = get_event($id);
		$funding_acquired = $event["funding_acquired"] + $amount;
		$mod_t = time();
		$request = <<<EOF
UPDATE `event`
SET
	`mod_t`="${mod_t}",
	`funding_acquired`="${funding_acquired}"
WHERE `id`="${id}"
EOF;
		debug($request);
		return $g_pdo->exec($request);
	}
?>