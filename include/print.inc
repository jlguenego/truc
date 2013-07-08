<?php
	function print_bill($bill, $context = PRINT_BILL_CONTEXT_DEFAULT) {
		global $g_i18n;

		$event = Event::get_from_id($bill->event_id);
		$billing_address = Address::get_from_id($bill->address_id);

		$bill_type = "invoice";
		if ($bill->type != BILL_TYPE_INVOICE) {
			$bill_type = "quotation";
		}
		if (!($context & PRINT_BILL_CONTEXT_CONFIRMATION)) {
?>
<div class="evt_title"><p>{{<?php echo strtoupper($bill_type); ?>}}</p></div>
<?php
		}
?>
<p>
	<table width="100%">
		<tr valign="top">
			<td width="50%">
				<b>{{Provider identification}}</b><br/>
				<br/>
				JLG-Consulting - Jean-Louis GUENEGO - Entreprise Individuelle<br/>
				2 allée du commandant Charcot <br/>
				77200 Torcy, France<br/>
				{{VAT identification number}}: FR 61 439 317 058<br/>
				SIRET: 439 317 058 00022<br/>
			</td>
			<td align="right">
				<b>{{Client identification}}</b><br/>
				<br/>
				<?php echo $bill->username; ?><br/>
				<?php echo nl2br($billing_address->address); ?><br/>
			<?php
				if ($bill->is_for_company()) {
			?>
				{{VAT identification number}}: <?php echo $bill->vat; ?>
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
	<b>{{<?php echo ucfirst($bill_type); ?> ID}}:</b> <?php echo $bill->label; ?><br/>
	<b>{{<?php echo ucfirst($bill_type); ?> date}}:</b> <?php echo format_date($bill->created_t, "timestamp"); ?><br/>
<?php
		if ($bill->type == BILL_TYPE_INVOICE) {
?>
	<b>{{Due date}}:</b> <?php echo format_date($bill->created_t, "timestamp"); ?><br/>
<?php
		}
	}
?>
	<table border="1px" class="evt_table">
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
			<td class="evt_curr"><?php echo $item->total_ht; ?>€</td>
			<td class="evt_curr"><?php echo $item->tax_rate; ?>%</td>
			<td class="evt_curr"><?php echo $item->total_tax; ?>€</td>
			<td class="evt_curr"><?php echo $item->total_ttc; ?>€</td>
		</tr>
<?php
		$i++;
	}
?>
	</table>
	<table border="1px" class="evt_table">
		<tr>
			<th class="th_left">{{Total (Tax excluded)}}</th>
			<td class="evt_curr"><?php echo $bill->total_ht; ?>€</td>
		</tr>
		<tr>
			<th class="th_left">{{Total taxes}}</th>
			<td class="evt_curr"><?php echo $bill->total_tax; ?>€</td>
		</tr>
		<tr>
			<th class="th_left">{{Total due}}</th>
			<td class="evt_curr"><b><?php echo $bill->total_ttc; ?>€</b></td>
		</tr>
	</table>
<?php
		if (!($context & PRINT_BILL_CONTEXT_CONFIRMATION)) {
			require_once($g_i18n->filename(BASE_DIR."/etc/".$bill_type."_legal.html"));
		}
?>
</p>
<?php
	}
?>