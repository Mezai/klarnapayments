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

require_once(dirname(__FILE__).'/classes/payment/KlarnaPrestashopCore.php');



class KlarnaPayments extends PaymentModule
{
	private $html = '';
	private $post_errors = array();
	public $settings;

	public $input_vals = array(
		"MULTI_LOCALE" => array(
			'KLARNA_EID_', 'KLARNA_SECRET_', 'ACTIVE_', 'KLARNA_PART_', 'KLARNA_INVOICE_', 'KLARNA_CHECKOUT_'),
		"GENERAL" => array('KLARNA_ENVIRONMENT', 'KLARNA_INVOICE_FEE_TAX', 'KLARNA_CHECKOUT_COLOR_LINK', 'KLARNA_CHECKOUT_COLOR_BUTTON',
			'KLARNA_CHECKOUT_COLOR_CHECKBOX', 'KLARNA_CHECKOUT_COLOR_HEADER', 'KLARNA_CHECKOUT_COLOR_BUTTON_TEXT', 'KLARNA_CHECKOUT_COLOR_CHECKBOX_CHECKMARK', 'KLARNA_INVOICE_FEE_TAX',
			'KLARNA_INVOICE_FEE', 'KLARNA_INVOICE_PRICE', 'KLARNA_INVOICE_METHOD', 'KLARNA_INVOICE_PRODUCT'),
		);

	const INVOICE_REF = 'Invoice fee';
	public $klarna;
	public $country;

	public function __construct()
	{
		$this->name = 'klarnapayments';
		$this->tab = 'payments_gateways';
		$this->limited_countries = array('se', 'no', 'fi', 'dk', 'de', 'nl');
		$this->module_key = '9ba314b95673c695df2051398019734c';
		$this->version = '1.0.2';
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
		$this->klarna = null;
        $this->country = null;

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
	&& $this->registerHook('shoppingCartExtra')
	&& $this->registerHook('displayBeforeCarrier')
	&& $this->registerHook('header');

	}

	 public function requireApi($country = null)
    {
        if (($this->klarna === null)
            || (($country != null) && ($this->country != $country))
        ) {
            if ($country == null || KlarnaCountry::fromCode($country) === null) {
                $eid = 1;
                $secret = 'invalid';
            } else {
                $eid = Configuration::get('KLARNA_EID_' . $country);
                $secret = Configuration::get('KLARNA_SECRET_' . $country);
            }

            $this->kconfig = new KlarnaConfig(null);
            $this->kconfig['eid'] = $eid;
            $this->kconfig['secret'] = $secret;
            $this->kconfig['mode'] = (Configuration::get('KLARNA_ENVIRONMENT') === 'live') ? KLARNA::LIVE : KLARNA::BETA;
            $this->kconfig['pcStorage'] = 'json';
            $this->kconfig['pcURI'] = dirname(__FILE__).'/pclasses/pclasses.json';

            Klarna::printDebug("config", $this->kconfig);

            $klarna = new KlarnaPrestaApi();
            $klarna->setConfig($this->kconfig);
            if (KlarnaCountry::fromCode($country) !== null) {
                $klarna->setCountry($country);
            }

            $this->country = $country;
            $this->klarna = $klarna;
        }
    }

    public function updatePClasses()
    {
        $countries = KlarnaConfigHandler::returnActiveCountries($this->settings);
        $this->requireApi();
        $this->klarna->clearPClasses();

        foreach ($countries as $country) {
            try {
                $this->requireApi($country);
            } catch(KlarnaException $e) {
                $this->_postErrors[] = "$country not fully configured";
                continue;
            }
            try {
                $this->klarna->fetchPClasses();
            } catch(Exception $e) {
                $this->_postErrors[] = "Failed to get pclasses for $country: " .
                    strval($e);
            }
        }
    }


	public function hookDisplayShoppingCart()
	{

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

		$invoice_product = new Product(Configuration::get('KLARNA_INVOICE_PRODUCT'));

		$klarna_uninstall = new KlarnaInstall();
		$klarna_uninstall->deleteConfiguration();

		return parent::uninstall()
		&& $tab_main->delete()
		&& $tab_pay->delete()
		&& $invoice_product->delete();
	}

