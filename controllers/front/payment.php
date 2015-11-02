<?php

class KlarnapaymentsPaymentModuleFrontController extends ModuleFrontController
{
  public function initContent()
  {

    require_once(dirname(__FILE__).'/../../libs/Klarna.php');
    require_once(dirname(__FILE__).'/../../libs/transport/xmlrpc-3.0.0.beta/lib/xmlrpc.inc');
		require_once(dirname(__FILE__).'/../../libs/transport/xmlrpc-3.0.0.beta/lib/xmlrpc_wrappers.inc');
    require_once(dirname(__FILE__).'/../../classes/KlarnaConfigHandler.php');

    if ($cart->id_customer == 0 || $cart->id_address_delivery == 0 || $cart->id_address_invoice == 0 || !$this->module->active)
			Tools::redirect('index.php?controller=order&step=1');

      if (!Validate::isLoadedObject($customer))
  			Tools::redirect('index.php?controller=order&step=1');

      $cart = $this->context->cart;
      $currency = new Currency((int)$cart->id_currency);
  		$address = new Address((int)$cart->id_address_invoice);
  		$carrier = new Carrier((int)$cart->id_carrier);
  		$amount = $cart->getOrderTotal(true, Cart::BOTH);
  		$goods = $cart->getOrderTotal(true, Cart::ONLY_PRODUCTS);
  		$shipping = $cart->getOrderTotal(true, Cart::ONLY_SHIPPING);

      $error_url = Context::getContext()->link->getModuleLink('klarnapayments', 'error');

      $payment_type = Tools::getValue('select_klarna_method');


        $k = KlarnaConfigHandler::setConfigurationByLocale(Country::getIsoById($this->context->country->id), $this->module->settings);

        var_dump($k);
        exit;
    }

}
