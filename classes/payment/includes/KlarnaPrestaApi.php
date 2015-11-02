<?php

class KlarnaPrestaApi extends Klarna
{
    /**
     * Constructor for PrestaKlarnaApi
     */
    public function __construct()
    {
        $this->VERSION = 'PHP:Prestashop:2.1.2';
        parent::__construct();
    }

    /**
     * Get the PCstorage used
     *
     * @return PCStorage
     */
    public function eGetPCStorage()
    {
        return $this->getPCStorage();
    }
}