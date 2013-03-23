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
			$this->password = sha1($this->id.$this->login.$passwd);
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
			$user = get_user($this->id);
			if (!is_activated($user)) {
				mail_inscription($this->email, $this->login, $this->activation_key);
			}
		}

		public function update() {
			global $g_pdo;

			$mod_t = time();
			$request = <<<EOF
UPDATE `user`
SET
	`mod_t`=${mod_t},
	`firstname`="{$this->firstname}",
	`lastname`="{$this->lastname}",
	`password`="{$this->password}",
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

		public static function get_from_login($login) {
			global $g_pdo;

			$request = <<<EOF
SELECT * FROM `user`
WHERE `login`="${login}"
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
			$user = $q->fetch();
			if (!isset($user['id'])) {
				return NULL;
			}
			unset($user["password"]);
			$user["lastname"] = strtoupper($user["lastname"]);
			$user["firstname"] = ucfirst($user["firstname"]);
			$this->hydrate($user);
		}

		public function address() {
			if ($this->state != "") {
				$this->state = " " . $this->state;
			}
			return $this->street." ".$this->zip." ".$this->city.
				$this->state.", ".$this->country;
		}
	}
?>