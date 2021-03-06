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

class KlarnaAdressPresta
{
	public static function buildKlarnaAddr($presta, $customer)
	{
		$country = new Country($presta->id_country);
		$address = KlarnaPrestaEncoding::encode($presta->address1);

		$house_nr = '';
		$house_ext = '';

		if (($country->iso_code == 'NL') || ($country->iso_code == 'DE'))
		{
			$split = self::splitAddress($address);
			$address = @$split[0];
			$house_nr = @$split[1];
			$house_ext = @$split[2];
		}

		$addr = new KlarnaAddr(KlarnaPrestaEncoding::encode($customer->email),
			KlarnaPrestaEncoding::encode($presta->phone),
			KlarnaPrestaEncoding::encode($presta->phone_mobile),
			KlarnaPrestaEncoding::encode($presta->firstname),
			KlarnaPrestaEncoding::encode($presta->lastname),
			null,
			$address,
			KlarnaPrestaEncoding::encode($presta->postcode),
			KlarnaPrestaEncoding::encode($presta->city),
			KlarnaPrestaEncoding::encode($country->iso_code),
			$house_nr,
			$house_ext);

		if ($presta->company)
			$addr->setCompanyName($presta->company);

		return $addr;
	}

	public static function splitAddress($address)
	{
		$has_match = preg_match('/^[^0-9]*/', $address, $match);

		if (!$has_match)
			return array($address, '', '');

		$address = str_replace($match[0], '', $address);
		$street = trim($match[0]);

		if (Tools::strlen($address == 0))
			return array($street, '', '');

		$addr_array = explode(' ', $address);

		$housenumber = array_shift($addr_array);

		if (count($addr_array) == 0)

			return array($street, $housenumber, '');

		$extension = implode(' ', $addr_array);

		return array($street, $housenumber, $extension);
	}
}