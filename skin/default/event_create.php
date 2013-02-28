<?php
	include_once("include/tinyMCE.inc");
	
	$test_content = <<<EOF
<h1 style="text-align: center;"><em><span style="color: #ff6600; text-decoration: underline;">My Event</span></em></h1>
<hr />
<ul>
<li>
<h2><span style="font-size: small; color: #ff9900;">Description</span></h2>
</li>
</ul>
<p><span style="font-size: small;">Here is the description of my event.</span></p>
<p><span style="font-size: small;"><span style="font-family: arial,helvetica,sans-serif;">I can</span> <span style="font-family: trebuchet ms,geneva;">write</span> <span style="font-family: courier new,courier;">with</span> <span style="font-family: impact,chicago;">differents</span> <span style="font-family: wingdings,zapf dingbats;">fonts<span style="font-family: times new roman,times; color: #00ffff;"> and c<span style="color: #800080;">olo</span>r<span style="color: #ff0000;">s</span></span></span>. <span style="background-color: #ffff00;">And <span style="background-color: #ff0000;">eve</span><span style="background-color: #3366ff;">n do</span><span style="background-color: #00ff00;"> this.</span></span></span></p>
<p>&nbsp;</p>
<hr />
<ul>
<li>
<h2><span style="font-size: small; color: #ff9900;">Rates</span></h2>
</li>
</ul>
<table style="width: 291px; height: 94px;" border="2" cellpadding="2">
<tbody>
<tr>
<td style="background-color: #8d8572;"><span style="font-size: small;"><strong>Members</strong></span></td>
<td><span style="font-size: small;">120$</span></td>
</tr>
<tr>
<td style="background-color: #758a7d;"><span style="font-size: small;"><strong>Non members</strong></span></td>
<td><span style="font-size: small;">150$</span></td>
</tr>
</tbody>
</table>
<p>&nbsp;</p>
<hr />
<ul>
<li>
<h2><span style="color: #ff9900;">Links</span></h2>
</li>
</ul>
<p><span style="color: #000000; font-size: small;">And a link to my event's web site: <a href="google.com">My Event</a><br /></span></p>
EOF;
?>
<script type="text/javascript" src="jscript/misc.js"></script>
<a href="index.php">Go back to index</a><br/><br/>
<span style="color:red;">
	<?php echo "$g_error_msg<br/>"; ?>
</span>
<form name="input" action="?action=create&amp;type=event" method="POST">
	<table>
	<tr>
		<td>Title: </td>
		<td><input type="text" name="title" value="<?php echo_default_value("title"); ?>"/></td>
	</tr>
	<tr>
		<td>Number of person wanted: </td>
		<td><input type="text" name="persons" value="<?php echo_default_value("persons"); ?>"/></td>
	</tr>
	<tr>
		<td>Date (DD.MM.YY): </td>
		<td><input type="text" name="date" value="<?php echo_default_value("date"); ?>"></td>
	</tr>
	<tr>
		<td>Deadline (DD.MM.YY): </td>
		<td><input type="text" name="deadline" value="<?php echo_default_value("deadline"); ?>"></td>
	</tr>
	</table>
	<br/><br/>
	<div id="rates">
	</div>
<?php
	if (isset($_GET["rates"])) {
		$i = 0;
		foreach ($_GET["rates"] as $rate) {
			$label = $_GET["labels"][$i];
			$amount = $rate;
			echo "<script language=\"javascript\" type=\"text/javascript\">";
			echo "addRate('rates', '$label', '$amount');";
			echo "</script>";
			$i++;
		}
		echo "<script language=\"javascript\" type=\"text/javascript\">";
		echo "setCounter($i);";
		echo "</script>";
	} else {
		echo "<script language=\"javascript\" type=\"text/javascript\">";
		echo "addRate('rates', '', '');";
		echo "</script>";
	}
?>
	<input type="button" value="Add a rate" onClick="addRate('rates');"/>
	<textarea name="content">
		<?php echo $test_content; ?>
	</textarea>
	<input type="submit" value="Submit"/>
</form>