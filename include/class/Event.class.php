<?php
	class Event {
		public $title;
		public $deadline;
		public $date;
		public $funding_wanted;
		public $funding_authorized;
		public $location;
		public $link;
		public $description_short;
		public $description_long;
		public $nominative;
		public $status;
		public $rates = array();

		public function build_from_table($event) {
			$this->title = $event['title'];
			$this->deadline = $event['deadline'];
			$this->date = $event['date'];
			$this->funding_wanted = $event['funding_wanted'];
			$this->funding_authorized = $event['funding_authorized'];
			$this->location = $event['location'];
			$this->link = $event['link'];
			$this->description_short = $event['description_short'];
			$this->description_long = $event['description_long'];
			$this->nominative = $event['nominative'];
			$this->status = $event['status'];
		}

		public function build($title, $date, $deadline, $funding_wanted, $location,
			$link, $description_short, $description_long, $nominative,
			$funding_authorized = 0, $status = EVENT_STATUS_SUBMITTED) {

			$this->title = $title;
			$this->deadline = $deadline;
			$this->date = $date;
			$this->funding_wanted = $funding_wanted;
			$this->funding_authorized = $funding_authorized;
			$this->location = $location;
			$this->link = $link;
			$this->description_short = $description_short;
			$this->description_long = $description_long;
			$this->nominative = $nominative;
			$this->status = $status;
		}

		public function store() {
			event_create(create_id(), $this->title, $this->date, $this->deadline,
				$this->funding_wanted, $this->location, $this->link,
				$this->description_short, $this->description_long, $this->nominative)

			foreach ($this->items as $item) {
				$item->store($id);
			}
		}
	}
?>