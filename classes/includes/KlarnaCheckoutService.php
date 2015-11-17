<?php

class KlarnaCheckoutService extends KlarnaPrestaConfig
{
	public function newCheckout($country, $total, $lang, $currency)
	{
		$config = new KlarnaPrestaConfig();
		$config->setKlarnaConfig($country);

		try {
			$response = $config->klarna->checkoutService(
				$total,
				$currency,
				$lang
				);
			$data = $response->getData();
			return $data;
		} catch (KlarnaException $e) {
			Logger::addLog('Communication with Klarna Failed with message' . $e->getMessage(). '');
			return false;
		}

		
		if ($response->getStatus() >= 400) {
		    // server responded with error
		    Logger::addLog('Communication with Klarna Failed with message' . print_r($data, true) . '');
		    return false;
		} 
		
	}
}