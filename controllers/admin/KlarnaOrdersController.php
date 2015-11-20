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

class KlarnaOrdersController extends ModuleAdminController
{
	public function __construct()
	{
		$this->table = 'klarna_orders';
		$this->className = 'KlarnaOrder';
		$this->lang = false;
		$this->identifier = 'id_order';
		$this->bootstrap = true;
		$this->colorOnBackground = false;
		$this->className = 'KlarnaOrders';
		$this->bootstrap = true;
		$this->meta_link = array('Handle your Klarna orders');
		$this->noLink = true;
		$this->list_no_link = true;
		$this->_select = '`id_order`';
		$this->_pagination = array(10, 50, 100, 300, 1000);
		$this->_default_pagination = 10;

		$this->fields_list = array(
			'id_order' => array('title' => $this->l('ID'), 'align' => 'center', 'class' => 'fixed-width-xs'),
			'customer_firstname' => array('title' => $this->l('Customer firstname'), 'filter_key' => 'customer_firstname'),
			'customer_lastname' => array('title' => $this->l('Customer Lastname')),
			'id_reservation' => array('title' => $this->l('Id reservation')),
			'payment_status' => array('title' => $this->l('Payment status')),
			'id_invoicenumber' => array('title' => $this->l('Id invoicenumber')),
			'customer_country' => array('title' => $this->l('Customer country')),
			'risk_status' => array('title' => $this->l('Risk status')),
		);

		parent::__construct();

		$resend_invoice = array(
		array(
			'id_option' => 1,
			'name' => 'E-mail'
		),
		array(
			'id_option' => 0,
			'name' => 'Post'
		),
		);

		$refund_qty = array(
			array(
			'id_option' => 1,
			'name' => '1'
		),
		array(
			'id_option' => 2,
			'name' => '2'
		),
		array(
			'id_option' => 3,
			'name' => '3'
		),
		array(
			'id_option' => 4,
			'name' => '4'
		),
		array(
			'id_option' => 5,
			'name' => '5'
		),
		array(
			'id_option' => 6,
			'name' => '6'
		),
		array(
			'id_option' => 7,
			'name' => '7'
		),
		array(
			'id_option' => 8,
			'name' => '8'
		),
		array(
			'id_option' => 9,
			'name' => '9'
		),
		array(
			'id_option' => 10,
			'name' => '10'
			),
		);

		$send_type = array(
		array(
			'id_option' => 0,
			'name' => 'E-mail'
		),
		array(
			'id_option' => 1,
			'name' => 'Post'
			),
		);

		$this->fields_options = array(
		'showinvoice' => array(
			'title' => $this->l('Show invoice'),
			'icon' => 'icon-user',
			'fields' => array(
			'KLARNA_SHOW_INVOICE_ID' => array(
				'title' => $this->l('Invoice number'),
				'desc' => $this->l('Fill in the invoice number to show the invoice'),
				'class' => 'fixed-width-lg',
				'type' => 'text'
				),
			),
			'submit' => array(
			'title' => $this->l('Show'),
			'class' => 'button pull-right',
			'name' => 'show_invoice_klarna',
			),
		),
		'activate' => array(
			'title' => $this->l('Activate invoice'),
			'description' => $this->l('This function will activate the invoice'),
			'icon' => 'icon-user',
			'fields' =>    array(
			'KLARNA_ACTIVE_ID' => array(
				'title' => $this->l('Activate klarna order'),
				'desc' => $this->l('Fill in the reservation id to activate order'),
				'validation' => 'isUnsignedId',
				'class' => 'fixed-width-lg',
				'type' => 'text',
				),
			'KLARNA_ACTIVE_TYPE' => array(
				'title' => $this->l('Type'),
				'desc' => $this->l('Select wheter to send invoice with email or post'),
				'type' => 'select',
				'list' => $send_type,
				'identifier' => 'name'
				),
			),
			'submit' => array(
			'title' => $this->l('Process'),
			'class' => 'button pull-right',
			'name' => 'activate_klarna',
			),
		),
		'checkstatus' => array(
			'title' => $this->l('Check status on invoice'),
			'description' => $this->l('This function will check status on a pending invoice'),
			'icon' => 'icon-user',
			'fields' =>    array(
			'KLARNA_CHECK_STATUS_ID' => array(
				'title' => $this->l('Reservation id'),
				'desc' => $this->l('Fill in reservation number to check status'),
				'class' => 'fixed-width-lg',
				'type' => 'text',
				),
			),
			'submit' => array(
			'title' => $this->l('Process'),
			'class' => 'button pull-right',
			'name' => 'checkstatus_klarna',
			),

		),
		'refundall' => array(
			'title' => $this->l('Refund full invoice'),
			'description' => $this->l('This function will credit the full invoice'),
			'icon' => 'icon-user',
			'fields' =>    array(
			'KLARNA_REFUND_ALL_ID' => array(
				'title' => $this->l('Invoice number'),
				'desc' => $this->l('Fill in invoice number to refund order'),
				'class' => 'fixed-width-lg',
				'type' => 'text',
				),
			),
			'submit' => array(
			'title' => $this->l('Process'),
			'class' => 'button pull-right',
			'name' => 'refundall_klarna',
			),
		),
		'refundpartial' => array(
			'title' => $this->l('Refund partial'),
			'icon' => 'icon-user',
			'fields' =>    array(
			'KLARNA_REFUND_PARTIAL_ID' => array(
				'title' => $this->l('Invoice number'),
				'desc' => $this->l('Fill in the invoice number for the refund'),
				'type' => 'text',
				'class' => 'fixed-width-lg',
				),
			'KLARNA_REFUND_PARTIAL_QTY' => array(
				'title' => $this->l('Quantity'),
				'desc' => $this->l('Fill in the quantity for the product to be refunded'),
				'type' => 'select',
				'list' => $refund_qty,
				'identifier' => 'id_option'
				),
			'KLARNA_REFUND_PARTIAL_PRODUCT' => array(
				'title' => $this->l('Product'),
				'type' => 'select',
				'desc' => $this->l('Select product to refund'),
				'list' => Product::getProducts($this->context->language->id, 0, 0, 'name', 'ASC'),
				'identifier' => 'reference'
				),
			),
			'submit' => array(
			'title' => $this->l('Process'),
			'class' => 'button pull-right',
			'name' => 'refundpartial_klarna',
			),

		),
		'refundgoodwill' => array(
			'title' => $this->l('Goodwill refund'),
			'icon' => 'icon-user',
			'fields' => array(
			'KLARNA_REFUND_GOODWILL_ID' => array(
				'title' => $this->l('Invoice number'),
				'desc' => $this->l('Fill in the invoice number'),
				'class' => 'fixed-width-lg',
				'type' => 'text',
				),
			'KLARNA_REFUND_GOODWILL_AMOUNT' => array(
				'title' => $this->l('Amount'),
				'desc' => $this->l('Fill in the amount to be discounted'),
				'type' => 'text',
				'class' => 'fixed-width-lg'
				),
			'KLARNA_REFUND_GOODWILL_TAX' => array(
				'title' => $this->l('Tax'),
				'desc' => $this->l('Fill in the tax for the discount'),
				'type' => 'select',
				'list' => Tax::getTaxes($this->context->language->id, true),
				'identifier' => 'rate'
				),
			'KLARNA_REFUND_GOODWILL_DESC' => array(
				'title' => $this->l('Description'),
				'desc' => $this->l('Fill in the description for the discount'),
				'type' => 'text',
				'class' => 'fixed-width-lg'
				),
			),
			'submit' => array(
			'title' => $this->l('Process'),
			'class' => 'button pull-right',
			'name' => 'refundgoodwill_klarna',
			),

		),
		'resendinvoice' => array(
			'title' => $this->l('Resend invoice'),
			'description' => $this->l('This function will resend the invoice'),
			'icon' => 'icon-user',
			'fields' =>    array(
			'KLARNA_RESEND_ID' => array(
				'title' =>  $this->l('Resend klarna invoice'),
				'desc' => $this->l('Fill in the id invoicenumber to resend invoice'),
				'type' => 'text',
				'class' => 'fixed-width-lg',
				),
			'KLARNA_RESEND_TYPE' => array(
				'title' =>  $this->l('Resend klarna invoice'),
				'desc' => $this->l('Choose resend type'),
				'type' => 'select',
				'identifier' => 'name',
				'list' => $resend_invoice
				),
			),
			'submit' => array(
			'title' => $this->l('Process'),
			'class' => 'button pull-right',
			'name' => 'resendinvoice_klarna',
			),
		),
		'cancelinvoice' => array(
			'title' => $this->l('Cancel invoice'),
			'description' => $this->l('This will cancel the existing reservation'),
			'icon' => 'icon-user',
			'fields' =>  array(
			'KLARNA_CANCEL_ID' => array(
				'title' => $this->l('Reservation number'),
				'desc' => $this->l('Fill in the reservation id to cancel the invoice'),
				'type' => 'text',
				'class' => 'fixed-width-lg',
				),
			),
			'submit' => array(
			'title' => $this->l('Process'),
			'class' => 'button pull-right',
			'name' => 'cancelinvoice_klarna',
			),
		),
		'updateinvoice' => array(
			'title' => $this->l('Update invoice'),
			'description' => $this->l('This will replace the existing reservation'),
			'icon' => 'icon-user',
			'fields' =>    array(
			'KLARNA_UPDATE_ID' => array(
				'title' => $this->l('Update reservation'),
				'desc' => $this->l('Fill in the reservation id to update order'),
				'type' => 'text',
				'class' => 'fixed-width-lg',
				),
			'KLARNA_UPDATE_PRODUCT' => array(
				'title' => $this->l('Choose product to add'),
				'desc' => $this->l('The product will be added to the invoice'),
				'type' => 'select',
				'cast' => 'intval',
				'list' => Product::getProducts($this->context->language->id, 0, 0, 'name', 'ASC'),
				'identifier' => 'id_product'
				),
			'KLARNA_UPDATE_ORDERID1' => array(
				'title' => $this->l('Order id 1:'),
				'desc' => $this->l('Set a new order id optional'),
				'type' => 'text',
				'class' => 'fixed-width-lg'
				),
			'KLARNA_UPDATE_ORDERID2' => array(
				'title' => $this->l('Order id 2:'),
				'desc' => $this->l('Set a new order id optional'),
				'type' => 'text',
				'class' => 'fixed-width-lg'
			),
			'KLARNA_UPDATE_QTY' => array(
				'title' => $this->l('Qty'),
				'desc' => $this->l('Quantity for the product to update'),
				'type' => 'select',
				'cast' => 'intval',
				'list' => $refund_qty,
				'identifier' => 'id_option'
					),
				),
				'submit' => array(
				'title' => $this->l('Process'),
				'class' => 'button pull-right',
				'name' => 'updateinvoice_klarna',
				),
			),
		);
		if (!$this->module->active)
		Tools::redirectAdmin($this->context->link->getAdminLink('AdminHome'));
	}
	public function renderOptions()
	{
		// Set toolbar options
		if ($this->fields_options && is_array($this->fields_options))
		{
		$helper = new HelperOptions($this);
		$this->setHelperDisplay($helper);
		$helper->toolbar_scroll = true;
		$helper->toolbar_btn = array(
		'save' => array(
		'href' => '#',
		'desc' => $this->l('Save')
		)
		);
		$helper->id = $this->id;
		$helper->tpl_vars = $this->tpl_option_vars;
		$options = $helper->generateOptions($this->fields_options);
		return $options;
		}
	}

