<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use App\Model\Behavior\UnixTimestampBehavior;
use Cake\Utility\Text;
use Cake\Utility\Hash;

class ProductsItemTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('products_item');

        $this->setPrimaryKey('id');

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

        $this->hasOne('Links', [
            'className' => 'Publishing.Links',
            'foreignKey' => 'foreign_id',
            'bindingKey' => 'product_id',
            'joinType' => 'LEFT',
            'propertyName' => 'Links'
        ]);

        $this->hasMany('ProductsItemAttribute', [
            'className' => 'ProductsItemAttribute',
            'foreignKey' => 'product_item_id',
            'joinType' => 'LEFT',
            'propertyName' => 'ProductsItemAttribute'
        ]);

        $this->hasOne('CategoryProduct', [
            'className' => 'CategoriesProduct',
            'foreignKey' => 'product_id',
            'bindingKey' => 'product_id',
            'joinType' => 'LEFT',
            'propertyName' => 'CategoryProduct'
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        return $validator;
    }

    public function queryListProductsItem($params = [])
    {
        $table = TableRegistry::get('ProductsItem');

        // sort
        $sort = !empty($params[SORT]) ? $params[SORT] : [];
        $sort_field = !empty($sort[FIELD]) ? $sort[FIELD] : null;
        $sort_type = !empty($sort[SORT]) && in_array($sort[SORT], [DESC, ASC]) ? $sort[SORT] : DESC;

        // filter
        $filter = !empty($params[FILTER]) ? $params[FILTER] : [];
        $lang = !empty($filter[LANG]) ? $filter[LANG] : TableRegistry::get('Languages')->getDefaultLanguage();
        $type = !empty($filter[TYPE]) ? $filter[TYPE] : null;
        $keyword = !empty($filter['keyword']) ? trim($filter['keyword']) : null;
        $price = !empty($filter['price']) ? $filter['price'] : null;
        $status = isset($filter['status']) && $filter['status'] != '' ? intval($filter['status']) : null;        
        $display_product = isset($filter['display_product']) && $filter['display_product'] != '' ? intval($filter['display_product']) : null;
        $id_categories = !empty($filter['id_categories']) ? $filter['id_categories'] : [];
        $category_id = !empty($filter['category_id']) ? intval($filter['category_id']) : null;
        
        $price_from = !empty($filter['price_from']) ? floatval(str_replace(',', '', $filter['price_from'])) : null;
        $price_to = !empty($filter['price_to']) ? floatval(str_replace(',', '', $filter['price_to'])) : null;
        $brand_id = !empty($filter['brand_id']) ? intval($filter['brand_id']) : null;        
        $check_has_image = isset($filter['check_has_image']) && $filter['check_has_image'] != '' ? intval($filter['check_has_image']) : null;
        $created_by = !empty($filter['created_by']) ? intval($filter['created_by']) : null;
        $product_mark = !empty($filter['product_mark']) ? $filter['product_mark'] : null;
        $stocking = isset($filter['stocking']) && $filter['stocking'] != '' ? intval($filter['stocking']) : null;

        $create_from = !empty($filter['create_from']) ? strtotime(date('Y-m-d 00:00:00', strtotime(str_replace('/', '-', $filter['create_from'])))) : null;
        $create_to = !empty($filter['create_to']) ? strtotime(date('Y-m-d 23:59:59', strtotime(str_replace('/', '-', $filter['create_to'])))) : null;

        $has_image = isset($filter['has_image']) && $filter['has_image'] != '' ? intval($filter['has_image']) : null;
        $fields = ['Products.id', 'Products.featured', 'Products.status', 'Products.draft', 'Products.width', 'Products.length', 'Products.height', 'Products.weight', 'Products.width_unit', 'Products.length_unit', 'Products.height_unit', 'Products.weight_unit', 'Products.seo_score', 'Products.keyword_score', 'Products.created_by', 'ProductsContent.name', 'ProductsContent.lang', 'Links.id', 'Links.url', 'ProductsItem.id', 'ProductsItem.product_id', 'ProductsItem.code', 'ProductsItem.price', 'ProductsItem.discount_percent', 'ProductsItem.price_special', 'ProductsItem.time_start_special', 'ProductsItem.time_end_special', 'ProductsItem.images', 'ProductsItem.quantity_available', 'ProductsItem.status'];

        //contain
        $contain = [
            'Products',
            'ProductsContent' => function ($q) use ($lang) {
                return $q->where([
                    'ProductsContent.lang' => $lang
                ]);
            }, 
            'Links' => function ($q) use ($type, $lang) {
                return $q->where([
                    'Links.type' => PRODUCT_DETAIL,
                    'Links.lang' => $lang,
                    'Links.deleted' => 0
                ]);
            },
            'ProductsItemAttribute',
        ];

        $sort_string = 'Products.id DESC, ProductsItem.id ASC';
        if(!empty($params[SORT])){
            switch($sort_field){
                case 'id':
                case 'product_item_id':
                    $sort_string = 'ProductsItem.id '. $sort_type;
                break;

                case 'name':
                    $sort_string = 'ProductsContent.name '. $sort_type .', Products.position DESC, Products.id DESC';
                break;

                case 'price':
                    $sort_string = 'ProductsItem.price '. $sort_type .', ProductsItem.position DESC, ProductsItem.id DESC';
                break;

                case 'price_special':
                    $sort_string = 'ProductsItem.price_special '. $sort_type .', ProductsItem.position DESC, ProductsItem.id DESC';
                break;

                case 'quantity_available':
                    $sort_string = 'ProductsItem.quantity_available '. $sort_type .', ProductsItem.position DESC, ProductsItem.id DESC';
                break;

                case 'status_product':
                    $sort_string = 'Products.status '. $sort_type .', Products.position DESC, Products.id DESC';
                break;

                case 'status':
                    $sort_string = 'ProductsItem.status '. $sort_type .', ProductsItem.position DESC, ProductsItem.id DESC';
                break;

                case 'position':
                    $sort_string = 'Products.position '. $sort_type .', Products.id DESC';
                break;

                case 'created':
                    $sort_string = 'Products.created '. $sort_type .', Products.position DESC, Products.id DESC';
                break;

                case 'updated':
                    $sort_string = 'Products.updated '. $sort_type .', Products.position DESC, Products.id DESC';
                break;

                case 'featured':
                    $sort_string = 'Products.featured '. $sort_type .', Products.position DESC, Products.id DESC';
                break;

                case 'created_by':
                    $sort_string = 'Products.created_by '. $sort_type .', Products.position DESC, Products.id DESC';
                break;             
            }
        }

        // filter by conditions
        $where = [
            'Products.deleted' => 0,
            'ProductsItem.deleted' => 0
        ];    

        if(!empty($keyword)){
            $where['ProductsContent.search_unicode LIKE'] = '%' . Text::slug(strtolower($keyword), ' ') . '%';
        }

        if(!is_null($status)){
            $where['ProductsItem.status'] = $status;
        }

        if(!is_null($display_product)){
            $where['Products.status'] = $display_product;
        }

        if(!is_null($display_product) && $display_product == 3){
            $where = ['Products.draft' => 1];
        }

        if(!empty($category_id)){
            $contain[] = 'CategoryProduct';
            $where['category_id'] = $category_id;
        }

        if(!is_null($price_to)) {
            $where['ProductsItem.price <= '] = str_replace(',', '', $price_to);
        }

        if(!is_null($price_from)) {
            $where['ProductsItem.price >= '] = str_replace(',', '', $price_from);
        }

        if(!empty($brand_id)){
            $where['Products.brand_id'] = $brand_id;
        }

        if($product_mark == 'featured'){
            $where['Products.featured'] = 1;
        }

        if($product_mark == 'discount'){
            $where['ProductsItem.price_special >'] = 0;
        }
        
        if($stocking === 1){
            $where['ProductsItem.quantity_available >'] = 0;
        }

        if($stocking === 0){
            if(!isset($where['OR'])) $where['OR'] = [];

            $where['OR'][] = ['ProductsItem.quantity_available IS' => null];
            $where['OR'][] = ['ProductsItem.quantity_available =' => 0];
        }

        if(!empty($create_from)){
            $where['Products.created >='] = $create_from;
        }

        if(!empty($create_to)){
            $where['Products.created <='] = $create_to;
        }

        if(!is_null($has_image) && $has_image == 0){
            $where['ProductsItem.images IS'] = null;
        }

        if(!is_null($has_image) && $has_image == 1){
            $where['ProductsItem.images IS NOT'] = null;
        }

        if(!empty($created_by)){
            $where['Products.created_by'] = $created_by;
        }

        return $table->find()->contain($contain)->where($where)->select($fields)->group('ProductsItem.id')->order($sort_string);
    }

    public function getDetailProductItem($product_item_id = null, $lang = null, $params = [])
    {
        if(empty($product_item_id) || empty($lang)) return [];
        $get_attribute = !empty($params['get_attribute']) ? true : false;

        $fields = ['Products.id', 'Products.width', 'Products.length', 'Products.height', 'Products.weight', 'Products.width_unit', 'Products.length_unit', 'Products.height_unit', 'Products.weight_unit', 'ProductsContent.name', 'ProductsContent.lang', 'ProductsItem.id', 'ProductsItem.product_id', 'ProductsItem.code', 'ProductsItem.price', 'ProductsItem.discount_percent', 'ProductsItem.price_special', 'ProductsItem.time_start_special', 'ProductsItem.time_end_special', 'ProductsItem.images', 'ProductsItem.quantity_available', 'ProductsItem.status', 'Links.id', 'Links.url'];
        
        $contain = [
            'Products',
            'ProductsContent' => function ($q) use ($lang) {
                return $q->where([
                    'ProductsContent.lang' => $lang
                ]);
            },
            'Links' => function ($q) use ($lang) {
                return $q->where([
                    'Links.type' => PRODUCT_DETAIL,
                    'Links.lang' => $lang,
                    'Links.deleted' => 0
                ]);
            }
        ];

        $where = [
            'ProductsItem.id' => $product_item_id,
            'ProductsItem.deleted' => 0
        ];

        if($get_attribute){
            $contain[] = 'ProductsItemAttribute';
        }

        $item_info = TableRegistry::get('ProductsItem')->find()->contain($contain)->where($where)->select($fields)->first();
        $result = $this->formatProductItemDetail($item_info, $lang);
        
        return $result;
    }

    public function formatProductItemDetail($data = [], $lang = null)
    {
        if(empty($data)) return [];
        
        // get name extend
        $name_extend = [];
        if(!empty($data['ProductsItemAttribute'])){
            $all_options = TableRegistry::get('AttributesOptions')->getAll($lang);
            $all_attributes = Hash::combine(TableRegistry::get('Attributes')->getAll($lang), '{n}.id', '{n}', '{n}.attribute_type');
            $attributes_item = !empty($all_attributes[PRODUCT_ITEM]) ? $all_attributes[PRODUCT_ITEM] : [];

            foreach($data['ProductsItemAttribute'] as $attribute_item){
                $attribute_info = !empty($attributes_item[$attribute_item['attribute_id']]) ? $attributes_item[$attribute_item['attribute_id']] : [];
                $input_type = !empty($attribute_info['input_type']) ? $attribute_info['input_type'] : null;
                if($input_type == SPECICAL_SELECT_ITEM && !empty($attribute_item['value'])){
                    $option_name = !empty($all_options[$attribute_item['value']]['name']) ? $all_options[$attribute_item['value']]['name'] : null;
                    if(!empty($option_name)){
                        $name_extend[] = $option_name;
                    }
                }
            }
        }

        $extend = !empty($name_extend) ? ' (' . implode(' - ', $name_extend) . ')' : '';
        $name = !empty($data['ProductsContent']['name']) ? $data['ProductsContent']['name'] : '';
        $name_extend = $name . $extend;

        $images = !empty($data['images']) ? json_decode($data['images'], true) : [];

        $price = !empty($data['price']) ? floatval($data['price']) : null;
        $price_special = !empty($data['price_special']) ? floatval($data['price_special']) : null;
        
        // check apply special
        $apply_special = $compare_time = false;
        $current_time = strtotime(date('Y-m-d H:i:s'));
        if((empty($data['time_start_special']) && empty($data['time_end_special'])) || (!empty($data['time_start_special']) && !empty($data['time_end_special']) && $data['time_start_special'] <= $current_time && $data['time_end_special'] >= $current_time)){
            $compare_time = true;
        }

        if(!empty($price_special) && $compare_time){
            $apply_special = true;
        }

        // get date special
        $date_special = null;
        $time_start_special = !empty($data['time_start_special']) ? date('d/m/Y', $data['time_start_special']) : null;
        $time_end_special = !empty($data['time_end_special']) ? date('d/m/Y', $data['time_end_special']) : null;
        if(!empty($time_start_special) && !empty($time_end_special)){
            $date_special = $time_start_special . ' → ' . $time_end_special;
        }

        $result = [
            'id' => !empty($data['id']) ? intval($data['id']) : null,
            'product_id' => !empty($data['Products']['id']) ? intval($data['Products']['id']) : null,
            'product_item_id' => !empty($data['id']) ? intval($data['id']) : null,
            'code' => !empty($data['code']) ? $data['code'] : null,
            'name' => $name,
            'name_extend' => $name_extend,
            'price' => $price,
            'price_special' => $price_special,
            'discount_percent' => !empty($data['discount_percent']) ? floatval($data['discount_percent']) : null,
            'time_start_special' => $time_start_special,
            'time_end_special' => $time_end_special,
            'date_special' => $date_special,
            'apply_special' => $apply_special,
            'images' => $images,
            'quantity_available' => !empty($data['quantity_available']) ? intval($data['quantity_available']) : null,
            'status' => !empty($data['status']) ? 1 : 0,
            'product_status' => !empty($data['Products']['status']) ? intval($data['Products']['status']) : 0,
            'product_draft' => !empty($data['Products']['draft']) ? intval($data['Products']['draft']) : 0,
            'url' => !empty($data['Links']['url']) ? $data['Links']['url'] : null,

            'width' => !empty($data['Products']['width']) ? intval($data['Products']['width']) : null,
            'length' => !empty($data['Products']['length']) ? intval($data['Products']['length']) : null,
            'height' => !empty($data['Products']['height']) ? intval($data['Products']['height']) : null,
            'weight' => !empty($data['Products']['weight']) ? intval($data['Products']['weight']) : null,
            
            'width_unit' => !empty($data['Products']['width_unit']) ? $data['Products']['width_unit'] : null,
            'length_unit' => !empty($data['Products']['length_unit']) ? $data['Products']['length_unit'] : null,
            'height_unit' => !empty($data['Products']['height_unit']) ? $data['Products']['height_unit'] : null,
            'weight_unit' => !empty($data['Products']['weight_unit']) ? $data['Products']['weight_unit'] : null
        ];

        return $result;
    }
}