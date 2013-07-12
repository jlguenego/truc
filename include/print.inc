<?php
	function print_bill($bill, $context = PRINT_BILL_CONTEXT_DEFAULT, $format = PRINT_BILL_FORMAT_HTML) {
		global $g_i18n;

		$event = Event::get_from_id($bill->event_id);
		$billing_address = Address::get_from_id($bill->client_address_id);

		$bill_type = "invoice";
		if ($bill->type != BILL_TYPE_INVOICE) {
			$bill_type = "quotation";
		}
		$biller_billing_address = Address::get_from_id($bill->biller_address_id);
		if (!($context & PRINT_BILL_CONTEXT_CONFIRMATION)) {
?>
<div class="evt_title"><p>{{<?php echo strtoupper($bill_type); ?>}}</p></div>
<br/>
<?php
		}
		if ($format == PRINT_BILL_FORMAT_PDF) {
?>
<style>
.evt_title {
	width: 100%;
	text-align: center;
}

.evt_stakeholder {
	width: 100%;
	font-size: 10px;
	border: 1px solid black;
}

.evt_invoice_info {
	font-size: 10px;
}

.evt_curr {
	text-align: right;
}

table {
	border-collapse: collapse;
}

th, td {
	padding: 3px 5px;
}

.evt_table {
	margin: 100px 0px;
}
</style>

<?php
		}
?>
<table class="evt_stakeholder" style="width:100%;">
	<tr valign="top">
		<td style="width:50%;">
			<b>{{Provider identification}}</b><br/>
			<br/>
			<?php echo $bill->biller_name ; ?><br/>
			<?php echo nl2br($biller_billing_address->address); ?><br/>
		<?php
			if (!is_null_or_empty($bill->biller_vat)) {
		?>
			{{VAT#}}: <?php echo $bill->biller_vat; ?>
		<?php
			}
		?>
		</td>
		<td style="width:50%;" align="right">
			<b>{{Client identification}}</b><br/>
			<br/>
			<?php echo $bill->client_name; ?><br/>
			<?php echo nl2br($billing_address->address); ?><br/>
		<?php
			if ($bill->is_for_company()) {
		?>
			{{VAT#}}: <?php echo $bill->client_vat; ?>
		<?php
			}
		?>
		</td>
	</tr>
</table>
<br/>
<?php
	if (!($context & PRINT_BILL_CONTEXT_CONFIRMATION)) {
?>
<div class="evt_invoice_info">
	<b>{{<?php echo ucfirst($bill_type); ?> ID}}:</b> <?php echo $bill->label; ?><br/>
	<b>{{<?php echo ucfirst($bill_type); ?> date}}:</b> <?php echo format_date($bill->created_t, "timestamp"); ?><br/>
<?php
		if ($bill->type == BILL_TYPE_INVOICE) {
?>
	<b>{{Due date}}:</b> <?php echo format_date($bill->created_t, "timestamp"); ?><br/>
<?php
		}
?>
</div>
<br/>
<?php
	}
?>
	<table border="1px" class="evt_table" align="center">
		<tr>
			<th>{{Description}}</th>
			<th>{{Quantity}}</th>
			<th>{{Total (taxes excluded)}}</th>
			<th>{{Tax rate}}</th>
			<th>{{Total taxes}}</th>
			<th>{{Total}}</th>
		</tr>
<?php
	$i = 0;
	foreach ($bill->items as $item) {
?>
		<tr>
			<td><?php echo $item->get_description(); ?></td>
			<td align="center"><?php echo $item->quantity; ?></td>
			<td class="evt_curr"><?php echo curr($item->total_ht); ?>€</td>
			<td class="evt_curr">
<?php
	if ($item->tax_rate != null) {
		echo curr($item->tax_rate).'%';
	} else {
		echo _t('N/A');
	}
?>
			</td>
			<td class="evt_curr"><?php echo curr($item->total_tax); ?>€</td>
			<td class="evt_curr"><?php echo curr($item->total_ttc); ?>€</td>
		</tr>
<?php
		$i++;
	}
?>
</table>
<br/>
<table border="1px" class="evt_table">
	<tr>
		<th class="th_left">{{Total (Tax excluded)}}</th>
		<td class="evt_curr"><?php echo curr($bill->total_ht); ?>€</td>
	</tr>
	<tr>
		<th class="th_left">{{Total taxes}}</th>
		<td class="evt_curr"><?php echo curr($bill->total_tax); ?>€</td>
	</tr>
	<tr>
		<th class="th_left">{{Total due}}</th>
		<td class="evt_curr"><b><?php echo curr($bill->total_ttc); ?>€</b></td>
	</tr>
</table>
<?php
		if (!($context & PRINT_BILL_CONTEXT_CONFIRMATION)) {
			if($format == PRINT_BILL_FORMAT_PDF) {
?>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<?php
			}
?>
<br/>
<div class="evt_invoice_info">
[[/etc/<?php echo $bill_type; ?>_legal.html]]
</div>
<?php
		}
?>
<?php
	}
?>