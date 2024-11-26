<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;
use Cake\Core\Exception\Exception;
use Cake\Utility\Security;

class MemberWalletComponent extends Component
{
	public $controller = null;
    public $components = ['System', 'Utilities', 'ReCaptcha', 'Email', 'Admin.CustomersPoint', 'Admin.Payment', 'Checkout'];

    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->controller = $this->_registry->getController();
    }

    public function givePoint($data = [], $options = [])
    {
        $api = !empty($options['api']) ? true : false;

        if (!$this->controller->getRequest()->is('post') || empty($data)) {
            return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $customer_code = !empty($data['customer_code']) ? trim($data['customer_code']) : null;
        $point = !empty($data['point']) ? intval(str_replace(',', '', $data['point'])) : null;
        $verify_code = !empty($data['code']) ? trim($data['code']) : null;
        $type_verify = !empty($data['type_verify']) ? $data['type_verify'] : null;

        if(empty($verify_code)) {
            return $this->System->getResponse([MESSAGE => __d('template', 'vui_long_nhap_ma_xac_nhan')]);
        }

        if(empty($type_verify) || !in_array($type_verify, ['phone', 'email'])) {
            return $this->System->getResponse([MESSAGE => __d('template', 'vui_long_nhap_ma_xac_nhan')]);
        }
        
        if(empty($customer_code)) {
            return $this->System->getResponse([MESSAGE => __d('template', 'vui_long_nhap_ma_khach_hang')]);
        }

        if(empty($point)) {
            return $this->System->getResponse([MESSAGE => __d('template', 'vui_long_nhap_so_diem')]);
        }

        // thông tin người nhận
        $receiver_info = TableRegistry::get('Customers')->find()->where(['code' => $customer_code, 'deleted' => 0])->select(['id'])->first();
        $receiver_id = !empty($receiver_info['id']) ? intval($receiver_info['id']) : null;
        if(empty($receiver_id)){
            return $this->System->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_nguoi_nhan')]);
        }

        $member = $this->controller->getRequest()->getSession()->read(MEMBER);
        $customer_id = !empty($member['customer_id']) ? intval($member['customer_id']) : null;        
        if(empty($customer_id)){
            return $this->System->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_khach_hang')]);
        }

        if($receiver_id == $customer_id){
            return $this->System->getResponse([MESSAGE => __d('template', 'ma_khach_hang_khong_hop_le')]);
        }

        // check recaptcha
        if(!$api){
            $token = !empty($data[TOKEN_RECAPTCHA]) ? $data[TOKEN_RECAPTCHA] : null;
            $check_recaptcha = $this->ReCaptcha->check($token);
            if($check_recaptcha[CODE] != SUCCESS){
                return $this->System->getResponse([MESSAGE => $check_recaptcha[MESSAGE]]);
            }
        }

        $point_info = TableRegistry::get('CustomersPoint')->getInfoCustomerPoint($customer_id);
        $wallet_point = !empty($point_info['point']) ? intval($point_info['point']) : 0;
        if($point > $wallet_point){
            return $this->System->getResponse([MESSAGE => __d('template', 'so_diem_trong_vi_khong_du_de_su_dung')]);
        }

        // xác minh mã hợp lệ
        $customers_table = TableRegistry::get('Customers');
        $customer_info = $customers_table->find()->where([
            'id' => $customer_id
        ])->select(['id', 'email', 'phone'])->first();
        if(empty($customer_info)) {
            return $this->System->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_tai_khoan')]);
        }

        $email = !empty($customer_info['email']) ? trim($customer_info['email']) : null;
        $phone = !empty($customer_info['phone']) ? trim($customer_info['phone']) : null;
        $table_email_token = TableRegistry::get('EmailToken');
        $where = [
            'code' => $verify_code,
            'type' => GIVE_POINT,
            'status' => 0,
            'end_time >=' => time()
        ];

        if($type_verify == 'email'){
            $where['email'] = $email;
        }

        if($type_verify == 'phone'){
            $where['phone'] = $phone;
        }

        $email_token_info = $table_email_token->find()->where($where)->first();

        if(empty($email_token_info)) {
            return $this->System->getResponse([MESSAGE => __d('template', 'thong_tin_khong_chinh_xac_hoac_ma_xac_nhan_da_het_han')]);
        }
        $email_token = $table_email_token->patchEntity($email_token_info, ['status' => 1]);

        $table = TableRegistry::get('CustomersPointHistory');
        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();
            
            // trừ điểm của người chuyển
            $minus_point = $this->CustomersPoint->saveCustomerPointHistory([
                'customer_id' => $customer_id,
                'point_type' => 1, // 1-> điểm ví
                'action' => 0, // 0 -> trừ
                'point' => $point,
                'action_type' => GIVE_POINT,
                'customer_related_id' => $receiver_id
            ]);

            if(empty($minus_point[CODE]) || $minus_point[CODE] != SUCCESS){
                $message = !empty($minus_point[MESSAGE]) ? $minus_point[MESSAGE] : null;
                throw new Exception($message);
            }

            // cộng điểm cho người nhận
            $plus_point = $this->CustomersPoint->saveCustomerPointHistory([
                'customer_id' => $receiver_id,
                'point_type' => 1, // 1-> điểm ví
                'action' => 1, // 1 -> cộng
                'point' => $point,
                'action_type' => GIVE_POINT,
                'customer_related_id' => $customer_id
            ]);

            if(empty($plus_point[CODE]) || $plus_point[CODE] != SUCCESS){
                $message = !empty($plus_point[MESSAGE]) ? $plus_point[MESSAGE] : null;
                throw new Exception($message);
            }

            $save_email_token = $table_email_token->save($email_token);
            if (empty($save_email_token->id)){
                throw new Exception();
            }

            $conn->commit();

            return $this->System->getResponse([
                CODE => SUCCESS, 
                MESSAGE => __d('template', 'giao_dich_thanh_cong')
            ]);

        }catch (Exception $e) {
            $conn->rollback();

            return $this->System->getResponse([
                MESSAGE => !empty($e->getMessage()) ? $e->getMessage() : __d('template', 'giao_dich_khong_thanh_cong')
            ]);  
        }
        
        if($result[CODE] == SUCCESS){
            $result[DATA] = $table->formatDataCustomerDetail($result[DATA]);

            // reset session
            $customer_info = $table->getDetailCustomer($customer_id, [
                'get_account' => true,
                'get_default_address' => true
            ]);    

            $customer_info = $table->formatDataCustomerDetail($customer_info);
            
            $session = $this->controller->getRequest()->getSession();
            $session->write(MEMBER, $customer_info);
        }

        return $result;
    }

    public function buyPoint($data = [], $options = [])
    {
        if(!$this->controller->getRequest()->is('post') || empty($data)){
           return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $point = !empty($data['point']) ? intval($data['point']) : null;
        if(empty($point)){
            return $this->System->getResponse([MESSAGE => __d('template', 'vui_long_nhap_so_diem')]);
        }

        $payment_gateway = !empty($data['payment_gateway']) ? $data['payment_gateway'] : null;
        if(empty($payment_gateway)){
            return $this->System->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_cong_thanh_toan')]);
        }

        $member = $this->controller->getRequest()->getSession()->read(MEMBER);
        $customer_id = !empty($member['customer_id']) ? intval($member['customer_id']) : null;
        if(empty($customer_id)){
            return $this->System->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_khach_hang')]);
        }

        // tạo 1 lịch sử giao dịch
        $point_history = $this->CustomersPoint->saveCustomerPointHistory([
            'customer_id' => $customer_id,
            'point_type' => 1, // 1-> điểm ví
            'action' => 1, // 1 -> cộng
            'point' => $point,
            'action_type' => BUY_POINT, // nạp điểm
            'status' => 2 // 2-> chờ duyệt (sau khi giao dịch qua cổng thanh toán thành công thì thực hiện cập nhật trạng thái lịch sử giao dịch qua IPN)
        ]);

        $point_history_id = !empty($point_history[DATA]['id']) ? intval($point_history[DATA]['id']) : null;
        if(empty($point_history[CODE]) || $point_history[CODE] != SUCCESS){
            return $this->System->getResponse([MESSAGE => __d('template', 'khoi_tao_giao_dich_khong_thanh_cong')]);
        }

        $settings = TableRegistry::get('Settings')->getSettingWebsite();
        $point_setting = !empty($settings['point']) ? $settings['point'] : [];

        $point_to_money = !empty($point_setting['point_to_money']) ? intval($point_setting['point_to_money']) : null;
        if(empty($point_to_money)){
            return $this->System->getResponse([MESSAGE => __d('template', 'cau_hinh_quy_doi_diem_khong_hop_le')]);
        }

        $amount = $point_to_money * $point;

        // kiểm tra riêng với cổng azpay
        $sub_method = null;
        if(strpos($payment_gateway, AZPAY) > -1){
            $split = explode('_', $payment_gateway);
            $sub_method = !empty($split[1]) ? $split[1] : null;
            $payment_gateway = !empty($split[0]) ? $split[0] : null;
        }

        $data_payment = [
            'foreign_id' => $point_history_id,
            'foreign_type' => POINT,
            'type' => 1, // 0 => CHI, 1 => THU
            'object_type' => CUSTOMER,
            'payment_method' => BANK,
            'payment_gateway_code' => $payment_gateway,
            'sub_method' => $sub_method,
            'object_id' => $customer_id,
            'amount' => $amount,
            'full_name' => !empty($member['full_name']) ? $member['full_name'] : null,
            'status' => 2
        ];


        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $create_payment = $this->Payment->savePayment($data_payment, null);
            if($create_payment[CODE] == ERROR){
                throw new Exception(!empty($create_payment[MESSAGE]) ? $create_payment[MESSAGE] : null);
            }

            $payment_info = !empty($create_payment[DATA]) ? $create_payment[DATA] : [];
            $payment_code = !empty($payment_info[CODE]) ? $payment_info[CODE] : null;

            // send to gateway payment
            $checkout = $this->Checkout->checkoutByGateway($payment_code, [
                'api' => !empty($options['api']) ? true : false
            ]);

            if($checkout[CODE] == ERROR){
                throw new Exception(!empty($checkout[MESSAGE]) ? $checkout[MESSAGE] : null);
            }
            $url = !empty($checkout[DATA]['url']) ? $checkout[DATA]['url'] : null;

            $conn->commit();

            return $this->System->getResponse([
                CODE => SUCCESS, 
                MESSAGE => __d('template', 'xu_ly_thong_tin_giao_dich_thanh_cong'), 
                DATA => [
                    'url' => $url
                ]
            ]);
        }catch (Exception $e) {
            $conn->rollback();
            return $this->System->getResponse([MESSAGE => $e->getMessage()]);
        }
    }
    
}