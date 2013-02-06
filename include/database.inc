<?php
	require_once('include/misc.inc');
	
	// Check if the user has entered correct login and password
	function authenticate($login, $password) {
		try {
			global $g_pdo;
			$dbname = MYSQL_DBNAME;
			$request = <<<EOF
SELECT COUNT(*) FROM `${dbname}`.`user`
WHERE `login`= ":login" AND `password`= ":password";
EOF;
			$q = $g_pdo->prepare($request);
			$q->execute(array(":login" => $login, ":password" => sha1($password)));
			$count = $q->fetch();
			debug("SELECT COUNT(*) FROM `${dbname}`.`user`
WHERE `login`= \"$login\" AND `password`= \"".sha1($password)."\"");
			return $count[0] > 0;
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
			debug($id);
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