<?php
declare(strict_types=1);

namespace App\Lib\SmsBrandname;

class NhSmsBrandname implements SmsBrandnameInterface
{
    protected static $_instance = null;
    protected $code = null;
    
    function __construct($code = null, $params = [])
    {
        switch ($code) {
            case FPT_TELECOM:
                self::$_instance = new FPTTelecom($params);
                break;

            case ESMS:
                self::$_instance = new Esms($params);
                break;
        }

    }

    public function sendOtp($params)
    {
        return self::$_instance->sendOtp($params);
    }  
}