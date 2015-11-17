<?php

class KlarnaValidation 
{

	public static function getPattern($country)
	{
		switch ($country)
		{
			case 'SE':
				return '^[0-9]{6,6}(([0-9]{2,2}[-\+]{1,1}[0-9]{4,4})|([-\+]{1,1}[0-9]{4,4})|([0-9]{4,6}))$';
			case 'DE':
				return '^[0-9]{7,9}$';
			case 'DK':
				return '^[0-9]{8,8}([0-9]{2,2})?$';
			case 'NO':
				return '^[0-9]{6,6}((-[0-9]{5,5})|([0-9]{2,2}((-[0-9]{5,5})|([0-9]{1,1})|([0-9]{3,3})|([0-9]{5,5))))$';
			case 'FI':
				return '^[0-9]{6,6}(([A\+-]{1,1}[0-9]{3,3}[0-9A-FHJK-NPR-Y]{1,1})|([0-9]{3,3}[0-9A-FHJK-NPR-Y]{1,1})|([0-9]{1,1}-{0,1}[0-9A-FHJK-NPR-Y]{1,1}))$';
			case 'NL':
				return '^[0-9]{7,9}$';
		}
	}

	public static function getPlaceholder($country)
	{
		switch ($country)
		{
			case 'SE':
				return 'YYMMDDNNNN';
			case 'DE':
				return 'DDMMYYYY';
			case 'DK':
				return 'DDMMYYNNNN';
			case 'NO':
				return 'DDMMYYNNNNN';
			case 'FI':
				return 'DDMMYY-NNNN';
			case 'NL':
				return 'DDMMYYYY';
		}
	}
}