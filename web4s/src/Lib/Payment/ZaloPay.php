<?php

namespace App\Lib\Payment;

use Cake\ORM\TableRegistry;
use App\Lib\Payment\PaymentUtilities;
use Cake\Core\Exception\Exception;
use Cake\Http\Client;
use Cake\Log\Log;

class ZaloPay
{
    protected $config = [];
    protected $payment_url = '';

    public function __construct($params = [])
    {
        $this->config = TableRegistry::get('PaymentsGateway')->getConfig(ZALOPAY);

        $mode = !empty($this->config['transaction_server']) ? $this->config['transaction_server'] : null;
        if ($mode == LIVE) {
            $this->payment_url = 'https://openapi.zalopay.vn/v2/create';
        } else {
            $this->payment_url = 'https://sb-openapi.zalopay.vn/v2/create';
        }
    }

    public function sendToGateway($params = []) 
    {
        $app_id = !empty($this->config['app_id']) ? $this->config['app_id'] : null;
        $app_user = !empty($this->config['app_user']) ? $this->config['app_user'] : 'demo';
        $key_1 = !empty($this->config['key_1']) ? $this->config['key_1'] : null;
        $key_2 = !empty($this->config['key_2']) ? $this->config['key_2'] : null;
        $bank_code = !empty($this->config['bank_code']) ? $this->config['bank_code'] : null;

        $payment_code = !empty($params['payment_code']) ? $params['payment_code'] : '';
        $order_description = !empty($params['order_description']) ? $params['order_description'] : '';
        $bill_code = !empty($params['bill_code']) ? $params['bill_code'] : '';
        $redirect_url = !empty($params['redirect_url']) ? $params['redirect_url'] : '';
        $amount = !empty($params['amount']) ? intval($params['amount']) : 0;
        $items = [];

        $embed_data = [
            "redirecturl"=> $redirect_url
        ];

        if ($bank_code == 'ATM') {
            $embed_data['bankgroup'] = 'ATM';
            $bank_code = '';
        }

        $order = [
            "app_id" => $app_id,
            "app_time" => round(microtime(true) * 1000), // miliseconds
            "app_trans_id" => date("ymd") . "_" . $payment_code, // translation missing: vi.docs.shared.sample_code.comments.app_trans_id
            "app_user" => $app_user,
            "item" => "[]",
            "embed_data" =>  json_encode($embed_data, JSON_UNESCAPED_UNICODE),
            "amount" => $amount,
            "description" => $order_description,
            "bank_code" => $bank_code
        ];

        // appid|app_trans_id|appuser|amount|apptime|embeddata|item
        $data = $order["app_id"] . "|" . $order["app_trans_id"] . "|" . $order["app_user"] . "|" . $order["amount"]
            . "|" . $order["app_time"] . "|" . $order["embed_data"] . "|" . $order["item"];
        $order["mac"] = hash_hmac("sha256", $data, $key_1);

        $context = stream_context_create([
            "http" => [
                "header" => "Content-type: application/x-www-form-urlencoded\r\n",
                "method" => "POST",
                "content" => http_build_query($order)
            ]
        ]);

        $response = file_get_contents($this->payment_url, false, $context);
        $result = json_decode($response, true);   

        $pay_url = !empty($result['order_url']) ? $result['order_url'] : '';

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

        $payment_code = !empty($data['app_trans_id']) ? explode('_', $data['app_trans_id']) : null;
        $payment_code = !empty($payment_code[1]) ? $payment_code[1] : null;

        $result = [
            CODE => ERROR,
            MESSAGE => null,
            DATA => [
                'code' => !empty($payment_code) ? $payment_code : null
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

        $payment_code = !empty($data['app_trans_id']) ? explode('_', $data['app_trans_id']) : null;
        $payment_code = !empty($payment_code[1]) ? $payment_code[1] : null;
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