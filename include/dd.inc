<?php
	$g_dd = new DataDictionary();
	$e = $g_dd->add_entity("advertisement");
	$e->add_field("content_h", "{{Content}}", "html");
	$e->add_field("name", "{{Name}}", "string");
	$e->add_field("event_id", "{{Event Id}}", "event");
	$f = $e->add_field("status", "{{Status}}", "int");
	$f->is_in_create_form = false;
	$e->add_action("send", "{{Send}}");
	$e->add_action("send_to_me", "{{Send to me only}}");
	$e->add_action("delete", "{{Delete}}");

	$e = $g_dd->add_entity("guest");
	$e->add_field("email", "{{Email}}", "mail");

	$e = $g_dd->add_entity("interaction");
	$e->add_field("guest_id", "{{Guest Id}}", "guest");
	$e->add_field("advertisement_id", "{{Advertisement Id}}", "advertisement");

	$e = $g_dd->add_entity("event");
	$e->add_field("title", "{{Title}}", "string");
?>