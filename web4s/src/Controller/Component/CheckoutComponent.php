<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;
use App\Lib\Payment\NhPayment;

class CheckoutComponent extends Component
{
	public $controller = null;
    public $components = ['System' ,'Utilities'];

    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->controller = $this->_registry->getController();
    }

    public function checkoutByGateway($payment_code = null, $options = [])
    {
        $api = !empty($options['api']) ? true : false;
        $type_os = !empty($options['type_os']) ? $options['type_os'] : null;
        if(empty($payment_code)){
            $this->System->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_giao_dich')]);
        }

        $payment_info = TableRegistry::get('Payments')->findByCode($payment_code)->first();
        $gateway_code = !empty($payment_info['payment_gateway_code']) ? $payment_info['payment_gateway_code'] : null;        
        if(empty($payment_info) || empty($gateway_code)){
            return $this->System->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_giao_dich')]);
        }

        $sub_method = !empty($payment_info['sub_method']) ? $payment_info['sub_method'] : null;

        $foreign_type = !empty($payment_info['foreign_type']) ? $payment_info['foreign_type'] : null;
        $status = !empty($payment_info['status']) ? $payment_info['status'] : null;
        if($status != 2){
            return $this->System->getResponse([MESSAGE => __d('template', 'giao_dich_da_duoc_thuc_hien')]);
        }

        $foreign_id = !empty($payment_info['foreign_id']) ? intval($payment_info['foreign_id']) : null;
        if(empty($foreign_id)){
            return $this->System->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_don_hang_can_thanh_toan')]);
        }

        $bill_id = $bill_code = $full_name = $email = $phone = $address = null;
        switch($foreign_type){
            case ORDER:
                $order_info = TableRegistry::get('Orders')->getDetailOrder($foreign_id, ['get_contact' => true]);
                $order_info = TableRegistry::get('Orders')->formatDataOrderDetail($order_info, LANGUAGE);
                if(empty($order_info['code'])){
                    return $this->System->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_don_hang_can_thanh_toan')]);
                }

                $bill_id = !empty($order_info['id']) ? intval($order_info['id']) : null;
                $bill_code = !empty($order_info['code']) ? $order_info['code'] : null;

                $full_name = !empty($order_info['contact']['full_name']) ? $order_info['contact']['full_name'] : null;
                $email = !empty($order_info['contact']['email']) ? $order_info['contact']['email'] : null;
                $phone = !empty($order_info['contact']['phone']) ? $order_info['contact']['phone'] : null;
                $address = !empty($order_info['contact']['address']) ? $order_info['contact']['address'] : null;

            break;

            case POINT:
                $point_history_info = TableRegistry::get('CustomersPointHistory')->find()->where(['id' => $foreign_id])->first();
                if(empty($point_history_info['code'])){
                    return $this->System->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_giao_dich')]);
                }

                $bill_id = !empty($point_history_info['id']) ? intval($point_history_info['id']) : null;
                $bill_code = !empty($point_history_info['code']) ? $point_history_info['code'] : null;

                $customer_id = !empty($point_history_info['customer_id']) ? intval($point_history_info['customer_id']) : null;
                $customer_info = TableRegistry::get('Customers')->getDetailCustomer($customer_id, ['get_default_address']);
                $customer_info = TableRegistry::get('Customers')->formatDataCustomerDetail($customer_info);

                $full_name = !empty($customer_info['full_name']) ? $customer_info['full_name'] : null;
                $email = !empty($customer_info['email']) ? $customer_info['email'] : null;
                $phone = !empty($customer_info['phone']) ? $customer_info['phone'] : null;
                $address = !empty($customer_info['address']) ? $customer_info['address'] : null;
            break;
        }        

        $gateway_params = [];
        
        $amount = floatval($payment_info['amount']) / CURRENCY_RATE;

        $request = $this->controller->getRequest();
        $ip_client = $request->clientIp();
        $base_url = $request->scheme() . '://' . $request->host();
        $redirect_url = $base_url . "/payment/return/$gateway_code/$bill_code";
        if($api){
            $redirect_url = $base_url . '/api/payment/return/' . $gateway_code;
        }
        $ipn_url = $base_url . '/payment/webhooks/' . $gateway_code;
        $cancel_url = $base_url . "/order/checkout?code=$bill_code&message=" . urlencode(__d('template', 'giao_dich_khong_thanh_cong'));        

        $order_description = 'Pay for bill '. $bill_code;
        if(LANGUAGE == 'vi'){
            $order_description = 'Thanh toan don hang '. $bill_code;
        }

        switch ($gateway_code) {
            case VNPAY:
                $gateway_params = [
                    'payment_code' => $payment_code,
                    'amount' => $amount,
                    'ip_client' => $ip_client,
                    'lang' => LANGUAGE,
                    'redirect_url' => $redirect_url,
                    'ipn_url' => $ipn_url,
                    'order_description' => $order_description
                ];
            break;

            case MOMO:
                $gateway_params = [
                    'payment_code' => $payment_code,
                    'amount' => $amount,
                    'redirect_url' => $redirect_url,
                    'ipn_url' => $ipn_url,
                    'lang' => LANGUAGE,
                    'order_description' => $order_description,
                    'type_os' => $type_os,
                    'bill_code' => $bill_code
                ];
            break;

            case ONEPAY:
            case ONEPAY_INSTALLMENT:
                $gateway_params = [
                    'payment_code' => $payment_code,
                    'amount' => $amount,
                    'ip_client' => $ip_client,
                    'lang' => LANGUAGE,
                    'redirect_url' => $redirect_url,
                    'referer_url' => !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null,
                    'order_description' => $order_description
                ];
            break;

            case AZPAY:
                $gateway_params = [
                    'sub_method' => $sub_method,
                    'bill_id' => $bill_id,
                    'bill_code' => $bill_code,
                    'trans_id' => $payment_code,
                    'full_name' => $full_name,
                    'email' => $email,
                    'phone' => $phone,
                    'address' => $address,
                    'amount' => $amount,
                    'lang' => LANGUAGE,
                    'redirect_url' => $redirect_url,
                    'ipn_url' => $ipn_url
                ];
            break;
            case BAOKIM:
                $gateway_params = [
                    'payment_code' => $payment_code,
                    'amount' => $amount,
                    'redirect_url' => $redirect_url,
                    'referer_url' => !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null,
                    'ipn_url' => $ipn_url,
                    'order_description' => $order_description
                ];
            break;

            case VNPTPAY:
                $gateway_params = [
                    'bill_id' => $bill_id,
                    'payment_code' => $payment_code,
                    'amount' => $amount,
                    'redirect_url' => $redirect_url,
                    'referer_url' => !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null,
                    'ipn_url' => $ipn_url,
                    'order_description' => $order_description
                ];
            break;

            case PAYPAL:
                $gateway_params = [
                    'payment_code' => $payment_code,
                    'amount' => $amount,
                    'redirect_url' => $redirect_url,
                    'cancel_url' => $cancel_url,
                    'order_description' => $order_description,
                    'bill_id' => $bill_id,
                    'bill_code' => $bill_code
                ];
            break;

            case ZALOPAY:
                $gateway_params = [
                    'payment_code' => $payment_code,
                    'amount' => $amount,
                    'redirect_url' => $redirect_url,
                    'cancel_url' => $cancel_url,
                    'order_description' => $order_description,
                    'bill_id' => $bill_id,
                    'bill_code' => $bill_code
                ];
            break;

            case ALEPAY:
            break;
        }


        if(empty($gateway_params)){
            return $this->System->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_cong_thanh_toan')]);
        }

        $nh_payment = new NhPayment($gateway_code);
        $result = $nh_payment->sendToGateway($gateway_params);
        if(empty($result[CODE]) || $result[CODE] == ERROR || empty($result[DATA]['pay_url'])){
            return $this->System->getResponse([
                MESSAGE => !empty($result[MESSAGE]) ? $result[MESSAGE] : __d('template', 'khong_lay_duoc_duong_dan_thanh_toan')
            ]);
        }

        return $this->System->getResponse([
            CODE => SUCCESS,
            MESSAGE => __d('template', 'xu_ly_thong_tin_giao_dich_thanh_cong'),
            DATA => [
                'url' => $result[DATA]['pay_url'],
                'app_pay_url' => !empty($result[DATA]['app_pay_url']) ? $result[DATA]['app_pay_url'] : null
            ]
        ]);
    }





}
