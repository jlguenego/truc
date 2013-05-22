<?php
	$g_dd = new DataDictionary();
	$e = $g_dd->add_entity("advertisement");
	$e->add_field("content_h", "{{Content}}", "html");
	$e->add_field("name", "{{Name}}", "string");
	$e->add_field("event_id", "{{Event Id}}", "event");
	$f = $e->add_field("status", "{{Status}}", "status");
	$f->status_def = array(
		ADVERTISEMENT_STATUS_DEFAULT => "{{Not sent}}",
		ADVERTISEMENT_STATUS_SENT => "{{Sent}}",
	);
	$f->is_in_create_form = false;
	$e->add_action("send", "{{Send}}");
	$e->add_action("send_to_me", "{{Send to me only}}");
	$e->add_action("delete", "{{Delete}}");

	$e = $g_dd->add_entity("task");
	$e->add_field("start_t", "{{Start date}}", "timestamp");
	$f = $e->add_field("status", "{{Status}}", "status");
	$f->status_def = array(
		TASK_STATUS_PENDING => "{{Pending}}",
		TASK_STATUS_RUNNING => "{{Running}}",
		TASK_STATUS_SUCCESS => "{{Success}}",
		TASK_STATUS_ERROR => "{{Error}}",
	);
	$f->is_in_create_form = false;
	$e->add_field("description", "{{Description}}", "string");
	$e->add_field("command", "{{Command}}", "string");
	$e->add_field("parameters", "{{Parameters}}", "string");
	$e->add_field("error_msg", "{{Error message}}", "string");
	$e->add_field("event_id", "{{Event Id}}", "event");
	$e->add_action("execute", "{{Execute}}");
	$e->add_action("execute_now", "{{Execute now}}");
	$e->add_action("delete", "{{Delete}}");


	$e = $g_dd->add_entity("guest");
	$e->add_field("email", "{{Email}}", "mail");

	$e = $g_dd->add_entity("interaction");
	$e->add_field("guest_id", "{{Guest Id}}", "guest");
	$e->add_field("advertisement_id", "{{Advertisement Id}}", "advertisement");

	$e = $g_dd->add_entity("event");
	$e->add_field("title", "{{Title}}", "string");
?>