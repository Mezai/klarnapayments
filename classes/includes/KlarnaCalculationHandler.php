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

class KlarnaCalculate
{

	public function __construct($country, array $klarna_settings)
	{
		$this->country = $country;
		$this->klarna_settings = $klarna_settings;
		$this->environment = (Configuration::get('KLARNA_ENVIRONMENT') == 'live') ? Klarna::LIVE : KLARNA::BETA;
	}

	public function klarnaCalculateMonthlyCost($amount, $type, $id)
	{
		$k = KlarnaConfigHandler::setConfigurationByLocale($this->country, $this->environment, $this->klarna_settings);
		
		$pclass = $k->getPClass($id);

		if ($pclass)
			$monthly = KlarnaCalc::calc_monthly_cost($amount, $pclass, $type);
			return $monthly;

	}

	public function klarnaCalculateTotalCredit($amount, $type, $id)
	{
		$k = KlarnaConfigHandler::setConfigurationByLocale($this->country, $this->environment, $this->klarna_settings);

		$pclass = $k->getPClass($id);

		if ($pclass)
			$total = KlarnaCalc::total_credit_purchase_cost($amount, $pclass, $type);
		return $total;
	}


	public function calculateKlarnaApr($amount, $type, $id)
	{
		
		$k = KlarnaConfigHandler::setConfigurationByLocale($this->country, $this->environment, $this->klarna_settings);

		$pclass = $k->getPClass($id);

		if ($pclass)
			$apr = KlarnaCalc::calc_apr($amount, $pclass, $type);
			return $apr;
	}
}