<?php

require_once '/../KlarnaPrestashopCore.php';

class KlarnaPclassesManager
{


	public function __construct($country, $merchant_settings) {
		$this->country = $country;
		$this->merchant_settings = $merchant_settings;
	}
	
	public function getKlarnaPClasses($type)
	{
		if (!KlarnaConfigHandler::checkConfigurationByLocale($this->country, $this->merchant_settings)) {
			return;
		} else {

		$k = KlarnaConfigHandler::setConfigurationByLocale($this->country, $this->merchant_settings);
		}
		
		if ($type == 'ACCOUNT')
				$klarna_pclass_type = KlarnaPClass::ACCOUNT;
			elseif ($type == 'CAMPAIGN')
				$klarna_pclass_type = KlarnaPClass::CAMPAIGN;
			elseif ($type == 'SPECIAL')
				$klarna_pclass_type = KlarnaPClass::SPECIAL;
			
			$pclasses = $k->getPClasses($klarna_pclass_type);
			
		return $pclasses;
	}
	
	
	public function updatePClasses()
    {
        $countries = KlarnaConfigHandler::returnActiveCountries($this->merchant_settings);

        $this->klarna->requireApi();
        $this->klarna->clearPClasses();

        foreach ($countries as $country) {
            try {
                $this->requireApi($country);
            } catch(KlarnaException $e) {
                $this->_postErrors[] = "$country not fully configured";
                continue;
            }
            try {
                $this->klarna->fetchPClasses();
            } catch(Exception $e) {
                $this->_postErrors[] = "Failed to get pclasses for $country: " .
                    strval($e);
            }
        }
    }
	
	public function fetchPClass($country)
	{
		switch ($country) {
			case 'SE':
				return $this->updatePClasses('SE', 'SV', 'SEK');
			case 'NO':
				return $this->updatePClasses('NO', 'NB', 'NOK');
			case 'DK':
				return $this->updatePClasses('DK','DA', 'DKK');
			case 'FI':
				return $this->updatePClasses('FI','FI','EUR');
			case 'DE':
				return $this->updatePClasses('DE','DE','EUR');
			case 'NL':
				return $this->updatePClasses('NL','NL','EUR');
							
		}	

	}


	public function deletePClasses()
	{
		
		$k = KlarnaConfigHandler::setConfigurationByLocale($this->country, $this->merchant_settings);
		

		try {

		$k->clearPClasses();


		} catch(Exception $e) {
		Logger::addLog('Klarna module: PClass call failed with message: '.$e->getMessage().' and response code: '.$e->getCode());
		return 'PClass call failed : see log for error message';
		}
	}
	
}