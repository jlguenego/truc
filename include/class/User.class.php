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
		public $address;

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

		public function set_passwd($passwd) {
			$this->password = sha1($this->id.$this->login.$passwd);
		}

		public function update() {
			global $g_pdo;

			$mod_t = time();
			$request = <<<EOF
UPDATE `user`
SET
	`password`="{$this->password}",
	`email`="{$this->email}",
	`mod_t`="${mod_t}"
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

		public function set_reset_passwd_token() {
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
	}
?>