<?php
	define("BASE_DIR", dirname(dirname(__FILE__)));

	require_once(BASE_DIR . "/include/constants.inc");
	require_once(BASE_DIR . "/include/globals.inc");
	require_once(BASE_DIR . "/include/misc.inc");
	require_once(BASE_DIR . "/include/print.inc");
	require_once(BASE_DIR . "/include/layout.inc");
	require_once(BASE_DIR.'/_ext/html2pdf_v4.03/html2pdf.class.php');

	$g_i18n = new I18n();
	$g_i18n->init();

	ob_start();
	layout_i18n(BASE_DIR.'/test/bill2pdf_content.php');
	$content = ob_get_clean();

    try
    {
        $html2pdf = new HTML2PDF('P', 'A4', 'fr');
//      $html2pdf->setModeDebug();
        $html2pdf->setDefaultFont('Arial');
        $html2pdf->writeHTML($content, false);
        $html2pdf->Output(BASE_DIR.'/test/pdf/test.pdf');
    }
    catch(HTML2PDF_exception $e) {
        echo $e;
        exit;
    }
?>