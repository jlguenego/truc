<?php
	class Discount {
		public $id;
		public $class;
		public $code;
		public $expiration_t;
		public $amount;
		public $percentage;
		public $event_id;

		public function hydrate($array) {
			foreach ($array as $key => $value) {
				$this->$key = $value;
				if ($key == "id_event") {
					$this->event_id = $value;
				}
			}
		}

		public function hydrate_from_form($i) {
			$this->code = $_GET['discount_code_a'][$i];
			$this->class = $_GET['discount_class_a'][$i];
			$this->expiration_t = s2t($_GET['discount_date_a'][$i]);
			if ($this->class == DISCOUNT_CLASS_FIXED) {
				$this->amount = $_GET['discount_value_a'][$i];
			} else {
				$this->percentage = $_GET['discount_value_a'][$i];
			}
		}

		public static function get_from_id($id) {
			$discount = new Discount();
			$discount->load($id);
			return $discount;
		}


		public function store() {
			global $g_pdo;

			$this->id = create_id();
			$created_t = time();
			$mod_t = $created_t;

			$request = <<<EOF
INSERT INTO `discount`
SET
	`id`= :id,
	`created_t`= :created_t,
	`mod_t`= :mod_t,
	`code`= :code,
	`class`= :class,
	`expiration_t`= :expiration_t,
	`amount`= :amount,
	`percentage`= :percentage,
	`id_event`= :event_id
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$array = array(
				":id" => $this->id,
				":created_t" => $created_t,
				":mod_t" => $mod_t,
				":code" => $this->code,
				":class" => $this->class,
				":expiration_t" => $this->expiration_t,
				":amount" => $this->amount,
				":percentage" => $this->percentage,
				":event_id" => $this->event_id,
			);
			$pst->execute($array);
		}

		public function load($id) {
			global $g_pdo;

			$request = <<<EOF
SELECT * FROM `discount`
WHERE `id`= :id
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$pst->execute(array(":id" => $id));
			$record = $pst->fetch(PDO::FETCH_ASSOC);
			if (!isset($record['id'])) {
				throw new Exception(_t("Cannot load the discount with id=") . $id);
			}
			$this->hydrate($record);
		}

		public function update() {
			global $g_pdo;

			$mod_t = time();
			$request = <<<EOF
UPDATE `discount`
SET
	`mod_t`= :mod_t,
	`code`= :code,
	`class`= :class,
	`expiration_t`= :expiration_t,
	`amount`= :amount,
	`percentage`= :percentage,
	`id_event`= :event_id
WHERE `id`= :id
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$array = array(
				":id" => $this->id,
				":mod_t" => $mod_t,
				":code" => $this->code,
				":class" => $this->class,
				":expiration_t" => $this->expiration_t,
				":amount" => $this->amount,
				":percentage" => $this->percentage,
				":event_id" => $this->event_id,
			);
			$pst->execute($array);
		}

		public function delete() {
			global $g_pdo;

			$request = <<<EOF
DELETE FROM `discount`
WHERE `id`= :id
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$pst->execute(array(
				":id" => $this->id,
			));
		}
	}
?>