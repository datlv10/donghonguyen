<?php

namespace App\Lib\Payment;

use Cake\ORM\TableRegistry;
use App\Lib\Payment\PaymentUtilities;
use Cake\Core\Exception\Exception;
use Cake\Http\Client;
use Cake\Log\Log;

class Paypal
{
    protected $config = [];
    protected $payment_url = '';

    public function __construct($params = [])
    {
        $this->config = TableRegistry::get('PaymentsGateway')->getConfig(PAYPAL);

        $mode = !empty($this->config['transaction_server']) ? $this->config['transaction_server'] : null;
        if ($mode == LIVE) {
            $this->payment_url = 'https://api-m.paypal.com';
        } else {
            $this->payment_url = 'https://api-m.sandbox.paypal.com';
        }
    }

    private function getAccessToken()
    {
        $client_id = !empty($this->config['client_id']) ? $this->config['client_id'] : null;
        $secret_key = !empty($this->config['secret_key']) ? $this->config['secret_key'] : null;

        // get token
        $http = new Client();
        $response = $http->post($this->payment_url . '/v1/oauth2/token', 
            [
                'grant_type' => 'client_credentials'
            ]
            ,[
            'auth' => [
                'username' => $client_id,
                'password' => $secret_key
            ],
            'type' => 'json'
        ]);

        $json_result = $response->getStringBody();
        $utilities = new PaymentUtilities();
        if(empty($json_result) || !$utilities->isJson($json_result)) return null;
        $result_token = json_decode($json_result, true);
        return !empty($result_token['access_token']) ? $result_token['access_token'] : null;
    }

