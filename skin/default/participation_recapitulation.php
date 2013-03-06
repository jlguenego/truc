<a href="index.php">Go back to index</a><br/><br/>
<?php
	global $g_display;
	$purchases = $g_display["purchase"];
?>
<table>
	<tr>
		<td>Categorie</td>
		<td>Rate</td>
		<td>Quantity</td>
	</tr>
<?php
	foreach ($purchases as $purchase) {
		$label = $purchase["label"];
		$rate = $purchase["rate"];
		$quantity = $purchase["quantity"];
?>
	<tr>
		<td><?php echo $label; ?></td>
		<td><?php echo $rate; ?></td>
		<td><?php echo $quantity; ?></td>
	</tr>
<?php
	}
?>
</table>

<?php
	paypal_button();
?>