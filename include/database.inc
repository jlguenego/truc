<?php
	require_once('include/misc.inc');
	
	// Check if the user has entered correct login and password
	function authenticate($login, $password) {
		try {
			global $g_pdo;
			$dbname = MYSQL_DBNAME;
			$request = <<<EOF
SELECT COUNT(*) FROM `${dbname}`.`user`
WHERE `login`= :login AND `password`= :password
EOF;
			$q = $g_pdo->prepare($request);
			$q->execute(array(":login" => $login, ":password" => sha1($password)));
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
			
			list($day, $month, $year) = explode("/", $date);
			$timestamp = mktime(0, 0, 0, $day, $month, $year);
			$dbname = MYSQL_DBNAME;
			$content = check($content);
			$request = <<<EOF
INSERT INTO `${dbname}`.`event` 
SET 
	`title`="${title}", 
	`event_date`="${timestamp}", 
	`content`="${content}", 
	`nbr_person_wanted`="${person}"
EOF;
			debug($request);
			$g_pdo->exec($request);
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

	function add_user($name, $lastname, $login, $password, $mail, $admin = FALSE) {
		try {
			global $g_pdo;
			$dbname = MYSQL_DBNAME;
			$id = rand(0, 10) * date("ymd") * date("Hi");
			debug(sha1($password));
			$password = sha1($password);
			$request = <<<EOF
INSERT INTO `${dbname}`.`user` 
SET 
	`id`=${id}, 
	`name`="${name}", 
	`lastname`="${lastname}", 
	`login`="${login}", 
	`password`="${password}", 
	`email`="${mail}", 
	`admin`=${admin};
EOF;
			debug($request);
			$g_pdo->exec($request);
		} catch (Exception $e) {
			println($e->getMessage());
			throw $e;
		}
	}
?>
