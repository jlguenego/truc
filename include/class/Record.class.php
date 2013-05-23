<?php
	class Record {
		public $type;

		public $id;
		public $created_t;
		public $mod_t;
		public $fields = array();

		public static function new_instance($type) {
			if (!dd()->has_entity($type)) {
				throw new Exception("Entity does not exists: ".$type);
			}
			$e = dd()->get_entity($type);
			$result = null;
			debug("classname=".$e->classname);
			if (class_exists($e->classname) && get_parent_class($e->classname) == "Record") {
				$result = new $e->classname();
			} else {
				$result = new Record();
			}
			$result->type = $type;
			return $result;
		}

		public static function get_classname($type) {
			if (!dd()->has_entity($type)) {
				throw new Exception("Entity does not exists: ".$type);
			}
			$e = dd()->get_entity($type);
			$result = "";
			debug("classname=".$e->classname);
			if (class_exists($e->classname) && get_parent_class($e->classname) == "Record") {
				$result = $e->classname;
			} else {
				$result = "Record";
			}
			return $result;
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
			$record = Record::new_instance($type);

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
			debug("array=".sprint_r(array(":id" => $this->id)));
			$pst = $g_pdo->prepare($request);
			$pst->execute(array(":id" => $this->id));
		}

		public static function get_table($type) {
			$classname = Record::get_classname($type);
			$result = <<<EOF
<table class="evt_table inline">
	<tr>
		<th><input type="checkbox" class="check_all_record" /></th>
		<th>Actions</th>
EOF;
			$columns = $classname::get_fields($type);
			foreach ($columns as $field) {
				$result .= <<<EOF
		<th>{$field->label}</th>
EOF;
			}
			$result .= <<<EOF
	</tr>
EOF;
			foreach ($classname::select_all($type) as $db_record) {
				$record = Record::get_from_db_record($db_record, $type);
				$result .= <<< EOF
	<tr>
EOF;
				$id = $db_record["id"];
				$html = <<<EOF
			<select class="evt_record_actions">
				<option value="#" selected>Choose...</option>
EOF;
						$classname = dd()->get_entity($type)->classname;
				foreach (dd()->get_entity($type)->get_actions() as $action) {
					if ($record->accept($action)) {
						$html .= <<<EOF
				<option value="?action={$action->name}&amp;type=$type&amp;id=$id">{$action->label}</option>
EOF;
					}
				}
					$html .= <<<EOF
			</select>
EOF;
			$result .= <<<EOF
		<td><input type="checkbox" name="$id" class="record" /></td>
		<td>$html</td>
EOF;
				foreach ($columns as $field) {
					$colname = Field::get_colname($field->name);
					$value = $db_record[$colname];
					$html = Record::format_value($value, $field, $db_record["id"]);
					$result .= <<<EOF
		<td>$html</td>
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
			$fields[] = new Field("id", "{{Id}}", "primary_key");
			$fields[] = new Field("created_t", "{{Created}}", "timestamp");
			$fields[] = new Field("mod_t", "{{Last modified}}", "timestamp");
			return array_merge($fields, dd()->get_entity($type)->get_fields());
		}

		public function get_field($name) {
			foreach ($this->fields as $field) {
				if ($field->name == $name) {
					return $field;
				}
			}
			throw new Exception("Field $name not found in {$this->type}");
		}

		public function get_value($name) {
			return $this->get_field($name)->value;
		}

		public function set_value($name, $value) {
			$this->get_field($name)->value = $value;
		}

		public static function select_all($type = "") {
			global $g_pdo;
			if ($type == "") {
				throw new Exception("Need a type");
			}
			$request = <<<EOF
SELECT * FROM $type
ORDER BY id
EOF;
			$pst = $g_pdo->prepare($request);
			$pst->execute();

			$result = array();
			while (($record = $pst->fetch(PDO::FETCH_ASSOC)) != null) {
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
			if ($field->type == "status") {
				$result = $field->status_def[$value];
				return $result;
			}
			if (dd()->has_entity($field->type)) {
				return '<a href="?action=retrieve&amp;type='.$field->type.'&amp;id='.$value.'">'.$value.'</a>';
			}
			return $value;
		}

		public function check_form() {
			// TODO: To be done.
		}

		public function hydrate() {
			$this->id = create_id();
			$this->created_t = time();
			$this->mod_t = $this->created_t;
			foreach (dd()->get_entity($this->type)->get_fields() as $field) {
				$this->fields[] = clone $field;
			}
		}

		public function hydrate_from_form() {
			$this->created_t = time();
			$this->mod_t = $this->created_t;
			foreach (dd()->get_entity($this->type)->get_fields() as $field) {
				if ($field->is_in_create_form) {
					if ($field->type == "timestamp") {
						$field->value = s2t($_GET[$field->name]);
					} else {
						$field->value = $_GET[$field->name];
					}
				} else {
					switch ($field->type) {
						case "int":
						case "status":
							$field->value = 0;
							break;
						default:
							$field->value = null;
					}
				}
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

		public static function get_from_db_record($db_record, $type) {
			$result = Record::new_instance($type);
			$e = dd()->get_entity($type);
			$result->id = $db_record["id"];
			$result->created_t = $db_record["created_t"];
			$result->mod_t = $db_record["mod_t"];

			foreach ($e->get_fields() as $field) {
				$f = clone $field;
				$f->value = $db_record[$f->colname];
				$result->fields[] = $f;
			}
			return $result;
		}

		public function accept($action) {
			return true;
		}

		public static function get_menu($type) {
			$a = array();
			foreach (dd()->get_entity($type)->get_global_actions() as $action) {
				$a[] = <<<EOF
<a href="javascript:eb_execute_global_action('$type', '{$action->name}', '{$action->label}');">{$action->label}</a>
EOF;
			}
			foreach (dd()->get_entity($type)->get_grouped_actions() as $action) {
				$a[] = <<<EOF
<a href="javascript:eb_execute_grouped_action('$type', '{$action->name}', '{$action->label}');">{$action->label}</a>
EOF;
			}
			return join("&nbsp;|&nbsp;", $a);
		}

		public static function get_dialog_content($type) {
			$result = <<<EOF
<div id="dialog_create" style="display: none;" title="">
	<form name="form_execute_global_action_create" action="?action=create&amp;type=$type" method="post">
EOF;
			foreach (dd()->get_entity($type)->get_fields() as $field) {
				if (!$field->is_in_create_form) {
					continue;
				}
				if ($field->is_foreign_key()) {
					$session_fieldname = $_SESSION[$field->name];
					$result .= <<<EOF
		<input type="hidden" name="{$field->name}" value="$session_fieldname"/>
EOF;
				} else if ($field->type == "html") {
					$result .= <<<EOF
		<textarea name="{$field->name}" class="apply_tinymce" placeholder="{$field->label}"></textarea>
EOF;
				} else if ($field->type == "timestamp") {
					$result .= <<<EOF
		<input class="timestamp_date" type="text" name="{$field->name}" placeholder="{$field->label}" autocomplete="off"/>
EOF;
				} else {
					$result .= <<<EOF
		<input type="text" name="{$field->name}" placeholder="{$field->label}"/>
EOF;
				}
			}
			$result .= <<<EOF
	</form>
</div>
EOF;
			return $result;
		}

		public static function create() {
			global $g_info_msg;
			global $g_error_msg;

			debug(sprint_r($_GET));

			try {
				$record = Record::new_instance($_GET["type"]);
				$record->check_form();
				$record->id = create_id();
				$record->hydrate_from_form();
				$record->store();
				$g_info_msg = _t("Record successfully created.");
			} catch (Exception $e) {
				$g_error_msg = $e->getMessage();
			}
		}

		public static function multi_action($action) {
			$id_array = explode("_", $_GET["ids"]);
			foreach ($id_array as $id) {
				$record = Record::get_from_id($_GET["type"], $id);
				if ($record != null) {
					$record->$action();
				}
			}
		}
	}
?>