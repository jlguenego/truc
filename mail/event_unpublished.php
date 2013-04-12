<?php
	function mail_html_event_unpublished($user, $event, $reason) {
		$link = HOST."?action=retrieve&amp;type=event&amp;id=".$event->id;
		ob_start();
		include("header.php");
?>
		<p>
			Dear <?php echo $user->get_name(); ?>,<br/>
			Your event (<a href="<?php echo $link; ?>"><?php echo $event->title; ?></a>) has been unpublished.<br/>
		</p>
		<p>
<?php echo nl2br($reason); ?>
		</p>
<?php
		include("footer.php");
		$result = ob_get_contents();
		ob_end_clean();
		return $result;
	}
?>