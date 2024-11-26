<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use App\Model\Behavior\UnixTimestampBehavior;
use Cake\Utility\Hash;

class OrdersItemTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('orders_item');

        $this->setPrimaryKey('id');
    

        $this->hasOne('Orders', [
            'className' => 'Publishing.Orders',
            'foreignKey' => 'id',
            'bindingKey' => 'order_id',
            'joinType' => 'INNER',
            'conditions' => [
                'Orders.deleted' => 0
            ],
            'propertyName' => 'Orders'
        ]);

        $this->hasOne('ProductsItem', [
            'className' => 'Publishing.ProductsItem',
            'foreignKey' => 'id',
            'bindingKey' => 'product_item_id',
            'joinType' => 'INNER',
            'propertyName' => 'ProductsItem'
        ]);

        $this->hasOne('CategoryProduct', [
            'className' => 'CategoriesProduct',
            'foreignKey' => 'product_id',
            'bindingKey' => 'product_id',
            'joinType' => 'LEFT',
            'propertyName' => 'CategoryProduct'
        ]);

        $this->hasOne('Products', [
            'className' => 'Publishing.Products',
            'foreignKey' => 'id',
            'bindingKey' => 'product_id',
            'joinType' => 'INNER',
            'conditions' => [
                'Products.deleted' => 0
            ],
            'propertyName' => 'Products'
        ]);

        $this->hasOne('ProductsContent', [
            'className' => 'Publishing.ProductsContent',
            'foreignKey' => 'product_id',
            'bindingKey' => 'product_id',
            'joinType' => 'LEFT',
            'propertyName' => 'ProductsContent'
        ]);

        $this->hasMany('ProductsItemAttribute', [
            'className' => 'ProductsItemAttribute',
            'foreignKey' => 'product_item_id',
            'bindingKey' => 'product_item_id',
            'joinType' => 'LEFT',
            'propertyName' => 'ProductsItemAttribute'
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        return $validator;
    }


    public function checkItemProductExist($product_item_id = null)
    {
        if(empty($product_item_id)) return false;

        $item_info = TableRegistry::get('OrdersItem')->find()->where([
            'OrdersItem.product_item_id' => $product_item_id
        ])->first();

        return !empty($item_info) ? true : false;
    }

    public function reportProduct($params = [])
    {   
        // sort
        $sort = !empty($params[SORT]) ? $params[SORT] : [];
        $sort_field = !empty($sort[FIELD]) ? $sort[FIELD] : null;
        $sort_type = !empty($sort[SORT]) ? $sort[SORT] : 'DESC';

        $create_from = !empty($params['create_from']) ? strtotime(date('Y-m-d 00:00:00', strtotime(str_replace('/', '-', $params['create_from'])))) : null;
        $create_to = !empty($params['create_to']) ? strtotime(date('Y-m-d 23:59:59', strtotime(str_replace('/', '-', $params['create_to'])))) : null;
        $category_id = !empty($params['category_id']) ? intval($params['category_id']) : null;
        $brand_id = !empty($params['brand_id']) && intval($params['brand_id']) ? $params['brand_id'] : null;
        $lang = !empty($params[LANG]) ? $params[LANG] : TableRegistry::get('Languages')->getDefaultLanguage();
        $status = !empty($params['status']) ? $params['status'] : null;

        $where = [
            'Orders.deleted' => 0,
            'Orders.status !=' => DRAFT
        ];

        if(!empty($create_from)) {
            $where['Orders.created >='] = $create_from;
        }

        if(!empty($create_to)){
            $where['Orders.created <='] = $create_to;
        }

        if(!empty($status)){
            $where['Orders.status'] = $status;
        }

        $contain = [
            'Orders', 
            'Products',
            'ProductsItem',
            'ProductsItemAttribute',
            'ProductsContent' => function ($q) use ($lang) {
                return $q->where([
                    'ProductsContent.lang' => $lang
                ]);
            }, 
        ];

        if(!empty($category_id)){
            $contain[] = 'CategoryProduct';
            $where['CategoryProduct.category_id'] = $category_id;
            // lấy id danh mục con
            $child_category_ids = TableRegistry::get('Categories')->getAllChildCategoryId($category_id);
            if(!empty($child_category_ids)){
                $contain[] = 'CategoryProduct';
                $where['CategoryProduct.category_id IN'] = $child_category_ids;
            }            
        }

        if(!empty($brand_id)){
            $where['Products.brand_id'] = $brand_id;
        }
        
        $query = $this->find()->contain($contain)->where($where);
        $select = [
            'total_price' => $query->func()->avg('OrdersItem.price'),
            'total_quantity' => $query->func()->sum('OrdersItem.quantity'),
            'total_discount' => $query->func()->sum('OrdersItem.total_discount'),
            'total_vat' => $query->func()->sum('OrdersItem.total_vat'),
            'total_item' => $query->func()->sum('OrdersItem.total_item')
        ];

        $select[] = 'OrdersItem.product_item_id';
        $select[] = 'OrdersItem.product_id';
        $select[] = 'ProductsItem.code';
        $select[] = 'ProductsItem.images';
        $select[] = 'ProductsContent.name';
        $select[] = 'Products.created';

        // sort by
        $sort_string = 'total_item DESC';
        if(!empty($sort_field)){
            switch($sort_field){
                case 'quantity':
                    $sort_string = 'total_quantity ' . $sort_type . ', total_item DESC';
                break;

                case 'code':
                    $sort_string = 'ProductsItem.code ' . $sort_type . ', total_item DESC';
                break;

                case 'name':
                    $sort_string = 'ProductsContent.name ' . $sort_type . ', total_item DESC';
                break;

                case 'price':
                    $sort_string = 'total_price ' . $sort_type . ', total_item DESC';
                break;

                case 'quantity':
                    $sort_string = 'total_quantity ' . $sort_type . ', total_item DESC';
                break;

                case 'discount':
                    $sort_string = 'total_discount ' . $sort_type . ', total_item DESC';
                break;

                case 'vat':
                    $sort_string = 'total_vat ' . $sort_type . ', total_item DESC';
                break;

                case 'total':
                    $sort_string = 'total_item ' . $sort_type;
                break;
            }
        }

        $result = $query->select($select)->group(['OrdersItem.product_item_id'])->order($sort_string);

        return $result;
    }

    public function formatReportProduct($data = [], $params = []) 
    {
        if(empty($data)) return [];

        $lang = !empty($params[LANG]) ? $params[LANG] : TableRegistry::get('Languages')->getDefaultLanguage();
        $pagination = !empty($params['pagination']) ? $params['pagination'] : null;

        $result = $item_report = $name_extendsion = [];
        $total_quantity = $total_total = $total_product_done = $total_product_cancel = 0;

        // get name extend
        $all_options = TableRegistry::get('AttributesOptions')->getAll($lang);
        $all_attributes = Hash::combine(TableRegistry::get('Attributes')->getAll($lang), '{n}.id', '{n}', '{n}.attribute_type');
        $attributes_item = !empty($all_attributes[PRODUCT_ITEM]) ? $all_attributes[PRODUCT_ITEM] : [];
        
        foreach ($data as $k => $report) {
            $total_quantity += intval($report['total_quantity']);
            $total_total += intval($report['total_item']);

            $extend = '';
            if(!empty($report['ProductsItemAttribute'])){
                foreach($report['ProductsItemAttribute'] as $attribute_item){
                    $attribute_info = !empty($attributes_item[$attribute_item['attribute_id']]) ? $attributes_item[$attribute_item['attribute_id']] : [];
                    $input_type = !empty($attribute_info['input_type']) ? $attribute_info['input_type'] : null;
                    if($input_type == SPECICAL_SELECT_ITEM && !empty($attribute_item['value'])){
                        $option_name = !empty($all_options[$attribute_item['value']]['name']) ? $all_options[$attribute_item['value']]['name'] : null;

                        if(!empty($option_name)){
                            $name_extendsion[] = $option_name;
                        }
                    }
                }
                $extend = !empty($name_extendsion) ? ' (' . implode(' - ', $name_extendsion) . ')' : '';
            }

            $name = !empty($report['ProductsContent']['name']) ? $report['ProductsContent']['name'] : '';

            $name_extend = $name . $extend;
            $images = !empty($report['ProductsItem']['images']) ? json_decode($report['ProductsItem']['images'], true) : [];

            $quantity = !empty($report['total_quantity']) ? intval($report['total_quantity']) : 0;
            $item_report[$k] = [
                'product_id' => !empty($report['product_id']) ? $report['product_id'] : null,
                'product_item_id' => !empty($report['product_item_id']) ? $report['product_item_id'] : null,
                'price' => !empty($report['total_price']) ? floatval($report['total_price']) : 0,
                'quantity' => $quantity,
                'discount' => !empty($report['total_discount']) ? floatval($report['total_discount']) : 0,
                'vat' => !empty($report['total_vat']) ? floatval($report['total_vat']) : 0,
                'total' => !empty($report['total_item']) ? floatval($report['total_item']) : 0,
                'product_created' => !empty($report['Products']['created']) ? $report['Products']['created'] : null,
                'product_done' => null,
                'product_cancel' => null,
                'cvr' => null,
                'name' => $name,
                'name_extend' => $name_extend,
                'code' => !empty($report['ProductsItem']['code']) ? $report['ProductsItem']['code'] : null,
                'images' => $images
            ];

            if(isset($report['product_done'])){
                $total_product_done += intval($report['product_done']);
                $item_report[$k]['product_done'] = !empty($report['product_done']) ? floatval($report['product_done']) : 0;
            }

            if(isset($report['product_cancel'])) {
                $total_product_cancel += floatval($report['product_cancel']);
                $item_report[$k]['product_cancel'] = !empty($report['product_cancel']) ? floatval($report['product_cancel']) : 0;
            }

            $number_product_done = !empty($report['number_product_done']) ? intval($report['number_product_done']) : 0;
            $cvr = round(floatval(($number_product_done / $quantity) * 100), 2);
            $item_report[$k]['cvr'] = $cvr;

        }

        $result = [
            'item_report' => $item_report,
            'total_quantity' => $total_quantity,
            'total_total' => $total_total,
            'total_product_done' => !empty($total_product_done) ? $total_product_done : null,
            'total_product_cancel' => !empty($total_product_cancel) ? $total_product_cancel : null,
            'avg_quantity' => null,
            'avg_total' => null,
            'avg_product_done' => null,
            'avg_product_cancel' => null
        ];

        if(!empty($pagination['current'])) {
            $result['avg_quantity'] = !empty($total_quantity) ? round($total_quantity / $pagination['current'], 0) : 0;
            $result['avg_total'] = !empty($total_total) ? round($total_total / $pagination['current'], 0) : 0;
            $result['avg_product_done'] = !empty($total_product_done) ? round($total_product_done / $pagination['current'], 0) : 0;
            $result['avg_product_cancel'] = !empty($total_product_cancel) ? round($total_product_cancel / $pagination['current'], 2) : 0;
        }

        return $result;
    }
}