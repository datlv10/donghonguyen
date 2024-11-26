<?php

namespace App\Lib\Payment;

use Cake\ORM\TableRegistry;
use App\Lib\Payment\PaymentUtilities;
use Cake\Core\Exception\Exception;

class OnePay
{
    protected $payment_url = '';
    protected $config = [];

    public function __construct($params)
    {
        $this->config = TableRegistry::get('PaymentsGateway')->getConfig(ONEPAY);      

        $mode = !empty($this->config['transaction_server']) ? $this->config['transaction_server'] : null;
        if ($mode == LIVE) {
            $this->payment_url = 'https://onepay.vn/paygate/vpcpay.op';
        } else {
            $this->payment_url = 'https://mtf.onepay.vn/paygate/vpcpay.op';
        }        
    }

    public function sendToGateway($params = []) 
    {
        $merchant_id = !empty($this->config['merchant_id']) ? $this->config['merchant_id'] : null;
        $access_key = !empty($this->config['access_key']) ? $this->config['access_key'] : null;
        $secret_key = !empty($this->config['secret_key']) ? $this->config['secret_key'] : null;

        $ip_client = !empty($params['ip_client']) ? $params['ip_client'] : null;
        $amount = !empty($params['amount']) ? floatval($params['amount']) * 100 : 0;
        $ip_client = !empty($params['ip_client']) ? $params['ip_client'] : null;
        $payment_code = !empty($params['payment_code']) ? $params['payment_code'] : '';
        $order_description = !empty($params['order_description']) ? $params['order_description'] : '';

        $lang = 'en';
        if(!empty($params['lang']) && $params['lang'] == 'vi'){
            $lang = 'vn';
        }
        
        $redirect_url = !empty($params['redirect_url']) ? $params['redirect_url'] : '';
        $referer_url = !empty($params['referer_url']) ? $params['referer_url'] : '';

        $input_data = [
            'vpc_Version' => '2',
            'vpc_Command' => 'pay',
            'vpc_AccessCode' => $access_key,
            'vpc_Merchant' => $merchant_id,
            'vpc_Locale' => $lang,
            'vpc_ReturnURL' => $redirect_url,
            'vpc_MerchTxnRef' => $payment_code,
            'vpc_OrderInfo' => $order_description,
            'vpc_Amount' => $amount,
            'vpc_TicketNo' => $ip_client,
            'AgainLink' => $referer_url,
            'Title' => $order_description
        ];
        
        ksort($input_data);
        
        $query = $hash_data = '';
        $i = 0;

        $append_amp = 0;
        foreach ($input_data as $key => $value) {
            if (strlen($value) > 0) {
                // this ensures the first paramter of the URL is preceded by the '?' char
                if ($append_amp == 0) {
                    $query .= urlencode($key) . '=' . urlencode($value);
                    $append_amp = 1;
                } else {
                    $query .= '&' . urlencode($key) . '=' . urlencode($value);
                }

                if ((strlen($value) > 0) && ((substr($key, 0,4)== 'vpc_') || (substr($key,0,5) == 'user_'))) {
                    $hash_data .= $key . '=' . $value . '&';
                }
            }
        }

        $hash_data = rtrim($hash_data, '&');        
        if (!empty($secret_key)) {
            $query .= '&vpc_SecureHash=' . strtoupper(hash_hmac('SHA256', $hash_data, pack('H*', $secret_key)));
        }

        $pay_url = $this->payment_url . '?' . $query;

        $utilities = new PaymentUtilities();
        return $utilities->getResponse([
            CODE => SUCCESS,
            DATA => [
                'pay_url' => $pay_url
            ]
        ]);
    }

    public function returnResult($params = [])
    {
        $utilities = new PaymentUtilities();

        if(empty($params) || !is_array($params)){
            return $utilities->getResponse([
                MESSAGE => __d('template', 'chu_ky_khong_hop_le')
            ]);
        }

        $response_code = isset($params['vpc_TxnResponseCode']) ? $params['vpc_TxnResponseCode'] : null;
        $secure_hash_from_onepay = !empty($params['vpc_SecureHash']) ? $params['vpc_SecureHash'] : null;

        $input_data = [];
        foreach ($params as $key => $value) {
            if (substr($key, 0, 4) == 'vpc_') {
                $input_data[$key] = $value;
            }
        }
        
        unset($input_data['vpc_SecureHash']);
        ksort($input_data);

        $i = 0;
        $hash_data = '';
        foreach ($input_data as $key => $value) {
            if ($key != 'vpc_SecureHash' && (strlen($value) > 0) && ((substr($key, 0,4)=='vpc_') || (substr($key,0,5) == 'user_'))) {
                $hash_data .= $key . '=' . $value . '&';
            }
        }
        $hash_data = rtrim($hash_data, '&');

        $hash_secret = !empty($this->config['secret_key']) ? $this->config['secret_key'] : null;
        $secure_hash = strtoupper(hash_hmac('SHA256', $hash_data, pack('H*', $hash_secret)));


        $result = [
            CODE => ERROR,
            MESSAGE => null,
            DATA => [
                'code' => !empty($params['vpc_MerchTxnRef']) ? $params['vpc_MerchTxnRef'] : null
            ]
        ];

        if($secure_hash != $secure_hash_from_onepay){
            $result[MESSAGE] = $response_code . ': ' . __d('template', 'chu_ky_khong_hop_le');
            return $utilities->getResponse($result);
        }

        if($response_code != '0'){
            $result[MESSAGE] = $response_code . ': ' . __d('template', 'giao_dich_khong_thanh_cong');
            return $utilities->getResponse($result);
        }

        // success
        $result[MESSAGE] = $response_code . ': ' . __d('template', 'giao_dich_thanh_cong');
        $result[CODE] = SUCCESS;
        return $utilities->getResponse($result);
    }

