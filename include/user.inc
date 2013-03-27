<?php
	require_once(BASE_DIR . '/include/misc.inc');

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

	function user_events($user_id) {
		global $g_pdo;

		$request = <<<EOF
SELECT `id`, `title`, `happening_t` FROM `event`
WHERE `id_user`=${user_id}
ORDER BY `happening_t`
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

	function is_activated($user) {
		return $user["activation_status"] == ACTIVATION_STATUS_ACTIVATED;
	}

	function need_authentication() {
		if (!is_logged()) {
			debug("\$_SERVER[\"REQUEST_URI\"]=".$_SERVER["REQUEST_URI"]);
			$_SESSION["url"] = $_SERVER["REQUEST_URI"];
			redirect_to("?action=sign_in&redirect=yes");
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
		global $g_error_msg;
		if (!user_login_exists($login)) {
			return FALSE;
		}
		$dbname = MYSQL_DBNAME;
		$request = <<<EOF
SELECT COUNT(*) FROM `${dbname}`.`user`
WHERE `login`= :login AND `password`= :password
EOF;
		$q = $g_pdo->prepare($request);
		$user = get_user_by_login($login);
		$q->execute(array(":login" => $login,
			":password" => hash_pass($user['id'], $login, $password)));
		$count = $q->fetch();
		return $count[0] > 0;
	}

	function get_id_from_account() {
		global $g_pdo;

		$request = <<<EOF
SELECT `id` FROM `user`
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

		$request = <<<EOF
SELECT * FROM `user`
WHERE `id`= ${id}
EOF;
		debug($request);
		$q = $g_pdo->prepare($request);
		$q->execute();
		$user = $q->fetch();
		if (!isset($user['id'])) {
			return NULL;
		}
		unset($user["password"]);
		$user["lastname"] = strtoupper($user["lastname"]);
		$user["firstname"] = ucfirst($user["firstname"]);
		return $user;
	}

	function generate_activation_key() {
		return sha1(rand().time().RANDOM_SALT);
	}

	function user_activate($key) {
		global $g_pdo;
		$dbname = MYSQL_DBNAME;
		$request = <<<EOF
SELECT COUNT(*) FROM `${dbname}`.`user`
WHERE `activation_key`="${key}"
EOF;
		$q = $g_pdo->prepare($request);
		$q->execute();
		$count = $q->fetch();
		if ($count[0] == 0) {
			throw new Exception("This account does not exist or is already activated");
		}
		$mod_t = time();
		$status = ACTIVATION_STATUS_ACTIVATED;
		$request = <<<EOF
UPDATE `${dbname}`.`user`
SET
	`activation_status`="${status}",
	`mod_t`="${mod_t}"
WHERE `activation_key`="${key}"
EOF;
		$q = $g_pdo->prepare($request);
		$q->execute();$request = <<<EOF
SELECT * FROM `${dbname}`.`user`
WHERE `activation_key`="${key}"
EOF;
		debug($request);
		$q = $g_pdo->prepare($request);
		$q->execute();
		$user = $q->fetch();
		return $user;
	}

	function valid_user() {
		foreach ($_GET as $value) {
			if ($value == "") {
				throw new Exception("Please fill all fields");
			}
		}
		if (user_login_exists($_GET['login'])) {
			throw new Exception("User already exists");
		}
		if (!check_mail($_GET["email"])) {
			throw new Exception("Invalid mail");
		}
		if (used_mail($_GET["email"])) {
			throw new Exception("This mail is already used");
		}
		if ($_GET['password'] != $_GET['password2']) {
			throw new Exception("Passwords are different");
		}
		require_once(BASE_DIR . '/include/recaptcha.inc');
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

	function valid_user_update($user) {
		if (!is_null_or_empty($_GET["firstname"])
			&& $user->firstname != $_GET["firstname"]) {
			$user->firstname = $_GET["firstname"];
		}
		if (!is_null_or_empty($_GET["lastname"])
			&& $user->lastname != $_GET["lastname"]) {
			$user->lastname = $_GET["lastname"];
		}
		if (!is_null_or_empty($_GET["street"])
			&& $user->street != $_GET["street"]) {
			$user->street = $_GET["street"];
		}
		if (!is_null_or_empty($_GET["zip"])
			&& $user->zip != $_GET["zip"]) {
			$user->zip = $_GET["zip"];
		}
		if (!is_null_or_empty($_GET["city"])
			&& $user->city != $_GET["city"]) {
			$user->city = $_GET["city"];
		}
		if (!is_null_or_empty($_GET["country"])
			&& $user->country != $_GET["country"]) {
			$user->country = $_GET["country"];
		}
		$user->state = $_GET["state"];
		if (!is_null_or_empty($_GET["email"])) {
			if (!check_mail($_GET["email"])) {
				throw new Exception(_t("Invalid mail."));
			}
			if (used_mail($_GET["email"])) {
				throw new Exception(_t("This mail is already used."));
			}
			$user->email = $_GET['email'];
		}
		if (!is_null_or_empty($_GET['new_pass'])) {
			if ($_GET['new_pass'] != $_GET['new_pass2']) {
				throw new Exception(_t("New passwords are different."));
			}
			$user->password = $_GET['new_pass'];
		} else {
			$user->password = "";
		}
		debug("user=".  sprint_r($user));
	}

	function user_can_administrate_event($event) {
		if (is_admin() || $event->id_user == get_id_from_account()) {
			debug("Can administrate.");
			return TRUE;
		}
		debug("Cannot administrate.");
		return FALSE;
	}
?>
