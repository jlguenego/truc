<?php
	class Record {
		public $type;

		public $id;
		public $created_t;
		public $mod_t;
		public $fields = array();

		public function __construct($type) {
			$this->type = $type;
		}

		public static function get_from_id($type, $id) {
			global $g_pdo;

			$request = <<<EOF
SELECT * FROM $type
WHERE `id`= :id
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$pst->execute(array(":id" => $id));
			$r = $pst->fetch();
			if (!isset($r['id'])) {
				return NULL;
			}
			$record = new Record($type);

			$record->id = $id;
			$record->created_t = time();
			$record->mod_t = $record->created_t;
			foreach (dd()->get_entity($record->type)->get_fields() as $f) {
				$field = clone $f;
				debug("value=".$r[$field->colname]);
				$field->value = $r[$field->colname];
				$record->fields[] = $field;
			}

			return $record;
		}

		public function delete() {
			global $g_pdo;
			$request = <<<EOF
DELETE FROM {$this->type}
WHERE `id`= :id
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$pst->execute(array(":id" => $this->id));
		}

		public static function get_table($type) {
			$result = <<<EOF
<table class="evt_table">
	<tr>
EOF;
			$columns = Record::get_fields($type);
			foreach ($columns as $field) {
				$result .= <<<EOF
		<th>{$field->name}</th>
EOF;
			}
			$result .= <<<EOF
	</tr>
EOF;
			foreach (Record::select_all($type) as $record) {
				$result .= <<< EOF
	<tr>
EOF;
				foreach ($columns as $field) {
					$colname = Field::get_colname($field->name);
					$value = $record[$colname];
					$html = Record::format_value($value, $field, $record["id"]);
					$result .= <<<EOF
		<td>$html</td>
EOF;
				}
				$id = $record["id"];
				$result .= <<<EOF
		<td><a href="?action=delete&amp;type=$type&amp;id=$id">Delete</a></td>
EOF;
				$classname = dd()->get_entity($type)->classname;
				foreach (dd()->get_entity($type)->get_actions() as $action) {
					$result .= <<<EOF
		<td><a href="?action={$action->name}&amp;type=$type&amp;id=$id">{$action->label}</a></td>
EOF;
				}
				$result .= <<<EOF
	</tr>
EOF;
			}
			$result .= <<<EOF
</table>
EOF;
			return $result;
		}

		public static function get_fields($type) {
			debug("dd=".sprint_r(dd()));
			$fields = array();
			$fields[] = new Field("id", "primary_key");
			$fields[] = new Field("created_t", "timestamp");
			$fields[] = new Field("mod_t", "timestamp");
			return array_merge($fields, dd()->get_entity($type)->get_fields());
		}

		public static function select_all($type) {
			global $g_pdo;

			$request = <<<EOF
SELECT * FROM $type
ORDER BY id
EOF;
			$pst = $g_pdo->prepare($request);
			$pst->execute();

			$result = array();
			while (($record = $pst->fetch()) != null) {
				$result[] = $record;
			}
			return $result;
		}

		public static function format_value($value, $field, $id) {
			if ($field->type == "timestamp") {
				return t2s($value, "Y/m/d-H:i:s");
			}
			if ($field->type == "html") {
				$result = <<<EOF
<a href="JavaScript:eb_show_html('html_${id}_{$field->name}')">&gt;&gt;details</a>
<div id="html_${id}_{$field->name}" style="display: none;">
	$value
</div>
EOF;
				return $result;
			}
			if (dd()->has_entity($field->type)) {
				return '<a href="?action=manage&amp;type='.$field->type.'">'.$value.'</a>';
			}
			return $value;
		}

		public function check_form() {
			// TODO: To be done.
		}

		public function hydrate_from_form() {
			$this->created_t = time();
			$this->mod_t = $this->created_t;
			foreach (dd()->get_entity($this->type)->get_fields() as $field) {
				$field->value = $_GET[$field->name];
				$this->fields[] = $field;
			}
		}

		public function store() {
			global $g_pdo;

			$other_fields = array();
			foreach ($this->fields as $field) {
				$other_fields[] = "`{$field->colname}`= :{$field->name}";
			}
			$rest = join(",", $other_fields);

			$request = <<<EOF
INSERT INTO {$this->type}
SET
	`id`= :id,
	`created_t`= :created_t,
	`mod_t`= :mod_t,
	$rest
EOF;
			debug($request);
			$pst = $g_pdo->prepare($request);
			$array = array(
				":id" => $this->id,
				":created_t" => $this->created_t,
				":mod_t" => $this->mod_t,
			);
			foreach ($this->fields as $field) {
				$array[":".$field->name] = $field->value;
			}
			$pst->execute($array);
		}
	}
?>