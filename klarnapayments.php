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

if (!defined('_PS_VERSION_'))
	exit;

require_once(dirname(__FILE__).'/libs/Klarna.php');
require_once(dirname(__FILE__).'/libs/transport/xmlrpc-3.0.0.beta/lib/xmlrpc.inc');
require_once(dirname(__FILE__).'/libs/transport/xmlrpc-3.0.0.beta/lib/xmlrpc_wrappers.inc');

class KlarnaPayments extends PaymentModule
{
	private $html = '';
	private $post_errors = array();
	public $settings;
	public function __construct()
	{
		$this->name = 'klarnapayments';
		$this->tab = 'payments_gateways';
		$this->limited_countries = array('se', 'no', 'fi', 'dk', 'de', 'nl');
		$this->module_key = '9ba314b95673c695df2051398019734c';
		$this->version = '1.0.1';
		$this->author = 'JET';
		$this->need_instance = 1;
		$this->controllers = array('payment', 'verification');
		$this->bootstrap = true;
		$this->currencies = true;
		$this->currencies_mode = 'checkbox';

		$this->settings = array(

	                  "SE"  => array(

	                                       "active" => (int)Configuration::get('ACTIVE_SE'),

	                                       "klarna_eid" => Configuration::get('KLARNA_EID_SE'),

	                                       "klarna_secret" => Configuration::get('KLARNA_SECRET_SE'),

																				 "klarna_part" => (int)Configuration::get('KLARNA_PART_SE'),

																				 "klarna_invoice" => (int)Configuration::get('KLARNA_INVOICE_SE'),

																				 "klarna_checkout" => (int)Configuration::get('KLARNA_CHECKOUT_SE')

	                                       ),

	                  "NO"  => array(

	                                       "active" => (int)Configuration::get('ACTIVE_NO'),

	                                       "klarna_eid" => (String)Configuration::get('KLARNA_EID_NO'),

	                                       "klarna_secret" => (String)Configuration::get('KLARNA_SECRET_NO'),

																				 "klarna_part" => (int)Configuration::get('KLARNA_PART_NO'),

																				 "klarna_invoice" => (int)Configuration::get('KLARNA_INVOICE_NO'),

																				 "klarna_checkout" => (int)Configuration::get('KLARNA_CHECKOUT_NO')

	                                       ),

	                  "FI"  => array(

	                                       "active" => (int)Configuration::get('ACTIVE_FI'),

	                                       "klarna_eid" => (String)Configuration::get('KLARNA_EID_FI'),

	                                       "klarna_secret" => (String)Configuration::get('KLARNA_SECRET_FI'),

																				 "klarna_part" => (int)Configuration::get('KLARNA_PART_FI'),

																				 "klarna_invoice" => (int)Configuration::get('KLARNA_INVOICE_FI'),

																				 "klarna_checkout" => (int)Configuration::get('KLARNA_CHECKOUT_FI')

	                                       ),

	                  "DK"  => array(

	                                       "active" => (int)Configuration::get('ACTIVE_DK'),

	                                       "klarna_eid" => (String)Configuration::get('KLARNA_EID_DK'),

	                                       "klarna_secret" => (String)Configuration::get('KLARNA_SECRET_DK'),

																				 "klarna_part" => (int)Configuration::get('KLARNA_PART_DK'),

																				 "klarna_invoice" => (int)Configuration::get('KLARNA_INVOICE_DK')

	                                       ),

	                  "NL"  => array(

	                                       "active" => (int)Configuration::get('ACTIVE_NL'),

	                                       "klarna_eid" => (String)Configuration::get('KLARNA_EID_NL'),

	                                       "klarna_secret" => (String)Configuration::get('KLARNA_SECRET_NL'),

																				 "klarna_part" => (int)Configuration::get('KLARNA_PART_NL'),

																				 "klarna_invoice" => (int)Configuration::get('KLARNA_INVOICE_NL')



	                                       ),

	                  "DE"  => array(

	                                       "active" => (int)Configuration::get('ACTIVE_DE'),

	                                       "klarna_eid" => (String)Configuration::get('KLARNA_EID_DE'),

	                                       "klarna_secret" => (String)Configuration::get('KLARNA_SECRET_DE'),

																				 "klarna_part" => (int)Configuration::get('KLARNA_PART_DE'),

																				 "klarna_invoice" => (int)Configuration::get('KLARNA_INVOICE_DE'),

																				 "klarna_checkout" => (int)Configuration::get('KLARNA_CHECKOUT_DE')



	                                       ),

	                  "AT"  => array(

	                                       "active" => (int)Configuration::get('ACTIVE_AT'),

	                                       "klarna_eid" => (String)Configuration::get('KLARNA_EID_AT'),

	                                       "klarna_secret" => (String)Configuration::get('KLARNA_SECRET_AT'),

																				 "klarna_part" => (int)Configuration::get('KLARNA_PART_AT'),

																				 "klarna_invoice" => (int)Configuration::get('KLARNA_INVOICE_AT')



	                                       ),

	                  );


		parent::__construct();
		$this->displayName = $this->l('Klarna invoice and part payment');
		$this->description = $this->l('Allows your customers to pay with Klarna invoice and part payment');
		$this->valid = false;
		$this->error = false;
		/**
	 	* Check for curl extension
	 	*
	 	* @return bool result
	 	* @author Johan Tedenmark
	 	*/

		if (!extension_loaded('curl'))
			$this->warning = $this->l('You need to activate curl extension on your server to use this module');

		if (version_compare(phpversion(), '5.2.16', '<'))
			$this->warning = $this->l('You need to have at least PHP version 5.2.16 in order to use Klarna');

	}

	/**
	 * Install the module and add hooks
	 *
	 * @return bool result
	 * @author Johan Tedenmark
	 */

	public function install()
	{
	include_once(_PS_MODULE_DIR_.$this->name.'/klarna_install.php');

	$klarna_install = new KlarnaInstall();

	$klarna_install->createTable();

	$klarna_install->createStatus();

	return parent::install()
	&& $this->registerHook('payment')
	&& $this->registerHook('backOfficeHeader')
	&& $this->registerHook('displayLeftColumn')
	&& $this->registerHook('paymentReturn')
	&& $this->registerHook('displayShoppingCartFooter')
	&& $this->registerHook('displayShoppingCart')
	&& $this->registerHook('displayRightColumnProduct')
	&& $this->registerHook('shoppingCartExtra')
	&& $this->registerHook('displayBeforeCarrier')
	&& $this->registerHook('header');

	}

	public function hookDisplayShoppingCart()
	{
		require_once(dirname(__FILE__).'/classes/KlarnaCheckoutPrestashop.php');

		$cart = $this->context->cart;
		$currency = new Currency((int)$cart->id_currency);
		$kco_products = $cart->getProducts();
		$shipping = $cart->getOrderTotal(true, Cart::ONLY_SHIPPING);
		$carrier = new Carrier((int)$cart->id_carrier);

		foreach ($this->settings as $key => $value) {
		if ($key == 'SE') {
			 if ((int)$value['active'] == 1 && (int)$value['klarna_checkout'] == 1 && $currency->iso_code == 'SEK') {
				 	$this->context->controller->addJS(__PS_BASE_URI__.'modules/klarnapayments/views/js/klarnacheckout.js');
		 			$this->context->controller->addCSS(__PS_BASE_URI__.'modules/klarnapayments/views/css/klarnacheckout.css');
				 	$sweden_active = true;

			 	}
			}
		if ($key == 'NO') {
			if ((int)$value['active'] == 1 && (int)$value['klarna_checkout'] == 1 && $currency->iso_code == 'NOK') {
				$this->context->controller->addJS(__PS_BASE_URI__.'modules/klarnapayments/views/js/klarnacheckout.js');
				$this->context->controller->addCSS(__PS_BASE_URI__.'modules/klarnapayments/views/css/klarnacheckout.css');
				$norway_active = true;
			}
		}

		if ($key == 'FI') {
			if ((int)$value['active'] == 1 && (int)$value['klarna_checkout'] == 1 && $currency->iso_code == 'EUR') {
				$this->context->controller->addJS(__PS_BASE_URI__.'modules/klarnapayments/views/js/klarnacheckout.js');
				$this->context->controller->addCSS(__PS_BASE_URI__.'modules/klarnapayments/views/css/klarnacheckout.css');
				$finland_active = true;
			}
		}

		if ($key == 'DE') {
			if ((int)$value['active'] == 1 && (int)$value['klarna_checkout'] == 1 && $currency->iso_code == 'EUR') {
				$this->context->controller->addJS(__PS_BASE_URI__.'modules/klarnapayments/views/js/klarnacheckout.js');
				$this->context->controller->addCSS(__PS_BASE_URI__.'modules/klarnapayments/views/css/klarnacheckout.css');
				$germany_active = true;
			}
		}
		}
		if ($sweden_active) {
		$new_checkout_order_se = new KlarnaCheckoutPs(Configuration::get('KLARNA_EID_SE'), Configuration::get('KLARNA_SECRET_SE'));
		$snippet = $new_checkout_order_se->createNew('SE', 'SEK', 'sv-se', $cart->getProducts(), $this->context->link->getPageLink('order-opc'), $shipping, $carrier, 25);

		$this->context->smarty->assign(array(
		 'snippet' => $snippet
	 	));

		return $this->display(__FILE__, 'klarnacheckout.tpl');

	} elseif ($norway_active) {
		$new_checkout_order_no = new KlarnaCheckoutPs(Configuration::get('KLARNA_EID_NO'), Configuration::get('KLARNA_SECRET_NO'));
		$snippet = $new_checkout_order_no->createNew('NO', 'NOK', 'nb-no');

		$this->context->smarty->assign(array(
		 'snippet' => $snippet
	 	));

		return $this->display(__FILE__, 'klarnacheckout.tpl');

	} elseif ($finland_active) {
		$new_checkout_order_fi = new KlarnaCheckoutPs(Configuration::get('KLARNA_EID_FI'), Configuration::get('KLARNA_SECRET_FI'));
		$snippet = $new_checkout_order_fi->createNew('NO', 'NOK', 'nb-no');

		$this->context->smarty->assign(array(
		 'snippet' => $snippet
	 	));

		return $this->display(__FILE__, 'klarnacheckout.tpl');


	} elseif ($germany_active) {
		$new_checkout_order_de = new KlarnaCheckoutPs(Configuration::get('KLARNA_EID_DE'), Configuration::get('KLARNA_SECRET_DE'));
		$snippet = $new_checkout_order_de->createNew('NO', 'NOK', 'nb-no');

		$this->context->smarty->assign(array(
		 'snippet' => $snippet
	 	));

		return $this->display(__FILE__, 'klarnacheckout.tpl');

		}

	}
	/**
	 * Uninstall the module and delete configuration
	 *
	 * @return bool result
	 * @author Johan Tedenmark
	 */

