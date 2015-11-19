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

class KlarnaPrestaConfig
{
	public function __construct()
	{
		$this->klarna = null;
		$this->country = null;
	}
	

	public function setKlarnaConfig($country = null, $locale = false)
	{
		 if (($this->klarna === null) || (($country != null) && ($this->country != $country)))
		 {
			if ($country == null || KlarnaCountry::fromCode($country) === null)
			{
				$eid = 1;
				$secret = 'invalid';
			}
			else
			{
				$eid = Configuration::get('KLARNA_EID_'.$country);
				$secret = Configuration::get('KLARNA_SECRET_'.$country);

			}
		}
		
		$storage_file = dirname(dirname(dirname(__FILE__))).'/pclasses/config'.Tools::strtolower($country).'.json';
		$this->kconfig = new KlarnaConfig($storage_file);

		$this->kconfig['eid'] = $eid;
		$this->kconfig['secret'] = $secret;
		if ($locale === true)
		{
			$localization = new KlarnaLocalization($country);
			$klarna = new KlarnaPrestaApi();
			$klarna_locale = $klarna->getLocale($localization->getCountry(), $localization->getLanguage(), $localization->getCurrency());
			
		$this->kconfig['country'] = $klarna_locale['country'];
		$this->kconfig['language'] = $klarna_locale['language'];
		$this->kconfig['currency'] = $klarna_locale['currency'];    
		}

		$this->kconfig['mode'] = KlarnaConfigHandler::mode();
		$this->kconfig['pcStorage'] = 'json';
		$this->kconfig['pcURI'] = dirname(dirname(dirname(__FILE__))).'/pclasses/pclasses'.Tools::strtolower($country).'.json';

		$klarna = new KlarnaPrestaApi();
		$klarna->setConfig($this->kconfig);
		if (KlarnaCountry::fromCode($country) !== null)
		{
				$klarna->setCountry($country);
		}

		$this->country = $country;
		$this->klarna = $klarna;

	}

	public function deleteKlarnaConfig($country) {
		$storage_file = dirname(dirname(dirname(__FILE__))).'/pclasses/config'.Tools::strtolower($country).'.json';
		$this->kconfig = new KlarnaConfig($storage_file);
		$klarna = new KlarnaPrestaApi();
		$klarna->clear($this->kconfig);
	}

}
