<?php

class KlarnaOrderManagement
{
  public function __construct()
  {
      $klarna = new Klarna();
      $klarna->setConfig(new KlarnaConfig(dirname(__FILE__).'/../pclasses/SV'))
  }

  public function activatePayment($reservation_id, $id_order, $order_number, $reference_number)
	{

    if (!is_string($reservation_id) || !is_int($id_order) || !is_int($order_number) || !is_string($reference_number))
    return;

		$k->config(Configuration::get('klarna_eid'),
			Configuration::get('klarna_secret'),
			$this->getPClassesLocale()[0],
			$this->getPClassesLocale()[1],
			$this->getPClassesLocale()[2],
			$this->getKlarnaEnvironment(),
			'json',
			dirname(__FILE__).'/pclasses/pclasses.json');

			$k->setActivateInfo('orderid1', (string)$order_number);
			$k->setActivateInfo('orderid2', (string)$reference_number);
			$k->setComment('A text string stored in the invoice commentary area.');

		try {

			$invoice_method = $this->getInvoiceType();

			$result = $k->activate($reservation_id, null, $invoice_method);

			$risk = $result[0];
			$invoice_number = $result[1];

			$this->_addNewPrivateMessage($id_order, 'Activation successful with invoice number: '.$invoice_number.' and risk status: '.$risk);

			$this->updateDatabase($invoice_number, $id_order);

			$history = new OrderHistory();
			$history->id_order = (int)$id_order;
			$history->changeIdOrderState((int)Configuration::get('KLARNA_OS_ACTIVATED'), $history->id_order);
			$history->addWithemail();
			$history->save();

		} catch (Exception $e) {
			$this->_addNewPrivateMessage($id_order, 'Activation failed');
		}
	}

	private function cancelPayment($reservation_id, $id_order)
	{
    if (!is_string($reservation_id) || !is_int($id_order))
    return;

		$k = new Klarna();

		$k->config(Configuration::get('klarna_eid'),
			Configuration::get('klarna_secret'),
			$this->getPClassesLocale()[0],
			$this->getPClassesLocale()[1],
			$this->getPClassesLocale()[2],
			$this->getKlarnaEnvironment(),
			'json',
			dirname(__FILE__).'/pclasses/pclasses.json');

		try {
			$k->cancelReservation($reservation_id);
			$this->_addNewPrivateMessage($id_order, 'Cancellation successful');
		} catch(Exception $e) {
			$this->_addNewPrivateMessage($id_order, 'Cancellation failed');
		}

	}

	private function refundAll($invoicenumber, $id_order)
	{
		$k = new Klarna();

		$k->config(Configuration::get('klarna_eid'),
			Configuration::get('klarna_secret'),
			$this->getPClassesLocale()[0],
			$this->getPClassesLocale()[1],
			$this->getPClassesLocale()[2],
			$this->getKlarnaEnvironment(),
			'json',
			dirname(__FILE__).'pclasses/pclasses.json');

		try {
			$k->creditInvoice($invoicenumber);
			$this->_addNewPrivateMessage($id_order, 'Credit successful');

			$history = new OrderHistory();
			$history->id_order = (int)$id_order;
			$history->changeIdOrderState((int)Configuration::get('KLARNA_OS_REFUNDED'), $history->id_order);
			$history->addWithemail();
			$history->save();

		} catch (Exception $e) {
			$this->_addNewPrivateMessage($id_order, 'Credit failed');
		}

	}

	private function refundPart($invoicenumber, $id_order, $quantity, $article_number)
	{
		$k = new Klarna();

		$k->config(Configuration::get('klarna_eid'),
			Configuration::get('klarna_secret'),
			$this->getPClassesLocale()[0],
			$this->getPClassesLocale()[1],
			$this->getPClassesLocale()[2],
			$this->getKlarnaEnvironment(),
			'json',
			dirname(__FILE__).'pclasses/pclasses.json');
		$k->addArtNo($quantity, $article_number);

		try {
			$k->creditPart($invoicenumber);
			$this->_addNewPrivateMessage($id_order, 'Successfully credited item :'.$article_number.' and quantity: '.$quantity);
		} catch (Exception $e) {
			$this->_addNewPrivateMessage($id_order, 'Credit part failed');
		}

	}



