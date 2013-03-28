<?php
	class Devis {
		public $id;
		public $items = array();
		public $total_ht;
		public $total_tax;
		public $total_ttc;
		public $label;
		public $username;
		public $address;
		public $status = DEVIS_STATUS_PLANNED;
		public $type = DEVIS_TYPE_QUOTATION;
		public $event_id;
		public $user_id;

		public static function get_from_id($id) {
			$devis = new Devis();
			$devis->load($id);
			return $devis;
		}

		public static function exists($id) {
			global $g_pdo;

			$request = <<<EOF
SELECT COUNT(*) FROM `bill` WHERE `id`= :id
EOF;
			debug($request);
			$q = $g_pdo->prepare($request);
			$q->execute(array(":id" => $id));
			$count = $q->fetch();
			return $count[0] > 0;
		}

		public function hydrate($record) {
			foreach ($record as $key => $value) {
				$this->$key = $value;
			}
			$this->event_id = $record["id_event"];
			$this->user_id = $record["id_user"];
		}

		public function compute() {
			$this->total_ht = 0.00;
			$this->total_tax = 0.00;
			$this->total_ttc = 0.00;
			foreach ($this->items as $item) {
				$this->total_ht += $item->total_ht;
				$this->total_tax += $item->total_tax;
				$this->total_ttc += $item->total_ttc;
			}

			$this->user_id = get_id_from_account();
		}

		public function store() {
			global $g_pdo;

			if ($this->type == DEVIS_TYPE_INVOICE) {
				$this->label = "Invoice - no ".seq_next('invoice');
			} else {
				$this->label = "Quotation - no ".seq_next('quotation');
			}
			$this->id = create_id();
			$created_t = time();

			$request = <<<EOF
INSERT INTO `bill`
SET
	`id`={$this->id},
	`created_t`=${created_t},
	`total_ht`="{$this->total_ht}",
	`total_tax`="{$this->total_tax}",
	`total_ttc`="{$this->total_ttc}",
	`label`="{$this->label}",
	`username`="{$this->username}",
	`address`="{$this->address}",
	`status`={$this->status},
	`type`={$this->type},
	`id_user`={$this->user_id},
	`id_event`={$this->event_id};
EOF;
			$st = $g_pdo->prepare($request);
			if ($st->execute() === FALSE) {
				debug($request);
				throw new Exception("Devis insertion: ".sprint_r($g_pdo->errorInfo())." InnoDB?");
			};
			$event = Event::get_from_id($this->event_id);
			$event->add_funding_acquired($this->total_ttc);

			foreach ($this->items as $item) {
				$item->store($this->id);
			}
		}

		public function load($id) {
			global $g_pdo;

			$request = "SELECT * FROM `bill` WHERE `id`= ${id}";

			debug($request);
			$q = $g_pdo->prepare($request);
			$q->execute();
			$devis = $q->fetch(PDO::FETCH_ASSOC);
			if (!isset($devis['id'])) {
				return NULL;
			}
			$this->hydrate($devis);
			$q->closeCursor();

			$request = <<<EOF
SELECT * FROM `item`
WHERE `id_bill`=${id}
ORDER BY event_rate_name
EOF;
			debug($request);
			$q = $g_pdo->prepare($request);
			$q->execute();
			$records = $q->fetchAll(PDO::FETCH_ASSOC);

			foreach ($records as $record) {
				$item = new Item();
				$item->hydrate($record);
				$this->items[] = $item;
			}
			debug(sprint_r($this));
		}

		public function update() {
			global $g_pdo;

			$label = $this->label;
			$status = $this->status;

			$request = <<<EOF
UPDATE `bill`
SET `status`=${status}
WHERE `label`="${label}";
EOF;
			$st = $g_pdo->prepare($request);
			if ($st->execute() === FALSE) {
				debug($request);
				throw new Exception("Devis update: ".sprint_r($g_pdo->errorInfo())." InnoDB?");
			};
		}

		public function set_status($status) {
			$this->status = $status;
			$this->update();
		}

		public function to_string() {
			$result = "total_ht=".curr($this->total_ht);
			$result .= "&total_tax=".curr($this->total_tax);
			$result .= "&total_ttc=".curr($this->total_ttc);
			$result .= "&label=".str_replace(" ", "%20", $this->label);
			$result .= "&username=".str_replace(" ", "%20", $this->username);
			$result .= "&address=".str_replace(" ", "%20", $this->address);
			$result .= "&status=".$this->status;
			$result .= "&event_id=".$this->event_id;
			$result .= "&user_id=".$this->user_id;

			$i = 0;
			foreach ($this->items as $item) {
				$result .= "&item_".$i."=".str_replace(" ", "%20", $item->to_string());
				$i++;
			}

			return $result;
		}

		public function url() {
			return HOST . "/index.php?action=retrieve&type=devis&id=" . $this->id;
		}

		public function create_invoice() {
			$invoice = clone $this;
			$invoice->type = DEVIS_TYPE_INVOICE;
			$invoice->store();
			return $invoice;
		}

		public function get_items() {
			global $g_pdo;

			$request = <<<EOF
SELECT * FROM `item`
WHERE `id_bill`={$this->id}
EOF;
			debug($request);
			$q = $g_pdo->prepare($request);
			if ($q->execute() === FALSE) {
				debug($request);
				throw new Exception("Get devis_items: ".sprint_r($g_pdo->errorInfo())." InnoDB?");
			};
			$items = array();
			while ($record = $q->fetch()) {
				$item = new Item();
				$item->hydrate($record);
				$items[] = $item;
			}
			return $items;
		}
	}
?>