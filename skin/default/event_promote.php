<?php
	$event = $g_display["event"];
	$author = $g_display["author"];
?>
	<div class="evt_title"><p><?php echo $event->title; ?></p></div>

<ul>
	<li><a href="?action=get_form&type=guest_file">Import or merge guest file</a></li>
	<li>Create, Retrieve, Update, Delete advertisement</li>
	<li>Create, Retrieve, Update, Delete campaign</li>
	<li>Start, Stop campaign</li>
</ul>