	public function renderForm()
	{
		$this->fields_form = array(
			'legend' => array(
			'title' => $this->l('Taxes'),
			'icon' => 'icon-money'
		),
		'input' => array(
			array(
				'type' => 'text',
				'label' => $this->l('Name'),
				'name' => 'customer_firstname',
				'required' => true,
				'lang' => true,
				'hint' => $this->l('Tax name to display in carts and on invoices (e.g. "VAT").').' - '.$this->l('Invalid characters').' <>;=#{}'
				),
			),
			'submit' => array(
			'title' => $this->l('Save')
			)
		);
		return parent::renderForm();
	}

	public function renderList()
	{
		// Set toolbar options
		$this->display = null;
		$this->initToolbar();

		return parent::renderList();
	}

	private function postValidation($reservation = false, $reservation_id = null, $invoicenumber = false, $id_invoicenumber = null)
	{
		if ($reservation === true)
		{
			$reservations = KlarnaInvoiceFeeHandler::getAllReservationIds();

			foreach ($reservations as $value)
			{
			if ($reservation_id === $value['id_reservation'])
			return true;
			}
			return false;
		}

		if ($invoicenumber === true)
		{
			$invoicenumbers = KlarnaInvoiceFeeHandler::getAllInvoiceIds();

			foreach ($invoicenumbers as $value)
			{
			if ($id_invoicenumber === $value['id_invoicenumber'])
			return true;

			}
			return false;
		}
	}


