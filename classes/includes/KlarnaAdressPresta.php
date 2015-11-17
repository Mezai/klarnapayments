<?php

class KlarnaAdressPresta
{
	public static function buildKlarnaAddr($presta, $customer)
	{
		$country = new Country($presta->id_country);
        $address = KlarnaPrestaEncoding::encode($presta->address1);

        $houseNr = '';
        $houseExt = '';

        // $kittAddresses = new KiTT_Addresses(null);
        // // detect house number for netherlands and germany
        // if (($country->iso_code  == 'NL')
        //     || ($country->iso_code == 'DE')
        // ) {
        //     $split = $kittAddresses->splitAddress($address);
        //     $address = @$split[0];
        //     $houseNr = @$split[1];
        //     $houseExt = @$split[2];
        // }

        $addr = new KlarnaAddr(
            KlarnaPrestaEncoding::encode($customer->email),  
            KlarnaPrestaEncoding::encode($presta->phone),
            KlarnaPrestaEncoding::encode($presta->phone_mobile),
            KlarnaPrestaEncoding::encode($presta->firstname),
            KlarnaPrestaEncoding::encode($presta->lastname),
            null,  // c/o
            $address,
            KlarnaPrestaEncoding::encode($presta->postcode),
            KlarnaPrestaEncoding::encode($presta->city),
            KlarnaPrestaEncoding::encode($country->iso_code),
            $houseNr,
            $houseExt
        );
        if ($presta->company) {
            $addr->setCompanyName($presta->company);
        }
        return $addr;
	}
}