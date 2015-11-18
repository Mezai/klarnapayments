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

class KlarnaOrder extends ObjectModel
{
	public $id_order;
	public $id_customer;
	public $customer_firstname;
	public $customer_lastname;

	public static $definition = array(
		'table' => 'klarna_orders',
		'primary' => 'id_order',
		'multilang' => false,
		'fields' => array(
			'id_order' => array('type' => self::TYPE_INT, 'required' => true, 'size' => 10),
			'id_customer' => array('type' => self::TYPE_INT, 'required' => false, 'size' => 10),
			'customer_firstname' => array('type' => self::TYPE_STRING, 'required' => false, 'size' => 32),
			'customer_lastname' => array('type' => self::TYPE_STRING, 'required' => false, 'size' => 32),
			'id_reservation' => array('type' => self::TYPE_STRING, 'required' => false, 'size' => 32),
			'id_invoicenumber' => array('type' => self::TYPE_STRING, 'required' => false, 'size' => 32),

			),
		);
}