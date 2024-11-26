<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use App\Model\Behavior\UnixTimestampBehavior;
use Cake\Core\Configure;
use Cake\Utility\Text;

class CustomersPointTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('customers_point');
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

        $this->hasMany('Addresses', [
            'className' => 'CustomersAddress',
            'foreignKey' => 'customer_id',
            'bindingKey' => 'customer_id',
            'joinType' => 'LEFT',
            'propertyName' => 'Addresses'
        ]);

    }

    public function queryListCustomersPoint($params = []) 
    {
        $table = TableRegistry::get('CustomersPoint');

        // get info params
        $field = !empty($params[FIELD]) ? $params[FIELD] : SIMPLE_INFO;

        // sort
        $sort = !empty($params[SORT]) ? $params[SORT] : [];
        $sort_field = !empty($sort[FIELD]) ? $sort[FIELD] : null;
        $sort_type = !empty($sort[SORT]) && in_array($sort[SORT], [DESC, ASC]) ? $sort[SORT] : DESC;

        // contain
        $get_contact = !empty($params['get_contact']) ? true : false;

        // filter
        $filter = !empty($params[FILTER]) ? $params[FILTER] : [];

        $keyword = !empty($filter['keyword']) ? trim($filter['keyword']) : null;
        $status = isset($filter['status']) && $filter['status'] != '' ? intval($filter['status']) : null;
        $staff_id = !empty($filter['staff_id']) ? $filter['staff_id'] : null;
        $city_id = !empty($filter['city_id']) ? $filter['city_id'] : null;
        $district_id = !empty($filter['district_id']) ? $filter['district_id'] : null;

        // fields select
        switch($field){
            case LIST_INFO:
                $fields = ['CustomersPoint.id', 'Customers.full_name'];
            break;

            case FULL_INFO:
            case SIMPLE_INFO:
            default:
                $fields = ['CustomersPoint.id', 'Customers.full_name', 'Customers.phone', 'CustomersPoint.customer_id', 'CustomersPoint.point', 'CustomersPoint.point_promotion', 'CustomersPoint.expiration_time'];
            break;
        }

        $sort_string = 'CustomersPoint.id DESC';
        if(!empty($params[SORT])){
            switch($sort_field){
                case 'id':
                    $sort_string = 'CustomersPoint.id '. $sort_type;
                break;

                case 'point':
                    $sort_string = 'CustomersPoint.point '. $sort_type .', CustomersPoint.id DESC';
                break;

                case 'point_promotion':
                    $sort_string = 'CustomersPoint.point_promotion '. $sort_type .', CustomersPoint.id DESC';
                break;

                case 'expiration_time':
                    $sort_string = 'CustomersPoint.expiration_time '. $sort_type .', CustomersPoint.id DESC';
                break;        
            }
        }

        // filter by conditions
        $where = ['Customers.deleted' => 0];    

        if(!empty($keyword)){
            $where['Customers.search_unicode LIKE'] = '%' . Text::slug(strtolower($keyword), ' ') . '%';
        }

        $contain = ['Customers'];

        return $table->find()->contain($contain)->where($where)->select($fields)->group('CustomersPoint.id')->order($sort_string);
    }

    public function getDetailCustomerPoint($id = null, $params = [])
    {
        $result = [];
        if(empty($id)) return [];        

        $where = [
            'CustomersPoint.id' => $id
        ];

        $contain = ['Customers'];

        $result = TableRegistry::get('CustomersPoint')->find()->contain($contain)->where($where)->first();

        return $result;
    }

    public function formatDataCustomerPointDetail($data = [])
    {
        if(empty($data)) return [];
       
        $result = [
            'id' => !empty($data['id']) ? intval($data['id']) : null,
            'customer_id' => !empty($data['customer_id']) ? intval($data['customer_id']) : null,
            'point' => !empty($data['point']) ? $data['point'] : 0,
            'point_promotion' => !empty($data['point_promotion']) ? $data['point_promotion'] : 0,
            'expiration_time' => !empty($data['expiration_time']) ? $data['expiration_time'] : null,

            'code' => !empty($data['Customers']['code']) ? $data['Customers']['code'] : null,
            'full_name' => !empty($data['Customers']['full_name']) ? $data['Customers']['full_name'] : null,
            'email' => !empty($data['Customers']['email']) ? $data['Customers']['email'] : null,
            'sex' => !empty($data['Customers']['sex']) ? $data['Customers']['sex'] : null,
            'phone' => !empty($data['Customers']['phone']) ? $data['Customers']['phone'] : null
        ];
        
        return $result;
    }

    public function getInfoCustomerPoint($customer_id = null, $options = [])
    {
        if(empty($customer_id)) return [];
        $where = [
            'CustomersPoint.customer_id' => $customer_id
        ];

        $result = TableRegistry::get('CustomersPoint')->find()->where($where)->first();

        return $result;
    }

    public function getPointRefund($total = null)
    {
        if(empty($total)) return 0;

        $settings = TableRegistry::get('Settings')->getSettingWebsite();
        $point_setting = !empty($settings['point']) ? $settings['point'] : [];

        $apply_refund_order = !empty($point_setting['apply_refund_order']) ? true : false;
        $condition_refund_order = !empty($point_setting['condition_refund_order']) ? intval($point_setting['condition_refund_order']) : 0;
        $type_refund = !empty($point_setting['type_refund']) ? $point_setting['type_refund'] : null;
        $value_refund = !empty($point_setting['value_refund']) ? floatval($point_setting['value_refund']) : 0;
        $point_to_money = !empty($point_setting['point_to_money']) ? floatval($point_setting['point_to_money']) : 0;

        if(!$apply_refund_order) return 0;
        if($total < $condition_refund_order) return 0;
        if(!in_array($type_refund, [POINT, PERCENT])) return 0;
        if($value_refund < 0) return 0;

        $result = $value_refund;
        if($type_refund == PERCENT){
            $result = ($total / $point_to_money) / 100 * $value_refund;
        }

        $result = round($result);
        return !empty($result) ? $result : 1;
    }

}