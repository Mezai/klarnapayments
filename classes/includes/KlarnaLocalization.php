<?php

class KlarnaLocalization extends KlarnaPrestaConfig
{
	private static $_api;
	protected $_country;
	protected $_language;
	protected $_currency;
 public function __construct($country, $language = null, $currency = null)
    {
        // For legacy reasons getLanguageForCountry etc is not static
        if (self::$_api === null) {
            self::$_api = new Klarna;
        }

        // Set country
        if (is_numeric($country)) {
            $this->_country = (int)$country;
        } else {
            $this->_country = KlarnaCountry::fromCode($country);
        }

        // Set language from user input or from country default
        if ($language === null) {
            $this->_language = self::$_api->getLanguageForCountry($this->_country);
        } else if (is_numeric($language)) {
            $this->_language = (int)$language;
        } else {
            $this->_language = KlarnaLanguage::fromCode($language);
        }

        // Set currency from user input or from country default
        if ($currency === null) {
            $this->_currency = self::$_api->getCurrencyForCountry($this->_country);
        } else if (is_numeric($currency)) {
            $this->_currency = (int)$currency;
        } else {
            $this->_currency = KlarnaCurrency::fromCode($currency);
        }
    }

    /**
     * get the country of the locale
     *
     * @return int country constant
     */
    public function getCountry()
    {
        return $this->_country;
    }

    public function getCountryCode()
    {
        return KlarnaCountry::getCode($this->_country);
    }

    /**
     * get the language of the locale
     *
     * @return int language constant
     */
    public function getLanguage()
    {
        return $this->_language;
    }

    /**
     * get the language ISO code
    */
   public function getLanguageCode()
   {
       return KlarnaLanguage::getCode($this->_language);
   }

    /**
     * get the currency of the locale
     *
     * @return int currency constant
     */
    public function getCurrency()
    {
        return $this->_currency;
    }

    public function getCurrencyCode()
    {
        switch ($this->_currency) {
            case 0:
               return 'SEK'; 
            case 1:
                return 'NOK';
            case 2:
                return 'EUR';
            case 3:
                return 'DKK';
        }
    }

    public function setlocale()
    {
        switch(Tools::strtoupper($this->_country)) {
        case 'AT':
        case 'DE':
            return 'DE';
        case 'NL':
            return 'NL';
        case 'FI':
            return 'FI';
        case 'DK':
            return 'DA';
        case 'NO':
            return 'NB';
        case 'SE':
            return 'SV';
        default:
            return 'EN';
        }
    }


    public static function getPrestaLanguage($lang)
    {
        switch ($lang)
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

    public function getShortCodes()
    {
        switch ($this->_country)
        {
            case 'SE':
                return array('SE', 'SV', 'SEK');
            case 'FI':
                return array('FI', 'FI', 'EUR');
            case 'DE':
            case 'AT':
                return array('DE', 'DE', 'EUR');
            case 'DK':
                return array('DK', 'DA', 'DKK');
            case 'NO':
                return array('NO', 'NB', 'NOK');
            case 'NL':
                return array('NL', 'NL', 'EUR');
           
        }
    }


}