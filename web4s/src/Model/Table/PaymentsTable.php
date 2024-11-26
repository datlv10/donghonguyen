<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use App\Model\Behavior\UnixTimestampBehavior;
use Cake\Utility\Text;

class PaymentsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('payments');

        $this->setPrimaryKey('id');

        $this->addBehavior('UnixTimestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                    'updated' => 'existing'
                ]
            ]
        ]);    
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        return $validator;
    }

    public function queryListPayments($params = []) 
    {
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
        $order_id = !empty($filter['order_id']) ? intval($filter['order_id']) : null;
        $foreign_id = !empty($filter['foreign_id']) ? intval($filter['foreign_id']) : null;
        $foreign_type = !empty($filter['foreign_type']) ? $filter['foreign_type'] : null;
        $type = isset($filter['type']) && $filter['type'] != '' ? intval($filter['type']) : null;
        $payment_method = !empty($filter['payment_method']) ? trim($filter['payment_method']) : null;
        $price_from = !empty($filter['price_from']) ? floatval(str_replace(',', '', $filter['price_from'])) : null;
        $price_to = !empty($filter['price_to']) ? floatval(str_replace(',', '', $filter['price_to'])) : null;
        $create_from = !empty($filter['create_from']) ? strtotime(date('Y-m-d 00:00:00', strtotime(str_replace('/', '-', $filter['create_from'])))) : null;
        $create_to = !empty($filter['create_to']) ? strtotime(date('Y-m-d 23:59:59', strtotime(str_replace('/', '-', $filter['create_to'])))) : null;
        $payment_from = !empty($filter['payment_from']) ? strtotime(date('Y-m-d', strtotime(str_replace('/', '-', $filter['payment_from'])))) : null;
        $payment_to = !empty($filter['payment_to']) ? strtotime(date('Y-m-d', strtotime(str_replace('/', '-', $filter['payment_to'])))) : null;
        $note = !empty($filter['note']) ? trim($filter['note']) : null;

        // fields select
        switch($field){
            case FULL_INFO:
                $fields = ['Payments.id', 'Payments.code', 'Payments.foreign_id', 'Payments.foreign_type', 'Payments.type', 'Payments.type_payment_id', 'Payments.object_type', 'Payments.object_id', 'Payments.amount', 'Payments.payment_method', 'Payments.sub_method', 'Payments.payment_gateway_code', 'Payments.payment_gateway_response', 'Payments.payment_time', 'Payments.reference', 'Payments.full_name', 'Payments.description', 'Payments.counted', 'Payments.status', 'Payments.created', 'Payments.updated', 'Payments.created_by'];
            break;

            case LIST_INFO:
            case SIMPLE_INFO:
            default:
                $fields = ['Payments.id', 'Payments.code', 'Payments.type', 'Payments.amount', 'Payments.payment_method', 'Payments.payment_gateway_code','Payments.full_name', 'Payments.description', 'Payments.object_type', 'Payments.object_id', 'Payments.note', 'Payments.payment_time', 'Payments.created', 'Payments.created_by', 'Payments.status'];
            break;
        }

        $sort_string = 'Payments.id DESC';
        if(!empty($params[SORT])){
            switch($sort_field){
                case 'id':
                case 'payment_id':
                    $sort_string = 'Payments.id '. $sort_type;
                break;

                case 'type':
                    $sort_string = 'Payments.type '. $sort_type .', Payments.id DESC';
                break;

                case 'status':
                    $sort_string = 'Payments.status '. $sort_type .', Payments.id DESC';
                break;

                case 'amount':
                    $sort_string = 'Payments.amount '. $sort_type .', Payments.id DESC';
                break;

                case 'created':
                    $sort_string = 'Payments.created '. $sort_type .', Payments.id DESC';
                break;

                case 'updated':
                    $sort_string = 'Payments.updated '. $sort_type .', Payments.id DESC';
                break;

                case 'created_by':
                    $sort_string = 'Payments.created_by '. $sort_type .', Payments.id DESC';
                break;             
            }
        }

        // filter by conditions
        $where = [];    

        if(!empty($keyword)){
            $where['Payments.search_unicode LIKE'] = '%' . Text::slug(strtolower($keyword), ' ') . '%';
        }

        if(!empty($note)){
            $where['OR'] = [
                'Payments.note LIKE' => '%' . Text::slug(strtolower($note), ' ') . '%'
            ];
        }

        if(!is_null($status)){
            $where['Payments.status'] = $status;
        }

        if(!is_null($type)){
            $where['Payments.type'] = $type;
        }

        if(!empty($payment_method)){
            $where['Payments.payment_method'] = $payment_method;
        }

        if(!empty($foreign_id)){
            $where['Payments.foreign_id'] = $foreign_id;
        }

        if(!empty($foreign_type)){
            $where['Payments.foreign_type'] = $foreign_type;
        }        

        if(!empty($order_id)){
            $where['Payments.foreign_id'] = $order_id;
            $where['Payments.foreign_type'] = ORDER;
        }

        if(!empty($price_from)){
            $where['Payments.amount >='] = $price_from;
        }

        if(!empty($price_to)){
            $where['Payments.amount <='] = $price_to;
        }

        if(!empty($create_from)){
            $where['Payments.created >='] = $create_from;
        }

        if(!empty($create_to)){
            $where['Payments.created <='] = $create_to;
        }

        if(!empty($payment_from)){
            $where['Payments.payment_time >='] = $payment_from;
        }

        if(!empty($payment_to)){
            $where['Payments.payment_time <='] = $payment_to;
        }

        return $this->find()->where($where)->select($fields)->order($sort_string);
    }

    public function getTotalPaidOrder($order_id = null, $params = [])
    {
        if(empty($order_id)) return 0;

        $type = isset($params['type']) ? intval($params['type']) : null;
        $payment_method = !empty($params['payment_method']) ? $params['payment_method'] : null;
        
        if(is_null($type) || !in_array($type, [0, 1])){
            $type = 1;
        }

        $table = TableRegistry::get('Payments');
        
        $where = [
            'Payments.foreign_id' => $order_id,
            'Payments.foreign_type' => ORDER,
            'Payments.status' => 1,
            'Payments.type' => $type
        ];

        if(!empty($payment_method)) {
            $where['Payments.payment_method'] = $payment_method;
        }

        $result = $table->find()->select([
            'total_paid' => $table->find()->func()->sum('Payments.amount')
        ])->where($where)->first();

        return !empty($result['total_paid']) ? floatval($result['total_paid']) : 0;
    }

    public function getPendingPaymentOrder($order_id = null, $type = null)
    {
        if(empty($order_id)) return 0;

        if(is_null($type) || !in_array($type, [0, 1])){
            $type = 1;
        }

        $table = TableRegistry::get('Payments');

        $result = $table->find()
        ->select([
            'total_pending' => $table->find()->func()->sum('Payments.amount')
        ])
        ->where([
            'Payments.foreign_id' => $order_id,
            'Payments.foreign_type' => ORDER,
            'Payments.status' => 2,
            'Payments.type' => $type
        ])->first();
        return !empty($result['total_pending']) ? floatval($result['total_pending']) : 0;
    }

    public function getDetailPayment($code = null)
    {
        if(empty($code)) return [];

        return $this->find()->where([
            'Payments.code' => $code
        ])->first();
    }

    public function checkExistPaymentWaitCodForOrder($order_id = null)
    {
        if(empty($order_id)) return false;

        $count = $this->find()->where([
            'Payments.foreign_id' => $order_id,
            'Payments.foreign_type' => ORDER,
            'Payments.type' => 1, // thu
            'Payments.status' => 2 // trạng thái đơn đang chờ
        ])->count();

        return !empty($count) ? true : false;
    }
}