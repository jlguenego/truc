<?php
	include_once("include/tinyMCE.inc");
	
	$event = $g_display["event"];
	$rates = $g_display["rates"];
?>
<html>
	<head>
		<title>Edit <?php echo $event['title']; ?></title>
	</head>
	<script type="text/javascript" src="jscript/misc.js"></script>
	
	<a href="index.php">Go back to index</a><br/><br/>
	<form name="input" action="?action=update&amp;type=event" method="POST">
		<table>
		<tr>
			<td>Title: </td>
			<td><input type="text" name="title" value="<?php echo $event['title']; ?>"></td>
		</tr>
		<tr>
			<td>Number of person wanted: </td>
			<td><input type="text" name="persons" value="<?php echo $event['nbr_person_wanted']; ?>"></td>
		</tr>
		<tr>
			<td>Date (DD.MM.YY): </td>
			<td><input type="text" name="date" value="<?php echo date("d.m.y", $event['event_date']); ?>"></td>
		</tr>
		</table>
		<div id="rates">
		</div>
<?php
	$i = 0;
	foreach ($rates as $rate) {
		$label = $rate["label"];
		$amount = $rate["amount"];
		echo "<script language=\"javascript\" type=\"text/javascript\">";
		echo "addRate('rates', '$label', '$amount');";
		echo "</script>";
		$i++;
	}
	echo "<script language=\"javascript\" type=\"text/javascript\">";
	echo "setCounter($i);";
	echo "</script>";
?>
		<input type="button" value="Add a rate" onClick="addRate('rates');">
		<textarea name="content">
			<?php echo $event['content']; ?>
		</textarea>
		<input type="hidden" name="id" value="<?php echo $event['id']; ?>"/>
		<input type="submit" value="Submit"/>
	</form>
</html>