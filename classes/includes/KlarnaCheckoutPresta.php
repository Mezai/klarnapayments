<?php
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
		$sharedSecret = KlarnaConfigHandler::getKlarnaSecret($country);
		$eid = KlarnaConfigHandler::getMerchantID($country);

		$products = $cart->getProducts();
		$connector = Klarna_Checkout_Connector::create(
    	$sharedSecret,
		(Configuration::get('KLARNA_ENVIRONMENT') == 'live') ? Klarna_Checkout_Connector::BASE_URL : Klarna_Checkout_Connector::BASE_TEST_URL
		);
		//products
		foreach ($products as $product) {
			$price = Tools::ps_round($product['price_wt'], _PS_PRICE_DISPLAY_PRECISION_);
			$price = (int)($price * 100);

			$checkoutcart[] = array(
		   'reference' => $product['reference'],
		   'name' => $product['name'],
		   'quantity' => (int)($product['cart_quantity']),
		   'unit_price' => $price,
		   'discount_rate' => 0,
		   'tax_rate' => (int)$product['rate'] * 100
		   ); 	
		}

		//shipping
		$carrier = new Carrier((int)$cart->id_carrier);
		$shipmentfee = $cart->getOrderShippingCost();
		$shipping = $cart->getOrderTotal(true, Cart::ONLY_SHIPPING);

		$shipping_tax = Tax::getCarrierTaxRate($cart->id_carrier, $cart->id_address_invoice);


		if ($shipping > 0)
		{
			$shipping_price = Tools::ps_round($shipping, _PS_PRICE_DISPLAY_PRECISION_);
			$shipping_price = (int)($shipping_price * 100);

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

		if (array_key_exists('klarna_order_id', $_SESSION)) {
		    // Resume session
		    $order = new Klarna_Checkout_Order(
		        $connector,
		        $_SESSION['klarna_order_id']
		    );
		    try {
		        $order->fetch();
		        // Reset cart
		        $update['cart']['items'] = array();
		        foreach ($checkoutcart as $item) {
		            $update['cart']['items'][] = $item;
		        }
		        $order->update($update);
		    } catch (Exception $e) {
		        // Reset session
		        $order = null;
		        unset($_SESSION['klarna_order_id']);
		    }
		}
		if ($order == null) 
		{
			$is_ssl = Tools::usingSecureMode();
			$cms = new CMS((int)(Configuration::get('KLARNA_CHECKOUT_TERMS')), (int)($this->context->cookie->id_lang));
			$link_conditions = $this->context->link->getCMSLink($cms, $cms->link_rewrite, $is_ssl);
			$check_checkout = ((int)Configuration::get('PS_ORDER_PROCESS_TYPE') === 1) ? 'order-opc' : 'order';
			$checkout_uri = $this->context->link->getPageLink($check_checkout, $is_ssl);
			$confirmation_uri = (Configuration::get('PS_SSL_ENABLED') ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].__PS_BASE_URI__.'index.php' .'?klarna_order={checkout.order.id}' . '&fc=module&module=klarnapayments&controller=checkout';
			$pushPage = (Configuration::get('PS_SSL_ENABLED') ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].__PS_BASE_URI__.'index.php' .'?klarna_order={checkout.order.id}' . '&fc=module&module=klarnapayments&controller=push';
			$terms_uri = $link_conditions;
			$klarnapayments = new KlarnaPayments();

		    $create['purchase_country'] = $country;
		    $create['purchase_currency'] = $currency;
		    $create['locale'] = $locale;
		    $create['gui']['layout'] = $klarnapayments->checkMobile();
		    $create['options']['color_button'] = (is_null(Configuration::get('KLARNA_CHECKOUT_COLOR_BUTTON'))) ? '' : Configuration::get('KLARNA_CHECKOUT_COLOR_BUTTON');
		    $create['options']['color_button_text'] = (is_null(Configuration::get('KLARNA_CHECKOUT_COLOR_BUTTON_TEXT'))) ? '' : Configuration::get('KLARNA_CHECKOUT_COLOR_BUTTON_TEXT');
		    $create['options']['color_checkbox'] = (is_null(Configuration::get('KLARNA_CHECKOUT_COLOR_CHECKBOX'))) ? '' : Configuration::get('KLARNA_CHECKOUT_COLOR_CHECKBOX');
		    $create['options']['color_checkbox_checkmark'] = (is_null(Configuration::get('KLARNA_CHECKOUT_COLOR_CHECKBOX_CHECKMARK'))) ? '' : Configuration::get('KLARNA_CHECKOUT_COLOR_CHECKBOX_CHECKMARK');
		    $create['options']['color_header'] = (is_null(Configuration::get('KLARNA_CHECKOUT_COLOR_HEADER'))) ? '' : Configuration::get('KLARNA_CHECKOUT_COLOR_HEADER'); 
		    $create['options']['color_link'] = (is_null(Configuration::get('KLARNA_CHECKOUT_COLOR_LINK'))) ? '' : Configuration::get('KLARNA_CHECKOUT_COLOR_LINK');
		    $create['merchant']['id'] = (String)$eid;
		    $create['merchant']['terms_uri'] = $terms_uri;
		    $create['merchant']['checkout_uri'] = $checkout_uri;
		    $create['merchant']['confirmation_uri'] = $confirmation_uri;
		    $create['merchant']['push_uri'] = $pushPage;
		    $create['merchant_reference']['orderid1'] = "".(int)$this->context->cart->id;
		    
		    $update['cart']['items'] = array();
		    foreach ($checkoutcart as $item) {
		        $create['cart']['items'][] = $item;
		    }
		    $order = new Klarna_Checkout_Order($connector);
		    try {
		        $order->create($create);
		        $order->fetch();
		    } catch (Klarna_Checkout_ApiErrorException $e) {
		    	Logger::addLog('Klarna module: failed creating checkout with message: '.$e->getMessage().' and payload :'.$e->getPayload().'');
		    }
		}
		// Store location of checkout session
		$_SESSION['klarna_order_id'] = $sessionID = $order['id'];
		if (isset($order['gui']['snippet'])) {
		    // Display checkout
		    $snippet = $order['gui']['snippet'];
		   return $snippet;
		}
	}
	
}			