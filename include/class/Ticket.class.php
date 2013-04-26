<?php
	class Ticket {
		public $id;
		public $name;
		public $type;
		public $amount;
		public $max_quantity;
		public $tax_rate;
		public $start_t;
		public $end_t;
		public $description;
		public $event_id;

		public function hydrate($array) {
			foreach ($array as $key => $value) {
				if (ESCAPE_QUOTE) {
					$value = stripcslashes($value);
					$value = str_replace("%5C%22", "", $value);
				}
				if ($key == "id_event") {
					$this->event_id = $value;
				}
				$this->$key = $value;
			}
		}

		public function store() {
			global $g_pdo;

			$this->id = create_id();
			$created_t = time();
			$mod_t = $created_t;

			$request = <<<EOF
INSERT INTO `ticket`
SET
	`id`= :id,
	`created_t`= :created_t,
	`mod_t`= :mod_t,
	`name`= :name,
	`type`= :type,
	`amount`= :amount,
	`max_quantity`= :max_quantity,
	`tax_rate`= :tax_rate,
	`start_t`= :start_t,
	`end_t`= :end_t,
	`description`= :description,
	`event_id`= :event_id
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$array = array(
				":id" => $this->id,
				":created_t" => $created_t,
				":mod_t" => $mod_t,
				":name" => $this->name,
				":amount" => $this->amount,
				":max_quantity" => $this->max_quantity,
				":tax_rate" => $this->tax_rate,
				":start_t" => $this->start_t,
				":end_t" => $this->end_t,
				":description" => $this->description,
				":event_id" => $this->event_id,
			);
			$pst->execute($array);
		}

		public function load($id) {
			global $g_pdo;

			$request = <<<EOF
SELECT * FROM `ticket`
WHERE `id`= :id
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$pst->execute(array(":id" => $id));
			$record = $pst->fetch(PDO::FETCH_ASSOC);
			if (!isset($record['id'])) {
				throw new Exception(_t("Cannot load the ticket with id=") . $id);
			}
			$this->hydrate($record);
		}

		public function update() {
			global $g_pdo;

			$mod_t = time();
			$request = <<<EOF
UPDATE `ticket`
SET
	`mod_t`= :mod_t,
	`name`= :name,
	`type`= :type,
	`amount`= :amount,
	`max_quantity`= :max_quantity,
	`tax_rate`= :tax_rate,
	`start_t`= :start_t,
	`end_t`= :end_t,
	`description`= :description
WHERE `id`= :id
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$array = array(
				":id" => $this->id,
				":mod_t" => $mod_t,
				":name" => $this->name,
				":type" => $this->type,
				":amount" => $this->amount,
				":max_quantity" => $this->max_quantity,
				":tax_rate" => $this->tax_rate,
				":start_t" => $this->start_t,
				":end_t" => $this->end_t,
				":description" => $this->description,
			);
			$pst->execute($array);
		}

		public function delete() {
			global $g_pdo;

			$request = <<<EOF
DELETE FROM `rate`
WHERE `id`= :id
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$pst->execute(array(
				":id" => $id,
			));
		}
	}
?>