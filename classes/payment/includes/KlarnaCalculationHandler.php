<?php

class KlarnaCalculate
{

	public function __construct($country, array $klarna_settings)
	{
		$this->country = $country;
		$this->klarna_settings = $klarna_settings;
		$this->environment = (Configuration::get('KLARNA_ENVIRONMENT') == 'live') ? Klarna::LIVE : KLARNA::BETA;
	}

	public function klarnaCalculateMonthlyCost($amount, $type, $id)
	{


		$k = KlarnaConfigHandler::setConfigurationByLocale($this->country, $this->environment, $this->klarna_settings);
		
		$pclass = $k->getPClass($id);

		if ($pclass)
			$monthly = KlarnaCalc::calc_monthly_cost($amount, $pclass, $type);
			return $monthly;

	}

	public function klarnaCalculateTotalCredit($amount, $type, $id)
	{
	

	$k = KlarnaConfigHandler::setConfigurationByLocale($this->country, $this->environment, $this->klarna_settings);

	$pclass = $k->getPClass($id);

	if ($pclass)
		$total = KlarnaCalc::total_credit_purchase_cost($amount, $pclass, $type);
		return $total;
	}


	public function calculateKlarnaApr($amount, $type, $id)
	{
		
		$k = KlarnaConfigHandler::setConfigurationByLocale($this->country, $this->environment, $this->klarna_settings);

		$pclass = $k->getPClass($id);

		if ($pclass)
			$apr = KlarnaCalc::calc_apr($amount, $pclass, $type);
			return $apr;
	}
}