<?php


require_once(dirname(__FILE__).'/../libs/KlarnaCheckout/Checkout.php');
class KlarnaCheckoutPs
{

  public function __construct($merchant_eid, $merchant_secret) {
    $this->merchant_eid = $merchant_eid;
    $this->merchant_secret = $merchant_secret;

  }

  public function createNew($country, $currency, $locale, $products, $checkout_uri, $kco_shipping_amount, $carrier, $shipping_tax)
  {
    if (!is_string($country) || !is_string($currency) || !is_string($locale))
    return;


    $connector = Klarna_Checkout_Connector::create(
        $this->merchant_secret,
        Klarna_Checkout_Connector::BASE_TEST_URL
    );

    $order = new Klarna_Checkout_Order($connector);

    $create['purchase_country'] = $country;
    $create['purchase_currency'] = $currency;
    $create['locale'] = $locale;

    // $create['recurring'] = true;
    $create['merchant'] = array(
        'id' => $this->merchant_eid,
        'terms_uri' => 'http://example.com/terms.html',
        'checkout_uri' => $checkout_uri,
        'confirmation_uri' => 'http://example.com/confirmation.php' .
            '?klarna_order_id={checkout.order.id}',
        'push_uri' => 'http://example.com/push.php' .
            '?klarna_order_id={checkout.order.id}'
    );
    $create['cart']['items'] = array();
    if (Configuration::get('KLARNA_CHECKOUT_COLOR_BUTTON') !== null)  {
      $create['options']['color_button'] = Configuration::get('KLARNA_CHECKOUT_COLOR_BUTTON');
    }

    if (Configuration::get('KLARNA_CHECKOUT_COLOR_BUTTON_TEXT') !== null) {
      $create['options']['color_button_text'] = Configuration::get('KLARNA_CHECKOUT_COLOR_BUTTON_TEXT');
    }

    if (Configuration::get('KLARNA_CHECKOUT_COLOR_CHECKBOX') !== null) {
      $create['options']['color_checkbox'] = Configuration::get('KLARNA_CHECKOUT_COLOR_CHECKBOX');
    }

    if (Configuration::get('KLARNA_CHECKOUT_COLOR_CHECKBOX_CHECKMARK') !== null) {
      $create['options']['color_checkbox_checkmark'] = Configuration::get('KLARNA_CHECKOUT_COLOR_CHECKBOX_CHECKMARK');
    }

    if (Configuration::get('KLARNA_CHECKOUT_COLOR_HEADER') !== null) {
      $create['options']['color_header'] = Configuration::get('KLARNA_CHECKOUT_COLOR_HEADER');
    }

    if (Configuration::get('KLARNA_CHECKOUT_COLOR_LINK') !== null) {
      $create['options']['color_link'] = Configuration::get('KLARNA_CHECKOUT_COLOR_LINK');
    }



    foreach ($products as $product) {
      $price = $product['price_wt'];
      $checkoutcart[] = array
      (
        'reference' => $product['reference'],
        'name' => $product['name'],
        'quantity' => (int)$product['cart_quantity'],
        'unit_price' => (int)$product['price_wt'] * 100,
        'discount_rate' => 0,
        'tax_rate' => (int)$product['rate'] * 100
      );

    }

    $checkoutcart[] = array
        (
          'type' => 'shipping_fee',
          'reference' => $carrier->id_reference,
          'name' => $carrier->name,
          'quantity' => 1,
          'unit_price' => $kco_shipping_amount * 100,
          'tax_rate' => $shipping_tax * 100
        );

    foreach ($checkoutcart as $item) {
        $create['cart']['items'][] = $item;

    }

    try {
        $order->create($create);
        $order->fetch();

        $_SESSION['klarna_order_id'] = $orderID = $order['id'];

        // Display checkout
        return $snippet = $order['gui']['snippet'];

        //echo sprintf('Order ID: %s', $orderID);
    } catch (Klarna_Checkout_ApiErrorException $e) {
        var_dump($e->getMessage());
        var_dump($e->getPayload());
        die;
    }

  }


}
