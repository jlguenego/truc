<?php
	class Address {
		public $id;
		public $address;
		public $lat;
		public $lng;
		public $street_number;
		public $route;
		public $postal_code;
		public $locality;
		public $administrative_area_level_2;
		public $administrative_area_level_1;
		public $country;

		public function __construct() {
			$this->set_null();
		}

		public function hydrate($array) {
			foreach ($array as $key => $value) {
				$this->$key = $value;
			}
		}

		public function hydrate_from_form($prefix = "") {
			$this->address = $_GET[$prefix];
			debug("address=".$this->address);

			if ($prefix != "" && !preg_match('#_$#', $prefix)) {
				$prefix .= "_";
			}

			foreach ($_GET as $key => $value) {
				if (is_null_or_empty($value) || $value == 'false') {
					$_GET[$key] = null;
				}
			}
			$this->lat = $_GET[$prefix."lat"];
			$this->lng = $_GET[$prefix."lng"];
			$this->street_number = $_GET[$prefix."street_number"];
			$this->route = $_GET[$prefix."route"];
			$this->postal_code = $_GET[$prefix."postal_code"];
			$this->locality = $_GET[$prefix."locality"];
			$this->administrative_area_level_2 = $_GET[$prefix."administrative_area_level_2"];
			$this->administrative_area_level_1 = $_GET[$prefix."administrative_area_level_2"];
			$this->country = $_GET[$prefix."country"];
		}

		public static function get_from_id($id) {
			$address = new Address();
			$address->load($id);
			return $address;
		}

		public function store() {
			global $g_pdo;

			$this->id = create_id();
			$created_t = time();
			$mod_t = $created_t;
			$request = <<<EOF
INSERT INTO `address`
SET
	`id`= :id,
	`address`= :address,
	`created_t`= :created_t,
	`mod_t`= :mod_t,
	`lat`= :lat,
	`lng`= :lng,
	`street_number`= :street_number,
	`route`= :route,
	`postal_code`= :postal_code,
	`locality`= :locality,
	`administrative_area_level_2`= :administrative_area_level_2,
	`administrative_area_level_1`= :administrative_area_level_1,
	`country`= :country;
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$array = array(
				":id" => $this->id,
				":created_t" => $created_t,
				":mod_t" => $mod_t,
				":address" => $this->address,
				":lat" => $this->lat,
				":lng" => $this->lng,
				":street_number" => $this->street_number,
				":route" => $this->route,
				":postal_code" => $this->postal_code,
				":locality" => $this->locality,
				":administrative_area_level_2" => $this->administrative_area_level_2,
				":administrative_area_level_1" => $this->administrative_area_level_1,
				":country" => $this->country,
			);
			$pst->execute($array);
		}

		public function load($id) {
			global $g_pdo;

			$request = <<<EOF
SELECT * FROM `address`
WHERE `id`= :id
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$pst->execute(array(":id" => $id));
			$record = $pst->fetch(PDO::FETCH_ASSOC);
			if (!isset($record['id'])) {
				return NULL;
			}
			$this->hydrate($record);
		}

		public function update() {
			global $g_pdo;

			$mod_t = time();
			$request = <<<EOF
UPDATE `address`
SET
	`address`= :address,
	`mod_t`= :mod_t,
	`lat`= :lat,
	`lng`= :lng,
	`street_number`= :street_number,
	`route`= :route,
	`postal_code`= :postal_code,
	`locality`= :locality,
	`administrative_area_level_2`= :administrative_area_level_2,
	`administrative_area_level_1`= :administrative_area_level_1,
	`country`= :country
WHERE
	`id`= :id
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$array = array(
				":id" => $this->id,
				":mod_t" => $mod_t,
				":address" => $this->address,
				":lat" => $this->lat,
				":lng" => $this->lng,
				":street_number" => $this->street_number,
				":route" => $this->route,
				":postal_code" => $this->postal_code,
				":locality" => $this->locality,
				":administrative_area_level_2" => $this->administrative_area_level_2,
				":administrative_area_level_1" => $this->administrative_area_level_1,
				":country" => $this->country,
			);
			$pst->execute($array);
		}

		public function delete() {
			global $g_pdo;
			$request = <<<EOF
DELETE FROM `address`
WHERE `id`= :id
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$pst->execute(array(":id" => $this->id));
		}

		public function is_empty() {
			return is_null_or_empty($this->address)
				|| is_null_or_empty($this->lat)
				|| is_null_or_empty($this->lng)
				|| is_null_or_empty($this->street_number)
				|| is_null_or_empty($this->route)
				|| is_null_or_empty($this->postal_code)
				|| is_null_or_empty($this->locality)
				|| is_null_or_empty($this->administrative_area_level_2)
				|| is_null_or_empty($this->administrative_area_level_1)
				|| is_null_or_empty($this->country);
		}

		public function set_null() {
			$this->address = null;
			$this->lat = null;
			$this->lng = null;
			$this->street_number = null;
			$this->route = null;
			$this->postal_code = null;
			$this->locality = null;
			$this->administrative_area_level_2 = null;
			$this->administrative_area_level_1 = null;
			$this->country = null;
		}

		public function to_string($b_google_addrs = false) {
			$result = $this->address;
			if ($b_google_addrs) {
				$result = $this->google_address();
			}
			return	$result;
		}

		public function google_address() {
			$address = "";
			if ($this->street_number) {
				$address .= $this->street_number . "&nbsp;";
			}
			if ($this->route) {
				$address .= $this->route;
			}
			if ($address != "") {
				$address .= "<br/>";
			}

			if ($this->postal_code) {
				$address .= $this->postal_code . "&nbsp;";
			}
			if ($this->locality) {
				$address .= $this->locality;
			}
			if ($address != "") {
				$address .= "<br/>";
			}

			if ($this->administrative_area_level_2) {
				$address .= $this->administrative_area_level_2;
			}
			if ($this->administrative_area_level_1) {
				if ($address != "") {
					$address .= ",&nbsp;";
				}
				$address .= $this->administrative_area_level_1;
			}
			if ($address != "") {
				$address .= "<br/>";
			}

			if ($this->country) {
				$address .= $this->country;
			}
			return $address;
		}

		public function has_accountancy_activity() {
			global $g_pdo;
			$request = <<<EOF
SELECT COUNT(*) FROM bill WHERE id_biller_address = :id
EOF;
			$q = $g_pdo->prepare($request);
			$q->execute(array(':id' => $this->id));
			$count = $q->fetch();
			return $count[0] > 0;
		}
	}
?>