<?php
	class Item {
		public $id;
		public $class;
		public $quantity;
		public $tax_rate;
		public $description;
		public $total_ht;
		public $total_tax;
		public $total_ttc;
		public $bill_id;

		public function __construct($class = '/item') {
			$this->class = $class;
		}

		public function hydrate($record) {
			foreach ($record as $key => $value) {
				if (ESCAPE_QUOTE) {
					$value = stripcslashes($value);
					$value = str_replace("%5C%22", "", $value);
				}
				switch ($key) {
					case 'id_bill':
						$this->bill_id = $record["id_bill"];
						break;
					case 'id_ticket':
						$this->ticket_id = $record["id_ticket"];
						break;
					default:
						$this->$key = $value;
				}
			}
		}

		public function get_object_class() {
			$result = explode('/', $this->class);
			foreach ($result as $key => $value) {
				$result[$key] = ucfirst(strtolower($value));
			}
			return join('', $result);
		}

		public function store() {
			global $g_pdo;

			$this->id = create_id();
			$created_t = time();
			$mod_t = $created_t;

			$request = <<<EOF
INSERT INTO `item`
SET
	`id`= :id,
	`created_t`= :created_t,
	`mod_t`= :mod_t,
	`class`= :class,
	`tax_rate`= :tax_rate,
	`description`= :description,
	`quantity`= :quantity,
	`total_ht`= :total_ht,
	`total_tax`= :total_tax,
	`total_ttc`= :total_ttc,
	`id_bill`= :id_bill
EOF;
			$pst = $g_pdo->prepare($request);
			$array = array(
				":id" => $this->id,
				":created_t" => $created_t,
				":mod_t" => $mod_t,
				":class" => $this->class,
				":tax_rate" => $this->tax_rate,
				":description" => $this->description,
				":quantity" => $this->quantity,
				":total_ht" => $this->total_ht,
				":total_tax" => $this->total_tax,
				":total_ttc" => $this->total_ttc,
				":id_bill" => $this->bill_id,
			);
			$pst->execute($array);
		}

		public function delete() {
			global $g_pdo;

			$request = <<<EOF
DELETE FROM item
WHERE `id`= :id
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$pst->execute(array(":id" => $this->id));
		}

		public static function get_from_id($id) {
			global $g_pdo;

			$item = null;
			$request = <<<EOF
SELECT class FROM item
WHERE id = :id
EOF;
			$pst = $g_pdo->prepare($request);
			$pst->execute(array(":id" => $id));
			$record = $pst->fetch(PDO::FETCH_ASSOC);
			$class = $record['class'];
			debug('class='.$class);
			if ($class == '/item/ticket') {
				$item = new ItemTicket();
			} else {
				$item = new Item();
			}
			$item->id = $id;
			$item->load();
			return $item;
		}

		public function load() {
			global $g_pdo;
			$request = <<<EOF
SELECT * FROM `item`
WHERE `id`= :id
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$pst->execute(array(":id" => $this->id));
			$record = $pst->fetch(PDO::FETCH_ASSOC);
			if (!isset($record['id'])) {
				throw new Exception("Cannot load the item with id=" . $id);
			}
			$this->hydrate($record);
		}

		public function get_description() {
			return $this->description;
		}

		public function get_bill() {
			return Bill::get_from_id($this->bill_id);
		}
	}
?>