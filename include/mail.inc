<?php
	require_once(BASE_DIR."/mail/inscription.php");
	require_once(BASE_DIR."/mail/event_confirm.php");
	require_once(BASE_DIR."/mail/event_cancel.php");
	require_once(BASE_DIR."/mail/payment.php");

	function mail_payment($mail, $devis) {
		$event = new Event();
		$event->load($devis->event_id);
		$msg_html = mail_html_payment($devis, $event);

		$msg = mail_build($msg_html, $mail);
		$subject = "Payment authorization";
		mail($mail, $subject, $msg["content"], $msg["header"]);
  	}

  	function mail_event_confirm($event_id) {
		$subject = "Event confirmation";
		$devis_array = devis_get_all_for_event($event_id);
		foreach ($devis_array as $devis) {
			$user = get_user($devis->user_id);
			$mail = $user["email"];

			$event = new Event();
			debug("event id=".$devis->event_id);
			$event->load($devis->event_id);
			$msg_html = mail_html_event_confirm($devis, $event);
			debug("msg_html=".$msg_html);
			$msg = mail_build($msg_html, $mail);
			debug("send mail to: ". $mail);
			mail($mail, $subject, $msg["content"], $msg["header"]);
		}
  	}

  	function mail_event_cancel($event_id) {
		$subject = "Event cancellation";
		$devis_array = devis_get_all_for_event($event_id);
		foreach ($devis_array as $devis) {
			$user = get_user($devis->user_id);
			$mail = $user["email"];

			$event = new Event();
			$event->load($devis->event_id);
			$msg_html = mail_html_event_cancel($devis, $event);
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