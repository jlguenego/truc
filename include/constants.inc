<?php
	define('SITE_ROOT', ".");
	define('SETTINGS_INI', "_settings.ini");
	define('SKIN_DIR', "skin/default");

	define('COMPANY_NAME', 'Event-Biller');

	define('ALLOWED_TAGS', "<br><div><p><img><ul><ol><li><span><a><b><i><strong><table><tr><th><td>");

	define('AUTHORIZATION_DELAY', '29'); // days

	define('ROLE_USER', 0);
	define('ROLE_ADMIN', 1);
	define('ROLE_BILLER', 2);

	define('ACTIVATION_STATUS_TO_BE_ACTIVATED', 0);
	define('ACTIVATION_STATUS_ACTIVATED', 1);
	define('ACTIVATION_STATUS_INACTIVATED', 3);

	define('EVENT_TYPE_ANONYMOUS', 0);
	define('EVENT_TYPE_NOMINATIVE', 1);
	define('EVENT_TYPE_FREE_NOMINATIVE', 2);

	define('BILL_STATUS_PLANNED', 0);
	define('BILL_STATUS_CONFIRMED', 1);
	define('BILL_STATUS_CANCELLED', 2);

	define("BILL_TYPE_AUTODETECT", -1);
	define("BILL_TYPE_QUOTATION", 0);
	define("BILL_TYPE_INVOICE", 1);

	define('BILL_TARGET_ATTENDEE', 0);
	define('BILL_TARGET_ORGANIZER', 1);

	define('EVENT_PUBLISH_FLAG_NO', 0);
	define('EVENT_PUBLISH_FLAG_YES', 1);

	define('EVENT_STATUS_PLANNED', 0);
	define('EVENT_STATUS_CONFIRMED', 1);
	define('EVENT_STATUS_CANCELLED', 2);
	define('EVENT_STATUS_INACTIVATED', 3);

	define('EVENT_FLAG_READY_FOR_PUBLICATION', 1);
	define('EVENT_FLAG_FACEBOOK_EVENT_CREATED', 2);
	define('EVENT_FLAG_INVOICE_ALREADY_CREATED', 4);

	define('RANDOM_SALT', 'kii/[sf8243rwk#$f98|}{{}]\'');

	define('TASK_RUNNING_DURATION', 2);
	define('TASK_STATUS_PENDING', 0);
	define('TASK_STATUS_RUNNING', 1);
	define('TASK_STATUS_SUCCESS', 2);
	define('TASK_STATUS_ERROR', 3);

	define('ADVERTISEMENT_STATUS_DEFAULT', 0);
	define('ADVERTISEMENT_STATUS_SENT', 1);

	define('TEMP_DIR', '_tmp');
	define('SYNC_LOCK_FILE', TEMP_DIR.'/_evt_lock.txt');


	define('INSTRUCTION_SEPARATOR', "\n---INSTRUCTION_SEPARATOR---\n");

	define('BITCOIN_FILENAME', '_tmp/bitcoin_curr.txt');

	define('QRCODE_CACHE_DIR', '_tmp/qrcode');

	define('PRINT_BILL_CONTEXT_DEFAULT', 0);
	define('PRINT_BILL_CONTEXT_CONFIRMATION', 1);
	define('PRINT_BILL_FORMAT_HTML', 0);
	define('PRINT_BILL_FORMAT_PDF', 1);

	define('DISCOUNT_CLASS_FIXED', '/discount/fixed');
	define('DISCOUNT_CLASS_PERCENTAGE', '/discount/percentage');
?>