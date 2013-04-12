<?php
	function mail_html_event_published($user, $event) {
		$link = HOST."?action=retrieve&amp;type=event&amp;id=".$event->id;
		ob_start();
		include("header.php");
?>
		<p>
			Dear <?php echo $user->get_name(); ?>,<br/>
			Your event (<a href="<?php echo $link; ?>"><?php echo $event->title; ?></a>) has been published.<br/>
		</p>
<?php
		include("footer.php");
		$result = ob_get_contents();
		ob_end_clean();
		return $result;
	}
?>