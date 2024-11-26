<?php

namespace App\Lib\Payment;

use Cake\ORM\TableRegistry;
use App\Lib\Payment\PaymentUtilities;
use Cake\Core\Exception\Exception;
use Firebase\JWT\JWT;
use Cake\Http\Client;

class BaoKim
{
    protected $payment_url = '';
    protected $config = [];

    const TOKEN_EXPIRE = 60; 

    public function __construct($params)
    {
        $this->config = TableRegistry::get('PaymentsGateway')->getConfig(BAOKIM);

        $mode = !empty($this->config['transaction_server']) ? $this->config['transaction_server'] : null;
        if ($mode == LIVE) {
            $this->payment_url = 'https://api.baokim.vn/payment';
        } else {
            $this->payment_url = 'https://dev-api.baokim.vn/payment';
        }
    }

    public function getToken($data = [])
    {   
        $api_key = !empty($this->config['api_key']) ? $this->config['api_key'] : null;
        $api_secret = !empty($this->config['api_secret']) ? $this->config['api_secret'] : null;

        $token_id = base64_encode(random_bytes(32));
        $issued_at = time();
        $not_before  = $issued_at;
        $expire = $not_before + self::TOKEN_EXPIRE;
        $data_encode = [
            'iat' => $issued_at,
            'jti' => $token_id,
            'iss' => $api_key,
            'nbf' => $not_before, 
            'exp' => $expire,
            'form_params' => []
        ];

        $jwt = new JWT();
        $token = $jwt->encode($data_encode, $api_secret, 'HS256');

        return $token;
    }

    public function sendToGateway($params = []) 
    {
        $merchant_id = !empty($this->config['merchant_id']) ? $this->config['merchant_id'] : null;
        $end_point = $this->payment_url . '/api/v5/order/send';
        $amount = !empty($params['amount']) ? floatval($params['amount']) : 0;
        $payment_code = !empty($params['payment_code']) ? $params['payment_code'] : '';
        $order_description = !empty($params['order_description']) ? $params['order_description'] : '';
        
        $ipn_url = !empty($params['ipn_url']) ? $params['ipn_url'] : '';
        $redirect_url = !empty($params['redirect_url']) ? $params['redirect_url'] : '';
        $referer_url = !empty($params['referer_url']) ? $params['referer_url'] : '';

        $data = [
            'merchant_id' => $merchant_id,
            'mrc_order_id' => $payment_code,
            'total_amount' => $amount,
            'description' => $order_description,
            'url_success' => $redirect_url,
            'url_detail' => $referer_url,
            'webhooks' => $ipn_url
        ];

        // get token
        $token = $this->getToken($data);
        $end_point = $this->payment_url . '/api/v5/order/send?jwt=' . $this->getToken($data);

        $http = new Client();
        $response = $http->post($end_point, $data,[
            'timeout' => 60,
        ]);

        $body = $response->getStringBody();
        $result = json_decode($body, true);

        $utilities = new PaymentUtilities();
        if(!isset($result['code']) || $result['code'] != 0){            
            $message = !empty($result[MESSAGE]) ? $result[MESSAGE] : __d('template', 'du_lieu_khong_hop_le');
            return $utilities->getResponse([MESSAGE => $message]);
        }

        $data = !empty($result[DATA]) ? $result[DATA] : [];
        $pay_url = !empty($data['payment_url']) ? $data['payment_url'] : null;
        if(empty($pay_url)){
            return $utilities->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_duong_dan_thanh_toan')]);
        }

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

        $api_secret = !empty($this->config['api_secret']) ? $this->config['api_secret'] : null;

        $mrc_order_id = !empty($params['mrc_order_id']) ? $params['mrc_order_id'] : null;
        $total_amount = isset($params['total_amount']) ? floatval($params['total_amount']) : null;
        $checksum = isset($params['checksum']) ? $params['checksum'] : null;

        if(empty($mrc_order_id) || empty($checksum)){
            $result[MESSAGE] = __d('template', 'chu_ky_khong_hop_le');
            return $utilities->getResponse($result);
        }

        unset($params['checksum']);
        ksort($params);

        $my_checksum = hash_hmac('sha256', http_build_query($params), $api_secret);

        $result = [
            CODE => ERROR,
            MESSAGE => null,
            DATA => [
                'code' => $mrc_order_id
            ]
        ];

        if($checksum != $my_checksum){
            $result[MESSAGE] = __d('template', 'chu_ky_khong_hop_le');
            return $utilities->getResponse($result);
        }

        // success
        $result[MESSAGE] = __d('template', 'giao_dich_thanh_cong');
        $result[CODE] = SUCCESS;

        return $utilities->getResponse($result);   
    }

