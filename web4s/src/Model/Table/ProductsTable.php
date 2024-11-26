<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use App\Model\Behavior\UnixTimestampBehavior;
use Cake\Utility\Hash;
use Cake\Utility\Text;

class ProductsTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('products');

        $this->setPrimaryKey('id');

        $this->addBehavior('UnixTimestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                    'updated' => 'existing'
                ]
            ]
        ]);

        $this->hasOne('ProductsContent', [
            'className' => 'Publishing.ProductsContent',
            'foreignKey' => 'product_id',
            'joinType' => 'LEFT',
            'propertyName' => 'ProductsContent'
        ]);

        $this->hasOne('Links', [
            'className' => 'Publishing.Links',
            'foreignKey' => 'foreign_id',
            'joinType' => 'LEFT',
            'propertyName' => 'Links'
        ]);

        $this->belongsTo('User', [
            'className' => 'Publishing.Users',
            'foreignKey' => 'created_by',
            'joinType' => 'LEFT',
            'propertyName' => 'User'
        ]);

        $this->hasOne('CategoryProduct', [
            'className' => 'CategoriesProduct',
            'foreignKey' => 'product_id',
            'joinType' => 'LEFT',
            'propertyName' => 'CategoryProduct'
        ]);

        $this->hasMany('CategoriesProduct', [
            'className' => 'CategoriesProduct',
            'foreignKey' => 'product_id',
            'joinType' => 'LEFT',
            'propertyName' => 'CategoriesProduct'
        ]);

        $this->hasMany('ProductsItem', [
            'className' => 'ProductsItem',
            'foreignKey' => 'product_id',
            'joinType' => 'INNER',
            'propertyName' => 'ProductsItem',
            'conditions' => [
                'ProductsItem.deleted' => 0
            ],
            'sort' => ['ProductsItem.position' => 'ASC']
        ]);

        $this->hasOne('SingleItem', [
            'className' => 'Publishing.ProductsItem',
            'foreignKey' => 'product_id',
            'joinType' => 'INNER',
            'conditions' => [
                'SingleItem.deleted' => 0
            ],
            'propertyName' => 'SingleItem'
        ]);

        $this->hasMany('ProductsAttribute', [
            'className' => 'ProductsAttribute',
            'foreignKey' => 'product_id',
            'joinType' => 'LEFT',
            'propertyName' => 'ProductsAttribute'
        ]);

        $this->hasMany('ProductsItemAttribute', [
            'className' => 'ProductsItemAttribute',
            'foreignKey' => 'product_id',
            'joinType' => 'LEFT',
            'propertyName' => 'ProductsItemAttribute'
        ]);

        $this->hasOne('SingleItemAttribute', [
            'className' => 'Publishing.ProductsItemAttribute',
            'foreignKey' => 'product_id',
            'joinType' => 'INNER',
            'propertyName' => 'SingleItemAttribute'
        ]);

        $this->hasOne('ProductAttribute', [
            'className' => 'Publishing.ProductsAttribute',
            'foreignKey' => 'product_id',
            'joinType' => 'INNER',
            'propertyName' => 'ProductAttribute'
        ]);

        $this->hasMany('ContentMutiple', [
            'className' => 'ProductsContent',
            'foreignKey' => 'product_id',
            'joinType' => 'LEFT',
            'propertyName' => 'ContentMutiple'
        ]);

        $this->hasMany('LinksMutiple', [
            'className' => 'Links',
            'foreignKey' => 'foreign_id',
            'joinType' => 'LEFT',
            'conditions' => [
                'LinksMutiple.type' => PRODUCT_DETAIL,
                'LinksMutiple.deleted' => 0
            ],
            'propertyName' => 'LinksMutiple'
        ]);

        $this->hasMany('TagsRelation', [
            'className' => 'TagsRelation',
            'foreignKey' => 'foreign_id',
            'conditions' => [
                'TagsRelation.type' => PRODUCT_DETAIL
            ],
            'joinType' => 'LEFT',
            'propertyName' => 'TagsRelation'
        ]);

        $this->hasOne('TagProduct', [
            'className' => 'TagsRelation',
            'foreignKey' => 'foreign_id',
            'conditions' => [
                'TagProduct.type' => PRODUCT_DETAIL
            ],
            'joinType' => 'LEFT',
            'propertyName' => 'TagProduct'
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        return $validator;
    }

    public function queryListProducts($params = []) 
    {
        // get info params
        $field = !empty($params[FIELD]) ? $params[FIELD] : SIMPLE_INFO;
        $get_user = !empty($params['get_user']) ? true : false;
        $get_item = !empty($params['get_item']) ? true : false;
        $get_categories = !empty($params['get_categories']) ? $params['get_categories'] : false;
        $get_item_attributes = !empty($params['get_item_attributes']) ? true : false;
        $get_attributes = !empty($params['get_attributes']) ? true : false;
        $get_tags = !empty($params['get_tags']) ? true : false;
        // dung cho danh sach san pham admin
        $get_empty_name = !empty($params['get_empty_name']) ? true : false;

        // sort
        $sort = !empty($params[SORT]) ? $params[SORT] : [];
        $sort_field = !empty($sort[FIELD]) ? $sort[FIELD] : null;
        $sort_type = !empty($sort[SORT]) && in_array($sort[SORT], [DESC, ASC]) ? $sort[SORT] : DESC;

        // filter
        $filter = !empty($params[FILTER]) ? $params[FILTER] : [];
        $lang = !empty($filter[LANG]) ? $filter[LANG] : TableRegistry::get('Languages')->getDefaultLanguage();
        $keyword = !empty($filter[KEYWORD]) ? trim($filter[KEYWORD]) : null;
        $status = isset($filter[STATUS]) && $filter[STATUS] != '' ? intval($filter[STATUS]) : null;
        $draft = isset($filter['draft']) && $filter['draft'] != '' ? intval($filter['draft']) : null;
        $status_item = isset($filter[STATUS_ITEM]) && $filter[STATUS_ITEM] != '' ? intval($filter[STATUS_ITEM]) : null;

        $ids = !empty($filter['ids']) && is_array($filter['ids']) ? $filter['ids'] : [];
        $not_ids = !empty($filter['not_ids']) && is_array($filter['not_ids']) ? $filter['not_ids'] : [];
        $id_categories = !empty($filter['id_categories']) && is_array($filter['id_categories']) ? $filter['id_categories'] : [];
        $id_brands = !empty($filter['id_brands']) && is_array($filter['id_brands']) ? $filter['id_brands'] : [];
            
        $featured = isset($filter['featured']) && $filter['featured'] != '' ? intval($filter['featured']) : null;
        $discount = isset($filter['discount']) && $filter['discount'] != '' ? intval($filter['discount']) : null;
        $stocking = isset($filter['stocking']) && $filter['stocking'] != '' ? intval($filter['stocking']) : null;
        $price_from = !empty($filter['price_from']) ? floatval(str_replace(',', '', $filter['price_from'])) : null;
        $price_to = !empty($filter['price_to']) ? floatval(str_replace(',', '', $filter['price_to'])) : null;
        $tag_id = !empty($filter['tag_id']) ? intval($filter['tag_id']) : null;
        $product_mark = !empty($filter['product_mark']) ? $filter['product_mark'] : null;
        $created_by = !empty($filter['created_by']) ? intval($filter['created_by']) : null;
        $create_from = !empty($filter['create_from']) ? strtotime(date('Y-m-d 00:00:00', strtotime(str_replace('/', '-', $filter['create_from'])))) : null;
        $create_to = !empty($filter['create_to']) ? strtotime(date('Y-m-d 23:59:59', strtotime(str_replace('/', '-', $filter['create_to'])))) : null;

        switch($field){
            case FULL_INFO:
                $fields = ['Products.id', 'Products.url_video', 'Products.type_video', 'Products.files', 'Products.brand_id', 'Products.width', 'Products.length', 'Products.height', 'Products.weight', 'Products.view', 'Products.like', 'Products.rating', 'Products.rating_number', 'Products.comment', 'Products.created_by', 'Products.created', 'Products.position', 'Products.featured', 'Products.catalogue', 'Products.status', 'ProductsContent.name', 'ProductsContent.description', 'ProductsContent.content', 'ProductsContent.seo_title', 'ProductsContent.seo_description', 'ProductsContent.seo_keyword', 'ProductsContent.lang', 'Links.id', 'Links.url'];
            break;

            case LIST_INFO:
                $fields = ['Products.id', 'ProductsContent.name'];
            break;

            case SIMPLE_INFO:
            default:
                $fields = ['Products.id', 'Products.brand_id', 'Products.url_video', 'Products.type_video', 'Products.files', 'Products.rating', 'Products.rating_number', 'Products.view', 'Products.created_by', 'Products.created', 'Products.position', 'Products.featured', 'Products.catalogue', 'Products.status', 'ProductsContent.name', 'ProductsContent.description', 'ProductsContent.lang', 'Links.id', 'Links.url'];
            break;
        }

        $where = ['Products.deleted' => 0];    
        
        //contain
        if(!$get_empty_name){
            $contain = ['ProductsContent', 'Links'];

            $where['ProductsContent.lang'] = $lang;
            $where['Links.lang'] = $lang;
            $where['Links.type'] = PRODUCT_DETAIL;
            $where['Links.deleted'] = 0;
        }else{
            $contain = [
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
        }
        

        // filter by conditions
        if(!empty($keyword)) {            
            $contain[] = 'SingleItem';

            $where['OR'][] = [
                    'ProductsContent.search_unicode LIKE' => '%' . Text::slug(strtolower($keyword), ' ') . '%'
                ];

            $where['OR'][] = [
                    'SingleItem.code LIKE' => '%' . Text::slug(strtolower($keyword), ' ') . '%'
                ];
        }

        if(!is_null($status)){
            $where['Products.status'] = $status;
        }

        if(!is_null($draft)){
            $where['Products.draft'] = $draft;
        }

        if(!is_null($featured)){
            $where['Products.featured'] = $featured;
        }

        if(!empty($ids)){
            $where['Products.id IN'] = $ids;
        }

        if(!empty($not_ids)){
            $where['Products.id NOT IN'] = $not_ids;
        }        

        if(!empty($id_categories)){
            // lay id danh muc con
            $all_category_ids = [];
            foreach($id_categories as $category_id){
                $child_category_ids = TableRegistry::get('Categories')->getAllChildCategoryId($category_id);
                $all_category_ids = array_unique(array_merge($all_category_ids, $child_category_ids));
            }

            if(!empty($all_category_ids)){
                $contain[] = 'CategoryProduct';
                $where['CategoryProduct.category_id IN'] = $all_category_ids;
            }
        }

        if(!empty($tag_id)){
            $contain[] = 'TagProduct';
            $where['TagProduct.tag_id'] = $tag_id;
        }

        if(!empty($id_brands)){
            $where['Products.brand_id IN'] = $id_brands;
        }

        if($product_mark == 'featured'){
            $where['Products.featured'] = 1;
        }

        if(!empty($created_by)){
            $where['Products.created_by'] = $created_by;
        }

        if(!empty($create_from)){
            $where['Products.created >='] = $create_from;
        }

        if(!empty($create_to)){
            $where['Products.created <='] = $create_to;
        }
        
        if(!empty($get_user)){
            $fields[] = 'User.id';
            $fields[] = 'User.full_name';

            $contain[] = 'User';
        }
        
        if(!empty($price_from) || !empty($price_to) || !is_null($discount) || !is_null($status_item) || !is_null($stocking) || !is_null($product_mark)){
            $get_item = 1;
        }

        if(!empty($get_item)){
            $where_item = [];
            if(!empty($price_from)){
                $where['SingleItem.price >='] = $price_from;
                $where_item['ProductsItem.price >='] = $price_from;
            }

            if(!empty($price_to)){
                $where['SingleItem.price <='] = $price_to;
                $where_item['ProductsItem.price <='] = $price_to;
            }

            if(!is_null($status_item)){
                $where['SingleItem.status'] = $status_item;
                $where_item['ProductsItem.status'] = $status_item;
            }

            if($product_mark == 'discount'){
                $where['SingleItem.price_special >'] = 0;
                $where_item['ProductsItem.price_special >'] = 0;
            }

            if(!is_null($discount)){
                $current_time = strtotime(date('Y-m-d H:i:s'));
                $where['SingleItem.price_special >'] = 0;

                if(!isset($where['OR'])) $where['OR'] = [];
                $where['OR'][] = [
                    'SingleItem.time_start_special IS' => null,
                    'SingleItem.time_end_special IS' => null
                ];                
                $where['OR'][] = [
                    'SingleItem.time_start_special <=' => $current_time,
                    'SingleItem.time_end_special >=' => $current_time,
                ];

                $where_item['ProductsItem.price_special >'] = 0;
                if(!isset($where_item['OR'])) $where_item['OR'] = [];
                $where_item['OR'][] = [
                    'ProductsItem.time_start_special IS' => null,
                    'ProductsItem.time_end_special IS' => null
                ];                
                $where_item['OR'][] = [
                    'ProductsItem.time_start_special <=' => $current_time,
                    'ProductsItem.time_end_special >=' => $current_time,
                ];
            }
            
            if(isset($stocking) && $stocking === 1){
                $where['SingleItem.quantity_available >'] = 0;
                $where_item['ProductsItem.quantity_available >'] = 0;
            }

            if(isset($stocking) && $stocking === 0){
                if(!isset($where['OR'])) $where['OR'] = [];

                $where['OR'][] = ['SingleItem.quantity_available IS' => null];
                $where['OR'][] = ['SingleItem.quantity_available =' => 0];

                if(!isset($where_item['OR'])) $where_item['OR'] = [];

                $where_item['OR'][] = ['ProductsItem.quantity_available IS' => null];
                $where_item['OR'][] = ['ProductsItem.quantity_available =' => 0];
            }

            $contain[] = 'SingleItem';
            $contain['ProductsItem'] = function ($q) use ($where_item) {
                return $q->where($where_item);
            };
        }

        if($get_attributes){
            $contain[] = 'ProductsAttribute';
        }       


        if($get_item_attributes){
            $contain[] = 'ProductsItemAttribute';
        }

        if(!empty($get_categories)){
            $contain[] = 'CategoriesProduct';
        }

        if(!empty($get_tags)){
            $contain[] = 'TagsRelation';
        }

        // sort by
        $sort_string = 'Products.id DESC';
        if(!empty($params[SORT])){
            switch($sort_field){
                case 'id':
                case 'product_id':
                    $sort_string = 'Products.id '. $sort_type;
                break;

                case 'name':
                    $sort_string = 'ProductsContent.name '. $sort_type .', Products.position DESC, Products.id DESC';
                break;

                case 'status':
                    $sort_string = 'Products.status '. $sort_type .', Products.position DESC, Products.id DESC';
                break;

                case 'price':
                    $contain[] = 'SingleItem';
                    $sort_string = 'SingleItem.price '. $sort_type .', Products.position DESC, Products.id DESC';
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
                    $sort_string = 'Products.featured DESC, Products.position DESC, Products.id DESC';
                break;

                case 'created_by':
                    $sort_string = 'Products.created_by '. $sort_type .', Products.position DESC, Products.id DESC';
                break; 

                case 'view':
                    $sort_string = 'Products.view '. $sort_type .', Products.position DESC, Products.id DESC';
                break;   

                case 'order_field_id':
                    $sort_string = 'Products.id DESC';
                    if(!empty($ids)){
                        $sort_string = 'FIELD(Products.id, '.implode(',', $ids).')';
                    }
                break;           
            }
        }

        $query = $this->find()->contain($contain)->where($where)->select($fields)->order($sort_string);

        // khi có filter theo danh mục hoặc lấy item để tránh bị lặp lại sản phẩm
        if(!empty($id_categories) || !empty($get_item)){
            $query = $query->group('Products.id');
        }
        return $query;
    }

    public function getDetailProduct($id = null, $lang = null, $params = [])
    {
        $result = [];
        if(empty($id) || empty($lang)) return [];

        $get_user = !empty($params['get_user']) ? true : false;
        $get_categories = !empty($params['get_categories']) ? true : false;
        $get_attributes = !empty($params['get_attributes']) ? true : false;
        $get_item_attributes = !empty($params['get_item_attributes']) ? true : false;
        $get_tags = !empty($params['get_tags']) ? true : false;
        $not_status = isset($params['not_status']) ? intval($params['not_status']) : null;
        $status = isset($params['status']) ? intval($params['status']) : null;
        $status_item = isset($params[STATUS_ITEM]) && $params[STATUS_ITEM] != '' ? intval($params[STATUS_ITEM]) : null;

        $contain = [
            'ProductsItem',
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

        if($get_user){
            $contain[] = 'User';
        }

        if($get_categories){
            $contain[] = 'CategoriesProduct';
        }

        if($get_attributes){
            $contain[] = 'ProductsAttribute';
        }

        if($get_item_attributes){
            $contain[] = 'ProductsItemAttribute';
        }

        if(!empty($get_tags)){
            $contain[] = 'TagsRelation';
        }

        $where = [
            'Products.id' => $id,
            'Products.deleted' => 0
        ];

        if(!is_null($not_status)) {
            $where['Products.status !='] = $not_status;
        }

        if(!is_null($status)) {
            $where['Products.status'] = $status;
        }

        $where_item = [];
        if(!is_null($status_item)){
            $where_item['ProductsItem.status'] = $status_item;            
        }

        if(!empty($where_item)){
            $contain['ProductsItem'] = function ($q) use ($where_item) {
                return $q->where($where_item);
            };
        }

        $result = $this->find()->contain($contain)->where($where)->first();
        return $result;
    }

    public function formatDataProductDetail($data = [], $lang = null)
    {
        if(empty($data) || empty($lang)) return [];

        $result = [
            'id' => !empty($data['id']) ? intval($data['id']) : null,
            'brand_id' => !empty($data['brand_id']) ? intval($data['brand_id']) : null,
            'brand_name' => null,
            'brand_url' => null,
            'url_video' => !empty($data['url_video']) ? $data['url_video'] : null,
            'type_video' => !empty($data['type_video']) ? $data['type_video'] : null,
            'files' => !empty($data['files']) ? json_decode($data['files'], true) : null,
            'width' => !empty($data['width']) ? floatval($data['width']) : null,
            'width_unit' => !empty($data['width_unit']) ? $data['width_unit'] : null,
            'length' => !empty($data['length']) ? floatval($data['length']) : null,
            'length_unit' => !empty($data['length_unit']) ? $data['length_unit'] : null,
            'height' => !empty($data['height']) ? floatval($data['height']) : null,
            'height_unit' => !empty($data['height_unit']) ? $data['height_unit'] : null,
            'weight' => !empty($data['weight']) ? floatval($data['weight']) : null,
            'weight_unit' => !empty($data['weight_unit']) ? $data['weight_unit'] : null,
            'view' => !empty($data['view']) ? intval($data['view']) : null,
            'like' => !empty($data['like']) ? intval($data['like']) : null,
            'main_category_id' => !empty($data['main_category_id']) ? intval($data['main_category_id']) : null,
            'rating' => !empty($data['rating']) ? floatval($data['rating']) : null,
            'rating_number' => !empty($data['rating_number']) ? intval($data['rating_number']) : null,
            'comment' => !empty($data['comment']) ? intval($data['comment']) : null,
            'featured' => !empty($data['featured']) ? 1 : 0,
            'catalogue' => !empty($data['catalogue']) ? 1 : 0,
            'position' => !empty($data['position']) ? intval($data['position']) : null,
            'status' => isset($data['status']) ? intval($data['status']) : null,
            'draft' => !empty($data['draft']) ? 1 : 0,
            'seo_score' => !empty($data['seo_score']) ? $data['seo_score'] : null,
            'keyword_score' => !empty($data['keyword_score']) ? $data['keyword_score'] : null,
            'created' => !empty($data['created']) ? date('H:i - d/m/Y', $data['created']) : null,
            'updated' => !empty($data['updated']) ? date('H:i - d/m/Y', $data['updated']) : null,
            'created_by' => !empty($data['created_by']) ? $data['created_by'] : null,
            'created_by_user' => !empty($data['User']['full_name']) ? $data['User']['full_name'] : null,

            'name' => !empty($data['ProductsContent']['name']) ? $data['ProductsContent']['name'] : null,
            'description' => !empty($data['ProductsContent']['description']) ? $data['ProductsContent']['description'] : null,
            'content' => !empty($data['ProductsContent']['content']) ? $data['ProductsContent']['content'] : null,
            'tags' => [],
            'seo_title' => !empty($data['ProductsContent']['seo_title']) ? $data['ProductsContent']['seo_title'] : null,
            'seo_description' => !empty($data['ProductsContent']['seo_description']) ? $data['ProductsContent']['seo_description'] : null,
            'seo_keyword' => !empty($data['ProductsContent']['seo_keyword']) ? $data['ProductsContent']['seo_keyword'] : null,
            'lang' => !empty($data['ProductsContent']['lang']) ? $data['ProductsContent']['lang'] : null,
            'url_id' => !empty($data['Links']['id']) ? intval($data['Links']['id']) : null,
            'url' => !empty($data['Links']['url']) ? $data['Links']['url'] : null,
        ];

        // format product items        
        $data_items = $this->formatDataProductItems($data, $lang);        

        $items = !empty($data_items['items']) ? $data_items['items'] : [];
        $result['items'] = $items;
        $result['number_item'] = !empty($items) ? count($items) : 1;
        $result['all_images'] = !empty($data_items['all_images']) ? array_unique($data_items['all_images']) : [];
        $result['total_quantity_available'] = !empty($data_items['total_quantity_available']) ? intval($data_items['total_quantity_available']) : 0;

        if(!empty($data['CategoriesProduct'])){
            $categories = [];
            $all_categories = TableRegistry::get('Categories')->getAll(PRODUCT, $lang);
            foreach ($data['CategoriesProduct'] as $k => $category) {
                $category_id = !empty($category['category_id']) ? intval($category['category_id']) : null;
                $category_info = !empty($all_categories[$category_id]) ? $all_categories[$category_id] : [];
                if(empty($category_info)) continue;

                $categories[$category_id] = [
                    'id' => $category_id,
                    'name' => !empty($category_info['name']) ? $category_info['name'] : null,
                    'url' => !empty($category_info['url']) ? $category_info['url'] : null,
                    'status' => !empty($category_info['status']) ? $category_info['status'] : null
                ];
            }

            $result['categories'] = $categories;
        }

        if(!empty($data['brand_id'])){
            $brand_info = TableRegistry::get('Brands')->getDetailBrand($data['brand_id'], $lang, ['status' => 1]);
            $result['brand_name'] = !empty($brand_info['BrandsContent']['name']) ? $brand_info['BrandsContent']['name'] : null;
            $result['brand_url'] = !empty($brand_info['Links']['url']) ? $brand_info['Links']['url'] : null;
        }

        $attributes_table = TableRegistry::get('Attributes');
        $all_attributes = Hash::combine($attributes_table->getAll($lang), '{n}.id', '{n}', '{n}.attribute_type');
        $all_attributes_product = !empty($all_attributes[PRODUCT]) ? $all_attributes[PRODUCT] : [];

        if(!empty($all_attributes_product) && !empty($data['ProductsAttribute'])){
            $attributes = [];
            $attribute_value = Hash::combine($data['ProductsAttribute'], '{n}.attribute_id', '{n}');

            foreach ($all_attributes_product as $attribute_id => $attribute_info) {
                $attribute_code = !empty($attribute_info['code']) ? $attribute_info['code'] : null;
                $attribute_name = !empty($attribute_info['name']) ? $attribute_info['name'] : null;
                $attribute_input_type = !empty($attribute_info['input_type']) ? $attribute_info['input_type'] : null;
                if(empty($attribute_code) || empty($attribute_name)) continue;

                $value = !empty($attribute_value[$attribute_id]['value']) ? $attribute_value[$attribute_id]['value'] : null;
                $value = $attributes_table->formatValueAttribute($attribute_input_type, $value, $lang);
                $attributes[$attribute_code] = [
                    'id' => $attribute_id,
                    'name' => $attribute_name,
                    'input_type' => $attribute_input_type,
                    'value' => $value
                ];
            }

            $result['attributes'] = $attributes;
        }

        if(!empty($data['User'])){
            $result['user_full_name'] = !empty($data['User']['full_name']) ? $data['User']['full_name'] : null;
        }

        if(!empty($data['TagsRelation'])){
            $tags = [];
            $tags_table = TableRegistry::get('Tags');
            foreach ($data['TagsRelation'] as $key => $tag) {
                $tag_id = !empty($tag['tag_id']) ? intval($tag['tag_id']) : null;
                if(empty($tag_id)) continue;
                $tag_info = $tags_table->find()->where(['id' => $tag_id])->select(['id', 'name', 'url'])->first();
                if(empty($tag_info)) continue;

                $tags[] = $tag_info;
            }

            $result['tags'] = $tags;
        }
        
        return $result;
    }

    public function formatDataProductItems($data = [], $lang = null)
    {
        $product_items = !empty($data['ProductsItem']) ? $data['ProductsItem'] : [];
        if(empty($product_items)) return [];

        if(!empty($data['ProductsItemAttribute'])){        
            $all_attributes = Hash::combine(TableRegistry::get('Attributes')->getAll($lang), '{n}.id', '{n}', '{n}.attribute_type');
            $all_attributes_item = !empty($all_attributes[PRODUCT_ITEM]) ? $all_attributes[PRODUCT_ITEM] : [];

            $all_options = TableRegistry::get('AttributesOptions')->getAll($lang);
        }
        
        $current_time = strtotime(date('Y-m-d H:i:s'));
        $items = $all_images = [];
        $total_quantity_available = 0;


        foreach($product_items as $k => $item){
            $item = $item->toArray(); // đổi object -> array()
            unset($item['deleted']);
            $apply_special = $compare_time = false;

            $product_item_id = !empty($item['id']) ? intval($item['id']) : null;
            $quantity_available = !empty($item['quantity_available']) ? intval($item['quantity_available']) : 0;
            $price = !empty($item['price']) ? floatval($item['price']) : null;
            $price_special = !empty($item['price_special']) ? floatval($item['price_special']) : null;

            $time_start_special = !empty($item['time_start_special']) ? intval($item['time_start_special']) : null;
            $time_end_special = !empty($item['time_end_special']) ? intval($item['time_end_special']) : null;

            $item['product_item_id'] = $product_item_id;
            $item['price'] = $price;
            $item['price_special'] = $price_special;
            $item['kiotviet_code'] = !empty($item['kiotviet_code']) ? $item['kiotviet_code'] : null;
            

            // get date special
            $date_special = $time_special = null;
            if(!empty($time_start_special) && !empty($time_end_special)){
                $date_special = date('d/m/Y', $time_start_special) . ' → ' . date('d/m/Y', $time_end_special);
                $time_special = date('H:i - d/m/Y', $time_start_special) . ' → ' . date('H:i - d/m/Y', $time_end_special);
            }

            $item['date_special'] = $date_special;
            $item['time_special'] = $time_special;
            $total_quantity_available += $quantity_available;

            // check apply special
            if(
                (
                    empty($time_start_special) && empty($time_end_special)
                ) || 
                (
                    !empty($time_start_special) && !empty($time_end_special) && 
                    $time_start_special <= $current_time && 
                    $time_end_special >= $current_time
                )
            ){
                $compare_time = true;
            }

            if(!empty($price_special) && $compare_time){
                $apply_special = true;
            }
            
            $item['apply_special'] = $apply_special;
            
            // images items
            $images = !empty($item['images']) ? json_decode($item['images'], true) : [];
            $item['images'] = $images;
            $all_images = array_merge($all_images, $images);            
            
            
            if(!empty($data['ProductsItemAttribute']) && !empty($all_attributes_item)){
                
                $special_code = $special_id = $attributes_of_item = $attribute_name = [];

                foreach($data['ProductsItemAttribute'] as $item_attribute){
                    $attribute_id = !empty($item_attribute['attribute_id']) ? intval($item_attribute['attribute_id']) : null;
                    $value = !empty($item_attribute['value']) ? $item_attribute['value'] : null;
                    $attribute_info = !empty($all_attributes_item[$attribute_id]) ? $all_attributes_item[$attribute_id] : [];
                    $input_type = !empty($attribute_info['input_type']) ? $attribute_info['input_type'] : null;
                
                    if($product_item_id != $item_attribute['product_item_id'] || empty($attribute_info)) continue;

                    if(empty($attributes_item_apply[$attribute_id])){
                        $attributes_item_apply[$attribute_id] = $attribute_info;
                        $attributes_item_apply[$attribute_id]['options'] = [];
                    }

                    $option_info = [];                    
                    if($input_type == SPECICAL_SELECT_ITEM){
                        $option_info = !empty($all_options[$value]) ? $all_options[$value] : [];


                        if(!empty($option_info) && empty($attributes_item_apply[$attribute_id]['options'][$value])){
                            // get image avatar for option of attribute
                            if(!empty($attribute_info['has_image'])){
                                $option_info['image'] = !empty($images[0]) ? $images[0] : null;
                            }
                            $attributes_item_apply[$attribute_id]['options'][$value] = $option_info;
                        }

                        $special_code[] = !empty($attribute_info['code']) ? $attribute_info['code'] : null;
                        $special_code[] = !empty($option_info['code']) ? $option_info['code'] : null;

                        $special_id[] = !empty($attribute_info['code']) ? $attribute_info['code'] : null;
                        $special_id[] = !empty($option_info['id']) ? $option_info['id'] : null;
                        $attribute_name[] = !empty($option_info['name']) ? $option_info['name'] : null;
                    }

                    $attributes_of_item[$attribute_id] = [                        
                        'code' => !empty($attribute_info['code']) ? $attribute_info['code'] : null,
                        'attribute_id' => $attribute_id,
                        'input_type' => $input_type,
                        'value' => !empty($item_attribute['value']) ? $item_attribute['value'] : null,
                    ];
                }

                if(!empty($special_code)){
                    $attributes_item_special[implode('_', $special_code)] = [
                        'product_item_id' => $product_item_id,
                        'code' => !empty($item['code']) ? $item['code'] : null,
                        'price' => $price,
                        'price_special' => $price_special,
                        'apply_special' => $apply_special,
                        'quantity_available' => !empty($item['quantity_available']) ? intval($item['quantity_available']) : null
                    ];                    
                }
                
                $item['special_code'] = !empty($special_code) ? implode('_', $special_code) : null;
                $item['special_id'] = !empty($special_id) ? implode('_', $special_id) : null;
                $item['attribute_name'] = !empty($attribute_name) ? implode(' - ', $attribute_name) : null;
                $item['attributes'] = $attributes_of_item;
            }           
                
            $items[] = $item;
            
        }

        return [
            'items' => $items,
            'all_images' => $all_images,
            'total_quantity_available' => $total_quantity_available
        ];            
    }

    public function checkNameExist($name = null, $product_id = null)
    {
        if(empty($name)) return false;

        $where = [
            'ProductsContent.name' => $name,
            'Products.deleted' => 0,
        ];

        if (!empty($product_id)) {
            $where['Products.id !='] = $product_id; 
        }

        $product = TableRegistry::get('Products')->find()->contain(['ProductsContent'])
        ->where($where)->first();
        return !empty($product) ? true : false;
    }

    public function checkCodeItemExist($code = null, $product_id = null)
    {
        if(empty($code)) return false;

        $where = [
            'ProductsItem.code' => $code,
            'Products.deleted' => 0,
        ];

        if (!empty($product_id)) {
            $where['Products.id !='] = $product_id; 
        }
        
        $product = TableRegistry::get('Products')->find()->contain(['ProductsItem'])
        ->where($where)->first();
        return !empty($product) ? true : false;
    }

    public function getAllNameContent($product_id = null)
    {
        if(empty($product_id)) return false;

        $product = TableRegistry::get('Products')->find()->where([
            'Products.id' => $product_id,
            'Products.deleted' => 0
        ])->contain(['ProductsContent'])->select(['ProductsContent.lang', 'ProductsContent.name'])->toArray();

        $result = Hash::combine($product, '{*}.ProductsContent.lang', '{*}.ProductsContent.name');

        return !empty($result) ? $result : null;
    }

    public function checkNameKiotviet($name = null)
    {
        if(empty($name)) return false;

        $where = [
            'ProductsContent.name' => $name,
            'Products.deleted' => 0,
        ];

        $product = $this->find()->contain(['ProductsContent'])
        ->where($where)->first();
        return $product;
    }
    
}