<?php
	class Item {
		public $event_name;
		public $event_rate_name;
		public $event_rate_amount;
		public $event_rate_tax;
		public $quantity;
		public $total_ht;
		public $total_tax;
		public $total_ttc;

		public function compute() {
			$this->total_ht = number_format($this->event_rate_amount * $this->quantity, 2);
			$this->total_tax = number_format(($this->total_ht * ($this->event_rate_tax/100)), 2);
			$this->total_ttc = $this->total_ht + $this->total_tax;
		}

		public function store($id_devis) {
			global $g_pdo;

			$id = create_id();
			$event_name = $this->event_name;
			$event_rate_name = $this->event_rate_name;
			$event_rate_amount = $this->event_rate_amount;
			$event_rate_tax = $this->event_rate_tax;
			$quantity = $this->quantity;
			$total_ht = $this->total_ht;
			$total_tax = $this->total_tax;
			$total_ttc = $this->total_ttc;

			$request = <<<EOF
INSERT INTO `devis_item`
SET
	`id`=${id},
	`event_name`="${event_name}",
	`event_rate_name`="${event_rate_name}",
	`event_rate_amount`=${event_rate_amount},
	`event_rate_tax`=${event_rate_tax},
	`quantity`=${quantity},
	`total_ht`=${total_ht},
	`total_tax`=${total_tax},
	`total_ttc`=${total_ttc},
	`id_devis`=${id_devis};
EOF;
			$st = $g_pdo->prepare($request);
			if ($st->execute() === FALSE) {
				echo($request.'<br/>');
				throw new Exception("Devis item insertion: ".sprint_r($g_pdo->errorInfo())." InnoDB?");
			};
		}
	}
?>