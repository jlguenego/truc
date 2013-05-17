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

		public function __construct($name, $classname = null) {
			$this->name = $name;
			if ($classname == null) {
				$this->classname = ucfirst(strtolower($name));
			} else {
				$this->classname = $classname;
			}
		}

		public function add_field($name, $type) {
			$f = new Field($name, $type);
			$this->fields[] = $f;
			return $f;
		}

		public function add_action($action, $label) {
			$this->actions[$action] = new Action($action, $label);
		}

		public function get_fields() {
			return $this->fields;
		}

		public function get_actions() {
			return $this->actions;
		}

		public function is_valid_action($action) {
			debug("action=".$action);
			debug("actions=".sprint_r($this->actions));
			$result = array_key_exists($action, $this->actions);
			if ($result == false) {
				debug("is_valid_action=false");
			}
			debug("is_valid_action=".$result);
			return $result;
		}

		public function execute_action($action) {
			$record = new $this->classname();
			$record->$action();
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