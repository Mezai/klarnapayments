<?php

class KlarnaAddressPrestashop
{
	/**
	*@param ps Ps address obj
	*@return KlarnaAddress
	*/

	public static function klarnaPrestashopAddress($ps)
	{
		$country = new Country($ps->id_country);
		$address = KlarnaPrestaEncoding::encode($ps->address1);

		$house_number = "";
		$house_extension = "";

		$kittAddresses = new KiTT_Addresses(null);

		if ($country->iso_code == 'NL' || $country->iso_code == 'DE') {

			//now we need to split the address
			$address_split = $kittAddresses->splitAddress($address);
		}

		$addr = new KlarnaAddr(
			null
			)
	}
}