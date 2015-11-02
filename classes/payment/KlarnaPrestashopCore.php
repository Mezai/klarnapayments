<?php

if (!defined('KLARNA_DIRECTORY')) {
define('KLARNA_DIRECTORY', dirname(__FILE__) . '/../../');
}

require_once KLARNA_DIRECTORY . '/libs/transport/xmlrpc-3.0.0.beta/lib/xmlrpc.inc';
require_once KLARNA_DIRECTORY . '/libs/transport/xmlrpc-3.0.0.beta/lib/xmlrpc_wrappers.inc';

require_once KLARNA_DIRECTORY . '/libs/Klarna.php';

require_once '/includes/KlarnaCalculationHandler.php';

require_once '/includes/KlarnaOrderManagement.php';
require_once '/includes/KlarnaPclassesHandler.php';
require_once '/includes/KlarnaPrestaEncoding.php';
require_once '/includes/KlarnaPrestaValidation.php';

require_once '/includes/KlarnaAddressPrestashop.php';
require_once '/includes/KlarnaConfigHandler.php';
require_once '/includes/KlarnaInvoiceFee.php';
require_once '/includes/KlarnaLocalization.php';

require_once '/includes/KlarnaPrestaApi.php';


