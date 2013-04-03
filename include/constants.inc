<?php
	define('SITE_ROOT', ".");
	define('SETTINGS_INI', "settings.ini");
	define('SKIN_DIR', "skin/default");

	define('CAPTCHA_PUB_KEY', '6LcxnN0SAAAAAJY7qJPcZl0eQHeEUo_N-8uOSEyo');
	define('CAPTCHA_PRIV_KEY', '6LcxnN0SAAAAANaQNIfxoIEErqdhkCTPDB7Dv746');

	define('COMPANY_NAME', 'Eventual');

	define('ALLOWED_TAGS', "<br><div><p><img><ul><ol><li><span><a><b><i>");

	define('AUTHORIZATION_DELAY', '29'); // days

	define('ROLE_USER', 0);
	define('ROLE_ADMIN', 1);

	define('ACTIVATION_STATUS_TO_BE_ACTIVATED', 0);
	define('ACTIVATION_STATUS_ACTIVATED', 1);
	define('ACTIVATION_STATUS_INACTIVATED', 3);

	define('EVENT_TYPE_ANONYMOUS', 0);
	define('EVENT_TYPE_NOMINATIVE', 1);
	define('EVENT_TYPE_FREE_NOMINATIVE', 2);

	define('DEVIS_STATUS_PLANNED', 0);
	define('DEVIS_STATUS_CONFIRMED', 1);
	define('DEVIS_STATUS_CANCELLED', 2);

	define("DEVIS_TYPE_AUTODETECT", -1);
	define("DEVIS_TYPE_QUOTATION", 0);
	define("DEVIS_TYPE_INVOICE", 1);

	define('EVENT_PUBLISH_FLAG_NO', 0);
	define('EVENT_PUBLISH_FLAG_YES', 1);

	define('EVENT_STATUS_PLANNED', 0);
	define('EVENT_STATUS_CONFIRMED', 1);
	define('EVENT_STATUS_CANCELLED', 2);
	define('EVENT_STATUS_INACTIVATED', 3);

	define('RANDOM_SALT', 'kii/[sf8243rwk#$f98|}{{}]\'');
?>