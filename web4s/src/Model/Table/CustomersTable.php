<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use App\Model\Behavior\UnixTimestampBehavior;
use Cake\Core\Configure;
use Cake\Utility\Text;

class CustomersTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('customers');
        $this->setPrimaryKey('id');

        $this->addBehavior('UnixTimestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                    'updated' => 'existing'
                ]
            ]
        ]);

        $this->hasOne('DefaultAddress', [
            'className' => 'CustomersAddress',
            'foreignKey' => 'customer_id',
            'joinType' => 'LEFT',
            'conditions' => [
                'DefaultAddress.is_default' => 1
            ],
            'propertyName' => 'DefaultAddress'
        ]);

        // khi sử dụng contain với Choose Address thì trong Where phải thêm điều kiện với id_address
        $this->hasOne('ChooseAddress', [
            'className' => 'CustomersAddress',
            'foreignKey' => 'customer_id',
            'joinType' => 'LEFT',
            'propertyName' => 'ChooseAddress'
        ]);

        $this->hasOne('Account', [
            'className' => 'CustomersAccount',
            'foreignKey' => 'customer_id',
            'joinType' => 'LEFT',
            'propertyName' => 'Account'
        ]);

        $this->hasOne('CustomersPoint', [
            'className' => 'CustomersPoint',
            'foreignKey' => 'customer_id',
            'joinType' => 'LEFT',
            'propertyName' => 'CustomersPoint'
        ]);

        $this->hasMany('Addresses', [
            'className' => 'CustomersAddress',
            'foreignKey' => 'customer_id',
            'joinType' => 'LEFT',
            'propertyName' => 'Addresses'
        ]);

        $this->hasMany('CustomersBank', [
            'className' => 'CustomersBank',
            'foreignKey' => 'customer_id',
            'joinType' => 'LEFT',
            'propertyName' => 'CustomersBank'
        ]);

    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('full_name')
            ->maxLength('full_name', 255)
            ->requirePresence('full_name')
            ->notEmptyString('full_name');

        return $validator;
    }

    public function queryListCustomers($params = []) 
    {
        $table = TableRegistry::get('Customers');

        // get info params
        $field = !empty($params[FIELD]) ? $params[FIELD] : SIMPLE_INFO;

        // sort
        $sort = !empty($params[SORT]) ? $params[SORT] : [];
        $sort_field = !empty($sort[FIELD]) ? $sort[FIELD] : null;
        $sort_type = !empty($sort[SORT]) && in_array($sort[SORT], [DESC, ASC]) ? $sort[SORT] : DESC;

        // contain
        $get_point = !empty($params['get_point']) ? true : false;
        $get_account = !empty($params['get_account']) ? true : false;

        // filter
        $filter = !empty($params[FILTER]) ? $params[FILTER] : [];

        $keyword = !empty($filter['keyword']) ? trim($filter['keyword']) : null;
        $phone = !empty($filter['phone']) ? trim($filter['phone']) : null;
        $status = isset($filter['status']) && $filter['status'] != '' ? intval($filter['status']) : null;
        $staff_id = !empty($filter['staff_id']) ? $filter['staff_id'] : null;
        $city_id = !empty($filter['city_id']) ? $filter['city_id'] : null;
        $district_id = !empty($filter['district_id']) ? $filter['district_id'] : null;
        $is_partner_affiliate = isset($filter['is_partner_affiliate']) && $filter['is_partner_affiliate'] != '' ? intval($filter['is_partner_affiliate']) : null;

        // fields select
        switch($field){
            case FULL_INFO:
                $fields = ['Customers.id', 'Customers.code', 'Customers.member_id', 'Customers.full_name', 'Customers.email', 'Customers.phone', 'Customers.birthday', 'Customers.sex', 'Customers.note', 'Customers.status', 'Customers.created', 'Customers.updated', 'Customers.is_partner_affiliate', 'DefaultAddress.name', 'DefaultAddress.phone', 'DefaultAddress.address', 'DefaultAddress.country_id', 'DefaultAddress.city_id', 'DefaultAddress.district_id', 'DefaultAddress.ward_id', 'DefaultAddress.country_name', 'DefaultAddress.city_name', 'DefaultAddress.district_name', 'DefaultAddress.ward_name', 'DefaultAddress.full_address', 'DefaultAddress.zip_code', 'DefaultAddress.is_default'];
            break;

            case LIST_INFO:
                $fields = ['Customers.id', 'Customers.full_name'];
            break;

            case SIMPLE_INFO:
            default:
                $fields = ['Customers.id', 'Customers.full_name', 'Customers.email', 'Customers.phone', 'Customers.code', 'Customers.status', 'Customers.birthday', 'Customers.sex', 'Customers.is_partner_affiliate', 'DefaultAddress.name', 'DefaultAddress.phone', 'DefaultAddress.address', 'DefaultAddress.country_id', 'DefaultAddress.city_id', 'DefaultAddress.district_id', 'DefaultAddress.ward_id', 'DefaultAddress.country_name', 'DefaultAddress.city_name', 'DefaultAddress.district_name', 'DefaultAddress.ward_name', 'DefaultAddress.full_address'];
            break;
        }

        $sort_string = 'Customers.id DESC';
        if(!empty($params[SORT])){
            switch($sort_field){
                case 'id':
                    $sort_string = 'Customers.id '. $sort_type;
                break;

                case 'full_name':
                    $sort_string = 'Customers.full_name '. $sort_type .', Customers.id DESC';
                break;

                case 'email':
                    $sort_string = 'Customers.email '. $sort_type .', Customers.id DESC';
                break;

                case 'status':
                    $sort_string = 'Customers.status '. $sort_type .', Customers.id DESC';
                break;        
            }
        }

        // filter by conditions
        $where = ['Customers.deleted' => 0];    

        if(!empty($keyword)){
            $where['Customers.search_unicode LIKE'] = '%' . Text::slug(strtolower($keyword), ' ') . '%';
        }

        if(!is_null($status)){
            $where['Customers.status'] = $status;
        }

        if(!empty($is_partner_affiliate)){
            $where['Customers.is_partner_affiliate'] = $is_partner_affiliate;   
        }

        if(!is_null($status)){
            $where['Customers.status'] = $status;
        }

        $contain = ['DefaultAddress'];

        if(!empty($city_id)){
            $where['DefaultAddress.city_id'] = $city_id;
        }

        if(!empty($district_id)){
            $where['DefaultAddress.district_id'] = $district_id;
        }

        if($get_account){
            $contain[] = 'Account';
            $fields[] = 'Account.id';
            $fields[] = 'Account.username';
            $fields[] = 'Account.status';
        }

        if($get_point){
            $contain[] = 'CustomersPoint';
            $fields[] = 'CustomersPoint.point';
            $fields[] = 'CustomersPoint.point_promotion';
            $fields[] = 'CustomersPoint.expiration_time';
        }

        return $table->find()->contain($contain)->where($where)->select($fields)->order($sort_string);
    }

    public function getDetailCustomer($id = null, $params = [])
    {
        $result = [];
        if(empty($id)) return [];        

        $get_user = !empty($params['get_user']) ? true : false;
        $get_account = !empty($params['get_account']) ? true : false;
        $get_default_address = !empty($params['get_default_address']) ? true : false;
        $get_list_address = !empty($params['get_list_address']) ? true : false;
        $get_point = !empty($params['get_point']) ? true : false;
        $get_bank = !empty($params['get_bank']) ? true : false;

        $address_id = !empty($params['address_id']) ? intval($params['address_id']) : null;

        $where = [
            'Customers.id' => $id,
            'Customers.deleted' => 0
        ];

        $contain = [];

        if($get_user){
            $contain[] = 'User';
        }

        if($get_account){
            $contain[] = 'Account';
        }

        if($get_default_address){
            $contain[] = 'DefaultAddress';
        }

        if($get_list_address){
            $contain[] = 'Addresses';
        }

        if($get_point){
            $contain[] = 'CustomersPoint';
        }

        if($get_bank){
            $contain[] = 'CustomersBank';
        }
        
        if(!empty($address_id)){
            $contain[] = 'ChooseAddress';
            $where['ChooseAddress.id'] = $address_id;
        }

        $result = TableRegistry::get('Customers')->find()->contain($contain)->where($where)->first();

        return $result;
    }

    public function formatDataCustomerDetail($data = [])
    {
        if(empty($data)) return [];
        
        $result = [
            'id' => !empty($data['id']) ? intval($data['id']) : null,
            'customer_id' => !empty($data['id']) ? intval($data['id']) : null,
            'is_partner_affiliate' => !empty($data['is_partner_affiliate']) ? intval($data['is_partner_affiliate']) : 0,
            'level_partner_affiliate' => isset($data['level_partner_affiliate']) ? intval($data['level_partner_affiliate']) : 0,
            'code' => !empty($data['code']) ? $data['code'] : null,
            'full_name' => !empty($data['full_name']) ? $data['full_name'] : null,
            'email' => !empty($data['email']) ? $data['email'] : null,
            'phone' => !empty($data['phone']) ? $data['phone'] : null,
            'birthday' => !empty($data['birthday']) ? date('d/m/Y', $data['birthday']) : null,
            'avatar' => !empty($data['avatar']) ? $data['avatar'] : null,
            'sex' => !empty($data['sex']) ? $data['sex'] : null,
            'note' => !empty($data['note']) ? json_decode($data['note'], true) : null,
            'staff_name' => !empty($data['staff_name']) ? $data['staff_name'] : null,
            'status' => !empty($data['status']) ? 1 : 0,
            'created' => !empty($data['created']) ? date('H:i - d/m/Y', $data['created']) : null,
            'updated' => !empty($data['updated']) ? date('H:i - d/m/Y', $data['updated']) : null,
            'address_id' => null,
            'address_name' => null,
            'address' =>  null,
            'full_address' => null,
            'country_id' => null,
            'city_id' => null,
            'district_id' => null,
            'ward_id' => null,
            'country_name' => null,
            'city_name' => null,
            'district_name' => null,
            'ward_name' => null,
            'zip_code' => null,
        ];

        $addresses = [];
        if(!empty($data['Addresses'])){
            foreach($data['Addresses'] as $k => $customer_address){

                $city_name = $district_name = $ward_name = $address = null;

                if(!empty($customer_address->address)){
                    $address = $customer_address->address;
                }

                if(!empty($customer_address->ward_name)){
                    $ward_name = $customer_address->ward_name;
                }

                if(!empty($customer_address->district_name)){
                    $district_name = $customer_address->district_name;
                }

                if(!empty($customer_address->city_name)){
                    $city_name = $customer_address->city_name;
                }

                $full_address = !empty($customer_address->full_address) ? $customer_address->full_address : null;
                $id = !empty($customer_address->id) ? $customer_address->id : null;
                $customer_id = !empty($customer_address->customer_id) ? $customer_address->customer_id : null;                
                $address_name = !empty($customer_address->name) ? $customer_address->name : null;
                $phone = !empty($customer_address->phone) ? $customer_address->phone : null;
                $address = !empty($customer_address->address) ? $customer_address->address : null;
                $country_id = !empty($customer_address->country_id) ? intval($customer_address->country_id) : null;
                $city_id = !empty($customer_address->city_id) ? intval($customer_address->city_id) : null;
                $district_id = !empty($customer_address->district_id) ? intval($customer_address->district_id) : null;
                $ward_id = !empty($customer_address->ward_id) ? intval($customer_address->ward_id) : null;
                $country_name = !empty($customer_address->country_name) ? $customer_address->country_name : null;
                $zip_code = !empty($customer_address->zip_code) ? $customer_address->zip_code : null;
                $is_default = !empty($customer_address->is_default) ? $customer_address->is_default : null;

                $is_default = !empty($customer_address->is_default) ? 1 : 0;                    
                if(!empty($is_default)){
                    $result['address_id'] = $id;
                    $result['address_name'] = $address_name;
                    // $result['phone'] = $phone;
                    $result['address'] = $address;
                    $result['country_id'] = $country_id;
                    $result['city_id'] = $city_id;
                    $result['district_id'] = $district_id;
                    $result['ward_id'] = $ward_id;
                    $result['country_name'] = $country_name;
                    $result['city_name'] = $city_name;
                    $result['district_name'] = $district_name;
                    $result['ward_name'] = $ward_name;
                    $result['full_address'] = $full_address;
                    $result['zip_code'] = $zip_code;
                }

                $addresses[] = [
                    'id' => $id,
                    'address_id' => $id,
                    'customer_id' => $customer_id,
                    'address_name' => $address_name,
                    'phone' => $phone,
                    'address' => $address,
                    'country_id' => $country_id,
                    'city_id' => $city_id,
                    'district_id' => $district_id,
                    'ward_id' => $ward_id,
                    'country_name' => $country_name,
                    'city_name' => $city_name,
                    'district_name' => $district_name,
                    'ward_name' => $ward_name,
                    'full_address' => $full_address,
                    'zip_code' => $zip_code,
                    'is_default' => $is_default
                ];
            }
        }
        $result['addresses'] = $addresses;

        if(!empty($data['DefaultAddress'])){
            $default_address = $data['DefaultAddress'];

            $result['address_id'] = !empty($default_address->id) ? $default_address->id : null;
            $result['address_name'] = !empty($default_address->name) ? $default_address->name : null;

            // $result['phone'] = !empty($default_address->phone) ? $default_address->phone : null;

            $result['address'] = !empty($default_address->address) ? $default_address->address : null;
            $result['country_id'] = !empty($default_address->country_id) ? intval($default_address->country_id) : null;
            $result['city_id'] = !empty($default_address->city_id) ? intval($default_address->city_id) : null;
            $result['district_id'] = !empty($default_address->district_id) ? intval($default_address->district_id) : null;
            $result['ward_id'] = !empty($default_address->ward_id) ? intval($default_address->ward_id) : null;
            $result['country_name'] = !empty($default_address->country_name) ? $default_address->country_name : null;
            $result['city_name'] = !empty($default_address->city_name) ? $default_address->city_name : null;
            $result['district_name'] = !empty($default_address->district_name) ? $default_address->district_name : null;
            $result['ward_name'] = !empty($default_address->ward_name) ? $default_address->ward_name : null;
            $result['full_address'] = !empty($default_address->full_address) ? $default_address->full_address : null;
            $result['zip_code'] = !empty($default_address->zip_code) ? $default_address->zip_code : null;
        }
        
        if(!empty($data['ChooseAddress'])){
            $choose_address = $data['ChooseAddress'];

            $result['address_id'] = !empty($choose_address->id) ? $choose_address->id : null;
            $result['address_name'] = !empty($choose_address->name) ? $choose_address->name : null;
            $result['phone'] = !empty($choose_address->phone) ? $choose_address->phone : null;
            $result['address'] = !empty($choose_address->address) ? $choose_address->address : null;
            $result['country_id'] = !empty($choose_address->country_id) ? intval($choose_address->country_id) : null;
            $result['city_id'] = !empty($choose_address->city_id) ? intval($choose_address->city_id) : null;
            $result['district_id'] = !empty($choose_address->district_id) ? intval($choose_address->district_id) : null;
            $result['ward_id'] = !empty($choose_address->ward_id) ? intval($choose_address->ward_id) : null;
            $result['country_name'] = !empty($choose_address->country_name) ? $choose_address->country_name : null;
            $result['city_name'] = !empty($choose_address->city_name) ? $choose_address->city_name : null;
            $result['district_name'] = !empty($choose_address->district_name) ? $choose_address->district_name : null;
            $result['ward_name'] = !empty($choose_address->ward_name) ? $choose_address->ward_name : null;
            $result['full_address'] = !empty($choose_address->full_address) ? $choose_address->full_address : null;
            $result['zip_code'] = !empty($choose_address->zip_code) ? $choose_address->zip_code : null;
        }

        if(!empty($data['Account'])){
            $account = $data['Account'];
            $result['account_id'] = !empty($account->id) ? $account->id : null;
            $result['username'] = !empty($account->username) ? $account->username : null;
            $result['account_status'] = isset($account->status) ? intval($account->status) : null;
        }

        if(!empty($data['CustomersPoint'])){
            $customer_point = $data['CustomersPoint'];
            $result['point'] = !empty($customer_point->point) ? intval($customer_point->point) : null;
            $result['point_promotion'] = !empty($customer_point->point_promotion) ? intval($customer_point->point_promotion) : null;
            $result['expiration_time'] = !empty($customer_point->expiration_time) ? intval($customer_point->expiration_time) : null;
        }

        $bank = [];
        if(!empty($data['CustomersBank'])){
            foreach($data['CustomersBank'] as $k => $customer_bank){
                $bank[] = [
                    'id' => !empty($customer_bank->id) ? $customer_bank->id : null,
                    'customer_id' => !empty($customer_bank->customer_id) ? $customer_bank->customer_id : null,
                    'bank_key' => !empty($customer_bank->bank_key) ? $customer_bank->bank_key : null,
                    'bank_name' => !empty($customer_bank->bank_name) ? $customer_bank->bank_name : null,
                    'bank_branch' => !empty($customer_bank->bank_branch) ? $customer_bank->bank_branch : null,
                    'account_number' => !empty($customer_bank->account_number) ? $customer_bank->account_number : null,
                    'account_holder' => !empty($customer_bank->account_holder) ? $customer_bank->account_holder : null,
                    'is_default' => !empty($customer_bank->is_default) ? $customer_bank->is_default : null
                ];
            }
        }
        $result['bank'] = $bank;
        
        return $result;
    }

    public function checkPhoneExist($phone = null, $customer_id = null)
    {
        if(empty($phone)) return false;

        $where = [
            'Customers.deleted' => 0,
            'Customers.phone' => $phone
        ];
        
        if(!empty($customer_id)){
            $where['Customers.id !='] = $customer_id;
        }

        $customer = TableRegistry::get('Customers')->find()->where($where)->first();
        return !empty($customer->id) ? true : false;
    }

    public function checkEmailExist($email = null, $customer_id = null)
    {
        if(empty($email)) return false;

        $where = [
            'Customers.deleted' => 0,
            'Customers.email' => $email
        ];
        
        if(!empty($customer_id)){
            $where['Customers.id !='] = $customer_id;
        }

        $customer = TableRegistry::get('Customers')->find()->where($where)->first();
        return !empty($customer->id) ? true : false;
    }

    public function checkEmailAccountExist($email = null, $customer_id = null)
    {
        if(empty($email)) return false;

        $where = [
            'Customers.deleted' => 0,
            'Customers.email' => $email,
            'Account.id >' => 0

        ];
        
        if(!empty($customer_id)){
            $where['Customers.id !='] = $customer_id;
        }

        $customer = TableRegistry::get('Customers')->find()->contain(['Account'])->where($where)->first();
        return !empty($customer->id) ? true : false;
    }
}