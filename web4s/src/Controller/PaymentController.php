<?php

namespace App\Controller;

use Cake\Event\EventInterface;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;
use App\Lib\Payment\NhPayment;
use Cake\Log\Log;

class PaymentController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
        $this->get_structure_layout = false;
    }

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
    }

    // function returnPayment chỉ cập nhật trạng thái giao dịch bị lỗi
    // giao dịch thành công được cập nhật ở webhooks
    public function returnPayment($gateway_code = null, $bill_code = null)
    {
        if ($this->request->is('post')) {
            $params = $this->request->getData();
        } else {
            $params = $this->request->getQueryParams();
        }        

        $table = TableRegistry::get('Payments');
        $payment_gateway = TableRegistry::get('PaymentsGateway')->getList(LANGUAGE);

        // kiểm tra mã cổng thanh toán
        if(empty($payment_gateway[$gateway_code])){            
            $message = __d('template', 'khong_lay_duoc_thong_tin_cong_thanh_toan');
            $url = !empty($bill_code) ? "/order/checkout?code=$bill_code&message=" . urlencode($message) : '/';
            return $this->redirect($url);
        }

        // xử lý kết quả giao dịch từ cổng thanh toán trả về
        $nh_payment = new NhPayment($gateway_code);
        $result = $nh_payment->returnResult($params);
        $payment_code = !empty($result[DATA]['code']) ? $result[DATA]['code'] : null;    
        if(empty($payment_code)){
            $message = !empty($result[MESSAGE]) ? $result[MESSAGE] : __d('template', 'khong_tim_thay_thong_tin_giao_dich');
            $url = !empty($bill_code) ? "/order/checkout?code=$bill_code&message=" . urlencode($message) : '/';            
            return $this->redirect($url);
        }
        
        // lấy thông tin giao dịch
        $payment_info = $table->find()->where([
            'code' => $payment_code
        ])->select([
            'id', 'foreign_id', 'foreign_type', 'status'
        ])->first();
        $status_payment = !empty($payment_info['status']) ? intval($payment_info['status']) : 0;

        if(empty($payment_info)){
            $message = !empty($result[MESSAGE]) ? $result[MESSAGE] : __d('template', 'khong_tim_thay_thong_tin_giao_dich');
            $url = !empty($bill_code) ? "/order/checkout?code=$bill_code&message=" . urlencode($message) : '/';            
            return $this->redirect($url);
        }
        
        $url_redirect = '/';
        $send_email = true;
        $foreign_type = !empty($payment_info['foreign_type']) ? $payment_info['foreign_type'] : null;

        switch($foreign_type){
            case ORDER:
                $order_code = null;
                $order_id = !empty($payment_info['foreign_id']) ? intval($payment_info['foreign_id']) : null;
                if(!empty($order_id)){
                    $order_info = TableRegistry::get('Orders')->find()->contain(['OrdersContact'])->where([
                        'Orders.id' => $order_id,
                        'Orders.deleted' => 0
                    ])->select(['Orders.id', 'Orders.code', 'OrdersContact.email'])->first();

                    $order_code = !empty($order_info['code']) ? $order_info['code'] : null;
                }
                
                $url_redirect = '/order/success?code=' . $order_code;
                if(!empty($result[CODE]) && $result[CODE] == ERROR) {
                    $message = !empty($result[MESSAGE]) ? $result[MESSAGE] : __d('template', 'thanh_toan_khong_thanh_cong');
                    $url_redirect = '/order/checkout?code='. $order_code .'&message=' . urlencode($message);
                    $send_email = false;
                }

                $email_contact = !empty($order_info['OrdersContact']['email']) ? $order_info['OrdersContact']['email'] : null;
                if($send_email && !empty($email_contact)){
                    $params_email = [
                        'to_email' => $email_contact,
                        'code' => 'ORDER',
                        'id_record' => !empty($order_info['id']) ? $order_info['id'] : null
                    ];

                    $this->loadComponent('Email')->send($params_email);
                }

            break;

            case POINT:
                $point_history_code = null;
                $point_history_id = !empty($payment_info['foreign_id']) ? intval($payment_info['foreign_id']) : null;
                if(!empty($point_history_id)){
                    $point_history_info = TableRegistry::get('CustomersPointHistory')->find()->where([
                        'id' => $point_history_id
                    ])->select(['id', 'code'])->first();

                    $point_history_code = !empty($point_history_info['code']) ? $point_history_info['code'] : null;
                }
                
                $url_redirect = '/member/wallet/buy-point-success?code=' . $point_history_code; 
                if(!empty($result[CODE]) && $result[CODE] == ERROR) {
                    $message = !empty($result[MESSAGE]) ? $result[MESSAGE] : __d('template', 'thanh_toan_khong_thanh_cong');
                    $url_redirect = '/member/wallet/buy-point?message=' . urlencode($message);
                    $send_email = false;
                }
            break;
        }

        // cập nhật trạng thái giao dịch không thành công và trạng thái chờ xác nhận (status == 2)
        if(!empty($result[CODE]) && $result[CODE] == ERROR && $status_payment == 2) {           
            try {
                $entity = $table->patchEntity($payment_info, [
                    'status' => 0,
                    'updated' => strtotime(date('Y-m-d H:i:s'))
                ]);
                
                $update_payment = $table->save($entity);
                if(empty($update_payment->id)){
                    throw new Exception(__d('template', 'loi_khi_cap_nhap_thong_tin_giao_dich'));
                }
            } catch (Exception $e) {
                $this->responseJson([
                    MESSAGE => $e->getMessage()
                ]);
            }
        }

        // chuyển hướng url
        if(!empty($url_redirect)){
            return $this->redirect($url_redirect);
        }

        // nếu không chuyển hướng thì thông báo lỗi
        $message = !empty($result[MESSAGE]) ? $result[MESSAGE] : __d('template', 'giao_dich_khong_thanh_cong');
        $url = !empty($bill_code) ? "/order/checkout?code=$bill_code&message=" . urlencode($message) : '/';            
        return $this->redirect($url);       
    }

    // nhận kết quả giao dịch và chỉ cập nhật với giao dịch thành công
    public function webhooks($gateway_code = null)
    {
        $this->layout = false;
        $this->autoRender = false;

        if (empty($gateway_code)) {
            return false;
        }
        
        $nh_payment = new NhPayment($gateway_code);
        $payment_gateway = TableRegistry::get('PaymentsGateway')->getList(LANGUAGE);
        if(empty($payment_gateway[$gateway_code])){
            $this->responseJson([
                MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_cong_thanh_toan')
            ]);
        }

        $params = ['data' => $this->request->getQueryParams()];

        if($this->request->is(['post','put'])){
            $data = file_get_contents('php://input');

            if($this->loadComponent('Utilities')->isJson($data)){
                $data = json_decode($data, true);
            }            
            
            if(!is_array($data)){
                $this->responseJson([
                    MESSAGE => __d('template', 'du_lieu_khong_hop_le')
                ]);
            }

            $params = ['data' => $data];
        }
   
        $result = $nh_payment->webhooks($params);

        $payment_code = !empty($result[DATA]['code']) ? $result[DATA]['code'] : null;
        $payment_gateway_response = !empty($result[DATA]['payment_gateway_response']) ? $result[DATA]['payment_gateway_response'] : null;
        $payment_transaction_no = !empty($result[DATA]['payment_transaction_no']) ? $result[DATA]['payment_transaction_no'] : null;

        if(empty($payment_code)){
            $this->responseJson([
                MESSAGE => __d('template', 'khong_lay_duoc_ma_giao_dich')
            ]);
        }

        $table = TableRegistry::get('Payments');

        $payment_info = $table->find()->where(['code' => $payment_code])->first();
        if(empty($payment_info)){
            $this->responseJson([
                MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_giao_dich')
            ]);
        }

        if(empty($result[CODE]) || $result[CODE] != SUCCESS) {
            $payment_status = 0;

            try {
                $entity = $table->patchEntity($payment_info, [
                    'status' => $payment_status,
                    'reference' => $payment_transaction_no,
                    'payment_gateway_response' => $payment_gateway_response,
                    'updated' => strtotime(date('Y-m-d H:i:s'))
                ]);
                
                $update_payment = $table->save($entity);
                if(empty($update_payment->id)){
                    throw new Exception(__d('template', 'loi_khi_cap_nhap_thong_tin_giao_dich'));
                }
            } catch (Exception $e) {
                $this->responseJson([
                    MESSAGE => $e->getMessage()
                ]);
            }

            if(!empty($result['result_for_gatewave'])) die(json_encode($result['result_for_gatewave']));

            $this->responseJson([
                MESSAGE => !empty($result[MESSAGE]) ? $result[MESSAGE] : __d('template', 'giao_dich_khong_thanh_cong')
            ]);
        }

        $payment_status = 1;
        try {
            $entity = $table->patchEntity($payment_info, [
                'status' => $payment_status,
                'reference' => $payment_transaction_no,
                'payment_gateway_response' => $payment_gateway_response,
                'updated' => strtotime(date('Y-m-d H:i:s'))
            ]);
            $update_payment = $table->save($entity);
            if(empty($update_payment->id)){
                throw new Exception(__d('template', 'loi_khi_cap_nhap_thong_tin_giao_dich'));
            }

            $foreign_id = !empty($payment_info['foreign_id']) ? intval($payment_info['foreign_id']) : null;
            $foreign_type = !empty($payment_info['foreign_type']) ? $payment_info['foreign_type'] : null;
            if(!empty($foreign_id) && $foreign_type == ORDER){
                $table_order = TableRegistry::get('Orders');
                $update_order = $table_order->updateAfterPayment($foreign_id);
                if (empty($update_order)){
                    throw new Exception(__d('template', 'loi_khi_cap_nhap_thong_tin_giao_dich'));
                }

                // cập nhật trạng thái đơn hàng
                $order_info = $table_order->find()->where(['id' => $foreign_id])->select(['id', 'status'])->first();
                if(empty($order_info)){
                    throw new Exception(__d('template', 'khong_lay_duoc_thong_tin_don_hang'));
                }

                if(!empty($order_info['status']) && $order_info['status'] == DRAFT){
                    $entity_order = $table_order->patchEntity($order_info, [
                        'status' => NEW_ORDER
                    ]);

                    $update_order = $table_order->save($entity_order);
                    if (empty($update_order->id)){
                        throw new Exception(__d('template', 'khong_cap_nhat_duoc_thong_tin_don_hang'));
                    }

                    $update_quantity_available = $this->loadComponent('Admin.Order')->updateQuantityAvailableOfProduct($foreign_id);
                    if(!$update_quantity_available){
                        throw new Exception(__d('template', 'khong_cap_nhat_duoc_thong_tin_don_hang'));
                    }
                }

            }

            if(!empty($foreign_id) && $foreign_type == POINT){
                $update_point = $this->loadComponent('Admin.CustomersPoint')->updatePointAfterPayment($foreign_id);
                if (empty($update_point[CODE] || $update_point[CODE] != SUCCESS)){
                    throw new Exception(__d('template', 'loi_khi_cap_nhap_thong_tin_giao_dich'));
                }
            }


            if(!empty($result['result_for_gatewave'])) die(json_encode($result['result_for_gatewave']));
            
            $this->responseJson([
                CODE => SUCCESS,
                MESSAGE => __d('template', 'cap_nhat_thanh_cong')
            ]);

        } catch (Exception $e) {
            $this->responseJson([
                MESSAGE => $e->getMessage()
            ]);
        }        
    }

    public function vnptPayProcess()
    {
        $this->viewBuilder()->enableAutoLayout(false);

        $params = $this->request->getQueryParams();
        $bill_id = !empty($params['bill_id']) ? intval($params['bill_id']) : null;

        $order = TableRegistry::get('Orders')->getDetailOrder($bill_id, [
            'get_items' => true,
            'get_contact' => true
        ]);
        $order = TableRegistry::get('Orders')->formatDataOrderDetail($order, LANGUAGE);
        if(empty($order)) die(__d('template', 'du_lieu_khong_hop_le'));

        $product_name = !empty($order['items'][0]['name']) ? $order['items'][0]['name'] : null;
        $gateway_config = TableRegistry::get('PaymentsGateway')->getConfig(VNPTPAY);
        $mode = !empty($gateway_config['transaction_server']) ? $gateway_config['transaction_server'] : null;

        $payment_domain = 'https://sandbox.megapay.vn:2810';
        if (!empty($gateway_config['transaction_server']) && $gateway_config['transaction_server'] == LIVE) {
            $payment_domain = 'https://pg.megapay.vn';
        }

        $full_name = !empty($order['contact']['full_name']) ? $order['contact']['full_name'] : null;
        $first_name = $full_name;
        $last_name = " ";
        if(!empty($full_name)){
            $parts = explode(' ', $full_name);
            if(count($parts) > 1) {
                $last_name = array_pop($parts);
                $first_name = implode(' ', $parts);
            }
        }


        $this->set('order', $order);
        $this->set('product_name', $product_name);
        $this->set('first_name', $first_name);
        $this->set('last_name', $last_name);
        $this->set('params', $params);
        $this->set('gateway_config', $gateway_config);
        $this->set('payment_domain', $payment_domain);
    }

}