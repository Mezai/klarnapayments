<?php

class KlarnaInvoiceFee
{
	private function getReservationNum($id_order)
	{
		$id_reservation = Db::getInstance()->getRow('SELECT `id_reservation` FROM `'._DB_PREFIX_.'klarna_orders` WHERE `id_order` = '.(int)$id_order);

		return $id_reservation['id_reservation'];
	}

	private function getInvoiceNum($id_order)
	{
		$id_invoicenumber = Db::getInstance()->getRow('SELECT `id_invoicenumber` FROM `'._DB_PREFIX_.'klarna_orders` WHERE `id_order` = '.(int)$id_order);

		return $id_invoicenumber['id_invoicenumber'];
	}

	public function getByReference($invoiceref)
	{
		$result = Db::getInstance()->getRow('SELECT id_product FROM `'._DB_PREFIX_.'product` WHERE reference='.$invoiceref);
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

	public function getProductId($invoicereference)
	{
	$result = Db::getInstance()->getRow('SELECT id_product FROM `'._DB_PREFIX_.'product` WHERE reference='.$invoicereference);
		if (isset($result['id_product']) && (int)$result['id_product'] > 0)

			return (int)$result['id_product'];

		else

			return null;
	}

	public function getInvoiceFeePrice()
	{
		if (Configuration::get('KLARNA_INVOICE_FEE_REF') && (int)Configuration::get('KLARNA_INVOICE_FEE') == 1)
		{

		$inv_id = $this->getProductId(Configuration::get('KLARNA_INVOICE_FEE_REF'));

		$inv_product = new Product();

		return $inv_product::getPriceStatic($inv_id);

	}elseif (!Configuration::get('KLARNA_INVOICE_FEE_REF') || (int)Configuration::get('KLARNA_INVOICE_FEE') == 0)
		{

			return 0;

		}
	}

	public function getInvoiceType()
	{
		if ((int)Configuration::get('KLARNA_INVOICE_METHOD') == 1)
			return KlarnaFlags::RSRV_SEND_BY_EMAIL;
		elseif ((int)Configuration::get('KLARNA_INVOICE_METHOD') == 0)
			return KlarnaFlags::RSRV_SEND_BY_MAIL;
	}

}