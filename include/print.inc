<?php
	function print_bill($bill) {
		global $g_i18n;

		$event = Event::get_from_id($bill->event_id);

		$bill_type = "invoice";
		if ($bill->type != DEVIS_TYPE_INVOICE) {
			$bill_type = "quotation";
		}
?>
<div class="evt_title"><p>{{<?php echo strtoupper($bill_type); ?>}}</p></div>
<p>
	<table width="100%">
		<tr valign="top">
			<td>
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
				{{Billing entity}}: <?php echo $bill->username; ?><br/>
				{{Billing address}}: <?php echo $bill->address; ?><br/>
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
	<b>{{<?php echo ucfirst($bill_type); ?> ID}}:</b> <?php echo $bill->label; ?><br/>
	<b>{{<?php echo ucfirst($bill_type); ?> date}}:</b> <?php echo format_date($bill->created_t, "timestamp"); ?><br/>
<?php
	if ($bill->type == DEVIS_TYPE_INVOICE) {
?>
	<b>{{Due date}}:</b> <?php echo format_date($bill->created_t, "timestamp"); ?><br/>
<?php
	}
?>
	<br/>

	<br/>
	<table border="1px" class="evt_table">
		<tr>
			<th>{{Event}}</th>
			<th>{{Rate name}}</th>
			<th>{{Amount}}</th>
			<th>{{Tax rate}}</th>
<?php
	if ($event->type == EVENT_TYPE_ANONYMOUS) {
?>
			<th>{{Quantity}}</th>
			<th>{{Total}}</th>
<?php
	}
?>
			<th>{{Total taxes}}</th>
			<th>{{Total due}}</th>
<?php
	if ($event->type == EVENT_TYPE_NOMINATIVE) {
?>
			<th>{{Title}}</th>
			<th>{{Firstname}}</th>
			<th>{{Lastname}}</th>
<?php
	}

	$i = 0;
	foreach ($bill->items as $item) {
?>
		<tr>
			<td><?php echo "(".$event->id.") "; ?><a href="?action=retrieve&amp;type=event&amp;id=<?php echo $event->id ?>"><?php echo $item->event_name; ?></a></td>
			<td><?php echo $item->event_rate_name; ?></td>
			<td class="evt_curr"><?php echo $item->event_rate_amount; ?>€</td>
			<td class="evt_curr"><?php echo $item->event_rate_tax; ?>%</td>
<?php
		if ($event->type == EVENT_TYPE_ANONYMOUS) {
?>
			<td class="evt_curr"><?php echo $item->quantity; ?></td>
			<td class="evt_curr"><?php echo $item->total_ht; ?>€</td>
<?php
		}
?>
			<td class="evt_curr"><?php echo $item->total_tax; ?>€</td>
			<td class="evt_curr"><?php echo $item->total_ttc; ?>€</td>
<?php
		if ($event->type == EVENT_TYPE_NOMINATIVE) {
?>
			<td><?php echo "&nbsp;".$item->attendee_title; ?></td>
			<td><?php echo $item->attendee_firstname; ?></td>
			<td><?php echo $item->attendee_lastname; ?></td>
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
	<br/>
	<br/>
	<?php require_once($g_i18n->filename(BASE_DIR."/etc/".$bill_type."_legal.html")); ?>
</p>
<?php
	}
?>