<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Utility\Text;

class CustomersAffiliateRequestTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('customers_affiliate_request');
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
    }

    public function queryListAffiliateRequest($params = []) 
    {
        $table = TableRegistry::get('CustomersAffiliateRequest');

        // get info params
        $field = !empty($params[FIELD]) ? $params[FIELD] : SIMPLE_INFO;

        // sort
        $sort = !empty($params[SORT]) ? $params[SORT] : [];
        $sort_field = !empty($sort[FIELD]) ? $sort[FIELD] : null;
        $sort_type = !empty($sort[SORT]) && in_array($sort[SORT], [DESC, ASC]) ? $sort[SORT] : DESC;

        // filter
        $filter = !empty($params[FILTER]) ? $params[FILTER] : [];

        $keyword = !empty($filter['keyword']) ? trim($filter['keyword']) : null;
        $status = isset($filter['status']) && $filter['status'] != '' ? intval($filter['status']) : null;

        // fields select
        switch($field){
            case LIST_INFO:
                $fields = ['CustomersAffiliateRequest.id', 'Customers.full_name'];
            break;

            case FULL_INFO:
            case SIMPLE_INFO:
            default:
                $fields = ['Customers.full_name', 'Customers.phone', 'Customers.email', 'CustomersAffiliateRequest.id', 'CustomersAffiliateRequest.customer_id', 'CustomersAffiliateRequest.identity_card', 'CustomersAffiliateRequest.bank', 'CustomersAffiliateRequest.status', 'CustomersAffiliateRequest.created'];
            break;
        }

        $sort_string = 'CustomersAffiliateRequest.id DESC';
        if(!empty($params[SORT])){
            switch($sort_field){
                case 'id':
                    $sort_string = 'CustomersAffiliateRequest.id '. $sort_type;
                break;

                case 'status':
                    $sort_string = 'CustomersAffiliateRequest.status '. $sort_type .', CustomersAffiliateRequest.id DESC';
                break;        
            }
        }

        // filter by conditions
        $where = [
            'Customers.deleted' => 0
        ];    

        if(!empty($keyword)){
            $where['Customers.search_unicode LIKE'] = '%' . Text::slug(strtolower($keyword), ' ') . '%';
        }

        if(!is_null($status)){
            $where['CustomersAffiliateRequest.status'] = $status;
        }

        $contain = ['Customers'];

        return $table->find()->contain($contain)->where($where)->select($fields)->group('CustomersAffiliateRequest.id')->order($sort_string);
    }

    public function formatDataAffiliateRequestDetail($data = [])
    {
        if(empty($data)) return [];
        
        $result = [
            'id' => !empty($data['id']) ? intval($data['id']) : null,
            'customer_id' => !empty($data['customer_id']) ? intval($data['customer_id']) : null,
            'identity_card_id' => null,
            'identity_card_date' => null,
            'identity_card_name' => null,
            'identity_card_where' => null,
            'bank_name' => null,
            'bank_branch' => null,
            'account_holder' => null,
            'account_number' => null,
            'created' => !empty($data['created']) ? intval($data['created']) : null,
            'status' => !empty($data['status']) ? $data['status'] : 0,
        ];

        if(!empty($data['Customers'])){
            $customer = $data['Customers'];
            $result['full_name'] = !empty($customer->full_name) ? $customer->full_name : null;
            $result['phone'] = !empty($customer->phone) ? $customer->phone : null;
            $result['email'] = !empty($customer->email) ? $customer->email : null;
        }

        if(!empty($data['identity_card'])){
            $identity_card = json_decode($data['identity_card'], true);

            $result['identity_card_id'] = !empty($identity_card['identity_card_id']) ? $identity_card['identity_card_id'] : null;
            $result['identity_card_date'] = !empty($identity_card['identity_card_date']) ? $identity_card['identity_card_date'] : null;
            $result['identity_card_name'] = !empty($identity_card['identity_card_name']) ? $identity_card['identity_card_name'] : null;
            $result['identity_card_where'] = !empty($identity_card['identity_card_where']) ? $identity_card['identity_card_where'] : null;
        }

        if(!empty($data['bank'])){
            $bank = json_decode($data['bank'], true);

            $result['bank_name'] = !empty($bank['bank_name']) ? $bank['bank_name'] : null;
            $result['bank_branch'] = !empty($bank['bank_branch']) ? $bank['bank_branch'] : null;
            $result['account_holder'] = !empty($bank['account_holder']) ? $bank['account_holder'] : null;
            $result['account_number'] = !empty($bank['account_number']) ? $bank['account_number'] : null;
        }
        
        return $result;
    }
}