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
		public $attendee_firstname;
		public $attendee_lastname;
		public $attendee_title;
		public $bill_id;

		public function hydrate($record) {
			foreach ($record as $key => $value) {
				if (ESCAPE_QUOTE) {
					$value = str_replace("\\'", "'", $value);
				}
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
	`id`= :id,
	`event_name`= :event_name,
	`event_rate_name`= :event_rate_name,
	`event_rate_amount`= :event_rate_amount,
	`event_rate_tax`= :event_rate_tax,
	`quantity`= :quantity,
	`total_ht`= :total_ht,
	`total_tax`= :total_tax,
	`total_ttc`= :total_ttc,
	`id_bill`= :id_bill,
	`attendee_firstname`= :attendee_firstname,
	`attendee_lastname`= :attendee_lastname,
	`attendee_title`= :attendee_title;
EOF;
			$pst = $g_pdo->prepare($request);
			$array = array(
				":id" => $this->id,
				":event_name" => $this->event_name,
				":event_rate_name" => $this->event_rate_name,
				":event_rate_amount" => $this->event_rate_amount,
				":event_rate_tax" => $this->event_rate_tax,
				":quantity" => $this->quantity,
				":total_ht" => $this->total_ht,
				":total_tax" => $this->total_tax,
				":total_ttc" => $this->total_ttc,
				":id_bill" => $this->bill_id,
				":attendee_firstname" => $this->attendee_firstname,
				":attendee_lastname" => $this->attendee_lastname,
				":attendee_title" => $this->attendee_title,
			);
			$pst->execute($array);
		}
	}
?>