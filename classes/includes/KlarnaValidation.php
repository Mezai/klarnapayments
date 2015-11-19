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

class KlarnaValidation
{

	public static function getPattern($country)
	{
		switch ($country)
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

	public static function getPlaceholder($country)
	{
		switch ($country)
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
}