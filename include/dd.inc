<?php
	$g_dd = new DataDictionary();
	$e = $g_dd->add_entity("advertisement");
	$e->add_field("content_h", "html");
	$e->add_field("name", "string");
	$e->add_field("event_id", "event");
	$e->add_action("send", "{{Send}}");

	$e = $g_dd->add_entity("guest");
	$e->add_field("email", "mail");

	$e = $g_dd->add_entity("interaction");
	$e->add_field("guest_id", "guest");
	$e->add_field("advertisement_id", "advertisement");

	$e = $g_dd->add_entity("event");
	$e->add_field("title", "string");
?>