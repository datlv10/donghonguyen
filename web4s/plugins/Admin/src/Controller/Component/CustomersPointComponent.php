<?php

namespace Admin\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;
use Cake\Core\Exception\Exception;
use Cake\Utility\Security;
use Cake\Utility\Hash;

class CustomersPointComponent extends AppComponent
{
    public $controller = null;
    public $components = ['System', 'Utilities', 'CustomersPoint'];

    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->controller = $this->_registry->getController();
    }

    public function saveCustomerPointHistory($data = [])
    {
        if (!$this->controller->getRequest()->is('post') || empty($data)) {
            return $this->System->getResponse([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('CustomersPointHistory');

        $customer_id = !empty($data['customer_id']) ? intval($data['customer_id']) : null;
        $point = !empty($data['point']) ? intval(str_replace(',', '', $data['point'])) : null;
        $point_type = isset($data['point_type']) ? intval($data['point_type']) : null;
        $action = isset($data['action']) ? intval($data['action']) : null;
        $action_type = !empty($data['action_type']) ? trim($data['action_type']) : null;
        $customer_related_id = !empty($data['customer_related_id']) ? intval($data['customer_related_id']) : null;

        if(empty($customer_id)){
            return $this->System->getResponse([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_khach_hang')]);
        }

        $customer_info = TableRegistry::get('Customers')->getDetailCustomer($customer_id);
        if(empty($customer_info)){
            return $this->System->getResponse([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_khach_hang')]);
        }

        if(empty($point)) {
            return $this->System->getResponse([MESSAGE => __d('admin', 'vui_long_nhap_so_diem')]);
        }

        if(!in_array($point_type, [0,1])){
            return $this->System->getResponse([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        if(!in_array($action, [0,1])){
            return $this->System->getResponse([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $list_type_action = Configure::read('LIST_ACTION_TYPE_POINT');
        if(empty($action_type) || (!empty($action_type) && !in_array($action_type, $list_type_action))) {
            return $this->System->getResponse([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_giao_dich')]);
        }

        $status = 1;
        if(isset($data['status'])){
            $status = intval($data['status']);
        }

        if(!in_array($status, [0, 1, 2])){
            return $this->System->getResponse([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $code = 'CPH' . $this->Utilities->generateRandomNumber(9);
        $data_save = [
            'code' => $code,
            'customer_id' => $customer_id,
            'point' => $point,
            'point_type' => $point_type,
            'action' => $action,
            'action_type' => $action_type,
            'staff_id' => !empty($data['staff_id']) ? intval($data['staff_id']) : null,
            'note' => !empty($data['note']) ? trim($data['note']) : null,
            'status' => $status,
            'customer_related_id' => $customer_related_id
        ];        

        $entity = $table->newEntity($data_save);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            // save data
            $save = $table->save($entity);

            if (empty($save->id)){
                throw new Exception();
            }

            // trạng thái = 1 -> update lại điểm của khách hàng
            if($status == 1){
                $update_point = $this->saveCustomerPoint([
                    'customer_id' => $customer_id,
                    'point' => $point,
                    'point_type' => $point_type,
                    'action' => $action,
                ]);

                if(empty($update_point[CODE]) || $update_point[CODE] == ERROR){
                    $message = !empty($update_point[MESSAGE]) ? $update_point[MESSAGE] : __d('admin', 'dieu_chinh_diem_khong_thanh_cong');
                    throw new Exception($message);
                }
            }
            

            $conn->commit();
            return [CODE => SUCCESS, DATA => $save, MESSAGE => __d('admin', 'dieu_chinh_diem_thanh_cong')];

        }catch (Exception $e) {
            $conn->rollback();
            return $this->System->getResponse([MESSAGE => $e->getMessage()]);
        }
    }

    public function saveCustomerPoint($data = [])
    {
        $customer_id = !empty($data['customer_id']) ? $data['customer_id'] : null;
        $point = !empty($data['point']) ? intval($data['point']) : null;
        $point_type = isset($data['point_type']) ? intval($data['point_type']) : null;
        $action = isset($data['action']) ? intval($data['action']) : null;

        if(empty($point)) {
            return $this->System->getResponse([MESSAGE => __d('admin', 'vui_long_nhap_so_diem')]);
        }

        if(!in_array($point_type, [0,1])){
            return $this->System->getResponse([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        if(!in_array($action, [0,1])){
            return $this->System->getResponse([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        if(empty($customer_id)){
            return $this->System->getResponse([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_khach_hang')]);
        }

        $customer_info = TableRegistry::get('Customers')->getDetailCustomer($customer_id);
        if(empty($customer_info)){
            return $this->System->getResponse([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_khach_hang')]);
        }

        $table = TableRegistry::get('CustomersPoint');

        $settings = TableRegistry::get('Settings')->getSettingWebsite();
        $setting_point = !empty($settings['point']) ? $settings['point'] : [];
        $time_used_point = !empty($setting_point['time_used_point']) ? intval($setting_point['time_used_point']) : 30;

        $point_info = $table->find()->where([
            'customer_id' => $customer_id
        ])->first();

        // khai bao thong tin luu db
        $customer_point = !empty($point_info['point']) ? intval($point_info['point']) : 0;
        $customer_point_promotion = !empty($point_info['point_promotion']) ? intval($point_info['point_promotion']) : 0;
        $expiration_time = !empty($point_info['expiration_time']) ? intval($point_info['expiration_time']) : 0;        
        
        if($action == 0 && $point_type == 1 && $customer_point < $point){
            return $this->System->getResponse([MESSAGE => __d('admin', 'so_diem_trong_vi_khong_du_de_su_dung')]);
        }

        if($action == 0 && $point_type == 0 && $customer_point_promotion < $point){
            return $this->System->getResponse([MESSAGE => __d('admin', 'so_diem_trong_vi_khong_du_de_su_dung')]);
        }

        // check cong diem mac dinh hay diem thuong
        if ($point_type == 1) {
            if($action == 1){
                $customer_point = $customer_point + $point;
            }else{
                $customer_point = $customer_point - $point;
            }
        } else {
            if($action == 1){
                $customer_point_promotion = $customer_point_promotion + $point;
            }else{
                $customer_point_promotion = $customer_point_promotion - $point;
            }
        }

        $expiration_time = time() + $time_used_point * (60 * 60 * 24);

        $data_save = [
            'customer_id' => $customer_id
        ];

        if($action == 1 && $point_type == 0){
            $data_save['expiration_time'] = $expiration_time;
        }

        if($point_type == 1){
            $data_save['point'] = $customer_point;
        }else{
            $data_save['point_promotion'] = $customer_point_promotion;
        }

        // merge data with entity 
        if(empty($point_info)){
            $entity = $table->newEntity($data_save);
        }else{            
            $entity = $table->patchEntity($point_info, $data_save);
        }

        $save = $table->save($entity);

        if (empty($save->id)){
            return $this->System->getResponse([MESSAGE => __d('admin', 'dieu_chinh_diem_khong_thanh_cong')]);
        }

        return $this->System->getResponse([
            CODE => SUCCESS
        ]);
    }

    public function refundPointOrder($order_id = null)
    {
        if(empty($order_id)) return false;

        $settings = TableRegistry::get('Settings')->getSettingWebsite();
        $point_setting = !empty($settings['point']) ? $settings['point'] : [];

        $apply_refund_order = !empty($point_setting['apply_refund_order']) ? true : false;
        $condition_refund_order = !empty($point_setting['condition_refund_order']) ? intval($point_setting['condition_refund_order']) : 0;
        $type_refund = !empty($point_setting['type_refund']) ? $point_setting['type_refund'] : null;
        $value_refund = !empty($point_setting['value_refund']) ? floatval($point_setting['value_refund']) : 0;
        $point_to_money = !empty($point_setting['point_to_money']) ? floatval($point_setting['point_to_money']) : 0;

        if(!$apply_refund_order || $value_refund < 0) return false;
        if(!in_array($type_refund, [POINT, PERCENT])) return false;
        
        $order_info = TableRegistry::get('Orders')->getDetailOrder($order_id, ['get_contact' => true]);        
        if(empty($order_info)) return false;

        $total_order = !empty($order_info['total']) ? floatval($order_info['total']) : 0;
        $status = !empty($order_info['status']) ? $order_info['status'] : null;
        if($status != DONE) return false;

        if($total_order < $condition_refund_order) return false;

        $result = $value_refund;
        if($type_refund == PERCENT){
            $result = ($total_order / $point_to_money) / 100 * $value_refund;
        }

        $point_refund = round($result);

        $data_point_history = [
            'customer_id' => !empty($order_info['OrdersContact']['customer_id']) ? intval($order_info['OrdersContact']['customer_id']) : null,
            'point' => $point_refund,
            'point_type' => 0, // 0 -> điểm thưởng
            'action' => 1, // 1 -> cộng điểm
            'action_type' => ORDER,
            'status' => 1
        ];

        $update_point = $this->CustomersPoint->saveCustomerPointHistory($data_point_history);
        if (!empty($update_point[CODE]) && $update_point[CODE] == SUCCESS){
            return true;
        }

        return false;
    }

    public function updatePointAfterPayment($point_history_id = null)
    {
        if (empty($point_history_id)) {
            return $this->System->getResponse([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('CustomersPointHistory');

        $point_history_info = $table->find()->where(['id' => $point_history_id])->first();
        if (empty($point_history_info)){
            throw new Exception(__d('admin', 'khong_lay_duoc_thong_tin_giao_dich'));
        }

        $customer_id = !empty($point_history_info['customer_id']) ? intval($point_history_info['customer_id']) : null;
        $point = !empty($point_history_info['point']) ? intval($point_history_info['point']) : 0;
        $point_type = !empty($point_history_info['point_type']) ? 1 : 0; // 0 -> điểm thưởng, 1 -> điểm trong ví
        $action = !empty($point_history_info['action']) ? 1 : 0; // 0 -> trừ, 1 -> cộng
        $action_type = !empty($point_history_info['action_type']) ? $point_history_info['action_type'] : null;
        $status = !empty($point_history_info['status']) ? intval($point_history_info['status']) : null;

        // chỉ thực hiện cập nhật các giao dịch chờ duyệt (status == 2)
        if(empty($status) || $status != 2){
            return $this->System->getResponse([MESSAGE => __d('admin', 'khong_the_cap_nhat_trang_thai_cua_giao_dich_nay')]);
        }

        if(empty($customer_id)){
            return $this->System->getResponse([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_khach_hang')]);
        }

        $customer_info = TableRegistry::get('Customers')->getDetailCustomer($customer_id);
        if(empty($customer_info)){
            return $this->System->getResponse([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_khach_hang')]);
        }

        if(empty($point)) {
            return $this->System->getResponse([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        if(!in_array($point_type, [0,1])){
            return $this->System->getResponse([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        if(!in_array($action, [0,1])){
            return $this->System->getResponse([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $list_type_action = Configure::read('LIST_ACTION_TYPE_POINT');
        if(empty($action_type) || (!empty($action_type) && !in_array($action_type, $list_type_action))) {
            return $this->System->getResponse([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $data_save = [
            'id' => $point_history_id,
            'status' => 1
        ];        

        $entity = $table->patchEntity($point_history_info, $data_save);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $update_point_history = $table->save($entity);
            if (empty($update_point_history->id)){
                throw new Exception();
            }

            $update_point = $this->saveCustomerPoint([
                'customer_id' => $customer_id,
                'point' => $point,
                'point_type' => $point_type,
                'action' => $action,
            ]);

            if(empty($update_point[CODE]) || $update_point[CODE] == ERROR){
                $message = !empty($update_point[MESSAGE]) ? $update_point[MESSAGE] : __d('admin', 'dieu_chinh_diem_khong_thanh_cong');
                throw new Exception($message);
            }

            $conn->commit();
            return [CODE => SUCCESS, MESSAGE => __d('admin', 'cap_nhat_thanh_cong')];

        }catch (Exception $e) {
            $conn->rollback();
            return $this->System->getResponse([MESSAGE => $e->getMessage()]);
        }
    }

    public function refundPointOrderPartner($order_id = null, $affiliate_code = null, $exist_coupon = false)
    {
        if(empty($order_id) && empty($affiliate_code)) return false;
        
        // kiểm tra khách hàng có tồn tại không | khách hàng đã trở thành đối tác chưa
        $customers_info = TableRegistry::get('Customers')->find()->where([
            'is_partner_affiliate' => 1,
            'status' => 1,
            'deleted' => 0,
            'code' => $affiliate_code

        ])->select(['id', 'level_partner_affiliate'])->first();

        $customer_id = !empty($customers_info['id']) ? intval($customers_info['id']) : null;
        $level_partner = !empty($customers_info['level_partner_affiliate']) ? intval($customers_info['level_partner_affiliate']) : 0;

        if (empty($customers_info)) return false;

        // check thông tin đơn hàng của đối tác
        $affiliates_order = TableRegistry::get('CustomersAffiliateOrder')->find()->where([
            'CustomersAffiliateOrder.order_id' => $order_id,
            'CustomersAffiliateOrder.customer_id' => $customer_id
        ])->first();

        if(empty($affiliates_order)) return false;

        // lấy thông tin chiết khấu hoa hồng cho đối tác từ bảng customer_affiliate_order
        $profit_point = !empty($affiliates_order['profit_point']) ? intval($affiliates_order['profit_point']) : 0;
        $profit_money = !empty($affiliates_order['profit_money']) ? intval($affiliates_order['profit_money']) : 0;

        // doc thong tin dơn hang | kiem tra trạng thái đơn hàng    
        $order_info = TableRegistry::get('Orders')->getDetailOrder($order_id);        
        if(empty($order_info)) return false;

        $total_order = !empty($order_info['total']) ? floatval($order_info['total']) : 0;
        $status = !empty($order_info['status']) ? $order_info['status'] : null;

        if(!in_array($status, [DONE, CANCEL])) return false;

        $action = 1;
        if ($status == CANCEL) {
            $action = 0;
        }

        // cập nhật lịch sử điểm cho khách hàng
        $data_point_history = [
            'customer_id' => $customer_id,
            'point' => $profit_point,
            'point_type' => 1, // 1 -> điểm mặc định
            'action' => intval($action), // 1 -> cộng điểm
            'action_type' => AFFILIATE,
            'status' => 1
        ];

        $update_point = $this->saveCustomerPointHistory($data_point_history);
        if (empty($update_point[CODE]) || (!empty($update_point[CODE]) && $update_point[CODE] != SUCCESS)) {
            return false;
        }

        // cập nhật thông tin vào bảng customers_affiliate
        $customers_affiliate_table = TableRegistry::get('CustomersAffiliate');

        $customers_affiliate = $customers_affiliate_table->find()->where([
            'CustomersAffiliate.customer_id' => $customer_id
        ])->first();

        $number_referral = !empty($customers_affiliate['number_referral']) ? intval($customers_affiliate['number_referral']) : 1;
        $number_order_success = !empty($customers_affiliate['number_order_success']) ? intval($customers_affiliate['number_order_success']) : 0;
        $total_order_success = !empty($customers_affiliate['total_order_success']) ? floatval($customers_affiliate['total_order_success']) : 0;
        $number_order_failed = !empty($customers_affiliate['number_order_failed']) ? intval($customers_affiliate['number_order_failed']) : 0;
        $total_order_failed = !empty($customers_affiliate['total_order_failed']) ? floatval($customers_affiliate['total_order_failed']) : 0;
        $total_point = !empty($customers_affiliate['total_point']) ? intval($customers_affiliate['total_point']) : 0;

        $data_customers_affiliate = [
            'customer_id' => intval($customer_id),
            'number_referral' => intval($number_referral),
            'number_order_success' => intval($number_order_success) + 1,
            'total_order_success' => floatval($total_order_success) + $total_order,
            'number_order_failed' => intval($number_order_failed),
            'total_order_failed' => floatval($total_order_failed),
            'total_point' => intval($total_point) + intval($profit_point)
        ];

        if ($status == CANCEL) {
            $data_customers_affiliate = [
                'customer_id' => intval($customer_id),
                'number_referral' => intval($number_referral),
                'number_order_success' => intval($number_order_success) - 1,
                'total_order_success' => floatval($total_order_success) - $total_order,
                'number_order_failed' => intval($number_order_failed) + 1,
                'total_order_failed' => floatval($total_order_failed) + $total_order,
                'total_point' => intval($total_point) - intval($profit_point)
            ];
        }

        if(empty($customers_affiliate)){
            $entity_affiliate = $customers_affiliate_table->newEntity($data_customers_affiliate);
        }else{            
            $entity_affiliate = $customers_affiliate_table->patchEntity($customers_affiliate, $data_customers_affiliate);
        }

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $save_affiliate = $customers_affiliate_table->save($entity_affiliate);

            if (empty($save_affiliate->id)){
                return $this->System->getResponse([MESSAGE => __d('admin', 'dieu_chinh_diem_khong_thanh_cong')]);
            }

            $conn->commit();
            return [CODE => SUCCESS, MESSAGE => __d('admin', 'cap_nhat_thanh_cong')];

        }catch (Exception $e) {
            $conn->rollback();
            return $this->System->getResponse([MESSAGE => $e->getMessage()]);
        }

        return true;
    }
}
