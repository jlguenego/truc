<?php

	class Form {
		public $action;
		public $method = "POST";
		public $elements = array();
		public $css = "form";
		public $title = "";
		public $cancel = false;
		public $cancel_url = "";

		public function add_text($label, $name, $default, $help) {
			$item = new FormItem();
			$item->label = $label;
			$item->type = "text";
			$item->name = $name;
			$item->default = $default;
			$item->help = $help;
			$this->elements[] = $item;
			return $item;
		}

		public function add_select($label, $name, $html_spec, $help) {
			$item = new FormItem();
			$item->label = $label;
			$item->type = "select";
			$item->name = $name;
			$item->html_spec = $html_spec;
			$item->help = $help;
			$this->elements[] = $item;
			return $item;
		}

		public function add_email($label, $name, $default, $help) {
			$item = new FormItem();
			$item->label = $label;
			$item->type = "email";
			$item->name = $name;
			$item->default = $default;
			$item->help = $help;
			$this->elements[] = $item;
			return $item;
		}

		public function add_number($label, $name, $default, $help) {
			$item = new FormItem();
			$item->label = $label;
			$item->type = "number";
			$item->name = $name;
			$item->default = $default;
			$item->help = $help;
			$this->elements[] = $item;
			return $item;
		}

		public function add_textarea($label, $name, $default, $help) {
			$item = new FormItem();
			$item->label = $label;
			$item->type = "textarea";
			$item->name = $name;
			$item->default = $default;
			$item->help = $help;
			$this->elements[] = $item;
			return $item;
		}

		public function add_password($label, $name, $help) {
			$item = new FormItem();
			$item->label = $label;
			$item->type = "password";
			$item->name = $name;
			$item->help = $help;
			$this->elements[] = $item;
			return $item;
		}

		public function add_checkbox($label, $name, $checked, $help) {
			$item = new FormItem();
			$item->label = $label;
			$item->type = "checkbox";
			$item->name = $name;
			$item->html_spec = $checked;
			$item->help = $help;
			$this->elements[] = $item;
			return $item;
		}

		public function add_submit($label = "Submit") {
			$item = new FormItem();
			$item->label = $label;
			$item->type = "submit";
			$this->elements[] = $item;
			return $item;
		}

		public function add_hidden($name, $value) {
			$item = new FormItem();
			$item->type = "hidden";
			$item->name = $name;
			$item->default = $value;
			$this->elements[] = $item;
			return $item;
		}

		public function add_raw_html($html) {
			$item = new FormItem();
			$item->type = "raw_html";
			$item->html_spec = $html;
			$this->elements[] = $item;
			return $item;
		}

		public function html() {

			$result = <<<EOF
<div class="{$this->css}">
<div class="{$this->css}_title">
	{$this->title}
EOF;
			if ($this->cancel) {
				$result .= <<<EOF
<a class="{$this->css}_cancel" href="{$this->cancel_url}">{{Cancel}}</a>
EOF;
			}
				$result .= <<<EOF
</div>
<form class="{$this->css}" action="{$this->action}" method="{$this->method}">
EOF;
			$autofocus = "autofocus";
			foreach ($this->elements as $item) {
				switch ($item->type) {
					case "raw_html":
						$result .= $item->html_spec;
						break;
					case "hidden":
						$name = htmlentities($item->name);
						$default = htmlentities($item->default);
						$result .= <<<EOF
<input type="hidden" name="$name" value="$default"/>
EOF;
						break;
					case "text":
						$name = htmlentities($item->name);
						$default = htmlentities($item->default);
						$result .= <<<EOF
<div class="{$this->css}_label">{$item->label}</div>
<input type="text" id="$name" name="$name" value="$default" {$item->other_attr} $autofocus/>
<div class="{$this->css}_help">{$item->help}</div>
EOF;
						break;
					case "email":
						$result .= <<<EOF
<div class="{$this->css}_label">{$item->label}</div>
<input type="email" name="{$item->name}" value="{$item->default}"/>
<div class="{$this->css}_help">{$item->help}</div>
EOF;
						break;
					case "number":
						$result .= <<<EOF
<div class="{$this->css}_label">{$item->label}</div>
<input type="number" name="{$item->name}" value="{$item->default}" {$item->other_attr}/>
<div class="{$this->css}_help">{$item->help}</div>
EOF;
						break;

					case "password":
						$result .= <<<EOF
<div class="{$this->css}_label">{$item->label}</div>
<input type="password" name="{$item->name}" {$item->other_attr}/>
<div class="{$this->css}_help">{$item->help}</div>
EOF;
						break;
					case "textarea":
						$result .= <<<EOF
<div class="{$this->css}_label">{$item->label}</div>
<textarea name="{$item->name}" maxlength="{$item->maxlength}" title="Up to {$item->maxlength} characters" {$item->other_attr}>{$item->default}</textarea>
<div class="{$this->css}_help">{$item->help}</div>
EOF;
						break;
					case "select":
						$result .= <<<EOF
<div class="{$this->css}_label">{$item->label}</div>
<select name="{$item->name}">
{$item->html_spec}
</select>
<div class="{$this->css}_help">{$item->help}</div>
EOF;
						break;
					case "checkbox":
						$result .= <<<EOF
<input type="checkbox" name="{$item->name}" {$item->html_spec}/>{$item->label}<br/>
<div class="{$this->css}_help">{$item->help}</div>
EOF;
						break;

					case "radio":
						$result .= <<<EOF
<input type="radio" name="{$item->name}" value="{$item->default}" {$item->html_spec} />{$item->label}<br/>
EOF;
						break;

					case "submit":
						if ($this->cancel) {
						$result .= <<<EOF
<span class="{$this->css}_cancel"><input type="button" onclick="window.location='{$this->cancel_url}'" value="{{Cancel}}"/></span><span class="spacer"></span>
EOF;
						}
						$result .= "<input type=\"submit\" value=\"".$item->label."\"/>";
						break;
					default:
				}
				$autofocus = "";
			}
			$result .= "</form></div>";
			$_SESSION["form"] = $this;
			return $result;
		}

		public function get_label($name) {
			foreach ($this->elements as $item) {
				if ($item->name == $name) {
					return $item->label;
				}
			}
			throw new Exception(_t("Item not found: ").$name);
		}

		public function is_optional($name) {
			foreach ($this->elements as $item) {
				if ($item->name == $name) {
					return $item->is_optional;
				}
			}
			throw new Exception(_t("Item not found: ").$name);
		}

		public function has_item($name) {
			foreach ($this->elements as $item) {
				if ($item->name == $name) {
					return true;
				}
			}
			return false;
		}
	}

	class FormItem {
		public $label;
		public $type;
		public $name;
		public $help;
		public $maxlength;
		public $default;
		public $is_optional = false;
		public $html_spec;
		public $other_attr;
	}
?>