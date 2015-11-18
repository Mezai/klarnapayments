<?php

class KlarnaPaymentsPaymentModuleFrontController extends ModuleFrontController
{

  public function __construct()
  {
    parent::__construct();
    $this->context = Context::getContext();
    require_once(dirname(__FILE__).'/../../klarnapayments.php');
  }
  public function initContent()
  {
     

     $this->display_column_left = false;
     $this->display_column_right = false;
     parent::initContent();
     $klarna_locale = KlarnaEncoding::get(Country::getIsoById($this->context->country->id));
     $error_url = $this->context->link->getModuleLink('klarnapayments', 'error');
     $enc = KlarnaEncoding::getRegexp($klarna_locale);
    if (!preg_match($enc, Tools::getValue('klarna_pno'))) {
      $location = $this->context->link->getModuleLink('klarnapayments', 'error');
      Tools::redirect($location);
      
      } else {
        
        $this->validation();

    }
  }



  public function validation()
  {

      $cart = $this->context->cart;
      $currency = new Currency((int)$cart->id_currency);

      $klarna = new KlarnaPrestaConfig();
      $country_iso = Country::getIsoById($this->context->country->id);

      $klarna->setKlarnaConfig($country_iso, true);

      $buildgoods_list = new KlarnaGoodsList();
      $buildgoods_list->buildGoodsList($cart, $klarna);

     

      $address = new Address(intval($cart->id_address_delivery));
      $customer = new Customer($cart->id_customer);
      $address_klarna = KlarnaAdressPresta::buildKlarnaAddr($address, $customer);
      $klarna->klarna->setAddress(KlarnaFlags::IS_BILLING, $address_klarna);
      $klarna->klarna->setAddress(KlarnaFlags::IS_SHIPPING, $address_klarna);


      try {

      $pclassid = (int)Tools::getValue('klarna_payment_type');
      if (!Tools::getIsset('klarna_payment_gender')) {
        $gender = null;
      } elseif (Tools::getIsset('klarna_payment_gender')) {
        $gender = ((int)Tools::getValue('klarna_payment_gender') === 1) ? KlarnaFlags::MALE : KlarnaFlags::FEMALE;
      }
      $result = $klarna->klarna->reserveAmount(
        (String)Tools::getValue('klarna_pno'), 
        $gender, 
        -1,   // Automatically calculate and reserve the cart total amount
        KlarnaFlags::NO_FLAG,
        $pclassid
        );

      $reservation_number = $result[0];
      $klarna_order_status = $result[1];
      $type = (Tools::getValue('klarna_payment_type') === -1) ? 'Invoice' : 'Part payment';
      if ((int)$klarna_order_status == 1)
      {
        $status = 'OK';

      $this->module->validateOrder($cart->id, Configuration::get('KLARNA_OS_AUTHORIZED'), $amount, $this->module->displayName, 'Status:'.
          $status.'; Reservation id:'.$reservation_number.'; Type:'.$type, array(), (int)$currency->id, false, $customer->secure_key);
          Db::getInstance()->Execute('
          INSERT INTO `'._DB_PREFIX_.'klarna_orders` (`id_order`, `id_reservation`, `customer_firstname`, `customer_lastname`, `payment_status`, `customer_country`)
          VALUES ('.(int)$this->module->currentOrder.', \''.pSQL($reservation_number).'\', \''.pSQL($address->firstname).'\', \''.pSQL($address->lastname).'\', \''.pSQL($status).'\', \''.pSQL($country_iso).'\')');
          Tools::redirect('index.php?controller=order-confirmation&id_cart='.
              $cart->id.'&id_module='.$this->module->id.'&id_order='.$this->module->currentOrder.'&key='.$customer->secure_key);
       
       } elseif ((int)$klarna_order_status == 2) {

        $status = 'Pending';

        $this->module->validateOrder($cart->id, Configuration::get('KLARNA_OS_PENDING'), $amount, $this->module->displayName, 'Status:'.
          $status.'; Reservation id:'.$reservation_number.'; Type:'.$type, array(), (int)$currency->id, false, $customer->secure_key);
        Db::getInstance()->Execute('
          INSERT INTO `'._DB_PREFIX_.'klarna_orders` (`id_order`, `id_reservation`, `customer_firstname`, `customer_lastname`, `payment_status`, `customer_country`)
          VALUES ('.(int)$this->module->currentOrder.', \''.pSQL($reservation_number).'\', \''.pSQL($address->firstname).'\', \''.pSQL($address->lastname).'\', \''.pSQL($status).'\', \''.pSQL($country_iso).'\')');
        Tools::redirect('index.php?controller=order-confirmation&id_cart='.
          $cart->id.'&id_module='.$this->module->id.'&id_order='.$this->module->currentOrder.'&key='.$customer->secure_key);


       }  
    } catch (Exception $e) {
      Logger::addLog('Klarna module: transaction failed with message: '.$e->getMessage().' and code :'.$e->getCode());
      Tools::redirect($this->context->link->getModuleLink('klarnapayments', 'error'));

    }


  }

}
