<?php
	class User {
		public $id;
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
			debug("email=".$this->email." | session_email=".$_SESSION["email"]);
			return ($this->email == $_SESSION["email"]) || is_admin_logged();
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
WHERE `id`= :id
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$pst->execute(array(":id" => $this->id));
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
SET `token`= :token
WHERE `id`= :id
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$array = array(
				":token" => $token,
				":id" => $this->id,
			);
			$pst->execute($array);
		}

		public function set_password($passwd) {
			$this->password = $this->make_password($passwd);
		}

		public function make_password($passwd) {
			debug("email=".$this->email);
			return sha1($this->email.$passwd);
		}

		public function store() {
			global $g_pdo;

			$created_t = time();
			$mod_t = $created_t;
			$request = <<<EOF
INSERT INTO `user`
SET
	`id`= :id,
	`created_t`= :created_t,
	`mod_t`= :mod_t,
	`firstname`= :firstname,
	`lastname`= :lastname,
	`password`= :password,
	`email`= :email,
	`role`= :role,
	`activation_status`= :activation_status,
	`activation_key`= :activation_key,
	`street`= :street,
	`zip`= :zip,
	`city`= :city,
	`country`= :country,
	`state`= :state;
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$array = array(
				":id" => $this->id,
				":created_t" => $created_t,
				":mod_t" => $mod_t,
				":firstname" => $this->firstname,
				":lastname" => $this->lastname,
				":password" => $this->password,
				":email" => $this->email,
				":role" => $this->role,
				":activation_status" => $this->activation_status,
				":activation_key" => $this->activation_key,
				":street" => $this->street,
				":zip" => $this->zip,
				":city" => $this->city,
				":country" => $this->country,
				":state" => $this->state,
			);
			$pst->execute($array);
			if (!$this->is_activated()) {
				mail_inscription($this);
			}
		}

		public function update() {
			global $g_pdo;

			$mod_t = time();
			$request = <<<EOF
UPDATE `user`
SET
	`mod_t`= :mod_t,
	`firstname`= :firstname,
	`lastname`= :lastname,
	`street`= :street,
	`zip`= :zip,
	`city`= :city,
	`country`= :country,
	`state`= :state
WHERE `id`= :id
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$array = array(
				":mod_t" => $mod_t,
				":firstname" => $this->firstname,
				":lastname" => $this->lastname,
				":street" => $this->street,
				":zip" => $this->zip,
				":city" => $this->city,
				":country" => $this->country,
				":state" => $this->state,
				":id" => $this->id,
			);
			$pst->execute($array);
			$pst->closeCursor();

			if ($this->password != "") {
				$request = <<<EOF
UPDATE `user`
SET `password`= :password
WHERE `id`= :id
EOF;
				$pst = $g_pdo->prepare($request);
				$array = array(
					":password" => $this->password,
					":id" => $this->id,
				);
				$pst->execute($array);
			}
		}

		public function generate_activation_key() {
			$this->activation_key = sha1(rand().time().RANDOM_SALT);
		}

		public static function get_from_email($email = null) {
			global $g_pdo;

			if ($email == null) {
				$email = $_SESSION["email"];
			}

			$request = <<<EOF
SELECT * FROM `user`
WHERE `email`= :email
EOF;
			debug($request);
			$array = array(":email" => $email);
			debug(sprint_r($array));
			$pst = $g_pdo->prepare($request);
			$pst->execute($array);
			$record = $pst->fetch();
			if ($record == NULL) {
				return NULL;
			}
			debug("User found.");
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
SET `token`= :token
WHERE `id`= :id
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$array = array(
				":token" => $token,
				":id" => $this->id,
			);
			$pst->execute($array);
			return $token;
		}

		public static function get_from_token($token) {
			global $g_pdo;

			$request = <<<EOF
SELECT * FROM `user`
WHERE `token`= :token
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$pst->execute(array(":token" => $token));
			$record = $pst->fetch();
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
WHERE `id`= :id
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$pst->execute(array(":id" => $id));
			$record = $pst->fetch();
			if (!isset($record['id'])) {
				return NULL;
			}
			unset($record["password"]);
			$record["lastname"] = mb_strtoupper($record["lastname"], "UTF-8");
			$record["firstname"] = ucfirst($record["firstname"]);
			$this->hydrate($record);
		}

		public function hydrate_from_form() {
			$this->hydrate($_GET);
			$this->set_password($_GET["password"]);
			$this->email = $_GET["email"];
			$this->lastname = mb_strtoupper($_GET["lastname"], "UTF-8");
			$this->firstname = ucfirst($_GET["firstname"]);
			$this->clean_format();
		}

		public function clean_format() {
			$this->lastname = mb_strtoupper($this->lastname, "UTF-8");
			$this->firstname = ucfirst(mb_strtolower($this->firstname, "UTF-8"));
			$this->country = mb_strtoupper($this->country, "UTF-8");
			$this->city = mb_strtoupper($this->city, "UTF-8");
		}

		public function address() {
			if ($this->has_no_address()) {
				return "Not defined";
			}
			$state = "";
			if ($this->state != "") {
				$state = $this->state.",&nbsp;";
			}
			$address = $this->street."\n".
				$state.$this->zip." ".$this->city." ".$this->country;
			return $address;
		}

		public function has_no_address() {
			return is_null_or_empty($this->street)
				|| is_null_or_empty($this->zip)
				|| is_null_or_empty($this->city)
				|| is_null_or_empty($this->country);
		}

		public function fill_address_from_participation() {
			$this->street = $_GET["address_street"];
			$this->zip = $_GET["address_zip"];
			$this->city = $_GET["address_city"];
			$this->state = $_GET["state"];
			$this->country = $_GET["address_country"];
			$this->update();
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
WHERE `id_user`= :id
ORDER BY `happening_t`
EOF;
			$pst = $g_pdo->prepare($request);
			$pst->execute(array(":id" => $this->id));
			$events = array();
			while ($record = $pst->fetch()) {
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
	`mod_t`= :mod_t,
	`token`=NULL
WHERE `id`= :id
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$array = array(
				":mod_t" => $mod_t,
				":id" => $this->id,
			);
			$pst->execute($array);
		}

		// Check if the user has entered correct email and password
		public static function authenticate($email, $password) {
			global $g_pdo;

			$request = <<<EOF
SELECT COUNT(*) FROM `user`
WHERE `email`= :email AND `password`= :password
EOF;
			$pst = $g_pdo->prepare($request);
			$user = User::get_from_email($email);
			if ($user == null) {
				return false;
			}
			$pst->execute(array(
				":email" => $email,
				":password" => $user->make_password($password)
			));
			$count = $pst->fetch();
			return $count[0] > 0;
		}

		public static function get_id_from_account() {
			global $g_pdo;

			$request = <<<EOF
SELECT `id` FROM `user`
WHERE `email`= :email
EOF;
			$q = $g_pdo->prepare($request);
			$q->execute(array(":email" => $_SESSION["email"]));
			$user = $q->fetch();
			if (!isset($user['id'])) {
				return NULL;
			}
			return $user["id"];
		}

		public static function activate($key) {
			global $g_pdo;

			$request = <<<EOF
SELECT COUNT(*) FROM `user`
WHERE `activation_key`= :key
EOF;
			$pst = $g_pdo->prepare($request);
			$pst->execute(array(":key" => $key));
			$count = $pst->fetch();
			if ($count[0] == 0) {
				throw new Exception("This account does not exist or is already activated");
			}
			$mod_t = time();
			$status = ACTIVATION_STATUS_ACTIVATED;
			$request = <<<EOF
UPDATE `user`
SET
	`activation_status`= :status,
	`mod_t`= :mod_t
WHERE `activation_key`= :key
EOF;
			$pst = $g_pdo->prepare($request);
			$pst->execute(array(
				":status" => $status,
				":mod_t" => $mod_t,
				":key" => $key,
			));
			$request = <<<EOF
SELECT `id` FROM `user`
WHERE `activation_key`= :key
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$pst->execute(array(":key" => $key));
			$record = $pst->fetch();
			$user = User::get_from_id($record["id"]);
			return $user;
		}

		public function get_organized_events() {
			global $g_pdo;

			$request = <<<EOF
SELECT `id`, `title`, `happening_t` FROM `event`
WHERE `id_user`= :id
ORDER BY `happening_t`
EOF;
			$pst = $g_pdo->prepare($request);
			$pst->execute(array(":id" => $this->id));
			$event = $pst->fetch();
			$events = array();
			while (isset($event["id"])) {
				$events[] = $event;
				$event = $pst->fetch();
			}
			return $events;
		}

		public static function used_mail($email) {
			global $g_pdo;

			$request = "";
			if (isset($_SESSION['email'])) {
				$user = User::get_from_email();
				$request = <<<EOF
SELECT COUNT(*) FROM `user` WHERE `email`= :email AND `id`!= :id
EOF;
				$q = $g_pdo->prepare($request);
				$q->execute(array(":email" => $email, ":id" => $user->id));
			} else {
				$request = <<<EOF
SELECT COUNT(*) FROM `user` WHERE `email`= :email
EOF;
				$q = $g_pdo->prepare($request);
				$q->execute(array(":email" => $email));
			}
			$count = $q->fetch();
				return $count[0] > 0;
		}

		public function get_name() {
			return ucfirst(mb_strtolower($this->firstname, "UTF-8"))." ".
				mb_strtoupper($this->lastname, "UTF-8");
		}
	}
?>
