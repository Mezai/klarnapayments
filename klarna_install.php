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

class KlarnaInstall extends KlarnaPayments
{
	/**
	* Create database table
	*
	* @return Bool result
	* @author Johan Tedenmark
	*/

	public function createTable()
	{
		if (!Db::getInstance()->Execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'klarna_orders` (
			`id_order` int(10) unsigned NOT NULL,
			`id_reservation` varchar(255) NOT NULL,
			`customer_firstname` varchar(255) NOT NULL,
			`customer_lastname` varchar(255) NOT NULL,
      		`id_invoicenumber` varchar(255) NOT NULL,
			`payment_status` varchar(255) NOT NULL,
			`risk_status` varchar(7) NOT NULL,
			`customer_country` varchar(2) NOT NULL,
			PRIMARY KEY(`id_order`)
			) ENGINE ='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8'))
			return false;

	}


	public function addTabs()
	{
		$parent_tab = new Tab();
		
		foreach (Language::getLanguages() as $language)
		
		$parent_tab->name[$language['id_lang']] = 'Klarna Payments';
				
		$parent_tab->class_name = 'KlarnaMain';
		$parent_tab->id_parent = 0;
		$parent_tab->module = $this->name;
		$parent_tab->add();

		// Link payment

		$tab_link = new Tab();

		foreach (Language::getLanguages() as $language)
		
		$tab_link->name[$language['id_lang']] = 'Handle klarna payments';

		$tab_link->class_name = 'KlarnaOrders';
		$tab_link->id_parent = $parent_tab->id;
		$tab_link->module = $this->name;
		$tab_link->add();
	}

	public function createInvoiceFee()
	{
		$rulesgroup = new TaxRulesGroup(Configuration::get('KLARNA_TAX_GROUP'));

		if (!$rulesgroup->id) {
            $rulesgroup = new TaxRulesGroup();
            $rulesgroup->active = true;
            $rulesgroup->name = 'Klarna tax fee';
            $rulesgroup->add();
            Configuration::updateValue('KLARNA_TAX_GROUP', $rulesgroup->id);
        }


        $invoice_fee = new Product(Configuration::get('KLARNA_INVOICE_PRODUCT'));

        if (!$invoice_fee->id) 
        {
        	$invoice_fee = new Product();
        	//$invoice_fee->name = array();
        	foreach (Language::getLanguages() as $language) {
                        
            $invoice_fee->name[$language['id_lang']] = KlarnaPrestaEncoding::decode('Invoicefee', "UTF-8");
            $invoice_fee->link_rewrite[$language['id_lang']] = 'invoicefee';
            
            }
       	 	$invoice_fee->reference = KlarnaPayments::INVOICE_REF;
            $invoice_fee->out_of_stock = 1; 
            $invoice_fee->active = 0;
            $invoice_fee->available_for_order = 1;
            $invoice_fee->id_tax_rules_group = $rulesgroup->id;
            
            $invoice_fee->add();
        
        	Configuration::updateValue('KLARNA_INVOICE_PRODUCT', (int)$invoice_fee->id);
    	}
    	
	}

	public function deleteConfiguration()
	{
		foreach ($this->input_vals as $keys => $values) {
				$configuration = new KlarnaConfigHandler();
				foreach ($values as $update_value) {
					foreach ($configuration->settings as $key_iso) {
						if ($keys == "MULTI_LOCALE") {
						Configuration::deleteByName((string)$update_value.$key_iso);
						}
						if ($keys == "GENERAL") {
						Configuration::deleteByName((string)$update_value);
						}	
					}
				}		
			
			}

	}

	/**
	* Create order statuses for klarna payments
	*
	* @return Bool result
	* @author Johan Tedenmark
	*/

	public function createStatus()
	{
		if (!Configuration::get('KLARNA_OS_PENDING'))
		{
			$order_state_pending = new OrderState();
			$order_state_pending->name = array();

			foreach (Language::getLanguages() as $language)
			{

				if (Tools::strtolower($language['iso_code']) == 'sv')

					$order_state_pending->name[$language['id_lang']] = 'Klarna avvaktande';

				elseif (Tools::strtolower($language['iso_code']) == 'no')

					$order_state_pending->name[$language['id_lang']] = 'Klarna ventende';

				elseif (Tools::strtolower($language['iso_code']) == 'da')

					$order_state_pending->name[$language['id_lang']] = 'Klarna verserende';

				elseif (Tools::strtolower($language['iso_code']) == 'fi')

					$order_state_pending->name[$language['id_lang']] = 'Klarna odotettaessa';

				elseif (Tools::strtolower($language['iso_code']) == 'de')

					$order_state_pending->name[$language['id_lang']] = 'Klarna schwebend';

				elseif (Tools::strtolower($language['iso_code']) == 'nl')

					$order_state_pending->name[$language['id_lang']] = 'Klarna hangende';

				else

					$order_state_pending->name[$language['id_lang']] = 'Klarna pending';

			}

			$order_state_pending->send_email = false;
			$order_state_pending->color = '#FFA500';
			$order_state_pending->hidden = true;
			$order_state_pending->delivery = false;
			$order_state_pending->logoable = true;
			$order_state_pending->invoice = false;
			$order_state_pending->paid = false;
			$order_state_pending->add();
			

				$source = _PS_MODULE_DIR_.'klarnapayments/views/img/klarnastate.gif';
				$destination = _PS_IMG_DIR_.'os/'.(int)$order_state_pending->id.'.gif';

				if (version_compare(_PS_VERSION_, '1.5.5', '<'))
					copy($source, $destination);
				else
					Tools::copy($source, $destination);



			Configuration::updateValue('KLARNA_OS_PENDING', (int)$order_state_pending->id);

		}

		if (!Configuration::get('KLARNA_OS_AUTHORIZED'))
		{
			$order_state_authorized = new OrderState();
			$order_state_authorized->name = array();

			foreach (Language::getLanguages() as $language)
			{

				if (Tools::strtolower($language['iso_code']) == 'sv')

					$order_state_authorized->name[$language['id_lang']] = 'Klarna reserverad';

				elseif (Tools::strtolower($language['iso_code']) == 'no')

					$order_state_authorized->name[$language['id_lang']] = 'Klarna autorisert';

				elseif (Tools::strtolower($language['iso_code']) == 'da')

					$order_state_authorized->name[$language['id_lang']] = 'Klarna autoriseret';

				elseif (Tools::strtolower($language['iso_code']) == 'fi')

					$order_state_authorized->name[$language['id_lang']] = 'Klarna sallittua';

				elseif (Tools::strtolower($language['iso_code']) == 'de')

					$order_state_authorized->name[$language['id_lang']] = 'Klarna zugelassen';

				elseif (Tools::strtolower($language['iso_code']) == 'nl')

					$order_state_authorized->name[$language['id_lang']] = 'Klarna geautoriseerd';

				else

				$order_state_authorized->name[$language['id_lang']] = 'Klarna authorized';

			}

			$order_state_authorized->send_email = true;
			$order_state_authorized->color = '#00FF00';
			$order_state_authorized->hidden = false;
			$order_state_authorized->delivery = false;
			$order_state_authorized->template = 'payment';
			$order_state_authorized->logoable = true;
			$order_state_authorized->invoice = true;
			$order_state_authorized->add();

				$source = _PS_MODULE_DIR_.'klarnapayments/views/img/klarnastate.gif';
				$destination = _PS_IMG_DIR_.'os/'.(int)$order_state_authorized->id.'.gif';

			if (version_compare(_PS_VERSION_, '1.5.5', '<'))
					copy($source, $destination);
			else
					Tools::copy($source, $destination);



			Configuration::updateValue('KLARNA_OS_AUTHORIZED', (int)$order_state_authorized->id);

		}

		if (!Configuration::get('KLARNA_OS_DENIED'))
		{
			$order_state_denied = new OrderState();
			$order_state_denied->name = array();

			foreach (Language::getLanguages() as $language)
			{
				if (Tools::strtolower($language['iso_code']) == 'sv')

				$order_state_denied->name[$language['id_lang']] = 'Klarna nekad';

				elseif (Tools::strtolower($language['iso_code']) == 'no')

					$order_state_denied->name[$language['id_lang']] = 'Klarna avvist';

				elseif (Tools::strtolower($language['iso_code']) == 'da')

					$order_state_denied->name[$language['id_lang']] = 'Klarna afvist';

				elseif (Tools::strtolower($language['iso_code']) == 'fi')

					$order_state_denied->name[$language['id_lang']] = 'Klarna kiisti';

				elseif (Tools::strtolower($language['iso_code']) == 'de')

					$order_state_denied->name[$language['id_lang']] = 'Klarna verweigert';

				elseif (Tools::strtolower($language['iso_code']) == 'nl')

					$order_state_denied->name[$language['id_lang']] = 'Klarna ontkende';

				else

				$order_state_denied->name[$language['id_lang']] = 'Klarna denied';

			}

			$order_state_denied->send_email = false;
			$order_state_denied->color = '#FF0000';
			$order_state_denied->hidden = true;
			$order_state_denied->delivery = false;
			$order_state_denied->paid = false;
			$order_state_denied->invoice = false;
			$order_state_denied->pdf_invoice = false;
			$order_state_denied->pdf_delivery = false;
			$order_state_denied->add();

			$source = _PS_MODULE_DIR_.'klarnapayments/views/img/klarnastate.gif';
			$destination = _PS_IMG_DIR_.'os/'.(int)$order_state_denied->id.'.gif';

			if (version_compare(_PS_VERSION_, '1.5.5', '<'))
					copy($source, $destination);
			else
					Tools::copy($source, $destination);



		Configuration::updateValue('KLARNA_OS_DENIED', (int)$order_state_denied->id);

		}

		if (!Configuration::get('KLARNA_OS_ACTIVATED'))
		{
			$order_state_activated = new OrderState();
			$order_state_activated->name = array();

			foreach (Language::getLanguages() as $language)
			{
				if (Tools::strtolower($language['iso_code']) == 'sv')

					$order_state_activated->name[$language['id_lang']] = 'Klarna aktiverad';

				elseif (Tools::strtolower($language['iso_code']) == 'no')

					$order_state_activated->name[$language['id_lang']] = 'Klarna aktivert';

				elseif (Tools::strtolower($language['iso_code']) == 'da')

					$order_state_activated->name[$language['id_lang']] = 'Klarna aktiveret';

				elseif (Tools::strtolower($language['iso_code']) == 'fi')

					$order_state_activated->name[$language['id_lang']] = 'Klarna aktivoitu';

				elseif (Tools::strtolower($language['iso_code']) == 'de')

					$order_state_activated->name[$language['id_lang']] = 'Klarna aktiviert';

				elseif (Tools::strtolower($language['iso_code']) == 'nl')

					$order_state_activated->name[$language['id_lang']] = 'Klarna geactiveerd';

				else

					$order_state_activated->name[$language['id_lang']] = 'Klarna activated';
			}

			$order_state_activated->send_email = true;
			$order_state_activated->hidden = false;
			$order_state_activated->template = 'shipped';
			$order_state_activated->paid = true;
			$order_state_activated->delivery = true;
			$order_state_activated->pdf_delivery = true;
			$order_state_activated->pdf_invoice = true;
			$order_state_activated->shipped = true;
			$order_state_activated->invoice = true;
			$order_state_activated->logable = true;
			$order_state_activated->color = '#108510';
			$order_state_activated->add();

			$source = _PS_MODULE_DIR_.'klarnapayments/views/img/klarnastate.gif';
			$destination = _PS_IMG_DIR_.'os/'.(int)$order_state_activated->id.'.gif';
			if (version_compare(_PS_VERSION_, '1.5.5', '<'))
					copy($source, $destination);
			else
					Tools::copy($source, $destination);


		Configuration::updateValue('KLARNA_OS_ACTIVATED', (int)$order_state_activated->id);
		}

		if (!Configuration::get('KLARNA_OS_REFUNDED'))
		{
			$order_state_refunded = new OrderState();
			$order_state_refunded->name = array();

			foreach (Language::getLanguages() as $language)
			{
				if (Tools::strtolower($language['iso_code']) == 'sv')

					$order_state_refunded->name[$language['id_lang']] = 'Klarna krediterad';

				elseif (Tools::strtolower($language['iso_code']) == 'no')

					$order_state_refunded->name[$language['id_lang']] = 'Klarna kreditert';

				elseif (Tools::strtolower($language['id_lang']) == 'da')

					$order_state_refunded->name[$language['id_lang']] = 'Klarna krediteret';

				elseif (Tools::strtolower($language['iso_code']) == 'fi')

					$order_state_refunded->name[$language['id_lang']] = 'Klarna hyvitetään';

				elseif (Tools::strtolower($language['iso_code']) == 'de')

					$order_state_refunded->name[$language['id_order']] = 'Klarna gutschrift';

				elseif (Tools::strtolower($language['iso_code']) == 'nl')

					$order_state_refunded->name[$language['id_lang']] = 'Klarna gecrediteerd';

				else

					$order_state_refunded->name[$language['id_lang']] = 'Klarna refunded';

			}

			$order_state_refunded->send_email = true;
			$order_state_refunded->hidden = false;
			$order_state_refunded->template = 'refund';
			$order_state_refunded->paid = false;
			$order_state_refunded->delivery = false;
			$order_state_refunded->pdf_delivery = false;
			$order_state_refunded->pdf_invoice = false;
			$order_state_refunded->shipped = false;
			$order_state_refunded->invoice = true;
			$order_state_refunded->logable = false;
			$order_state_refunded->color = '#ec2e15';
			$order_state_refunded->add();

				$source = _PS_MODULE_DIR_.'klarnapayments/views/img/klarnastate.gif';
				$destination = _PS_IMG_DIR_.'os/'.(int)$order_state_refunded->id.'.gif';

				if (version_compare(_PS_VERSION_, '1.5.5', '<'))
					copy($source, $destination);
				else
					Tools::copy($source, $destination);




			Configuration::updateValue('KLARNA_OS_REFUNDED', (int)$order_state_refunded->id);
		}
	}
}
