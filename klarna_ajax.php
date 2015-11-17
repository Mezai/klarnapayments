<?php


include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');



$klarna_module = Module::getInstanceByName('klarnapayments');

if(Tools::getValue('id_data') == 'klarna_payment_part_flexible')
{
   	$type = Tools::getValue('id_data');
   	echo $klarna_module->showPaymentPart($type);
  	
} elseif (Tools::getValue('id_data') == 'klarna_payment_part_fixed_1' ||
	Tools::getValue('id_data') == 'klarna_payment_part_fixed_2' || 
	Tools::getValue('id_data') == 'klarna_payment_part_fixed_3') {
	
	$type = Tools::getValue('id_data');

	echo $klarna_module->showPaymentPart($type);

} elseif (Tools::getValue('id_data') == 'klarna_payment_invoice' || Tools::getValue('id_data') == 'klarna_payment_invoice_payinx') {
	
	$type = Tools::getValue('id_data');

	echo $klarna_module->showPaymentPart($type);
}

  

