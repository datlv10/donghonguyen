<?php
namespace App\Model\Table;

use Cake\Utility\Text;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Utility\Hash;

class CustomersAffiliateOrderTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('customers_affiliate_order');
        $this->setPrimaryKey('id');

        $this->hasOne('Customers', [
            'className' => 'Customers',
            'foreignKey' => 'id',
            'bindingKey' => 'customer_id',
            'propertyName' => 'Customers',
            'joinType' => 'INNER',
        ]);

        $this->hasOne('Orders', [
            'className' => 'Orders',
            'foreignKey' => 'id',
            'bindingKey' => 'order_id',
            'propertyName' => 'Orders',
            'joinType' => 'INNER'
        ]);

        $this->hasMany('OrdersItem', [
            'className' => 'OrdersItem',
            'foreignKey' => 'order_id',
            'bindingKey' => 'order_id',
            'joinType' => 'INNER',
            'propertyName' => 'OrdersItem'
        ]);
    }

    public function queryListAffiliateOrder($params = []) 
    {
        // get info params
        $get_items = !empty($params['get_items']) ? true : false;

        //filter
        $field = !empty($params[FIELD]) ? $params[FIELD] : SIMPLE_INFO;

        // sort
        $sort = !empty($params[SORT]) ? $params[SORT] : [];
        $sort_field = !empty($sort[FIELD]) ? $sort[FIELD] : null;
        $sort_type = !empty($sort[SORT]) && in_array($sort[SORT], [DESC, ASC]) ? $sort[SORT] : DESC;

        // filter
        $filter = !empty($params[FILTER]) ? $params[FILTER] : [];
        $status = isset($filter['status']) && $filter['status'] != '' ? $filter['status'] : null;
        $group_status = !empty($filter['group_status']) ? $filter['group_status'] : null;
        $price_from = !empty($filter['price_from']) ? floatval(str_replace(',', '', $filter['price_from'])) : null;
        $price_to = !empty($filter['price_to']) ? floatval(str_replace(',', '', $filter['price_to'])) : null;
        $create_from = !empty($filter['create_from']) ? strtotime(date('Y-m-d 00:00:00', strtotime(str_replace('/', '-', $filter['create_from'])))) : null;
        $create_to = !empty($filter['create_to']) ? strtotime(date('Y-m-d 23:59:59', strtotime(str_replace('/', '-', $filter['create_to'])))) : null;
        $keyword = !empty($filter['keyword']) ? trim($filter['keyword']) : null;
        $customer_id = !empty($filter['customer_id']) ? intval($filter['customer_id']) : null;
        $order_code = !empty($filter['order_code']) ? $filter['order_code'] : null;
        $order_id = !empty($filter['order_id']) ? $filter['order_id'] : null;

        // fields select
        switch($field){
            case LIST_INFO:
                $fields = ['CustomersAffiliateOrder.id', 'Orders.code'];
            break;

            case FULL_INFO:
            case SIMPLE_INFO:
            default:
                $fields = ['CustomersAffiliateOrder.id', 'CustomersAffiliateOrder.customer_id', 'CustomersAffiliateOrder.order_id', 'CustomersAffiliateOrder.profit_value', 'CustomersAffiliateOrder.profit_point', 'CustomersAffiliateOrder.profit_money', 'Customers.full_name', 'Customers.email', 'Customers.phone', 'Orders.code', 'Orders.affiliate_discount_type', 'Orders.affiliate_discount_value', 'Orders.total_affiliate', 'Orders.total', 'Orders.status', 'Orders.created'];
            break;
        }

        $sort_string = 'CustomersAffiliateOrder.id DESC';

        // filter by conditions
        $where = [
            'Customers.deleted' => 0,
            'Customers.is_partner_affiliate' => 1,
        ];    

        $contain = ['Customers', 'Orders'];

        if(!empty($get_items)){
            $contain[] = 'OrdersItem';
        }

        if(!empty($keyword)){
            $where['Customers.search_unicode LIKE'] = '%' . Text::slug(strtolower($keyword), ' ') . '%';
        }

        if(!empty($customer_id)){
            $where['CustomersAffiliateOrder.customer_id'] = $customer_id;
        }

        if(!empty($order_code)){
            $where['Orders.code'] = $order_code;
        }

        if(!empty($order_id)){
            $where['Orders.id'] = $order_id;
        }

        if(!is_null($status)){
            $where['Orders.status'] = $status;
        }

        if(!empty($price_from)){
            $where['Orders.total >='] = $price_from;
        }

        if(!empty($price_to)){
            $where['Orders.total <='] = $price_to;
        }

        if(!empty($create_from)){
            $where['Orders.created >='] = $create_from;
        }

        if(!empty($create_to)){
            $where['Orders.created <='] = $create_to;
        }

        if(!empty($group_status)){
            switch($group_status){
                case 'wait_payment':
                    $where['Orders.status'] = DRAFT;
                break;

                case 'processing':
                    $where['Orders.status IN'] = [NEW_ORDER, CONFIRM];
                break;

                case 'transport':
                    $where['Orders.status IN'] = [PACKAGE, EXPORT, DONE];
                break;

                case 'cancel':
                    $where['Orders.status'] = CANCEL;
                break;
            }
        }

        return $this->find()->contain($contain)->where($where)->select($fields)->group('CustomersAffiliateOrder.id')->order($sort_string);
    }

    public function formatDataAffiliateOrderDetail($data = [], $lang = null)
    {
        if(empty($data)) return [];
        
        $result = [
            'id' => !empty($data['id']) ? intval($data['id']) : null,
            'customer_id' => !empty($data['customer_id']) ? intval($data['customer_id']) : null,
            'order_id' => !empty($data['order_id']) ? intval($data['order_id']) : null,
            'profit_value' => !empty($data['profit_value']) ? floatval($data['profit_value']) : null,
            'profit_point' => !empty($data['profit_point']) ? floatval($data['profit_point']) : null,
            'profit_money' => !empty($data['profit_money']) ? floatval($data['profit_money']) : null,
            'code' => null,
            'total_affiliate' => null,
            'affiliate_discount_type' => null,
            'affiliate_discount_value' => null,
            'total' => null,
            'status' => null,
            'created' => null,
            'full_name' => null,
            'email' => null,
            'phone' => null
        ];

        if(!empty($data['Orders'])){
            $order = $data['Orders'];
            $result['code'] = !empty($order->code) ? $order->code : null;
            $result['total_affiliate'] = !empty($order->total_affiliate) ? floatval($order->total_affiliate) : null;
            $result['affiliate_discount_type'] = !empty($order->affiliate_discount_type) ? $order->affiliate_discount_type : null;
            $result['affiliate_discount_value'] = !empty($order->affiliate_discount_value) ? floatval($order->affiliate_discount_value) : null;
            $result['total'] = !empty($order->total) ? floatval($order->total) : null;
            $result['status'] = !empty($order->status) ? $order->status : null;
            $result['created'] = !empty($order->created) ? intval($order->created) : null;
        }

        if(!empty($data['Customers'])){
            $customer = $data['Customers'];
            $result['full_name'] = !empty($customer->full_name) ? $customer->full_name : null;
            $result['email'] = !empty($customer->email) ? $customer->email : null;
            $result['phone'] = !empty($customer->phone) ? $customer->phone : null;
        }

        $items = [];
        if(!empty($data['OrdersItem'])){
            $products_item = TableRegistry::get('ProductsItem');

            $attribute_options_table = TableRegistry::get('AttributesOptions');
            $all_options = Hash::combine($attribute_options_table->find()->contain([
                'AttributesOptionsContent' => function ($q) use ($lang) {
                    return $q->where([
                        'AttributesOptionsContent.lang' => $lang
                    ]);
                }
            ])->group('AttributesOptions.id')->toArray(), '{n}.id', '{n}.AttributesOptionsContent.name');
            
            foreach($data['OrdersItem'] as $k => $order_item){
                
                $product_item_id = !empty($order_item['product_item_id']) ? intval($order_item['product_item_id']) : null;
                $item_info = $products_item->getDetailProductItem($product_item_id, $lang, ['get_attribute' => true]);            
                
                $items[] = [
                    'id' => !empty($order_item['id']) ? intval($order_item['id']) : null,
                    'product_id' => !empty($order_item['product_id']) ? intval($order_item['product_id']) : null,
                    'product_item_id' => $product_item_id,                    
                    'quantity' => !empty($order_item['quantity']) ? intval($order_item['quantity']) : null,
                    'price' => !empty($order_item['price']) ? floatval($order_item['price']) : null,
                    'discount_type' => !empty($order_item['discount_type']) ? $order_item['discount_type'] : null,
                    'discount_value' => !empty($order_item['discount_value']) ? floatval($order_item['discount_value']) : null,
                    'vat_value' => !empty($order_item['vat_value']) ? floatval($order_item['vat_value']) : null,
                    'total_discount' => !empty($order_item['total_discount']) ? floatval($order_item['total_discount']) : null,
                    'total_vat' => !empty($order_item['total_vat']) ? floatval($order_item['total_vat']) : null,
                    'total_item' => !empty($order_item['total_item']) ? floatval($order_item['total_item']) : null,
                    'code' => !empty($item_info['code']) ? $item_info['code'] : null,
                    'name' => !empty($item_info['name']) ? $item_info['name'] : null,
                    'name_extend' => !empty($item_info['name_extend']) ? $item_info['name_extend'] : null,
                    'images' => !empty($item_info['images']) ? $item_info['images'] : [],
                    'url' => !empty($item_info['url']) ? $item_info['url'] : null,

                    'width' => !empty($item_info['width']) ? intval($item_info['width']) : null,
                    'length' => !empty($item_info['length']) ? intval($item_info['length']) : null,
                    'height' => !empty($item_info['height']) ? intval($item_info['height']) : null,
                    'weight' => !empty($item_info['weight']) ? intval($item_info['weight']) : null,
                    
                    'width_unit' => !empty($item_info['width_unit']) ? $item_info['width_unit'] : null,
                    'length_unit' => !empty($item_info['length_unit']) ? $item_info['length_unit'] : null,
                    'height_unit' => !empty($item_info['height_unit']) ? $item_info['height_unit'] : null,
                    'weight_unit' => !empty($item_info['weight_unit']) ? $item_info['weight_unit'] : null

                ];
            }
        }
        
        $result['items'] = !empty($items) ?  array_reverse($items) : [];
        
        return $result;
    }

    public function countNumberOrderOfCustomer($customer_id = null, $params = [])
    {
        if(empty($customer_id)) return 0;
        $order_status = !empty($params['order_status']) ? $params['order_status'] : null;

        $create_from = !empty($params['create_from']) ? intval($params['create_from']) : null;
        $create_to = !empty($params['create_to']) ? intval($params['create_to']) : null;

        $where = [
            'CustomersAffiliateOrder.customer_id' => $customer_id,
            'Customers.is_partner_affiliate' => 1
        ];

        if(!empty($order_status)){
            $where['Orders.status'] = $order_status;
        }

        if(!empty($create_from)){
            $where['Orders.created >='] = $create_from;
        }

        if(!empty($create_to)){
            $where['Orders.created <='] = $create_to;
        }

        return $this->find()->contain(['Customers', 'Orders'])->where($where)->count();
    }

    public function sumTotalOrderOfCustomer($customer_id = null, $params = [])
    {
        if(empty($customer_id)) return 0;

        $where = ['CustomersAffiliateOrder.customer_id' => $customer_id];
        if(!empty($order_status)){
            $where['Orders.status'] = $order_status;
        }

        if(!empty($create_from)){
            $where['Orders.created >='] = $create_from;
        }
        
        $query = $this->find()->contain(['Orders']);
        $result = $query->where($where)->select(['sum_total' => $query->func()->sum('Orders.total')])->first();
        return !empty($result['sum_total']) ? intval($result['sum_total']): 0;
    }

    public function countNumberOrder($customer_id = null, $params = [])
    {
        $get_failed_order = !empty($params['get_failed_order']) ? true : false;

        $create_from = !empty($params['create_from']) ? $params['create_from'] : null;
        $create_to = !empty($params['create_to']) ? $params['create_to'] : null;

        $where = [
            'Customers.deleted' => 0,
            'Customers.is_partner_affiliate' => 1,
        ];

        if (!empty($customer_id)) {
            $where['customer_id'] = $customer_id;
        }

        if(!empty($get_failed_order)){
            $where['Orders.status'] = CANCEL;
        }

        if(!empty($create_from)){
            $where['Orders.created >='] = $create_from;
        }
        if(!empty($create_to)){
            $where['Orders.created <'] = $create_to;
        }

        return $this->find()->contain(['Customers', 'Orders'])->where($where)->count();
    }

    public function provisionalReturnApply($customer_id = null)
    {
        $result = [];
        $table = TableRegistry::get('CustomersAffiliateOrder');

        $where = [];

        if (!empty($customer_id)) {
            $where['customer_id'] = $customer_id;
        }

        $result = $table->find()->select([
            'point' => $table->find()->func()->sum('profit_point'),
            'money' => $table->find()->func()->sum('profit_money'),
        ])->where($where)->first();
        
        return $result;
    }
}