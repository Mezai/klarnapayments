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

require_once(dirname(__FILE__).'/classes/KlarnaPrestashopCore.php');



class KlarnaPayments extends PaymentModule
{
	private $html = '';
	private $post_errors = array();

	public $input_vals = array(
		'MULTI_LOCALE' => array(
			'KLARNA_EID_', 'KLARNA_SECRET_', 'ACTIVE_', 'KLARNA_PART_', 'KLARNA_INVOICE_', 'KLARNA_CHECKOUT_'),
		'GENERAL' => array('KLARNA_ENVIRONMENT', 'KLARNA_INVOICE_FEE_TAX', 'KLARNA_CHECKOUT_COLOR_LINK', 'KLARNA_CHECKOUT_COLOR_BUTTON',
			'KLARNA_CHECKOUT_COLOR_CHECKBOX', 'KLARNA_CHECKOUT_COLOR_HEADER', 'KLARNA_CHECKOUT_COLOR_BUTTON_TEXT', 'KLARNA_CHECKOUT_COLOR_CHECKBOX_CHECKMARK',
			'KLARNA_INVOICE_PRICE'),
		);

	const INVOICE_REF = 'invoicefee';

	public function __construct()
	{
		$this->name = 'klarnapayments';
		$this->tab = 'payments_gateways';
		$this->limited_countries = array('se', 'no', 'fi', 'dk', 'de', 'nl');
		$this->module_key = '9ba314b95673c695df2051398019734c';
		$this->version = '1.0.2';
		$this->author = 'JET';
		$this->need_instance = 1;
		$this->bootstrap = true;
		$this->currencies = true;
		$this->currencies_mode = 'checkbox';

		parent::__construct();
		$this->displayName = $this->l('Klarna invoice and part payment');
		$this->description = $this->l('Allows your customers to pay with Klarna invoice and part payment');
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

	$klarna_install->createInvoiceFee();

	$klarna_install->createTable();

	$klarna_install->addTabs();

	$klarna_install->createStatus();

	return parent::install()
	&& $this->registerHook('payment')
	&& $this->registerHook('backOfficeHeader')
	&& $this->registerHook('displayLeftColumn')
	&& $this->registerHook('paymentReturn')
	&& $this->registerHook('displayShoppingCartFooter')
	&& $this->registerHook('displayShoppingCart')
	&& $this->registerHook('displayRightColumnProduct')
	&& $this->registerHook('displayBeforeCarrier')
	&& $this->registerHook('header');

	}

