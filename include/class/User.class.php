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
		public $phone;
		public $locale = "en";
		public $address_id;

		public static function get_from_id($id = null) {
			$user = new User();
			if ($id == null) {
				$id = $_SESSION["user_id"];
			}
			$user->load($id);
			return $user;
		}

		public static function get_from_session() {
			$user = new User();
			$user->load($_SESSION["user_id"]);
			return $user;
		}

		public function check_owner() {
			debug("user_id=".$this->id." | session_user_id=".$_SESSION["user_id"]);
			return ($this->id == $_SESSION["user_id"]) || is_admin_logged();
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

			$address = Address::get_from_id($this->address_id);
			$address->delete();
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
				if (ESCAPE_QUOTE) {
					//$value = str_replace("\\'", "'", $value);
					//$value = str_replace('\\"', '"', $value);
					$value = stripcslashes($value);
					$value = str_replace("%5C%22", "", $value);
				}
				$this->$key = $value;
			}
			$this->address_id = $array['id_address'];
		}

		public function set_token($token) {
			global $g_pdo;
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
		}

		public function set_password($passwd) {
			$this->password = $this->make_password($passwd);
		}

		public function make_password($passwd) {
			return sha1("a little bit of salt...".$passwd);
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
	`locale`= :locale,
	`id_address`= :address_id;
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
				":locale" => $this->locale,
				":address_id" => $this->address_id,
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
	`email`= :email,
	`firstname`= :firstname,
	`lastname`= :lastname,
	`phone`= :phone,
	`locale`= :locale,
	`id_address`= :address_id
WHERE `id`= :id
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$array = array(
				":mod_t" => $mod_t,
				":email" => $this->email,
				":firstname" => $this->firstname,
				":lastname" => $this->lastname,
				":id" => $this->id,
				":phone" => $this->phone,
				":locale" => $this->locale,
				":address_id" => $this->address_id,
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

		public static function get_from_email($email) {
			global $g_pdo;
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
				throw new Exception(_t("No user found with this token."));
			}
			$user = new User();
			$user->hydrate($record);

			$user->set_token(NULL);

			$exp_ts = explode("_", $token);
			if ($exp_ts[0] < time()) {
				throw new Exception(_t("This token is expired."));
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
			$this->hydrate($record);
		}

		public function hydrate_from_form() {
			$this->hydrate($_GET);
			$this->password = $_GET["password"];
			$this->email = $_GET["email"];
			$this->lastname = mb_strtoupper($_GET["lastname"], "UTF-8");
			$this->firstname = format_firstname($_GET["firstname"]);
			$this->clean_format();
		}

		public function clean_format() {
			$this->lastname = format_lastname($this->lastname);
			$this->firstname = format_firstname($this->firstname);
		}

		public function address($b_google_addrs = false) {
			$address = Address::get_from_id($this->address_id);
			return $address->to_string($b_google_addrs);
		}

		public function has_no_address() {
			$address = Address::get_from_id($this->address_id);
			return $address->is_empty();
		}

		public function fill_address_from_participation() {
			$address = Address::get_from_id($this->address_id);
			$address->hydrate_from_form('billing_address');
			debug('address='.sprint_r($address));
			$address->update();
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
			if ($this->has_bill()) {
				return true;
			}
			return false;
		}

		public function has_bill() {
			global $g_pdo;

			$request = <<<EOF
SELECT COUNT(*) FROM `bill`
WHERE `id_user`= :id
EOF;
			$pst = $g_pdo->prepare($request);
			$pst->execute(array(":id" => $this->id));
			$record = $pst->fetch();
			return $record[0] > 0;
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
		public static function authenticate($email, $client_pwd, $cnonce) {
			$user = User::get_from_email($email);
			if ($user == null) {
				return false;
			}
			$nonce = $_SESSION['nonce'];
			$server_pwd = sha1($user->password.$nonce.$cnonce);

			return $server_pwd == $client_pwd;
		}

		public static function get_id_from_account() {
			return $_SESSION["user_id"];
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
				throw new Exception(_t("This account does not exist or is already activated"));
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

		public function force_activation() {
			global $g_pdo;

			$mod_t = time();
			$status = ACTIVATION_STATUS_ACTIVATED;
			$request = <<<EOF
UPDATE `user`
SET
	`activation_status`= :status,
	`mod_t`= :mod_t
WHERE `id`= :id
EOF;
			$pst = $g_pdo->prepare($request);
			$pst->execute(array(
				":status" => $status,
				":mod_t" => $mod_t,
				":id" => $this->id,
			));
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
			if (isset($_SESSION['user_id'])) {
				$user = User::get_from_id();
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
			return format_firstname($this->firstname)." ".format_lastname($this->lastname);
		}

		public function sync_phone($phone) {
			if (is_null_or_empty($this->phone)) {
				$this->phone = $phone;
				$this->update();
			}
		}

		public function get_i18n() {
			$result = new I18n();
			$result->init($this->locale);
			return $result;
		}

		public static function create_from_partner($email, $lastname, $firstname, $locale) {
			$user = new User();
			$user->id = create_id();
			$user->email = $email;
			$user->firstname = $firstname;
			$user->lastname = $lastname;
			$user->password = $_SESSION['partner'];
			$user->locale = $locale;
			$user->role = ROLE_USER;
			$user->activation_status = ACTIVATION_STATUS_ACTIVATED;
			$user->clean_format();

			$address = new Address();
			$address->store();
			$user->address_id = $address->id;
			$user->store();
			message_set_info(_t('An account has been created based on the information given by '.
					format_partner($_SESSION['partner'])));
			unset($_SESSION['partner']);
			return $user;
		}
	}
?>
