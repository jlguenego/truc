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

		public function compute() {
			$this->total_ht = curr($this->event_rate_amount * $this->quantity);
			$this->total_tax = curr(($this->total_ht * ($this->event_rate_tax/100)));
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
			$participant_firstname = $this->participant_firstname;
			$participant_lastname = $this->participant_lastname;
			$participant_title = $this->participant_title;

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
	`id_devis`=${id_devis},
	`participant_firstname`="${participant_firstname}",
	`participant_lastname`="${participant_lastname}",
	`participant_title`="${participant_title}";
EOF;
			$st = $g_pdo->prepare($request);
			if ($st->execute() === FALSE) {
				echo($request.'<br/>');
				throw new Exception("Devis item insertion: ".sprint_r($g_pdo->errorInfo())." InnoDB?");
			};
		}

		public function build($item) {
			$this->event_name = $item["event_name"];
			$this->event_rate_name = $item["event_rate_name"];
			$this->event_rate_amount = curr($item["event_rate_amount"]);
			$this->event_rate_tax = curr($item["event_rate_tax"]);
			$this->quantity = $item["quantity"];
			$this->total_ht = curr($item["total_ht"]);
			$this->total_tax = curr($item["total_tax"]);
			$this->total_ttc = curr($item["total_ttc"]);
			$this->participant_firstname = $item["participant_firstname"];
			$this->participant_lastname = $item["participant_lastname"];
			$this->participant_title = $item["participant_title"];
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