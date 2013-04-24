<?php
	require_once(BASE_DIR."/mail/inscription.php");
	require_once(BASE_DIR."/mail/payment_full.php");
	require_once(BASE_DIR."/mail/payment_authorization.php");
	require_once(BASE_DIR."/mail/forgotten_password.php");
	require_once(BASE_DIR."/mail/password_changed.php");
	require_once(BASE_DIR."/mail/publication_request.php");

	function mail_notify_publication($user, $event, $reason = "") {
  		global $g_display;

		$subject = "Publication notification";
		$msg_html = "";
		$g_display["user"] = $user;
		$g_display["event"] = $event;
		$g_display["reason"] = $reason;
		if ($event->is_published()) {
			$msg_html = i18n_get_html(i18n_filename(BASE_DIR."/mail/event_published.php"));
		} else {
			$msg_html = i18n_get_html(i18n_filename(BASE_DIR."/mail/event_unpublished.php"));
		}

		$msg = mail_build($msg_html, $user->email);
		mail($user->email, $subject, $msg["content"], $msg["header"]);
	}

	function mail_publication_request($event) {
		$subject = "Publication request";
		$msg_html = mail_html_publication_request($event);

		$msg = mail_build($msg_html, CONTACT_MAIL);
		mail(CONTACT_MAIL, $subject, $msg["content"], $msg["header"]);
	}

	function mail_payment($mail, $devis) {
		$event = Event::get_from_id($devis->event_id);
		$user = User::get_from_id($devis->user_id);
		$subject = "Payment authorization";
		if ($devis->status == DEVIS_STATUS_CONFIRMED) {
			$msg_html = mail_html_payment_full($devis, $event, $user);
			$subject = "Payment confirmation and invoice";
		} else if ($devis->status == DEVIS_STATUS_PLANNED) {
			$msg_html = mail_html_payment_authorization($devis, $event, $user);
		} else {
			throw new Exception("Unexpected devis status.");
		}

		$msg = mail_build($msg_html, $mail);
		mail($mail, $subject, $msg["content"], $msg["header"]);
  	}

  	function mail_event_confirm($event_id) {
  		global $g_display;

		$subject = "Event confirmation";
		$event = Event::get_from_id($event_id);
		$devis_array = $event->get_devis(DEVIS_TYPE_QUOTATION);
		foreach ($devis_array as $devis) {
			$user = User::get_from_id($devis->user_id);
			$mail = $user->email;


			$g_display["invoice"] = $devis->create_invoice();
			$g_display["quotation"] = $devis;
			$g_display["user"] = $user;
			$msg_html = i18n_get_html(i18n_filename(BASE_DIR."/mail/event_confirm.php"));
			debug("msg_html=".$msg_html);
			$msg = mail_build($msg_html, $mail);
			debug("send mail to: ". $mail);
			mail($mail, $subject, $msg["content"], $msg["header"]);
		}
  	}

  	function mail_event_cancel($event_id) {
  		global $g_display;

		$subject = "Event cancellation";
		$event = Event::get_from_id($event_id);
		$devis_array = $event->get_devis(DEVIS_TYPE_QUOTATION);
		foreach ($devis_array as $devis) {
			$user = User::get_from_id($devis->user_id);
			$mail = $user->email;

			$g_display["bill"] = $devis;
			$g_display["user"] = $user;
			$msg_html = i18n_get_html(i18n_filename(BASE_DIR."/mail/event_cancel.php"));
			debug("msg_html=".$msg_html);
			$msg = mail_build($msg_html, $mail);
			mail($mail, $subject, $msg["content"], $msg["header"]);
		}
  	}

  	function mail_inscription($user) {
		$msg_html = mail_html_inscription($user);

		$msg = mail_build($msg_html, $user->email);
		$subject = "Activate your account";
		mail($user->email, $subject, $msg["content"], $msg["header"]);
  	}

  	function mail_forgotten_password($user, $token) {
		$msg_html = mail_html_forgotten_password($user, $token);

		$msg = mail_build($msg_html, $user->email);
		$subject = "Forgotten password";
		mail($user->email, $subject, $msg["content"], $msg["header"]);
  	}

  	function mail_password_changed($user) {
  		$msg_html = mail_html_password_changed($user);

		$msg = mail_build($msg_html, $user->email);
		$subject = "Password changed";
		mail($user->email, $subject, $msg["content"], $msg["header"]);
  	}

  	function mail_build($msg_html, $mail = "") {
  		if (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $mail)){ //Mise aux normes
			$passage_ligne = "\r\n";
		}else{
			$passage_ligne = "\n";
		}

		$msg_html = str_replace("<br/>", "<br/>".$passage_ligne, $msg_html);

  		$msg_txt = html_entity_decode(strip_tags($msg_html));
		$boundary = '----='.md5(rand());
		$boundary_alt = "-----=".md5(rand());

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

		/*FORMATION DU HEADER*/
		$header = 'From: "'.CONTACT_MAIL.'" <'.CONTACT_MAIL.'>'.$passage_ligne;
		$header.= 'Bcc: "'.CONTACT_MAIL.'" <'.CONTACT_MAIL.'>'.$passage_ligne;
		$header.= 'MIME-Version: 1.0'.$passage_ligne;
		$header.= 'Content-Type: multipart/mixed;'.$passage_ligne.' boundary="'.$boundary.'"'.$passage_ligne;

		$msg = array(
			"header" => $header,
			"content" => $content,
		);

		return $msg;
  	}
?>