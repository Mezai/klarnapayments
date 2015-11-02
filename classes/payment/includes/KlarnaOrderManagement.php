<?php

require_once '/../KlarnaPrestashopCore.php';

class KlarnaOrderManagement
{
  public function __construct($country, array $klarna_settings)
  {
		$this->country = $country;
		$this->klarna_settings = $klarna_settings;
		$this->environment = (Configuration::get('KLARNA_ENVIRONMENT') == 'live') ? Klarna::LIVE : KLARNA::BETA;
		$this->invoice_type = ((int)Configuration::get('klarna_invoice_method') == 1) ? KlarnaFlags::RSRV_SEND_BY_EMAIL : KlarnaFlags::RSRV_SEND_BY_MAIL;
  }

  public function activatePayment($reservation_id, $id_order, $order_number, $reference_number)
	{

    if (!is_string($reservation_id) || !is_int($id_order) || !is_int($order_number) || !is_string($reference_number))
		return;
			
			$k = KlarnaConfigHandler::setConfigurationByLocale($this->country, $this->environment, $this->klarna_settings);
		
			$k->setActivateInfo('orderid1', (string)$order_number);
			$k->setActivateInfo('orderid2', (string)$reference_number);
			$k->setComment('A text string stored in the invoice commentary area.');

		try {

			$invoice_method = $this->invoice_type;

			$result = $k->activate($reservation_id, null, $invoice_method);

			$risk = $result[0];
			$invoice_number = $result[1];
			$this->changeKlarnaOrderState($id_order, 'KLARNA_OS_ACTIVATED');
			$this->addNewKlarnaOrderMessage($id_order, 'Activation successful with invoice number: '.$invoice_number.' and risk status: '.$risk);

		} catch (Exception $e) {
			$this->addNewKlarnaOrderMessage($id_order, 'Activation failed');
		}
	}

	public function cancelPayment($reservation_id, $id_order)
	{
		if (!is_string($reservation_id) || !is_int($id_order))
			return;

		$k = KlarnaConfigHandler::setConfigurationByLocale($this->country, $this->environment, $this->klarna_settings);

		try {
			$k->cancelReservation($reservation_id);
			$this->addNewKlarnaOrderMessage($id_order, 'Cancellation successful');

			
		} catch(Exception $e) {
			$this->addNewKlarnaOrderMessage($id_order, 'Cancellation failed');


		}

	}

	public function refundAll($invoicenumber, $id_order)
	{
		
			
			$k = KlarnaConfigHandler::setConfigurationByLocale($this->country, $this->environment, $this->klarna_settings);

		try {
			$k->creditInvoice($invoicenumber);
			$this->changeKlarnaOrderState($id_order, 'KLARNA_OS_REFUNDED');
			$this->addNewKlarnaOrderMessage($id_order, 'Credit successful');

		} catch (Exception $e) {
			$this->addNewKlarnaOrderMessage($id_order, 'Credit failed');
		}

	}

	public function refundPart($invoicenumber, $id_order, $quantity, $article_number)
	{
	
		$k = KlarnaConfigHandler::setConfigurationByLocale($this->country, $this->environment, $this->klarna_settings);
		
		$k->addArtNo($quantity, $article_number);

		try {
			$k->creditPart($invoicenumber);
				$this->addNewKlarnaOrderMessage($id_order, 'Successfully credited item :'.$article_number.' and quantity: '.$quantity);
			} catch (Exception $e) {
				$this->addNewKlarnaOrderMessage($id_order, 'Credit part failed');
			}

	}



