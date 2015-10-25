<?php
/**
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2015 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class KlarnaPaymentsPaymentPartModuleFrontController extends ModuleFrontController
{
	public $ssl = true;
	public $display_column_left = false;
	public $display_column_right = false;

	public function initContent()
	{
		$this->display_column_left = false;
		$this->display_column_right = false;

		parent::initContent();

		$cart = $this->context->cart;

		if (!$this->module->checkCurrency($cart))
				Tools::redirect('index.php?controller=order');

		if ((int)Configuration::get('klarna_part_payment') == 0)
				Tools::redirect('index.php?controller=order');

		$klarna_account_pclass = $this->module->getKlarnaPClasses('ACCOUNT');

		if (!empty($klarna_account_pclass))
		{

			foreach ($klarna_account_pclass as $value)
			{
			$this->context->smarty->assign(array(
				'klarna_account_id' => $value->getId(),
				'klarna_account_description' => $value->getDescription(),
				'klarna_account_interest' => $value->getInterestRate(),
				'klarna_account_invfee' => $value->getInvoiceFee(),
				'klarna_account_startfee' => $value->getStartFee(),
				'klarna_account_months' => $value->getMonths(),
				'klarna_account_minamount' => $value->getMinAmount(),
				'klarna_account_country' => $value->getCountry(),
				'klarna_account_apr' => $this->module->calculateKlarnaApr($cart->getOrderTotal(true, Cart::BOTH), KlarnaFlags::CHECKOUT_PAGE, $value->getId()),
				'klarna_account_monthly_cost' =>
					$this->module->klarnaCalculateMonthlyCost($cart->getOrderTotal(true, CART::BOTH), KlarnaFlags::CHECKOUT_PAGE, $value->getId())
				));
			}
		}

		$klarna_campaign_pclass = $this->module->getKlarnaPClasses('CAMPAIGN');

		if (!empty($klarna_campaign_pclass[0]))
		{

		$this->context->smarty->assign(array(
				'klarna_campaign_id_1' => $klarna_campaign_pclass[0]->getId(),
				'klarna_campaign_description_1' => $klarna_campaign_pclass[0]->getDescription(),
				'klarna_campaign_interest_1' => $klarna_campaign_pclass[0]->getInterestRate(),
				'klarna_campaign_invfee_1' => $klarna_campaign_pclass[0]->getInvoiceFee(),
				'klarna_campaign_startfee_1' => $klarna_campaign_pclass[0]->getStartFee(),
				'klarna_campaign_months_1' => $klarna_campaign_pclass[0]->getMonths(),
				'klarna_campaign_minamount_1' => $klarna_campaign_pclass[0]->getMinAmount(),
				'klarna_campaign_country_1' => $klarna_campaign_pclass[0]->getCountry(),
				'klarna_campaign_credit_cost_1' =>
				$this->module->klarnaCalculateTotalCredit($cart->getOrderTotal(true, Cart::BOTH), KlarnaFlags::CHECKOUT_PAGE, $value->getId()),
				'klarna_campaign_monthly_cost_1' =>
				$this->module->klarnaCalculateMonthlyCost($cart->getOrderTotal(true, Cart::BOTH), KlarnaFlags::CHECKOUT_PAGE, $value->getId()),
				'klarna_campaign_calc_apr_1' =>
				$this->module->calculateKlarnaApr($cart->getOrderTotal(true, Cart::BOTH), KlarnaFlags::CHECKOUT_PAGE, $value->getId())
			));

		}

		if (!empty($klarna_campaign_pclass[1]))
		{

		$this->context->smarty->assign(array(
				'klarna_campaign_id_2' => $klarna_campaign_pclass[1]->getId(),
				'klarna_campaign_description_2' => $klarna_campaign_pclass[1]->getDescription(),
				'klarna_campaign_interest_2' => $klarna_campaign_pclass[1]->getInterestRate(),
				'klarna_campaign_invfee_2' => $klarna_campaign_pclass[1]->getInvoiceFee(),
				'klarna_campaign_startfee_2' => $klarna_campaign_pclass[1]->getStartFee(),
				'klarna_campaign_months_2' => $klarna_campaign_pclass[1]->getMonths(),
				'klarna_campaign_minamount_2' => $klarna_campaign_pclass[1]->getMinAmount(),
				'klarna_campaign_country_2' => $klarna_campaign_pclass[1]->getCountry(),
				'klarna_campaign_credit_cost_2' =>
				$this->module->klarnaCalculateTotalCredit($cart->getOrderTotal(true, Cart::BOTH), KlarnaFlags::CHECKOUT_PAGE, $value->getId()),
				'klarna_campaign_monthly_cost_2' =>
				$this->module->klarnaCalculateMonthlyCost($cart->getOrderTotal(true, Cart::BOTH), KlarnaFlags::CHECKOUT_PAGE, $value->getId()),
				'klarna_campaign_calc_apr_2' =>
				$this->module->calculateKlarnaApr($cart->getOrderTotal(true, Cart::BOTH), KlarnaFlags::CHECKOUT_PAGE, $value->getId())
			));
		}

		if (!empty($klarna_campaign_pclass[2]))
		{

		$this->context->smarty->assign(array(
				'klarna_campaign_id_3' => $klarna_campaign_pclass[2]->getId(),
				'klarna_campaign_description_3' => $klarna_campaign_pclass[2]->getDescription(),
				'klarna_campaign_interest_3' => $klarna_campaign_pclass[2]->getInterestRate(),
				'klarna_campaign_invfee_3' => $klarna_campaign_pclass[2]->getInvoiceFee(),
				'klarna_campaign_startfee_3' => $klarna_campaign_pclass[2]->getStartFee(),
				'klarna_campaign_months_3' => $klarna_campaign_pclass[2]->getMonths(),
				'klarna_campaign_minamount_3' => $klarna_campaign_pclass[2]->getMinAmount(),
				'klarna_campaign_country_3' => $klarna_campaign_pclass[2]->getCountry(),
				'klarna_campaign_credit_cost_3' =>
				$this->module->klarnaCalculateTotalCredit($cart->getOrderTotal(true, Cart::BOTH), KlarnaFlags::CHECKOUT_PAGE, $value->getId()),
				'klarna_campaign_monthly_cost_3' =>
				$this->module->klarnaCalculateMonthlyCost($cart->getOrderTotal(true, Cart::BOTH), KlarnaFlags::CHECKOUT_PAGE, $value->getId()),
				'klarna_campaign_calc_apr_3' =>
				$this->module->calculateKlarnaApr($cart->getOrderTotal(true, Cart::BOTH), KlarnaFlags::CHECKOUT_PAGE, $value->getId())
			));

		}

		$this->context->smarty->assign(array(
			'checkLocale' => $this->module->checkLocale(Country::getIsoById($this->context->country->id), Tools::strtoupper($this->context->currency->iso_code), Tools::strtolower($this->context->language->iso_code)),
			'nbProducts' => $cart->nbProducts(),
			'cust_currency' => $cart->id_currency,
			'currencies' => $this->module->getCurrency((int)$cart->id_currency),
			'total' => $cart->getOrderTotal(true, Cart::BOTH),
			'klarna_pno_placeholder' => $this->module->getPlaceholderPno(),
			'klarna_pno_pattern' => $this->module->getPatternPno(),
			'klarna_language' => $this->module->getLocale(),
			'klarna_device' => $this->module->checkMobile(),
			'klarna_merchant_eid' => $this->module->klarna_eid,
			'klarna_invoice_sum' => $this->module->getInvoiceFee(),
			'klarna_country' => Country::getIsoById($this->context->country->id),
			'this_path' => $this->module->getPathUri(),
			'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->module->name.'/'
		));
		$this->context->controller->addCSS(__PS_BASE_URI__.'modules/klarnapayments/views/css/payment_part.css', 'all');
		$this->context->controller->addJS(__PS_BASE_URI__.'modules/klarnapayments/views/js/payment_part.js');
		$this->setTemplate('payment_part.tpl');

	}

	public function postProcess()
	{
		$cart = $this->context->cart;

		if ($cart->id == 0 || $cart->id_address_delivery == 0 || $cart->id_address_invoice == 0 || !$this->module->active)
			Tools::redirect('index.php?controller=order&step=1');

		$authorized = false;

		foreach (Module::getPaymentModules() as $module)
		if ($module['name'] == 'klarnapayments')
		{
					$authorized = true;
				break;
		}

		if (!$authorized)
			die($this->module->l('The payment method is not available.', 'validation'));

		$customer = new Customer($cart->id_customer);
		if (!Validate::isLoadedObject($customer))
			Tools::redirect('index.php?controller=order&step=1');

		if (Tools::isSubmit('submitKlarnaPayment')) 
		{
		
		if (preg_match($this->module->getPatternPnoPHP(), Tools::getValue('klarna_pno')) && Tools::getIsset('klarna_pno'))
		{	
		
		$currency = new Currency((int)$cart->id_currency);
		$address = new Address((int)$cart->id_address_invoice);

		$carrier = new Carrier((int)$cart->id_carrier);
		$amount = $cart->getOrderTotal(true, Cart::BOTH);
		$goods = $cart->getOrderTotal(true, Cart::ONLY_PRODUCTS);
		$shipping = $cart->getOrderTotal(true, Cart::ONLY_SHIPPING);
		$error_klarna = Context::getContext()->link->getModuleLink('klarnapayments', 'error');

		$k = new Klarna();

		$k->config($this->module->klarna_eid,
			$this->module->klarna_secret,
			$this->module->getKlarnaCountry(),
			$this->module->getKlarnaLanguage(),
			$this->module->getKlarnaCurrency(),
			$this->module->getKlarnaEnvironment(),
			'json',
			dirname(__FILE__).'/../../../pclasses/pclasses.json');

		$products = $cart->getProducts();

		$goods = $cart->getOrderTotal(true, Cart::ONLY_PRODUCTS);

		$discount_value = $cart->getCartRules();

		$disc_value = 0;
		foreach ($discount_value as $values)
			if ($values['reduction_percent'] > 0)

				$disc_value = $values['reduction_percent'];
			elseif ($values['value_real'] > 0)
				$disc_value = ($values['value_real'] / $goods) * 100;
				$disc_value = number_format($disc_value, 2, '.', '');

		foreach ($products as $product)
			$k->addArticle(utf8_decode($product['quantity']),
			utf8_decode($product['id_product']),
			utf8_decode(html_entity_decode($product['name'])),
			utf8_decode(Tools::ps_round($product['price_wt'], _PS_PRICE_DISPLAY_PRECISION_)),
			utf8_decode($product['rate']),
			utf8_decode($disc_value),
			KlarnaFlags::INC_VAT);

		$shipping_tax = Tax::getCarrierTaxRate($cart->id_carrier, $cart->id_address_invoice);

		$k->addArticle(1, utf8_decode($cart->id_carrier), utf8_decode(html_entity_decode($carrier->name)), utf8_decode($shipping), utf8_decode($shipping_tax), 0, KlarnaFlags::INC_VAT | KlarnaFlags::IS_SHIPMENT);

		$addr = new KlarnaAddr(utf8_decode(html_entity_decode($customer->email)),
			utf8_decode(html_entity_decode($address->phone)),
			utf8_decode(html_entity_decode($address->phone_mobile)),
			utf8_decode(html_entity_decode($address->firstname)),
			utf8_decode(html_entity_decode($address->lastname)),
			utf8_decode(html_entity_decode($address->address2)),
			utf8_decode(html_entity_decode($address->address1)),
			utf8_decode(html_entity_decode($address->postcode)),
			utf8_decode(html_entity_decode($address->city)),
			$this->module->getKlarnaCountry(),
			Tools::getIsset('klarna_house_num') ? utf8_decode(Tools::getValue('klarna_house_num')) : null,
			Tools::getIsset('klarna_house_ext') ? utf8_decode(Tools::getValue('klarna_house_ext')) : null);

		if (strlen($address->company) > 0 && $address->company !== '')
		{
			$addr->setCompanyName(utf8_decode(html_entity_decode($address->company)));
		}

		$k->setAddress(KlarnaFlags::IS_BILLING, $addr);
		$k->setAddress(KlarnaFlags::IS_SHIPPING, $addr);

		try
		{
				$result = $k->reserveAmount(Tools::getValue('klarna_pno'),
				Tools::getIsset('klarnapaymentsgender') ? (Tools::getValue('klarnapaymentsgender') == '1' ? KlarnaFlags::MALE : KlarnaFlags::FEMALE) : null,
				-1,
				KlarnaFlags::NO_FLAG,
				Tools::getValue('select_klarna_method'));

				$reservation_number = $result[0];
				$klarna_order_status = $result[1];


				if ($klarna_order_status == 1)
				{
					$status = 'OK';
					$type = 'Part payment';
					$this->module->validateOrder($cart->id, Configuration::get('KLARNA_OS_AUTHORIZED'), $amount, $this->module->displayName, 'Status:'.
					$status.'; Reservation id:'.$reservation_number.'; Type:'.$type, array(), (int)$currency->id, false, $customer->secure_key);


					Db::getInstance()->execute('
					INSERT INTO `'._DB_PREFIX_.'klarna_orders` (`id_order`, `id_reservation`, `payment_status`)
					VALUES ('.(int)$this->module->currentOrder.', \''.pSQL($reservation_number).'\', \''.pSQL($status).'\')');

					Tools::redirect('index.php?controller=order-confirmation&id_cart='.
							$cart->id.'&id_module='.$this->module->id.'&id_order='.$this->module->currentOrder.'&key='.$customer->secure_key);
				}

				if ($klarna_order_status == 2)
				{
				$status = 'Pending';
				$type = 'Part payment';
				$this->module->validateOrder($cart->id, Configuration::get('KLARNA_OS_PENDING'), $amount, $this->module->displayName, 'Status:'.
					$status.'; Reservation id:'.$reservation_number.'; Type:'.$type, array(), (int)$currency->id, false, $customer->secure_key);

				Db::getInstance()->execute('
					INSERT INTO `'._DB_PREFIX_.'klarna_orders` (`id_order`, `id_reservation`, `payment_status`)
					VALUES ('.(int)$this->module->currentOrder.', \''.pSQL($reservation_number).'\', \''.pSQL($status).'\')');

				Tools::redirect('index.php?controller=order-confirmation&id_cart='.
					$cart->id.'&id_module='.$this->module->id.'&id_order='.$this->module->currentOrder.'&key='.$customer->secure_key);
				}

		} catch (Exception $e){
						
			Logger::addLog('Klarna module: order failed with message: '.$e->getMessage().' and response code '.$e->getCode().' on cart id: '.$cart->id, 1);
			Tools::redirect($error_klarna);
			

		}

		} 
		else
		{
			$this->context->smarty->assign('klarna_error', 1);
						
		}
		
			
		}
	}
}