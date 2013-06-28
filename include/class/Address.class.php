<?php
	class Address {
		public $id;
		public $lat;
		public $lng;
		public $street_number;
		public $route;
		public $postal_code;
		public $locality;
		public $administrative_area_level_2;
		public $administrative_area_level_1;
		public $country;

		public function hydrate($array) {
			foreach ($array as $key => $value) {
				$this->$key = $value;
			}
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

		public function is_empty() {
			return is_null_or_empty($this->lat)
				|| is_null_or_empty($this->lng)
				|| is_null_or_empty($this->street_number)
				|| is_null_or_empty($this->route)
				|| is_null_or_empty($this->postal_code)
				|| is_null_or_empty($this->locality)
				|| is_null_or_empty($this->administrative_area_level_2)
				|| is_null_or_empty($this->administrative_area_level_1)
				|| is_null_or_empty($this->country);
		}

	}
?>