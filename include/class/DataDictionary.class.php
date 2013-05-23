<?php
	class DataDictionary {
		public $entity_array = array();

		public function add_entity($name) {
			$e = new Entity($name);
			$this->entity_array[$name] = $e;
			return $e;
		}

		public function get_entity($name) {
			return $this->entity_array[$name];
		}

		public function has_entity($name) {
			return isset($this->entity_array[$name]);
		}
	}

	class Entity {
		public $name;
		public $classname;
		public $fields = array();
		public $actions = array();
		public $global_actions = array();
		public $grouped_actions = array();

		public function __construct($name, $classname = null) {
			$this->name = $name;
			if ($classname == null) {
				$this->classname = ucfirst(strtolower($name));
			} else {
				$this->classname = $classname;
			}
		}

		public function add_field($name, $label, $type) {
			$f = new Field($name, $label, $type);
			$this->fields[] = $f;
			return $f;
		}

		public function add_action($action, $label) {
			$this->actions[$action] = new Action($action, $label);
		}

		public function add_global_action($action, $label) {
			$this->global_actions[$action] = new Action($action, $label);
		}

		public function add_grouped_action($action, $label) {
			$this->grouped_actions[$action] = new Action($action, $label);
		}

		public function get_fields() {
			return $this->fields;
		}

		public function get_actions() {
			return $this->actions;
		}

		public function get_global_actions() {
			return $this->global_actions;
		}

		public function get_grouped_actions() {
			return $this->grouped_actions;
		}

		public function is_valid_action() {
			$action = $_GET["action"];
			debug("action=".$action);
			debug("actions=".sprint_r($this->actions));
			$result = array_key_exists($action, $this->actions) ||
				array_key_exists($action, $this->global_actions) ||
				array_key_exists($action, $this->grouped_actions);
			return $result;
		}

		public function is_global_action() {
			$action = $_GET["action"];
			$result = array_key_exists($action, $this->global_actions);
			return $result;
		}

		public function is_grouped_action() {
			$action = $_GET["action"];
			$result = array_key_exists($action, $this->grouped_actions)
				&& isset($_GET["grouped"]);
			return $result;
		}

		public function execute_action($action) {
			$record = null;
			if (isset($_GET["id"])) {
				$record = Record::get_from_id($this->name, $_GET["id"]);
			} else {
				$record = new $this->classname();
			}
			$record->$action();
		}

		public function execute_global_action($action) {
			$classname = $this->classname;
			$classname::$action();
		}

		public function execute_grouped_action($action) {
			$classname = $this->classname;
			$classname::multi_action($action);
		}
	}

	class Action {
		public $name;
		public $label;

		public function __construct($name, $label) {
			$this->name = $name;
			$this->label = $label;
		}
	}
?>