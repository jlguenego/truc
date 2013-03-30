<?php
	class User {
		public $id;
		public $login;
		public $password;
		public $email;
		public $lastname;
		public $firstname;
		public $role;
		public $activation_status;
		public $activation_key;
		public $street;
		public $zip;
		public $city;
		public $country;
		public $state;

		public static function get_from_id($id) {
			$user = new User();
			$user->load($id);
			return $user;
		}

		public function check_owner() {
			debug("login=".$this->login." | session_login=".$_SESSION["login"]);
			return ($this->login == $_SESSION["login"]) || is_admin();
		}

		public function delete_try() {
			global $g_pdo;

			foreach ($this->list_event() as $event) {
				$event->delete_try();
			}

			if ($this->has_accountancy_activity() ) {
				debug("Account has accountancy");
				$this->activation_status = ACTIVATION_STATUS_INACTIVATED;
				$this->password = "inactivated";
				$this->email .= " (inactivated)";
				$this->update();
				return;
			}

			$request = <<<EOF
DELETE FROM `user`
WHERE `id`={$this->id}
EOF;
			debug($request);
			if(!$g_pdo->exec($request)) {
				throw new Exception("User delete: " . sprint_r($g_pdo->errorInfo()));
			}
		}

		public static function exists($id) {
			global $g_pdo;

			$request = <<<EOF
SELECT COUNT(*) FROM `user` WHERE `id`= :id
EOF;
			$q = $g_pdo->prepare($request);
			$q->execute(array(":id" => $id));
			$count = $q->fetch();
			return $count[0] > 0;
		}

		private function hydrate($array) {
			foreach ($array as $key => $value) {
				$this->$key = $value;
			}
		}

		public function set_token($token) {
			global $g_pdo;
			$request = <<<EOF
UPDATE * FROM `user`
SET `token`="${token}"
WHERE `id`={$this->id}
EOF;
			debug($request);
			$q = $g_pdo->prepare($request);
			$q->execute();
		}

		public function set_password($passwd) {
			$this->password = $this->make_password($passwd);
		}

		public function make_password($passwd) {
			return sha1($this->login.$passwd);
		}

		public function store() {
			global $g_pdo;

			$created_t = time();
			$mod_t = $created_t;
			$request = <<<EOF
INSERT INTO `user`
SET
	`id`={$this->id},
	`created_t`=${created_t},
	`mod_t`=${mod_t},
	`firstname`="{$this->firstname}",
	`lastname`="{$this->lastname}",
	`login`="{$this->login}",
	`password`="{$this->password}",
	`email`="{$this->email}",
	`role`={$this->role},
	`activation_status`={$this->activation_status},
	`activation_key`="{$this->activation_key}",
	`street`="{$this->street}",
	`zip`="{$this->zip}",
	`city`="{$this->city}",
	`country`="{$this->country}",
	`state`="{$this->state}";

EOF;
			debug($request);
			if ($g_pdo->exec($request) == 0) {
				$error = $g_pdo->errorInfo();
				throw new Exception("User creation: ".$error[2]);
			}
			if (!$this->is_activated()) {
				mail_inscription($this->email, $this->login, $this->activation_key);
			}
		}

		public function update() {
			global $g_pdo;

			if ($this->password == "") {
				$set_password = "";
			} else {
				$set_password = <<<EOF
	`password`="{$this->password}",
EOF;
			}

			$mod_t = time();
			$request = <<<EOF
UPDATE `user`
SET
	`mod_t`=${mod_t},
	`firstname`="{$this->firstname}",
	`lastname`="{$this->lastname}",
	${set_password}
	`email`="{$this->email}",
	`street`="{$this->street}",
	`zip`="{$this->zip}",
	`city`="{$this->city}",
	`country`="{$this->country}",
	`state`="{$this->state}"
WHERE `id`={$this->id}
EOF;
			debug($request);
			$g_pdo->exec($request);
		}

		public static function get_from_login($login = "") {
			global $g_pdo;

			if ($login == "") {
				$login = $_SESSION["login"];
			}
			$request = <<<EOF
SELECT * FROM `user`
WHERE `login`="${login}"
EOF;
			debug($request);
			$q = $g_pdo->prepare($request);
			if (!$q->execute()) {
				throw new Exception("User get from login: ".sprint_r($g_pdo->errorInfo()));
			}
			$record = $q->fetch(PDO::FETCH_ASSOC);
			debug("record=".sprint_r($record));
			if ($record == NULL) {
				return NULL;
			}
			$user = new User();
			$user->hydrate($record);
			return $user;
		}

		public function generate_activation_key() {
			$this->activation_key = sha1(rand().time().RANDOM_SALT);
		}

		public static function get_from_email($email) {
			global $g_pdo;

			$request = <<<EOF
SELECT * FROM `user`
WHERE `email`="${email}"
EOF;
			debug($request);
			$q = $g_pdo->prepare($request);
			$q->execute();
			$record = $q->fetch();
			if ($record == NULL) {
				return NULL;
			}
			$user = new User();
			$user->hydrate($record);
			return $user;
		}

		public function set_reset_password_token() {
			global $g_pdo;

			$exp_ts = time() + 86400;
			$token = $exp_ts . "_" .  rand (0, 1000000);
			$request = <<<EOF
UPDATE `user`
SET `token`="${token}"
WHERE `id`={$this->id}
EOF;
			debug($request);
			$q = $g_pdo->prepare($request);
			$q->execute();
			return $token;
		}

		public static function get_from_token($token) {
			global $g_pdo;

			$request = <<<EOF
SELECT * FROM `user`
WHERE `token`="${token}"
EOF;
			debug($request);
			$q = $g_pdo->prepare($request);
			$q->execute();
			$record = $q->fetch();
			if ($record == NULL) {
				throw new Exception("No user found with this token.");
			}
			$user = new User();
			$user->hydrate($record);

			$user->set_token(NULL);

			$exp_ts = explode("_", $token);
			if ($exp_ts[0] < time()) {
				throw new Exception("This token is expired.");
			}
			return $user;
		}

		public function load($id) {
			global $g_pdo;

			$request = <<<EOF
SELECT * FROM `user`
WHERE `id`= ${id}
EOF;
			debug($request);
			$q = $g_pdo->prepare($request);
			if(!$q->execute()) {
				throw new Exception("User Update: ".sprint_r($g_pdo->errorInfo()));
			}
			$record = $q->fetch();
			if (!isset($record['id'])) {
				return NULL;
			}
			unset($record["password"]);
			$record["lastname"] = mb_strtoupper($record["lastname"], "UTF-8");
			$record["firstname"] = ucfirst($record["firstname"]);
			$this->hydrate($record);
		}

		public function hydrate_from_form() {
			$this->login = $_GET["login"];
			$this->set_password($_GET["password"]);
			$this->email = $_GET["email"];
			$this->lastname = mb_strtoupper($_GET["lastname"], "UTF-8");
			$this->firstname = ucfirst($_GET["firstname"]);
			$this->street = $_GET["street"];
			$this->zip = $_GET["zip"];
			$this->city = $_GET["city"];
			$this->country = $_GET["country"];
			$this->state = $_GET["state"];
		}

		public function address() {
			$state = "";
			if ($this->state != "") {
				$state = $this->state.",&nbsp;";
			}
			$address = $this->street."\n".
				$state.$this->zip." ".$this->city." ".$this->country;
			return $address;
		}

		public function is_activated() {
			debug("activation_status=".$this->activation_status);
			return $this->activation_status == ACTIVATION_STATUS_ACTIVATED;
		}

		public function has_accountancy_activity() {
			foreach ($this->list_event() as $event) {
				if ($event->has_accountancy_activity()) {
					return true;
				}
			}
			return false;
		}

		public function list_event() {
			global $g_pdo;

			$request = <<<EOF
SELECT * FROM `event`
WHERE `id_user`={$this->id}
ORDER BY `happening_t`
EOF;
			$q = $g_pdo->prepare($request);
			$q->execute();
			$events = array();
			while ($record = $q->fetch()) {
				$event = new Event();
				$event->hydrate($record);
				$events[] = $event;
			}
			return $events;
		}

		public function reset_token() {
			global $g_pdo;

			$mod_t = time();
			$request = <<<EOF
UPDATE `user`
SET
	`mod_t`=${mod_t},
	`token`=NULL
WHERE `id`={$this->id}
EOF;
			debug($request);
			$g_pdo->exec($request);
		}
	}
?>