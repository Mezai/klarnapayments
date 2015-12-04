<?php

class KlarnaPaymentsCarrierModuleFrontController extends ModuleFrontController
{
	public $display_column_left = false;
	public $display_column_right = false;
	public $ssl = true;

	public function postProcess()
	{
		if (Tools::getIsset('delivery_option'))
		{
			if ($this->validateDeliveryOption(Tools::getValue('delivery_option')))
				$this->context->cart->setDeliveryOption(Tools::getValue('delivery_option'));
			if (!$this->context->cart->update())
				$this->context->smarty->assign('klarna_carrier_error', Tools::displayError('Could not save carrier selection'));
			
		}
		CartRule::autoRemoveFromCart($this->context);
		CartRule::autoAddToCart($this->context);

		Tools::redirect('order-opc');
	}

	public function validateDeliveryOption($delivery_option)
	{
		if (!is_array($delivery_option))
			return false;

		foreach ($delivery_option as $option) {
			if (!preg_match('/(\d+,)?\d+/', $option))
		
		return false;
		}
		return true;
	}

}