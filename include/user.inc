<?php
	require_once('include/misc.inc');
	
	function is_logged() {
		if (!is_null_or_empty($_SESSION['login'])) {
			if (user_login_exists($_SESSION['login'])) {
				return TRUE;
			} else {
				unset($_SESSION['login']);
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}
	
	function need_authentication() {
		if (!is_logged()) {
			debug("\$_SERVER[\"REQUEST_URI\"]=".$_SERVER["REQUEST_URI"]);
			$_SESSION["url"] = $_SERVER["REQUEST_URI"];
			redirect_to("?action=sign_in");
		}
	}
	
	function is_admin() {
		if (!is_logged()) {
			return FALSE;
		}
		$user = get_user_by_login($_SESSION['login']);
		return $user['role'] == ROLE_ADMIN;
	}
	
	function hash_pass($id, $login, $password) {
		return sha1($id.$login.$password);
	}
	
	function user_exists($id) {
		global $g_pdo;
		$dbname = MYSQL_DBNAME;
		$request = <<<EOF
SELECT COUNT(*) FROM `${dbname}`.`user` WHERE `id`= :id
EOF;
		$q = $g_pdo->prepare($request);
		$q->execute(array(":id" => $id));
		$count = $q->fetch();
		return $count[0] > 0;
	}
	
	function user_login_exists($login) {
		global $g_pdo;
		$dbname = MYSQL_DBNAME;
		$request = <<<EOF
SELECT COUNT(*) FROM `${dbname}`.`user` WHERE `login`= :login
EOF;
		$q = $g_pdo->prepare($request);
		$q->execute(array(":login" => $login));
		$count = $q->fetch();
		return $count[0] > 0;
	}
	
	function used_mail($mail) {
		global $g_pdo;
		$dbname = MYSQL_DBNAME;
		$request = "";
		if (isset($_SESSION['login'])) {
			$user = get_user_by_login($_SESSION['login']);
			$request = <<<EOF
SELECT COUNT(*) FROM `${dbname}`.`user` WHERE `email`= :mail AND `id`!= :id
EOF;
			$q = $g_pdo->prepare($request);
			$q->execute(array(":mail" => $mail, ":id" => $user['id']));
		} else {
			$request = <<<EOF
SELECT COUNT(*) FROM `${dbname}`.`user` WHERE `email`= :mail
EOF;
			$q = $g_pdo->prepare($request);
			$q->execute(array(":mail" => $mail));
		}
		$count = $q->fetch();
			return $count[0] > 0;
	}
	
	// Check if the user has entered correct login and password
	function authenticate($login, $password) {
		global $g_pdo;
		if (!user_login_exists($login)) {
			return FALSE;
		}
		$user = get_user_by_login($login);
		$dbname = MYSQL_DBNAME;
		$request = <<<EOF
SELECT COUNT(*) FROM `${dbname}`.`user`
WHERE `login`= :login AND `password`= :password
EOF;
			debug("SELECT COUNT(*) FROM `${dbname}`.`user`
WHERE `login`= ${login} AND `password`= ".hash_pass($user['id'], $login, $password));
		$q = $g_pdo->prepare($request);
		$q->execute(array(":login" => $login, 
			":password" => hash_pass($user['id'], $login, $password)));
		$count = $q->fetch();
		return $count[0] > 0;
	}
	
	function get_id_from_account() {
		global $g_pdo;
		$dbname = MYSQL_DBNAME;
		$request = <<<EOF
SELECT `id` FROM `${dbname}`.`user`
WHERE `login`= :login
EOF;
		$q = $g_pdo->prepare($request);
		$q->execute(array(":login" => $_SESSION["login"]));
		$user = $q->fetch();
		if (!isset($user['id'])) {
			return NULL;
		}
		return $user["id"];
	}
	
	function get_user_by_login($login) {
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
	}
	
	function get_user($id) {
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
		$user["lastname"] = strtoupper($user["lastname"]);
		$user["firstname"] = ucfirst($user["firstname"]);
		return $user;
	}

	function add_user($firstname, $lastname, $login, $password, $mail, $role = ROLE_USER) {
		global $g_pdo;
		$dbname = MYSQL_DBNAME;
		$id = create_id();
		// With this method it is quite impossible to know who has the same 
		// password
		$password = hash_pass($id, $login, $password);
		$request = <<<EOF
INSERT INTO `${dbname}`.`user` 
SET 
	`id`=${id}, 
	`firstname`="${firstname}", 
	`lastname`="${lastname}", 
	`login`="${login}", 
	`password`="${password}", 
	`email`="${mail}", 
	`role`=${role};
EOF;
		debug($request);
		if ($g_pdo->exec($request) == 0) {
			throw new Exception("Bad Request...");
		}
	}
	
	function update_user($id, $password, $mail) {
		try {
			global $g_pdo;
			$dbname = MYSQL_DBNAME;
			// With this method it is quite impossible to know who has the same 
			// password
			$password = hash_pass($id, $_SESSION['login'], $password);
			$request = <<<EOF
UPDATE `${dbname}`.`user` 
SET 
	`password`="${password}", 
	`email`="${mail}"
WHERE `id`=${id}
EOF;
			debug($request);
			$g_pdo->exec($request);
		} catch (Exception $e) {
			println($e->getMessage());
			throw $e;
		}
	}
	
	function valid_user() {
		if (isset($_GET['login'])) {
			foreach ($_GET as $value) {
				if ($value == "") {
					throw new Exception("Please fill all fields");
				}
			}
			if (!check_mail($_GET["email"])) {
				throw new Exception("Invalid mail");
			}
			if (used_mail($_GET["email"])) {
				throw new Exception("This mail is already used");
			} 
			if (user_login_exists($_GET['login'])) {
				throw new Exception("User already exists");
			}
			if ($_GET['password'] != $_GET['password2']) {
				throw new Exception("Passwords are different");
			}
			require_once('recaptcha.inc');
		    $privatekey = CAPTCHA_PRIV_KEY;
		    $resp = recaptcha_check_answer ($privatekey,
										$_SERVER["REMOTE_ADDR"],
										$_POST["recaptcha_challenge_field"],
										$_POST["recaptcha_response_field"]);

			if (!$resp->is_valid) {
				// What happens when the CAPTCHA was entered incorrectly
				throw new Exception ("The reCAPTCHA wasn't entered correctly.");
			}
		}
	}
	
	function valid_user_update() {
		global $g_display;
		if (isset($_GET['password'])) {
			$user = $g_display["user"];
			if ($user['password'] != hash_pass($user['id'], $_SESSION['login'], $_GET['password'])) {
				throw new Exception(_t("Wrong password."));
			}
			if ($_GET["email"] != "") {
				if (!check_mail($_GET["email"])) {
					throw new Exception(_t("Invalid mail."));
				}
				if (used_mail($_GET["email"])) {
					throw new Exception(_t("This mail is already used."));
				}
				$user['email'] = $_GET['email'];
			}
			if ($_GET['new_pass'] != "" || $_GET['new_pass2'] != "") {
				if ($_GET['new_pass'] != $_GET['new_pass2']) {
					throw new Exception(_t("New passwords are different."));
				}
				$user['password'] = $_GET['new_pass'];
			} else {
				$user['password'] = $_GET['password'];
			}
		}
	}
?>
