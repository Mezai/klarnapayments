<?php

  protected $country;

class KlarnaCountryLogic
{
  public function __construct(KlarnaLocalization $locale)
  {
    $this->country = Tools::strtoupper($locale->getCountryCode());
  }


  public function needGender()
  {
    switch ($this->country) {
      case 'NL':
      case 'DE':
      case 'AT':
        return true;
      default:
        return false;
    }
  }

  public function needDateOfBirth()
  {
    switch ($this->country) {
        case 'NL':
        case 'DE':
        case 'AT':
            return true;
        default:
            return false;
        }
  }

  public function getSplitCountry()
  {
    switch ($this->country) {
      case 'DE':
        return array('street', 'house_number');
        case 'NL':
        return array('street', 'house_number', 'house_extension');
      default:
        return array('street');
      }
  }

  public function useGetAddress()
  {
    switch ($this->country) {
      case 'SE':
        return true;
      default:
        return false;
    }
  }

  public function isBusinessAllowed()
  {
    switch ($this->country) {
        case 'NL':
        case 'DE':
        case 'AT':
      return false;
      default:
        return true;
    }
  }

  public function isBelowLimit($sum, $method)
    {
        if ($this->country !== 'NL') {
            return true;
        }

        if ($method === KiTT::INVOICE) {
            return true;
        }

        if (((double)$sum) <= 250.0) {
            return true;
        }

        return false;
    }

}
