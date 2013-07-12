<?php
	require_once(BASE_DIR.'/_ext/html2pdf_v4.03/html2pdf.class.php');

	function pdf_display($bill) {
		global $g_i18n;

		ob_start();
		print_bill($bill, PRINT_BILL_CONTEXT_DEFAULT, PRINT_BILL_FORMAT_PDF);
		$content = ob_get_clean();
		$content = $g_i18n->parse($content);

		try
		{
			$html2pdf = new HTML2PDF('P', 'A4', 'fr');
			//$html2pdf->setModeDebug();
			$html2pdf->setDefaultFont('Arial');
			$html2pdf->writeHTML($content, false);
			$html2pdf->Output(BASE_DIR.'/archives/'.$bill->label.'.pdf');
		}
		catch(HTML2PDF_exception $e) {
			echo $e;
		}
		exit;
	}
?>