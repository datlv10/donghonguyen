<?php

namespace App\Lib\Payment;

use Cake\ORM\TableRegistry;
use App\Lib\Payment\PaymentUtilities;
use Cake\Core\Exception\Exception;
use Cake\Http\Client;
use App\Lib\Payment\NhPayment;

class AzPay
{
    protected $payment_url = '';
    protected $config = [];

    public function __construct($params)
    {
        $this->config = TableRegistry::get('PaymentsGateway')->getConfig(AZPAY);      

        $mode = !empty($this->config['transaction_server']) ? $this->config['transaction_server'] : null;
        if ($mode == LIVE) {
            $this->payment_url = 'https://secure.azpay.vn/web/';
        } else {
            $this->payment_url = 'https://secure.azpay.vn/web/';
        }        
    }

    public function sendToGateway($params = []) 
    {
        $partner_email = !empty($this->config['partner_email']) ? $this->config['partner_email'] : null;
        $partner_id = !empty($this->config['partner_id']) ? $this->config['partner_id'] : null;

        $sub_method = !empty($params['sub_method']) ? $params['sub_method'] : null;
        $ord_id = !empty($params['bill_id']) ? intval($params['bill_id']) : '';
        $ord_name = !empty($params['bill_code']) ? $params['bill_code'] : '';
        $trans_id = !empty($params['trans_id']) ? $params['trans_id'] : '';
        $ord_total = !empty($params['amount']) ? intval($params['amount']) : 0;
        $ord_cus_name = !empty($params['full_name']) ? $params['full_name'] : '';
        $ord_cus_email = !empty($params['email']) ? $params['email'] : '';
        $ord_cus_phone = !empty($params['phone']) ? $params['phone'] : '';
        $ord_cus_address = !empty($params['address']) ? $params['address'] : '';
        
        $return_url = !empty($params['redirect_url']) ? $params['redirect_url'] : '';
        $ipn_url = !empty($params['ipn_url']) ? $params['ipn_url'] : '';

        $input_data = [
            'partner_email' => $partner_email,
            'partner_id' => $partner_id,
            'payment_method' => $sub_method,
            'ord_id' => $ord_id,
            'ord_name' => $ord_name,
            'trans_id' => $trans_id,
            'ord_total' => $ord_total,
            'ord_cus_name' => $ord_cus_name,
            'ord_cus_email' => $ord_cus_email,
            'ord_cus_phone' => $ord_cus_phone,
            'ord_cus_address' => $ord_cus_address,
            'ipn_url' => $ipn_url,
            'return_url' => $return_url
        ];

        $json_data = json_encode($input_data);
        $http = new Client();
        $response = $http->post($this->payment_url, $json_data, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Content-Length' => strlen($json_data)
            ],
            'type' => 'json'
        ]);

        $json_result = $response->getStringBody();
        $utilities = new PaymentUtilities();
        if(empty($json_result) || !$utilities->isJson($json_result)){
            return $utilities->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $result = json_decode($json_result, true);
        if(empty($result['status'])){
            $message = !empty($result[DATA]) ? $result[DATA] : __d('template', 'du_lieu_khong_hop_le');
            return $utilities->getResponse([MESSAGE => $message]);
        }

        $pay_url = !empty($result[DATA]) ? $result[DATA] : '';

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


        $result = [
            CODE => ERROR,
            MESSAGE => null,
            DATA => [
                'code' => !empty($params['trans_id']) ? $params['trans_id'] : null
            ]
        ];

        if(empty($params['status']) || $params['status'] != SUCCESS){
            $result[MESSAGE] = !empty($params[MESSAGE]) ? $params[MESSAGE] : __d('template', 'giao_dich_khong_thanh_cong');
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
        if(empty($data) || !is_array($data)){
            return $utilities->getResponse([
                MESSAGE => __d('template', 'khong_lay_duoc_du_lieu_tra_ve_tu_webhooks')
            ]);
        }

        $payment_code = !empty($data['trans_id']) ? $data['trans_id'] : null;
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
                'payment_transaction_no' => null,
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

        $status_payment = isset($payment_info['status']) ? $payment_info['status'] : null;
        if($status_payment == 1) {
            $result[MESSAGE] = __d('template', 'giao_dich_nay_da_thanh_cong');
            return $utilities->getResponse($result);
        }

        $net_amount = !empty($data['amount']) ? floatval($data['amount']) : 0;
        $amount = !empty($payment_info['amount']) ? floatval($payment_info['amount']) : 0;
        if($amount != $net_amount){
            $result[MESSAGE] = __d('template', 'gia_tri_giao_dich_khong_chinh_xac');
            return $utilities->getResponse($result);
        }

        if(!empty($data['status']) && $data['status'] == SUCCESS){
            $result[CODE] = SUCCESS;
            $result[MESSAGE] = __d('template', 'giao_dich_thanh_cong');

            return $utilities->getResponse($result);
        }

        return $utilities->getResponse($result);        
    }

}

?>