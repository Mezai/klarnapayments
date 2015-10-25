<?php

if (!defined('_PS_VERSION_'))
	exit;


include_once(dirname(__FILE__).'/../../../config/config.inc.php');
include_once(dirname(__FILE__).'/../../../init.php');

require_once(dirname(__FILE__).'/Klarna.php');

// Dependencies from http://phpxmlrpc.sourceforge.net/
require_once(dirname(__FILE__).'/transport/xmlrpc-3.0.0.beta/lib/xmlrpc.inc');
require_once(dirname(__FILE__).'/transport/xmlrpc-3.0.0.beta/lib/xmlrpc_wrappers.inc');
/**
* Class klarna get address
* @param $pno 
* @author Johan Tedenmark
* make request for address information
*/
class KlarnaGetAdresses 
{
	
function getKlarnaAddresses($pno)
{

				$k = new Klarna();

				$k->config(
				    1736,                    // Merchant ID
				    'CqF3rmhgK166ge9',       // Shared secret
				    KlarnaCountry::SE,    // Purchase country
				    KlarnaLanguage::SV,   // Purchase language
				    KlarnaCurrency::SEK,  // Purchase currency
				    Klarna::BETA,         // Server
				    'json',               // PClass storage
				    './pclasses.json'     // PClass storage URI path
				);

				$k->setCountry('se'); // Sweden only
				try {
				    $addrs = $k->getAddresses($pno);

				    
				} catch(Exception $e) {
				    echo "{$e->getMessage()} (#{$e->getCode()})\n";
				}
	}
}

