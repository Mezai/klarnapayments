<?php


include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/klarnapayments.php');
if (!defined('_PS_VERSION_'))
	exit;
/* Check that the Stripe's module is active and that we have the token */
$stripe = new KlarnaPayments();
$context = Context::getContext();
if (!KlarnaEncoding::checkPNO(Tools::getValue('klarna_pno'), $enc)) {
      $this->context->cookie->__set("klarnapayments_error", 'There was a problem with your payment');
      $controller = Configuration::get('PS_ORDER_PROCESS_TYPE') ? 'order-opc.php' : 'order.php';
      $location = $this->context->link->getModuleLink('klarnapayments', 'error');
      Tools::redirect($error_url);
}