	public function getJsonUri()
	{
		return dirname(__FILE__).'/pclasses/pclasses.json';
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

	private function postProcess()
	{
		if (Tools::isSubmit('saveBtn'))
		{

			// better looop cause we have alot of values 

			foreach ($this->input_vals as $keys => $values) {
				
				foreach ($values as $update_value) {
					foreach ($this->settings as $key_iso => $country_iso) {
						if ($keys == "MULTI_LOCALE") {
						Configuration::updateValue((string)$update_value.$key_iso, Tools::getValue((string)$update_value.$key_iso));
						}
						if ($keys == "GENERAL") {
						Configuration::updateValue((string)$update_value, Tools::getValue((string)$update_value));
						}	
					}
				}		
			
			}

			if (Tools::getValue('KLARNA_INVOICE_PRICE') || Tools::getValue('KLARNA_INVOICE_FEE_TAX')) {
				$tax = new TaxRulesGroup(Configuration::get('KLARNA_INVOICE_FEE_TAX'));
				$prod = new Product(Configuration::get('KLARNA_INVOICE_PRODUCT'));
				$prod->price = floatval(Tools::getValue('KLARNA_INVOICE_PRICE'));
				$prod->id_tax_rules_group = $tax->id;
				$prod->update();
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
		// Check if module is active and that the country set, check for valid currency, country aswell
		if (!$this->active || !KlarnaConfigHandler::isCountryActive(Country::getIsoById($this->context->country->id), $this->settings))
			return;



			//$this->smarty->assign('validation_url', (Configuration::get('PS_SSL_ENABLED') ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].__PS_BASE_URI__.'index.php?fc=module&module=klarnapayments&controller=payment');




		//return $this->display(__FILE__, 'payment.tpl');
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

	
	public function getContent()
	{
		if (Tools::isSubmit('pclass_se'))
		{
			$this->updatePClasses();
		}



		if (Tools::isSubmit('saveBtn'))
		{
			$this->postValidation();
			if (!count($this->post_errors)) {
				$this->postProcess();
			}
			foreach ($this->post_errors as $err) {
					$this->html .= $this->displayError($err);
			}

		}




		$this->html .= '<br />';
		//setting the admin template
		$this->context->smarty->assign(array('module_dir' => $this->_path));

		$this->context->controller->addJS($this->_path.'views/js/klarnabackoffice.js');

		$this->html .= $this->context->smarty->fetch($this->local_path.'/views/templates/admin/admin.tpl');

		$this->html .= $this->renderForm();

		$this->html .= $this->renderList();

		return $this->html;
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
				array(
				'type' => 'select',
				'label' => $this->l('Invoice method'),
				'desc' => $this->l('Select if you wish Klarna to send the invoice by mail or post'),
				'name' => 'KLARNA_INVOICE_METHOD',
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
	
			$helper->tpl_vars['fields_value']['KLARNA_INVOICE_PRICE'] = '0';
		return $helper->generateForm(array($fields_form));
	}


	public function renderList()
	{
		$pclasses_uri = dirname(__FILE__).'/pclasses/pclasses.json';
		$fetch_json = Tools::file_get_contents($pclasses_uri);
		$json_assoc = Tools::jsonDecode($fetch_json, true);
		//return active countries 

		$active_countries = KlarnaConfigHandler::returnActiveCountries($this->settings);

		foreach ($active_countries as $countries) {
			$klarna_merchant_id = KlarnaConfigHandler::getMerchantID($countries, $this->settings);


			$helper_array = $json_assoc[$klarna_merchant_id];

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

		foreach ($this->input_vals as $key_input => $value_input) {
			foreach ($value_input as $update_value) {
			foreach ($this->settings as $key => $value) {
				if ($key_input == "MULTI_LOCALE") {
				$return_array[$update_value.$key] = Tools::getValue((string)$update_value.$key, Configuration::get((string)$update_value.$key));
					
					}
					if ($key_input == "GENERAL") {
						$return_array[$update_value] = Tools::getValue((string)$update_value, Configuration::get((string)$update_value));
					}

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

	private function updateDatabase($invoice_number, $id_order)
	{
		if (!Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'klarna_orders` SET `id_invoicenumber` = '.$invoice_number.' WHERE `id_order` = '.(int)$id_order))
		die(Tools::displayError('Error when updating Klarna database'));

	}


}
