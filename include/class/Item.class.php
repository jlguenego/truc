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
		public $participant_firstname;
		public $participant_lastname;
		public $participant_title;
		public $bill_id;

		public function hydrate($record) {
			foreach ($record as $key => $value) {
				$this->$key = $value;
			}
			$this->bill_id = $record["id_bill"];
		}

		public function compute() {
			$this->total_ht = curr($this->event_rate_amount * $this->quantity);
			$this->total_tax = curr(($this->total_ht * ($this->event_rate_tax/100)));
			$this->total_ttc = $this->total_ht + $this->total_tax;
		}

		public function store($id_bill) {
			global $g_pdo;

			$this->id = create_id();
			$this->bill_id = $id_bill;

			$request = <<<EOF
INSERT INTO `item`
SET
	`id`={$this->id},
	`event_name`="{$this->event_name}",
	`event_rate_name`="{$this->event_rate_name}",
	`event_rate_amount`={$this->event_rate_amount},
	`event_rate_tax`={$this->event_rate_tax},
	`quantity`={$this->quantity},
	`total_ht`={$this->total_ht},
	`total_tax`={$this->total_tax},
	`total_ttc`={$this->total_ttc},
	`id_bill`={$this->bill_id},
	`participant_firstname`="{$this->participant_firstname}",
	`participant_lastname`="{$this->participant_lastname}",
	`participant_title`="{$this->participant_title}";
EOF;
			$st = $g_pdo->prepare($request);
			if ($st->execute() === FALSE) {
				echo($request.'<br/>');
				throw new Exception("Devis item insertion: ".sprint_r($g_pdo->errorInfo())." InnoDB?");
			};
		}

		public function to_string() {
			$result = "|event_name=".$this->event_name;
			$result .= "|event_rate_name=".$this->event_rate_name;
			$result .= "|event_rate_amount=".curr($this->event_rate_amount);
			$result .= "|event_rate_tax=".curr($this->event_rate_tax);
			$result .= "|quantity=".$this->quantity;
			$result .= "|total_ht=".curr($this->total_ht);
			$result .= "|total_tax=".curr($this->total_tax);
			$result .= "|total_ttc=".curr($this->total_ttc);
			$result .= "|participant_firstname=".$this->participant_firstname;
			$result .= "|participant_lastname=".$this->participant_lastname;
			$result .= "|participant_title=".$this->participant_title;

			return $result;
		}
	}
?>