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

class KlarnaPaymentsPushModuleFrontController extends ModuleFrontController
{
	public $display_column_left = false;
	public $display_column_right = false;
	public $ssl = true;



	public function postProcess()
	{
		require_once KLARNA_DIRECTORY.'/libs/checkout/Checkout.php';

		$country_iso_codes = array(
		'SWE' => 'SE',
		'NOR' => 'NO',
		'FIN' => 'FI',
		'DNK' => 'DK',
		'DEU' => 'DE',
		'NLD' => 'NL',
		'se' => 'SE',
		'no' => 'NO',
		'fi' => 'FI',
		'dk' => 'DK',
		'de' => 'DE',
		'nl' => 'NL',
		);

		try {
			$country = Tools::strtoupper(Tools::getValue('country'));
			$shared_secret = Configuration::get('KLARNA_SECRET_'.$country.'');

			if ((String)Configuration::get('KLARNA_ENVIRONMENT') === 'live')
				$connector = Klarna_Checkout_Connector::create((String)$shared_secret, Klarna_Checkout_Connector::BASE_URL);
			else
				$connector = Klarna_Checkout_Connector::create((String)$shared_secret, Klarna_Checkout_Connector::BASE_TEST_URL);

			@$order_id = Tools::getValue('klarna_order');
			$klarna_order = new Klarna_Checkout_Order($connector, $order_id);
			$klarna_order->fetch();

			if ($klarna_order['status'] == 'checkout_complete')
			{
				$id_cart = $klarna_order['merchant_reference']['orderid1'];
				$cart = new Cart((int)$id_cart);

				if ($cart->OrderExists())
				{
					$reference_ps = Order::getUniqReferenceOf((int)$cart->id);
					//Already created, send create
					$update = array();
					$update['status'] = 'created';
					$update['merchant_reference'] = array(
							'orderid1' => (String)$cart->id,
							'orderid2' => (String)$reference_ps
					);
					$klarna_order->update($update);
					Logger::addLog('Klarna module: create sent for order with cart id'.$cart->id);
						die;
				}

				$amount = (int)$klarna_order['cart']['total_price_including_tax'];
				$amount = (float)$amount / 100;
				$shipping = $klarna_order['shipping_address'];
				$billing = $klarna_order['billing_address'];
				$reservation_number = $klarna_order['reservation'];
				if ($country == 'SE' || $country == 'AT' || $country == 'FI' || $country == 'NO')
				{
					$extra = array();
					$extra['transaction_id'] = $reservation_number;
				}
					
				$id_customer = (int)Customer::customerExists($shipping['email'], true, false);
				if ($id_customer > 0)
				{
					$customer = new Customer($id_customer);
					if ($country === 'DE')
					{
						($billing['title'] === 'Herr') ? $customer->id_gender = 1 : $customer->id_gender = 0;

					}
					else
					{

						if ($klarna_order['customer']['gender'] === 'male')
						{
							$customer->id_gender = 1;

						}
						else
						{
							$customer->id_gender = 0;

						}
					}

					$date_of_birth = $klarna_order['customer']['date_of_birth'];

					$match_date = preg_match('/^(\d{4})(?:-)(\d{2})(?:-)(\d{2})$/', $date_of_birth, $match);

					if ((Bool)$match_date === true)
					{
						$customer->years = $match[1];
						$customer->months = $match[2];
						$customer->days = $match[3];

					}
					$customer->update();

				}
				else
				{

					$customer = new Customer();
					$customer->firstname = $shipping['given_name'];
					$customer->lastname = $shipping['family_name'];
					$customer->email = $shipping['email'];
					$customer->passwd = Tools::passwdGen(8, 'ALPHANUMERIC');
					$customer->is_guest = 1;
					$customer->id_default_group = (int)Configuration::get('PS_GUEST_GROUP', null, $cart->id_shop);
					if ($klarna_order['merchant_requested']['additional_checkbox'] === true)
						$customer->newsletter = 1;
						else
						$customer->newsletter = 0;

					$customer->optin = 0;
					$customer->active = 1;

					if ($country === 'DE')
					{
						($billing['title'] === 'Herr') ? $customer->id_gender = 1 : $customer->id_gender = 0;

					}
					else
					{

						if ($klarna_order['customer']['gender'] == 'male')
						{
							$customer->id_gender = 1;

						}
						else
						{
							$customer->id_gender = 0;

						}

					}

					$date_of_birth = $klarna_order['customer']['date_of_birth'];

					$match_date = preg_match('/^(\d{4})(?:-)(\d{2})(?:-)(\d{2})$/', $date_of_birth, $match);

					if ((Bool)$match_date === true)
					{
						$customer->years = $match[1];
						$customer->months = $match[2];
						$customer->days = $match[3];

					}

					$customer->add();
				}

				$delivery_address_id = 0;
				$invoice_address_id = 0;
				$shipping_iso = $country_iso_codes[$shipping['country']];
				$invocie_iso = $country_iso_codes[$billing['country']];
				$shipping_country_id = Country::getByIso($shipping_iso);
				$invocie_country_id = Country::getByIso($invocie_iso);
				if ($country == 'SE' || $country == 'FI' || $country == 'NO' || $country == 'AT')
				{
					foreach ($customer->getAddresses($cart->id_lang) as $address)
					{

						if ($address['firstname'] == $shipping['given_name'] && $address['lastname'] == $shipping['family_name']
							&& $address['city'] == $shipping['city'] && $address['address2'] == $shipping['care_of'] && $address['address1'] == $shipping['street_address']
							&& $address['postcode'] == $shipping['postal_code'] && $address['phone_mobile'] == $shipping['phone']
							&& $address['id_country'] == $shipping_country_id)
						{
							$cart->id_address_delivery = $address['id_adress'];
							$delivery_address_id = $address['id_adress'];
						}

						if ($address['firstname'] == $billing['given_name'] && $address['lastname'] == $billing['family_name']
							&& $address['city'] == $billing['city'] && $address['address2'] == $billing['care_of']
							&& $address['address1'] == $billing['street_address'] && $address['postcode'] == $billing['postal_code']
							&& $address['phone_mobile'] == $billing['phone'] && $address['id_country'] == $shipping_country_id)
						{
							$cart->id_address_invoice = $address['id_address'];
							$invoice_address_id = $address['id_address'];
						}
					}
				}

				if ($country == 'DE')
				{
					$street_address_shipping_de = $shipping['street_name'].$shipping['street_number'];
					$street_address_billing_de = $billing['street_name'].$billing['street_number'];

					foreach ($customer->getAddresses($cart->id_lang) as $address)
					{
						if ($address['firstname'] == $shipping['given_name'] && $address['lastname'] == $shipping['family_name']
							&& $address['city'] == $shipping['city'] && $address['address2'] == $shipping['care_of']
							&& $address['address1'] == $street_address_shipping_de && $address['postcode'] == $shipping['postal_code']
							&& $address['phone_mobile'] == $shipping['phone'] && $address['id_country'] == $shipping_country_id)
						{
							$cart->id_address_delivery = $address['id_adress'];
							$delivery_address_id = $address['id_adress'];
						}

						if ($address['firstname'] == $billing['given_name'] && $address['lastname'] == $billing['family_name']
							&& $address['city'] == $billing['city'] && $address['address2'] == $billing['care_of']
							&& $address['address1'] == $street_address_billing_de && $address['postcode'] == $billing['postal_code']
							&& $address['phone_mobile'] == $billing['phone'] && $address['id_country'] == $shipping_country_id)
						{
							$cart->id_address_invoice = $address['id_address'];
							$invoice_address_id = $address['id_address'];
						}
					}
				}

				if ($invoice_address_id == 0)
				{
					$address = new Address();
					$address->firstname = $billing['given_name'];
					$address->lastname = $billing['family_name'];

					if ($country == 'SE' || $country == 'FI' || $country == 'NO' || $country == 'AT')
					{
						if (Tools::strlen($billing['care_of']) > 0)
						{
							$address->address1 = $billing['care_of'];
							$address->address2 = $billing['street_address'];
						}
						else
						{
							$address->address1 = $billing['street_address'];

						}

					} elseif ($country == 'DE')
					{

						$address->address1 = $street_address_billing_de;

					}
					$address->postcode = $billing['postal_code'];
					$address->phone = $billing['phone'];
					$address->phone_mobile = $billing['phone'];
					$address->city = $billing['city'];
					$address->id_country = $invocie_country_id;
					$address->id_customer = $customer->id;
					$address->alias = 'Klarna Address';
					$address->add();
					$cart->id_address_invoice = $address->id;
					$invoice_address_id = $address->id;
				}
				if ($delivery_address_id == 0)
				{
					$address = new Address();
					$address->firstname = $shipping['given_name'];
					$address->lastname = $shipping['family_name'];

					if ($country == 'SE' || $country == 'FI' || $country == 'NO' || $country == 'AT')
					{
						if (Tools::strlen($shipping['care_of']) > 0)
						{
							$address->address1 = $shipping['care_of'];
							$address->address2 = $shipping['street_address'];
						}
						else
						{
							$address->address1 = $shipping['street_address'];

						}

					} elseif ($country == 'DE')
					{

						$address->address1 = $street_address_shipping_de;

					}

					$address->city = $shipping['city'];
					$address->postcode = $shipping['postal_code'];
					$address->phone = $shipping['phone'];
					$address->phone_mobile = $shipping['phone'];
					$address->id_country = $shipping_country_id;
					$address->id_customer = $customer->id;
					$address->alias = 'Klarna Address';
					$address->add();
					$cart->id_address_delivery = $address->id;
					$delivery_address_id = $address->id;
				}

				$new_delivery_options = array();	
				$new_delivery_options[(int)$delivery_address_id] = $cart->id_carrier.',';
				$new_delivery_options_serialized = serialize($new_delivery_options);

					Db::getInstance()->Execute('
						UPDATE `'._DB_PREFIX_.'cart`
						SET `delivery_option` = \''.pSQL($new_delivery_options_serialized).'\'
						WHERE `id_cart` = '.(int)$cart->id);

					if ($cart->id_carrier > 0)
						$cart->delivery_option = $new_delivery_options_serialized;
					else
						$cart->delivery_option = '';

					Db::getInstance()->Execute('
						UPDATE `'._DB_PREFIX_.'cart_product`
						SET `id_address_delivery` = \''.pSQL($delivery_address_id).'\'
						WHERE `id_cart` = '.(int)$cart->id);

					$cart->getPackageList(true);

				$cart->id_customer = $customer->id;
				$cart->secure_key = $customer->secure_key;
				$cart->save();

				Db::getInstance()->Execute('
					UPDATE `'._DB_PREFIX_.'cart`
					SET `id_customer` = \''.pSQL($customer->id).'\'
					WHERE `id_cart` = '.(int)$cart->id);
				Db::getInstance()->Execute('
					UPDATE `'._DB_PREFIX_.'cart`
					SET `secure_key` = \''.pSQL($customer->secure_key).'\'
					WHERE `id_cart` = '.(int)$cart->id);

				$cache_id = 'objectmodel_cart_'.$cart->id.'_0_0';
				Cache::clean($cache_id);

				$cart = new Cart($cart->id);
				$this->module->validateOrder($cart->id, Configuration::get('KLARNA_OS_CHECKOUT'), $amount, $this->module->displayName,
				$reservation_number, $extra, null, false, $customer->secure_key);

				Db::getInstance()->Execute('
				INSERT INTO `'._DB_PREFIX_.'klarna_orders` (`id_order`, `id_reservation`, `customer_firstname`,
				`customer_lastname`, `payment_status`, `customer_country`)
				VALUES ('.(int)$this->module->currentOrder.', \''.pSQL($reservation_number).'\', \''.pSQL($billing['given_name']).'\', \''.
				pSQL($billing['family_name']).'\', \''.pSQL($klarna_order['status']).'\', \''.pSQL(Tools::strtoupper($shipping['country'])).'\')');

				$reference_ps = Order::getUniqReferenceOf((int)$this->module->currentOrder);
				$update = array();
				$update['status'] = 'created';
				$update['merchant_reference'] = array(
					'orderid1' => (String)$cart->id,
					'orderid2' => (String)$reference_ps
				);
				$klarna_order->update($update);
			}

		} catch (Exception $e) {
			Logger::addLog('Klarna Checkout: '.htmlspecialchars($e->getMessage()), 1, null, null, null, true);
		}
	}
}