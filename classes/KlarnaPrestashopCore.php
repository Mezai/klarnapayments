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

if (!defined('KLARNA_DIRECTORY'))
define('KLARNA_DIRECTORY', dirname(__FILE__).'/../');


require_once KLARNA_DIRECTORY.'/libs/payment/transport/xmlrpc-3.0.0.beta/lib/xmlrpc.inc';
require_once KLARNA_DIRECTORY.'/libs/payment/transport/xmlrpc-3.0.0.beta/lib/xmlrpc_wrappers.inc';

require_once KLARNA_DIRECTORY.'/libs/payment/Klarna.php';

require_once KLARNA_DIRECTORY.'/libs/checkout/Checkout.php';

require_once KLARNA_DIRECTORY.'/classes/includes/KlarnaPrestaConfig.php';
require_once KLARNA_DIRECTORY.'/classes/includes/KlarnaPrestaApi.php';
require_once KLARNA_DIRECTORY.'/classes/includes/KlarnaPClassesHandler.php';
require_once KLARNA_DIRECTORY.'/classes/includes/KlarnaCheckoutService.php';
require_once KLARNA_DIRECTORY.'/classes/includes/KlarnaPrestaEncoding.php';
require_once KLARNA_DIRECTORY.'/classes/includes/KlarnaOrderManagement.php';
require_once KLARNA_DIRECTORY.'/classes/includes/KlarnaInvoiceFeeHandler.php';
require_once KLARNA_DIRECTORY.'/classes/includes/KlarnaGoodsList.php';
require_once KLARNA_DIRECTORY.'/classes/includes/KlarnaConfigHandler.php';
require_once KLARNA_DIRECTORY.'/classes/includes/KlarnaCountryLogic.php';
require_once KLARNA_DIRECTORY.'/classes/includes/KlarnaCalculationHandler.php';
require_once KLARNA_DIRECTORY.'/classes/includes/KlarnaLocalization.php';
require_once KLARNA_DIRECTORY.'/classes/includes/KlarnaValidation.php';
require_once KLARNA_DIRECTORY.'/classes/includes/KlarnaAdressPresta.php';
require_once KLARNA_DIRECTORY.'/classes/includes/KlarnaCheckoutPresta.php';