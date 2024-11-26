<?php
declare(strict_types=1);

namespace App\Lib\Shipping;

class NhShipping implements ShippingInterface
{
    protected static $_instance = null;
    protected $code = null;
    
    function __construct($code = null, $params = [])
    {
        switch ($code) {
            case GIAO_HANG_NHANH:
                self::$_instance = new GiaoHangNhanh($params);
                break;

            case GIAO_HANG_TIET_KIEM:
                self::$_instance = new GiaoHangTietKiem($params);
                break;
        }
    }

    public function calculateFee($params)
    {
        return self::$_instance->calculateFee($params);
    }

    public function createOrder($params)
    {
        return self::$_instance->createOrder($params);
    }

    public function cancelOrder($params)
    {
        return self::$_instance->cancelOrder($params);
    } 

    public function orderInfo($params)
    {
        return self::$_instance->orderInfo($params);
    }

    public function webhooks($params)
    {
        return self::$_instance->webhooks($params);
    }    
}