	public function checkStatus($id_reservation, $id_order)
	{
		if (!is_string($reservation_id) || !is_int($id_order))
			return;
		
		$k = KlarnaConfigHandler::setConfigurationByLocale($this->country, $this->environment, $this->klarna_settings);

		try {
			$status = $k->checkOrderStatus($id_reservation);

			if ($status == KlarnaFlags::ACCEPTED)
			{
				$this->changeKlarnaOrderState($id_order, 'KLARNA_OS_AUTHORIZED');
				$this->addNewKlarnaOrderMessage($id_order, 'Invoice is ok you may now activate it.');

			} elseif ($status == KlarnaFlags::DENIED)
			{

				$this->changeKlarnaOrderState($id_order, 'KLARNA_OS_DENIED');
				$this->addNewKlarnaOrderMessage($id_order, 'Invoice is denied please cancel the order.');

			}
			else
			{
				$this->addNewKlarnaOrderMessage($id_order, 'Invoice is still pending, try again later');
			}
		} catch (Exception $e) {
			Logger::addLog('Order status check failed with message: '.$e->getMessage().' and response code: '.$e->getCode());
		}

	}

	public function updateKlarnaInvoice($reservation_id, $id_product, $quantity, $id1, $id2)
	{
		$k = KlarnaConfigHandler::setConfigurationByLocale($this->country, $this->environment, $this->klarna_settings);
		
		$product = new Product($id_product);

		foreach ($product as $addproduct)
		{
			$k->addArticle($quantity,
			$addproduct['id_product'],
			$addproduct['name'],
			$addproduct['price_wt'],
			$addproduct['rate'],
			0,
			KlarnaFlags::INC_VAT);
		}

		$k->setEstoreInfo($id1, $id2, '');

		try {

			$k->update($reservation_id);

		} catch (Exception $e) {
			Logger::addLog('Failed updating invoice with reservation id: '.$reservation_id);
		}

	}

	public function resendKlarnaInvoice($invoicenumber, $id_order)
	{
		$k = KlarnaConfigHandler::setConfigurationByLocale($this->country, $this->environment, $this->klarna_settings);

		$type = $this->invoice_type;

		if ($type == KlarnaFlags::RSRV_SEND_BY_EMAIL)
		{

			try {
				$k->emailInvoice($invoicenumber);
				$this->addNewKlarnaOrderMessage($id_order, 'Successfully resent invoice with invoice number: '.$invoicenumber);
			} catch (Exception $e) {
				$this->addNewKlarnaOrderMessage($id_order, 'Failed resending invoice with invoice number: '.$invoicenumber);
				Logger::addLog('Failed resending invoice with invoice number: '.$invoicenumber);

			}
		} elseif ($type == KlarnaFlags::RSRV_SEND_BY_MAIL)
		{
			try {
				
				$k->sendInvoice($invoicenumber);
				$this->addNewKlarnaOrderMessage($id_order, 'Successfully resent invoice with invoice number: '.$invoicenumber);
			} catch (Exception $e) {

				$this->addNewKlarnaOrderMessage($id_order, 'Failed resending invoice with invoice number: '.$invoicenumber);
			Logger::addLog('Failed resending invoice with invoice number: '.$invoicenumber);

			}
		}

	}

	public function getInvoiceURI($id_order)
	{
		$invoicenumber = $this->getInvoiceNum($id_order);

		if (Configuration::get('KLARNA_ENVIRONMENT') == 'beta')
			return 'https://online.testdrive.klarna.com/invoices/'.$invoicenumber.'.pdf';
		elseif (Configuration::get('KLARNA_ENVIRONMENT') == 'live')
			return 'https://online.klarna.com/invoices/'.$invoicenumber.'.pdf';

	}

	
	private function addNewKlarnaOrderMessage($id_order, $message)
	{
				$msg = new Message();

				$msg->message = $message;

				$msg->id_order = (int)$id_order;

				$msg->private = 1;

				$msg->add();
	}
	
	private function changeKlarnaOrderState($id_order, $klarna_state)
	{
		$history = new OrderHistory();
		$history->id_order = (int)$id_order;
		$history->changeIdOrderState((int)Configuration::get($klarna_state), $history->id_order);
		$history->addWithemail();
		$history->save();
	}
}

?>
