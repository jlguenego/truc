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

	function echo_default_value($name, $default = "") {
		if (!is_null_or_empty($_GET[$name])) {
			echo $_GET[$name];
		} else {
			echo $default;
		}
	}

	function mail_to($mail, $login, $key) {
		$mailcrypt = sha1($mail);
		if (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $mail)){ //Mise aux normes
			$passage_ligne = "\r\n";
		}else{
			$passage_ligne = "\n";
		}

		$lien = HOST.'/?action=activation&amp;key='.$key;
		$msg_html = '<p>Ceci est un mail automatique. Veuillez ne pas y répondre.<br/><br/>'.$passage_ligne.$passage_ligne;
		$msg_html.= 'Merci '.$login.' de vous être inscrit.<br/>'.$passage_ligne;
		$msg_html.= 'Veuillez cliquer sur le liens suivant pour activer votre compte:<br/>'.$passage_ligne;
		$msg_html.= '<a href="'.$lien.'">'.$lien.'</a><br/><br/>'.$passage_ligne.$passage_ligne.'</p>';


		$msg_txt = htmlspecialchars($msg_html);
		$boundary = '----='.md5(rand());
		$boundary_alt = "-----=".md5(rand());

		/*FORMATION DU HEADER*/
		$header = 'From: "'.CONTACT_MAIL.'" <'.CONTACT_MAIL.'>'.$passage_ligne;
		$header.= 'MIME-Version: 1.0'.$passage_ligne;
		$header.= 'Content-Type: multipart/mixed;'.$passage_ligne.' boundary="'.$boundary.'"'.$passage_ligne;

		/*CONTENU DU MAIL*/
		$content = $passage_ligne.'--'.$boundary.$passage_ligne;
		$content.= 'Content-Type: multipart/alternative;'.$passage_ligne.' boundary="'.$boundary_alt.'"'.$passage_ligne;
		$content.= $passage_ligne.'--'.$boundary_alt.$passage_ligne;

		//Message texte
		$content.= 'Content-Type: text/plain; charset="utf-8"'.$passage_ligne;
		$content.= 'Content-Transfer-Encoding: 8bit'.$passage_ligne;
		$content.= $passage_ligne.$msg_txt.$passage_ligne;

		//-----------------------------------------------------------
		$content.= $passage_ligne.'--'.$boundary_alt.$passage_ligne;

		//Message html
		$content.= 'Content-Type: text/html; charset=utf-8'.$passage_ligne;
		$content.= 'Content-Transfer-Encoding: 8bit'.$passage_ligne;
		$content.= $passage_ligne.$msg_html.$passage_ligne;

		//-----------------------------------------------------------
		$content.= $passage_ligne."--".$boundary_alt."--".$passage_ligne;
		$content.= $passage_ligne."--".$boundary."--".$passage_ligne;

		/*ENVOI DU MAIL*/
		$subject = "Activate your account";
		mail($mail, $subject, $content, $header);
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
?>