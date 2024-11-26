<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use App\Model\Behavior\UnixTimestampBehavior;
use Cake\Utility\Text;

class ShippingsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('shippings');

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
            
        $validator
        ->scalar('order_id')
        ->requirePresence('order_id', 'create');

        $validator
        ->scalar('shipping_method')
        ->requirePresence('shipping_method', 'create');
        
        return $validator;
    }

    public function queryListShippings($params = []) 
    {
        $table = TableRegistry::get('Shippings');

        // get info params
        $field = !empty($params[FIELD]) ? $params[FIELD] : SIMPLE_INFO;       

        // sort
        $sort = !empty($params[SORT]) ? $params[SORT] : [];
        $sort_field = !empty($sort[FIELD]) ? $sort[FIELD] : null;
        $sort_type = !empty($sort[SORT]) && in_array($sort[SORT], [DESC, ASC]) ? $sort[SORT] : DESC;

        // filter
        $filter = !empty($params[FILTER]) ? $params[FILTER] : [];
        $lang = !empty($filter[LANG]) ? $filter[LANG] : TableRegistry::get('Languages')->getDefaultLanguage();
        $type = !empty($filter[TYPE]) ? $filter[TYPE] : null;
        $keyword = !empty($filter['keyword']) ? trim($filter['keyword']) : null;
        $status = !empty($filter['status']) && $filter['status'] != '' ? $filter['status'] : null;
        $order_id = !empty($filter['order_id']) ? intval($filter['order_id']) : null;
        $cod_money_from = !empty($filter['cod_money_from']) ? floatval(str_replace(',', '', $filter['cod_money_from'])) : null;
        $cod_money_to = !empty($filter['cod_money_to']) ? floatval(str_replace(',', '', $filter['cod_money_to'])) : null;     
        $shipping_fee_from = !empty($filter['shipping_fee_from']) ? floatval(str_replace(',', '', $filter['shipping_fee_from'])) : null;
        $shipping_fee_to = !empty($filter['shipping_fee_to']) ? floatval(str_replace(',', '', $filter['shipping_fee_to'])) : null;   
        $create_from = !empty($filter['create_from']) ? strtotime(date('Y-m-d 00:00:00', strtotime(str_replace('/', '-', $filter['create_from'])))) : null;
        $create_to = !empty($filter['create_to']) ? strtotime(date('Y-m-d 23:59:59', strtotime(str_replace('/', '-', $filter['create_to'])))) : null; 
        $shipping_method = !empty($filter['shipping_method']) ? $filter['shipping_method'] : null; 

        // fields select
        switch($field){
            case FULL_INFO:
                $fields = ['Shippings.id', 'Shippings.code', 'Shippings.order_id', 'Shippings.shipping_method', 'Shippings.cod_money', 'Shippings.carrier_code', 'Shippings.carrier_service_code', 'Shippings.carrier_service_type_code', 'Shippings.carrier_shop_id', 'Shippings.carrier_order_code', 'Shippings.carrier_shipping_fee', 'Shippings.shipping_fee', 'Shippings.shipping_fee_discount', 'Shippings.shipping_fee_customer', 'Shippings.cod_fee_discount', 'Shippings.cod_fee', 'Shippings.insurance_fee', 'Shippings.extra_fee', 'Shippings.estimated_pick_time', 'Shippings.estimated_deliver_time', 'Shippings.full_name', 'Shippings.phone', 'Shippings.country_id', 'Shippings.city_id', 'Shippings.district_id', 'Shippings.ward_id', 'Shippings.country_name', 'Shippings.city_name', 'Shippings.district_name', 'Shippings.ward_name', 'Shippings.address', 'Shippings.full_address', 'Shippings.note', 'Shippings.created', 'Shippings.updated', 'Shippings.created_by', 'Shippings.status'];
            break;

            case LIST_INFO:
            case SIMPLE_INFO:
            default:
                $fields = ['Shippings.id', 'Shippings.code', 'Shippings.order_id', 'Shippings.shipping_method', 'Shippings.cod_money', 'Shippings.shipping_fee', 'Shippings.shipping_fee_customer', 'Shippings.estimated_pick_time', 'Shippings.estimated_deliver_time', 'Shippings.full_name', 'Shippings.full_address', 'Shippings.phone', 'Shippings.created', 'Shippings.updated', 'Shippings.created_by', 'Shippings.status'];
            break;
        }

        $sort_string = 'Shippings.id DESC';
        if(!empty($params[SORT])){
            switch($sort_field){
                case 'id':
                case 'shipping_id':
                    $sort_string = 'Shippings.id '. $sort_type;
                break;

                case 'code':
                    $sort_string = 'Shippings.code '. $sort_type .', Shippings.id DESC';
                break;

                case 'cod_money':
                    $sort_string = 'Shippings.cod_money '. $sort_type .', Shippings.id DESC';
                break;

                case 'shipping_fee':
                    $sort_string = 'Shippings.shipping_fee '. $sort_type .', Shippings.id DESC';
                break;

                case 'shipping_method':
                    $sort_string = 'Shippings.shipping_method '. $sort_type .', Shippings.id DESC';
                break;

                case 'status':
                    $sort_string = 'Shippings.status '. $sort_type .', Shippings.id DESC';
                break;

                case 'created':
                    $sort_string = 'Shippings.created '. $sort_type .', Shippings.id DESC';
                break;

                case 'updated':
                    $sort_string = 'Shippings.updated '. $sort_type .', Shippings.id DESC';
                break;

                case 'created_by':
                    $sort_string = 'Shippings.created_by '. $sort_type .', Shippings.id DESC';
                break;             
            }
        }

        // filter by conditions
        $where = [];    

        if(!empty($keyword)){
            $where['Shippings.search_unicode LIKE'] = '%' . Text::slug(strtolower($keyword), ' ') . '%';
        }

        if(!is_null($status)){
            $where['Shippings.status'] = $status;
        }

        if(!empty($order_id)){
            $where['Shippings.order_id'] = $order_id;
        }

        if(!empty($cod_money_from)){
            $where['Shippings.cod_money >='] = $cod_money_from;
        }

        if(!empty($cod_money_to)){
            $where['Shippings.cod_money <='] = $cod_money_to;
        }

        if(!empty($shipping_fee_from)){
            $where['Shippings.shipping_fee >='] = $shipping_fee_from;
        }

        if(!empty($shipping_fee_to)){
            $where['Shippings.shipping_fee <='] = $shipping_fee_to;
        }

        if(!empty($create_from)){
            $where['Shippings.created >='] = $create_from;
        }

        if(!empty($create_to)){
            $where['Shippings.created <='] = $create_to;
        }

        if(!empty($shipping_method)){
            $where['Shippings.shipping_method'] = $shipping_method;
        }
        
        return $table->find()->where($where)->select($fields)->order($sort_string);
    }

    public function getDetailShippings($id = null)
    {
        if(empty($id)) return [];

        $result = TableRegistry::get('Shippings')->find()->where([
            'Shippings.id' => $id
        ])->first();

        return !empty($result) ? $result : [];
    }

    public function checkExistShippingActiveForOrder($order_id = null)
    {
        if(empty($order_id)) return false;

        $count = $this->find()->where([
            'Shippings.order_id' => $order_id,
            'Shippings.status NOT IN' => [CANCEL_PACKAGE, CANCEL_DELIVERED]
        ])->count();

        return !empty($count) ? true : false;
    }
}