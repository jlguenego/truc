<?php
	function format_ucfirst($array) {
		return our_mb_ucfirst($array[1], "UTF-8");
	}

	function format_ucwords($str) {
		return preg_replace_callback('/([\wçÇàÀéÉèÈêÊëËïÏ]+)/iu', "format_ucfirst", $str);
	}

	function format_firstname($firstname) {
		return format_ucwords(mb_strtolower($firstname, "UTF-8"));
	}

	function format_lastname($lastname) {
		return mb_strtoupper($lastname, "UTF-8");
	}

	function format_participate_button($event) {
		$participate = _t("Book tickets!");
		if ($event->is_confirmed()) {
			$participate = _t("Buy tickets!");
		}
		return $participate;
	}

	function format_partner($name) {
		return format_ucwords(mb_strtolower($name, "UTF-8"));
	}

	function format_participate_title($event) {
		$participate = _t("Book tickets");
		if ($event->is_confirmed()) {
			$participate = _t("Buy tickets");
		}
		return $participate;
	}

	function format_date($date, $format = "%Y-%m-%d") {
		setlocale(LC_ALL, I18n::get_os_locale());
		if ($format != "timestamp") {
			$date = s2t($date, $format);
		}
		return strftime("%A %#d %B %Y", $date);
	}

	function format_mail_subject($subject) {
		return "=?UTF-8?B?".base64_encode($subject)."?=";
	}

	function format_columns($blocks) {
		$result = <<<EOF
<div id="eb_two_column">
EOF;
		$left_column = array();
		$right_column = array();
		foreach($blocks as $block) {
			$side = $block->side;
			if ($side == 'left') {
				$left_column[] = $block;
			} else {
				$right_column[] = $block;
			}
		}

		$left_column_blocks = format_blocks($left_column);
		$right_column_blocks = format_blocks($right_column);

		$result .= <<<EOF
	<div class="eb_column eb_left_column">
		${left_column_blocks}
	</div>
	<div class="eb_column eb_right_column">
		${right_column_blocks}
	</div>
</div>
EOF;
		return $result;
	}

	function format_blocks($array) {
		$result = '';
		foreach ($array as $block) {
			$result .= <<<EOF
		<div class="eb_block {$block->css_class}">
			<div class="eb_block_title">{{{$block->title}}}</div>
			<div class="eb_block_content">{$block->content}</div>
		</div>
EOF;
		}
		return $result;
	}

	function format_html2text($html) {
		$text = strip_tags($html);
		$text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
		return $text;
	}
?>