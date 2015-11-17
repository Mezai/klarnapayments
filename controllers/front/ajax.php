<?php

class KlarnapaymentsajaxModuleFrontController extends ModuleFrontController {



public function initContent() {
      parent::initContent();
      $this->ajax = true; // enable ajax
      if($this->ajax) //special variable to check if the call is ajax
    	{
      $this->displayAjax(); // call your function here or what ever you wanna do
    	}
 }

public function displayAjax()
{
        if ($this->errors)
            die(Tools::jsonEncode(array('hasError' => true, 'errors' => $this->errors)));

        if(Tools::getValue('id_data') == 'klarna_payment_part_fixed')
        {
           	echo json_encode($this->context->smarty->fetch(_PS_MODULE_DIR_ .'/klarnapayments/views/templates/front/part_payment.tpl'));
        	die();
        }

        if (Tools::getValue('id_data') == '') {

        }

        if (Tools::getValue('id_data') == '') {

        }
}

}

