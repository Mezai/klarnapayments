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

class KlarnaConfigHandler 
{
		public $settings;

		public function __construct()
		{
				$this->settings = array(

											"SE"  => array(

																					 "active" => (int)Configuration::get('ACTIVE_SE'),

																					 "klarna_eid" => (int)Configuration::get('KLARNA_EID_SE'),

																					 "klarna_secret" => (String)Configuration::get('KLARNA_SECRET_SE'),

																						"klarna_part" => (int)Configuration::get('KLARNA_PART_SE'),

																						"klarna_invoice" => (int)Configuration::get('KLARNA_INVOICE_SE'),

																						"klarna_checkout" => (int)Configuration::get('KLARNA_CHECKOUT_SE')

																					 ),

											"NO"  => array(

																					 "active" => (int)Configuration::get('ACTIVE_NO'),

																					 "klarna_eid" => (int)Configuration::get('KLARNA_EID_NO'),

																					 "klarna_secret" => (String)Configuration::get('KLARNA_SECRET_NO'),

																						"klarna_part" => (int)Configuration::get('KLARNA_PART_NO'),

																						"klarna_invoice" => (int)Configuration::get('KLARNA_INVOICE_NO'),

																						"klarna_checkout" => (int)Configuration::get('KLARNA_CHECKOUT_NO')

																					 ),

											"FI"  => array(

																					 "active" => (int)Configuration::get('ACTIVE_FI'),

																					 "klarna_eid" => (int)Configuration::get('KLARNA_EID_FI'),

																					 "klarna_secret" => (String)Configuration::get('KLARNA_SECRET_FI'),

																						"klarna_part" => (int)Configuration::get('KLARNA_PART_FI'),

																						 "klarna_invoice" => (int)Configuration::get('KLARNA_INVOICE_FI'),

																																								 "klarna_checkout" => (int)Configuration::get('KLARNA_CHECKOUT_FI')

																					 ),

											"DK"  => array(

																					 "active" => (int)Configuration::get('ACTIVE_DK'),

																					 "klarna_eid" => (int)Configuration::get('KLARNA_EID_DK'),

																					 "klarna_secret" => (String)Configuration::get('KLARNA_SECRET_DK'),

																						"klarna_part" => (int)Configuration::get('KLARNA_PART_DK'),

																						"klarna_invoice" => (int)Configuration::get('KLARNA_INVOICE_DK')

																					 ),

											"NL"  => array(

																					 "active" => (int)Configuration::get('ACTIVE_NL'),

																					 "klarna_eid" => (int)Configuration::get('KLARNA_EID_NL'),

																					 "klarna_secret" => (String)Configuration::get('KLARNA_SECRET_NL'),

																						"klarna_part" => (int)Configuration::get('KLARNA_PART_NL'),

																						"klarna_invoice" => (int)Configuration::get('KLARNA_INVOICE_NL')



																					 ),

											"DE"  => array(

																					 "active" => (int)Configuration::get('ACTIVE_DE'),

																					 "klarna_eid" => (int)Configuration::get('KLARNA_EID_DE'),

																					 "klarna_secret" => (String)Configuration::get('KLARNA_SECRET_DE'),

																						"klarna_part" => (int)Configuration::get('KLARNA_PART_DE'),

																						 "klarna_invoice" => (int)Configuration::get('KLARNA_INVOICE_DE'),

																						 "klarna_checkout" => (int)Configuration::get('KLARNA_CHECKOUT_DE')



																					 ),

											"AT"  => array(

																					 "active" => (int)Configuration::get('ACTIVE_AT'),

																					 "klarna_eid" => (int)Configuration::get('KLARNA_EID_AT'),

																					 "klarna_secret" => (String)Configuration::get('KLARNA_SECRET_AT'),

																						"klarna_part" => (int)Configuration::get('KLARNA_PART_AT'),

																						"klarna_invoice" => (int)Configuration::get('KLARNA_INVOICE_AT')



																					 ),

											);
		}

		public static function getInstance() {
				return new self();
		}


		public static function getSettings()
		{
			$klarna_settings = KlarnaConfigHandler::getInstance();


			return $klarna_settings->settings;

		}


	/**
	*@param $country_iso = 'SE' etc
	*@param $klarna_settings = array containing our settings from PS
	*@param $type string invoice, checkout or part to check if part payment / checkout / invoice is active
	*@return boolean true|false
	*/


