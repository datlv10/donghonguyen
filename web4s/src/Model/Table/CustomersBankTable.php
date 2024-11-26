<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

class CustomersBankTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('customers_bank');
        $this->setPrimaryKey('id');

        $this->hasOne('Customers', [
            'className' => 'Customers',
            'foreignKey' => 'id',
            'bindingKey' => 'customer_id',
            'propertyName' => 'Customers'
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');
        return $validator;
    }

    public function queryListCustomersBank($params = []) 
    {
        $table = TableRegistry::get('CustomersBank');

        // get info params
        $field = !empty($params[FIELD]) ? $params[FIELD] : SIMPLE_INFO;
        $get_customer = !empty($params['get_customer']) ? $params['get_customer'] : false;

        // sort
        $sort = !empty($params[SORT]) ? $params[SORT] : [];
        $sort_field = !empty($sort[FIELD]) ? $sort[FIELD] : null;
        $sort_type = !empty($sort[SORT]) && in_array($sort[SORT], [DESC, ASC]) ? $sort[SORT] : DESC;

        // filter
        $filter = !empty($params[FILTER]) ? $params[FILTER] : [];
        $customer_id = !empty($filter['customer_id']) ? intval($filter['customer_id']) : null;

        // fields select
        switch($field){
            case LIST_INFO:
                $fields = ['CustomersBank.id', 'CustomersBank.bank_name', 'CustomersBank.account_number'];
            break;

            case FULL_INFO:
            case SIMPLE_INFO:
            default:
                $fields = ['CustomersBank.id', 'CustomersBank.customer_id', 'CustomersBank.bank_key', 'CustomersBank.bank_name', 'CustomersBank.bank_branch', 'CustomersBank.account_number', 'CustomersBank.account_holder', 'CustomersBank.is_default'];
            break;
        }

        $sort_string = 'CustomersBank.id DESC';
        if(!empty($params[SORT])){
            switch($sort_field){
                case 'id':
                    $sort_string = 'CustomersBank.id '. $sort_type;
                break;
            }
        }

        // filter by conditions
        
        $where['CustomersBank.deleted'] = 0;  
        if (!empty($customer_id)) {
            $where['CustomersBank.customer_id'] = $customer_id;
        }

        $contain = [];
        if($get_customer){
            $contain[] = 'Customers';
            $where['Customers.deleted'] = 0;
            
            $fields[] = 'Customers.full_name';
        }

        return $table->find()->contain($contain)->where($where)->select($fields)->order($sort_string);
    }

    public function formatDataCustomersBankDetail($data = [])
    {
        if(empty($data)) return [];
        $result = [
            'id' => !empty($data['id']) ? intval($data['id']) : null,
            'customer_id' => !empty($data['customer_id']) ? $data['customer_id'] : null,
            'bank_key' => !empty($data['bank_key']) ? $data['bank_key'] : null,
            'bank_name' => !empty($data['bank_name']) ? $data['bank_name'] : null,
            'bank_branch' => !empty($data['bank_branch']) ? $data['bank_branch'] : null,
            'account_number' => !empty($data['account_number']) ? $data['account_number'] : null,
            'account_holder' => !empty($data['account_holder']) ? $data['account_holder'] : null
        ];

        if(!empty($data['Customers'])){
            $result['full_name'] = !empty($data['Customers']['full_name']) ? $data['Customers']['full_name'] : null;
        }

        return $result;
    }
}