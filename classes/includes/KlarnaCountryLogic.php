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

class KlarnaCountryLogic
{
	protected $country;
	public function __construct(KlarnaLocalization $locale)
	{
		$this->country = Tools::strtoupper($locale->getCountryCode());
	}


	public function needGender()
	{
		switch ($this->country)
		{
			case 'NL':
			case 'DE':
			case 'AT':
				return true;
			default:
				return false;
		}
	}

	public function needDateOfBirth()
	{
		switch ($this->country)
		{
				case 'NL':
				case 'DE':
				case 'AT':
						return true;
				default:
						return false;
		}
	}

	public function getSplitCountry()
	{
		switch ($this->country)
		{
			case 'DE':
				return array('street', 'house_number');
				case 'NL':
				return array('street', 'house_number', 'house_extension');
			default:
				return array('street');
		}
	}

	public function useGetAddress()
	{
		switch ($this->country)
		{
			case 'SE':
				return true;
			default:
				return false;
		}
	}

	public function isBusinessAllowed()
	{
		switch ($this->country)
		{
				case 'NL':
				case 'DE':
				case 'AT':
			return false;
			default:
				return true;
		}
	}

	public function isBelowLimit($sum, $method)
	{
		if ($this->country !== 'NL')

			return true;

		if ($method === 'invoice')

			return true;

		if (((double)$sum) <= 250.0)

			return true;

		return false;
	}

	public function checkLocale($country, $currency, $language, $type)
	{
		if ($type == 'payment' || $type == 'checkout')
		{
			if ($country == 'SE' && $currency == 'SEK' && $language == 'sv')
				return true;
			elseif ($country == 'DE' && $currency == 'EUR' && $language == 'de')
				return true;
			elseif ($country == 'DK' && $currency == 'DKK' && $language == 'da' )
				return true;
			elseif ($country == 'NL' && $currency == 'EUR' && $language == 'nl')
				return true;
			elseif ($country == 'NO' && $currency == 'NOK' && $language == 'no')
				return true;
			elseif ($country == 'FI' && $currency == 'EUR' && $language == 'fi')
				return true;
			elseif ($country == 'AT' && $currency == 'EUR' && $language == 'at')
				return true;
			elseif ($type == 'checkout' && $country == 'FI' && $currency == 'EUR' && $language == 'sv')
				return true;
			else
				return false;
		}

	}

}
