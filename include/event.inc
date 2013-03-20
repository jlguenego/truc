<?php
	require_once(BASE_DIR . '/include/misc.inc');
	require_once(BASE_DIR . "/include/user.inc");

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

	function event_list() {
		global $g_pdo;
		$request = <<<EOF
SELECT `id` FROM `event` ORDER BY `happening_t`
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