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

	function add_event($id, $title, $date, $deadline, $funding_wanted, $location,
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
	`nominative`=${nominative}
EOF;
		debug($request);
		$g_pdo->exec($request);
		return TRUE;
	}

	function update_event($id, $title, $content, $date, $person) {
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
		$g_pdo->exec($request);
		return TRUE;
	}

	function add_person_to_event($id, $nbr) {
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

	function check_owner($event) {
		if ($event['id_user'] != get_id_from_account() && !is_admin()) {
			throw new Exception("You are not the creator of this event");
		}
	}

	function delete_event() {
		if (isset($_GET['confirm'])) {
			if (is_null_or_empty($_GET['id'])) {
				throw new Exception("No event given.");
			}
			if (!event_exists($_GET['id'])) {
				throw new Exception("Event does not exists.");
			}
			$event = get_event($_GET['id']);
			$user = get_user_by_login($_SESSION["login"]);
			check_owner($event);
			if($_GET['confirm'] != "yes") {
				redirect_to("index.php");
			} else {
				delete_events_rates($_GET['id']);
				erase_event($_GET['id']);
				redirect_to("index.php");
			}
		}
	}

	function erase_event($id) {
		global $g_pdo;
		$dbname = MYSQL_DBNAME;
		$request = <<<EOF
DELETE FROM `${dbname}`.`event`
WHERE `id`= ${id}
EOF;
		debug($request);
		$g_pdo->exec($request);
	}

	function list_events() {
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

	function valid_event($update = FALSE) {
		if (is_null_or_empty($_GET["title"])) {
			throw new Exception("Event must have a title");
		}
		if (!$update && event_title_exists($_GET["title"])) {
			throw new Exception("Event already exists");
		}

		foreach ($_GET as $value) {
			if (is_null_or_empty($value)) {
				throw new Exception("Please fill all fields");
			}
		}

		foreach ($_GET['rates'] as $rate) {
			if (is_null_or_empty($rate)
				|| (!is_null_or_empty($rate) && !is_number($rate))) {

				throw new Exception("Please enter a number for the rates");
			}
		}

		foreach ($_GET['tax_rates'] as $tax_rate) {
			if (is_null_or_empty($tax_rate)
				|| (!is_null_or_empty($tax_rate) && !is_number($tax_rate))) {

				throw new Exception("Please enter a number for the tax rates");
			}
		}
		foreach ($_GET['labels'] as $label) {
			if (is_null_or_empty($label)) {
				throw new Exception("Please enter a label for each rate");
			}
		}
	}

	function event_is_confirmed($event) {
		if ($event['funding_wanted'] <= $event['funding_acquired']) {
			return TRUE;
		}
		return FALSE;
	}
?>