<?php
	$bill = $g_display["bill"];
?>
<div style="position: absolute; top: 30px; left: 50px;">
	<a href="<?php echo $bill->url_pdf();?>">Version PDF</a>
</div>
<?php print_bill($bill); ?>