		public static function checkConfigurationByLocale($country_iso, $type = null)
		{
			if (!is_string($country_iso)) {
				return;
			}

			foreach (KlarnaConfigHandler::getSettings() as $key => $value) {
				if ($key == $country_iso) {
					if ((int)$value['active'] == 1 && $value['klarna_eid'] != "" && $value['klarna_secret'] != "") {
						
										if ($type == 'part' && (int)$value['klarna_part'] === 1) {
												return true;
										} else {
												return false;
										}

										if ($type == 'invoice' && (int)$value['klarna_invoice'] === 1) {
												return true;
										} else {
												return false;
										}

										if ($type == 'checkout' && (int)$value['klarna_checkout'] == 1) {
												return true;
										} else {
												return false;
										}

									return true;  
								}
							 return false; 
						}
			}	
		}


		/**
		*Get the merchant id for a given country iso eg 'SE'
		*@param $country string 'SE'
		*@param array Klarna PS settings
		*@return string merchant id | null 
		*/

		public static function getMerchantID($country)
		{
			if (!is_string($country)){
				return;
				}

			foreach (KlarnaConfigHandler::getSettings() as $key => $value) {
				$country = Tools::strtoupper($country);
				if ($country === $key)
				{
					if ((int)$value['active'] == 1){
						return $value['klarna_eid'];
					}
				}
			}
			return null;
		}


		/**
		*Get the klarna secret from a given country
		*@param $country string 'SE'
		*@param array Klarna settings
		*@return string klarna secret | null
		*/


		public static function getKlarnaSecret($country)
		{	
			if (!is_string($country)) {
				return;
			}

			foreach (KlarnaConfigHandler::getSettings() as $key => $value) {
				$country = Tools::strtoupper($country);
				if ($key === $country) 
				{
					if ((int)$value['active'] == 1) {
						return $value['klarna_secret'];
					
					}
				}
			}
			return null;
		}

		public static function isKlarnaInvoiceActive($country)
		{ 
			if (!is_string($country)) {
				return;
			}

			foreach (KlarnaConfigHandler::getSettings() as $key => $value) {
				$country = Tools::strtoupper($country);
				if ($key === $country) 
				{
					if ((int)$value['active'] == 1 && (int)$value['klarna_invoice'] == 1) {
						return true;
					
					}
				}
			}
			return false;
		}

		public static function isKlarnaPartActive($country)
		{ 
			if (!is_string($country)) {
				return;
			}

			foreach (KlarnaConfigHandler::getSettings() as $key => $value) {
				$country = Tools::strtoupper($country);
				if ($key === $country) 
				{
					if ((int)$value['active'] == 1 && (int)$value['klarna_part'] == 1) {
						return true;
					
					}
				}
			}
			return false;
		}


		/**
		*
		*@param $settings PS settings array 
		*@return an array containing configured countries
		*/

		public static function returnActiveCountries()
		{

			$country_array = array('SE', 'NO', 'DK', 'NL', 'DE', 'FI');
			$active_countries = array();
			foreach (KlarnaConfigHandler::getSettings() as $key => $value) {
				foreach ($country_array as $country_value) {
					if ($country_value ==  $key) {
						if ((int)$value['active'] == 1 && $value['klarna_eid'] != "" && $value['klarna_secret'] != "") {
							array_push($active_countries, $key);
						}

					}
				}

			}
			return $active_countries;
		}

		public static function isCountryActive($country)
		{
			$active_array = self::returnActiveCountries(KlarnaConfigHandler::getSettings());

			if (in_array($country, $active_array)) {
				return true;
			} else {
				return false;
			}
		}


		/**
	*
	*@return int Klarna::LIVE or KLARNA::BETA
	*
	*/
		public static function mode()
		{
			switch (Configuration::get('KLARNA_ENVIRONMENT')) {
				case 'live':
					return KLARNA::LIVE;
				case 'beta':
					return KLARNA::BETA;
				}
		}


 

	private static function getKlarnaLocaleConfiguration($country_iso)
	{
		switch ($country_iso)
		{
			case 'SE':
						$locale = new KlarnaLocalization('SE', 'SV', 'SEK');
						$currency = $locale->getCurrency();
						$country = $locale->getCountry();
						$language = $locale->getLanguage();
				return array($country, $language, $currency);
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
}

