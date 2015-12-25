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

class KlarnaPrestaPclasses extends KlarnaPrestaConfig
{

	public function updatePClasses($country)
	{
		$config = new KlarnaPrestaConfig();
		$config->setKlarnaConfig($country);
		$config->klarna->clearPClasses();

		try {
			$config->klarna->fetchPClasses($country);
			return true;
		} catch(Exception $e) {
			Logger::addLog('Klarna module: failed fetching pclasses for country '.$country);
			return false;
		}

	}

	public function deletePClasses($country)
	{
		$config = new KlarnaPrestaConfig();
		$config->setKlarnaConfig($country);
		
		try {
			$config->klarna->clearPClasses();
			return true;
		} catch (Exception $e) {
			Logger::addLog('Klarna module: failed deleting pclasses for country '.$country);
			return false;
		}
	}

	public function getKlarnaPClasses($country)
	{
		$config = new KlarnaPrestaConfig();
		$config->setKlarnaConfig($country);

		try
		{
			$pclasses = $config->klarna->getPClasses();
			return $pclasses;
		} catch (Exception $e) {
			Logger::addLog('Failed retreiving pclasses for country '.$country);
			return false;
		}		
	}
}