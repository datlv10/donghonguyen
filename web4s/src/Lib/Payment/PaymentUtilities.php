<?php

namespace App\Lib\Payment;

use Cake\ORM\TableRegistry;

class PaymentUtilities
{
    public function getResponse($params = []) 
    {
        $code = ERROR;
        if(!empty($params[CODE]) && in_array($params[CODE], [SUCCESS, ERROR])){
            $code = $params[CODE];
        }

        $message = !empty($params[MESSAGE]) ? $params[MESSAGE] : null;
        if(empty($params[MESSAGE]) && $code == ERROR){
            $message = __d('template', 'xu_ly_du_lieu_khong_thanh_cong');
        }

        if(empty($params[MESSAGE]) && $code == SUCCESS){
            $message = __d('template', 'xu_ly_du_lieu_thanh_cong');
        }
        
        $result = [
            CODE => $code,
            MESSAGE => $message,
        ];

        if(isset($params['result_for_gatewave'])){
            $result['result_for_gatewave'] = !empty($params['result_for_gatewave']) ? $params['result_for_gatewave'] : [];
        }

        if(isset($params[DATA])){
            $result[DATA] = !empty($params[DATA]) ? $params[DATA] : [];
        }

        return $result;
    }

    public function isJson($json_str = null)
    {
        return is_string($json_str) && is_array(json_decode($json_str, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }

    public function transferToUsd($number = 0)
    {
        if(empty($number)) return 0;

        // nếu đơn vị tiền tệ đang chọn không phải mặc định thì chuyển đổi lại tổng tiền sang mặc định
        if(CURRENCY_CODE != CURRENCY_CODE_DEFAULT){
            $number = $number * CURRENCY_RATE;
        }

        // chuyển đổi tiền tệ sang USD    
        if(CURRENCY_CODE_DEFAULT != 'USD'){
            $all_currencies = TableRegistry::get('Currencies')->getAll();
            $usd_currency = !empty($all_currencies['USD']) ? $all_currencies['USD'] : [];
            $exchange_rate = !empty($usd_currency['exchange_rate']) ? $usd_currency['exchange_rate'] : null;
            if(!empty($exchange_rate)){
                $number = $number / floatval($exchange_rate);
            }
        }

        $number = round($number, 2);

        return $number;
    }

}

?>