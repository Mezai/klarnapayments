<?php

require_once(dirname(__FILE__).'/../libs/Klarna.php');
require_once(dirname(__FILE__).'/../libs/transport/xmlrpc-3.0.0.beta/lib/xmlrpc.inc');
require_once(dirname(__FILE__).'/../libs/transport/xmlrpc-3.0.0.beta/lib/xmlrpc_wrappers.inc');


class KlarnaConfigHandler
{


    static public function set($array)
    {
        if (!is_array($array))
        {
            $array = array($array);  
        }

        return $array;
    }    

}