<?php

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