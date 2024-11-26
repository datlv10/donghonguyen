<?php

namespace App\Lib\Payment;

use Cake\ORM\TableRegistry;
use App\Lib\Payment\PaymentUtilities;
use Cake\Core\Exception\Exception;

class VnPay
{
    protected $payment_url = '';
    protected $config = [];

    public function __construct($params)
    {
        $this->config = TableRegistry::get('PaymentsGateway')->getConfig(VNPAY);

        $mode = !empty($this->config['transaction_server']) ? $this->config['transaction_server'] : null;
        if ($mode == LIVE) {
            $this->payment_url = 'https://pay.vnpay.vn/vpcpay.html';
        } else {
            $this->payment_url = 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html';            
        }
    }

    public function sendToGateway($params = []) 
    {
        $tmn_code = !empty($this->config['vnp_TmnCode']) ? $this->config['vnp_TmnCode'] : null;
        $hash_secret = !empty($this->config['vnp_HashSecret']) ? $this->config['vnp_HashSecret'] : null;
        $ip_client = !empty($params['ip_client']) ? $params['ip_client'] : null;
        $amount = !empty($params['amount']) ? floatval($params['amount']) * 100 : 0;
        $payment_code = !empty($params['payment_code']) ? $params['payment_code'] : '';
        $order_description = !empty($params['order_description']) ? $params['order_description'] : '';
        $lang = 'en';
        if(!empty($params['lang']) && $params['lang'] == 'vi'){
            $lang = 'vn';
        }
        
        $redirect_url = !empty($params['redirect_url']) ? $params['redirect_url'] : '';
        $ipn_url = !empty($params['ipn_url']) ? $params['ipn_url'] : '';

        $input_data = [
            'vnp_Version' => '2.1.0',
            'vnp_TmnCode' => $tmn_code,
            'vnp_Amount' => $amount,
            'vnp_Command' => 'pay',
            'vnp_CreateDate' => date('YmdHis'),
            'vnp_CurrCode' => 'VND',
            'vnp_IpAddr' => $ip_client,
            'vnp_Locale' => $lang,
            'vnp_OrderInfo' => $order_description,
            'vnp_OrderType' => 'billpayment',
            'vnp_ReturnUrl' => $redirect_url,
            'vnp_TxnRef' => $payment_code
        ];

        ksort($input_data);

        $query = $hash_data = '';
        $i = 0;
        foreach ($input_data as $key => $value) {            
            if ($i == 1) {
                $hash_data .= '&' . urlencode($key) . '=' . urlencode($value);
            } else {
                $hash_data .= urlencode($key) . '=' . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . '=' . urlencode($value) . '&';
        }

        $pay_url = $this->payment_url . '?' . $query;
        if (!empty($hash_secret)) {
            $vnp_secure_hash = hash_hmac('sha512', $hash_data, $hash_secret);
            $pay_url .= 'vnp_SecureHash=' . $vnp_secure_hash;
        }

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

        $response_code = isset($params['vnp_ResponseCode']) ? $params['vnp_ResponseCode'] : null;

        $input_data = [];
        foreach ($params as $key => $value) {
            if (substr($key, 0, 4) == 'vnp_') {
                $input_data[$key] = $value;
            }
        }
        
        unset($input_data['vnp_SecureHashType']);
        unset($input_data['vnp_SecureHash']);
        ksort($input_data);
        
        $i = 0;
        $hash_data = '';
        foreach ($input_data as $key => $value) {
            if ($i == 1) {
                $hash_data = $hash_data . '&' . urlencode($key) . "=" . urlencode($value);
            }else {
                $hash_data = $hash_data . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $hash_secret = !empty($this->config['vnp_HashSecret']) ? $this->config['vnp_HashSecret'] : null;       
        $secure_hash = hash_hmac('sha512', $hash_data, $hash_secret);

        $secure_hash_from_vnp = !empty($params['vnp_SecureHash']) ? $params['vnp_SecureHash'] : null;

        $result = [
            CODE => ERROR,
            MESSAGE => null,
            DATA => [
                'code' => !empty($params['vnp_TxnRef']) ? $params['vnp_TxnRef'] : null
            ]
        ];

        if($secure_hash != $secure_hash_from_vnp){
            $result[MESSAGE] = $response_code . ': ' . __d('template', 'chu_ky_khong_hop_le');
            return $utilities->getResponse($result);
        }

        if($response_code != '00'){
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

        $hash_secret = !empty($this->config['vnp_HashSecret']) ? $this->config['vnp_HashSecret'] : null;

        $input_data = [];
        foreach ($data as $key => $value) {
            if (substr($key, 0, 4) == 'vnp_') {
                $input_data[$key] = $value;
            }
        }

        if(empty($input_data)){
            return $utilities->getResponse([
                MESSAGE => __d('template', 'khong_lay_duoc_du_lieu_tra_ve_tu_webhooks')
            ]);
        }

        $payment_code = !empty($input_data['vnp_TxnRef']) ? $input_data['vnp_TxnRef'] : null;
        if(empty($payment_code)){
            return $utilities->getResponse([
                MESSAGE => __d('template', 'khong_lay_duoc_ma_giao_dich')
            ]);
        }

        $response_code = isset($input_data['vnp_ResponseCode']) ? $input_data['vnp_ResponseCode'] : null;

        $result = [
            CODE => ERROR,
            MESSAGE => __d('template', 'giao_dich_khong_thanh_cong'),
            DATA => [
                'code' => $payment_code,
                'payment_transaction_no' => !empty($input_data['vnp_TransactionNo']) ? $input_data['vnp_TransactionNo'] : null,
                'payment_gateway_response' => json_encode($input_data)
            ]
        ];

        $vnp_secure_hash = !empty($input_data['vnp_SecureHash']) ? $input_data['vnp_SecureHash'] : null;
        unset($input_data['vnp_SecureHashType']);
        unset($input_data['vnp_SecureHash']);
        ksort($input_data);
        $i = 0;

        $hash_data = '';
        foreach ($input_data as $key => $value) {
            if ($i == 1) {
                $hash_data = $hash_data . '&' . urlencode($key) . '=' . urlencode($value);
            }else {
                $hash_data = $hash_data . urlencode($key) . '=' . urlencode($value);
                $i = 1;
            }
        }

        $secure_hash_from_vnp = hash_hmac('sha512', $hash_data, $hash_secret);

        //Kiểm tra checksum của dữ liệu
        if ($secure_hash_from_vnp != $vnp_secure_hash) {
            $result['result_for_gatewave'] = [
                'RspCode' => '97',
                'Message' => 'Invalid Checksum'
            ];
            $result[MESSAGE] = __d('template', 'chu_ky_khong_hop_le');
            return $utilities->getResponse($result);
        }

        $table = TableRegistry::get('Payments');

        // check status payment
        $payment_info = $table->findByCode($payment_code)->first();
        if (empty($payment_info)) {
            $result['result_for_gatewave'] = [
                'RspCode' => '01',
                'Message' => 'Order Not Found'
            ];

            $result[MESSAGE] = __d('template', 'khong_tim_thay_thong_tin_giao_dich');
            return $utilities->getResponse($result);
        }

        $net_amount = !empty($input_data['vnp_Amount']) ? floatval($input_data['vnp_Amount']) / 100 : 0;
        $amount = !empty($payment_info['amount']) ? floatval($payment_info['amount']) : 0;

        if($amount != $net_amount){
            $result['result_for_gatewave'] = [
                'RspCode' => '04',
                'Message' => 'Invalid amount'
            ];

            $result[MESSAGE] = __d('template', 'gia_tri_giao_dich_khong_chinh_xac');
            return $utilities->getResponse($result);
        }


        $status_payment = isset($payment_info['status']) ? $payment_info['status'] : null;
        if($status_payment == 1) {
            $result['result_for_gatewave'] = [
                'RspCode' => '02',
                'Message' => 'Order already confirmed'
            ];

            $result[MESSAGE] = __d('template', 'giao_dich_nay_da_thanh_cong');
            return $utilities->getResponse($result);
        }


        if ($response_code == '00') {
            $result['result_for_gatewave'] = [
                'RspCode' => '00',
                'Message' => 'Confirm Success'
            ];
            $result[CODE] = SUCCESS;
            $result[MESSAGE] = __d('template', 'giao_dich_thanh_cong');
        }

        if($response_code == '99'){
            $result['result_for_gatewave'] = [
                'RspCode' => '00',
                'Message' => 'Confirm Success'
            ];

            
            $result[MESSAGE] = __d('template', 'giao_dich_khong_thanh_cong');
        }

        return $utilities->getResponse($result);
    }

}

?>