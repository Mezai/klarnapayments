<?php

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
		Klarna_Checkout_Connector::BASE_TEST_URL
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

			$cms = new CMS((int)(Configuration::get('KLARNA_CHECKOUT_TERMS')), (int)($this->context->cookie->id_lang));
			$link_conditions = $this->context->link->getCMSLink($cms, $cms->link_rewrite, $is_ssl);
			$is_ssl = Tools::usingSecureMode();
			$check_checkout = ((int)Configuration::get('PS_ORDER_PROCESS_TYPE') === 1) ? 'order-opc' : 'order';
			$checkout_uri = $this->context->link->getPageLink($check_checkout, $is_ssl);

			$terms_uri = $link_conditions;
		    // Start new session
		    $create['purchase_country'] = $country;
		    $create['purchase_currency'] = $currency;
		    $create['locale'] = $locale;
		    $create['merchant'] = array(
		        'id' => (String)$eid,
		        'terms_uri' => $terms_uri,
		        'checkout_uri' => $checkout_uri,
		        'confirmation_uri' => 'http://example.com/confirmation.php' .
		            '?klarna_order_id={checkout.order.id}',
		        'push_uri' => 'http://example.com/push.php' .
		            '?klarna_order_id={checkout.order.id}'
		    );
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