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

class KlarnaOrderManagement extends KlarnaPrestaConfig
{

	public function activatePayment($reservation_id, $send_type)
	{

	if (!is_string($reservation_id) || !is_string($send_type))
		return;

			$order_number = KlarnaInvoiceFeeHandler::getOrderNumberByReservation($reservation_id);
			$country = KlarnaInvoiceFeeHandler::getInvoiceCountry($order_number);

			$reference_number = Order::getUniqReferenceOf($order_number);

			$config = new KlarnaPrestaConfig();
			$config->setKlarnaConfig($country, true);

			$config->klarna->setActivateInfo('orderid1', (string)$order_number);
			$config->klarna->setActivateInfo('orderid2', (string)$reference_number);
			$config->klarna->setComment('A text string stored in the invoice commentary area.');


		try {

			$invoice_type = ($send_type === 'E-mail') ? KlarnaFlags::RSRV_SEND_BY_EMAIL : KlarnaFlags::RSRV_SEND_BY_MAIL;

			$result = $config->klarna->activate(KlarnaPrestaEncoding::encode($reservation_id), null, $invoice_type);

			$risk = $result[0];
			$invoice_number = $result[1];
			
			KlarnaInvoiceFeeHandler::updateInvoiceNumber($invoice_number, $risk, $order_number);
			return true;
		} catch (Exception $e) {
			Logger::addLog('Klarna module: failed activating resevation id '.$reservation_id.' with code: '.$e->getCode().' and message: '.$e->getMessage());
			return false;

		}
	}

	
	public function cancelPayment($reservation_id)
	{
		if (!is_string($reservation_id))
			return;

		$order_number = KlarnaInvoiceFeeHandler::getOrderNumberByReservation($reservation_id);
		$country = KlarnaInvoiceFeeHandler::getInvoiceCountry($order_number);

		$config = new KlarnaPrestaConfig();
		$config->setKlarnaConfig($country, true);

		try {
			$config->klarna->cancelReservation(KlarnaPrestaEncoding::encode($reservation_id));
			return true;
		} catch(Exception $e) {
			Logger::addLog('Klarna module: failed canceling reservation id '.$reservation_id.' with code: '.$e->getCode().' and message: '.$e->getMessage());
			return false;

		}

	}

	public function refundAll($invoicenumber)
	{
		
		if (!is_string($invoicenumber))
			return;

		$order_number = KlarnaInvoiceFeeHandler::getOrderNumberByInvoice($invoicenumber);	 	
		$country = KlarnaInvoiceFeeHandler::getInvoiceCountry($order_number);

		$config = new KlarnaPrestaConfig();
		$config->setKlarnaConfig($country, true);

		try {

			$config->klarna->creditInvoice(KlarnaPrestaEncoding::encode($invoicenumber));
			return true;

		} catch (Exception $e) {
			Logger::addLog('Klarna module: failed refunding invoicenumber '.$invoicenumber.' with code: '.$e->getCode().' and message: '.$e->getMessage());
			return false;
		}

	}

	public function refundPart($invoicenumber, $quantity, $article_number)	
	{	
		if (!is_string($invoicenumber) || !is_int($quantity) || !is_string($article_number))
			return;

		$order_number = KlarnaInvoiceFeeHandler::getOrderNumberByInvoice($invoicenumber);	 	
		$country = KlarnaInvoiceFeeHandler::getInvoiceCountry($order_number);

		$config = new KlarnaPrestaConfig();
		$config->setKlarnaConfig($country, true);

		$config->klarna->addArtNo($quantity, $article_number);

		try {

			$config->klarna->creditPart(KlarnaPrestaEncoding::encode($invoicenumber));
			return true;
			} catch (Exception $e) {
			Logger::addLog('Klarna module: failed refunding invoicenumber '.$invoicenumber.' with code: '.$e->getCode().' and message: '.$e->getMessage());
			return false;	

			}

	}



