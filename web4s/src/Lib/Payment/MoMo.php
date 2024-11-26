<?php

namespace App\Lib\Payment;

use Cake\ORM\TableRegistry;
use App\Lib\Payment\PaymentUtilities;
use Cake\Core\Exception\Exception;
use Cake\Http\Client;
use Cake\Log\Log;

class MoMo
{
    protected $config = [];
    protected $payment_url = '';

    public function __construct($params = [])
    {
        $this->config = TableRegistry::get('PaymentsGateway')->getConfig(MOMO);

        $mode = !empty($this->config['transaction_server']) ? $this->config['transaction_server'] : null;
        if ($mode == LIVE) {
            $this->payment_url = 'https://payment.momo.vn';
        } else {
            $this->payment_url = 'https://test-payment.momo.vn';
        }
    }

    public function sendToGateway($params = [])
    {
        $partner_code = !empty($this->config['partner_code']) ? $this->config['partner_code'] : null;
        $partner_name = !empty($this->config['partner_name']) ? $this->config['partner_name'] : null;
        $access_key = !empty($this->config['access_key']) ? $this->config['access_key'] : null;
        $secret_key = !empty($this->config['secret_key']) ? $this->config['secret_key'] : null;
        $ios_scheme = !empty($this->config['ios_scheme']) ? $this->config['ios_scheme'] : null;

        $end_point = $this->payment_url . '/v2/gateway/api/create';
        $type_os = !empty($params['type_os']) ? $params['type_os'] : '';
        $payment_code = !empty($params['payment_code']) ? $params['payment_code'] : '';
        $order_description = !empty($params['order_description']) ? $params['order_description'] : '';
        $bill_code = !empty($params['bill_code']) ? $params['bill_code'] : '';
        $redirect_url = !empty($params['redirect_url']) ? $params['redirect_url'] : '';
        $ipn_url = !empty($params['ipn_url']) ? $params['ipn_url'] : '';
        $amount = !empty($params['amount']) ? intval($params['amount']) : 0;
        $request_type = 'captureWallet';
        $extra_data = !empty($params['extra_data']) ? $params['extra_data'] : '';

        $request_id = time().'';

        if(!empty($ios_scheme) && ($type_os == 'apple' || $type_os == 'android')){
            $domain = $_SERVER['SERVER_NAME'];
            $redirect_url = "$ios_scheme://$domain?orderCode=$bill_code";
        }

        //before sign HMAC SHA256 signature
        $raw_hash = "accessKey=" . $access_key . "&amount=" . $amount . "&extraData=" . $extra_data . "&ipnUrl=" . $ipn_url . "&orderId=" . $payment_code . "&orderInfo=" . $order_description . "&partnerCode=" . $partner_code . "&redirectUrl=" . $redirect_url . "&requestId=" . $request_id . "&requestType=" . $request_type;

        $signature = hash_hmac('sha256', $raw_hash, $secret_key);    
        
        $data = [
            'partnerCode' => $partner_code,
            'partnerName' => $partner_name,
            'storeId' => $partner_code,
            'requestType' => $request_type,
            'ipnUrl' => $ipn_url,
            'redirectUrl' => $redirect_url,
            'orderId' => $payment_code,
            'amount' => $amount,
            'lang' => 'vi',
            'autoCapture' => true,
            'orderInfo' => $order_description,
            'accessKey' => $access_key,
            'requestId' => $request_id,
            'extraData' => $extra_data,
            'signature' => $signature
        ];
        
        $json_data = json_encode($data);

        $http = new Client();
        $response = $http->post($end_point, $json_data,[
            'headers' => [
                'Content-Type' => 'application/json',
                'Content-Length' => strlen($json_data)
            ],
            'timeout' => 20,
            'type' => 'json'
        ]);

        $json_result = $response->getStringBody();
        
        $utilities = new PaymentUtilities();
        if(empty($json_result) || !$utilities->isJson($json_result)){
            return $utilities->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $result = json_decode($json_result, true);
        if(!empty($json_result['resultCode'])){            
            $message = !empty($result[MESSAGE]) ? $result[MESSAGE] : __d('template', 'du_lieu_khong_hop_le');
            return $utilities->getResponse([MESSAGE => $message]);
        }

        $pay_url = !empty($result['payUrl']) ? $result['payUrl'] : null;
        if(empty($pay_url)){
            return $utilities->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_duong_dan_thanh_toan')]);
        }

        return $utilities->getResponse([
            CODE => SUCCESS,
            DATA => [
                'pay_url' => $pay_url,
                'app_pay_url' => !empty($result['deeplink']) ? $result['deeplink'] : null
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
        
        $partner_code = !empty($this->config['partner_code']) ? $this->config['partner_code'] : '';
        $access_key = !empty($this->config['access_key']) ? $this->config['access_key'] : null;
        $secret_key = !empty($this->config['secret_key']) ? $this->config['secret_key'] : '';
        
        $request_id = !empty($params['requestId']) ? $params['requestId'] : '';
        $amount = !empty($params['amount']) ? $params['amount'] : '';
        $payment_code = !empty($params['orderId']) ? $params['orderId'] : '';
        $order_description = !empty($params['orderInfo']) ? $params['orderInfo'] : '';
        $order_type = !empty($params['orderType']) ? $params['orderType'] : '';
        $trans_id = !empty($params['transId']) ? $params['transId'] : '';
        $message = !empty($params['message']) ? $params['message'] : '';
        $local_message = !empty($params['localMessage']) ? $params['localMessage'] : '';
        $response_time = !empty($params['responseTime']) ? $params['responseTime'] : '';
        $result_code = isset($params['resultCode']) ? $params['resultCode'] : '';
        $error_code = isset($params['errorCode']) ? $params['errorCode'] : '';
        $pay_type = !empty($params['payType']) ? $params['payType'] : '';
        $extra_data = !empty($params['extraData']) ? $params['extraData'] : '';

        $signature_from_momo = !empty($params['signature']) ? $params['signature'] : '';

        //before sign HMAC SHA256 signature
        $raw_hash = "accessKey=$access_key&amount=$amount&extraData=$extra_data&message=$message&orderId=$payment_code&orderInfo=$order_description&orderType=$order_type&partnerCode=$partner_code&payType=$pay_type&requestId=$request_id&responseTime=$response_time&resultCode=$result_code&transId=$trans_id";

        $signature = hash_hmac('sha256', $raw_hash, $secret_key);

        $result = [
            CODE => ERROR,
            MESSAGE => null,
            DATA => [
                'code' => $payment_code
            ]
        ];

        if($signature != $signature_from_momo){
            return $utilities->getResponse([
                MESSAGE => $result_code . ': ' . __d('template', 'chu_ky_khong_hop_le')
            ]);
        }

        if($result_code != '0'){
            $result[MESSAGE] = $result_code . ': ' . __d('template', 'giao_dich_khong_thanh_cong');
            return $utilities->getResponse($result);
        }

        // success
        $result[MESSAGE] = $result_code . ': ' . __d('template', 'giao_dich_thanh_cong');
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
        
        $partner_code = !empty($this->config['partner_code']) ? $this->config['partner_code'] : '';
        $access_key = !empty($this->config['access_key']) ? $this->config['access_key'] : '';
        $secret_key = !empty($this->config['secret_key']) ? $this->config['secret_key'] : '';

        $request_id = !empty($data['requestId']) ? $data['requestId'] : '';
        $amount = !empty($data['amount']) ? $data['amount'] : '';
        $payment_code = !empty($data['orderId']) ? $data['orderId'] : '';
        $order_info = !empty($data['orderInfo']) ? $data['orderInfo'] : '';
        $order_type = !empty($data['orderType']) ? $data['orderType'] : '';
        $trans_id = !empty($data['transId']) ? $data['transId'] : '';
        $message = !empty($data['message']) ? $data['message'] : '';
        $local_message = !empty($data['localMessage']) ? $data['localMessage'] : '';
        $response_time = !empty($data['responseTime']) ? $data['responseTime'] : '';
        $result_code = isset($data['resultCode']) ? $data['resultCode'] : '';
        $error_code = isset($data['errorCode']) ? $data['errorCode'] : '';
        $pay_type = !empty($data['payType']) ? $data['payType'] : '';
        $extra_data = !empty($data['extraData']) ? $data['extraData'] : '';
        $signature_from_momo = !empty($data['signature']) ? $data['signature'] : '';

        if(empty($payment_code)){
            return $utilities->getResponse([
                MESSAGE => __d('template', 'khong_lay_duoc_ma_giao_dich')
            ]);
        }

        $raw_hash = "accessKey=$access_key&amount=$amount&extraData=$extra_data&message=$message&orderId=$payment_code&orderInfo=$order_info&orderType=$order_type&partnerCode=$partner_code&payType=$pay_type&requestId=$request_id&responseTime=$response_time&resultCode=$result_code&transId=$trans_id";
        $signature = hash_hmac('sha256', $raw_hash, $secret_key);

        if($signature != $signature_from_momo){
            return $utilities->getResponse([
                MESSAGE => $result_code . ': ' . __d('template', 'chu_ky_khong_hop_le')
            ]);
        }        

        $result = [
            CODE => ERROR,
            MESSAGE => __d('template', 'giao_dich_khong_thanh_cong'),
            DATA => [
                'code' => $payment_code,
                'payment_transaction_no' => $trans_id,
                'payment_gateway_response' => json_encode($data)
            ]
        ];

        $table = TableRegistry::get('Payments');

        // check status payment
        $payment_info = $table->findByCode($payment_code)->first();
        if (empty($payment_info)) {
            $result[MESSAGE] = __d('template', 'khong_tim_thay_thong_tin_giao_dich');
            return $utilities->getResponse($result);
        }

        $status_payment = isset($payment_info['status']) ? $payment_info['status'] : '';
        if($status_payment == 1) {
            $result[MESSAGE] = __d('template', 'giao_dich_nay_da_thanh_cong');
            return $utilities->getResponse($result);
        }
        
        $payment_amount = !empty($payment_info['amount']) ? floatval($payment_info['amount']) : 0;
        if($amount != $payment_amount){
            $result[MESSAGE] = __d('template', 'gia_tri_giao_dich_khong_chinh_xac');
            return $utilities->getResponse($result);
        }

        if ($result_code == '0') {
            $result[CODE] = SUCCESS;
            $result[MESSAGE] = __d('template', 'giao_dich_thanh_cong');

            return $utilities->getResponse($result);
        }

        return $utilities->getResponse($result);
    }
}

?>