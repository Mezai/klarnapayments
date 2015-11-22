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
		session_start();
		if (!isset($_SESSION['klarna_order_id']))
			Tools::redirect('index.php');
		try
		{
			$connector = Klarna_Checkout_Connector::create(Configuration::get('KLARNA_SECRET_SE'),
				Klarna_Checkout_Connector::BASE_TEST_URL); 

			

			$checkout_id = $_SESSION['klarna_order_id'];
			$klarnaorder = new Klarna_Checkout_Order($connector, $checkout_id);  
			$klarnaorder->fetch();

			if ($klarnaorder['status'] == 'checkout_incomplete')
			{
				Tools::redirect('order-opc');
			}

			$snippet = $klarnaorder['gui']['snippet'];
			$this->context->smarty->assign(array(
					'klarna_html' => $snippet
				));
			unset($_SESSION['klarna_order_id']);  



		} catch (Klarna_Exception $e) {

		}
		$this->setTemplate('checkout-confirmation.tpl');


	}
}