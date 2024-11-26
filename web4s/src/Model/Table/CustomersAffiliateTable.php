<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Utility\Hash;

class CustomersAffiliateTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('customers_affiliate');
        $this->setPrimaryKey('id');

        $this->hasOne('Customers', [
            'className' => 'Customers',
            'foreignKey' => 'id',
            'bindingKey' => 'customer_id',
            'propertyName' => 'Customers'
        ]);

        $this->hasOne('CustomersAffiliateRequest', [
            'className' => 'AffiliateRequest',
            'foreignKey' => 'customer_id',
            'bindingKey' => 'customer_id',
            'propertyName' => 'AffiliateRequest'
        ]);
    }

    public function queryListAffiliate($params = []) 
    {
        $table = TableRegistry::get('CustomersAffiliate');

        // get info params
        $field = !empty($params[FIELD]) ? $params[FIELD] : SIMPLE_INFO;

        // sort
        $sort = !empty($params[SORT]) ? $params[SORT] : [];
        $sort_field = !empty($sort[FIELD]) ? $sort[FIELD] : null;
        $sort_type = !empty($sort[SORT]) && in_array($sort[SORT], [DESC, ASC]) ? $sort[SORT] : DESC;

        // filter
        $filter = !empty($params[FILTER]) ? $params[FILTER] : [];
        $customer_id = !empty($filter['customer_id']) ? intval($filter['customer_id']) : null;
        $keyword = !empty($filter['keyword']) ? trim($filter['keyword']) : null;

        // fields select
        switch($field){
            case LIST_INFO:
                $fields = ['CustomersAffiliate.id', 'Customers.full_name'];
            break;

            case FULL_INFO:
            case SIMPLE_INFO:
            default:
                $fields = ['Customers.full_name', 'Customers.level_partner_affiliate', 'Customers.code', 'CustomersAffiliate.id', 'CustomersAffiliate.customer_id', 'CustomersAffiliate.number_referral', 'CustomersAffiliate.number_order_success', 'CustomersAffiliate.total_order_success', 'CustomersAffiliate.number_order_failed', 'CustomersAffiliate.total_order_failed', 'CustomersAffiliate.total_point'];
            break;
        }

        $sort_string = 'CustomersAffiliate.id DESC';
        if(!empty($params[SORT])){
            switch($sort_field){
                case 'id':
                    $sort_string = 'CustomersAffiliate.id '. $sort_type;
                break;

                case 'status':
                    $sort_string = 'CustomersAffiliate.status '. $sort_type .', CustomersAffiliate.id DESC';
                break;        
            }
        }
        $contain = ['Customers'];

        // filter by conditions
        $where = [
            'Customers.deleted' => 0,
            'Customers.is_partner_affiliate' => 1,
        ];    

        if(!empty($customer_id)){
            $where['CustomersAffiliate.customer_id'] = $customer_id;
        }

        

        return $table->find()->contain($contain)->where($where)->select($fields)->group('CustomersAffiliate.id')->order($sort_string);
    }

    public function formatDataAffiliateDetail($data = [])
    {
        if(empty($data)) return [];
        
        $settings = TableRegistry::get('Settings')->getSettingWebsite();

        $affiliate_setting = !empty($settings['affiliate']) ? $settings['affiliate'] : [];
        $commissions = !empty($affiliate_setting['commissions']) ? json_decode($affiliate_setting['commissions'], true) : [];
        $commissions = Hash::combine($commissions, '{n}.key', '{n}');

        $point_setting = !empty($settings['point']) ? $settings['point'] : [];
        $point_to_money = !empty($point_setting['point_to_money']) ? floatval($point_setting['point_to_money']) : 0;
        $total_point = !empty($data['total_point']) ? intval($data['total_point']) : 0;
        $result = [
            'id' => !empty($data['id']) ? intval($data['id']) : null,
            'customer_id' => !empty($data['customer_id']) ? intval($data['customer_id']) : null,
            'number_referral' => !empty($data['number_referral']) ? intval($data['number_referral']) : 0,
            'number_order_success' => !empty($data['number_order_success']) ? intval($data['number_order_success']) : 0,
            'total_order_success' => !empty($data['total_order_success']) ? floatval($data['total_order_success']) : 0,
            'number_order_failed' => !empty($data['number_order_failed']) ? intval($data['number_order_failed']) : 0,
            'total_order_failed' => !empty($data['total_order_failed']) ? floatval($data['total_order_failed']) : 0,
            'total_point' => $total_point,
            'total_point_to_money' => 0,
            'full_name' => !empty($data['Customers']['full_name']) ? $data['Customers']['full_name'] : null,
            'code' => !empty($data['Customers']['code']) ? $data['Customers']['code'] : null,
            'level' => null,
            'profit' => null,
            'image' => null,
            'source' => null
        ];

        if(!empty($point_to_money) && !empty($total_point)) {
            $result['total_point_to_money'] = $total_point * $point_to_money;
        }

        if(!empty($commissions)){
            $key_level = isset($data['Customers']['level_partner_affiliate']) ? intval($data['Customers']['level_partner_affiliate']) : 0;
            $result['level'] = isset($commissions[$key_level]['name']) ? $commissions[$key_level]['name']  : null;
            $result['profit'] = !empty($commissions[$key_level]['profit']) ? $commissions[$key_level]['profit']  : null;
            $result['image'] = !empty($commissions[$key_level]['image']) ? $commissions[$key_level]['image']  : null;
            $result['source'] = !empty($commissions[$key_level]['source']) ? $commissions[$key_level]['source']  : null;
        }
        
        return $result;
    }

    public function checkLevelForPartner($customer_id = null)
    {
        if (empty($customer_id)) return 0;

        // lấy thông tin cấu hình thứ hạng
        $settings = TableRegistry::get('Settings')->getSettingWebsite();
        $setting_affiliate = !empty($settings['affiliate']) ? $settings['affiliate'] : [];
        $commissions = !empty($setting_affiliate['commissions']) ? json_decode($setting_affiliate['commissions'], true) : [];

        if (empty($commissions)) return 0;

        // thông tin của đối tác
        $affiliate_info = TableRegistry::get('CustomersAffiliate')->find()->where(['customer_id' => $customer_id])->first();
        
        $all_number_referral = !empty($affiliate_info['number_referral']) ? intval($affiliate_info['number_referral']) : 0;
        $all_total_order_success = !empty($affiliate_info['total_order_success']) ? floatval($affiliate_info['total_order_success']) : 0;

        $result = 0; // cấp độ mặc định
        foreach ($commissions as $key => $value) {
            $number_referral = !empty($value['number_referral']) ? intval($value['number_referral']) : 0;
            $total_order = !empty($value['total_order']) ? floatval($value['total_order']) : 0;

            if ($all_number_referral > $number_referral && $all_total_order_success > $total_order) {
                $result = !empty($value['key']) ? intval($value['key']) : 0;
            }
        }

        return $result;
    }
}