    public function webhooks($params = []) 
    {
        $utilities = new PaymentUtilities();

        $data = !empty($params['data']) ? $params['data'] : [];
        if(empty($data) || !is_array($data)){
            return $utilities->getResponse([
                MESSAGE => __d('template', 'khong_lay_duoc_du_lieu_tra_ve_tu_webhooks')
            ]);
        }

        $hash_secret = !empty($this->config['secret_key']) ? $this->config['secret_key'] : null;        
        $input_data = [];
        foreach ($data as $key => $value) {
            if (substr($key, 0, 4) == 'vpc_') {
                $input_data[$key] = $value;
            }
        }

        $response_code = isset($input_data['vpc_TxnResponseCode']) ? $input_data['vpc_TxnResponseCode'] : null;
        $secure_hash_from_onepay = !empty($input_data['vpc_SecureHash']) ? $input_data['vpc_SecureHash'] : null;

        unset($input_data['vpc_SecureHash']);
        ksort($input_data);

        if(empty($input_data)){
            return $utilities->getResponse([
                MESSAGE => __d('template', 'khong_lay_duoc_du_lieu_tra_ve_tu_webhooks')
            ]);
        }

        $payment_code = !empty($input_data['vpc_MerchTxnRef']) ? $input_data['vpc_MerchTxnRef'] : null;
        if(empty($payment_code)){
            return $utilities->getResponse([
                MESSAGE => __d('template', 'khong_lay_duoc_ma_giao_dich')
            ]);
        }

        $result = [
            CODE => ERROR,
            MESSAGE => __d('template', 'giao_dich_khong_thanh_cong'),
            DATA => [
                'code' => $payment_code,
                'payment_transaction_no' => !empty($input_data['vpc_TransactionNo']) ? $input_data['vpc_TransactionNo'] : null,
                'payment_gateway_response' => json_encode($input_data)
            ]
        ];


        $i = 0;
        $hash_data = '';
        foreach ($input_data as $key => $value) {
            if ($key != 'vpc_SecureHash' && (strlen($value) > 0) && ((substr($key, 0,4)=='vpc_') || (substr($key,0,5) == 'user_'))) {
                $hash_data .= $key . '=' . $value . '&';
            }
        }    
        $hash_data = rtrim($hash_data, '&');
        $secure_hash = strtoupper(hash_hmac('SHA256', $hash_data, pack('H*', $hash_secret)));
        if($secure_hash != $secure_hash_from_onepay){
            return $utilities->getResponse([
                MESSAGE => $response_code . ': ' . __d('template', 'chu_ky_khong_hop_le')
            ]);
        }

        $table = TableRegistry::get('Payments');

        // check status payment
        $payment_info = $table->findByCode($payment_code)->first();
        if (empty($payment_info)) {
            $result[MESSAGE] = __d('template', 'khong_tim_thay_thong_tin_giao_dich');
            return $utilities->getResponse($result);
        }

        $status_payment = isset($payment_info['status']) ? $payment_info['status'] : null;
        if($status_payment == 1) {
            $result[MESSAGE] = __d('template', 'giao_dich_nay_da_thanh_cong');
            return $utilities->getResponse($result);
        }

        $net_amount = !empty($input_data['vpc_Amount']) ? floatval($input_data['vpc_Amount']) / 100 : 0;
        $amount = !empty($payment_info['amount']) ? floatval($payment_info['amount']) : 0;

        if($amount != $net_amount){
            $result[MESSAGE] = __d('template', 'gia_tri_giao_dich_khong_chinh_xac');
            return $utilities->getResponse($result);
        }

        if ($response_code == '0') {
            $result[CODE] = SUCCESS;
            $result[MESSAGE] = __d('template', 'giao_dich_thanh_cong');

            return $utilities->getResponse($result);
        }

        return $utilities->getResponse($result);        
    }

}

?>