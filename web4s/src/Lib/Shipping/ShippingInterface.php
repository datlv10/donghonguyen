<?php

namespace App\Lib\Shipping;

interface ShippingInterface{
    public function calculateFee($params);
    public function createOrder($params);
    public function cancelOrder($params);
    public function orderInfo($params);
    public function webhooks($params);    
}