	private function checkStatus($id_reservation, $id_order)
	{
		$k = new Klarna();
		$k->config(Configuration::get('klarna_eid'),
			Configuration::get('klarna_secret'),
			$this->getPClassesLocale()[0],
			$this->getPClassesLocale()[1],
			$this->getPClassesLocale()[2],
			$this->getKlarnaEnvironment(),
			'json',
			dirname(__FILE__).'pclasses/pclasses.json');

		try {
			$status = $k->checkOrderStatus($id_reservation);

			if ($status == KlarnaFlags::ACCEPTED)
			{

				$history = new OrderHistory();
				$history->id_order = (int)$id_order;
				$history->changeIdOrderState((int)Configuration::get('KLARNA_OS_AUTHORIZED'), $history->id_order);
				$history->addWithemail();
				$history->save();
				$this->_addNewPrivateMessage($id_order, 'Invoice is ok you may now activate it.');

			} elseif ($status == KlarnaFlags::DENIED)
			{

				$history = new OrderHistory();
				$history->id_order = (int)$id_order;
				$history->changeIdOrderState((int)Configuration::get('KLARNA_OS_DENIED'), $history->id_order);
				$history->addWithemail();
				$history->save();
				$this->_addNewPrivateMessage($id_order, 'Invoice is denied please cancel the order.');

			}
			else
			{
				$this->_addNewPrivateMessage($id_order, 'Invoice is still pending, try again later');

			}
		} catch (Exception $e) {
			Logger::addLog('Order status check failed with message: '.$e->getMessage().' and response code: '.$e->getCode());
		}

	}

	private function updateKlarnaInvoice($reservation_id, $id_product, $quantity, $id1, $id2)
	{
		$k = new Klarna();

		$k->config(Configuration::get('klarna_eid'),
			Configuration::get('klarna_secret'),
			$this->getPClassesLocale()[0],
			$this->getPClassesLocale()[1],
			$this->getPClassesLocale()[2],
			$this->getKlarnaEnvironment(),
			'json',
			dirname(__FILE__).'pclasses/pclasses.json');

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

	private function resendKlarnaInvoice($invoicenumber, $id_order)
	{
		$k = new Klarna();

		$k->config(Configuration::get('klarna_eid'),
			Configuration::get('klarna_secret'),
			$this->getPClassesLocale()[0],
			$this->getPClassesLocale()[1],
			$this->getPClassesLocale()[2],
			$this->getKlarnaEnvironment(),
			'json',
			dirname(__FILE__).'pclasses/pclasses.json');

		$type = $this->getInvoiceType();

		if ($type == KlarnaFlags::RSRV_SEND_BY_EMAIL)
		{

			try {
				$k->emailInvoice($invoicenumber);
				$this->_addNewPrivateMessage($id_order, 'Successfully resent invoice with invoice number: '.$invoicenumber);
			} catch (Exception $e) {
				$this->_addNewPrivateMessage($id_order, 'Failed resending invoice with invoice number: '.$invoicenumber);
				Logger::addLog('Failed resending invoice with invoice number: '.$invoicenumber);

			}
		} elseif ($type == KlarnaFlags::RSRV_SEND_BY_MAIL)
		{
			try {
				$k->sendInvoice($invoicenumber);
				$this->_addNewPrivateMessage($id_order, 'Successfully resent invoice with invoice number: '.$invoicenumber);
			} catch (Exception $e) {
				$this->_addNewPrivateMessage($id_order, 'Failed resending invoice with invoice number: '.$invoicenumber);
				Logger::addLog('Failed resending invoice with invoice number: '.$invoicenumber);

			}
		}

	}
}

?>
