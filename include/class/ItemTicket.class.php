<?php
	class ItemTicket extends Item {
		public $event_name;
		public $event_rate_name;
		public $event_rate_amount;
		public $event_rate_tax;
		public $attendee_firstname;
		public $attendee_lastname;
		public $attendee_title;
		public $ticket_id;

		public function __construct() {
			parent::__construct();
			$this->class = '/item/ticket';
		}

		public function load() {
			parent::load();
			global $g_pdo;

			$request = <<<EOF
SELECT * FROM `item_ticket`
WHERE `obj_id`= :id
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$pst->execute(array(":id" => $this->id));
			$record = $pst->fetch(PDO::FETCH_ASSOC);
			if (!isset($record['obj_id'])) {
				throw new Exception(_t("Cannot load the item_ticket with id=") . $id);
			}
			$this->hydrate($record);
		}

		public function store() {
			global $g_pdo;

			parent::store();

			$request = <<<EOF
INSERT INTO `item_ticket`
SET
	`obj_id`= :obj_id,
	`event_name`= :event_name,
	`event_rate_name`= :event_rate_name,
	`event_rate_amount`= :event_rate_amount,
	`event_rate_tax`= :event_rate_tax,
	`attendee_firstname`= :attendee_firstname,
	`attendee_lastname`= :attendee_lastname,
	`attendee_title`= :attendee_title,
	`id_ticket`= :id_ticket
EOF;
			$pst = $g_pdo->prepare($request);
			$array = array(
				":obj_id" => $this->id,
				":event_name" => $this->event_name,
				":event_rate_name" => $this->event_rate_name,
				":event_rate_amount" => $this->event_rate_amount,
				":event_rate_tax" => $this->event_rate_tax,
				":attendee_firstname" => $this->attendee_firstname,
				":attendee_lastname" => $this->attendee_lastname,
				":attendee_title" => $this->attendee_title,
				":id_ticket" => $this->ticket_id,
			);
			$pst->execute($array);
		}

		public function delete() {
			global $g_pdo;

			$request = <<<EOF
DELETE FROM item_ticket
WHERE `obj_id`= :id
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$pst->execute(array(":id" => $this->obj_id));
		}

		public function get_description() {
			$event_id = $this->get_bill()->event_id;
			$event = Event::get_from_id($event_id);
			$result = <<<EOF
(${event_id})
<a href="?action=retrieve&amp;type=event&amp;id=${event_id}">{$this->event_name}</a><br/>
{{Ticket}}: {$this->event_rate_name}&nbsp;&nbsp;&nbsp;
EOF;
			if ($event->type == EVENT_TYPE_NOMINATIVE) {
				$result .= <<<EOF
{{Attendee:}} {$this->attendee_title}&nbsp;{$this->attendee_firstname}&nbsp;{$this->attendee_lastname}
EOF;
			} else {
				$result .= <<<EOF
{{Unit Price}}: {$this->event_rate_amount}<br/>
EOF;
			}

			return $result;
		}
	}
?>