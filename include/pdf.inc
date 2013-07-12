<?php
	require_once(BASE_DIR.'/_ext/html2pdf_v4.03/html2pdf.class.php');

	function pdf_display($bill) {
		global $g_i18n;

		$filename = INVOICE_DIR.'/'.$bill->label.'_'.$g_i18n->locale.'.pdf';

		if (!file_exists($filename)) {
			pdf_create($bill);
		}

		if (!file_exists($filename)) {
			throw new Exception('PDF file does not exist.');
		}
		$content = file_get_contents($filename);
		header('Content-Type: application/pdf');
		echo $content;
		exit;
	}

	function pdf_create($bill) {
		global $g_i18n;

		$filename = INVOICE_DIR.'/'.$bill->label.'_'.$g_i18n->locale.'.pdf';

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
			$html2pdf->Output($filename, 'F');
		} catch(HTML2PDF_exception $e) {
			echo '<pre>';
			print_r($e);
			echo '</pre>';
		}
	}
?>