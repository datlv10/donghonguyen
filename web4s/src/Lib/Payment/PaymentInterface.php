<?php

namespace App\Lib\Payment;

interface PaymentInterface{
    public function sendToGateway($params);
    public function returnResult($params);
    public function webhooks($params);    
}