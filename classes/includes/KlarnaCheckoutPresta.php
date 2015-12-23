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

require_once KLARNA_DIRECTORY.'/libs/checkout/Checkout.php';
class KlarnaCheckoutPresta
{

	public function __construct()
	{
		$this->context = Context::getContext();

	}


	public function checkout($cart, $country, $currency, $locale)
	{
		session_start();

		$order = null;
		$shared_secret = KlarnaConfigHandler::getKlarnaSecret($country);
		$eid = KlarnaConfigHandler::getMerchantID($country);

		$products = $cart->getProducts();

		if ((String)Configuration::get('KLARNA_ENVIRONMENT') === 'live')
			$connector = Klarna_Checkout_Connector::create((String)$shared_secret, Klarna_Checkout_Connector::BASE_URL);
		else
			$connector = Klarna_Checkout_Connector::create((String)$shared_secret, Klarna_Checkout_Connector::BASE_TEST_URL);

		$is_ssl = Tools::usingSecureMode();
		$cms = new CMS((int)Configuration::get('PS_CONDITIONS_CMS_ID'), (int)$this->context->cookie->id_lang);
		$link_conditions = $this->context->link->getCMSLink($cms, $cms->link_rewrite, $is_ssl);
		$check_checkout = ((int)Configuration::get('PS_ORDER_PROCESS_TYPE') === 1) ? 'order-opc' : 'order';
		$checkout_uri = $this->context->link->getPageLink($check_checkout, $is_ssl);
		$confirmation_uri = (Configuration::get('PS_SSL_ENABLED') ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].__PS_BASE_URI__.'index.php'.'?klarna_order={checkout.order.id}'.'&country='.$country.'&fc=module&module=klarnapayments&controller=checkout';
		$push_page = (Configuration::get('PS_SSL_ENABLED') ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].__PS_BASE_URI__.'index.php'.'?klarna_order={checkout.order.id}'.'&country='.$country.'&fc=module&module=klarnapayments&controller=push';
		$terms_uri = $link_conditions;
		$klarnapayments = new KlarnaPayments();

		//products
		foreach ($products as $product)
		{
			$price = Tools::ps_round($product['price_wt'], _PS_PRICE_DISPLAY_PRECISION_);
			$price = (int)$price * 100;

			$product_img = $this->context->link->getImageLink($product['link_rewrite'], $product['id_image']);
			$product_uri = $this->context->link->getProductLink(new Product($product['id_product']));

			$checkoutcart[] = array(
			'reference' => $product['reference'],
			'name' => $product['name'],
			'quantity' => (int)$product['cart_quantity'],
			'ean' => $product['ean13'],
			'uri' => $product_uri,
			'image_uri' => $product_img,
			'unit_price' => $price,
			'discount_rate' => 0,
			'tax_rate' => (int)$product['rate'] * 100
			);
		}

		//shipping
		if (!$cart->isVirtualCart())
		{
		$carrier = new Carrier((int)$cart->id_carrier);
		$shipping = $cart->getOrderTotal(true, Cart::ONLY_SHIPPING);

		$shipping_tax = Tax::getCarrierTaxRate($cart->id_carrier, $cart->id_address_invoice);

		$shipping_price = Tools::ps_round($shipping, _PS_PRICE_DISPLAY_PRECISION_);

		$shipping_price = (int)$shipping_price * 100;

		$checkoutcart[] = array(
			'type' => 'shipping_fee',
			'reference' => (String)$carrier->id_reference,
			'name' => (String)$carrier->name,
			'quantity' => 1,
			'unit_price' => $shipping_price,
			'discount_rate' => 0,
			'tax_rate' => (int)$shipping_tax * 100
			);
		}
		$discounts = $this->context->cart->getCartRules();
		if (!empty($discounts) && count($discounts) > 0)
		{
			foreach ($discounts as $discount)
			{
				$tax_discount = (int)round((($discount['value_real'] / $discount['value_tax_exc']) - 1.0) * 100);

				$price = $discount['value_real'];
				$price = Tools::ps_round($price, _PS_PRICE_DISPLAY_PRECISION_);

				$checkoutcart[] = array(
				'type' => 'discount',
				'reference' => $discount['name'],
				'name' => $discount['name'],
				'quantity' => 1,
				'unit_price' => -($price * 100),
				'tax_rate' => $tax_discount * 100

				);
			}
		}

		if (array_key_exists('klarna_order_id', $_SESSION))
		{
			$order = new Klarna_Checkout_Order($connector, $_SESSION['klarna_order_id']);

				try {
				$order->fetch();
				$update['cart']['items'] = array();
				$update['merchant']['id'] = (String)$eid;
				$update['merchant']['terms_uri'] = (String)$terms_uri;
				$update['merchant']['checkout_uri'] = (String)$checkout_uri;
				$update['merchant']['confirmation_uri'] = (String)$confirmation_uri;
				$update['merchant']['push_uri'] = (String)$push_page;
				$update['merchant_reference']['orderid1'] = (String)$this->context->cart->id;
				$update['purchase_country'] = (String)$country;
				$update['purchase_currency'] = (String)$currency;
				$update['locale'] = (String)$locale;

				foreach ($checkoutcart as $item)
					$update['cart']['items'][] = $item;

				$order->update($update);
				} catch (Exception $e) {
				$order = null;
				unset($_SESSION['klarna_order_id']);
				}
		}
		if ($order === null)
		{
			$create['purchase_country'] = (String)$country;
			$create['purchase_currency'] = (String)$currency;
			$create['locale'] = (String)$locale;
			$create['gui']['layout'] = (String)$klarnapayments->checkMobile();

			if (Tools::strlen(Configuration::get('KLARNA_CHECKOUT_SHIPPING_DETAILS')) > 0)
			$create['options']['shipping_details'] = Configuration::get('KLARNA_CHECKOUT_SHIPPING_DETAILS');

			if (!is_null(Configuration::get('KLARNA_CHECKOUT_COLOR_BUTTON')))
			$create['options']['color_button'] = (String)Configuration::get('KLARNA_CHECKOUT_COLOR_BUTTON');
			if (!is_null(Configuration::get('KLARNA_CHECKOUT_COLOR_BUTTON_TEXT')))
			$create['options']['color_button_text'] = (String)Configuration::get('KLARNA_CHECKOUT_COLOR_BUTTON_TEXT');
			if (!is_null(Configuration::get('KLARNA_CHECKOUT_COLOR_CHECKBOX')))
			$create['options']['color_checkbox'] = (String)Configuration::get('KLARNA_CHECKOUT_COLOR_CHECKBOX');
			if (!is_null(Configuration::get('KLARNA_CHECKOUT_COLOR_CHECKBOX_CHECKMARK')))
			$create['options']['color_checkbox_checkmark'] = (String)Configuration::get('KLARNA_CHECKOUT_COLOR_CHECKBOX_CHECKMARK');
			if (!is_null(Configuration::get('KLARNA_CHECKOUT_COLOR_HEADER')))
			$create['options']['color_header'] = (String)Configuration::get('KLARNA_CHECKOUT_COLOR_HEADER');
			if (!is_null(Configuration::get('KLARNA_CHECKOUT_COLOR_LINK')))
			$create['options']['color_link'] = (String)Configuration::get('KLARNA_CHECKOUT_COLOR_LINK');

			if ((int)Configuration::get('KLARNA_CHECKOUT_CHECKBOX') === 1)
			{
				$create['options']['additional_checkbox']['text'] = (String)Configuration::get('KLARNA_CHECKOUT_CHECKBOX_TEXT');
				$create['options']['additional_checkbox']['checked'] = (Bool)Configuration::get('KLARNA_CHECKOUT_CHECKBOX_CHECKED');
				$create['options']['additional_checkbox']['required'] = (Bool)Configuration::get('KLARNA_CHECKOUT_CHECKBOX_REQUIRED');
			}

			$create['merchant']['id'] = (String)$eid;
			$create['merchant']['terms_uri'] = (String)$terms_uri;
			$create['merchant']['checkout_uri'] = (String)$checkout_uri;
			$create['merchant']['confirmation_uri'] = (String)$confirmation_uri;
			$create['merchant']['push_uri'] = (String)$push_page;
			$create['merchant_reference']['orderid1'] = (String)$this->context->cart->id;

			$update['cart']['items'] = array();
			foreach ($checkoutcart as $item)
				$create['cart']['items'][] = $item;

			$order = new Klarna_Checkout_Order($connector);

			try {
				$order->create($create);
				$order->fetch();

			} catch (Klarna_Checkout_ApiErrorException $e) {
				Logger::addLog('Klarna module: failed creating checkout with message: '.$e->getMessage().' and payload :'.print_r($e->getPayload()).'');
			}
		}
		// Store location of checkout session
		$_SESSION['klarna_order_id'] = $sessionID = $order['id'];
		if (isset($order['gui']['snippet']))
		{
			// Display checkout
			$snippet = $order['gui']['snippet'];
			return $snippet;
		}
	}

}