	public function checkStatus($id_reservation)
	{

		if (!is_string($id_reservation))
		{
			return;
		}	

		$order_number = KlarnaInvoiceFeeHandler::getOrderNumberByReservation($id_reservation);	 	
		$country = KlarnaInvoiceFeeHandler::getInvoiceCountry($order_number);
		$config = new KlarnaPrestaConfig();
		$config->setKlarnaConfig($country, true);

		try {
			$status = $config->klarna->checkOrderStatus($id_reservation);

			if ($status == KlarnaFlags::ACCEPTED)
			{
				$history = new OrderHistory();
				$history->id_order = (int)$order_number;
				$history->changeIdOrderState((int)Configuration::get('KLARNA_OS_AUTHORIZED'), $history->id_order);
				$history->addWithemail();
				$history->save();

				$msg = new Message();
				$msg->message = 'Invoice is ok you may now activate it';
				$msg->id_order = (int)$order_number;
				$msg->private = 1;
				$msg->add();

			} elseif ($status == KlarnaFlags::DENIED) {
				$history = new OrderHistory();
				$history->id_order = (int)$order_number;
				$history->changeIdOrderState((int)Configuration::get('KLARNA_OS_DENIED'), $history->id_order);
				$history->addWithemail();
				$history->save();

				$msg = new Message();
				$msg->message = 'Invoice is denied please abort it';
				$msg->id_order = (int)$order_number;
				$msg->private = 1;
				$msg->add();

			} else
			{
				$msg = new Message();
				$msg->message = 'Invoice is still pending try again later';
				$msg->id_order = (int)$order_number;
				$msg->private = 1;
				$msg->add();
			}

			return true;
		} catch (Exception $e) {
			Logger::addLog('Order status check failed with message: '.$e->getMessage().' and response code: '.$e->getCode());
			return false;
		}

	}

	public function updateKlarnaInvoice($reservation_id, $id_product, $quantity, $id1, $id2)
	{
		if (!is_string($reservation_id) || !is_int($id_product) || !is_int($quantity) || !is_string($id1) || !is_string($id2))
			return;

		$order_number = KlarnaInvoiceFeeHandler::getOrderNumberByReservation($reservation_id);	 	
		$country = KlarnaInvoiceFeeHandler::getInvoiceCountry($order_number);

		$config = new KlarnaPrestaConfig();
		$config->setKlarnaConfig($country, true);

		$product = new Product($id_product);

		foreach ($product as $addproduct)
		{
			$attributes = "";

			if (isset($addproduct['attributes'])) {
				$attributes = $addproduct['attributes'];
			}

			if (empty($addproduct['rate'])) {
				$price_wt = (float)$addproduct['price_wt'];
				$price = (float)$addproduct['price'];
				$rate = round((($price_wt / $price) - 1.0) * 100);
			} else {
				$rate = $addproduct['rate'];

			}

			$config->klarna->addArticle(
					$quantity,
					KlarnaPrestaEncoding::encode($addproduct['reference']),
					KlarnaPrestaEncoding::encode($addproduct['name'] . $attributes),
					$addproduct['price_wt'],
					$rate,
					0,
				   KlarnaFlags::INC_VAT
					
				);
		
		}

		$config->klarna->setEstoreInfo($id1, $id2, '');

		try {

			$config->klarna->update($reservation_id);
			return true;
		} catch (Exception $e) {
			Logger::addLog('Failed updating invoice with reservation with code: '.$e->getCode().' and message: '.$e->getMessage());
			return false;
		}	

	}

	public function resendKlarnaInvoice($invoicenumber, $type)
	{
		if (!is_string($invoicenumber) || !is_string($type))
			return;

		$order_number = KlarnaInvoiceFeeHandler::getOrderNumberByInvoice($invoicenumber);	 	
		$country = KlarnaInvoiceFeeHandler::getInvoiceCountry($order_number);

		$config = new KlarnaPrestaConfig();
		$config->setKlarnaConfig($country, true);

		if ($type === 'E-mail')
		{

			try {
				$config->klarna->emailInvoice(KlarnaPrestaEncoding::encode($invoicenumber));
				return true;
			} catch (Exception $e) {
				Logger::addLog('Failed resending invoice id '.$invoicenumber.' with code: '.$e->getCode().' and message: '.$e->getMessage());
				return false;
			}
		} elseif ($type === 'Post')
		{
			try {
				
				$config->klarna->sendInvoice(KlarnaPrestaEncoding::encode($invoicenumber));
				return true;
			} catch (Exception $e) {
			Logger::addLog('Failed resending invoice id: '.$invoicenumber.' with code: '.$e->getCode().' and message: '.$e->getMessage());
			return false;
			}
		}

	}

	public function getInvoiceURI($invoicenumber)
	{

		if (Configuration::get('KLARNA_ENVIRONMENT') == 'beta')
			return 'https://online.testdrive.klarna.com/invoices/'.$invoicenumber.'.pdf';
		elseif (Configuration::get('KLARNA_ENVIRONMENT') == 'live')
			return 'https://online.klarna.com/invoices/'.$invoicenumber.'.pdf';

	}
}

?>
