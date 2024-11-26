<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use App\Model\Behavior\UnixTimestampBehavior;
use Cake\Utility\Hash;
use Cake\Utility\Text;

class OrdersTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('orders');

        $this->setPrimaryKey('id');

        $this->addBehavior('UnixTimestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                    'updated' => 'existing'
                ]
            ]
        ]);

        $this->hasMany('OrdersItem', [
            'className' => 'OrdersItem',
            'foreignKey' => 'order_id',
            'joinType' => 'INNER',
            'propertyName' => 'OrdersItem'
        ]);

        $this->hasOne('OrdersContact', [
            'className' => 'Publishing.OrdersContact',
            'foreignKey' => 'order_id',
            'joinType' => 'LEFT',
            'propertyName' => 'OrdersContact'
        ]);

        $this->belongsTo('User', [
            'className' => 'Publishing.Users',
            'foreignKey' => 'created_by',
            'joinType' => 'LEFT',
            'propertyName' => 'User'
        ]);

        $this->belongsTo('Staff', [
            'className' => 'Publishing.Users',
            'foreignKey' => 'staff_id',
            'joinType' => 'LEFT',
            'propertyName' => 'Staff'
        ]);

        $this->belongsTo('Objects', [
            'className' => 'Publishing.Objects',
            'foreignKey' => 'source',
            'joinType' => 'LEFT',
            'propertyName' => 'Objects'
        ]);

        $this->hasMany('Payments', [
            'className' => 'Publishing.Payments',
            'foreignKey' => 'foreign_id',
            'joinType' => 'LEFT',
            'conditions' => [
                'Payments.type' => 1,
                'Payments.foreign_type' => ORDER
            ],
            'propertyName' => 'Payments'
        ]);

        $this->hasMany('Shippings', [
            'className' => 'Publishing.Shippings',
            'foreignKey' => 'order_id',
            'joinType' => 'LEFT',
            'propertyName' => 'Shippings'
        ]);

        $this->belongsTo('Related', [
            'className' => 'Publishing.Orders',
            'foreignKey' => 'related_order_id',
            'joinType' => 'INNER',
            'propertyName' => 'Related'
        ]);

        $this->hasOne('CustomersAffiliateOrder', [
            'className' => 'Publishing.CustomersAffiliateOrder',
            'foreignKey' => 'order_id',
            'joinType' => 'INNER',
            'propertyName' => 'CustomersAffiliateOrder'
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        return $validator;
    }

    public function queryListOrders($params = []) 
    {
        // get info params
        $get_items = !empty($params['get_items']) ? true : false;
        $get_contact = !empty($params['get_contact']) ? true : false;
        $get_user = !empty($params['get_user']) ? true : false;
        $get_staff = !empty($params['get_staff']) ? true : false;
        $get_payment = !empty($params['get_payment']) ? true : false;
        $get_shipping = !empty($params['get_shipping']) ? true : false;
        $get_related = !empty($params['get_related']) ? true : false;
        $get_customer_affiliate_order = !empty($params['get_customer_affiliate_order']) ? true : false;

        // sort
        $sort = !empty($params[SORT]) ? $params[SORT] : [];
        $sort_field = !empty($sort[FIELD]) ? $sort[FIELD] : null;
        $sort_type = !empty($sort[SORT]) && in_array($sort[SORT], [DESC, ASC]) ? $sort[SORT] : DESC;

        // filter
        $filter = !empty($params[FILTER]) ? $params[FILTER] : [];

        $type = !empty($filter[TYPE]) ? $filter[TYPE] : null;
        $keyword = !empty($filter['keyword']) ? trim($filter['keyword']) : null;
        $note = !empty($filter['note']) ? trim($filter['note']) : null;
        $source = !empty($filter['source']) ? trim($filter['source']) : null;
        $branch_id = !empty($filter['branch_id']) ? intval($filter['branch_id']) : null;
        $city_id = !empty($filter['city_id']) ? intval($filter['city_id']) : null;
        $district_id = !empty($filter['district_id']) ? intval($filter['district_id']) : null;
        $ward_id = !empty($filter['ward_id']) ? intval($filter['ward_id']) : null;
        $price_from = !empty($filter['price_from']) ? floatval(str_replace(',', '', $filter['price_from'])) : null;
        $price_to = !empty($filter['price_to']) ? floatval(str_replace(',', '', $filter['price_to'])) : null;
        $staff_id = !empty($filter['staff_id']) ? intval($filter['staff_id']) : null;
        $create_from = !empty($filter['create_from']) ? strtotime(date('Y-m-d 00:00:00', strtotime(str_replace('/', '-', $filter['create_from'])))) : null;
        $create_to = !empty($filter['create_to']) ? strtotime(date('Y-m-d 23:59:59', strtotime(str_replace('/', '-', $filter['create_to'])))) : null;
        $created_by = !empty($filter['created_by']) ? trim($filter['created_by']) : null;
        $status = isset($filter['status']) && $filter['status'] != '' ? $filter['status'] : null;
        $not_status = isset($filter['not_status']) && $filter['not_status'] != '' ? $filter['not_status'] : null;
        $group_status = !empty($filter['group_status']) ? $filter['group_status'] : null;
        $pay_status = isset($filter['pay_status']) && $filter['pay_status'] != '' ? $filter['pay_status'] : null;
        $affiliate_customer_id = !empty($filter['affiliate_customer_id']) ? intval($filter['affiliate_customer_id']) : null;
        $customer_id = !empty($filter['customer_id']) ? intval($filter['customer_id']) : null;
        $order_code = !empty($filter['order_code']) ? $filter['order_code'] : null;
        $order_id = !empty($filter['order_id']) ? $filter['order_id'] : null;
        $sort_string = 'Orders.id DESC';


        if(!empty($params[SORT])){
            switch($sort_field){
                case 'id':
                    $sort_string = 'Orders.id '. $sort_type;
                break;

                case 'code':
                    $sort_string = 'Orders.code '. $sort_type;
                break;

                case 'status':
                    $sort_string = 'Orders.status '. $sort_type;
                break;

                case 'count_items':
                    $sort_string = 'Orders.count_items '. $sort_type;
                break;

                case 'total':
                    $sort_string = 'Orders.total '. $sort_type;
                break;

                case 'created':
                    $sort_string = 'Orders.created '. $sort_type .', Orders.id DESC';
                break;

                case 'updated':
                    $sort_string = 'Orders.updated '. $sort_type .',  Orders.id DESC';
                break;

                case 'created_by':
                    $sort_string = 'Orders.created_by '. $sort_type .', Orders.id DESC';
                break;             
            }
        }

        // filter by conditions
        $where = [
            'Orders.deleted' => 0,
        ];

        if(!empty($type) && in_array($type, [ORDER, ORDER_RETURN, IMPORT, TRANSFER, RETAIL, OTHER_BILL])){
            $where['Orders.type'] = $type;
        }

        if(!empty($keyword)){
            $where['OR'] = [
                'OrdersContact.search_unicode LIKE' => '%' . Text::slug(strtolower($keyword), ' ') . '%',
                'OrdersContact.search_unicode LIKE ' => '%' . $keyword . '%',
                'Orders.code LIKE' => '%' . Text::slug(strtolower($keyword), ' ') . '%'
            ];
        }

        if(!empty($note)){
            $where['OR'] = [
                'Orders.note LIKE' => '%' . Text::slug(strtolower($note), ' ') . '%'
            ];
        }

        if(!is_null($status)){
            $where['Orders.status'] = $status;
        }

        if(!is_null($not_status)){
            $where['Orders.status !='] = $not_status;
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

        if(!empty($customer_id)){
            $get_contact = true;
            $where['OrdersContact.customer_id'] = $customer_id;
        }

        if(!empty($pay_status) && $pay_status == 'debt'){
            $where['Orders.debt >'] = 0;
        }

        if(!empty($pay_status) && $pay_status == 'completed'){
            $where['Orders.debt'] = 0;
        }

        if(!empty($source)){
            $where['Orders.source'] = $source;
        }

        if(!empty($city_id)){
            $where['OrdersContact.city_id'] = $city_id;
        }

        if(!empty($district_id)){
            $where['OrdersContact.district_id'] = $district_id;
        }

        if(!empty($ward_id)){
            $where['OrdersContact.ward_id'] = $ward_id;
        }

        if(!empty($price_from)){
            $where['Orders.total >='] = $price_from;
        }

        if(!empty($price_to)){
            $where['Orders.total <='] = $price_to;
        }

        if(!empty($staff_id)){
            $where['Orders.staff_id'] = $staff_id;
        }

        if(!empty($create_from)){
            $where['Orders.created >='] = $create_from;
        }

        if(!empty($create_to)){
            $where['Orders.created <='] = $create_to;
        }

        if(!empty($created_by)){
            $where['Orders.created_by'] = $created_by;
        }

        if(!empty($order_code)){
            $where['Orders.code'] = $order_code;
        }

        if(!empty($order_id)){
            $where['Orders.id'] = $order_id;
        }

        if(!empty($affiliate_customer_id)){
            $get_customer_affiliate_order = true;
            $where['CustomersAffiliateOrder.customer_id'] = $affiliate_customer_id;
        }

        // contain
        $contain = [];
        
        if(!empty($get_items)){
            $contain[] = 'OrdersItem';
        }

        if(!empty($get_contact)){
            $contain[] = 'OrdersContact';
        }

        if(!empty($get_user)){
            $contain[] = 'User';
        }

        if(!empty($get_staff)){
            $contain[] = 'Staff';
        }

        if(!empty($get_payment)){
            $contain[] = 'Payments';
        }

        if(!empty($get_shipping)){
            $contain[] = 'Shippings';
        }

        if(!empty($get_related)){
            $contain[] = 'Related';
        }

        if(!empty($get_customer_affiliate_order)) {
            $contain[] = 'CustomersAffiliateOrder';
        }

        return $this->find()->contain($contain)->where($where)->order($sort_string);
    }

    public function getDetailOrder($id = null, $params = [])
    {
        $result = [];
        if(empty($id)) return [];

        $get_items = !empty($params['get_items']) ? true : false;
        $get_contact = !empty($params['get_contact']) ? true : false;
        $get_user = !empty($params['get_user']) ? true : false;
        $get_staff = !empty($params['get_staff']) ? true : false;
        $get_payment = !empty($params['get_payment']) ? true : false;
        $get_shipping = !empty($params['get_shipping']) ? true : false;

        $where = [
            'Orders.id' => $id,
            'Orders.deleted' => 0,
        ];

        $contain = [];

        if(!empty($get_items)){
            $contain[] = 'OrdersItem';
        }

        if(!empty($get_user)){
            $contain[] = 'User';
        }

        if(!empty($get_staff)){
            $contain[] = 'Staff';
        }

        if(!empty($get_payment)){
            $contain[] = 'Payments';
        }

        if(!empty($get_shipping)){
            $contain[] = 'Shippings';
        }

        if(!empty($get_contact)){
            $contain[] = 'OrdersContact';
        }

        $result = $this->find()->contain($contain)->where($where)->first();

        return $result;
    }

    public function formatDataOrderDetail($data = [], $lang = null)
    {
        if(empty($data)) return [];

        $result = [
            'id' => !empty($data['id']) ? intval($data['id']) : null,
            'type' => !empty($data['type']) ? $data['type'] : null,
            'code' => !empty($data['code']) ? $data['code'] : null,
            'source' => !empty($data['source']) ? $data['source'] : null,
            'note' => !empty($data['note']) ? $data['note'] : null,
            'related_order_id' => !empty($data['related_order_id']) ? intval($data['related_order_id']) : null,
            'branch_id' => !empty($data['branch_id']) ? intval($data['branch_id']) : null,
            'staff_id' => !empty($data['staff_id']) ? intval($data['staff_id']) : null,
            'staff_note' => !empty($data['staff_note']) ? $data['staff_note'] : null,
            'other_service' => !empty($data['other_service']) ? json_decode($data['other_service'], true) : [],
            'number_items' => !empty($data['number_items']) ? intval($data['number_items']) : 0,
            'count_items' => !empty($data['count_items']) ? intval($data['count_items']) : 0,
            'coupon_code' => !empty($data['coupon_code']) ? $data['coupon_code'] : null,
            'voucher_code' => !empty($data['voucher_code']) ? $data['voucher_code'] : null,
            'voucher_value' => !empty($data['voucher_value']) ? floatval($data['voucher_value']) : null,
            'promotion_id' => !empty($data['promotion_id']) ? intval($data['promotion_id']) : null,
            'discount_type' => !empty($data['discount_type']) ? $data['discount_type'] : null,
            'discount_value' => !empty($data['discount_value']) ? floatval($data['discount_value']) : 0,
            'affiliate_code' => !empty($data['affiliate_code']) ? $data['affiliate_code'] : null,
            'affiliate_discount_type' => !empty($data['affiliate_discount_type']) ? $data['affiliate_discount_type'] : null,
            'affiliate_discount_value' => !empty($data['affiliate_discount_value']) ? floatval($data['affiliate_discount_value']) : 0,
            'discount_note' => !empty($data['discount_note']) ? $data['discount_note'] : null,
            'weigh' => !empty($data['weigh']) ? intval($data['weigh']) : 0,
            'length' => !empty($data['length']) ? intval($data['length']) : 0,
            'width' => !empty($data['width']) ? intval($data['width']) : 0,
            'height' => !empty($data['height']) ? intval($data['height']) : 0,
            'shipping_method_id' => !empty($data['shipping_method_id']) ? intval($data['shipping_method_id']) : null,
            'shipping_method' => !empty($data['shipping_method']) ? $data['shipping_method'] : null,
            'shipping_fee_customer' => !empty($data['shipping_fee_customer']) ? floatval($data['shipping_fee_customer']) : 0,
            'shipping_fee_partner' => !empty($data['shipping_fee_partner']) ? floatval($data['shipping_fee_partner']) : 0,
            'shipping_fee' => !empty($data['shipping_fee']) ? floatval($data['shipping_fee']) : 0,
            'shipping_note' => !empty($data['shipping_note']) ? $data['shipping_note'] : null,
            'cod_money' => !empty($data['cod_money']) ? floatval($data['cod_money']) : 0,
            'total_coupon' => !empty($data['total_coupon']) ? floatval($data['total_coupon']) : 0,
            'total_affiliate' => !empty($data['total_affiliate']) ? floatval($data['total_affiliate']) : 0,
            'total_discount' => !empty($data['total_discount']) ? floatval($data['total_discount']) : 0,
            'total_vat' => !empty($data['total_vat']) ? floatval($data['total_vat']) : 0,
            'total_other_service' => !empty($data['total_other_service']) ? floatval($data['total_other_service']) : 0,
            'total_discount_items' => !empty($data['total_discount_items']) ? floatval($data['total_discount_items']) : 0,
            'total' => !empty($data['total']) ? floatval($data['total']) : 0,
            'total_items' => !empty($data['total_items']) ? floatval($data['total_items']) : 0,
            'total_origin' => !empty($data['total_origin']) ? floatval($data['total_origin']) : 0,
            'paid' => !empty($data['paid']) ? floatval($data['paid']) : 0,
            'debt' => !empty($data['debt']) ? floatval($data['debt']) : 0,

            'point' => !empty($data['point']) ? intval($data['point']) : 0,
            'point_promotion' => !empty($data['point_promotion']) ? intval($data['point_promotion']) : 0,
            'point_paid' => !empty($data['point_paid']) ? floatval($data['point_paid']) : 0,
            'point_promotion_paid' => !empty($data['point_promotion_paid']) ? floatval($data['point_promotion_paid']) : 0,
            
            'cod_paid' => !empty($data['cod_paid']) ? floatval($data['cod_paid']) : 0,
            'cash_paid' => !empty($data['cash_paid']) ? floatval($data['cash_paid']) : 0,
            'bank_paid' => !empty($data['bank_paid']) ? floatval($data['bank_paid']) : 0,
            'credit_paid' => !empty($data['credit_paid']) ? floatval($data['credit_paid']) : 0,
            'gateway_paid' => !empty($data['gateway_paid']) ? floatval($data['gateway_paid']) : 0,
            'voucher_paid' => !empty($data['voucher_paid']) ? floatval($data['voucher_paid']) : 0,
            'date_create' => !empty($data['date_create']) ? $data['date_create'] : null,
            'date_received' => !empty($data['date_received']) ? $data['date_received'] : null,
            'status' => !empty($data['status']) ? $data['status'] : null,
            'customer_note_cancel' => !empty($data['customer_note_cancel']) ? $data['customer_note_cancel'] : null,
            'customer_cancel' => !empty($data['customer_cancel']) ? intval($data['customer_cancel']) : null,
            'created' => !empty($data['created']) ? $data['created'] : null,
            'updated' => !empty($data['updated']) ? $data['updated'] : null,
            'created_by' => !empty($data['created_by']) ? intval($data['created_by']) : null
        ];

        $items = [];
        if(!empty($data['OrdersItem'])){
            $products_item = TableRegistry::get('ProductsItem');

            $all_options = TableRegistry::get('AttributesOptions')->getAll($lang);
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
        $result['contact'] = !empty($data['OrdersContact']) ? $data['OrdersContact'] : [];
        $result['user'] = [];
        if(!empty($data['User'])){
            $result['user'] = $data['User'];
            unset($result['user']['password']);
            unset($result['user']['deleted']);
            unset($result['user']['search_unicode']);
        }
        
        $result['staff'] = [];
        if(!empty($data['Staff'])){
            $result['staff'] = $data['Staff'];
            unset($result['staff']['password']);
            unset($result['staff']['deleted']);
            unset($result['staff']['search_unicode']);
        }

        $result['affiliate_order'] = [];
        if(!empty($data['CustomersAffiliateOrder'])){
            $result['affiliate_order'] = $data['CustomersAffiliateOrder'];
        }

        $result['related'] = !empty($data['Related']) ? $data['Related'] : [];
        $result['object'] = !empty($data['Objects']) ? $data['Objects'] : [];

        return $result;
    }

    public function updateAfterPayment($order_id = null, $type = null)
    {
        if(empty($order_id)) return false;

        $payments_table = TableRegistry::get('Payments');

        $order_info = $this->find()->where([
            'id' => $order_id,
            'deleted' => 0
        ])->select([
            'id', 'total', 'point_paid', 'point_promotion_paid'
        ])->first();

        if(empty($order_info)) return false;

        $total = !empty($order_info['total']) ? floatval($order_info['total']) : 0;
        $paid = $payments_table->getTotalPaidOrder($order_id, ['type' => $type]);
        $cash_paid = $payments_table->getTotalPaidOrder($order_id, ['payment_method' => CASH]);
        $bank_paid = $payments_table->getTotalPaidOrder($order_id, ['payment_method' => BANK]);
        $credit_paid = $payments_table->getTotalPaidOrder($order_id, ['payment_method' => CREDIT]);
        $cod_paid = $payments_table->getTotalPaidOrder($order_id, ['payment_method' => COD]);
        $gateway_paid = $payments_table->getTotalPaidOrder($order_id, ['payment_method' => GATEWAY]);
        $voucher_paid = $payments_table->getTotalPaidOrder($order_id, ['payment_method' => VOUCHER]);

        $point_paid = !empty($order_info['point_paid']) ? floatval($order_info['point_paid']) : 0;
        $point_promotion_paid = !empty($order_info['point_promotion_paid']) ? floatval($order_info['point_promotion_paid']) : 0;

        $paid += $point_paid + $point_promotion_paid;

        
        $debt = $total - $paid;

        // xử lý dữ liệu sẽ cập nhật cho đơn hàng
        $data_order = [
            'paid' => $paid,
            'debt' => $debt,
            'cash_paid' => $cash_paid,
            'bank_paid' => $bank_paid,
            'credit_paid' => $credit_paid,
            'cod_paid' => $cod_paid,
            'gateway_paid' => $gateway_paid,
            'voucher_paid' => $voucher_paid
        ];        

        // cập nhật trạng thái đơn hàng -> thành công nếu đã thanh toán đủ và đã chuyển hàng
        if($debt <= 0){
            $shipping = TableRegistry::get('Shippings')->find()->where([
                'Shippings.order_id' => $order_id
            ])->order('Shippings.id DESC')->first();

            if(!empty($shipping['status']) && $shipping['status'] == DELIVERED){
                $data_order['status'] = DONE;
            }                
        }

        $data_entity = $this->patchEntity($order_info, $data_order);
        $update_order = $this->save($data_entity);
        if (empty($update_order->id)){
            return false;
        }

        return true;
    }

    public function reportOrder($params = [], $type = null) 
    {
        // sort
        $sort = !empty($params[SORT]) ? $params[SORT] : [];
        $sort_field = !empty($sort[FIELD]) ? $sort[FIELD] : null;
        $sort_type = !empty($sort[SORT]) ? $sort[SORT] : 'DESC';

        $get_contact = !empty($params['get_contact']) ? true : false;

        $display = !empty($params['display']) ? $params['display'] : null;
        $create_from = !empty($params['create_from']) ? strtotime(date('Y-m-d 00:00:00', strtotime(str_replace('/', '-', $params['create_from'])))) : null;
        $create_to = !empty($params['create_to']) ? strtotime(date('Y-m-d 23:59:59', strtotime(str_replace('/', '-', $params['create_to'])))) : null;
        $staff_id = !empty($params['staff_id']) ? intval($params['staff_id']) : null;
        $city_id = !empty($params['city_id']) ? intval($params['city_id']) : null;
        $ward_id = !empty($params['ward_id']) ? intval($params['ward_id']) : null;
        $district_id = !empty($params['district_id']) ? intval($params['district_id']) : null;
        $status = !empty($params['status']) ? $params['status'] : null;
        $source = !empty($params['source']) ? $params['source'] : null;

        $where = [
            'Orders.deleted' => 0,
            'Orders.status !=' => DRAFT
        ];

        $display_report = "'%d/%m/%Y'";
        switch ($display) {
            case 'by_date':
                $display_report = "'%d/%m/%Y'";
                break;

            case 'by_month':
                $display_report = "'%m/%Y'";
                break;

            case 'by_year':
                $display_report = "'%Y'";
                break;
            
            default:
                $display_report = "'%d/%m/%Y'";
                break;
        }

        if(!empty($create_from)) {
            $where['Orders.created >='] = $create_from;
        }

        if(!empty($create_to)){
            $where['Orders.created <='] = $create_to;
        }

        if(!empty($staff_id)){
            $where['Orders.staff_id'] = $staff_id;
        }

        if(!empty($status)){
            $where['Orders.status'] = $status;
        }

        if(!empty($source)){
            $where['Orders.source'] = $source;
        }


        if(!empty($city_id) || !empty($district_id) || !empty($ward_id) || (!empty($type) && $type == 'city')) {
            $get_contact = true;
        }


        if(!empty($city_id)){
            $where['OrdersContact.city_id'] = $city_id;
        }

        if(!empty($district_id)){
            $where['OrdersContact.district_id'] = $district_id;
        }

        if(!empty($ward_id)){
            $where['OrdersContact.ward_id'] = $ward_id;
        }

        $contain = [];

        if(!empty($get_contact)){
            $contain[] = 'OrdersContact';
        }

        $type_group = null;
        switch ($type) {
            case 'the_time':
                $type_group = 'the_time';
                break;

            case 'city':
                $type_group = 'OrdersContact.city_id';
                $where['OrdersContact.city_id >'] = 0;
                break;

            case 'staff':
                $type_group = 'Orders.staff_id';
                $where['Orders.staff_id >'] = 0;
                break;

            case 'source':
                $type_group = 'source';
                break;
            case 'the_time':
            default:
                $type_group = 'the_time';
                break;
        }

        $query = $this->find()->contain($contain)->where($where);
        $select = [
            'the_time' => $query->func()->from_unixtime([
                'Orders.created' => 'identifier',
                $display_report => 'literal'
            ]),
            'origin' => $query->func()->sum('Orders.total_origin'), //tổng tiền ban đầu trước chiết khấu
            'number_order' => $query->func()->count('Orders.id'), // số đơn hàng
            'count_items' => $query->func()->sum('Orders.count_items'), // tổng số sản phẩm trong đơn hàng
            'shipping' => $query->func()->sum('Orders.shipping_fee_customer'), // tổng tiền phí vận chuyển cho khách hàng
            'coupon' => $query->func()->sum('Orders.total_coupon'), // tổng tiền coupon
            'discount' => $query->func()->sum('Orders.total_discount'), // tổng tiền giảm giá
            'affiliate' => $query->func()->sum('Orders.total_affiliate'), // tổng tiền giảm giá nhập mã giới thiệu
            'vat' => $query->func()->sum('Orders.total_vat'), // tổng tiền vat
            'debt' => $query->func()->sum('Orders.debt'), //tổng tiền còn nợ
            'total' => $query->func()->sum('Orders.total'), //tổng tiền doanh thu
            'created' => 'Orders.created'
        ];

        //hiển thị thông tin theo tỉnh thành
        if(!empty($type) && $type == 'city') {
            $select[] = 'OrdersContact.city_id';
        }
        //hiển thị thông tin theo nhân viên
        if(!empty($type) && $type == 'staff') {
            $select[] = 'Orders.staff_id';
        }
        //hiển thị thông tin theo nguồn đơn hàng
        if(!empty($type) && $type == 'source') {
            $select[] = 'Orders.source';
        }

        // sort by
        $sort_string = 'Orders.created DESC';
        if(!empty($sort_field)){
            switch($sort_field){
                case 'created':
                    $sort_string = 'Orders.created ' . $sort_type;
                break;

                case 'origin':
                    $sort_string = 'origin ' . $sort_type;
                break;

                case 'number_order':
                    $sort_string = 'number_order ' . $sort_type;
                break;

                case 'count_items':
                    $sort_string = 'count_items ' . $sort_type;
                break;

                case 'shipping':
                    $sort_string = 'shipping ' . $sort_type;
                break;

                case 'coupon':
                    $sort_string = 'coupon ' . $sort_type;
                break;

                case 'affiliate':
                    $sort_string = 'affiliate ' . $sort_type;
                break;

                case 'vat':
                    $sort_string = 'vat ' . $sort_type;
                break;

                case 'debt':
                    $sort_string = 'debt ' . $sort_type;
                break;

                case 'total':
                    $sort_string = 'total ' . $sort_type;
                break;

                case 'city_id':
                    if($get_contact) {
                        $sort_string = 'OrdersContact.city_id ' . $sort_type;
                    } else {
                        $sort_string = 'Orders.created DESC';
                    }
                break;

                case 'staff':
                    $sort_string = 'Orders.staff_id ' . $sort_type;
                break;

                case 'source':
                    $sort_string = 'Orders.source ' . $sort_type;
                break;
            }
        }

        return $query->select($select)->group([$type_group])->order($sort_string);
    }

    public function formatReportOrder($data_format = [], $params = []) 
    {
        if(empty($data_format)) return [];
        $result = $item_report = [];
        $pagination = !empty($params['pagination']) ? $params['pagination'] : [];

        $total_number_order = $total_origin = $total_coupon = $total_discount = $total_vat = $total_affiliate = $total_debt = $total_total = $total_shipping = $total_all_discount = $all_discount = $total_count_items = $total_order_done = $total_order_cancel = $total_cvr = 0;

        $cities = TableRegistry::get('Cities')->getListCity();
        $list_source = Hash::combine(TableRegistry::get('Objects')->find()->where([
            'type' => ORDER_SOURCE,
            'deleted' => 0
        ])->order('is_default DESC')->toArray(), '{n}.code', '{n}.name');
        
        foreach ($data_format as $k => $data) {

            $vat = !empty($data['vat']) ? $data['vat'] : 0;
            $discount = !empty($data['discount']) ? intval($data['discount']) : 0;
            $affiliate = !empty($data['affiliate']) ? intval($data['affiliate']) : 0;

            $total_number_order += !empty($data['number_order']) ? intval($data['number_order']) : 0;
            $total_count_items += !empty($data['count_items']) ? intval($data['count_items']) : 0;
            $total_origin += !empty($data['origin']) ? intval($data['origin']) : 0;
            $total_coupon += !empty($data['coupon']) ? intval($data['coupon']) : 0;
            $total_discount += $discount;
            $total_vat += $vat;
            $total_affiliate += !empty($data['affiliate']) ? intval($data['affiliate']) : 0;
            $total_shipping += !empty($data['shipping']) ? intval($data['shipping']) : 0;
            $total_debt += !empty($data['debt']) ? intval($data['debt']) : 0;
            $total_total += !empty($data['total']) ? intval($data['total']) : 0;
            $all_discount = $discount + $affiliate;
            $total_all_discount += $all_discount;

            $number_order = !empty($data['number_order']) ? intval($data['number_order']) : 0;

            $item_report[$k] = [
                'the_time' => !empty($data['the_time']) ? $data['the_time'] : null,
                'created' => !empty($data['created']) ? $data['created'] : null,
                'origin' => !empty($data['origin']) ? $data['origin'] : 0,
                'number_order' => $number_order,
                'count_items' => !empty($data['count_items']) ? $data['count_items'] : 0,
                'shipping' => !empty($data['shipping']) ? $data['shipping'] : 0,
                'coupon' => !empty($data['coupon']) ? $data['coupon'] : 0,
                'discount' => !empty($data['discount']) ? $data['discount'] : 0,
                'all_discount' => !empty($all_discount) ? $all_discount : 0,
                'affiliate' => !empty($data['affiliate']) ? $data['affiliate'] : 0,
                'vat' => $vat,
                'debt' => !empty($data['debt']) ? $data['debt'] : 0,
                'total' => !empty($data['total']) ? $data['total'] : 0,
                'order_done' => null,
                'order_cancel' => null,
                'cvr' => null
            ];

            if(!empty($data['OrdersContact'])){
                $city_id = !empty($data['OrdersContact']['city_id']) ? intval($data['OrdersContact']['city_id']) : null;
                $item_report[$k]['city_name'] = !empty($cities[$city_id]) ? $cities[$city_id] : null;
            }

            if(!empty($data['staff_id'])) {
                $user_info = TableRegistry::get('Users')->find()->select(['Users.full_name'])->where(['Users.id' => $data['staff_id']])->first();
                $item_report[$k]['staff_name'] = !empty($user_info['full_name']) ? $user_info['full_name'] : null;
            }

            if(!empty($data['source'])) {
                $item_report[$k]['source'] = !empty($list_source[$data['source']]) ? $list_source[$data['source']] : null;
            }

            if(isset($data['order_done'])){
                $total_order_done += intval($data['order_done']);
                $item_report[$k]['order_done'] = isset($data['order_done']) ? intval($data['order_done']) : 0;
            }

            if(isset($data['order_cancel'])) {
                $total_order_cancel += intval($data['order_cancel']);
                $item_report[$k]['order_cancel'] = isset($data['order_cancel']) ? $data['order_cancel'] : 0;
            }

            $number_order_done = !empty($data['number_order_done']) ? intval($data['number_order_done']) : 0;
            $cvr = round(floatval(($number_order_done / $number_order) * 100), 2);
            $item_report[$k]['cvr'] = $cvr;
            $total_cvr += $cvr;
        }
        
        $result = [
            'item_report' => $item_report,
            'total_number_order' => $total_number_order,
            'total_count_items' => $total_count_items,
            'total_origin' => $total_origin,
            'total_coupon' => $total_coupon,
            'total_discount' => $total_discount,
            'total_all_discount' => !empty($total_all_discount) ? $total_all_discount : 0,
            'total_vat' => $total_vat,
            'total_affiliate' => $total_affiliate,
            'total_shipping' => $total_shipping,
            'total_total' => $total_total,
            'total_debt' => $total_debt,
            'total_order_done' => !empty($total_order_done) ? $total_order_done : null,
            'total_order_cancel' => !empty($total_order_cancel) ? $total_order_cancel : null,
            'avg_number_order' => null,
            'avg_count_items' => null,
            'avg_origin' => null,
            'avg_discount' => null,
            'avg_all_discount' => null,
            'avg_vat' => null,
            'avg_affiliate' => null,
            'avg_shipping' => null,
            'avg_total' => null,
            'avg_debt' => null,
            'avg_order_done' => null,
            'avg_order_cancel' => null,
            'avg_cvr' => null
        ];

        if(!empty($pagination['current'])) {
            $result['avg_number_order'] = round($total_number_order / $pagination['current'], 0);
            $result['avg_count_items'] = round($total_count_items / $pagination['current'], 0);
            $result['avg_origin'] = round($total_origin / $pagination['current'], 0);
            $result['avg_discount'] = round($total_discount / $pagination['current'], 0);
            $result['avg_all_discount'] = round($result['total_all_discount'] / $pagination['current'], 0);
            $result['avg_vat'] = round($total_vat / $pagination['current'], 0);
            $result['avg_affiliate'] = round($total_affiliate / $pagination['current'], 0);
            $result['avg_shipping'] = round($total_shipping / $pagination['current'], 0);
            $result['avg_total'] = round($total_total / $pagination['current'], 0);
            $result['avg_debt'] = round($total_debt / $pagination['current'], 0);

            $result['avg_order_done'] = !empty($total_order_done) ? round($total_order_done / $pagination['current'], 0) : 0;
            $result['avg_order_cancel'] = !empty($total_order_cancel) ? round($total_order_cancel / $pagination['current'], 0) : 0;
            $result['avg_cvr'] = !empty($total_cvr) ? round($total_cvr / $pagination['current'], 2) : 0;
        }

        return $result;
    }
}