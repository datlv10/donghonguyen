<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use App\Model\Behavior\UnixTimestampBehavior;
use Cake\Core\Configure;
use Cake\Utility\Text;

class CustomersPointHistoryTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('customers_point_history');
        $this->setPrimaryKey('id');

        $this->addBehavior('UnixTimestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                    'updated' => 'existing'
                ]
            ]
        ]);

        $this->hasOne('Customers', [
            'className' => 'Customers',
            'foreignKey' => 'id',
            'bindingKey' => 'customer_id',
            'propertyName' => 'Customers'
        ]);

        $this->hasOne('Users', [
            'className' => 'Users',
            'foreignKey' => 'id',
            'bindingKey' => 'staff_id',
            'propertyName' => 'Users'
        ]);

    }

    public function queryListCustomerPointHistory($params = []) 
    {
        $table = TableRegistry::get('CustomersPointHistory');

        // get info params
        $field = !empty($params[FIELD]) ? $params[FIELD] : SIMPLE_INFO;
        $get_customer = !empty($params['get_customer']) ? $params['get_customer'] : false;
        $get_staff = !empty($params['get_staff']) ? $params['get_staff'] : false;

        // sort
        $sort = !empty($params[SORT]) ? $params[SORT] : [];
        $sort_field = !empty($sort[FIELD]) ? $sort[FIELD] : null;
        $sort_type = !empty($sort[SORT]) && in_array($sort[SORT], [DESC, ASC]) ? $sort[SORT] : DESC;

        // filter
        $filter = !empty($params[FILTER]) ? $params[FILTER] : [];

        $keyword = !empty($filter['keyword']) ? trim($filter['keyword']) : null;
        $code = !empty($filter['code']) ? trim($filter['code']) : null;
        $status = isset($filter['status']) && $filter['status'] != '' ? intval($filter['status']) : null;
        $staff_id = !empty($filter['staff_id']) ? $filter['staff_id'] : null;
        $customer_id = !empty($filter['customer_id']) ? intval($filter['customer_id']) : null;

        $point_type = isset($filter['point_type']) ? intval($filter['point_type']) : null;
        $action = isset($filter['action']) ? intval($filter['action']) : null;
        $action_type = !empty($filter['action_type']) ? $filter['action_type'] : null;
        $create_from = !empty($filter['create_from']) ? strtotime(date('Y-m-d 00:00:00', strtotime(str_replace('/', '-', $filter['create_from'])))) : null;
        $create_to = !empty($filter['create_to']) ? strtotime(date('Y-m-d 23:59:59', strtotime(str_replace('/', '-', $filter['create_to'])))) : null;

        // fields select
        switch($field){
            case LIST_INFO:
                $fields = ['CustomersPointHistory.id'];
            break;

            case FULL_INFO:
            case SIMPLE_INFO:
            default:
                $fields = ['CustomersPointHistory.id', 'CustomersPointHistory.code', 'CustomersPointHistory.customer_id', 'CustomersPointHistory.point', 'CustomersPointHistory.point_type', 'CustomersPointHistory.action', 'CustomersPointHistory.action_type', 'CustomersPointHistory.staff_id', 'CustomersPointHistory.note', 'CustomersPointHistory.customer_related_id', 'CustomersPointHistory.status', 'CustomersPointHistory.created'];
            break;
        }

        $sort_string = 'CustomersPointHistory.id DESC';
        if(!empty($params[SORT])){
            switch($sort_field){
                case 'id':
                    $sort_string = 'CustomersPointHistory.id '. $sort_type;
                break;

                case 'point':
                    $sort_string = 'CustomersPointHistory.point '. $sort_type .', CustomersPointHistory.id DESC';
                break;

                case 'point_type':
                    $sort_string = 'CustomersPointHistory.point_type '. $sort_type .', CustomersPointHistory.id DESC';
                break;

                case 'action':
                    $sort_string = 'CustomersPointHistory.action '. $sort_type .', CustomersPointHistory.id DESC';
                break;

                case 'action_type':
                    $sort_string = 'CustomersPointHistory.action_type '. $sort_type .', CustomersPointHistory.id DESC';
                break;  

                case 'staff_id':
                    $sort_string = 'CustomersPointHistory.staff_id '. $sort_type .', CustomersPointHistory.id DESC';
                break;

                case 'status':
                    $sort_string = 'CustomersPointHistory.status '. $sort_type .', CustomersPointHistory.id DESC';
                break;       
            }
        }

        // filter by conditions
        $where = $contain = [];   

        if (!empty($code)) {
            $where['CustomersPointHistory.code'] = $code;
        } 

        if (!empty($customer_id)) {
            $where['CustomersPointHistory.customer_id'] = $customer_id;
        }

        if (!is_null($point_type)) {
            $where['CustomersPointHistory.point_type'] = $point_type;
        }

        if (!is_null($action)) {
            $where['CustomersPointHistory.action'] = $action;
        }

        if (!is_null($status)) {
            $where['CustomersPointHistory.status'] = $status;
        }

        if (!empty($action_type)) {
            $where['CustomersPointHistory.action_type'] = $action_type;
        }

        if(!empty($keyword)){
            $where['Customers.search_unicode LIKE'] = '%' . Text::slug(strtolower($keyword), ' ') . '%';
        }

        if(!empty($create_from)){
            $where['CustomersPointHistory.created >='] = $create_from;
        }

        if(!empty($create_to)){
            $where['CustomersPointHistory.created <='] = $create_to;
        }

        if($get_customer){
            $contain[] = 'Customers';
            $where['Customers.deleted'] = 0;
            
            $fields[] = 'Customers.full_name';
        }

        if($get_staff){
            $fields[] = 'Users.full_name';
            $contain[] = 'Users';
        }
        return $table->find()->contain($contain)->where($where)->select($fields)->group('CustomersPointHistory.id')->order($sort_string);
    }

    public function formatDataPointHistoryDetail($data = [])
    {
        if(empty($data)) return [];

        $point_type = !empty($data['point_type']) ? 1 : 0;
        $action = !empty($data['action']) ? 1 : 0;
        $action_type = !empty($data['action_type']) ? $data['action_type'] : null;

        $description = __d('template', 'su_dung_diem');
        switch($action_type){
            case ORDER:

                if($point_type == 0){
                    if($action == 0){
                        $description = __d('template', 'dung_diem_thuong_khi_mua_hang');
                    }

                    if($action == 1){
                        $description = __d('template', 'mua_hang_tich_diem');
                    }
                }

                if($point_type == 1){
                    if($action == 0){
                        $description = __d('template', 'dung_diem_vi_khi_mua_hang');
                    }
                }
            break;

            case PROMOTION:
                $description = __d('template', 'chuong_trinh_khuyen_mai');
            break;

            case ATTENDANCE:
                if ($point_type == 0 && $action == 1) {
                    $description = __d('template', 'diem_danh_tich_tiem');
                }
            break;

            case AFFILIATE:
                $description = __d('template', 'tiep_thi_lien_ket');
            break;

            case WITHDRAW:
                $description = __d('template', 'rut_tien');
            break;

            case BUY_POINT:
                $description = __d('template', 'nap_diem_vi');
            break;

            case GIVE_POINT:
                $description = __d('template', 'tang_diem_vi');

                if($action == 1){
                    $description = __d('template', 'nhan_diem_tang');
                }

                if (!empty($data['customer_related_id'])) {
                    $customer_related_info = TableRegistry::get('Customers')->getDetailCustomer($data['customer_related_id']);
                }
            break;

            case OTHER:
                $description = __d('template', 'dieu_chinh_diem_tu_he_thong');
            break;
        }

        $result = [
            'id' => !empty($data['id']) ? intval($data['id']) : null,
            'code' => !empty($data['code']) ? $data['code'] : null,
            'customer_id' => !empty($data['customer_id']) ? intval($data['customer_id']) : null,
            'full_name' => null,
            'point' => !empty($data['point']) ? $data['point'] : null,
            'point_type' => $point_type,
            'action' => $action,
            'action_type' => $action_type,
            'description' => $description,
            'note' => !empty($data['note']) ? trim($data['note']) : null,
            'staff_id' => !empty($data['staff_id']) ? intval($data['staff_id']) : null,
            'customer_related_id' => !empty($data['customer_related_id']) ? intval($data['customer_related_id']) : null,
            'customer_related_name' => null,
            'customer_related_code' => null,
            'created' => !empty($data['created']) ? intval($data['created']) : null,
            'status' => isset($data['status']) ? intval($data['status']) : null,
        ];
        
        if(!empty($data['Customers'])){
            $result['full_name'] = !empty($data['Customers']['full_name']) ? $data['Customers']['full_name'] : null;
        }

        if(!empty($customer_related_info)){
            $result['customer_related_name'] = !empty($customer_related_info['full_name']) ? $customer_related_info['full_name'] : null;
            $result['customer_related_code'] = !empty($customer_related_info['code']) ? $customer_related_info['code'] : null;
        }

        if(!empty($data['Users'])){
            $result['staff_full_name'] = !empty($data['Users']['full_name']) ? $data['Users']['full_name'] : null;
        }

        return $result;
    }

    public function getInfoCustomerPointHistory($params = [])
    {
        if(empty($params)) return [];

        $customer_id = !empty($params['customer_id']) ? $params['customer_id'] : null;
        $code = !empty($params['code']) ? $params['code'] : null;

        if(empty($customer_id) && empty($code)) return [];

        $where = [];
        if (!empty($customer_id)) {
            $where = ['CustomersPointHistory.customer_id' => $customer_id];
        }

        if (!empty($code)) {
            $where = ['CustomersPointHistory.code' => $code];
        }

        $result = TableRegistry::get('CustomersPointHistory')->find()->where($where)->first();

        return !empty($result) ? $result : [];
    }

    public function sumAffiliatePointOfCustomer($customer_id = null, $params = [])
    {
        $get_all_partner = !empty($params['get_all_partner']) ? $params['get_all_partner'] : false;

        $create_from = !empty($params[FILTER]['create_from']) ? $params[FILTER]['create_from'] : null;
        $create_to = !empty($params[FILTER]['create_to']) ? $params[FILTER]['create_to'] : null;
        $action = isset($params[FILTER]['action']) ? intval($params[FILTER]['action']) : 1;

        if(empty($customer_id) && !$get_all_partner) return 0;

        $where = [
            'action' => $action, // 0 -> trừ , 1 -> cộng
            'action_type' => AFFILIATE,
            'point_type' => 1, //0 -> khuyến mại, 1 -> mặc định
            'status' => 1
        ];

        if (!empty($customer_id)) {
            $where['customer_id'] = $customer_id;
        }

        if(!empty($create_from)){
            $where['created >='] = $create_from;
        }

        if(!empty($create_to)){
            $where['created <='] = $create_to;
        }

        $result = $this->find()->select([
            'point' => $this->find()->func()->sum('point')
        ])->where($where)->first();

        return !empty($result['point']) ? intval($result['point']) : 0;
    }


    public function sumWithDrawPointOfCustomer($customer_id = null, $params = [])
    {
        if(empty($customer_id)) return 0;

        $create_from = !empty($params['create_from']) ? $params['create_from'] : null;
        $create_to = !empty($params['create_to']) ? $params['create_to'] : null;

        $where = [
            'customer_id' => $customer_id,
            'action' => 0, // 0 -> trừ , 1 -> cộng
            'action_type' => WITHDRAW,
            'point_type' => 1, //0 -> khuyến mại, 1 -> mặc định
            'status' => 1
        ];

        if(!empty($create_from)){
            $where['created >='] = $create_from;
        }

        if(!empty($create_to)){
            $where['created <='] = $create_to;
        }

        $result = $this->find()->select([
            'point' => $this->find()->func()->sum('point')
        ])->where($where)->first();

        return !empty($result['point']) ? intval($result['point']) : 0;
    }









}