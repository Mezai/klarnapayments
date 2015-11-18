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

class KlarnaGoodsList
{
	public function buildGoodsList($cart, $klarna)
	{
		$products = $cart->getProducts();

		foreach ($products as $product) {
			$attributes = "";

			if (isset($product['attributes'])) {
				$attributes = $product['attributes'];
			}

			if (empty($product['rate'])) {
				$price_wt = (float)$product['price_wt'];
				$price = (float)$product['price'];
				$rate = round((($price_wt / $price) - 1.0) * 100);
			} else {
				$rate = $product['rate'];

			}

			try {

				$klarna->klarna->addArticle(
					$product['cart_quantity'],
                    KlarnaPrestaEncoding::encode($product['reference']),
                    KlarnaPrestaEncoding::encode($product['name'] . $attributes),
                    $product['price_wt'],
                    $rate,
                    0,
                    KlarnaFlags::INC_VAT | ($product['reference'] == 'invoicefee'
                        ? KlarnaFlags::IS_HANDLING
                        : 0
                    )
                );
			} catch(Exception $e) {
                Klarna::printDebug(
                    "error adding article -  " . $e->getMessage(), $product
                );
		}
	}
        // Add discounts
        $discounts = $cart->getDiscounts();

        foreach ($discounts as $discount) {

            $rate = 0;
            $incvat = 0;
            // Free shipping has a real value of '!'.
            if ($discount['value_real'] !== '!') {
                $incvat = $discount['value_real'];
                $extvat = $discount['value_tax_exc'];
                $rate = round((($incvat / $extvat) - 1.0) * 100);
            }

            try {
                $klarna->klarna->addArticle(
                    1,
                    '', // no article number for discounts
                    KlarnaPrestaEncoding::encode($discount['description']),
                    ($incvat * -1),
                    $rate,
                    0,
                    KlarnaFlags::INC_VAT
                );
            } catch(Exception $e) {
                Klarna::printDebug(
                    "error adding article -  " . $e->getMessage(), $discount
                );
            }
        }

        // Add shipping fee
        $shipmentfee = $cart->getOrderShippingCost();
        if ($shipmentfee > 0.0) {
            if (isset($cart->id_carrier) && !empty($cart->id_carrier)) {
                $cid = (int)$cart->id_carrier;
            } else {
                $cid = Configuration::get('PS_CARRIER_DEFAULT');
            }
            $carrier = new Carrier($cid);

            $carriedLoaded = Validate::isLoadedObject($carrier);
            if (!$carriedLoaded) {
                die(Tools::displayError('Hack attempt: "no default carrier"'));
            }

            if ($carrier->active) {
                $taxrate = Tax::getCarrierTaxRate(
                    (int)$carrier->id,
                    (int)$cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')}
                );

                try {
                    $klarna->klarna->addArticle(
                        1,
                        '',
                        KlarnaPrestaEncoding::encode($this->l('Shipping fee')),
                        $shipmentfee,
                        $taxrate,
                        0,
                        KlarnaFlags::INC_VAT | KlarnaFlags::IS_SHIPMENT
                    );
                } catch(Exception $e) {
                    Klarna::printDebug(
                        "error adding shipping fee - " . $e->getMessage(), $carrier
                    );
                }
            }
        }

        // Add the fee for gift wrapping
        if ($cart->gift == 1) {
            try {
                $rate = 0;
                $wrapping_fees_tax = new Tax(
                    (int)(Configuration::get('PS_GIFT_WRAPPING_TAX'))
                );
                if ($wrapping_fees_tax->rate !== null) {
                    $rate = $wrapping_fees_tax->rate;
                }
                $klarna->klarna->addArticle(
                    1,
                    '',
                    KlarnaPrestaEncoding::encode($this->l('Gift wrapping fee')),
                    $cart->getOrderTotal(true, Cart::ONLY_WRAPPING),
                    $rate,
                    0,
                    KlarnaFlags::INC_VAT
                );
            } catch(Exception $e) {
                Klarna::printDebug(
                    "error adding giftwrap fee", $e->getMessage()
                );
            }
        }

        return $klarna;
    }
}
