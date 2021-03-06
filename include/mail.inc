<?php
	function mail_advertise($event_id, $guest_mail, $advertisement_id) {
		global $g_display;

		$event = Event::get_from_id($event_id);
		$ad = Record::get_from_id("advertisement", $advertisement_id);
		$subject = format_mail_subject($ad->get_field("name")->value);
		$msg_html = $ad->get_field("content_h")->value;

		$msg = mail_build($msg_html, $guest_mail);
		if (TEST_MODE) {
			usleep(500 * 1000);
		}
		mail($guest_mail, $subject, $msg["content"], $msg["header"]);
	}

	function mail_notify_publication($event, $reason = "") {
  		global $g_display;

		$subject = format_mail_subject(_t("Publication notification"));
		$msg_html = "";
		$event_owner = User::get_from_id($event->user_id);
		$g_display["user"] = $event_owner;
		$g_display["event"] = $event;
		$g_display["reason"] = $reason;
		$i18n = $event_owner->get_i18n();
		if ($event->is_published()) {
			$msg_html = $i18n->get_mail_html(BASE_DIR."/mail/event_published.php");
		} else {
			$msg_html = $i18n->get_mail_html(BASE_DIR."/mail/event_unpublished.php");
		}

		$msg = mail_build($msg_html, $event_owner->email);
		mail($event_owner->email, $subject, $msg["content"], $msg["header"]);
	}

	function mail_publication_request($event) {
		global $g_display;
		global $g_i18n;

		$g_display["event"] = $event;
		$subject = format_mail_subject(_t("Publication request"));
		$msg_html = $g_i18n->get_mail_html(BASE_DIR."/mail/publication_request.php");

		$msg = mail_build($msg_html, CONTACT_MAIL);
		mail(CONTACT_MAIL, $subject, $msg["content"], $msg["header"]);
	}

	function mail_payment($mail, $bill) {
		global $g_display;

		$event = Event::get_from_id($bill->event_id);
		$user = User::get_from_id($bill->user_id);
		$subject = format_mail_subject(_t("Payment authorization"));
		$g_display["user"] = $user;
		$g_display["bill"] = $bill;
		if ($bill->status == BILL_STATUS_PLANNED) {
			$msg_html = $user->get_i18n()->get_mail_html(BASE_DIR."/mail/payment_authorization.php");
			$subject = _t("Payment confirmation and invoice");
		} else if ($bill->status == BILL_STATUS_CONFIRMED) {
			$msg_html = $user->get_i18n()->get_mail_html(BASE_DIR."/mail/payment_full.php");
		} else {
			throw new Exception("Unexpected bill status.");
		}

		$msg = mail_build($msg_html, $mail);
		mail($mail, $subject, $msg["content"], $msg["header"]);
  	}

  	function mail_event_confirm($event_id) {
  		global $g_display;

		$subject = format_mail_subject(_t("Event confirmation"));
		$event = Event::get_from_id($event_id);
		$bill_array = $event->get_bill(BILL_TYPE_QUOTATION);
		foreach ($bill_array as $bill) {
			$user = User::get_from_id($bill->user_id);
			$i18n = $user->get_i18n();
			$mail = $user->email;


			$g_display["invoice"] = $bill->create_invoice();
			$g_display["quotation"] = $bill;
			$g_display["user"] = $user;
			$msg_html = $i18n->get_mail_html(BASE_DIR."/mail/event_confirm.php");
			debug("msg_html=".$msg_html);
			$msg = mail_build($msg_html, $mail);
			debug("send mail to: ". $mail);
			mail($mail, $subject, $msg["content"], $msg["header"]);
		}
  	}

  	function mail_event_cancel($event_id) {
  		global $g_display;

		$subject = format_mail_subject(_t("Event cancellation"));
		$event = Event::get_from_id($event_id);
		$bill_array = $event->get_bill(BILL_TYPE_QUOTATION);
		foreach ($bill_array as $bill) {
			$user = User::get_from_id($bill->user_id);
			$i18n = $user->get_i18n();
			$mail = $user->email;

			$g_display["bill"] = $bill;
			$g_display["user"] = $user;
			$msg_html = $i18n->get_mail_html(BASE_DIR."/mail/event_cancel.php");
			debug("msg_html=".$msg_html);
			$msg = mail_build($msg_html, $mail);
			mail($mail, $subject, $msg["content"], $msg["header"]);
		}
  	}

  	function mail_inscription($user) {
  		global $g_display;

  		$g_display["user"] = $user;
  		$msg_html = $user->get_i18n()->get_mail_html(BASE_DIR."/mail/inscription.php");

		$msg = mail_build($msg_html, $user->email);
		$subject = format_mail_subject(_t("Activate your account"));
		mail($user->email, $subject, $msg["content"], $msg["header"]);
  	}

  	function mail_forgotten_password($user, $token) {
  		global $g_display;
  		global $g_i18n;

		$g_display["user"] = $user;
		$g_display["token"] = $token;
		$i18n = $user->get_i18n();
		$msg_html = $i18n->get_mail_html(BASE_DIR."/mail/forgotten_password.php");

		$msg = mail_build($msg_html, $user->email);
		$subject = format_mail_subject($i18n->_t("Forgotten password"));
		mail($user->email, $subject, $msg["content"], $msg["header"]);
  	}

  	function mail_password_changed($user) {
  		global $g_display;

  		$g_display["user"] = $user;
		$msg_html = $user->get_i18n()->get_mail_html(BASE_DIR."/mail/password_changed.php");

		$msg = mail_build($msg_html, $user->email);
		$subject = format_mail_subject($user->get_i18n()->_t("Password changed"));
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