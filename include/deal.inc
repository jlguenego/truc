<?php
	function deal_generate_report($event) {
		$deal_name = $event->deal_name;

		$result = array();
		switch($deal_name) {
			case "deal_050_5":
				$result = deal_050_5($event);
				break;
			default:
				throw new Exception("Unknown deal_name: " . $deal_name);
				break;
		}
		return $result;
	}

	function deal_050_5($event) {
		$invoices = $event->get_invoices();
		$result = array();
		$result["eb_fee"] = 0;
		$result["paypal_fee"] = 0;
		$result["real_eb_fee"] = 0;
		$result["total"] = 0;
		$result["total_ticket"] = 0;
		$result["eb_total"] = 0;
		$result["organizer_revenue"] = 0;
		$result["ticket_quantity"] = 0;
		$result['invoices'] = array();

		$fix = 0;
		foreach ($invoices as $invoice) {
			if (!$invoice->is_really_paid()) {
				continue;
			}
			$invoice_a = array();
			$invoice_a['total'] = $invoice->total_ttc;
			$invoice_a['paypal_fee'] = round($invoice->total_ttc * 0.034, 2) + 0.25;
			$invoice_a['eb_total'] = $invoice_a['total'] - $invoice_a['paypal_fee'];
			$invoice_a['tickets'] = array();
			foreach ($invoice->get_items() as $item) {
				$ticket = array();
				if (!is_null_or_empty($item->event_rate_name)) {
					$ticket['name'] = $item->event_rate_name;
				} else {
					$ticket['name'] = $item->description;
				}
				$ticket['quantity'] = $item->quantity;
				$ticket['total_ttc'] = $item->total_ttc;
				$invoice_a['tickets'][] = $ticket;
				if ($item->class == "/item/ticket") {
					$result["ticket_quantity"] += $item->quantity;
					$result["total_ticket"] += $item->total_ttc;
				}
			}
			$result["total"] += $invoice->total_ttc;
			$result['invoices'][] = $invoice_a;

			$paypal_fee = round($invoice->total_ttc * 0.034, 2) + 0.25;
			$result["paypal_fee"] += $paypal_fee;
		}
		$var = $result["total_ticket"] * 0.05; // 5%
		$result["eb_fee"] = (0.50 * $result["ticket_quantity"]) + $var; // +0.50€
		$result["organizer_revenue"] = $result["total"] - $result["eb_fee"];
		$result["real_eb_fee"] = $result["eb_fee"] - $result["paypal_fee"];
		$result["eb_total"] = $result["total"] - $result["paypal_fee"];

		return $result;
	}

	function deal_get_description($deal_name) {
		$result = "";
		switch($deal_name) {
			case "deal_050_5":
				$result = _t('0.50€/ticket + 5% of total sales');
				break;
			default:
				throw new Exception("Unknown deal_name: " . $deal_name);
				break;
		}
		return $result;
	}
?>