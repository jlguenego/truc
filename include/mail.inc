<?php
	require_once(BASE_DIR."/mail/inscription.php");
	require_once(BASE_DIR."/mail/event_confirm.php");
	require_once(BASE_DIR."/mail/event_cancel.php");
	require_once(BASE_DIR."/mail/payment_full.php");
	require_once(BASE_DIR."/mail/payment_authorization.php");
	require_once(BASE_DIR."/mail/forgotten_password.php");

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
		$subject = "Event confirmation";
		$event = Event::get_from_id($event_id);
		$devis_array = $event->get_devis();
		foreach ($devis_array as $devis) {
			$user = User::get_from_id($devis->user_id);
			$mail = $user->email;

			debug("event id=".$devis->event_id);
			$event = Event::get_from_id($devis->event_id);
			$invoice = $devis->create_invoice();
			$msg_html = mail_html_event_confirm($devis->label, $invoice, $event, $user);
			debug("msg_html=".$msg_html);
			$msg = mail_build($msg_html, $mail);
			debug("send mail to: ". $mail);
			mail($mail, $subject, $msg["content"], $msg["header"]);
		}
  	}

  	function mail_event_cancel($event_id) {
		$subject = "Event cancellation";
		$event = Event::get_from_id($event_id);
		$devis_array = $event->get_devis();
		foreach ($devis_array as $devis) {
			$user = User::get_from_id($devis->user_id);
			$mail = $user->email;

			$event = Event::get_from_id($devis->event_id);
			$msg_html = mail_html_event_cancel($devis, $event, $user);
			debug("msg_html=".$msg_html);
			$msg = mail_build($msg_html, $mail);
			mail($mail, $subject, $msg["content"], $msg["header"]);
		}
  	}

  	function mail_inscription($mail, $login, $key) {
		$msg_html = mail_html_inscription($key, $login);

		$msg = mail_build($msg_html, $mail);
		$subject = "Activate your account";
		mail($mail, $subject, $msg["content"], $msg["header"]);
  	}

  	function mail_forgotten_password($user, $token) {
		$msg_html = mail_html_forgotten_password($user, $token);

		$msg = mail_build($msg_html, $user->email);
		$subject = "Forgotten password";
		mail($user->email, $subject, $msg["content"], $msg["header"]);
  	}

  	function mail_build($msg_html, $mail = "") {
  		if (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $mail)){ //Mise aux normes
			$passage_ligne = "\r\n";
		}else{
			$passage_ligne = "\n";
		}

		$msg_html = str_replace("<br/>", "<br/>".$passage_ligne, $msg_html);

  		$msg_txt = htmlspecialchars($msg_html);
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
		$header.= 'MIME-Version: 1.0'.$passage_ligne;
		$header.= 'Content-Type: multipart/mixed;'.$passage_ligne.' boundary="'.$boundary.'"'.$passage_ligne;

		$msg = array(
			"header" => $header,
			"content" => $content,
		);

		return $msg;
  	}
?>