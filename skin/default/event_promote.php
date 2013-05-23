<?php
	$event = $g_display["event"];
	$author = $g_display["author"];
?>
<div class="evt_title"><p>{{Promote your event}}</p></div>
<?php require($g_i18n->filename(SKIN_DIR."/sidebar_promote.php")); ?>
<p>
	First define your <a href="?action=manage&type=guest">guests</a>. A guest represents a potential customer you whish to advertise.
	You can import many guests from a single file.
</p>
<p>
	Then define your <a href="?action=manage&type=advertisement">advertisement</a>. An advertisement is a mail template.
	When you are ready, you can send the advertisement to all your guests.
</p>
<p>
	This send operation generates one <a href="?action=manage&type=task">task</a> per mail sent to a guest.
	Finally check the task progression to be informed of the sent mails.
</p>