    public function webhooks($params = []) 
    {
        $utilities = new PaymentUtilities();

        $data = !empty($params['data']) ? $params['data'] : [];
        $order_result = !empty($data['order']) ? $data['order'] : [];
        $txn_result = !empty($data['txn']) ? $data['txn'] : [];

        if(empty($data) || !is_array($data)){
            return $utilities->getResponse([
                MESSAGE => __d('template', 'khong_lay_duoc_du_lieu_tra_ve_tu_webhooks')
            ]);
        }

        $payment_code = !empty($order_result['mrc_order_id']) ? $order_result['mrc_order_id'] : null;
        $payment_transaction_no = !empty($txn_result['reference_id']) ? $txn_result['reference_id'] : null;

        $api_secret = !empty($this->config['api_secret']) ? $this->config['api_secret'] : null;
        $sign = !empty($data['sign']) ? $data['sign'] : null;
        unset($data['sign']);

        $sign_data = json_encode($data);
        $my_sign = hash_hmac('sha256', $sign_data, $api_secret);

        if ($sign != $my_sign) {
            $result['result_for_gatewave'] = [
                'err_code' => '0',
                'message' => 'Invalid Checksum'
            ];
            $result[MESSAGE] = __d('template', 'chu_ky_khong_hop_le');
            return $utilities->getResponse($result);
        }

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
                'payment_transaction_no' => $payment_transaction_no,
                'payment_gateway_response' => json_encode($data)
            ]
        ];

        $table = TableRegistry::get('Payments');

        // check status payment
        $payment_info = $table->findByCode($payment_code)->first();
        if (empty($payment_info)) {
            $result['result_for_gatewave'] = [
                'err_code' => '0',
                'message' => 'Order Not Found'
            ];

            $result[MESSAGE] = __d('template', 'khong_tim_thay_thong_tin_giao_dich');
            return $utilities->getResponse($result);
        }

        $net_amount = !empty($order_result['total_amount']) ? floatval($order_result['total_amount']) : 0;
        $amount = !empty($payment_info['amount']) ? floatval($payment_info['amount']) : 0;

        if($amount != $net_amount){
            $result['result_for_gatewave'] = [
                'err_code' => '0',
                'message' => 'Invalid amount'
            ];

            $result[MESSAGE] = __d('template', 'gia_tri_giao_dich_khong_chinh_xac');
            return $utilities->getResponse($result);
        }


        $status_payment = isset($payment_info['status']) ? $payment_info['status'] : null;
        if($status_payment == 1) {
            $result['result_for_gatewave'] = [
                'err_code' => '0',
                'message' => 'Order already confirmed'
            ];

            $result[MESSAGE] = __d('template', 'giao_dich_nay_da_thanh_cong');
            return $utilities->getResponse($result);
        }


        $result['result_for_gatewave'] = [
            'err_code' => '0',
            'message' => 'Confirm Success'
        ];

        $result[CODE] = SUCCESS;
        $result[MESSAGE] = __d('template', 'giao_dich_thanh_cong');
        
        return $utilities->getResponse($result);
    }

}

?>