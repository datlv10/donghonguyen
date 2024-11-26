<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;
use Cake\Core\Exception\Exception;
use Cake\Utility\Security;

class CustomersPointFrontendComponent extends Component
{
	public $controller = null;
    public $components = ['System', 'Utilities', 'OrderFrontend', 'Admin.CustomersPoint', 'PaginatorExtend'];

    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->controller = $this->_registry->getController();
    }

    public function attendanceTick($data = [])
    {
        $api = !empty($options['api']) ? true : false;

        $table = TableRegistry::get('CustomersPointTick');
        $settings = TableRegistry::get('Settings')->getSettingWebsite();
        $member = $this->controller->getRequest()->getSession()->read(MEMBER);
        $member_id = !empty($member['customer_id']) ? intval($member['customer_id']) : null;

        $today = $this->Utilities->stringDateTimeToInt(date('Y-m-d 00:00:00'));
        $date = !empty($data['date']) ? intval($data['date']) : null;

        if (empty($data) || empty($date)) {
            return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        if (empty($member_id)) {
            return $this->System->getResponse([MESSAGE => __d('template', 'vui_long_dang_nhap_tai_khoan_de_su_dung_chuc_nang_nay')]);
        }
        
        if ($date != $today) {
            return $this->System->getResponse([MESSAGE => __d('template', 'ngay_diem_danh_khong_hop_le')]);
        }

        // kiểm tra ngày hôm nay đã điểm danh chưa
        if (!empty($table->find()->where(['member_id' => $member_id,'tick_time' => $today])->first())) {
            return $this->System->getResponse([MESSAGE => __d('template', 'ban_da_diem_danh_trong_ngay_hom_nay')]);
        }

        $config_attendance = !empty($settings['attendance']) ? $settings['attendance'] : [];

        $attendance_point_config = !empty($config_attendance['point_config']) ? explode(',', $config_attendance['point_config']) : [];
        $number_day = !empty($config_attendance['number_day']) ? $config_attendance['number_day'] : null;        

        if (empty($attendance_point_config) || empty($number_day)) {
            return $this->System->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_cau_hinh_diem_danh')]);
        }

        $list_tick = $table->getInfoAttendanceByMemberId($member_id);
        $today_sticked = array_key_first($list_tick) == $today ? true : false;

        $sticked = [];
        $point = null;
        $number_check = $today_sticked ? $number_day : $number_day + 1;
        for ($x = 0; $x < $number_check ; $x++) {
            $day = strtotime("-" . $x . "days", $today);

            // nếu ngày hôm nay chưa điểm danh thì bỏ qua
            if(!$today_sticked && $day == $today) continue;

            // nếu chưa điểm danh thì dừng luôn
            if (empty($list_tick[$day])) break;
            $sticked[] = $day;
        }

        if (empty($sticked)) {
            $point = $attendance_point_config[1];
        }
        $point = !empty($attendance_point_config[intval(count($sticked))]) ? $attendance_point_config[intval(count($sticked))] : null;

        if(count($sticked) >= $number_day && reset($sticked) != $today) {
            $list_delete_check = $sticked;
            $sticked = [];
        }

        $start_date = !empty($sticked) ? end($sticked) : $today;

        $customer_attendance = $table->newEntity([
            'member_id' => $member_id,
            'tick_time' => $date
        ]);

        // check xem trong db có dữ liệu điểm danh trước ngày start_date không
        $list_old_tick = $table->find()->where([
            'member_id' => $member_id,
            'tick_time <' => $start_date
        ])->first();        
        $delete_old_tick = !empty($list_old_tick) ? true : false;

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();
                        
            $save = $table->save($customer_attendance);

            if (empty($save->id)){
                throw new Exception();
            }

            // clear dữ liệu
            if ($delete_old_tick) {
                $clear_point_tick = $table->deleteAll([
                    'member_id' => $member_id,
                    'tick_time <' => $start_date
                ]);

                if (empty($clear_point_tick)){
                    throw new Exception();
                }
            }

            // luu diem cua khach hang vao bang customer_point va customer_point_history
            $save_customer_point = $this->CustomersPoint->saveCustomerPointHistory([
                'customer_id' => $member_id,
                'point_type' => 0,
                'action' => 1,
                'point' => $point,
                'action_type' => ATTENDANCE
            ]);

            if (empty($save_customer_point)) {
                return $this->System->getResponse([MESSAGE => __d('template', 'luu_thong_tin_diem_khong_thanh_cong')]);
            }
            
            $conn->commit();
            $this->System->getResponse([
                CODE => SUCCESS,
                MESSAGE => __d('template', 'tai_khoan_cua_ban_da_duoc_dang_ky_thanh_cong')
            ]);

            return $this->System->getResponse([CODE => SUCCESS]);

        }catch (Exception $e) {
            $conn->rollback();
            return $this->System->getResponse([MESSAGE => $e->getMessage()]);
        }
    }

    public function processAttendance($options = [])
    {

        $member = $this->controller->getRequest()->getSession()->read(MEMBER);
        $member_id = !empty($member['customer_id']) ? intval($member['customer_id']) : null;
        if (empty($member_id)) return [];

        $settings = TableRegistry::get('Settings')->getSettingWebsite();
        $table = TableRegistry::get('CustomersPointTick');

        $config_attendance = !empty($settings['attendance']) ? $settings['attendance'] : [];        
        $attendance_point_config = !empty($config_attendance['point_config']) ? explode(',', $config_attendance['point_config']) : [];
        $number_day = !empty($config_attendance['number_day']) ? $config_attendance['number_day'] : null;

        if (empty($attendance_point_config) || empty($number_day)) return [];

        $result = $list_delete_check = [];
        $today = $this->Utilities->stringDateTimeToInt(date('Y-m-d 00:00:00'));

        $list_tick = $table->getInfoAttendanceByMemberId($member_id, intval($number_day) + 1);        
        $today_sticked = array_key_first($list_tick) == $today ? true : false;

        $sticked = [];
        $number_check = $today_sticked ? $number_day : $number_day + 1;
        for ($x = 0; $x < $number_check ; $x++) {
            $day = strtotime("-" . $x . "days", $today);

            // nếu ngày hôm nay chưa điểm danh thì bỏ qua
            if(!$today_sticked && $day == $today) continue;

            // nếu chưa điểm danh thì dừng luôn
            if (empty($list_tick[$day])) break;
            $sticked[] = $day;
        }

        // nếu điểm danh đã đủ số ngày trong config và ngày cuối cùng điểm danh không phải hôm nay thì làm mới
        if(count($sticked) >= $number_day && reset($sticked) != $today) {
            $list_delete_check = $sticked;
            $sticked = [];
        }

        $start_date = !empty($sticked) ? end($sticked) : $today;

        // Hiển thị danh sách ngày đọc từ thông tin cấu hình number_day
        for ($i = 0; $i < $number_day; $i++) {
            $date = strtotime("+" . $i . "days", $start_date);

            $is_today = false;
            if($date == $today) {
                $is_today = true;                
            }

            $result[] = [
                'date' => $date,
                'point' => !empty($attendance_point_config[$i]) ? $attendance_point_config[$i] : 0,
                'check' => in_array($date, $sticked) ? true : false,
                'is_today' => $is_today
            ];
        }
        
        return $result;
    }

    public function applyPointToOrder($data = [], $options = [])
    {
        if(!$this->controller->getRequest()->is('post') || empty($data)){
           return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $type = !empty($data['type']) ? $data['type'] : null;
        $point = !empty($data['point']) ? intval($data['point']) : null;

        if(empty($type) || !in_array($type, [PROMOTION, WALLET])){
            return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        if(empty($point)){
            return $this->System->getResponse([MESSAGE => __d('template', 'vui_long_nhap_so_diem')]);
        }

        $session = $this->controller->getRequest()->getSession();

        $member_info = $session->read(MEMBER);
        $customer_id = !empty($member_info['id']) ? intval($member_info['id']) : null;
        if(empty($customer_id)){
            return $this->System->getResponse([MESSAGE => __d('template', 'vui_long_dang_nhap_tai_khoan_de_su_dung_chuc_nang_nay')]);
        }

        $settings = TableRegistry::get('Settings')->getSettingWebsite();
        $setting = !empty($settings['point']) ? $settings['point'] : [];        
        $point_to_money = !empty($setting['point_to_money']) ? floatval($setting['point_to_money']) : 1;
        if(empty($setting['pay_by_point'])){
            return $this->System->getResponse([MESSAGE => __d('template', 'khong_ap_dung_diem_voi_don_hang_nay')]);
        }

        // kiểm tra số điểm nhập vào có hợp lệ
        $point_info = TableRegistry::get('CustomersPoint')->getInfoCustomerPoint($customer_id);

        $point_wallet = !empty($point_info['point']) ? intval($point_info['point']) : 0;
        $point_promotion = !empty($point_info['point_promotion']) ? intval($point_info['point_promotion']) : 0;
        $expiration_time = !empty($point_info['expiration_time']) ? intval($point_info['expiration_time']) : 0;

        if($point > $point_promotion && $type == PROMOTION){            
            return $this->System->getResponse([MESSAGE => __d('template', 'so_diem_thuong_vi_khong_du_de_su_dung')]);
        }

        $current_time = $this->Utilities->stringDateTimeToInt(date('Y-m-d H:i:s'));
        if($expiration_time < $current_time){
            return $this->System->getResponse([MESSAGE => __d('template', 'diem_thuong_da_het_han_su_dung')]);
        }

        if($point > $point_wallet && $type == WALLET){
            return $this->System->getResponse([MESSAGE => __d('template', 'so_diem_trong_vi_khong_du_de_su_dung')]);
        }

        // xóa điểm và tổng tiền đã có trước đó trong session
        $session_point = $session->read(POINT);       

        if(empty($session_point)){
            $session_point = [
                'point' => 0,
                'point_promotion' => 0,
                'total_by_point' => 0,
                'total_by_point_promotion' => 0,
            ];
        }else{
            switch($type){
                case PROMOTION:
                    if(!empty($session_point['point_promotion'])){
                        $session_point['point_promotion'] = 0;
                        $session_point['total_by_point_promotion'] = 0;
                    }
                break;

                case WALLET:
                    if(!empty($session_point['point_promotion'])){
                        $session_point['point'] = 0;
                        $session_point['total_by_point'] = 0;
                    }
                break;
            }
        }

        $session->write(POINT, $session_point);

        // đọc thông tin order
        $order_info = $this->OrderFrontend->confirmOrderInfomation();
        $debt_order = !empty($order_info['debt']) ? floatval($order_info['debt']) : 0;

        $total_by_point = $point * $point_to_money;
        if($total_by_point >= $debt_order){
            $total_by_point = $debt_order;
            $point = round($total_by_point / $point_to_money);
        }

        // xử lý gia trị của point và point_total
        $result = $session_point;
        switch($type){
            case PROMOTION:
                $result['point_promotion'] = $point;
                $result['total_by_point_promotion'] = $total_by_point;
            break;

            case WALLET:
                $result['point'] = $point;
                $result['total_by_point'] = $total_by_point;
            break;
        }

        $session->write(POINT, $result);
        return $this->System->getResponse([
            CODE => SUCCESS,
            MESSAGE => __d('template', 'cap_nhat_thanh_cong'),
            DATA => $result
        ]);
    }

    public function clearPointInOrder($data = [], $options = [])
    {
        if(!$this->controller->getRequest()->is('post') || empty($data)){
           return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $type = !empty($data['type']) ? $data['type'] : null;

        if(empty($type) || !in_array($type, [PROMOTION, WALLET])){
            return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $session = $this->controller->getRequest()->getSession();

        $session_point = $session->read(POINT);

        $result = [
            CODE => SUCCESS,
            MESSAGE => __d('template', 'cap_nhat_thanh_cong')
        ];

        if(empty($session_point)) return $this->System->getResponse($result);

        switch($type){
            case PROMOTION:
                if(isset($session_point['point_promotion'])){
                    unset($session_point['point_promotion']);
                    unset($session_point['total_by_point_promotion']);
                }
            break;

            case WALLET:
                if(isset($session_point['point_promotion'])){
                    unset($session_point['point']);
                    unset($session_point['total_by_point']);
                }
            break;
        }

        $session->write(POINT, $session_point);

        return $this->System->getResponse($result);
    }

    public function getInfoCustomerPoint()
    {
        $session = $this->controller->getRequest()->getSession();

        $member_info = $session->read(MEMBER);
        $customer_id = !empty($member_info['id']) ? intval($member_info['id']) : null;
        if(empty($customer_id)){
            return $this->System->getResponse([MESSAGE => __d('template', 'het_phien_lam_viec_vui_long_dang_nhap_lai_tai_khoan')]);
        }

        $result = TableRegistry::get('CustomersPoint')->getInfoCustomerPoint($customer_id);

        return !empty($result) ? $result : [];
    }

    public function historyUsingPoint($data = [])
    {
        $point_type = isset($data['point_type']) ? intval($data['point_type']) : null;
        $action = isset($data['action']) ? intval($data['action']) : null;
        $action_type = !empty($data['action_type']) ? trim($data['action_type']) : null;
        $number_record = !empty($data[NUMBER_RECORD]) ? intval($data[NUMBER_RECORD]) : 10;
        $page = !empty($data[PAGE]) ? intval($data[PAGE]) : 1;

        if(!is_null($point_type) && !in_array($point_type, [0,1])){
            return $this->System->getResponse([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        if(!is_null($action) && !in_array($action, [0,1])){
            return $this->System->getResponse([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        if(!empty($action_type) && !in_array($action_type, [ORDER, PROMOTION, ATTENDANCE, OTHER, AFFILIATE, WITHDRAW])) {
            return $this->System->getResponse([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_giao_dich')]);
        }

        $session = $this->controller->getRequest()->getSession();

        $member_info = $session->read(MEMBER);
        $customer_id = !empty($member_info['id']) ? intval($member_info['id']) : null;
        if(empty($customer_id)){
            return $this->System->getResponse([MESSAGE => __d('template', 'vui_long_dang_nhap_tai_khoan_de_su_dung_chuc_nang_nay')]);
        }

        $params = [
            FILTER => [
                // STATUS => 1,
                'customer_id' => $customer_id,
            ]
        ];

        if(!is_null($point_type)){
            $params[FILTER]['point_type'] = $point_type;
        }

        if(!is_null($action)){
            $params[FILTER]['action'] = $action;
        }

        if(!is_null($action_type)){
            $params[FILTER]['action_type'] = $action_type;
        }

        $table = TableRegistry::get('CustomersPointHistory');

        try {
            $history_point = $this->PaginatorExtend->paginate($table->queryListCustomerPointHistory($params), [
                'limit' => $number_record,
                'page' => $page
            ])->toArray();
        } catch (Exception $e) {
            $history_point = [];
        }

        $pagination_info = !empty($this->controller->getRequest()->getAttribute('paging')['CustomersPointHistory']) ? $this->controller->getRequest()->getAttribute('paging')['CustomersPointHistory'] : [];
        $pagination = $this->Utilities->formatPaginationInfo($pagination_info);

        $result = [];
        if(!empty($history_point)){
            foreach ($history_point as $k => $history) {
                $result[] = $table->formatDataPointHistoryDetail($history);
            }
        }

        return $this->System->getResponse([
            CODE => SUCCESS, 
            DATA => [
                PAGINATION => $pagination,
                'history_point' => $result
            ]
        ]); 
    }
}
