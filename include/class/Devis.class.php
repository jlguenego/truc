<?php
	class Devis {
		public $items = array();
		public $total_ht;
		public $total_tax;
		public $total_ttc;
		public $label;
		public $username;
		public $address;
		public $status = DEVIS_STATUS_AUTHORIZED;
		public $event_id;
		public $user_id;


		public function compute() {
			$this->total_ht = 0.00;
			$this->total_tax = 0.00;
			$this->total_ttc = 0.00;
			foreach ($this->items as $item) {
				$this->total_ht += $item->total_ht;
				$this->total_tax += $item->total_tax;
				$this->total_ttc += $item->total_ttc;
			}

			$this->label = COMPANY_NAME." - no ".seq_next('devis');
			debug($this->label);

			$this->user_id = get_id_from_account();
		}

		public function store() {
			global $g_pdo;

			$id = create_id();
			$created_t = time();
			$total_ht = $this->total_ht;
			$total_tax = $this->total_tax;
			$total_ttc = $this->total_ttc;
			$label = $this->label;
			$username = $this->username;
			$address = $this->address;
			$status = $this->status;
			$id_user = $this->user_id;
			$id_event = $this->event_id;

			$request = <<<EOF
INSERT INTO `devis`
SET
	`id`=${id},
	`created_t`=${created_t},
	`total_ht`="${total_ht}",
	`total_tax`="${total_tax}",
	`total_ttc`="${total_ttc}",
	`label`="${label}",
	`username`="${username}",
	`address`="${address}",
	`status`=${status},
	`id_user`=${id_user},
	`id_event`=${id_event};
EOF;
			$st = $g_pdo->prepare($request);
			if ($st->execute() === FALSE) {
				debug($request);
				throw new Exception("Devis insertion: ".sprint_r($g_pdo->errorInfo())." InnoDB?");
			};
			event_add_funding_aquired($id_event, $total_ttc);

			foreach ($this->items as $item) {
				$item->store($id);
			}
		}

		public function update() {
			global $g_pdo;

			$label = $this->label;
			$status = $this->status;

			$request = <<<EOF
UPDATE `devis`
SET `status`=${status}
WHERE `label`="${label}";
EOF;
			$st = $g_pdo->prepare($request);
			if ($st->execute() === FALSE) {
				debug($request);
				throw new Exception("Devis update: ".sprint_r($g_pdo->errorInfo())." InnoDB?");
			};
		}

		public function build($devis) { // Generate devis from MySQL array
			$this->total_ht = curr($devis["total_ht"]);
			$this->total_tax = curr($devis["total_tax"]);
			$this->total_ttc = curr($devis["total_ttc"]);
			$this->label = $devis["label"];
			$this->username = $devis["username"];
			$this->address = $devis["address"];
			$this->status = $devis["status"];
			$this->event_id = $devis["id_event"];
			$this->user_id = $devis["id_user"];

			foreach ($devis["items"] as $row) {
				$item = new Item();
				$item->build($row);
				debug("Item=".sprint_r($item));
				$this->items[] = $item;
			}
			debug("Items=".sprint_r($this->items));
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
	}
?>