	public function uninstall()
	{
		include_once(_PS_MODULE_DIR_.$this->name.'/klarna_install.php');

		$klarna_uninstall = new KlarnaInstall();
		$klarna_uninstall->deleteConfiguration();
		return parent::uninstall();

	}

	/**
	* Validate merchant configuration
	*
	* @return array $post_errors
	* @author Johan Tedenmark
	*/

	private function postValidation()
	{
		if (Tools::isSubmit('saveBtn'))
		{
				foreach ($this->settings as $key => $value) {
					if ((int)$value['active'] == 1) {
						if (!Tools::getValue('KLARNA_EID_'.$key))
								$this->post_errors[] = $this->l('You need to set the Klarna EID for country: '.$key);
						elseif (!Tools::getValue('KLARNA_SECRET_'.$key))
								$this->post_errors[] = $this->l('You need to set the Klarna secret for country: '.$key);
					}
				}
		}
	}



	/**
	* Update merchant configuration
	*
	* @return display confirmation backoffice
	* @author Johan Tedenmark
	*/

	private function postProcess()
	{
		if (Tools::isSubmit('saveBtn'))
		{


			Configuration::updateValue('KLARNA_EID_SE', Tools::getValue('KLARNA_EID_SE'));
			Configuration::updateValue('KLARNA_SECRET_SE', Tools::getValue('KLARNA_SECRET_SE'));
			Configuration::updateValue('KLARNA_EID_NO', Tools::getValue('KLARNA_EID_NO'));
			Configuration::updateValue('KLARNA_SECRET_NO', Tools::getValue('KLARNA_SECRET_NO'));
			Configuration::updateValue('KLARNA_EID_DK', Tools::getValue('KLARNA_EID_DK'));
			Configuration::updateValue('KLARNA_SECRET_DK', Tools::getValue('KLARNA_SECRET_DK'));
			Configuration::updateValue('KLARNA_EID_FI', Tools::getValue('KLARNA_EID_FI'));
			Configuration::updateValue('KLARNA_SECRET_FI', Tools::getValue('KLARNA_SECRET_FI'));
			Configuration::updateValue('KLARNA_EID_DE', Tools::getValue('KLARNA_EID_DE'));
			Configuration::updateValue('KLARNA_SECRET_DE', Tools::getValue('KLARNA_SECRET_DE'));
			Configuration::updateValue('KLARNA_EID_AT', Tools::getValue('KLARNA_EID_AT'));
			Configuration::updateValue('KLARNA_SECRET_AT', Tools::getValue('KLARNA_SECRET_AT'));
			Configuration::updateValue('KLARNA_EID_NL', Tools::getValue('KLARNA_EID_NL'));
			Configuration::updateValue('KLARNA_SECRET_NL', Tools::getValue('KLARNA_SECRET_NL'));
			Configuration::updateValue('ACTIVE_SE', Tools::getValue('ACTIVE_SE'));
			Configuration::updateValue('ACTIVE_NO', Tools::getValue('ACTIVE_NO'));
			Configuration::updateValue('ACTIVE_FI', Tools::getValue('ACTIVE_FI'));
			Configuration::updateValue('ACTIVE_DK', Tools::getValue('ACTIVE_DK'));
			Configuration::updateValue('ACTIVE_DE', Tools::getValue('ACTIVE_DE'));
			Configuration::updateValue('ACTIVE_AT', Tools::getValue('ACTIVE_AT'));
			Configuration::updateValue('ACTIVE_NL', Tools::getValue('ACTIVE_NL'));
			Configuration::updateValue('KLARNA_PART_SE', Tools::getValue('KLARNA_PART_SE'));
			Configuration::updateValue('KLARNA_INVOICE_SE', Tools::getValue('KLARNA_INVOICE_SE'));
			Configuration::updateValue('KLARNA_PART_NO', Tools::getValue('KLARNA_PART_NO'));
			Configuration::updateValue('KLARNA_INVOICE_NO', Tools::getValue('KLARNA_INVOICE_NO'));
			Configuration::updateValue('KLARNA_PART_FI', Tools::getValue('KLARNA_PART_FI'));
			Configuration::updateValue('KLARNA_INVOICE_FI', Tools::getValue('KLARNA_INVOICE_FI'));
			Configuration::updateValue('KLARNA_PART_DK', Tools::getValue('KLARNA_PART_DK'));
			Configuration::updateValue('KLARNA_INVOICE_DK', Tools::getValue('KLARNA_INVOICE_DK'));
			Configuration::updateValue('KLARNA_PART_DE', Tools::getValue('KLARNA_PART_DE'));
			Configuration::updateValue('KLARNA_INVOICE_DE', Tools::getValue('KLARNA_INVOICE_DE'));
			Configuration::updateValue('KLARNA_PART_NL', Tools::getValue('KLARNA_PART_NL'));
			Configuration::updateValue('KLARNA_INVOICE_NL', Tools::getValue('KLARNA_INVOICE_NL'));
			Configuration::updateValue('KLARNA_PART_AT', Tools::getValue('KLARNA_PART_AT'));
			Configuration::updateValue('KLARNA_INVOICE_AT', Tools::getValue('KLARNA_INVOICE_AT'));
			Configuration::updateValue('KLARNA_CHECKOUT_SE', Tools::getValue('KLARNA_CHECKOUT_SE'));
			Configuration::updateValue('KLARNA_CHECKOUT_NO', Tools::getValue('KLARNA_CHECKOUT_NO'));
			Configuration::updateValue('KLARNA_CHECKOUT_FI', Tools::getValue('KLARNA_CHECKOUT_FI'));
			Configuration::updateValue('KLARNA_CHECKOUT_DE', Tools::getValue('KLARNA_CHECKOUT_DE'));
			Configuration::updateValue('KLARNA_ENVIRONMENT', Tools::getValue('KLARNA_ENVIRONMENT'));
			Configuration::updateValue('KLARNA_CHECKOUT_COLOR_BUTTON', Tools::getValue('KLARNA_CHECKOUT_COLOR_BUTTON'));
			Configuration::updateValue('KLARNA_CHECKOUT_COLOR_BUTTON_TEXT', Tools::getValue('KLARNA_CHECKOUT_COLOR_BUTTON_TEXT'));
			Configuration::updateValue('KLARNA_CHECKOUT_COLOR_CHECKBOX', Tools::getValue('KLARNA_CHECKOUT_COLOR_CHECKBOX'));
			Configuration::updateValue('KLARNA_CHECKOUT_COLOR_CHECKBOX_CHECKMARK', Tools::getValue('KLARNA_CHECKOUT_COLOR_CHECKBOX_CHECKMARK'));
			Configuration::updateValue('KLARNA_CHECKOUT_COLOR_LINK', Tools::getValue('KLARNA_CHECKOUT_COLOR_LINK'));
			Configuration::updateValue('KLARNA_CHECKOUT_COLOR_HEADER', Tools::getValue('KLARNA_CHECKOUT_COLOR_HEADER'));


			Configuration::updateValue('klarna_fetch_address', Tools::getValue('klarna_fetch_address'));
			Configuration::updateValue('KLARNA_INVOICE_FEE_REF', Tools::getValue('KLARNA_INVOICE_FEE_REF'));
			Configuration::updateValue('KLARNA_INVOICE_FEE', Tools::getValue('KLARNA_INVOICE_FEE'));

		}


		$this->html .= $this->displayConfirmation($this->l('Settings updated'));


	}

	/**
	* Hook header
	*
	* @return
	* @author Johan Tedenmark
	*/

	public function hookDisplayHeader()
	{
		$this->context->controller->addCSS($this->_path.'/views/css/klarnapayments.css', 'all');

		return '<script src="https://cdn.klarna.com/public/kitt/core/v1.0/js/klarna.min.js"></script>
				<script src="https://cdn.klarna.com/public/kitt/toc/v1.1/js/klarna.terms.min.js"></script>';
	}

	/**
	* Function getLocation
	*
	* @return lang code for Klarna
	* @author Johan Tedenmark
	*/

	public function getLocale()
	{
		switch (Language::getIsoById($this->context->language->id))
		{
			case 'sv':
				return 'sv_SE';
			case 'no':
				return 'nb_NO';
			case 'fi':
				return 'fi_FI';
			case 'da':
				return 'da_DK';
			case 'de':
				return 'de_DE';
			case 'nl':
				return 'nl_NL';
			default:
				return 'sv_SE';

		}
	}
	/**
	* Hook backoffice header
	*
	* @return html output
	* @author Johan Tedenmark
	*/

