<?php
	define('SITE_ROOT', ".");
	define('SETTINGS_INI', "settings.ini");
	define('SKIN_DIR', "skin/default");

	define('COMPANY_NAME', 'Eventual');

	define('ALLOWED_TAGS', "<br><div><p><img><ul><ol><li><span><a><b><i><strong>");

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

	define('EVENT_FLAG_READY_FOR_PUBLICATION', 1);

	define('RANDOM_SALT', 'kii/[sf8243rwk#$f98|}{{}]\'');
?>