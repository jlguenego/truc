<?php
	function form_event_valid($update = FALSE) {
		debug("About to validate form");

		foreach ($_GET as $key => $value) {
			if (is_null_or_empty($_SESSION["form"])) {
				throw new Exception("Internal Error: SESSION['form'] is not set");
			}
			$f = $_SESSION["form"];
			if (!$f->has_item($key)) {
				continue;
			}
			if ($f->is_optional($key)) {
				continue;
			}
			if (is_null_or_empty($value)) {
				throw new Exception(_t("Please fill all fields: ") . $f->get_label($key));
			}
		}

		if (!check_date($_GET["happening_t"])) {
			throw new Exception(_t("The given date is wrong"));
		}

		foreach ($_GET['ticket_amount_a'] as $amount) {
			if (is_null_or_empty($amount) || !is_number($amount)) {
				throw new Exception(_t("Please enter a number for the rates"));
			}
		}

		foreach ($_GET['ticket_tax_a'] as $tax_rate) {
			if (is_null_or_empty($tax_rate) || !is_number($tax_rate)) {
				throw new Exception(_t("Please enter a number for the tax rates"));
			}
		}
		foreach ($_GET['ticket_name_a'] as $label) {
			if (is_null_or_empty($label)) {
				throw new Exception(_t("Please enter a label for each rate"));
			}
		}

		if (!$update && !isset($_GET["confirm"])) {
			throw new Exception(_t("You have to read and agree with the CGU"));
		}
		unset($_SESSION["form"]);
	}

	function form_valid_user() {
		global $g_use_recaptcha;
		if (is_null_or_empty($_SESSION["form"])) {
			throw new Exception("SESSION not well set.");
		}
		$f = $_SESSION["form"];
		foreach ($_GET as $key => $value) {
			if (!$f->has_item($key)) {
				continue;
			}
			if ($f->is_optional($key)) {
				continue;
			}
			debug($key."=>".$value);
			if (is_null_or_empty($value)) {
				throw new Exception(_t("Please fill all fields: ") . $f->get_label($key));
			}
		}
		if (!check_mail($_GET["email"])) {
			throw new Exception(_t("Invalid mail"));
		}
		if (User::used_mail($_GET["email"])) {
			throw new Exception(_t("This mail is already used."));
		}
		if ($_GET['password'] != $_GET['password2']) {
			throw new Exception(_t("Passwords are different"));
		}
		if ($g_use_recaptcha) {
			require_once(BASE_DIR . '/include/recaptcha.inc');
		    $privatekey = CAPTCHA_PRIV_KEY;
		    $resp = recaptcha_check_answer ($privatekey,
										$_SERVER["REMOTE_ADDR"],
										$_POST["recaptcha_challenge_field"],
										$_POST["recaptcha_response_field"]);

			if (!$resp->is_valid) {
				// What happens when the CAPTCHA was entered incorrectly
				throw new Exception (_t("The reCAPTCHA wasn't entered correctly."));
			}
		}
	}

	function form_valid_user_update($user) {
		if (!is_null_or_empty($_GET["firstname"])
			&& $user->firstname != $_GET["firstname"]) {
			$user->firstname = $_GET["firstname"];
		}
		if (!is_null_or_empty($_GET["lastname"])
			&& $user->lastname != $_GET["lastname"]) {
			$user->lastname = $_GET["lastname"];
		}
		if ($user->phone != $_GET["phone"]) {
			$user->phone = $_GET["phone"];
		}
		if ($user->street != $_GET["street"]) {
			$user->street = $_GET["street"];
		}
		if ($user->zip != $_GET["zip"]) {
			$user->zip = $_GET["zip"];
		}
		if ($user->city != $_GET["city"]) {
			$user->city = $_GET["city"];
		}
		if ($user->country != $_GET["country"]) {
			$user->country = $_GET["country"];
		}
		$user->state = $_GET["state"];
		if (!is_null_or_empty($_GET["email"])) {
			if (!check_mail($_GET["email"])) {
				throw new Exception(_t("Invalid mail."));
			}
			if (User::used_mail($_GET["email"])) {
				throw new Exception(_t("This mail is already used."));
			}
			$user->email = $_GET['email'];
		}
		if (!is_null_or_empty($_GET['new_pass'])) {
			if ($_GET['new_pass'] != $_GET['new_pass2']) {
				throw new Exception(_t("New passwords are different."));
			}
			$user->password = $_GET['new_pass'];
		} else {
			$user->password = "";
		}
		$user->clean_format();
		debug("user=".  sprint_r($user));
	}

	function form_get_country_options($country = "") {
		$result = file_get_contents("etc/countries.html");
		$result = str_replace("<option value=\"$country\">", "<option value=\"$country\" selected=\"selected\">", $result);
		return $result;
	}
?>