	public function hookBackOfficeHeader()
	{
		$this->context->controller->addCSS($this->_path.'views/css/klarnapayments_bo.css', 'all');


		if (!Tools::getIsset('vieworder') || !Tools::getIsset('id_order'))
			return;

		if (Db::getInstance()->getValue('SELECT `module` FROM '._DB_PREFIX_.'orders WHERE id_order = '.(int)Tools::getValue('id_order')) == $this->name)
		{
			if (Tools::isSubmit('ActivateKlarnaInvoice'))
			{
				$order = new Order(Tools::getValue('id_order'));
				$order_reference = $order->{'reference'};
				$order_number = $order->{'id'};
				$klarna_order_id = Tools::getValue('id_order');
				$klarna_reservation = $this->getReservationNum($klarna_order_id);

				$this->activatePayment($klarna_reservation, $klarna_order_id, $order_number, $order_reference);
			}

			if (Tools::isSubmit('CancelKlarnaInvoice'))
			{
				$klarna_order_id = Tools::getValue('id_order');
				$klarna_reservation = $this->getReservationNum($klarna_order_id);

				$this->cancelPayment($klarna_reservation, $klarna_order_id);
			}

			if (Tools::isSubmit('RefundAllKlarnaInvoice'))
			{
				$klarna_order_id = Tools::getValue('id_order');
				$klarna_invoice_number = $this->getInvoiceNum($klarna_order_id);

				$this->refundAll($klarna_invoice_number, $klarna_order_id);
			}

			if (Tools::isSubmit('RefundPartKlarnaInvoice'))
			{
				$klarna_order_id = Tools::getValue('id_order');
				$klarna_invoice_number = $this->getInvoiceNum($klarna_order_id);
				$credit_quantity = Tools::getValue('klarna_quantity');
				$credit_article = Tools::getValue('klarna_article');

				$this->refundPart($klarna_invoice_number, $klarna_order_id, $credit_quantity, $credit_article);

			}
			if (Tools::isSubmit('CheckKlarnaOrderStatus'))
			{
				$klarna_order_id = Tools::getValue('id_order');
				$klarna_reservation = $this->getReservationNum($klarna_order_id);

				$this->checkStatus($klarna_reservation, $klarna_order_id);
			}

			if (Tools::isSubmit('ResendKlarnaInvoice'))
			{
				$klarna_order_id = Tools::getValue('id_order');
				$klarna_invoice_number = $this->getInvoiceNum($klarna_order_id);
				$this->resendKlarnaInvoice($klarna_invoice_number, $klarna_order_id);
			}

			$output = '
			<script type="text/javascript">
			$(document).ready(function() {
			var appendEl;
			if ($(\'select[name=id_order_state]\').is(":visible")) {
			appendEl = $(\'select[name=id_order_state]\').parents(\'form\').after($(\'<div/>\'));
			} else {
			appendEl = $("#status");
			}

			$(\'<fieldset'.(_PS_VERSION_ < 1.5 ? ' style="width: 400px;"' : '').'>'.
					'<legend><img src="../img/admin/money.gif" alt=""/>'.$this->l('Klarna payment operations').'</legend>';

			$order = new Order(Tools::getValue('id_order'));

			$order_state_pending = array('Klarna avvaktande', 'Klarna ventende', 'Klarna verserende', 'Klarna odotettaessa', 'Klarna schwebend', 'Klarna hangende', 'Klarna pending');
			$order_state_authorized = array('Klarna reserverad', 'Klarna autorisert', 'Klarna autoriseret', 'Klarna sallittua', 'Klarna zugelassen', 'Klarna geautoriseerd', 'Klarna authorized');
			$order_state_activated = array('Klarna aktiverad', 'Klarna aktivert', 'Klarna aktiveret', 'Klarna aktivoitu', 'Klarna aktiviert', 'Klarna geactiveerd', 'Klarna activated');

			$current_order_state = $order->getCurrentOrderState();
			$name_array = $current_order_state->{'name'};

			$array_keys = array_keys($name_array);

			$current_order_state_name = $name_array[$array_keys[0]];

			if (in_array($current_order_state_name, $order_state_pending))
			{
				//display check order status

				$output .= '<form action="" method="post"><p style="font-weight: bold;">'.$this->l('Check status').'</p>'.
					'<input type="submit" style"margin-left:10px; width:120px;" class="btn btn-primary"'.
							'onclick="return confirm(\\\''.addslashes($this->l('Check status?')).'\\\');"'.
							'value="'.$this->l('Check status').'" name="CheckKlarnaOrderStatus" /></form></fieldset>';
			}

			if (in_array($current_order_state_name, $order_state_authorized))
			{
				//display activation and cancelation
				$output .= '<form action="" method="post"><p style="font-weight: bold;">'.$this->l('Activate invoice').'</p>'.
					'<input type="submit" style="margin-left:10px; width:120px;" class="btn btn-primary"'.
										'onclick="return confirm(\\\''.addslashes($this->l('Are you sure you want to activate the invoice?')).'\\\');"'.
						'value="'.$this->l('Activate invoice').'" name="ActivateKlarnaInvoice" /></form>';

				$output .= '<form action="" method="post"><p style="font-weight: bold;">'.$this->l('Cancel invoice').'</p>'.
					'<input type="submit" style="margin-left:10px; width:120px;" class="btn btn-primary"'.
										'onclick="return confirm(\\\''.addslashes($this->l('Are you sure you want to cancel the invoice?')).'\\\');"'.
						'value="'.$this->l('Cancel invoice').'" name="CancelKlarnaInvoice" /></form>';

			}

			if (in_array($current_order_state_name, $order_state_activated))
			{
				$output .= '<form action="" method="post"><p style="font-weight: bold;">'.$this->l('Refund full invoice').'</p>'.
					'<input type="submit" style"margin-left:10px; width:120px;" class="btn btn-primary"'.
							'onclick="return confirm(\\\''.addslashes($this->l('Are you sure you want to credit the full invoice?')).'\\\');"'.
							'value="'.$this->l('Credit invoice').'" name="RefundAllKlarnaInvoice" /></form>';

				$output .= '<form action="" method="post"><p style="font-weight: bold;">'.$this->l('Credit partial articles on invoice').'</p>'.
				'<label for="klarna_article">'.$this->l('Product id:').'</label>'.

				'<input type="text" title="Klarna partial credit" value=""'.
							'name="klarna_article" style="display: inline-block; width: 120px; margin-right:10px;" />'.
				'<label for="klarna_quantity">'.$this->l('Quantity:').'</label>'.

				'<input type="text" name="klarna_quantity" style="display: inline-block; width: 120px; margin-right:10px;" />'.

				'<input type="submit" style="margin-left:10px; width:120px;" class="btn btn-primary"'.

									'onclick="return confirm(\\\''.addslashes($this->l('Are you sure you want to credit the article from invoice?')).'\\\');"'.
											'value="'.$this->l('Credit article').'" name="RefundPartKlarnaInvoice" /></form>';

				$output .= '<form action="" method="post"><p style="font-weight: bold;">'.$this->l('Resend the invoice').'</p>'.
					'<input type="submit" style"width:120px;" class="btn btn-primary"'.
						'onclick="return confirm(\\\''.addslashes($this->l('Resend the invoice?')).'\\\');"'.
						'value="'.$this->l('Resend invoice').'" name="ResendKlarnaInvoice" /></form></fieldset>';
			}

			$output .= '<a href="'.$this->getInvoiceURI(Tools::getValue('id_order')).'" target="_blank">'.$this->l('Link to the invoice valid for 30 days').'</a>\').appendTo(appendEl);



			});
			</script>';

			return $output;
		}
	}

	public function showPaymentOption($country_id, $payment_type)
	{
		if (!is_string($country_id))
		return;

 		$country_code = Tools::strtoupper($country_id);
		$merchant_settings = $this->getKlarnaSettings($country_code);
		if ($payment_type == 'part') {
			if ((int)$merchant_settings['active'] == 1 && (int)$merchant_settings['klarna_part'] == 1)
					 return true;
					 else
					 return false;
		}
		if ($payment_type == 'invoice')
		{
			if ((int)$merchant_settings['active'] == 1 && (int)$merchant_settings['klarna_invoice'] == 1)
			return true;
			else
			return false;

		}

	}

	public function getKlarnaSettings($country)
  {

  	if (!is_string($country))

    return;


  	foreach ($this->settings as $key => $value)
    {
           if ($key == $country) {

            	return $value;
           }

        }
     }

	/**
	* Hook order confirmation
	*
	* @return template order confirmation
	* @author Johan Tedenmark
	*/

	public function hookPaymentReturn()
	{
		if (!$this->active)
			return;

		return $this->display(__FILE__, 'payment_return.tpl');
	}

	/**
	* Hook payment
	*
	* @return template payment
	* @author Johan Tedenmark
	*/

	public function hookPayment($params)
	{
		if (!$this->active)
			return;

			if ($this->showPaymentOption(Country::getIsoById($this->context->country->id), "invoice"))
			{
				$invoice = true;
			} else {
				$invoice = false;
			}

			if ($this->showPaymentOption(Country::getIsoById($this->context->country->id), "part"))
			{
				$part = true;
			} else {
				$part = false;
			}

			$cart = $this->context->cart;

			$this->context->smarty->assign(array(
				'checkLocale' => $this->checkLocale(Country::getIsoById($this->context->country->id), Tools::strtoupper($this->context->currency->iso_code), Tools::strtolower($this->context->language->iso_code)),
				'nbProducts' => $cart->nbProducts(),
				'cust_currency' => $cart->id_currency,
				'currencies' => $this->getCurrency((int)$cart->id_currency),
				'total' => $cart->getOrderTotal(true, Cart::BOTH),
				'klarna_pno_placeholder' => $this->getPlaceholderPno(),
				'klarna_pno_pattern' => $this->getPatternPno(),
				'klarna_language' => $this->getLocale(),
				'klarna_device' => $this->checkMobile(),
				'klarna_merchant_eid' => '1736',
				'klarna_invoice_sum' => $this->getInvoiceFee(),
				'klarna_country' => Country::getIsoById($this->context->country->id)
			));

			$this->context->controller->addCSS(__PS_BASE_URI__.'modules/klarnapayments/views/css/payment_invoice.css', 'all');
			$this->context->controller->addJS(__PS_BASE_URI__.'modules/klarnapayments/views/js/payment_invoice.js');


			// $klarna_special_pclass = $this->getKlarnaPClasses('SPECIAL');
			//
			// if (!empty($klarna_special_pclass))
			// {
			// 	foreach ($klarna_special_pclass as $value)
			// 	{
			// 		$this->context->smarty->assign(array(
			// 		'klarna_special_id' => $value->getId(),
			// 		'klarna_special_description' => $value->getDescription(),
			// 		'klarna_special_invfee' => $value->getInvoiceFee(),
			// 		'klarna_special_interest' => $value->getInterestRate(),
			// 		'klarna_special_startfee' => $value->getStartFee(),
			// 		'klarna_special_months' => $value->getMonths(),
			// 		'klarna_special_minamount' => $value->getMinAmount(),
			// 		'klarna_special_country' => $value->getCountry(),
			// 		'klarna_special_total_credit' => $this->module->klarnaCalculateTotalCredit($cart->getOrderTotal(true, Cart::BOTH), KlarnaFlags::CHECKOUT_PAGE, $value->getId())
			//
			// 		));
			// 	}
			// }

			$this->smarty->assign(array(
			'fee' => $this->getInvoiceFee(),
			'klarna_country' => Country::getIsoById($this->context->country->id),
			'lang_code' => Tools::strtolower($this->getLocale()),
			'showinvoice' => $invoice,
			'showpart' => $part,
			'this_path' => $this->_path,
			'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/'
		));
		return $this->display(__FILE__, 'payment.tpl');
	}
	/**
	* Hook left column
	*
	* @return template payment
	* @author Johan Tedenmark
	*/

	public function hookLeftColumn()
	{
		$this->context->smarty->assign(array(
			'klarna_merchant' => Configuration::get('klarna_eid'),
			'klarna_lang' => Tools::strtolower($this->getLocale())
			));

		return $this->display(__FILE__, 'klarnapaymentinfo.tpl');
	}


	/**
	* Hook right column
	*
	* @return function hookLeftColumn
	* @author Johan Tedenmark
	*/

	public function hookRightColumn()
	{
		return $this->hookLeftColumn();
	}

	/**
	* Display configuration
	*
	* @return renderForm
	* @author Johan Tedenmark
	*/
	private function fetchPClass()
	{
				if (Tools::isSubmit('pclass_se')) {
				return $this->updatePClasses('SE', 'SV', 'SEK');
				}	elseif (Tools::isSubmit('pclass_no')) {
				return $this->updatePClasses('NO', 'NB', 'NOK');
				} elseif (Tools::isSubmit('pclass_dk')) {
				return $this->updatePClasses('DK','DA', 'DKK');
				} elseif (Tools::isSubmit('pclass_fi')) {
				return $this->updatePClasses('FI','FI','EUR');
				} elseif (Tools::isSubmit('pclass_de')) {
				return $this->updatePClasses('DE','DE','EUR');
				} elseif (Tools::isSubmit('pclass_nl')) {
				return $this->updatePClasses('NL','NL','EUR');
			}

	}

	public function getContent()
	{

		if (Tools::isSubmit('pclass_se') || Tools::isSubmit('pclass_no') || Tools::isSubmit('pclass_dk') || Tools::isSubmit('pclass_fi') || Tools::isSubmit('pclass_de') || Tools::isSubmit('pclass_nl'))
		$this->fetchPClass();

		if (Tools::isSubmit('delete_pclasses'))
		$this->deletePClasses();

		if (Tools::isSubmit('saveBtn'))
		{
			$this->postValidation();
			if (!count($this->post_errors))
				$this->postProcess();
			else
				foreach ($this->post_errors as $err)
					$this->html .= $this->displayError($err);

		}




		$this->html .= '<br />';
		//setting the admin template
		$this->context->smarty->assign(array('module_dir' => $this->_path));

		$this->context->controller->addJS($this->_path.'views/js/klarnabackoffice.js');

		$this->html .= $this->context->smarty->fetch($this->local_path.'/views/templates/admin/admin.tpl');

		$this->html .= $this->renderForm();

		return $this->html;
	}


	private function deletePClasses()
	{
		foreach ($this->settings as $key => $value) {
			if ((int)$value['active'] == 1) {
				if (isset($value['klarna_eid']) && isset($value['klarna_secret'])) {
					$klarna = $this->getConfiguration($key);
				}
			}
		}

		try {

		$klarna->clearPClasses();

		$this->html .= $this->displayConfirmation($this->l('PClasses deleted'));

		} catch(Exception $e) {
		Logger::addLog('Klarna module: PClass call failed with message: '.$e->getMessage().' and response code: '.$e->getCode());
		$this->html .= $this->displayError('PClass call failed : see log for error message');
		}
	}

	/**
	* Render admin form
	*
	* @return helper generateForm
	* @author Johan Tedenmark
	*/

	public function renderForm()
	{
		$klarna_mode = array(
			array(
				'id_option' => 'live',
				'name' => 'Live'
				),
			array(
				'id_option' => 'beta',
				'name' => 'Beta'
				),
		);

		$klarna_invoice_fee = array(
			array(
				'id_option' => 0,
				'name' => 'Part payments'
				),
			array(
				'id_option' => 1,
				'name' => 'Invoice payments'
				),
			array(
				'id_option' => 2,
				'name' => 'Both'
				),
			);

		$klarna_invoice_method = array(
			array(
			'id_option' => 1,
			'name' => 'E-mail'
			),
			array(
			'id_option' => 0,
			'name' => 'Post'
			),
		);

		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Klarna settings'),
					'icon' => 'icon-cogs'
					),
				'tabs' => array(
				'general' => $this->l('General settings'),
				'sweden' => $this->l('Sweden'),
				'finland' => $this->l('Finland'),
				'denmark' => $this->l('Denmark'),
				'germany' => $this->l('Germany'),
				'norway' => $this->l('Norway'),
				'netherlands' => $this->l('Netherlands'),
				'austria' => $this->l('Austria'),
			),
			'input' => array(
				array(
				'type' => 'radio',
				'is_bool' => true,
				'label' => $this->l('Activate Sweden?'),
				'desc' => $this->l('Select yes to activate Sweden'),
				'name' => 'ACTIVE_SE',
				'tab' => 'sweden',
				'values' => array(
					array(
						'id' => 'active_on',
						'value' => 1,
						'label' => $this->l('Yes')
					),
					array(
						'id' => 'active_off',
						'value' => 0,
						'label' => $this->l('No')
						)
					),
				),
				array(
				'type' => 'text',
				'label' => $this->l('Klarna EID'),
				'desc' => $this->l('Fill in the merchant id from Klarna'),
				'name' => 'KLARNA_EID_SE',
				'tab' => 'sweden',
				'required' => true,
				'class' => 'fixed-width-lg'
				),
				array(
				'type' => 'text',
				'label' => $this->l('Klarna secret'),
				'desc' => $this->l('Fill in the Klarna secret'),
				'name' => 'KLARNA_SECRET_SE',
				'tab' => 'sweden',
				'required' => true,
				'class' => 'fixed-width-lg'
				),
				array(
                    'type' => 'html',
                    'name' => 'html_data',
										'tab' => 'sweden',
                    'html_content' => '<button type="submit" class="btn btn-default" name="pclass_se">Update PClasses</button>'
                ),
				array(
				'type' => 'radio',
				'label' => $this->l('Activate invoice payments?'),
				'tab' => 'sweden',
				'desc' => $this->l('Select if you wish to offer invoice payments'),
				'name' => 'KLARNA_INVOICE_SE',
				'values' => array(
					array(
						'id' => 'active_on',
						'value' => 1,
						'label' => $this->l('Yes')
					),
					array(
						'id' => 'active_off',
						'value' => 0,
						'label' => $this->l('No')
						)
					),
				),
				array(
				'type' => 'radio',
				'label' => $this->l('Activate part payments?'),
				'tab' => 'sweden',
				'desc' => $this->l('Select if you wish to offer part payments'),
				'name' => 'KLARNA_PART_SE',
				'values' => array(
					array(
						'id' => 'active_on',
						'value' => 1,
						'label' => $this->l('Yes')
					),
					array(
						'id' => 'active_off',
						'value' => 0,
						'label' => $this->l('No')
						)
					),
				),
				array(
				'type' => 'radio',
				'label' => $this->l('Activate klarna checkout?'),
				'tab' => 'sweden',
				'desc' => $this->l('Select if you wish to offer klarna checkout'),
				'name' => 'KLARNA_CHECKOUT_SE',
				'values' => array(
					array(
						'id' => 'active_on',
						'value' => 1,
						'label' => $this->l('Yes')
					),
					array(
						'id' => 'active_off',
						'value' => 0,
						'label' => $this->l('No')
						)
					),
				),
				array(
				'type' => 'radio',
				'is_bool' => true,
				'label' => $this->l('Activate Norway?'),
				'desc' => $this->l('Select yes to activate Norway'),
				'name' => 'ACTIVE_NO',
				'tab' => 'norway',
				'values' => array(
					array(
						'id' => 'active_on',
						'value' => 1,
						'label' => $this->l('Yes')
					),
					array(
						'id' => 'active_off',
						'value' => 0,
						'label' => $this->l('No')
						)
					),
				),
				array(
				'type' => 'text',
				'label' => $this->l('Klarna EID'),
				'desc' => $this->l('Fill in the merchant id from Klarna'),
				'name' => 'KLARNA_EID_NO',
				'tab' => 'norway',
				'required' => true,
				'class' => 'fixed-width-lg'
				),
				array(
				'type' => 'text',
				'label' => $this->l('Klarna secret'),
				'desc' => $this->l('Fill in the Klarna secret'),
				'name' => 'KLARNA_SECRET_NO',
				'tab' => 'norway',
				'required' => true,
				'class' => 'fixed-width-lg'
				),
				array(
                    'type' => 'html',
                    'name' => 'html_data',
										'tab' => 'norway',
                    'html_content' => '<button type="submit" class="btn btn-default" name="pclass_no">Update PClasses</button>'
                ),
				array(
				'type' => 'radio',
				'label' => $this->l('Activate invoice payments?'),
				'tab' => 'norway',
				'desc' => $this->l('Select if you wish to offer invoice payments'),
				'name' => 'KLARNA_INVOICE_NO',
				'values' => array(
					array(
						'id' => 'active_on',
						'value' => 1,
						'label' => $this->l('Yes')
					),
					array(
						'id' => 'active_off',
						'value' => 0,
						'label' => $this->l('No')
						)
					),
				),
				array(
				'type' => 'radio',
				'label' => $this->l('Activate part payments?'),
				'tab' => 'norway',
				'desc' => $this->l('Select if you wish to offer part payments'),
				'name' => 'KLARNA_PART_NO',
				'values' => array(
					array(
						'id' => 'active_on',
						'value' => 1,
						'label' => $this->l('Yes')
					),
					array(
						'id' => 'active_off',
						'value' => 0,
						'label' => $this->l('No')
						)
					),
				),
				array(
				'type' => 'radio',
				'label' => $this->l('Activate klarna checkout?'),
				'tab' => 'norway',
				'desc' => $this->l('Select if you wish to offer klarna checkout'),
				'name' => 'KLARNA_CHECKOUT_NO',
				'values' => array(
					array(
						'id' => 'active_on',
						'value' => 1,
						'label' => $this->l('Yes')
					),
					array(
						'id' => 'active_off',
						'value' => 0,
						'label' => $this->l('No')
						)
					),
				),
				array(
				'type' => 'radio',
				'is_bool' => true,
				'label' => $this->l('Activate Denmark?'),
				'desc' => $this->l('Select yes to activate Denmark'),
				'name' => 'ACTIVE_DK',
				'tab' => 'denmark',
				'values' => array(
					array(
						'id' => 'active_on',
						'value' => 1,
						'label' => $this->l('Yes')
					),
					array(
						'id' => 'active_off',
						'value' => 0,
						'label' => $this->l('No')
						)
					),
				),
				array(
				'type' => 'text',
				'label' => $this->l('Klarna EID'),
				'desc' => $this->l('Fill in the merchant id from Klarna'),
				'name' => 'KLARNA_EID_DK',
				'tab' => 'denmark',
				'required' => true,
				'class' => 'fixed-width-lg'
				),
				array(
				'type' => 'text',
				'label' => $this->l('Klarna secret'),
				'desc' => $this->l('Fill in the Klarna secret'),
				'name' => 'KLARNA_SECRET_DK',
				'tab' => 'denmark',
				'required' => true,
				'class' => 'fixed-width-lg'
				),
				array(
                    'type' => 'html',
                    'name' => 'html_data',
										'tab' => 'denmark',
                    'html_content' => '<button type="submit" class="btn btn-default" name="pclass_dk">Update PClasses</button>'
                ),
				array(
				'type' => 'radio',
				'label' => $this->l('Activate invoice payments?'),
				'tab' => 'denmark',
				'desc' => $this->l('Select if you wish to offer invoice payments'),
				'name' => 'KLARNA_INVOICE_DK',
				'values' => array(
					array(
						'id' => 'active_on',
						'value' => 1,
						'label' => $this->l('Yes')
					),
					array(
						'id' => 'active_off',
						'value' => 0,
						'label' => $this->l('No')
						)
					),
				),
				array(
				'type' => 'radio',
				'label' => $this->l('Activate part payments?'),
				'tab' => 'denmark',
				'desc' => $this->l('Select if you wish to offer part payments'),
				'name' => 'KLARNA_PART_DK',
				'values' => array(
					array(
						'id' => 'active_on',
						'value' => 1,
						'label' => $this->l('Yes')
					),
					array(
						'id' => 'active_off',
						'value' => 0,
						'label' => $this->l('No')
						)
					),
				),
				array(
				'type' => 'radio',
				'is_bool' => true,
				'label' => $this->l('Activate Germany?'),
				'desc' => $this->l('Select yes to activate Germany'),
				'name' => 'ACTIVE_DE',
				'tab' => 'germany',
				'values' => array(
					array(
						'id' => 'active_on',
						'value' => 1,
						'label' => $this->l('Yes')
					),
					array(
						'id' => 'active_off',
						'value' => 0,
						'label' => $this->l('No')
						)
					),
				),
				array(
				'type' => 'text',
				'label' => $this->l('Klarna EID'),
				'desc' => $this->l('Fill in the merchant id from Klarna'),
				'name' => 'KLARNA_EID_DE',
				'tab' => 'germany',
				'required' => true,
				'class' => 'fixed-width-lg'
				),
				array(
				'type' => 'text',
				'label' => $this->l('Klarna secret'),
				'desc' => $this->l('Fill in the Klarna secret'),
				'name' => 'KLARNA_SECRET_DE',
				'tab' => 'germany',
				'required' => true,
				'class' => 'fixed-width-lg'
				),
				array(
                    'type' => 'html',
                    'name' => 'html_data',
										'tab' => 'germany',
                    'html_content' => '<button type="submit" class="btn btn-default" name="pclass_de">Update PClasses</button>'
                ),
				array(
				'type' => 'radio',
				'label' => $this->l('Activate invoice payments?'),
				'tab' => 'germany',
				'desc' => $this->l('Select if you wish to offer invoice payments'),
				'name' => 'KLARNA_INVOICE_DE',
				'values' => array(
					array(
						'id' => 'active_on',
						'value' => 1,
						'label' => $this->l('Yes')
					),
					array(
						'id' => 'active_off',
						'value' => 0,
						'label' => $this->l('No')
						)
					),
				),
				array(
				'type' => 'radio',
				'label' => $this->l('Activate part payments?'),
				'tab' => 'germany',
				'desc' => $this->l('Select if you wish to offer part payments'),
				'name' => 'KLARNA_PART_DE',
				'values' => array(
					array(
						'id' => 'active_on',
						'value' => 1,
						'label' => $this->l('Yes')
					),
					array(
						'id' => 'active_off',
						'value' => 0,
						'label' => $this->l('No')
						)
					),
				),
				array(
				'type' => 'radio',
				'label' => $this->l('Activate klarna checkout?'),
				'tab' => 'germany',
				'desc' => $this->l('Select if you wish to offer klarna checkout'),
				'name' => 'KLARNA_CHECKOUT_DE',
				'values' => array(
					array(
						'id' => 'active_on',
						'value' => 1,
						'label' => $this->l('Yes')
					),
					array(
						'id' => 'active_off',
						'value' => 0,
						'label' => $this->l('No')
						)
					),
				),
				array(
				'type' => 'radio',
				'is_bool' => true,
				'label' => $this->l('Activate Austria?'),
				'desc' => $this->l('Select yes to activate Austria'),
				'name' => 'ACTIVE_AT',
				'tab' => 'austria',
				'values' => array(
					array(
						'id' => 'active_on',
						'value' => 1,
						'label' => $this->l('Yes')
					),
					array(
						'id' => 'active_off',
						'value' => 0,
						'label' => $this->l('No')
						)
					),
				),
				array(
				'type' => 'text',
				'label' => $this->l('Klarna EID'),
				'desc' => $this->l('Fill in the merchant id from Klarna'),
				'name' => 'KLARNA_EID_AT',
				'tab' => 'austria',
				'required' => true,
				'class' => 'fixed-width-lg'
				),
				array(
				'type' => 'text',
				'label' => $this->l('Klarna secret'),
				'desc' => $this->l('Fill in the Klarna secret'),
				'name' => 'KLARNA_SECRET_AT',
				'tab' => 'austria',
				'required' => true,
				'class' => 'fixed-width-lg'
				),
				array(
				'type' => 'radio',
				'label' => $this->l('Activate invoice payments?'),
				'tab' => 'austria',
				'desc' => $this->l('Select if you wish to offer invoice payments'),
				'name' => 'KLARNA_INVOICE_AT',
				'values' => array(
					array(
						'id' => 'active_on',
						'value' => 1,
						'label' => $this->l('Yes')
					),
					array(
						'id' => 'active_off',
						'value' => 0,
						'label' => $this->l('No')
						)
					),
				),
				array(
				'type' => 'radio',
				'label' => $this->l('Activate part payments?'),
				'tab' => 'austria',
				'desc' => $this->l('Select if you wish to offer part payments'),
				'name' => 'KLARNA_PART_AT',
				'values' => array(
					array(
						'id' => 'active_on',
						'value' => 1,
						'label' => $this->l('Yes')
					),
					array(
						'id' => 'active_off',
						'value' => 0,
						'label' => $this->l('No')
						)
					),
				),
				array(
				'type' => 'radio',
				'is_bool' => true,
				'label' => $this->l('Activate Finland?'),
				'desc' => $this->l('Select yes to activate Finland'),
				'name' => 'ACTIVE_FI',
				'tab' => 'finland',
				'values' => array(
					array(
						'id' => 'active_on',
						'value' => 1,
						'label' => $this->l('Yes')
					),
					array(
						'id' => 'active_off',
						'value' => 0,
						'label' => $this->l('No')
						)
					),
				),
				array(
				'type' => 'text',
				'label' => $this->l('Klarna EID'),
				'desc' => $this->l('Fill in the merchant id from Klarna'),
				'name' => 'KLARNA_EID_FI',
				'tab' => 'finland',
				'required' => true,
				'class' => 'fixed-width-lg'
				),
				array(
				'type' => 'text',
				'label' => $this->l('Klarna secret'),
				'desc' => $this->l('Fill in the Klarna secret'),
				'name' => 'KLARNA_SECRET_FI',
				'tab' => 'finland',
				'required' => true,
				'class' => 'fixed-width-lg'
				),
				array(
                    'type' => 'html',
                    'name' => 'html_data',
										'tab' => 'finland',
                    'html_content' => '<button type="submit" class="btn btn-default" name="pclass_fi">Update PClasses</button>'
                ),
				array(
				'type' => 'radio',
				'label' => $this->l('Activate invoice payments?'),
				'tab' => 'finland',
				'desc' => $this->l('Select if you wish to offer invoice payments'),
				'name' => 'KLARNA_INVOICE_FI',
				'values' => array(
					array(
						'id' => 'active_on',
						'value' => 1,
						'label' => $this->l('Yes')
					),
					array(
						'id' => 'active_off',
						'value' => 0,
						'label' => $this->l('No')
						)
					),
				),
				array(
				'type' => 'radio',
				'label' => $this->l('Activate part payments?'),
				'tab' => 'finland',
				'desc' => $this->l('Select if you wish to offer part payments'),
				'name' => 'KLARNA_PART_FI',
				'values' => array(
					array(
						'id' => 'active_on',
						'value' => 1,
						'label' => $this->l('Yes')
					),
					array(
						'id' => 'active_off',
						'value' => 0,
						'label' => $this->l('No')
						)
					),
				),
				array(
				'type' => 'radio',
				'label' => $this->l('Activate klarna checkout?'),
				'tab' => 'finland',
				'desc' => $this->l('Select if you wish to offer klarna checkout'),
				'name' => 'KLARNA_CHECKOUT_FI',
				'values' => array(
					array(
						'id' => 'active_on',
						'value' => 1,
						'label' => $this->l('Yes')
					),
					array(
						'id' => 'active_off',
						'value' => 0,
						'label' => $this->l('No')
						)
					),
				),
				array(
				'type' => 'radio',
				'is_bool' => true,
				'label' => $this->l('Activate Netherlands?'),
				'desc' => $this->l('Select yes to activate Netherlands'),
				'name' => 'ACTIVE_NL',
				'tab' => 'netherlands',
				'values' => array(
					array(
						'id' => 'active_on',
						'value' => 1,
						'label' => $this->l('Yes')
					),
					array(
						'id' => 'active_off',
						'value' => 0,
						'label' => $this->l('No')
						)
					),
				),
				array(
				'type' => 'text',
				'label' => $this->l('Klarna EID'),
				'desc' => $this->l('Fill in the merchant id from Klarna'),
				'name' => 'KLARNA_EID_NL',
				'tab' => 'netherlands',
				'required' => true,
				'class' => 'fixed-width-lg'
				),
				array(
				'type' => 'text',
				'label' => $this->l('Klarna secret'),
				'desc' => $this->l('Fill in the Klarna secret'),
				'name' => 'KLARNA_SECRET_NL',
				'tab' => 'netherlands',
				'required' => true,
				'class' => 'fixed-width-lg'
				),
				array(
                    'type' => 'html',
                    'name' => 'html_data',
										'tab' => 'netherlands',
                    'html_content' => '<button type="submit" class="btn btn-default" name="pclass_nl">Update PClasses</button>'
                ),
				array(
				'type' => 'radio',
				'label' => $this->l('Activate invoice payments?'),
				'tab' => 'netherlands',
				'desc' => $this->l('Select if you wish to offer invoice payments'),
				'name' => 'KLARNA_INVOICE_NL',
				'values' => array(
					array(
						'id' => 'active_on',
						'value' => 1,
						'label' => $this->l('Yes')
					),
					array(
						'id' => 'active_off',
						'value' => 0,
						'label' => $this->l('No')
						)
					),
				),
				array(
				'type' => 'radio',
				'label' => $this->l('Activate part payments?'),
				'tab' => 'netherlands',
				'desc' => $this->l('Select if you wish to offer part payments'),
				'name' => 'KLARNA_PART_NL',
				'values' => array(
					array(
						'id' => 'active_on',
						'value' => 1,
						'label' => $this->l('Yes')
					),
					array(
						'id' => 'active_off',
						'value' => 0,
						'label' => $this->l('No')
						)
					),
				),
				array(
				'type' => 'text',
				'label' => $this->l('Invoice product reference'),
				'desc' => $this->l('Fill in the reference number for the invoice fee product'),
				'name' => 'KLARNA_INVOICE_FEE_REF',
				'tab' => 'general',
				'class' => 'fixed-width-lg'
					),
				array(
				'type' => 'select',
				'label' => $this->l('Select environment'),
				'desc' => $this->l('Select test or live mode'),
				'name' => 'KLARNA_ENVIRONMENT',
				'tab' => 'general',
				'required' => true,
				'options' => array(
					'query' => $klarna_mode,
					'id' => 'id_option',
					'name' => 'name'
						)
					),
				array(
				'type' => 'select',
				'label' => $this->l('Use invoice fee?'),
				'desc' => $this->l('Select for which payments you want to add invoice fee'),
				'name' => 'KLARNA_INVOICE_FEE',
				'tab' => 'general',
				'required' => true,
				'options' => array(
					'query' => $klarna_invoice_fee,
					'id' => 'id_option',
					'name' => 'name'
					)
				),
				array(
					'type' => 'color',
	        'label' => $this->l('Color button'),
					'tab' => 'general',
	        'name' => 'KLARNA_CHECKOUT_COLOR_BUTTON'
				),
				array(
					'type' => 'color',
	        'label' => $this->l('Text color on button'),
					'tab' => 'general',
	        'name' => 'KLARNA_CHECKOUT_COLOR_BUTTON_TEXT'
				),
				array(
					'type' => 'color',
	        'label' => $this->l('Color checkbox'),
					'tab' => 'general',
	        'name' => 'KLARNA_CHECKOUT_COLOR_CHECKBOX'
				),
				array(
					'type' => 'color',
	        'label' => $this->l('Color checkbox checkmark'),
					'tab' => 'general',
	        'name' => 'KLARNA_CHECKOUT_COLOR_CHECKBOX_CHECKMARK'
				),
				array(
					'type' => 'color',
	        'label' => $this->l('Color header'),
					'tab' => 'general',
					'name' => 'KLARNA_CHECKOUT_COLOR_HEADER'
				),
				array(
					'type' => 'color',
	        'label' => $this->l('Color link'),
					'tab' => 'general',
	        'name' => 'KLARNA_CHECKOUT_COLOR_LINK'
				),
				array(
				'type' => 'select',
				'label' => $this->l('Invoice method'),
				'desc' => $this->l('Select if you wish Klarna to send the invoice by mail or post'),
				'name' => 'klarna_invoice_method',
				'tab' => 'general',
				'options' => array(
					'query' => $klarna_invoice_method,
					'id' => 'id_option',
					'name' => 'name'
					)
				),
				array(
                    'type' => 'html',
                    'name' => 'html_data',
										'tab' => 'general',
                    'html_content' => '<button type="submit" class="btn btn-default" name="delete_pclasses">Delete PClasses</button>'
              ),
				array(
						'type' => 'radio',
						'is_bool' => false,
						'class' => 't',
						'name' => 'klarna_fetch_address',
						'label' => $this->l('Klarna getadress in checkout'),
						'tab' => 'general',
						'desc' => $this->l('Works only for Sweden and shall only be used with Klarna payments'),
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Yes')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('No')
								)
							),
						),
				),
				'submit' => array(
						'title' => $this->l('Save'),
						'class' => 'button pull-right'
						)
					),
				);

		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG')
		? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();
		$helper->id = (int)Tools::getValue('id_carrier');
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'saveBtn';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).
		'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
		'fields_value' => $this->getConfigFieldsValues(),
		'languages' => $this->context->controller->getLanguages(),
		'id_language' => $this->context->language->id
		);
		return $helper->generateForm(array($fields_form));
	}

	/**
	* Get config values
	*
	* @return array
	* @author Johan Tedenmark
	*/

	public function getConfigFieldsValues()
	{
		return array(
			'KLARNA_EID_SE' => Tools::getValue('KLARNA_EID_SE', Configuration::get('KLARNA_EID_SE')),
			'KLARNA_SECRET_SE' => Tools::getValue('KLARNA_SECRET_SE', Configuration::get('KLARNA_SECRET_SE')),
			'KLARNA_EID_NO' => Tools::getValue('KLARNA_EID_NO', Configuration::get('KLARNA_EID_NO')),
			'KLARNA_SECRET_NO' => Tools::getValue('KLARNA_SECRET_NO', Configuration::get('KLARNA_SECRET_NO')),
			'KLARNA_EID_DK' => Tools::getValue('KLARNA_EID_DK', Configuration::get('KLARNA_EID_DK')),
			'KLARNA_SECRET_DK' => Tools::getValue('KLARNA_SECRET_DK', Configuration::get('KLARNA_SECRET_DK')),
			'KLARNA_EID_DE' => Tools::getValue('KLARNA_EID_DE', Configuration::get('KLARNA_EID_DE')),
			'KLARNA_SECRET_DE' => Tools::getValue('KLARNA_SECRET_DE', Configuration::get('KLARNA_SECRET_DE')),
			'KLARNA_EID_AT' => Tools::getValue('KLARNA_EID_AT', Configuration::get('KLARNA_EID_AT')),
			'KLARNA_SECRET_AT' => Tools::getValue('KLARNA_SECRET_AT', Configuration::get('KLARNA_SECRET_AT')),
			'KLARNA_EID_FI' => Tools::getValue('KLARNA_EID_FI', Configuration::get('KLARNA_EID_FI')),
			'KLARNA_SECRET_FI' => Tools::getValue('KLARNA_SECRET_FI', Configuration::get('KLARNA_SECRET_FI')),
			'KLARNA_EID_NL' => Tools::getValue('KLARNA_EID_NL', Configuration::get('KLARNA_EID_NL')),
			'KLARNA_SECRET_NL' => Tools::getValue('KLARNA_SECRET_NL', Configuration::get('KLARNA_SECRET_NL')),
			'ACTIVE_SE' => Tools::getValue('ACTIVE_SE', Configuration::get('ACTIVE_SE')),
			'ACTIVE_FI' => Tools::getValue('ACTIVE_FI', Configuration::get('ACTIVE_FI')),
			'ACTIVE_NO' => Tools::getValue('ACTIVE_NO', Configuration::get('ACTIVE_NO')),
			'ACTIVE_DK' => Tools::getValue('ACTIVE_DK', Configuration::get('ACTIVE_DK')),
			'ACTIVE_DE' => Tools::getValue('ACTIVE_DE', Configuration::get('ACTIVE_DE')),
			'ACTIVE_NL' => Tools::getValue('ACTIVE_NL', Configuration::get('ACTIVE_NL')),
			'ACTIVE_AT' => Tools::getValue('ACTIVE_AT', Configuration::get('ACTIVE_AT')),
			'KLARNA_PART_SE' => Tools::getValue('KLARNA_PART_SE', Configuration::get('KLARNA_PART_SE')),
			'KLARNA_INVOICE_SE' => Tools::getValue('KLARNA_INVOICE_SE', Configuration::get('KLARNA_INVOICE_SE')),
			'KLARNA_PART_FI' => Tools::getValue('KLARNA_PART_FI', Configuration::get('KLARNA_PART_FI')),
			'KLARNA_INVOICE_FI' => Tools::getValue('KLARNA_INVOICE_FI', Configuration::get('KLARNA_INVOICE_FI')),
			'KLARNA_PART_NO' => Tools::getValue('KLARNA_PART_NO', Configuration::get('KLARNA_PART_NO')),
			'KLARNA_INVOICE_NO' => Tools::getValue('KLARNA_INVOICE_NO', Configuration::get('KLARNA_INVOICE_NO')),
			'KLARNA_PART_DK' => Tools::getValue('KLARNA_PART_DK', Configuration::get('KLARNA_PART_DK')),
			'KLARNA_INVOICE_DK' => Tools::getValue('KLARNA_INVOICE_DK', Configuration::get('KLARNA_INVOICE_DK')),
			'KLARNA_PART_DE' => Tools::getValue('KLARNA_PART_DE', Configuration::get('KLARNA_PART_DE')),
			'KLARNA_INVOICE_DE' => Tools::getValue('KLARNA_INVOICE_DE', Configuration::get('KLARNA_INVOICE_DE')),
			'KLARNA_PART_NL' => Tools::getValue('KLARNA_PART_NL', Configuration::get('KLARNA_PART_NL')),
			'KLARNA_INVOICE_NL' => Tools::getValue('KLARNA_INVOICE_NL', Configuration::get('KLARNA_INVOICE_NL')),
			'KLARNA_PART_AT' => Tools::getValue('KLARNA_PART_AT', Configuration::get('KLARNA_PART_AT')),
			'KLARNA_INVOICE_AT' => Tools::getValue('KLARNA_INVOICE_AT', Configuration::get('KLARNA_INVOICE_AT')),
			'KLARNA_CHECKOUT_SE' => Tools::getValue('KLARNA_CHECKOUT_SE', Configuration::get('KLARNA_CHECKOUT_SE')),
			'KLARNA_CHECKOUT_NO' => Tools::getValue('KLARNA_CHECKOUT_NO', Configuration::get('KLARNA_CHECKOUT_NO')),
			'KLARNA_CHECKOUT_FI' => Tools::getValue('KLARNA_CHECKOUT_FI', Configuration::get('KLARNA_CHECKOUT_FI')),
			'KLARNA_CHECKOUT_DE' => Tools::getValue('KLARNA_CHECKOUT_DE', Configuration::get('KLARNA_CHECKOUT_DE')),
			'KLARNA_ENVIRONMENT' => Tools::getValue('KLARNA_ENVIRONMENT', Configuration::get('KLARNA_ENVIRONMENT')),
			'KLARNA_CHECKOUT_COLOR_BUTTON' => Tools::getValue('KLARNA_CHECKOUT_COLOR_BUTTON', Configuration::get('KLARNA_CHECKOUT_COLOR_BUTTON')),
			'KLARNA_CHECKOUT_COLOR_BUTTON_TEXT' => Tools::getValue('KLARNA_CHECKOUT_COLOR_BUTTON_TEXT', Configuration::get('KLARNA_CHECKOUT_COLOR_BUTTON_TEXT')),
			'KLARNA_CHECKOUT_COLOR_CHECKBOX' => Tools::getValue('KLARNA_CHECKOUT_COLOR_CHECKBOX', Configuration::get('KLARNA_CHECKOUT_COLOR_CHECKBOX')),
			'KLARNA_CHECKOUT_COLOR_CHECKBOX_CHECKMARK' => Tools::getValue('KLARNA_CHECKOUT_COLOR_CHECKBOX_CHECKMARK', Configuration::get('KLARNA_CHECKOUT_COLOR_CHECKBOX_CHECKMARK')),
			'KLARNA_CHECKOUT_COLOR_HEADER' => Tools::getValue('KLARNA_CHECKOUT_COLOR_HEADER', Configuration::get('KLARNA_CHECKOUT_COLOR_HEADER')),
			'KLARNA_CHECKOUT_COLOR_LINK' => Tools::getValue('KLARNA_CHECKOUT_COLOR_LINK', Configuration::get('KLARNA_CHECKOUT_COLOR_LINK')),


			'KLARNA_INVOICE_FEE_REF' => Tools::getValue('KLARNA_INVOICE_FEE_REF', Configuration::get('KLARNA_INVOICE_FEE_REF')),
			'KLARNA_INVOICE_FEE' => Tools::getValue('KLARNA_INVOICE_FEE', Configuration::get('KLARNA_INVOICE_FEE')),
			'klarna_invoice_method' => Tools::getValue('klarna_invoice_method', Configuration::get('klarna_invoice_method')),
			'klarna_fetch_address' => Tools::getValue('klarna_fetch_address', Configuration::get('klarna_fetch_address')),
		);
	}

	/**
	* Check if mobile or not
	*
	* @return string desktop || mobile
	* @author Johan Tedenmark
	*/

	public function checkMobile()
	{
		switch ($this->context->getDevice())
		{
			case 1:
				return 'desktop';
			case 2:
				return 'mobile';
			case 4:
				return 'mobile';
		}

	}

	public function getInvoiceType()
	{
		if ((int)Configuration::get('klarna_invoice_method') == 1)
			return KlarnaFlags::RSRV_SEND_BY_EMAIL;
		elseif ((int)Configuration::get('klarna_invoice_method') == 0)
			return KlarnaFlags::RSRV_SEND_BY_MAIL;
	}

	public function hookDisplayRightColumnProduct()
	{
		if ($id_product = (int)Tools::getValue('id_product'))
			$product = new Product($id_product, true, $this->context->language->id, $this->context->shop->id);

		if (!Validate::isLoadedObject($product))
    	return;

    	$product_price = $product::getPriceStatic($id_product);

    	$this->smarty->assign(array(
    		'eid' => Configuration::get('klarna_eid'),
    		'price' => $product_price,
    		'lang' => Tools::strtolower($this->getLocale()),
    		'fee' => $this->getInvoiceFee()
    		));

		return $this->display(__FILE__, 'klarnapartinfo.tpl');
	}

	/**
	* Display hook shopping cart extra
	*
	* @return shopping cart extra template
	* @author Johan Tedenmark
	*/

	public function hookShoppingCartExtra($params)
	{
		if (!$this->active)
			return;

		if ($this->context->language->iso_code == 'sv' && $this->context->currency->iso_code == 'SEK' && Configuration::get('klarna_fetch_address') == 1 && Configuration::get('PS_ORDER_PROCESS_TYPE') == 1)
		{

		$this->prepareHook();

		return $this->display(__FILE__, 'klarnaaddress.tpl');

		}

	}


	protected function klarnaAddressRegistration()
	{
		if (!$this->isPno(Tools::getValue('pno')))

			return $this->error = $this->l('Invalid personal number');

		if ($this->isPno(Tools::getValue('pno')))
		{

			$k = new Klarna();

			$k->config(Configuration::get('klarna_eid'),
				Configuration::get('klarna_secret'),
				$this->getKlarnaCountry(),
				$this->getKlarnaLanguage(),
				$this->getKlarnaCurrency(),
				$this->getKlarnaEnvironment(),
				'json',
				dirname(__FILE__).'/pclasses/pclasses.json');

			$k->setCountry('se'); // Sweden only
			try {
			$addrs = $k->getAddresses(Tools::getValue('pno'));

			foreach ($addrs as $value)
			{
				$this->smarty->assign(array(
					'klarna_firstname' => utf8_encode($value->getFirstName()),
					'klarna_lastname' => utf8_encode($value->getLastName()),
					'klarna_email' => utf8_encode($value->getEmail()),
					'klarna_telno' => utf8_encode($value->getTelno()),
					'klarna_cellno' => utf8_encode($value->getCellno()),
					'klarna_careof' => utf8_encode($value->getCareof()),
					'klarna_street' => utf8_encode($value->getStreet()),
					'klarna_zip' => utf8_encode($value->getZipCode()),
					'klarna_city' => utf8_encode($value->getCity()),
					'klarna_country' => utf8_encode($value->getCountry())));
			}

			$this->valid = $this->l('Fetched address was successful');

			} catch(Exception $e) {

			$this->error = $this->l('Address could not be fetched');

			}

		}

	}

	public function checkCurrency($cart)
	{
		$currency_order = new Currency($cart->id_currency);
		$currencies_module = $this->getCurrency($cart->id_currency);
		if (is_array($currencies_module))
			foreach ($currencies_module as $currency_module)
				if ($currency_order->id == $currency_module['id_currency'])
					return true;
		return false;
	}

	private function encodeKlarna($str)
	{
		return iconv('UTF-8', 'ISO-8859-1', $str);
	}
	/**
	* Check if pno is valid or not (SE used for get address)
	*
	* @param $pno, personal number
	* @return bool is ok or not
	* @author Johan Tedenmark
	*/

	public function isPno($pno)
	{
		return !empty($pno) && preg_match('/^[0-9]{6}-?[0-9]{4}$/', $pno);
	}

	private function prepareHook()
	{
		if (Tools::isSubmit('submitKlarnaAddress'))
		{
			$this->klarnaAddressRegistration();
			if ($this->error)
			{
				$this->smarty->assign(array(
					'color' => 'red',
					'address_error' => true,
					'msg' => $this->error));
			}
			else if ($this->valid)
			{
				$this->smarty->assign(array(
					'color' => 'green',
					'address_error' => false,
					'msg' => $this->valid));
			}

		}
		$this->smarty->assign('this_path', $this->_path);

	}

	public function getConfiguration($klarna_country)
	{
		if (!is_string($klarna_country))
				return;

		$environment = Tools::getValue('KLARNA_ENVIRONMENT') == 'live' ? Klarna::LIVE : KLARNA::BETA;

		$locale = $this->getKlarnaLocale($klarna_country);

		$k = new Klarna();

		foreach ($this->settings as $key => $value) {
				if ($key == $klarna_country) {
					$k->config(
			  	(int)$value['klarna_eid'],               // Merchant ID
			    (String)$value['klarna_secret'],       // Shared Secret
			    $locale[0],    // Country
			    $locale[1],   // Language
			    $locale[2],  // Currency
			    $environment,
			    'json',               // PClass Storage
			    dirname(__FILE__).'/pclasses/pclasses.json' // PClass Storage URI path
					);
				}

		}

		return $k;
	}

	private function updatePClasses($country, $language, $currency)
	{
		if (!is_string($country) || !is_string($language) || !is_string($currency))
						return;

		$klarnas = $this->getConfiguration($country);


		try {

		$klarnas->fetchPClasses($country, $language, $currency);

		$this->html .= $this->displayConfirmation($this->l('PClass updated for country: '. $country));

		} catch(Exception $e) {
		Logger::addLog('Klarna module: PClass call failed with message: '.$e->getMessage().' and response code: '.$e->getCode());
		$this->html .= $this->displayError('PClass call failed : see log for error message');
		}

	}


	public function getPlaceholderPno()
	{
		switch (Country::getIsoById($this->context->country->id))
		{
			case 'SE':
				return 'YYMMDDNNNN';
			case 'DE':
				return 'DDMMYYYY';
			case 'DK':
				return 'DDMMYYNNNN';
			case 'NO':
				return 'DDMMYYNNNNN';
			case 'FI':
				return 'DDMMYY-NNNN';
			case 'NL':
				return 'DDMMYYYY';
		}
	}

	public function checkLocale($country, $currency, $language)
	{
		if ($country == 'SE' && $currency == 'SEK' && $language == 'sv')
			return true;
		elseif ($country == 'DE' && $currency == 'EUR' && $language == 'de')
			return true;
		elseif ($country == 'DK' && $currency == 'DKK' && $language == 'da')
			return true;
		elseif ($country == 'NL' && $currency == 'EUR' && $language == 'nl')
			return true;
		elseif ($country == 'NO' && $currency == 'NOK' && $language == 'no')
			return true;
		elseif ($country == 'FI' && $currency == 'EUR' && $language == 'fi')
			return true;
		elseif ($country == 'AT' && $currency == 'EUR' && $language == 'at')
			return true;
		else
			return false;

	}

	public function getPatternPno()
	{
		switch (Country::getIsoById($this->context->country->id))
		{
			case 'SE':
				return '^[0-9]{6,6}(([0-9]{2,2}[-\+]{1,1}[0-9]{4,4})|([-\+]{1,1}[0-9]{4,4})|([0-9]{4,6}))$';
			case 'DE':
				return '^[0-9]{7,9}$';
			case 'DK':
				return '^[0-9]{8,8}([0-9]{2,2})?$';
			case 'NO':
				return '^[0-9]{6,6}((-[0-9]{5,5})|([0-9]{2,2}((-[0-9]{5,5})|([0-9]{1,1})|([0-9]{3,3})|([0-9]{5,5))))$';
			case 'FI':
				return '^[0-9]{6,6}(([A\+-]{1,1}[0-9]{3,3}[0-9A-FHJK-NPR-Y]{1,1})|([0-9]{3,3}[0-9A-FHJK-NPR-Y]{1,1})|([0-9]{1,1}-{0,1}[0-9A-FHJK-NPR-Y]{1,1}))$';
			case 'NL':
				return '^[0-9]{7,9}$';
		}
	}

	public function getPatternPnoPHP()
	{
		switch (Country::getIsoById($this->context->country->id))
		{
			case 'SE':
				return	'/^[0-9]{6,6}(([0-9]{2,2}[-\+]{1,1}[0-9]{4,4})|([-\+]{1,1}[0-9]{4,4})|([0-9]{4,6}))$/';
			case 'DE':
				return '/^[0-9]{7,9}$/';
			case 'DK':
				return '/^[0-9]{8,8}([0-9]{2,2})?$/';
			case 'NO':
				return '/^[0-9]{6,6}((-[0-9]{5,5})|([0-9]{2,2}((-[0-9]{5,5})|([0-9]{1,1})|([0-9]{3,3})|([0-9]{5,5))))$/';
			case 'NL':
				return '/^[0-9]{7,9}$/';
			case 'FI':
				return '/^[0-9]{6,6}(([A\+-]{1,1}[0-9]{3,3}[0-9A-FHJK-NPR-Y]{1,1})|([0-9]{3,3}[0-9A-FHJK-NPR-Y]{1,1})|([0-9]{1,1}-{0,1}[0-9A-FHJK-NPR-Y]{1,1}))$/';
		}
	}


	public function getKlarnaCurrency()
	{
		switch (Tools::strtoupper($this->context->currency->iso_code))
		{
			case 'SEK':
				return KlarnaCurrency::SEK;
			case 'EUR':
				return KlarnaCurrency::EUR;
			case 'DKK':
				return KlarnaCurrency::DKK;
			case 'NOK':
				return KlarnaCurrency::NOK;
		}
	}

	public function getKlarnaCountry()
	{
		switch (Country::getIsoById($this->context->country->id))
		{
			case 'SE':
				return KlarnaCountry::SE;
			case 'NO':
				return KlarnaCountry::NO;
			case 'DE':
				return KlarnaCountry::DE;
			case 'DK':
				return KlarnaCountry::DK;
			case 'FI':
				return KlarnaCountry::FI;
			case 'NL':
				return KlarnaCountry::NL;
		}
	}

	public function getKlarnaLanguage()
	{
		switch (Language::getIsoById($this->context->language->id))
		{
			case 'sv':
				return KlarnaLanguage::SV;
			case 'no':
				return KlarnaLanguage::NB;
			case 'fi':
				return KlarnaLanguage::FI;
			case 'da':
				return KlarnaLanguage::DA;
			case 'de':
				return KlarnaLanguage::DE;
			case 'nl':
				return KlarnaLanguage::NL;
		}
	}

	private function getKlarnaLocale($country_iso)
	{
		switch ($country_iso)
		{
			case 'SE':
				return array(KlarnaCountry::SE, KlarnaLanguage::SV, KlarnaCurrency::SEK);
			case 'FI':
				return array(KlarnaCountry::FI, KlarnaLanguage::FI, KlarnaCurrency::EUR);
			case 'DE':
				return array(KlarnaCountry::DE, KlarnaLanguage::DE, KlarnaCurrency::EUR);
			case 'DK':
				return array(KlarnaCountry::DK, KlarnaLanguage::DA, KlarnaCurrency::DKK);
			case 'NO':
				return array(KlarnaCountry::NO, KlarnaLanguage::NB, KlarnaCurrency::NOK);
			case 'NL':
				return array(KlarnaCountry::NL, KlarnaLanguage::NL, KlarnaCurrency::EUR);
			case 'AT':
				return array(KlarnaCountry::DE, KlarnaLanguage::DE, KlarnaCurrency::EUR);
		}
	}

	public function getByReference($invoiceref)
	{
		$result = Db::getInstance()->getRow('SELECT id_product FROM `'._DB_PREFIX_.'product` WHERE reference='.$invoiceref);
		if (isset($result['id_product']) && (int)$result['id_product'] > 0)
		{
			$feeproduct = new Product((int)$result['id_product'], true);
			return $feeproduct;
		}
		else
		{

		return null;
		}
	}

	public function getProductId($invoicereference)
	{
	$result = Db::getInstance()->getRow('SELECT id_product FROM `'._DB_PREFIX_.'product` WHERE reference='.$invoicereference);
		if (isset($result['id_product']) && (int)$result['id_product'] > 0)

			return (int)$result['id_product'];

		else

			return null;
	}

	public function getInvoiceFee()
	{
		if (Configuration::get('KLARNA_INVOICE_FEE_REF') && (int)Configuration::get('KLARNA_INVOICE_FEE') == 1)
		{

		$inv_id = $this->getProductId(Configuration::get('KLARNA_INVOICE_FEE_REF'));

		$inv_product = new Product();

		return $inv_product::getPriceStatic($inv_id);

	}elseif (!Configuration::get('KLARNA_INVOICE_FEE_REF') || (int)Configuration::get('KLARNA_INVOICE_FEE') == 0)
		{

			return 0;

		}
	}

	public function getKlarnaPClasses($type)
	{
		$k = new Klarna();

		$k->config(Configuration::get('klarna_eid'),
			Configuration::get('klarna_secret'),
			$this->getKlarnaCountry(),
			$this->getKlarnaLanguage(),
			$this->getKlarnaCurrency(),
			$this->getKlarnaEnvironment(),
			'json',
			dirname(__FILE__).'/pclasses/pclasses.json');

			if ($type == 'ACCOUNT')

				$klarna_pclass_type = KlarnaPClass::ACCOUNT;

			elseif ($type == 'CAMPAIGN')

				$klarna_pclass_type = KlarnaPClass::CAMPAIGN;

			elseif ($type == 'SPECIAL')

				$klarna_pclass_type = KlarnaPClass::SPECIAL;

			$pclasses = $k->getPClasses($klarna_pclass_type);

			return $pclasses;

	}

	public function calculateKlarnaApr($amount, $type, $id)
	{
		$k = new Klarna();

		$k->config(Configuration::get('klarna_eid'),
			Configuration::get('klarna_secret'),
			$this->getKlarnaCountry(),
			$this->getKlarnaLanguage(),
			$this->getKlarnaCurrency(),
			$this->getKlarnaEnvironment(),
			'json',
			dirname(__FILE__).'/pclasses/pclasses.json');

		$pclass = $k->getPClass($id);

		if ($pclass)
			$apr = KlarnaCalc::calc_apr($amount, $pclass, $type);
			return $apr;
	}

	private function getInvoiceURI($id_order)
	{
		$invoicenumber = $this->getInvoiceNum($id_order);

		if (Configuration::get('KLARNA_ENVIRONMENT') == 'beta')
			return 'https://online.testdrive.klarna.com/invoices/'.$invoicenumber.'.pdf';
		elseif (Configuration::get('KLARNA_ENVIRONMENT') == 'live')
			return 'https://online.klarna.com/invoices/'.$invoicenumber.'.pdf';

	}

	public function klarnaCalculateMonthlyCost($amount, $type, $id)
	{
		$k = new Klarna();

		$k->config(Configuration::get('klarna_eid'),
			Configuration::get('klarna_secret'),
			$this->getKlarnaCountry(),
			$this->getKlarnaLanguage(),
			$this->getKlarnaCurrency(),
			$this->getKlarnaEnvironment(),
			'json',
			dirname(__FILE__).'/pclasses/pclasses.json');

		$pclass = $k->getPClass($id);

		if ($pclass)
			$monthly = KlarnaCalc::calc_monthly_cost($amount, $pclass, $type);
			return $monthly;

	}

	public function klarnaCalculateTotalCredit($amount, $type, $id)
	{
	$k = new Klarna();

	$k->config(Configuration::get('klarna_eid'),
			Configuration::get('klarna_secret'),
			$this->getKlarnaCountry(),
			$this->getKlarnaLanguage(),
			$this->getKlarnaCurrency(),
			$this->getKlarnaEnvironment(),
			'json',
			dirname(__FILE__).'/pclasses/pclasses.json');

	$pclass = $k->getPClass($id);

	if ($pclass)
		$total = KlarnaCalc::total_credit_purchase_cost($amount, $pclass, $type);
		return $total;
	}

	private function getReservationNum($id_order)
	{
		$id_reservation = Db::getInstance()->getRow('SELECT `id_reservation` FROM `'._DB_PREFIX_.'klarna_orders` WHERE `id_order` = '.(int)$id_order);

		return $id_reservation['id_reservation'];
	}

	private function getInvoiceNum($id_order)
	{
		$id_invoicenumber = Db::getInstance()->getRow('SELECT `id_invoicenumber` FROM `'._DB_PREFIX_.'klarna_orders` WHERE `id_order` = '.(int)$id_order);

		return $id_invoicenumber['id_invoicenumber'];
	}

	private function updateDatabase($invoice_number, $id_order)
	{
		if (!Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'klarna_orders` SET `id_invoicenumber` = '.$invoice_number.' WHERE `id_order` = '.(int)$id_order))
		die(Tools::displayError('Error when updating Klarna database'));

	}




	public function _addNewPrivateMessage($id_order, $message)
	{
				$msg = new Message();

				$msg->message = $message;

				$msg->id_order = (int)$id_order;

				$msg->private = 1;

				$msg->add();
	}

}
