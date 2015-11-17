<?php


class KlarnaPrestaPclasses extends KlarnaPrestaConfig
{

	public function updatePClasses($country)
	{

		$config = new KlarnaPrestaConfig();
		$config->setKlarnaConfig($country);
		$config->klarna->clearPClasses();


        try {
             $config->klarna->fetchPClasses($country);
        } catch(Exception $e) {
        	Logger::addLog('Klarna module: failed fetching pclasses for country '.$country);
        	return false;
              
       }

	}

	public function deletePClasses($country)
	{
		$config = new KlarnaPrestaConfig();
		$config->setKlarnaConfig($country);
		

		try {
			$config->klarna->clearPClasses();
			return true;
		} catch (Exception $e) {
			Logger::addLog('Klarna module: failed deleting pclasses for country '.$country);
			return false;
		}
	}

	public function getKlarnaPClasses($country)
	{
		$config = new KlarnaPrestaConfig();
		$config->setKlarnaConfig($country);

		try
		{
			$pclasses = $config->klarna->getPClasses();
			
		} catch (Exception $e) {
			Logger::addLog('Failed retreiving pclasses for country '.$country);
			return false;
		}
		return $pclasses;
	}
}