	public function postProcess()
	{
		if (Tools::isSubmit($this->table.'Orderby') || Tools::isSubmit($this->table.'Orderway'))
			$this->filter = true;

		if (Tools::isSubmit('activate_klarna'))
		{

			$reservation_id = Tools::getValue('KLARNA_ACTIVE_ID');
			$send_type = Tools::getValue('KLARNA_ACTIVE_TYPE');
			if (!$this->postValidation(true, $reservation_id, false, null))
			{
				$this->displayWarning('Invalid input please check your reservation id');
				return;
			}
			$klarna_order = new KlarnaOrderManagement();

			if ($klarna_order->activatePayment($reservation_id, $send_type))
				$this->displayInformation('Successfully activated reservation id:'.$reservation_id);
			else
				$this->displayWarning('Activation failed: see log for more information');
		}
		if (Tools::isSubmit('checkstatus_klarna'))
		{
			$reservation_id = Tools::getValue('KLARNA_CHECK_STATUS_ID');
			if (!$this->postValidation(true, $reservation_id, false, null))
			{
				$this->displayWarning('Invalid input please check your reservation id');
				return;
			}
			$klarna_order = new KlarnaOrderManagement();

			if ($klarna_order->checkStatus($reservation_id))
				$this->displayInformation('Successfully checked status on invoice please see order page for more information');
			else
				$this->displayWarning('Check status failed: see log for more information');
		}

		if (Tools::isSubmit('refundall_klarna'))
		{

			$id_invoicenumber = Tools::getValue('KLARNA_REFUND_ALL_ID');
			if (!$this->postValidation(false, null, true, $id_invoicenumber))
			{
			$this->displayWarning('Invalid input please check your invoice id');
			return;
			}
			$klarna_order = new KlarnaOrderManagement();

			if ($klarna_order->refundAll($id_invoicenumber))
				$this->displayInformation('Successfully refunded invoice id:'.$id_invoicenumber);
			else
				$this->displayWarning('Refund failed: see log for more information');

		}

		if (Tools::isSubmit('refundpartial_klarna'))
		{
			$id_invoicenumber = Tools::getValue('KLARNA_REFUND_PARTIAL_ID');
			$quantity = (int)Tools::getValue('KLARNA_REFUND_PARTIAL_QTY');
			$product = Tools::getValue('KLARNA_REFUND_PARTIAL_PRODUCT');

			if (!$this->postValidation(false, null, true, $id_invoicenumber))
			{
				$this->displayWarning('Invalid input please check your invoice id');
				return;
			}

			$klarna_order = new KlarnaOrderManagement();

			if ($klarna_order->refundPart($id_invoicenumber, $quantity, $product))
				$this->displayInformation('Successfully refunded invoice id:'.$id_invoicenumber);

			else
				$this->displayWarning('Refund failed: see log for more information');
		}

		if (Tools::isSubmit('resendinvoice_klarna'))
		{
			$id_invoicenumber = Tools::getValue('KLARNA_RESEND_ID');
			$type = Tools::getValue('KLARNA_RESEND_TYPE');
			if (!$this->postValidation(false, null, true, $id_invoicenumber))
			{
				$this->displayWarning('Invalid input please check your invoice id');
				return;
			}

			$klarna_order = new KlarnaOrderManagement();

			if ($klarna_order->resendKlarnaInvoice($id_invoicenumber, $type))

				$this->displayInformation('Successfully resent invoice id:'.$id_invoicenumber);

			else

				$this->displayWarning('Failed resending invoice: see log for more information');

		}

		if (Tools::isSubmit('cancelinvoice_klarna'))
		{
			$reservation_id = Tools::getValue('KLARNA_CANCEL_ID');
			if (!$this->postValidation(true, $reservation_id, false, null))
			{
				$this->displayWarning('Invalid input please check your reservation id');
				return;
			}

			$klarna_order = new KlarnaOrderManagement();

			if ($klarna_order->cancelPayment($reservation_id))

				$this->displayInformation('Successfully canceled reservation id:'.$reservation_id);

			else
				$this->displayWarning('Failed canceling reservation: see log for more information');

		}

		if (Tools::isSubmit('show_invoice_klarna'))
		{
			$id_invoicenumber = Tools::getValue('KLARNA_SHOW_INVOICE_ID');
			if (!$this->postValidation(false, null, true, $id_invoicenumber))
			{
			$this->displayWarning('Invalid input please check your invoice id');
			return;
			}
			$klarna_order = new KlarnaOrderManagement();
			$invoice_uri = $klarna_order->getInvoiceURI($id_invoicenumber);

			Tools::redirect($invoice_uri);

		}

		if (Tools::isSubmit('updateinvoice_klarna'))
		{
			$id_reservation = Tools::getValue('KLARNA_UPDATE_ID');
			$product = (int)Tools::getValue('KLARNA_UPDATE_PRODUCT');
			$quantity = (int)Tools::getValue('KLARNA_UPDATE_QTY');
			$order_id1 = Tools::getValue('KLARNA_UPDATE_ORDERID1');
			$order_id2 = Tools::getValue('KLARNA_UPDATE_ORDERID2');

			if (!$this->postValidation(true, $reservation_id, false, null))
			{
			$this->displayWarning('Invalid input please check your invoice id');
			return;
			}

			$klarna_order = new KlarnaOrderManagement();

			if ($klarna_order->updateKlarnaInvoice($id_reservation, $product, $quantity, $order_id1, $order_id2))

			$this->displayInformation('Successfully canceled reservation id:'.$reservation_id);

			else

			$this->displayWarning('Failed canceling reservation: see log for more information');
		}

		if (Tools::isSubmit('refundgoodwill_klarna'))
		{
			$id_invoicenumber = Tools::getValue('KLARNA_REFUND_GOODWILL_ID');
			$tax = Tools::getValue('KLARNA_REFUND_GOODWILL_TAX');
			$amount = Tools::getValue('KLARNA_REFUND_GOODWILL_AMOUNT');
			$description = Tools::getValue('KLARNA_REFUND_GOODWILL_DESC');
			if (!$this->postValidation(false, null, true, $id_invoicenumber))
			{
			$this->displayWarning('Invalid input please check your invoice id');
			return;
			}
			
			$klarna_order = new KlarnaOrderManagement();

			if ($klarna_order->refundGoodwill($id_invoicenumber, $description, (float)$amount, (float)$tax))

			$this->displayInformation('Successfully refunded amount '.$amount.' on invoice id:'.$id_invoicenumber);

			else

			$this->displayWarning('Failed refunding amount on invoice: see log for more information');
		}
	}
}