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

require_once dirname(__FILE__).'../../../../../init.php';
require_once dirname(__FILE__).'../../../../../config/config.inc.php';

class KlarnaInvoiceFeeHandler
{
	public static function getReservationNumberByOrderId($id_order)
	{
		$id_reservation = Db::getInstance()->getRow('SELECT `id_reservation` FROM `'._DB_PREFIX_.'klarna_orders` WHERE `id_order` = '.(int)$id_order);

		if ($id_reservation != false)
			return (int)$id_reservation['id_order'];
		return 0;
	}

	public static function getInvoiceNumberByOrderId($id_order)
	{
		$id_invoicenumber = Db::getInstance()->getRow('SELECT `id_invoicenumber` FROM `'._DB_PREFIX_.'klarna_orders` WHERE `id_order` = '.(int)$id_order);

		if ($id_invoicenumber != false)
			return (int)$id_invoicenumber['id_order'];
		return 0;
	}

	public static function getByReference($invoiceref)
	{
		$result = Db::getInstance()->getRow('SELECT `id_product` FROM `'._DB_PREFIX_.'product` WHERE `reference` = '.$invoiceref);

		if (isset($result['id_product']) && (int)$result['id_product'] > 0)
		{
			$feeproduct = new Product((int)$result['id_product'], true);

			return $feeproduct;

		}
		else
		{

		return null;
		}
	}

	public static function getProductId($invoicereference)
	{
		$result = Db::getInstance()->getRow('
			SELECT `id_product`
			FROM `'._DB_PREFIX_.'product`
			WHERE `reference` = \''.pSQL($invoicereference).'\'');

		if (isset($result['id_product']) && (int)$result['id_product'] > 0)
		{

			return (int)$result['id_product'];

		}
		else
		{

			return null;
		}
	}

	public static function getInvoiceFeePrice($reference)
	{
		$invoice_product_id = self::getProductId($reference);

		$price = Product::getPriceStatic((int)$invoice_product_id);

		return $price;
	}


	public static function updateInvoiceNumber($invoice_number, $risk_status, $id_order)
	{
		if (!Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'klarna_orders` SET `id_invoicenumber` = '.$invoice_number.' WHERE `id_order` = '.(int)$id_order)
			|| !Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'klarna_orders` SET `risk_status` = '.$risk_status.' WHERE `id_order` = '.(int)$id_order))
		die(Tools::displayError('Error when updating Klarna database'));

	}

	public static function getInvoiceCountry($id_order)
	{
		if (!(int)$id_order)
			return false;

		$customer_country = Db::getInstance()->getRow('SELECT `customer_country` FROM `'._DB_PREFIX_.'klarna_orders` WHERE `id_order` = '.(int)$id_order);

		if ($customer_country != false)
			return (String)$customer_country['customer_country'];
		return 0;
	}

	public static function getOrderNumberByReservation($id_reservation)
	{
		$reservation_id = Db::getInstance()->getRow('SELECT `id_order` FROM `'._DB_PREFIX_.'klarna_orders` WHERE `id_reservation` = '.(String)$id_reservation);

		if ($reservation_id != false)
			return (int)$reservation_id['id_order'];
		return 0;

	}

	public static function getOrderNumberByInvoice($id_invoicenumber)
	{
		$invoicenumber_id = Db::getInstance()->getRow('SELECT `id_order` FROM `'._DB_PREFIX_.'klarna_orders` WHERE `id_invoicenumber` = '.(String)$id_invoicenumber);

		if ($invoicenumber_id != false)
			return (int)$invoicenumber_id['id_order'];
		return 0;

	}

	public static function getAllReservationIds()
	{
		$reservation_ids = Db::getInstance()->executeS('SELECT `id_reservation` FROM `'._DB_PREFIX_.'klarna_orders` GROUP BY `id_reservation`');

		if (is_array($reservation_ids))

			return $reservation_ids;

		else

			return 0;

	}

	public static function getAllInvoiceIds()
	{
		$invoicenumber_ids = Db::getInstance()->executeS('SELECT `id_invoicenumber` FROM `'._DB_PREFIX_.'klarna_orders` GROUP BY `id_invoicenumber`');

		if (is_array($invoicenumber_ids))

			return $invoicenumber_ids;

		else

			return 0;

	}

}