	public function hookDisplayShoppingCart()
	{
		if (!$this->active || !KlarnaConfigHandler::checkConfigurationByLocale(Country::getIsoById($this->context->country->id), 'checkout'))
			return;
		$this->context->controller->addJS($this->_path.'views/js/klarnacheckout.js');
		$this->context->controller->addCSS($this->_path.'views/css/klarnacheckout.css');
		$cart = $this->context->cart;
		$country = Country::getIsoById($this->context->country->id);
		$currency = $this->context->currency->iso_code;
		$locale = $this->context->language->language_code;
		$checkout = new KlarnaCheckoutPresta();

		$snippet = $checkout->checkout($cart, $country, $currency, $locale);

		$this->context->smarty->assign(array(
			'snippet' => $snippet,
			));

		return $this->display(__FILE__, 'klarnacheckout.tpl');
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

		$tab_main = new Tab(Tab::getIdFromClassName('KlarnaMain'));
		$tab_pay = new Tab(Tab::getIdFromClassName('KlarnaOrders'));

		$invoice_product = new Product((int)Configuration::get('KLARNA_INVOICE_PRODUCT'));

		$klarna_uninstall = new KlarnaInstall();
		$klarna_uninstall->deleteConfiguration();

		return parent::uninstall()
		&& $tab_main->delete()
		&& $tab_pay->delete()
		&& $invoice_product->delete();
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
				$configuration = new KlarnaConfigHandler();

				foreach ($configuration->settings as $key => $value)
				{
					if ((int)$value['active'] == 1)
					{
						if (!Tools::getValue('KLARNA_EID_'.$key))
								$this->post_errors[] = $this->l('You need to set the Klarna EID for country: '.$key);
						elseif (!Tools::getValue('KLARNA_SECRET_'.$key))
								$this->post_errors[] = $this->l('You need to set the Klarna secret for country: '.$key);
					}
				}
		}
	}

	private function postProcess()
	{
		if (Tools::isSubmit('saveBtn'))
		{

			// better looop cause we have alot of values

			foreach ($this->input_vals as $keys => $values)
			{
				$configuration = new KlarnaConfigHandler();

				foreach ($values as $update_value)
				{
					foreach ($configuration->settings as $key_iso => $value)
					{
						if ($keys == 'MULTI_LOCALE')
						Configuration::updateValue($update_value.$key_iso, Tools::getValue($update_value.$key_iso));
						if ($keys == 'GENERAL')
						Configuration::updateValue($update_value, Tools::getValue($update_value));
					}
				}

			}
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
		$this->context->controller->addJS($this->_path.'views/js/klarnapayments.js');
		$this->context->controller->addJS($this->_path.'views/js/descriptionloader.js');

		return '<script src="https://cdn.klarna.com/public/kitt/core/v1.0/js/klarna.min.js"></script>
				<script src="https://cdn.klarna.com/public/kitt/toc/v1.1/js/klarna.terms.min.js"></script>';
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

	}


	/**
	* Hook order confirmation
	*
	* @return template order confirmation
	* @author Johan Tedenmark
	*/

	public function showPaymentPart($type)
	{
		$this->hookPayment();
		if ($type == 'klarna_payment_part_flexible')
		$vars = $this->context->smarty->fetch(_PS_MODULE_DIR_.'/klarnapayments/views/templates/front/part_payment_flexible.tpl');
		elseif ($type == 'klarna_payment_part_fixed_1')
			$vars = $this->context->smarty->fetch(_PS_MODULE_DIR_.'/klarnapayments/views/templates/front/part_payment_fixed_1.tpl');
		elseif ($type == 'klarna_payment_part_fixed_2')
			$vars = $this->context->smarty->fetch(_PS_MODULE_DIR_.'/klarnapayments/views/templates/front/part_payment_fixed_2.tpl');
		elseif ($type == 'klarna_payment_part_fixed_3')
			$vars = $this->context->smarty->fetch(_PS_MODULE_DIR_.'/klarnapayments/views/templates/front/part_payment_fixed_3.tpl');
		elseif ($type == 'klarna_payment_invoice')
			$vars = $this->context->smarty->fetch(_PS_MODULE_DIR_.'/klarnapayments/views/templates/front/invoice.tpl');
		elseif ($type == 'klarna_payment_invoice_payinx')
			$vars = $this->context->smarty->fetch(_PS_MODULE_DIR_.'/klarnapayments/views/templates/front/invoice_payinx.tpl');

		return Tools::jsonEncode($vars);
	}

	public function hookPaymentReturn()
	{
		if (!$this->active)
			return;

		return $this->display(__FILE__, 'payment_return.tpl');
	}

	private function checkLocale($country, $currency, $language)
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


	/**
	* Hook payment
	*
	* @return template payment
	* @author Johan Tedenmark
	*/

	public function hookPayment()
	{
		// Check if module is active and that the country set, check for valid currency, country aswell
		if (!$this->active || !KlarnaConfigHandler::isCountryActive(Country::getIsoById($this->context->country->id)))
			return;
			$cart = $this->context->cart;
			$currency = $this->context->currency;	

			$locale = new KlarnaLocalization(Country::getIsoById($this->context->country->id));
			$country_logic = new KlarnaCountryLogic($locale);
			$address = new Address((int)$cart->id_address_invoice);

		if (!$country_logic->isBusinessAllowed() && Tools::strlen($address->company) > 0)
			return;
			
			if (Country::getIsoById($this->context->country->id) === 'SE' || Country::getIsoById($this->context->country->id) === 'NO')
			{
				$checkout_data = new KlarnaCheckoutService();
				$data_klarna = $checkout_data->newCheckout(Country::getIsoById($this->context->country->id), $cart->getOrderTotal(true, Cart::BOTH),
				KlarnaLocalization::getPrestaLanguage($this->context->language->iso_code), Tools::strtoupper($currency->iso_code));

			}

		if (!empty($data_klarna['payment_methods']))
		{
			foreach ($data_klarna['payment_methods'] as $key => $value)
			{
				if ((String)$value['group']['code'] == 'invoice')
				{
					$this->context->smarty->assign(array(
					'invoice_description' => $value['group']['title'],
					'invoice_pclass_id' => $value['pclass_id'],
					'invoice_title' => $value['title'],
					'invoice_use_case' => $value['use_case'],
					));
				}

				if ((String)$value['group']['code'] == 'part_payment')
				{
					$this->context->smarty->assign(array(
					'partpayment_description' => $value['group']['title'],
					'partpayment_pclass_id' => $value['pclass_id'],
					'partpayment_title' => $value['title'],
					'partpayment_use_case' => $value['use_case'],
					'partpayment_interest_label' => $value['details']['interest_rate']['label'],
					'partpayment_interest_symbol' => $value['details']['interest_rate']['symbol'],
					'partpayment_interest_value' => $value['details']['interest_rate']['value'],
					'partpayment_startfee_label' => $value['details']['start_fee']['label'],
					'partpayment_startfee_symbol' => $value['details']['start_fee']['symbol'],
					'partpayment_startfee_value' => $value['details']['start_fee']['value'],
					'partpayment_invoicefee_label' => $value['details']['monthly_invoice_fee']['label'],
					'partpayment_invoicefee_symbol' => $value['details']['monthly_invoice_fee']['symbol'],
					'partpayment_invoicefee_value' => $value['details']['monthly_invoice_fee']['value'],
					));
				}
				if (Country::getIsoById($this->context->country->id) == 'NO' && (String)$value['group']['code'] == 'part_payment')
				{
					$this->context->smarty->assign(array(
					'partpayment_monthlypay_label' => $value['details']['minimum_monthly_pay']['label'],
					'partpayment_monthlypay_symbol' => $value['details']['minimum_monthly_pay']['symbol'],
					'partpayment_monthlypay_value' => $value['details']['minimum_monthly_pay']['value'],
					));
				} elseif (Country::getIsoById($this->context->country->id) == 'SE' && (String)$value['group']['code'] == 'part_payment')
				{
					$this->context->smarty->assign(array(
					'partpayment_monthlypay_label' => $value['details']['monthly_pay']['label'],
					'partpayment_monthlypay_symbol' => $value['details']['monthly_pay']['symbol'],
					'partpayment_monthlypay_value' => $value['details']['monthly_pay']['value'],
					));
				}
			}
		}

		$pclasses = new KlarnaPrestaPclasses();
		$get_pclasses = $pclasses->getKlarnaPClasses(Country::getIsoById($this->context->country->id));
		$this->context->smarty->assignByRef('KlarnaPClass', $get_pclasses);

		foreach ($get_pclasses as $key => $value)
		{
			if ($key === 1 || $key === 2 || $key === 3)
			{
			$this->context->smarty->assign(array(
			'klarna_pclass_id'.$key.'' => $value->getId(),
			'klarna_calc_monthly'.$key.'' => KlarnaCalc::calc_monthly_cost($cart->getOrderTotal(true, Cart::BOTH), $value, KlarnaFlags::CHECKOUT_PAGE),
			'klarna_calc_apr'.$key.'' => KlarnaCalc::calc_apr($cart->getOrderTotal(true, Cart::BOTH), $value, KlarnaFlags::CHECKOUT_PAGE),
			'klarna_calc_total_credit'.$key.'' => KlarnaCalc::total_credit_purchase_cost($cart->getOrderTotal(true, Cart::BOTH),
					$value, KlarnaFlags::CHECKOUT_PAGE),
			'klarna_min_amount'.$key.'' => $value->getMinAmount(),
			));
			}
		}

		foreach ($get_pclasses as $key => $value)
		{
			if ($value->getType() == (int)2)
			{
				$this->context->smarty->assign(array(
				'klarna_special_description' => $value->getDescription(),
				'klarna_special_id' => $value->getId(),
				'klarna_special_invoicefee' => $value->getInvoiceFee(),
				'klarna_special_start_fee' => $value->getStartFee(),
				'klarna_special_interest' => $value->getInterestRate(),
				'klarna_special_apr' => KlarnaCalc::calc_apr($cart->getOrderTotal(true, Cart::BOTH), $value, KlarnaFlags::CHECKOUT_PAGE),
				'klarna_special_credit' => KlarnaCalc::total_credit_purchase_cost($cart->getOrderTotal(true, Cart::BOTH), $value, KlarnaFlags::CHECKOUT_PAGE),
				));
			}
		}

		foreach ($get_pclasses as $key => $value)
		{
			if ($value->getType() == (int)1)
			{
				$this->context->smarty->assign(array(
				'klarna_account_description' => $value->getDescription(),
				'klarna_account_id' => $value->getId(),
				'klarna_account_start_fee' => $value->getStartFee(),
				'klarna_account_invoicefee' => $value->getInvoiceFee(),
				'klarna_account_interest' => $value->getInterestRate(),
				'klarna_account_monthly' => KlarnaCalc::calc_monthly_cost($cart->getOrderTotal(true, Cart::BOTH), $value, KlarnaFlags::CHECKOUT_PAGE),
				));
			}
		}

		$invoice = KlarnaConfigHandler::isKlarnaInvoiceActive(Country::getIsoById($this->context->country->id));
		$part = KlarnaConfigHandler::isKlarnaPartActive(Country::getIsoById($this->context->country->id));

		$this->context->smarty->assign(array(
			'merchant_id' => KlarnaConfigHandler::getMerchantID(Country::getIsoById($this->context->country->id)),
			'locale' => KlarnaLocalization::getPrestaLanguage(Language::getIsoById($this->context->language->id)),
			'type' => $this->checkMobile(),
			'total' => $cart->getOrderTotal(true, Cart::BOTH),
			'payment_part' => $part,
			'payment_invoice' => $invoice,
			'this_path' => $this->_path,
			'klarna_invoice_sum' => KlarnaInvoiceFeeHandler::getInvoiceFeePrice(self::INVOICE_REF),
			'klarna_pattern' => KlarnaValidation::getPattern(Country::getIsoById($this->context->country->id)),
			'klarna_placeholder' => KlarnaValidation::getPlaceholder(Country::getIsoById($this->context->country->id)),
			'klarna_locale' => Country::getIsoById($this->context->country->id),
			'checkLocale' => $this->checkLocale(Country::getIsoById($this->context->country->id),
			Tools::strtoupper($currency->iso_code), $this->context->language->iso_code),
			));

		$this->smarty->assign('validation_url', (Configuration::get('PS_SSL_ENABLED') ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].__PS_BASE_URI__.
				'index.php?process=validation&fc=module&module=klarnapayments&controller=payment');

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
		if (!KlarnaConfigHandler::isCountryActive(Country::getIsoById($this->context->country->id)))
			return;

		$this->context->smarty->assign(array(
			'klarna_merchant' => KlarnaConfigHandler::getMerchantID(Country::getIsoById($this->context->country->id)),
			'klarna_lang' => Tools::strtolower(KlarnaLocalization::getPrestaLanguage(Language::getIsoById($this->context->language->id)))
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


	public function getContent()
	{
		if (Tools::isSubmit('PCLASS_SE') || Tools::isSubmit('DELETE_PCLASS_SE'))
		{
			if (Tools::isSubmit('PCLASS_SE'))
			{
			$fetch = new KlarnaPrestaPclasses();
			if ($fetch->updatePClasses('SE'))
				$this->html .= $this->displayConfirmation('Pclasses update for Sweden');
			else
				$this->html .= $this->displayError('Failed updating pclasses for country Sweden');

			}
			if (Tools::isSubmit('DELETE_PCLASS_SE'))
			{
				$delete = new KlarnaPrestaPclasses();

				if ($delete->deletePClasses('SE'))
					$this->html .= $this->displayConfirmation('Pclasses deleted for Sweden');

				else
					$this->html .= $this->displayError('Failed deleting pclasses for country Sweden');

			}
		}
		elseif (Tools::isSubmit('PCLASS_NO') || Tools::isSubmit('DELETE_PCLASS_NO'))
		{

			if (Tools::isSubmit('PCLASS_NO'))
			{

			$fetch = new KlarnaPrestaPclasses();

			if ($fetch->updatePClasses('NO'))

				$this->html .= $this->displayConfirmation('Pclasses update for Norway');
			else
				$this->html .= $this->displayError('Failed updating pclasses for country Norway');
			}
			if (Tools::isSubmit('DELETE_PCLASS_NO'))
			{

				$delete = new KlarnaPrestaPclasses();

				if ($delete->deletePClasses('NO'))
					$this->html .= $this->displayConfirmation('Pclasses deleted for Norway');
				else
					$this->html .= $this->displayError('Failed deleting pclasses for Norway');
			}
		}
		elseif (Tools::isSubmit('PCLASS_DK') || Tools::isSubmit('DELETE_PCLASS_DK'))
		{
			if (Tools::isSubmit('PCLASS_DK'))
			{

			$fetch = new KlarnaPrestaPclasses();

			if ($fetch->updatePClasses('DK'))
				$this->html .= $this->displayConfirmation('Pclasses update for Denmark');
			else
				$this->html .= $this->displayError('Failed updating pclasses for country Denmark');

			}
			if (Tools::isSubmit('DELETE_PCLASS_DK'))
			{
				$delete = new KlarnaPrestaPclasses();

				if ($delete->deletePClasses('DK'))
					$this->html .= $this->displayConfirmation('Pclasses deleted for Denmark');

				else
					$this->html .= $this->displayError('Failed deleting pclasses for Denmark');

			}
		}
		elseif (Tools::isSubmit('PCLASS_NL') || Tools::isSubmit('DELETE_PCLASS_NL'))
		{
			if (Tools::isSubmit('PCLASS_NL'))
			{
			$fetch = new KlarnaPrestaPclasses();

			if ($fetch->updatePClasses('NL'))

				$this->html .= $this->displayConfirmation('Pclasses update for Netherlands');
			else
				$this->html .= $this->displayError('Failed updating pclasses for country Netherlands');
			}
			if (Tools::isSubmit('DELETE_PCLASS_NL'))
			{
				$delete = new KlarnaPrestaPclasses();

				if ($delete->deletePClasses('NL'))
					$this->html .= $this->displayConfirmation('Pclasses deleted for Netherlands');

				else
					$this->html .= $this->displayError('Failed deleting pclasses for Netherlands');
			}
		}
		elseif (Tools::isSubmit('PCLASS_DE') || Tools::isSubmit('DELETE_PCLASS_DE'))
		{
			if (Tools::isSubmit('PCLASS_DE'))
			{
			$fetch = new KlarnaPrestaPclasses();

			if ($fetch->updatePClasses('DE'))

				$this->html .= $this->displayConfirmation('Pclasses update for Germany');
			else
				$this->html .= $this->displayError('Failed updating pclasses for country Germany');
			}
			if (Tools::isSubmit('DELETE_PCLASS_DE'))
			{
				$delete = new KlarnaPrestaPclasses();

				if ($delete->deletePClasses('DE'))
					$this->html .= $this->displayConfirmation('Pclasses deleted for Germany');
				else
					$this->html .= $this->displayError('Failed deleting pclasses for Germany');
			}
		} elseif (Tools::isSubmit('PCLASS_FI') || Tools::isSubmit('DELETE_PCLASS_FI'))
		{
			if (Tools::isSubmit('PCLASS_FI'))
			{
			$fetch = new KlarnaPrestaPclasses();

			if ($fetch->updatePClasses('FI'))

				$this->html .= $this->displayConfirmation('Pclasses update for Finland');
			else
				$this->html .= $this->displayError('Failed updating pclasses for country Finland');
			}
			if (Tools::isSubmit('DELETE_PCLASS_FI'))
			{
				$delete = new KlarnaPrestaPclasses();

				if ($delete->deletePClasses('FI'))
					$this->html .= $this->displayConfirmation('Pclasses deleted for Finland');
				else
					$this->html .= $this->displayError('Failed deleting pclasses for Finland');
			}
		}
		if (Tools::isSubmit('saveBtn'))
		{
			$this->postValidation();
			if (!count($this->post_errors))

			$this->postProcess();

			foreach ($this->post_errors as $err)

					$this->html .= $this->displayError($err);

			$prod_id = (int)Configuration::get('KLARNA_INVOICE_PRODUCT');

			$prod = new Product($prod_id);

			$prod->price = (float)Tools::getValue('KLARNA_INVOICE_PRICE');

			$prod->id_tax_rules_group = (int)Tools::getValue('KLARNA_INVOICE_FEE_TAX');

			$prod->update();

		}

		$this->html .= '<br />';
		//setting the admin template
		$this->context->smarty->assign(array('module_dir' => $this->_path,
			'klarna_cron' => _PS_BASE_URL_._MODULE_DIR_.'klarnapayments/klarna_cron.php?token='.Tools::substr(Tools::encrypt('klarnapayments/cron'), 0, 10).''));

		$this->context->controller->addJS($this->_path.'views/js/klarnabackoffice.js');

		$this->html .= $this->context->smarty->fetch($this->local_path.'/views/templates/admin/admin.tpl');

		$this->html .= $this->renderForm();
		
		$this->html .= $this->renderList();

		return $this->html;
	}

	/***
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
					'html_content' => '<button type="submit" class="btn btn-default" name="PCLASS_SE">Update PClasses</button>'
				),
				array(
					'type' => 'html',
					'name' => 'html_data',
					'tab' => 'sweden',
					'html_content' => '<button type="submit" class="btn btn-default" name="DELETE_PCLASS_SE">Delete PClasses</button>'
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
					'html_content' => '<button type="submit" class="btn btn-default" name="PCLASS_NO">Update PClasses</button>'
				),
				array(
					'type' => 'html',
					'name' => 'html_data',
					'tab' => 'norway',
					'html_content' => '<button type="submit" class="btn btn-default" name="DELETE_PCLASS_NO">Delete PClasses</button>'
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
					'html_content' => '<button type="submit" class="btn btn-default" name="PCLASS_DK">Update PClasses</button>'
				),
				array(
					'type' => 'html',
					'name' => 'html_data',
					'tab' => 'denmark',
					'html_content' => '<button type="submit" class="btn btn-default" name="DELETE_PCLASS_DK">Delete PClasses</button>'
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
					'html_content' => '<button type="submit" class="btn btn-default" name="PCLASS_DE">Update PClasses</button>'
				),
				array(
					'type' => 'html',
					'name' => 'html_data',
					'tab' => 'germany',
					'html_content' => '<button type="submit" class="btn btn-default" name="DELETE_PCLASS_DE">Delete PClasses</button>'
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
					'html_content' => '<button type="submit" class="btn btn-default" name="PCLASS_FI">Update PClasses</button>'
				),
				array(
					'type' => 'html',
					'name' => 'html_data',
					'tab' => 'finland',
					'html_content' => '<button type="submit" class="btn btn-default" name="DELETE_PCLASS_FI">Delete PClasses</button>'
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
					'html_content' => '<button type="submit" class="btn btn-default" name="PCLASS_NL">Update PClasses</button>'
				),
				array(
					'type' => 'html',
					'name' => 'html_data',
					'tab' => 'netherlands',
					'html_content' => '<button type="submit" class="btn btn-default" name="DELETE_PCLASS_NL">Delete PClasses</button>'
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
				'label' => $this->l('Invoice product price'),
				'desc' => $this->l('Fill in the price number for the invoice fee product'),
				'name' => 'KLARNA_INVOICE_PRICE',
				'tab' => 'general',
				'class' => 'fixed-width-lg'
					),
				array(
				'type' => 'select',
				'label' => $this->l('Invoice product tax'),
				'desc' => $this->l('Fill in the invoice fee tax'),
				'name' => 'KLARNA_INVOICE_FEE_TAX',
				'tab' => 'general',
				'options' => array(
					'query' => Tax::getTaxes(),
					'id' => 'id_tax',
					'name' => 'rate',
						),
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
					'type' => 'color',
					'label' => $this->l('Color button'),
					'tab' => 'general',
					'hint' => $this->l('Only used for Klarna Checkout'),
					'desc' => $this->l('Select a color for the checkout button'),
					'name' => 'KLARNA_CHECKOUT_COLOR_BUTTON'
				),
				array(
					'type' => 'color',
					'label' => $this->l('Text color on button'),
					'hint' => $this->l('Only used for Klarna Checkout'),
					'desc' => $this->l('Select a color the text on the checkout button'),
					'tab' => 'general',
					'name' => 'KLARNA_CHECKOUT_COLOR_BUTTON_TEXT'
				),
				array(
					'type' => 'color',
					'label' => $this->l('Color checkbox'),
					'hint' => $this->l('Only used for Klarna Checkout'),
					'desc' => $this->l('Select a color for the checkbox'),
					'tab' => 'general',
					'name' => 'KLARNA_CHECKOUT_COLOR_CHECKBOX'
				),
				array(
					'type' => 'color',
					'label' => $this->l('Color checkbox checkmark'),
					'hint' => $this->l('Only used for Klarna Checkout'),
					'desc' => $this->l('Select a color for the checkmark'),
					'tab' => 'general',
					'name' => 'KLARNA_CHECKOUT_COLOR_CHECKBOX_CHECKMARK'
				),
				array(
					'type' => 'color',
					'label' => $this->l('Color header'),
					'hint' => $this->l('Only used for Klarna Checkout'),
					'desc' => $this->l('Select a color for the header'),
					'tab' => 'general',
					'name' => 'KLARNA_CHECKOUT_COLOR_HEADER'
				),
				array(
					'type' => 'color',
					'label' => $this->l('Color link'),
					'hint' => $this->l('Only used for Klarna Checkout'),
					'desc' => $this->l('Select a color for the link'),
					'tab' => 'general',
				'name' => 'KLARNA_CHECKOUT_COLOR_LINK'
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


	public function renderList()
	{
		$active_countries = KlarnaConfigHandler::returnActiveCountries();

		foreach ($active_countries as $countries)
		{
			$pclasses_uri = dirname(__FILE__).'/pclasses/pclasses'.Tools::strtolower($countries).'.json';
			if (file_exists($pclasses_uri))
			{
			$klarna_merchant_id = KlarnaConfigHandler::getMerchantID($countries);
			$fetch_json = Tools::file_get_contents($pclasses_uri);
			$json_assoc = Tools::jsonDecode($fetch_json, true);
			$helper_array = $json_assoc[$klarna_merchant_id];
			}
		}
			

			$this->fields_list = array(
				'eid' => array(
								'title' => $this->l('Merchant eid'),
								'align' => 'center',
								'width' => 'auto'
							),
							'id' => array(
								'title' => $this->l('Id'),
								'width' => 'auto',
								),
							'months' => array(
								'title' => $this->l('Months'),
								'width' => 'auto',
							),
							'startfee' => array(
								'title' => $this->l('Startfee'),
								'width' => 'auto',
							),
							'invoicefee' => array(
								'title' => $this->l('Invoicefee'),
								'width' => 'auto',
							),
							'interestrate' => array(
								'title' => $this->l('Interestrate'),
								'width' => 'auto',
							),
							'minamount' => array(
								'title' => $this->l('Minamount'),
								'width' => 'auto',
							),
							'country' => array(
								'title' => $this->l('Country'),
								'width' => 'auto',
							),
							'type' => array(
								'title' => $this->l('Type'),
								'width' => 'auto',
							),
							'expire' => array(
								'title' => $this->l('Expire'),
								'width' => 'auto',
							),
					);

					$helper = new HelperList();
					$helper->shopLinkType = '';
					$helper->simple_header = true;
					$helper->identifier = 'eid';
					$helper->no_link = true;
					$helper->show_toolbar = false;
					$helper->title = $this->l('Pclasses for country Klarna Online');
					$helper->table = $this->name;
					$helper->token = Tools::getAdminTokenLite('AdminModules');
					$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
					return $helper->generateList((array)$helper_array, $this->fields_list);

	}
	/**
	* Get config values
	*
	* @return array
	* @author Johan Tedenmark
	*/

	public function getConfigFieldsValues()
	{
		$return_array = array();
		$configuration = new KlarnaConfigHandler();
		foreach ($this->input_vals as $key_input => $value_input)
		{
			foreach ($value_input as $update_value)
			{
			foreach ($configuration->settings as $key => $value)
			{
				if ($key_input == 'MULTI_LOCALE')
				$return_array[$update_value.$key] = Tools::getValue($update_value.$key, Configuration::get($update_value.$key));

					if ($key_input == 'GENERAL')
						$return_array[$update_value] = Tools::getValue($update_value, Configuration::get($update_value));

			}
			}

		}
		return $return_array;
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

	public function hookDisplayRightColumnProduct()
	{
		if (!KlarnaConfigHandler::isCountryActive(Country::getIsoById($this->context->country->id)))
			return;

		if ($id_product = (int)Tools::getValue('id_product'))
			$product = new Product($id_product, true, $this->context->language->id, $this->context->shop->id);

		if (!Validate::isLoadedObject($product))
			return;

		$product_price = $product::getPriceStatic($id_product);

		$this->smarty->assign(array(
			'eid' => KlarnaConfigHandler::getMerchantID(Country::getIsoById($this->context->country->id)),
			'price' => $product_price,
			'lang' => Tools::strtolower(KlarnaLocalization::getPrestaLanguage(Language::getIsoById($this->context->language->id))),
			'fee' => KlarnaInvoiceFeeHandler::getInvoiceFeePrice(self::INVOICE_REF)
			));

		return $this->display(__FILE__, 'klarnapartinfo.tpl');
	}

}