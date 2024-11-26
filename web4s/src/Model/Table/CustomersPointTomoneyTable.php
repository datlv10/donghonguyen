<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Utility\Text;
use App\Model\Behavior\UnixTimestampBehavior;

class CustomersPointTomoneyTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('customers_point_tomoney');
        $this->setPrimaryKey('id');

        $this->addBehavior('UnixTimestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new'
                ]
            ]
        ]);

        $this->hasOne('Customers', [
            'className' => 'Customers',
            'foreignKey' => 'id',
            'bindingKey' => 'customer_id',
            'propertyName' => 'Customers'
        ]);

        $this->hasOne('CustomersBank', [
            'className' => 'CustomersBank',
            'foreignKey' => 'id',
            'bindingKey' => 'bank_id',
            'propertyName' => 'CustomersBank'
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');
        return $validator;
    }

    public function queryListPointTomoney($params = []) 
    {
        $table = TableRegistry::get('CustomersPointTomoney');

        // get info params
        $field = !empty($params[FIELD]) ? $params[FIELD] : SIMPLE_INFO;
        $get_customer = !empty($params['get_customer']) ? $params['get_customer'] : false;
        $get_bank = !empty($params['get_bank']) ? $params['get_bank'] : false;

        // sort
        $sort = !empty($params[SORT]) ? $params[SORT] : [];
        $sort_field = !empty($sort[FIELD]) ? $sort[FIELD] : null;
        $sort_type = !empty($sort[SORT]) && in_array($sort[SORT], [DESC, ASC]) ? $sort[SORT] : DESC;

        // filter
        $filter = !empty($params[FILTER]) ? $params[FILTER] : [];
        $status = isset($filter['status']) && $filter['status'] != '' ? $filter['status'] : null;
        $create_from = !empty($filter['create_from']) ? strtotime(date('Y-m-d 00:00:00', strtotime(str_replace('/', '-', $filter['create_from'])))) : null;
        $create_to = !empty($filter['create_to']) ? strtotime(date('Y-m-d 23:59:59', strtotime(str_replace('/', '-', $filter['create_to'])))) : null;
        $confirm_from = !empty($filter['confirm_from']) ? strtotime(date('Y-m-d 00:00:00', strtotime(str_replace('/', '-', $filter['confirm_from'])))) : null;
        $confirm_to = !empty($filter['confirm_to']) ? strtotime(date('Y-m-d 23:59:59', strtotime(str_replace('/', '-', $filter['confirm_to'])))) : null;
        $keyword = !empty($filter['keyword']) ? trim($filter['keyword']) : null;

        $customer_id = !empty($filter['customer_id']) ? intval($filter['customer_id']) : null;

        // fields select
        switch($field){
            case LIST_INFO:
                $fields = ['CustomersPointTomoney.id'];
            break;

            case FULL_INFO:
            case SIMPLE_INFO:
            default:
                $fields = ['CustomersPointTomoney.id', 'CustomersPointTomoney.customer_id', 'CustomersPointTomoney.bank_id', 'CustomersPointTomoney.point', 'CustomersPointTomoney.money', 'CustomersPointTomoney.note_admin', 'CustomersPointTomoney.note', 'CustomersPointTomoney.type', 'CustomersPointTomoney.status', 'CustomersPointTomoney.created', 'CustomersPointTomoney.time_confirm'];
            break;
        }

        $sort_string = 'CustomersPointTomoney.id DESC';
        if(!empty($params[SORT])){
            switch($sort_field){
                case 'id':
                    $sort_string = 'CustomersPointTomoney.id '. $sort_type;
                break;
            }
        }

        // filter by conditions
        $where = $contain = [];

        if(!empty($keyword)){
            $where['Customers.search_unicode LIKE'] = '%' . Text::slug(strtolower($keyword), ' ') . '%';
        }

        if(!empty($create_from)){
            $where['CustomersPointTomoney.created >='] = $create_from;
        }

        if(!empty($create_to)){
            $where['CustomersPointTomoney.created <='] = $create_to;
        }

        if(!empty($confirm_from)){
            $where['CustomersPointTomoney.time_confirm >='] = $confirm_from;
        }

        if(!empty($confirm_to)){
            $where['CustomersPointTomoney.time_confirm <='] = $confirm_to;
        }

        if (!empty($customer_id)) {
            $where['CustomersPointTomoney.customer_id'] = $customer_id;
        }

        if(!is_null($status)){
            $where['CustomersPointTomoney.status'] = $status;
        }

        $contain = [];
        if($get_customer){
            $contain[] = 'Customers';
            $where['Customers.deleted'] = 0;
            
            $fields[] = 'Customers.full_name';
            $fields[] = 'Customers.phone';
            $fields[] = 'Customers.email';
        }

        if($get_bank){
            $contain[] = 'CustomersBank';
            $where['CustomersBank.deleted'] = 0;
            
            $fields[] = 'CustomersBank.bank_key';
            $fields[] = 'CustomersBank.bank_name';
            $fields[] = 'CustomersBank.bank_branch';
            $fields[] = 'CustomersBank.account_number';
            $fields[] = 'CustomersBank.account_holder';
        }

        return $table->find()->contain($contain)->where($where)->select($fields)->order($sort_string);
    }

    public function formatDataPointTomoneyDetail($data = [])
    {
        if(empty($data)) return [];
        
        $result = [
            'id' => !empty($data['id']) ? intval($data['id']) : null,
            'customer_id' => !empty($data['customer_id']) ? intval($data['customer_id']) : null,
            'full_name' => null,
            'phone' => null,
            'email' => null,
            'bank_id' => !empty($data['bank_id']) ? intval($data['bank_id']) : null,
            'bank_key' => null,
            'bank_name' => null,
            'bank_branch' => null,
            'account_number' => null,
            'account_holder' => null,
            'point' => !empty($data['point']) ? $data['point'] : null,
            'money' => !empty($data['money']) ? $data['money'] : null,
            'note_admin' => !empty($data['note_admin']) ? $data['note_admin'] : null,
            'note' => !empty($data['note']) ? $data['note'] : null,
            'type' => !empty($data['type']) ? $data['type'] : null,
            'status' => isset($data['status']) ? $data['status'] : null,
            'created' => !empty($data['created']) ? $data['created'] : null,
            'time_confirm' => !empty($data['time_confirm']) ? $data['time_confirm'] : null
        ];

        if(!empty($data['Customers'])){
            $result['full_name'] = !empty($data['Customers']['full_name']) ? $data['Customers']['full_name'] : null;
            $result['phone'] = !empty($data['Customers']['phone']) ? $data['Customers']['phone'] : null;
            $result['email'] = !empty($data['Customers']['email']) ? $data['Customers']['email'] : null;
        }

        if(!empty($data['CustomersBank'])){
            $result['bank_key'] = !empty($data['CustomersBank']['bank_key']) ? $data['CustomersBank']['bank_key'] : null;
            $result['bank_name'] = !empty($data['CustomersBank']['bank_name']) ? $data['CustomersBank']['bank_name'] : null;
            $result['bank_branch'] = !empty($data['CustomersBank']['bank_branch']) ? $data['CustomersBank']['bank_branch'] : null;
            $result['account_number'] = !empty($data['CustomersBank']['account_number']) ? $data['CustomersBank']['account_number'] : null;
            $result['account_holder'] = !empty($data['CustomersBank']['account_holder']) ? $data['CustomersBank']['account_holder'] : null;
        }

        return $result;
    }

    public function sumTotalRequestProcessing($customer_id = null, $params = [])
    {
        if(empty($customer_id)) return [];

        $create_from = !empty($params['create_from']) ? $params['create_from'] : null;
        $create_to = !empty($params['create_to']) ? $params['create_to'] : null;

        $where = [
            'customer_id' => $customer_id,
            'status' => 2
        ];

        if(!empty($create_from)){
            $where['created >='] = $create_from;
        }

        if(!empty($create_to)){
            $where['created <='] = $create_to;
        }

        $result = $this->find()->select([
            'point' => $this->find()->func()->sum('point'),
            'money' => $this->find()->func()->sum('money'),
        ])->where($where)->first();
        
        return $result;
    }

}