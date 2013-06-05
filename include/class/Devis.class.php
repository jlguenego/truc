<?php
	class Devis {
		public $id;
		public $created_t;
		public $mod_t;
		public $flags = 0;
		public $items = array();
		public $total_ht;
		public $total_tax;
		public $total_ttc;
		public $label;
		public $username;
		public $address;
		public $vat;
		public $status = DEVIS_STATUS_PLANNED;
		public $type = DEVIS_TYPE_QUOTATION;
		public $payment_info;
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

			$this->user_id = User::get_id_from_account();
		}

		public function store() {
			global $g_pdo;

			if ($this->type == DEVIS_TYPE_INVOICE) {
				$this->label .= "#".seq_next('invoice');
			} else {
				$this->label .= "#".seq_next('quotation');
			}
			$this->id = create_id();
			$this->created_t = time();
			$this->mod_t = $this->created_t;

			$request = <<<EOF
INSERT INTO `bill`
SET
	`id`= :id,
	`created_t`= :created_t,
	`mod_t`= :mod_t,
	`flags`= :flags,
	`total_ht`= :total_ht,
	`total_tax`= :total_tax,
	`total_ttc`= :total_ttc,
	`label`= :label,
	`username`= :username,
	`address`= :address,
	`status`= :status,
	`type`= :type,
	`id_user`= :id_user,
	`id_event`= :id_event,
	`payment_info`= :payment_info,
	`vat`= :vat
EOF;
			$pst = $g_pdo->prepare($request);
			$array = array(
				":id" => $this->id,
				":created_t" => $this->created_t,
				":mod_t" => $this->mod_t,
				":flags" => $this->flags,
				":total_ht" => $this->total_ht,
				":total_tax" => $this->total_tax,
				":total_ttc" => $this->total_ttc,
				":label" => $this->label,
				":username" => $this->username,
				":address" => $this->address,
				":status" => $this->status,
				":type" => $this->type,
				":id_user" => $this->user_id,
				":id_event" => $this->event_id,
				":payment_info" => $this->payment_info,
				":vat" => $this->vat,
			);

			$pst->execute($array);
			$event = Event::get_from_id($this->event_id);
			$event->add_funding_acquired($this->total_ttc);

			foreach ($this->items as $item) {
				$item->bill_id = $this->id;
				$item->store();
			}
		}

		public function load($id) {
			global $g_pdo;

			$request = "SELECT * FROM `bill` WHERE `id`= :id";

			debug($request);
			$pst = $g_pdo->prepare($request);
			$pst->execute(array(
				":id" => $id,
			));
			$devis = $pst->fetch(PDO::FETCH_ASSOC);
			if (!isset($devis['id'])) {
				return NULL;
			}
			$this->hydrate($devis);
			$pst->closeCursor();

			$request = <<<EOF
SELECT * FROM `item`
WHERE `id_bill`= :id
ORDER BY event_rate_name
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$pst->execute(array(
				":id" => $id,
			));
			$records = $pst->fetchAll(PDO::FETCH_ASSOC);

			foreach ($records as $record) {
				$item = new Item();
				$item->hydrate($record);
				$this->items[] = $item;
			}
			debug(sprint_r($this));
		}

		public function update() {
			global $g_pdo;

			$this->mod_t = time();
			$request = <<<EOF
UPDATE `bill`
SET
	`mod_t`= :mod_t,
	`flags`= :flags,
	`payment_info`= :payment_info,
	`status`= :status
WHERE `id`= :id;
EOF;
			$pst = $g_pdo->prepare($request);
			$pst->execute(array(
				":mod_t" => $this->mod_t,
				":flags" => $this->flags,
				":payment_info" => $this->payment_info,
				":status" => $this->status,
				":id" => $this->id,
			));
		}

		public function delete() {
			global $g_pdo;

			foreach ($this->get_items() as $item) {
				$item->delete();
			}

			$request = <<<EOF
DELETE FROM bill
WHERE `id`= :id
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$pst->execute(array(":id" => $this->id));
		}

		public function set_status($status) {
			$this->status = $status;
			$this->update();
		}

		public function set_flags($flags) {
			$this->flags = $flags;
			$this->update();
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
WHERE `id_bill`= :id
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$pst->execute(array(":id" => $this->id));
			$items = array();
			while ($record = $pst->fetch()) {
				$item = new Item();
				$item->hydrate($record);
				$items[] = $item;
			}
			return $items;
		}

		public function can_be_retrieved() {
			$user = need_authentication();
			$event = Event::get_from_id($this->event_id);

			if (is_admin_logged() || $this->user_id == $user->id
				|| $event->user_id == $user->id) {
				debug("Can administrate.");
				return TRUE;
			}
			debug("Cannot administrate.");
			return FALSE;
		}

		public function is_for_company() {
			return !is_null_or_empty($this->vat);
		}

		public function get_event() {
			return Event::get_from_id($this->event_id);
		}

		public function is_really_paid() {
			return !is_null_or_empty($this->payment_info);
		}
	}
?>