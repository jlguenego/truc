<?php
	class Bill {
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
		public $vat;
		public $status = BILL_STATUS_PLANNED;
		public $type = BILL_TYPE_QUOTATION;
		public $target = BILL_TARGET_ATTENDEE;
		public $payment_info;
		public $event_id;
		public $user_id;
		public $address_id;

		public static function get_from_id($id) {
			$bill = new Bill();
			$bill->load($id);
			return $bill;
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
			$this->address_id = $record["id_address"];
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

			if ($this->type == BILL_TYPE_INVOICE) {
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
	`status`= :status,
	`type`= :type,
	`id_user`= :id_user,
	`id_event`= :id_event,
	`payment_info`= :payment_info,
	`vat`= :vat,
	`target`= :target,
	`id_address`= :address_id
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
				":status" => $this->status,
				":type" => $this->type,
				":id_user" => $this->user_id,
				":id_event" => $this->event_id,
				":payment_info" => $this->payment_info,
				":vat" => $this->vat,
				":target" => $this->target,
				":address_id" => $this->address_id,
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
			$bill = $pst->fetch(PDO::FETCH_ASSOC);
			if (!isset($bill['id'])) {
				return NULL;
			}
			$this->hydrate($bill);
			$pst->closeCursor();

			$this->items = $this->get_items();
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
			return HOST . "/index.php?action=retrieve&type=bill&id=" . $this->id;
		}

		public function create_invoice() {
			$invoice = clone $this;
			$invoice->type = BILL_TYPE_INVOICE;
			$invoice->store();
			return $invoice;
		}

		public function get_items() {
			global $g_pdo;

			$request = <<<EOF
SELECT id FROM `item`
WHERE `id_bill`= :id
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$pst->execute(array(":id" => $this->id));
			$items = array();
			while ($record = $pst->fetch()) {
				$id = $record['id'];
				$item = Item::get_from_id($id);
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

		public function is_for($target) {
			return $this->target == $target;
		}

		public static function invoice_from_report($report, $event) {
			$bill = new Bill();
			$bill->user_id = $_SESSION['user_id'];
			$bill->target = BILL_TARGET_ORGANIZER;
			$bill->username = $event->organizer_name;
			$bill->event_id = $event->id;
			$bill->address_id = $event->billing_address_id;
			$bill->vat = $event->vat;
			$bill->type = BILL_TYPE_INVOICE;

			$label = "Event-Biller-I-";
			$bill->label = $label."EVT-".sprintf("%06d", $event->id);

			$tickets = $event->get_tickets();
			$item = new Item();
			$item->quantity = 1;
			$sold_ticket_nbr = $report["ticket_quantity"];
			$rate= deal_get_description($event->deal_name);
			$amount_ticket_sales = $report['total'];
			$item->description = <<<EOF
<b>{{Ticket sales fees}}</b><br/>
{{Amount ticket sales:}}&nbsp;${amount_ticket_sales}€&nbsp;&nbsp;&nbsp;{{Sold ticket number:}}&nbsp;${sold_ticket_nbr}<br/>
{{Rate details:}}&nbsp;${rate}
EOF;
			$item->tax_rate = 19.6;
			$item->total_ttc = curr($report['eb_fee']);
			$item->total_ht = curr($item->total_ttc / (1 + ($item->tax_rate / 100)));
			$item->total_tax = $item->total_ttc - $item->total_ht;

			$bill->items[] = $item;
			$bill->compute();

			return $bill;
		}
	}
?>