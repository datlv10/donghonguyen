<?php
declare(strict_types=1);

namespace App\Lib\Payment;

class NhPayment implements PaymentInterface
{
    protected static $_instance = null;
    protected $code = null;
    
    function __construct($code = null, $params = [])
    {
        switch ($code) {
            case ALEPAY:
                self::$_instance = new Alepay();
                break;

            case ONEPAY:
                self::$_instance = new OnePay($params);
                break;

            case ONEPAY_INSTALLMENT:
                self::$_instance = new OnePayInstallment($params);
                break;

            case VNPAY:
                self::$_instance = new VnPay($params);
                break;

            case AZPAY:
                self::$_instance = new AzPay($params);
                break;

            case MOMO:
                self::$_instance = new MoMo($params);
                break;

            case BAOKIM:
                self::$_instance = new BaoKim($params);
                break;

            case VNPTPAY:
                self::$_instance = new VnptPay($params);
                break;

            case PAYPAL:
                self::$_instance = new Paypal($params);
                break;

            case ZALOPAY:
                self::$_instance = new ZaloPay($params);
                break;
        }

    }

    public function sendToGateway($params)
    {
        return self::$_instance->sendToGateway($params);
    }

    public function returnResult($params)
    {
        return self::$_instance->returnResult($params);
    }

    public function webhooks($params)
    {
        return self::$_instance->webhooks($params);
    }    
}