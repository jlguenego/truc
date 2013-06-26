<?php
	define("BASE_DIR", dirname(dirname(__FILE__)));

	require_once(BASE_DIR . "/include/constants.inc");
	require_once(BASE_DIR . "/include/globals.inc");
	require_once(BASE_DIR . "/include/misc.inc");
	require_once(BASE_DIR . "/include/deal.inc");

	$event = Event::get_from_id(100022);
	$report = deal_generate_report($event);
?>
<html>
	<head>
		<meta charset="utf-8"/>
<style>
	table {
		border-collapse: collapse;
		border: 1px solid black;
	}

	td, th {
		padding: 5px;
		border: 1px solid black;
	}
</style>
	</head>
	<body>
		<table>
			<tr>
				<th>Ticket name</th>
				<th>Ticket price</th>
				<th>Total invoice</th>
				<th>Paypal fee</th>
				<th>EB total invoice</th>
			</tr>
<?php
	foreach ($report['invoices'] as $invoice) {
		$rowspan = count($invoice['tickets']);
		$first = true;
?>
			<tr>
<?php
		foreach ($invoice['tickets'] as $ticket) {
?>
				<td><?php echo $ticket['name']; ?></td>
				<td><?php echo $ticket['total_ttc']; ?></td>
<?php
			if ($first) {
				$first = false;
?>
				<td rowspan="<?php echo $rowspan; ?>"><?php echo $invoice['total']; ?></td>
				<td rowspan="<?php echo $rowspan; ?>"><?php echo $invoice['paypal_fee']; ?></td>
				<td rowspan="<?php echo $rowspan; ?>"><?php echo $invoice['eb_total']; ?></td>
<?php
			}
?>
			</tr>
<?php
		}
	}
?>
		</table>
		<table>
			<tr>
				<th>Total</th>
				<td><?php echo $report['total'] ; ?></td>
			</tr>
			<tr>
				<th>EB fees</th>
				<td><?php echo $report['eb_fee'] ; ?></td>
			</tr>
			<tr>
				<th>Paypal fees</th>
				<td><?php echo $report['paypal_fee'] ; ?></td>
			</tr>
			<tr>
				<th>Real EB fees</th>
				<td><?php echo $report['real_eb_fee'] ; ?></td>
			</tr>
			<tr>
				<th>EB total</th>
				<td><?php echo $report['eb_total'] ; ?></td>
			</tr>
			<tr>
				<th>Organizer revenue</th>
				<td><?php echo $report['organizer_revenue'] ; ?></td>
			</tr>
		</table>
	</body>
</html>