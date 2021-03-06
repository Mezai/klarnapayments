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

class KlarnaPaymentsCheckoutModuleFrontController extends ModuleFrontController
{
	public $display_column_left = false;
	public $display_column_right = false;
	public $ssl = true;


	public function initContent()
	{
		parent::initContent();
		require_once KLARNA_DIRECTORY.'/libs/checkout/Checkout.php';
		if (!$this->context->cookie->__isset('klarna_order_id'))
			Tools::redirect('index.php');

		try
		{
			$country = Tools::strtoupper(Tools::getValue('country'));
			$shared_secret = Configuration::get('KLARNA_SECRET_'.$country.'');

			if ((String)Configuration::get('KLARNA_ENVIRONMENT') === 'live')
				$connector = Klarna_Checkout_Connector::create((String)$shared_secret, Klarna_Checkout_Connector::BASE_URL);
			else
				$connector = Klarna_Checkout_Connector::create((String)$shared_secret, Klarna_Checkout_Connector::BASE_TEST_URL);

			$checkout_id = $this->context->cookie->__get('klarna_order_id');
			$klarnaorder = new Klarna_Checkout_Order($connector, $checkout_id);
			$klarnaorder->fetch();

			if ($klarnaorder['status'] === 'checkout_incomplete')
				Tools::redirect('order-opc');

			$sql = 'SELECT id_order FROM '._DB_PREFIX_.'orders WHERE id_cart='.(int)$klarnaorder['merchant_reference']['orderid1'];
			$result = Db::getInstance()->getRow($sql);

			$snippet = $klarnaorder['gui']['snippet'];
			$this->context->smarty->assign(array(
					'klarna_html' => $snippet,
					'HOOK_ORDER_CONFIRMATION' => $this->displayOrderConfirmation((int)$result['id_order'])

			));

			$this->context->cookie->__unset('klarna_order_id');

		} catch (Klarna_Checkout_ApiErrorException $e) {
			Logger::addLog('Klarna module: '.htmlspecialchars($e->getMessage()));

		}
		$this->setTemplate('checkout-confirmation.tpl');
	}

	public function displayOrderConfirmation($id_order)
	{
		if (Validate::isUnsignedId($id_order))
		{
			$params = array();
			$order = new Order($id_order);
			$currency = new Currency($order->id_currency);
			if (Validate::isLoadedObject($order))
			{
				$params['total_to_pay'] = $order->getOrdersTotalPaid();
				$params['currency'] = $currency->sign;
				$params['objOrder'] = $order;
				$params['currencyObj'] = $currency;
				return Hook::exec('displayOrderConfirmation', $params);
			}
		}
		return false;
	}
}