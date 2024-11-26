<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Utility\Text;
use Cake\Utility\Hash;
use Cake\Cache\Cache;

class PromotionsTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('promotions');

        $this->setPrimaryKey('id');

        $this->addBehavior('UnixTimestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                    'updated' => 'existing'
                ]
            ]
        ]);

        $this->hasOne('Links', [
            'className' => 'Links',
            'foreignKey' => 'foreign_id',
            'propertyName' => 'Links'
        ]);

        $this->belongsTo('User', [
            'className' => 'Users',
            'foreignKey' => 'created_by',
            'propertyName' => 'User'
        ]);

        $this->hasMany('LinksMutiple', [
            'className' => 'Links',
            'foreignKey' => 'foreign_id',
            'joinType' => 'LEFT',
            'conditions' => [
                'LinksMutiple.type' => PROMOTION_DETAIL,
                'LinksMutiple.deleted' => 0
            ],
            'propertyName' => 'LinksMutiple'
        ]);

        $this->hasMany('TagsRelation', [
            'className' => 'TagsRelation',
            'foreignKey' => 'foreign_id',
            'conditions' => [
                'TagsRelation.type' => PROMOTION_DETAIL
            ],
            'joinType' => 'LEFT',
            'propertyName' => 'TagsRelation'
        ]);

        $this->hasOne('TagPromotion', [
            'className' => 'TagsRelation',
            'foreignKey' => 'foreign_id',
            'conditions' => [
                'TagPromotion.type' => PROMOTION_DETAIL
            ],
            'joinType' => 'LEFT',
            'propertyName' => 'TagPromotion'
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        return $validator;
    }

    public function queryListPromotions($params = []) 
    {
        $table = TableRegistry::get('Promotions');

        // get info params
        $field = !empty($params[FIELD]) ? $params[FIELD] : SIMPLE_INFO;
        $get_user = !empty($params['get_user']) ? $params['get_user'] : false;
        $get_empty_name = !empty($params['get_empty_name']) ? true : false;
        $check_expiry_date = !empty($params['check_expiry_date']) ? true : false;

        // sort
        $sort = !empty($params[SORT]) ? $params[SORT] : [];
        $sort_field = !empty($sort[FIELD]) ? $sort[FIELD] : null;
        $sort_type = !empty($sort[SORT]) ? $sort[SORT] : DESC;

        // filter
        $filter = !empty($params[FILTER]) ? $params[FILTER] : [];
        $lang = !empty($filter[LANG]) ? $filter[LANG] : TableRegistry::get('Languages')->getDefaultLanguage();
        $keyword = !empty($filter['keyword']) ? trim($filter['keyword']) : null;
        $status = isset($filter['status']) && $filter['status'] != '' ? intval($filter['status']) : null;
        $public = isset($filter['public']) && $filter['public'] != '' ? intval($filter['public']) : null;
        $type_discount = !empty($filter['type_discount']) ? $filter['type_discount'] : null;
        $ids = !empty($filter['ids']) ? $filter['ids'] : [];
        $tag_id = !empty($filter['tag_id']) ? intval($filter['tag_id']) : null;
        $create_from = !empty($filter['create_from']) ? strtotime(date('Y-m-d 00:00:00', strtotime(str_replace('/', '-', $filter['create_from'])))) : null;
        $create_to = !empty($filter['create_to']) ? strtotime(date('Y-m-d 23:59:59', strtotime(str_replace('/', '-', $filter['create_to'])))) : null;

        // fields select
        switch($field){
            case FULL_INFO:
                $fields = ['Promotions.id', 'Promotions.code', 'Promotions.name', 'Promotions.public', 'Promotions.type_discount', 'Promotions.value', 'Promotions.condition_product', 'Promotions.condition_order', 'Promotions.condition_location', 'Promotions.start_time', 'Promotions.end_time', 'Promotions.number_coupon', 'Promotions.used', 'Promotions.position', 'Promotions.status', 'Promotions.created', 'Promotions.updated', 'Promotions.created_by'];
            break;

            case LIST_INFO:
                $fields = ['Promotions.id', 'Promotions.name'];
            break;

            case SIMPLE_INFO:
            default:
                $fields = ['Promotions.id', 'Promotions.code', 'Promotions.name', 'Promotions.public', 'Promotions.type_discount', 'Promotions.value', 'Promotions.start_time', 'Promotions.end_time', 'Promotions.number_coupon', 'Promotions.used', 'Promotions.position', 'Promotions.status', 'Promotions.created', 'Promotions.updated', 'Promotions.created_by'];
            break;
        }

        $where = ['Promotions.deleted' => 0];        
        $contain = [];

        // filter by conditions  
        if(!empty($keyword)){
            $where['Promotions.search_unicode LIKE'] = '%' . Text::slug(strtolower($keyword), ' ') . '%';
        }

        if(!empty($ids)){
            $where['Promotions.id IN'] = $ids;
        }

        if(!is_null($status)){
            $where['Promotions.status'] = $status;
        }   

        if(!is_null($public)){
            $where['Promotions.public'] = $public;
        }  

        if(!is_null($type_discount)){
            $where['Promotions.type_discount'] = $type_discount;
        }  

        if(!empty($create_from)){
            $where['Promotions.created >='] = $create_from;
        }

        if(!empty($create_to)){
            $where['Promotions.created <='] = $create_to;
        }   

        if(!empty($get_user)){
            $fields[] = 'User.id';
            $fields[] = 'User.full_name';

            $contain[] = 'User';
        }

        $current_time = strtotime(date('Y-m-d H:i:s'));
        if(!empty($check_expiry_date)){
            $where['AND'] = [
                'OR' => [
                    'Promotions.start_time <=' => $current_time,
                    'Promotions.start_time IS' => null
                ]
            ];

            $where['AND'][] = [
                'OR' => [
                    'Promotions.end_time >=' => $current_time,
                    'Promotions.end_time IS' => null
                ]
            ];
        }

        // sort by
        $sort_string = 'Promotions.id DESC';
        if(!empty($params[SORT])){
            switch($sort_field){
                case 'id':
                case 'promotion_id':
                    $sort_string = 'Promotions.id '. $sort_type;
                break;

                case 'name':
                    $sort_string = 'Promotions.name '. $sort_type .', Promotions.position DESC, Promotions.id DESC';
                break;

                case 'status':
                    $sort_string = 'Promotions.status '. $sort_type .', Promotions.position DESC, Promotions.id DESC';
                break;

                case 'position':
                    $sort_string = 'Promotions.position '. $sort_type .', Promotions.id DESC';
                break;

                case 'created':
                    $sort_string = 'Promotions.created '. $sort_type .', Promotions.position DESC, Promotions.id DESC';
                break;

                case 'updated':
                    $sort_string = 'Promotions.updated '. $sort_type .', Promotions.position DESC, Promotions.id DESC';
                break;

                case 'created_by':
                    $sort_string = 'Promotions.created_by '. $sort_type .', Promotions.position DESC, Promotions.id DESC';
                break;             
            }
        }

        return $table->find()->contain($contain)->where($where)->select($fields)->group('Promotions.id')->order($sort_string);
    }

    public function getDetailPromotion($id = null, $params = [])
    {
        $result = [];
        if(empty($id)) return [];        

        $get_user = !empty($params['get_user']) ? true : false;
        $status = isset($params['status']) ? intval($params['status']) : null;

        $where = [
            'Promotions.id' => $id,
            'Promotions.deleted' => 0,
        ];

        if(!is_null($status)) {
            $where['Promotions.status'] = $status;
        }

        $result = TableRegistry::get('Promotions')->find()->where($where)->first();
        return $result;
    }

    public function formatDataPromotionDetail($data = [], $lang = null)
    {
        if(empty($data) || empty($lang)) return [];

        $type_discount = !empty($data['type_discount']) ? $data['type_discount'] : null;
        $value = !empty($data['value']) ? json_decode($data['value'], true): null; 

        $value_description = null;
        if($type_discount == FREE_SHIP){
            $value_description = __d('template', 'freeship_label');
        }

        if(!empty($value['value_discount']) && $value['type_value_discount'] == PERCENT){
            $value_description = intval(str_replace(',', '', $value['value_discount'])) . '%';
        }

        if(!empty($value['value_discount']) && $value['type_value_discount'] == MONEY){
            $value_description = round(intval(str_replace(',', '', $value['value_discount'])));
        }

        $max_value = !empty($value['max_value']) ? intval(str_replace(',', '', $value['max_value'])) : null;

        $condition_product = !empty($data['condition_product']) ? json_decode($data['condition_product'], true): null;
        $condition_order = !empty($data['condition_order']) ? json_decode($data['condition_order'], true): null;
        $condition_location = !empty($data['condition_location']) ? json_decode($data['condition_location'], true): null;

        $condition_description = [];
        if(!empty($condition_product['type']) && !empty($condition_product['ids'])){
            switch($condition_product['type']){
                case PRODUCT:
                    $products = TableRegistry::get('Products')->queryListProducts([
                        FIELD => LIST_INFO,
                        FILTER => [
                            'ids' => $condition_product['ids']
                        ]
                    ])->toArray();
                    $products = Hash::extract($products, '{n}.ProductsContent.name');
                    if(!empty($products)){
                        $condition_description[] = __d('template', 'ap_dung_voi_san_pham_{0}', [implode(', ', $products)]);
                    }
                break;

                case CATEGORY_PRODUCT:
                    $categories = TableRegistry::get('Categories')->queryListCategories([
                        FIELD => LIST_INFO,
                        FILTER => [
                            'type' => PRODUCT,
                            'ids' => $condition_product['ids']
                        ]
                    ])->toArray();
                    $categories = Hash::extract($categories, '{n}.CategoriesContent.name');
                    
                    if(!empty($categories)){
                        $condition_description[] = __d('template', 'ap_dung_voi_danh_muc_{0}', [implode(', ', $categories)]);
                    }
                break;

                case BRAND:
                    $brands = TableRegistry::get('Brands')->queryListBrands([
                        FIELD => LIST_INFO,
                        FILTER => [
                            'ids' => $condition_product['ids']
                        ]
                    ])->toArray();
                    $brands = Hash::extract($brands, '{n}.name');

                    if(!empty($brands)){
                        $condition_description[] = __d('template', 'ap_dung_voi_thuong_hieu_{0}', [implode(', ', $brands)]);
                    }
                break;
            }
        }

        $order_min_value = !empty($condition_order['min_value']) ? intval(str_replace(',', '', $condition_order['min_value'])) : 0;
        $order_max_value = !empty($condition_order['max_value']) ? intval(str_replace(',', '', $condition_order['max_value'])) : 0;
        $order_number_product = !empty($condition_order['number_product']) ? intval(str_replace(',', '', $condition_order['number_product'])) : 0;
        if(!empty($order_min_value)){
            $condition_description[] = __d('template', 'ap_dung_voi_don_hang_tu_{0}', [number_format($order_min_value)]);
        }

        if(!empty($order_max_value)){
            $condition_description[] = __d('template', 'ap_dung_voi_don_hang_duoi_{0}', [number_format($order_max_value)]);
        }

        if($order_number_product > 1){
            $condition_description[] = __d('template', 'ap_dung_khi_mua_tren_{0}_san_pham', [number_format($order_number_product)]);
        }

        if(!empty($condition_location['ids'])){
            $cities = TableRegistry::get('Cities')->queryListCities([
                FIELD => LIST_INFO,
                FILTER => [
                    'ids' => $condition_location['ids']
                ]
            ])->toArray();
            $cities = Hash::extract($cities, '{n}.name');
            if(!empty($cities)){
                $condition_description[] = __d('template', 'ap_dung_voi_don_tai_{0}', [implode(', ', $cities)]);
            }
        }


        if(empty($condition_description)){
            $condition_description[] = __d('template', 'ap_dung_voi_tat_ca_san_pham');
        }

        $result = [
            'id' => !empty($data['id']) ? intval($data['id']) : null,
            'name' => !empty($data['name']) ? $data['name'] : null,
            'code' => !empty($data['code']) ? $data['code'] : null,
            'public' => !empty($data['public']) ? 1 : 0,
            'type_discount' => $type_discount,

            'value' => $value,
            'value_description' => $value_description,
            'max_value' => $max_value,

            'condition_product' => !empty($data['condition_product']) ? json_decode($data['condition_product'], true): null,
            'condition_order' => !empty($data['condition_order']) ? json_decode($data['condition_order'], true): null,
            'condition_location' => !empty($data['condition_location']) ? json_decode($data['condition_location'], true): null,            
        
            'condition_description' => $condition_description,

            'start_time' => !empty($data['start_time']) ? intval($data['start_time']) : null,
            'end_time' => !empty($data['end_time']) ? intval($data['end_time']) : null,            
          
            'number_coupon' => !empty($data['number_coupon']) ? intval($data['number_coupon']) : null,
            'used' => !empty($data['used']) ? intval($data['used']) : null,
       
            'created_by' => !empty($data['created_by']) ? intval($data['created_by']) : null,
            'created_by_user' => !empty($data['User']['full_name']) ? $data['User']['full_name'] : null,
            'created' => !empty($data['created']) ? date('H:i - d/m/Y', $data['created']) : null,
            'updated' => !empty($data['updated']) ? date('H:i - d/m/Y', $data['updated']) : null,
            'position' => !empty($data['position']) ? intval($data['position']) : null,
            'status' => isset($data['status']) ? intval($data['status']) : null
        ];

        return $result;
    }

    public function checkNameExist($name = null)
    {
        if(empty($name)) return false;
        $promotion = TableRegistry::get('Promotions')->find()
        ->where([
            'Promotions.name' => $name,
            'Promotions.deleted' => 0,
        ])->first();
        return !empty($promotion) ? true : false;
    }

    public function getListPromotionActive()
    {
        $cache_key = PROMOTION . '_active';
        $result = Cache::read($cache_key);
        if(is_null($result)){
            $promotions = TableRegistry::get('Promotions')->find()->where([
                'Promotions.deleted' => 0,
                'Promotions.status' => 1
            ])->select(['Promotions.id', 'Promotions.code', 'Promotions.name', 'Promotions.type_discount', 'Promotions.value', 'Promotions.condition_product', 'Promotions.condition_order', 'Promotions.condition_location', 'Promotions.start_time', 'Promotions.end_time'])->order('Promotions.position DESC, Promotions.id DESC')->toArray();            
            $result = Hash::combine($promotions, '{n}.id', '{n}');

            Cache::write($cache_key, !empty($result) ? $result : []);
        }

        return $result;
    }
}