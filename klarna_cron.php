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

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');

if (Tools::substr(Tools::encrypt('klarnapayments/cron'), 0, 10) != Tools::getValue('token') || !Module::isInstalled('klarnapayments'))
	die('Bad token');

$klarnapayments = Module::getInstanceByName('klarnapayments');
set_time_limit(0);

if ($klarnapayments->active)
{
	$array = Db::getInstance()->executeS('SELECT `payment_status`, `id_reservation` FROM `'._DB_PREFIX_.'klarna_orders`');

	foreach ($array as $key => $value)
	{
		if ($value['payment_status'] == 'PENDING')
		{
			$klarna_check_status = new KlarnaOrderManagement();
			$klarna_check_status->checkStatus($value['id_reservation']);
		}
	}
}
