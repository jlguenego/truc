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
			$id_user = get_id_from_account();
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

			foreach ($this->items as $item) {
				$item->store($id);
			}
		}
	}
?>