    public function sendToGateway($params = [])
    {
        $utilities = new PaymentUtilities();

        $access_token = $this->getAccessToken();
        if(empty($access_token)){
            return $utilities->getResponse([MESSAGE => __d('template', 'thong_tin_cau_hinh_chua_hop_le')]);
        }
        
        $payment_code = !empty($params['payment_code']) ? $params['payment_code'] : '';
        $bill_id = !empty($params['bill_id']) ? intval($params['bill_id']) : null;
        $bill_code = !empty($params['bill_code']) ? $params['bill_code'] : '';
        $redirect_url = !empty($params['redirect_url']) ? $params['redirect_url'] : '';
        $cancel_url = !empty($params['cancel_url']) ? $params['cancel_url'] : '';        
        $order_description = !empty($params['order_description']) ? $params['order_description'] : '';
        $amount = !empty($params['amount']) ? $params['amount'] : 0;
        if(empty($payment_code) || empty($bill_id)){
            return $utilities->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_giao_dich')]);
        }

        $amount = $utilities->transferToUsd($amount);
        $data = [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'items' => [
                        [
                            'name' => $order_description,
                            'quantity' => 1,
                            'unit_amount' => [
                                'currency_code' => 'USD',
                                'value' => $amount
                            ]
                        ]
                    ],
                    'amount' => [
                        'currency_code' => 'USD',
                        'value' => $amount,
                        'breakdown' => [
                            'item_total' => [
                                'currency_code' => 'USD',
                                'value' => $amount,
                            ]
                        ]
                    ],
                    'description' => $order_description,
                    'invoice_id' => $bill_code,
                    'reference_id' => $payment_code
                ]
            ],
            'application_context' => [
                'return_url' => $redirect_url,
                'cancel_url' => $cancel_url
            ]
        ];
        
        $http = new Client();
        $response = $http->post($this->payment_url . '/v2/checkout/orders', json_encode($data),[
            'headers' => [
                'Authorization' => 'Bearer ' . $access_token
            ],
            'type' => 'json'
        ]);
        $json_result = $response->getStringBody();        
        if(empty($json_result) || !$utilities->isJson($json_result)){
            return $utilities->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $result = json_decode($json_result, true);    
        $paypal_order_id = !empty($result['id']) ? $result['id'] : null;
        if(empty($paypal_order_id) || empty($result['links']) || !is_array($result['links'])){
            $message = !empty($result[MESSAGE]) ? $result[MESSAGE] : __d('template', 'du_lieu_khong_hop_le');            
            return $utilities->getResponse([MESSAGE => $message]);
        }

        $pay_url = null;
        foreach($result['links'] as $link){
            if(empty($link['rel']) || $link['rel'] != 'approve') continue;
            $pay_url = !empty($link['href']) ? $link['href'] : null;
        }

        if(empty($pay_url)){
            return $utilities->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_duong_dan_thanh_toan')]);
        }

        // cập nhật mã đơn hàng của paypal vào db để khi paypal trả về kết quả giao dịch sẽ lấy thông tin dựa theo mã này
        $table = TableRegistry::get('Payments');
        $payment_info = $table->find()->where(['code' => $payment_code])->select(['id', 'reference'])->first();
        if(empty($payment_info)){
            return $utilities->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_giao_dich')]);
        }

        $entity = $table->patchEntity($payment_info, [
            'reference' => $paypal_order_id
        ]);

        $update = $table->save($entity);
        if(empty($update->id)){
            throw new Exception(__d('template', 'loi_khi_cap_nhap_thong_tin_giao_dich'));
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

        
        $paypal_order_id = !empty($params['token']) ? $params['token'] : null;        
        $payer_id = !empty($params['PayerID']) ? $params['PayerID'] : null;
        if(empty($paypal_order_id)){
            return $utilities->getResponse([
                MESSAGE => __d('template', 'chu_ky_khong_hop_le')
            ]);
        }

        // lấy thông tin giao dịch
        $payment_info = TableRegistry::get('Payments')->find()->where([
            'payment_gateway_code' => PAYPAL,
            'reference' => $paypal_order_id
        ])->select(['id', 'code', 'amount', 'status'])->first();

        $payment_code = !empty($payment_info['code']) ? $payment_info['code'] : null;
        if(empty($payment_code)){
            return $utilities->getResponse([
                MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_giao_dich')
            ]);
        }

        $access_token = $this->getAccessToken();
        if(empty($access_token)){
            return $utilities->getResponse([MESSAGE => __d('template', 'thong_tin_cau_hinh_chua_hop_le')]);
        }

        // xác nhận thanh toán
        $http = new Client();
        $response = $http->post($this->payment_url . '/v2/checkout/orders/' . $paypal_order_id . '/capture', '', [
            'headers' => [
                'Authorization' => 'Bearer ' . $access_token,
                'Content-Type' => 'application/json',
                'Content-Length' => 0
            ],
            'type' => 'json'
        ]);

        $json_checkout = $response->getStringBody();

        if(empty($json_checkout) || !$utilities->isJson($json_checkout)){
            return $utilities->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_giao_dich')]);
        }

        $checkout = json_decode($json_checkout, true);        
        if(empty($checkout['id'])){
            $message = !empty($checkout[MESSAGE]) ? $checkout[MESSAGE] : __d('template', 'khong_lay_duoc_thong_tin_giao_dich');
            return $utilities->getResponse([MESSAGE => $message]);
        }

        if(empty($checkout['status']) || $checkout['status'] != 'COMPLETED'){
            $message = !empty($checkout[MESSAGE]) ? $checkout[MESSAGE] : __d('template', 'giao_dich_khong_thanh_cong');
            return $utilities->getResponse([MESSAGE => $message]);
        }

        $result = [
            CODE => ERROR,
            MESSAGE => null,
            DATA => [
                'code' => $payment_code
            ]
        ];

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

        $paypal_order_id = !empty($data['resource']['supplementary_data']['related_ids']['order_id']) ? $data['resource']['supplementary_data']['related_ids']['order_id'] : null;
        $event_type = !empty($data['event_type']) ? $data['event_type'] : null;
        $status = !empty($data['resource']['status']) ? $data['resource']['status'] : null;        
        if(empty($paypal_order_id) || $event_type != 'PAYMENT.CAPTURE.COMPLETED' || $status != 'COMPLETED'){
            return $utilities->getResponse([
                MESSAGE => __d('template', 'du_lieu_khong_hop_le')
            ]);
        }

        // lấy thông tin giao dịch
        $payment_info = TableRegistry::get('Payments')->find()->where([
            'payment_gateway_code' => PAYPAL,
            'reference' => $paypal_order_id
        ])->select(['id', 'code', 'amount', 'status'])->first();
        $payment_code = !empty($payment_info['code']) ? $payment_info['code'] : null;
        if(empty($payment_code)){
            return $utilities->getResponse([
                MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_giao_dich')
            ]);
        }

        $result = [
            CODE => ERROR,
            MESSAGE => __d('template', 'giao_dich_khong_thanh_cong'),
            DATA => [
                'code' => $payment_code,
                'payment_transaction_no' => $paypal_order_id,
                'payment_gateway_response' => json_encode($data)
            ]
        ];

        $status_payment = isset($payment_info['status']) ? $payment_info['status'] : '';
        if($status_payment == 1) {
            $result[MESSAGE] = __d('template', 'giao_dich_nay_da_thanh_cong');
            return $utilities->getResponse($result);
        }

        $result[CODE] = SUCCESS;
        $result[MESSAGE] = __d('template', 'giao_dich_thanh_cong');

        return $utilities->getResponse($result);
    }
}

?>