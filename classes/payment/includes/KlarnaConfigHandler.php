<?php

require_once '/../KlarnaPrestashopCore.php';


class KlarnaConfigHandler
{


	/**
	*@param $country_iso = 'SE' etc
	*@param $klarna_settings = array containing our settings from PS
	*@param $type string invoice, checkout or part to check if part payment / checkout / invoice is active
	*@return boolean true|false
	*/
    public static function checkConfigurationByLocale($country_iso, $klarna_settings, $type = null)
    {
    	if (!is_string($country_iso) || !is_array($klarna_settings))
    		return;

    	foreach ($klarna_settings as $key => $value) {
    		if ($key == $country_iso) {
    			if ((int)$value['active'] == 1 && $value['klarna_eid'] != "" && $value['klarna_secret'] != "") {
    				
                    if ($type == 'part' && (int)$value['klarna_part'] === 1) {
                        return true;
                    } else {
                        return false;
                    }

                    if ($type == 'invoice' && (int)$value['klarna_invoice'] === 1) {
                        return true;
                    } else {
                        return false;
                    }

                    if ($type == 'checkout' && (int)$value['klarna_checkout'] == 1) {
                        return true;
                    } else {
                        return false;
                    }

                  return true;  
                }
               return false; 
            }
    	}	
    }


    /**
    *Get the merchant id for a given country iso eg 'SE'
    *@param $country string 'SE'
    *@param array Klarna PS settings
    *@return string merchant id | null 
    */

    public static function getMerchantID($country, $klarna_settings)
    {
    	if (!is_string($country) || !is_array($klarna_settings))
    		return;

    	foreach ($klarna_settings as $key => $value) {
    		if ($key == $country)
    		{
    			if ((int)$value['active'] == 1){
    				return $value['klarna_eid'];
    			}
    			return null;
    		}
    		return null;
    	}
    }


    /**
    *Get the klarna secret from a given country
    *@param $country string 'SE'
    *@param array Klarna settings
    *@return string klarna secret | null
    */


    public static function getKlarnaSecret($country, $klarna_settings)
    {	
    	if (!is_string($country) || is_array($klarna_settings)) {
    		return;
    	}

    	foreach ($klarna_settings as $key => $value) {
    		if ($country == $key) 
    		{
    			if ((int)$value['active'] == 1) {
    				return $value['klarna_secret'];
    			}
    			return null;
    		}
    		return null;
    	}
    }


    /**
    *
    *@param $settings PS settings array 
    *@return an array containing configured countries
    */

    public static function returnActiveCountries($settings)
    {
    	$country_array = array('SE', 'NO', 'DK', 'NL', 'DE', 'FI');
    	$active_countries = array();
    	foreach ($settings as $key => $value) {
    		foreach ($country_array as $country_value) {
    			if ($country_value ==  $key) {
    				if ((int)$value['active'] == 1 && $value['klarna_eid'] != "" && $value['klarna_secret'] != "") {
    					array_push($active_countries, $key);
    				}

    			}
    		}

    	}
    	return $active_countries;
    }

    public static function isCountryActive($country, $settings)
    {
    	$active_array = self::returnActiveCountries($settings);

    	if (in_array($country, $active_array)) {
    		return true;
    	} else {
    		return false;
    	}
    }


    /**
	*
	*@return int Klarna::LIVE or KLARNA::BETA
	*
	*/
    public static function mode()
    {
    	switch (Configuration::get('KLARNA_ENVIRONMENT')) {
    		case 'live':
    			return Klarna::LIVE;
    		case 'beta':
    			return Klarna::BETA;
       	}
    }

    public static function pcURI() {
        
    }


 

	public static function setConfigurationByLocale($klarna_country, $klarna_settings)
	{
		if (!is_string($klarna_country) || !is_array($klarna_settings)) {
				return;
		}

		$k = new Klarna();

		foreach ($klarna_settings as $key => $value) {
				if ($key == $klarna_country) {
				$locale = self::getKlarnaLocaleConfiguration($key);
					$k->config(
			  	(int)$value['klarna_eid'],
			    (String)$value['klarna_secret'],
			    $locale[0],
			    $locale[1],
			    $locale[2],
			    (Configuration::get('KLARNA_ENVIRONMENT') == 'live') ? Klarna::LIVE : Klarna::BETA,
			    'json',
			    dirname(__FILE__).'/../pclasses/pclasses.json'
					);
				}

		}

		return $k;
	}


	private static function getKlarnaLocaleConfiguration($country_iso)
	{
		switch ($country_iso)
		{
			case 'SE':
            $locale = new KlarnaLocalization('SE', 'SV', 'SEK');
            $currency = $locale->getCurrency();
            $country = $locale->getCountry();
            $language = $locale->getLanguage();
				return array($country, $language, $currency);
			case 'FI':
				return array(KlarnaCountry::FI, KlarnaLanguage::FI, KlarnaCurrency::EUR);
			case 'DE':
				return array(KlarnaCountry::DE, KlarnaLanguage::DE, KlarnaCurrency::EUR);
			case 'DK':
				return array(KlarnaCountry::DK, KlarnaLanguage::DA, KlarnaCurrency::DKK);
			case 'NO':
				return array(KlarnaCountry::NO, KlarnaLanguage::NB, KlarnaCurrency::NOK);
			case 'NL':
				return array(KlarnaCountry::NL, KlarnaLanguage::NL, KlarnaCurrency::EUR);
			case 'AT':
				return array(KlarnaCountry::DE, KlarnaLanguage::DE, KlarnaCurrency::EUR);
		}
	}


	public static function getLocale($language)
	{
		switch ($language)
		{
			case 'sv':
				return 'sv_SE';
			case 'no':
				return 'nb_NO';
			case 'fi':
				return 'fi_FI';
			case 'da':
				return 'da_DK';
			case 'de':
				return 'de_DE';
			case 'nl':
				return 'nl_NL';
			
		}
	}


}
