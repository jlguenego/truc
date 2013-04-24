<?php
	$user = $g_display["user"];
	$event = $g_display["event"];
	$link = HOST."?action=retrieve&amp;type=event&amp;id=".$event->id;
?>
<p>
	Chèr(e) <?php echo $user->get_name(); ?>,<br/>
	Votre évènement (<a href="<?php echo $link; ?>"><?php echo $event->title; ?></a>) a été publié.<br/>
</p>