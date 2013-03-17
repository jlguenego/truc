<p>
	Dear <?php echo $_GET["username"]; ?>.<br/>
	We send you this mail to inform you that your payment for the event
	<?php echo $_GET["event_name"]; ?> has been authorized.<br/>
	Here are the information about your purchase: <br/>
	<br/>
	<?php echo $_GET["label"]; ?>:
	<table border="1px">
		<tr>
			<th>Rate name</th>
			<th>Amount HT</th>
			<th>Rate tax</th>
<?php
	if ($_GET['nominative'] == 0) {
?>
			<th>Quantity</th>
			<th>Total ticket HT</th>
<?php
	}
?>
			<th>Total ticket tax</th>
			<th>Total ticket TTC</th>
<?php
	if ($_GET['nominative'] == 1) {
?>
			<th>Title</th>
			<th>Firsname</th>
			<th>Lastname</th>
<?php
	}

	$i = 0;
	while (isset($_GET["item_${i}"])) {
		preg_match_all("|([\w. ]+)=([\w. ]*)|", $_GET["item_${i}"], $pairs);
		$item = array_combine($pairs[1], $pairs[2]);
?>
		<tr>
			<td><?php echo $item["event_rate_name"]; ?></td>
			<td><?php echo $item["event_rate_amount"]; ?>€</td>
			<td><?php echo $item["event_rate_tax"]; ?>%</td>
<?php
		if ($_GET['nominative'] == 0) {
?>
			<td><?php echo $item["quantity"]; ?></td>
			<td><?php echo $item["total_ht"]; ?>€</td>
<?php
		}
?>
			<td><?php echo $item["total_tax"]; ?>€</td>
			<td><?php echo $item["total_ttc"]; ?>€</td>
<?php
		if ($_GET['nominative'] == 1) {
?>
			<td><?php echo $item["participant_title"]; ?></td>
			<td><?php echo $item["participant_firstname"]; ?></td>
			<td><?php echo $item["participant_lastname"]; ?></td>
<?php
		}
?>
		</tr>
<?php
		$i++;
	}
?>
	</table>
	<br/>
	<table border="1px">
		<tr>
			<th>Total HT</th>
			<td><?php echo $_GET["total_ht"]; ?>€</td>
		</tr>
		<tr>
			<th>Total tax</th>
			<td><?php echo $_GET["total_tax"]; ?>€</td>
		</tr>
		<tr>
			<th>Total Due</th>
			<td><b><?php echo $_GET["total_ttc"]; ?>€</b></td>
		</tr>
	</table>
	<br/>
	Please make sure that the amount of <b><?php echo $_GET["total_ttc"]; ?>€</b> will be avalable for at least 30 days.<br/>
</p>