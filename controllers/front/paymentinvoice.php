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

class KlarnaPaymentsPaymentInvoiceModuleFrontController extends ModuleFrontController
{
	public $ssl = true;
	public $display_column_left = false;
	public $display_column_right = false;


	/***
	 *	@See FrontController::initContent()
	 */

	public function initContent()
	{
		$this->display_column_left = false;
		$this->display_column_right = false;

		parent::initContent();

		$cart = $this->context->cart;

		if (!$this->module->checkCurrency($cart))
				Tools::redirect('index.php?controller=order');

		if ((int)Configuration::get('klarna_invoice_payment') == 0)
				Tools::redirect('index.php?controller=order');

		$klarna_special_pclass = $this->module->getKlarnaPClasses('SPECIAL');

		if (!empty($klarna_special_pclass))
		{
			foreach ($klarna_special_pclass as $value)
			{
				$this->context->smarty->assign(array(
				'klarna_special_id' => $value->getId(),
				'klarna_special_description' => $value->getDescription(),
				'klarna_special_invfee' => $value->getInvoiceFee(),
				'klarna_special_interest' => $value->getInterestRate(),
				'klarna_special_startfee' => $value->getStartFee(),
				'klarna_special_months' => $value->getMonths(),
				'klarna_special_minamount' => $value->getMinAmount(),
				'klarna_special_country' => $value->getCountry(),
				'klarna_special_total_credit' => $this->module->klarnaCalculateTotalCredit($cart->getOrderTotal(true, Cart::BOTH), KlarnaFlags::CHECKOUT_PAGE, $value->getId())

				));
			}
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
		$this->context->controller->addCSS(__PS_BASE_URI__.'modules/klarnapayments/views/css/payment_invoice.css', 'all');
		$this->context->controller->addJS(__PS_BASE_URI__.'modules/klarnapayments/views/js/payment_invoice.js');
		$this->setTemplate('payment_invoice.tpl');
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

		if ((int)Configuration::get('klarna_invoice_fee') == 1 && Tools::getValue('select_klarna_method') == '-1')
		{

			$invoicefeeproduct = $this->module->getByReference(Configuration::get('klarna_invoice_fee_ref'));
			$invoicerefid = $this->module->getProductId(Configuration::get('klarna_invoice_fee_ref'));

			if (Validate::isLoadedObject($invoicefeeproduct))
			{
					$cart->updateQty(1, $invoicerefid);
					$cart->update(true);
					$cart->getPackageList(true);

			}
				$invoice_product = $cart->getProducts(false, $invoicerefid);

				foreach ($invoice_product as $invoicehandling)
				$k->addArticle(utf8_decode($invoicehandling['quantity']),
						utf8_decode($invoicehandling['id_product']),
						utf8_decode(html_entity_decode($invoicehandling['name'])),
						utf8_decode(Tools::ps_round($invoicehandling['price_wt'], _PS_PRICE_DISPLAY_PRECISION_)),
						utf8_decode($invoicehandling['rate']),
						0,
						KlarnaFlags::INC_VAT,
						KlarnaFlags::IS_HANDLING);

		}

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
					$type = 'Invoice payment';
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
				$type = 'Invoice payment';
				$this->module->validateOrder($cart->id, Configuration::get('KLARNA_OS_PENDING'), $amount, $this->module->displayName, 'Status:'.
					$status.'; Reservation id:'.$reservation_number.'; Type:'.$type, array(), (int)$currency->id, false, $customer->secure_key);

				Db::getInstance()->execute('
					INSERT INTO `'._DB_PREFIX_.'klarna_orders` (`id_order`, `id_reservation`, `payment_status`)
					VALUES ('.(int)$this->module->currentOrder.', \''.pSQL($reservation_number).'\', \''.pSQL($status).'\')');

				Tools::redirect('index.php?controller=order-confirmation&id_cart='.
					$cart->id.'&id_module='.$this->module->id.'&id_order='.$this->module->currentOrder.'&key='.$customer->secure_key);
				}

		} catch (Exception $e){
			if ((int)Configuration::get('klarna_invoice_fee') == 1 && Tools::getValue('select_klarna_method') == '-1')
			$cart->deleteProduct($invoicerefid);

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