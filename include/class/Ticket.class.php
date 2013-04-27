<?php
	class Ticket {
		public $id;
		public $name;
		public $type = 0;
		public $amount;
		public $max_quantity;
		public $tax_rate;
		public $start_t;
		public $end_t;
		public $description = "";
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

		public function hydrate_from_form($i) {
			$this->name = $_GET['ticket_name_a'][$i];
			$this->amount = $_GET['ticket_amount_a'][$i];
			$this->tax_rate = $_GET['ticket_tax_a'][$i];
			debug("hydrate_from_form=".sprint_r($this));
		}

		public static function get_from_id($id) {
			$ticket = new Ticket();
			$ticket->load($id);
			return $ticket;
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
	`id_event`= :event_id
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$array = array(
				":id" => $this->id,
				":created_t" => $created_t,
				":mod_t" => $mod_t,
				":name" => $this->name,
				":type" => $this->type,
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
DELETE FROM `ticket`
WHERE `id`= :id
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$pst->execute(array(
				":id" => $id,
			));
		}

		public function has_accountancy_activity() {
			global $g_pdo;

			$event = Event::get_from_id($this->event_id);
			$bills = $event->get_devis();

			foreach ($bills as $bill) {
				foreach ($bill->items as $item) {
					if (($item->event_rate_name == $this->name)
						&& ($item->event_rate_amount == $this->amount)
						&& ($item->event_rate_tax == $this->tax_rate)
					) {
						return true;
					}
				}
				return false;
			}
			return $count[0] > 0;
		}
	}
?>