<?php
	require_once("include/constants.inc");

	function my_autoloader($class) {
		require_once('include/class/'.$class.'.class.php');
	}

	spl_autoload_register('my_autoloader');

	function _t($msg) {
		// TODO: implement i18n
		return $msg;
	}

	function is_null_or_empty(&$var) {
		if (!isset($var)) {
			return TRUE;
		}
		if ($var == NULL) {
			return TRUE;
		}
		if ($var == "") {
			return TRUE;
		}
		return FALSE;
	}

	function is_installed() {
		if (!file_exists(SETTINGS_INI)) {
			return false;
		}
		return true;
	}

	function admin_exists() {
		try {
			global $g_pdo;
			$dbname = MYSQL_DBNAME;
			$admin = ROLE_ADMIN;
			$request = <<<EOF
SELECT COUNT(*) FROM `${dbname}`.`user` WHERE `role`=${admin}
EOF;
			$q = $g_pdo->prepare($request);
			$q->execute();
			$count = $q->fetch();
			return $count[0] > 0;
		} catch (Exception $e) {
			println($e->getMessage());
			throw $e;
		}
	}

	function println($txt) {
		echo $txt . "<br/>";
	}

	function debug($msg) {
		global $g_debug;
		global $g_debug_lines;
		if ($g_debug) {
			$g_debug_lines[] = "<p>[DEBUG] $msg</p>";
		}
	}

	function create_id() {
		return abs((rand(0, 999) * time()) % 100000000);
	}

	function check($var) {
		//$var = mysql_escape_string($var);
		$var = htmlentities($var, 0, "UTF-8");
		// TODO: to implement
		return $var;
	}

	function check_mail($mail) {
		return preg_match("#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$#", $mail);
	}

	function check_date($date) {
		if (!preg_match("#^[\d]{4}-[0-1][\d]-[0-3][\d]$#", $date)) {
			return FALSE;
		}
		list($day, $month, $year) = explode("-", $date);
		if ($month > 12) {
			return FALSE;
		}
		if (($month == "01" || $month == "03" || $month == "05"|| $month == "07"
			|| $month == "08" || $month == "10" || $month == "12")
			&& $day > 31) {
			return FALSE;
		} elseif (($month == "04" || $month == "06"|| $month == "09"
			|| $month == "11") && $day > 30) {
			return FALSE;
		} elseif ((is_bisextil($year) && $day > 29)
			|| (!is_bisextil($year) && $day > 28)) {
			return FALSE;
		}
		return TRUE;
	}

	function is_bisextil($year) {
		if ($year == "00" && ($year % 400) == 0) {
			return TRUE;
		}
		if (($year % 4) == 0) {
			return TRUE;
		}
		return FALSE;
	}

	function redirect_to($path) {
		header('Location: ' . $path);
		header('Cache-Control: no-cache');
	}

	function is_number($var) {
		return preg_match("#^[\d]+[\.,]?[\d]*$#", $var);
	}

	function curr($nbr) {
		return "".str_replace(",", "",number_format($nbr, 2));
	}

	function echo_default_value($name, $default = "") {
		if (!is_null_or_empty($_GET[$name])) {
			echo $_GET[$name];
		} else {
			echo $default;
		}
	}

	function sprint_r($var) {
		ob_start();
		print_r($var);
		$output=ob_get_contents();
		ob_end_clean();
		return $output;
	}

	function seq_create($name, $init_value) {
		global $g_pdo;

		$request = <<<EOF
INSERT INTO `sequence`
SET
	`name`="${name}",
	`current`="${init_value}";
EOF;
		$st = $g_pdo->prepare($request);
		if ($st->execute() === FALSE) {
			echo($request."<br/>");
			throw new Exception("Sequence creation: ".sprint_r($g_pdo->errorInfo()));
		};
	}

	function seq_next($name) {
		global $g_pdo;

		$request = <<<EOF
SELECT `current` FROM `sequence`
WHERE `name`="${name}"
EOF;

		$st = $g_pdo->prepare($request);
		if ($st->execute() === FALSE) {
			throw new Exception("Sequence retrieve: ".sprint_r($g_pdo->errorInfo()));
		};

		$seq = $st->fetch();
		if (!isset($seq['current'])) {
			throw new Exception("No current value for sequence ".$name);
		}

		$value = $seq['current'] + 1;

		$request = <<<EOF
UPDATE `sequence`
SET `current`="${value}"
WHERE `name`="${name}"
EOF;
		$st = $g_pdo->prepare($request);
		if ($st->execute() === FALSE) {
			throw new Exception("Sequence update: ".sprint_r($g_pdo->errorInfo()));
		};
		return $value;
	}
	
	//string to timestamp conversion
	// TODO: implement this.
	function s2t($string, $format) {
		//$a = array();
		
		//$result = mktime(0, 0, 0, $a['tm_mon']+1, $a['tm_mday'], $a['tm_year']+1900);
		return 0;
	}
?>