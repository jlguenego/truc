<?php
	require_once('include/misc.inc');
	
	function user_exists($login) {
		try {
			global $g_pdo;
			$dbname = MYSQL_DBNAME;
			$request = <<<EOF
SELECT COUNT(*) FROM `${dbname}`.`user` WHERE `login`= :login
EOF;
			$q = $g_pdo->prepare($request);
			$q->execute(array(":login" => $login));
			$count = $q->fetch();
			return $count[0] > 0;
		} catch (Exception $e) {
			println($e->getMessage());
			throw $e;
		}
	}
	
	function used_mail($mail) {
		try {
			global $g_pdo;
			$dbname = MYSQL_DBNAME;
			$request = <<<EOF
SELECT COUNT(*) FROM `${dbname}`.`user` WHERE `email`= :mail
EOF;
			$q = $g_pdo->prepare($request);
			$q->execute(array(":mail" => $mail));
			$count = $q->fetch();
			return $count[0] > 0;
		} catch (Exception $e) {
			println($e->getMessage());
			throw $e;
		}
	}
	
	// Check if the user has entered correct login and password
	function authenticate($login, $password) {
		try {
			global $g_pdo;
			if (!user_exists($login)) {
				return FALSE;
			}
			$user = get_user_by_login($login);
			$dbname = MYSQL_DBNAME;
			$request = <<<EOF
SELECT COUNT(*) FROM `${dbname}`.`user`
WHERE `login`= :login AND `password`= :password
EOF;
			$q = $g_pdo->prepare($request);
			$q->execute(array(":login" => $login, ":password" => sha1($user['id'].$login.$password)));
			$count = $q->fetch();
			return $count[0] > 0;
		} catch (Exception $e) {
			println($e->getMessage());
			throw $e;
		}
	}
	
	function get_user_by_login($login) {
		try {
			global $g_pdo;
			$dbname = MYSQL_DBNAME;
			$request = <<<EOF
SELECT * FROM `${dbname}`.`user`
WHERE `login`= :login
EOF;
			$q = $g_pdo->prepare($request);
			$q->execute(array(":login" => $login));
			$user = $q->fetch();
			if (!isset($user['id'])) {
				return NULL;
			}
			return $user;
		} catch (Exception $e) {
			println($e->getMessage());
			throw $e;
		}
	}
	
	function get_user($id) {
		try {
			global $g_pdo;
			$dbname = MYSQL_DBNAME;
			$request = <<<EOF
SELECT * FROM `${dbname}`.`user`
WHERE `id`= :id
EOF;
			$q = $g_pdo->prepare($request);
			$q->execute(array(":id" => $id));
			$user = $q->fetch();
			if (!isset($user['id'])) {
				return NULL;
			}
			return $user;
		} catch (Exception $e) {
			println($e->getMessage());
			throw $e;
		}
	}

	function add_user($name, $lastname, $login, $password, $mail, $admin = FALSE) {
		try {
			global $g_pdo;
			$dbname = MYSQL_DBNAME;
			$id = create_id();
			// With this method it is quite impossible to know who has the same 
			// password
			$password = sha1($id.$login.$password);
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
