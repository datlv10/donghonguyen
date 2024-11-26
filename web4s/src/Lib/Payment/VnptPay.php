<?php

namespace App\Lib\Payment;

use Cake\ORM\TableRegistry;
use App\Lib\Payment\PaymentUtilities;
use Cake\Core\Exception\Exception;

class VnptPay
{
    protected $payment_url = '';
    protected $config = [];

    public function __construct($params)
    {
        $this->config = TableRegistry::get('PaymentsGateway')->getConfig(VNPTPAY);

        $mode = !empty($this->config['transaction_server']) ? $this->config['transaction_server'] : null;
        if ($mode == LIVE) {
            $this->payment_url = 'https://pg.megapay.vn';            
        } else {
            $this->payment_url = 'https://sandbox.megapay.vn:2810';
        }
    }

    public function sendToGateway($params = []) 
    {
        $utilities = new PaymentUtilities();
        $merchant_id = !empty($this->config['merchant_id']) ? $this->config['merchant_id'] : null;
        $encode_key = !empty($this->config['encode_key']) ? $this->config['encode_key'] : null;

        $time_stamp = date('YmdHis');
        $mer_trx_id = 'MERTRXID' . $time_stamp . '_' . rand(100, 10000);
        $bill_id = !empty($params['bill_id']) ? intval($params['bill_id']) : '';
        $payment_code = !empty($params['payment_code']) ? $params['payment_code'] : '';
        $amount = !empty($params['amount']) ? floatval($params['amount']) : 0;
        $order_info = !empty($params['order_info']) ? $params['order_info'] : '';

        $plain_txt_token = $time_stamp . $mer_trx_id . $merchant_id . $amount . $encode_key;
        $token = hash('sha256', $plain_txt_token);

        $ipn_url = !empty($params['ipn_url']) ? $params['ipn_url'] : '';
        $redirect_url = !empty($params['redirect_url']) ? $params['redirect_url'] : '';
        $referer_url = !empty($params['referer_url']) ? $params['referer_url'] : '';

        $pay_url = '/order/payment-process/vnpt-pay?' . http_build_query([
            'time_stamp' => $time_stamp,
            'mer_trx_id' => $mer_trx_id,
            'bill_id' => $bill_id,
            'payment_code' => $payment_code,
            'amount' => $amount,
            'order_info' => $order_info,
            'token' => $token,
            'ipn_url' => $ipn_url,
            'redirect_url' => $redirect_url,
            'referer_url' => $referer_url,
        ]);

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

        $merchant_id = !empty($this->config['merchant_id']) ? $this->config['merchant_id'] : null;
        $encode_key = !empty($this->config['encode_key']) ? $this->config['encode_key'] : null;

        $result_cd = !empty($params['resultCd']) ? $params['resultCd'] : null;
        $time_stamp = !empty($params['timeStamp']) ? $params['timeStamp'] : null;
        $mer_trx_id = !empty($params['merTrxId']) ? $params['merTrxId'] : null;
        $trx_id = !empty($params['trxId']) ? $params['trxId'] : null;
        $amount = !empty($params['amount']) ? $params['amount'] : null;
        $token = !empty($params['payToken']) ? $params['payToken'] : null;
        $payment_code = !empty($params['invoiceNo']) ? $params['invoiceNo'] : null;

        $plain_txt_token = $result_cd . $time_stamp . $mer_trx_id . $trx_id . $merchant_id . $amount . $token . $encode_key;
        $token = hash('sha256', $plain_txt_token);
        
        $token_response = !empty($params['merchantToken']) ? $params['merchantToken'] : null;
        
        $result = [
            CODE => ERROR,
            MESSAGE => null,
            DATA => [
                'code' => $payment_code
            ]
        ];

        if ($token != $token_response) {
            return $utilities->getResponse([
                MESSAGE => __d('template', 'chu_ky_khong_hop_le')
            ]);
        }

        if($result_cd != '00_000'){
            $result[MESSAGE] = __d('template', 'giao_dich_khong_thanh_cong');
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

        $merchant_id = !empty($this->config['merchant_id']) ? $this->config['merchant_id'] : null;
        $encode_key = !empty($this->config['encode_key']) ? $this->config['encode_key'] : null;

        $result_cd = !empty($data['resultCd']) ? $data['resultCd'] : null;
        $time_stamp = !empty($data['timeStamp']) ? $data['timeStamp'] : null;
        $mer_trx_id = !empty($data['merTrxId']) ? $data['merTrxId'] : null;
        $trx_id = !empty($data['trxId']) ? $data['trxId'] : null;
        $amount = !empty($data['amount']) ? floatval($data['amount']) : null;
        $token = !empty($data['payToken']) ? $data['payToken'] : null;
        $payment_code = !empty($data['invoiceNo']) ? $data['invoiceNo'] : null;


        $plain_txt_token = $result_cd . $time_stamp . $mer_trx_id . $trx_id . $merchant_id . $amount . $token . $encode_key;
        $token = hash('sha256', $plain_txt_token);
        
        $token_response = !empty($data['merchantToken']) ? $data['merchantToken'] : null;

        $result = [
            CODE => ERROR,
            MESSAGE => __d('template', 'giao_dich_khong_thanh_cong'),
            DATA => [
                'code' => $payment_code,
                'payment_transaction_no' => $mer_trx_id,
                'payment_gateway_response' => json_encode($data)
            ]
        ];

        if($token != $token_response){
            return $utilities->getResponse([
                MESSAGE => __d('template', 'chu_ky_khong_hop_le')
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

        $amount_payment = !empty($payment_info['amount']) ? floatval($payment_info['amount']) : 0;

        if($amount != $amount_payment){
            $result[MESSAGE] = __d('template', 'gia_tri_giao_dich_khong_chinh_xac');
            return $utilities->getResponse($result);
        }

        if($result_cd != '00_000') return $utilities->getResponse($result);

        $result[CODE] = SUCCESS;
        $result[MESSAGE] = __d('template', 'giao_dich_thanh_cong');
        return $utilities->getResponse($result);
        
    }

}

?>