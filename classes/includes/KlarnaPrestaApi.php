<?php

class KlarnaPrestaApi extends Klarna
{
    /**
     * Constructor for PrestaKlarnaApi
     */
    public function __construct()
    {
        $this->VERSION = '1.0.3';
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