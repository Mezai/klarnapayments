<?php

class KlarnaPrestaConfig
{
	function __construct()
	{
		$this->klarna = null;
        $this->country = null;
	}
	

	public function setKlarnaConfig($country = null, $locale = false)
	{
		

		 if (($this->klarna === null) || (($country != null) && ($this->country != $country))) {
            if ($country == null || KlarnaCountry::fromCode($country) === null) {
                $eid = 1;
                $secret = 'invalid';
            } else {
                $eid = Configuration::get('KLARNA_EID_' . $country);
                $secret = Configuration::get('KLARNA_SECRET_' . $country);

            }
		}
		
        $storage_file = dirname(dirname(dirname(__FILE__))). '/pclasses/config'.Tools::strtolower($country).'.json';
		$this->kconfig = new KlarnaConfig($storage_file);

        $this->kconfig['eid'] = $eid;
        $this->kconfig['secret'] = $secret;
        if ($locale === true) {
            $localization = new KlarnaLocalization($country);
            $klarna = new KlarnaPrestaApi();
            $klarna_locale = $klarna->getLocale($localization->getCountry(), $localization->getLanguage(), $localization->getCurrency());
            
        $this->kconfig['country'] = $klarna_locale['country'];
        $this->kconfig['language'] = $klarna_locale['language'];
        $this->kconfig['currency'] = $klarna_locale['currency'];    
        }

        $this->kconfig['mode'] = KlarnaConfigHandler::mode();
        $this->kconfig['pcStorage'] = 'json';
        $this->kconfig['pcURI'] = dirname(dirname(dirname(__FILE__))). '/pclasses/pclasses'.Tools::strtolower($country).'.json';

    	$klarna = new KlarnaPrestaApi();
        $klarna->setConfig($this->kconfig);
        if (KlarnaCountry::fromCode($country) !== null) {
                $klarna->setCountry($country);

        }

        $this->country = $country;
        $this->klarna = $klarna;

	}

	public function deleteKlarnaConfig($country) {
        $storage_file = dirname(dirname(dirname(__FILE__))). '/pclasses/config'.Tools::strtolower($country).'.json';
        $this->kconfig = new KlarnaConfig($storage_file);
        $klarna = new KlarnaPrestaApi();
		$klarna->clear($this->kconfig);
	}

}
