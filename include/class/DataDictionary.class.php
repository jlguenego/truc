<?php
	class DataDictionary {
		public $entity_array = array();

		public function add_entity($name) {
			$e = new Entity();
			$e->name = $name;
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
		public $fields = array();

		public function add_field($name, $type) {
			$f = new Field($name, $type);
			$this->fields[] = $f;
			return $f;
		}

		public function get_fields() {
			return $this->fields;
		}
	}
?>