<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use App\Model\Behavior\UnixTimestampBehavior;
use Cake\Utility\Text;
use Cake\Cache\Cache;
use Cake\Utility\Hash;
use Cake\Collection\Collection;

class AttributesTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('attributes');

        $this->setPrimaryKey('id');

        $this->addBehavior('UnixTimestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                    'updated' => 'existing'
                ]
            ]
        ]);

        $this->belongsTo('User', [
            'className' => 'Publishing.Users',
            'foreignKey' => 'created_by',
            'propertyName' => 'User'
        ]);

        $this->hasOne('AttributesContent', [
            'className' => 'Publishing.AttributesContent',
            'foreignKey' => 'attribute_id',
            'propertyName' => 'AttributesContent'
        ]);

        $this->hasMany('ContentMutiple', [
            'className' => 'AttributesContent',
            'foreignKey' => 'attribute_id',
            'joinType' => 'LEFT',
            'propertyName' => 'ContentMutiple'
        ]);

        $this->hasMany('AttributesOptions', [
            'className' => 'AttributesOptions',
            'foreignKey' => 'attribute_id',
            'joinType' => 'LEFT',
            'propertyName' => 'AttributesOptions'
        ]);

        

    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');
        $validator
            ->scalar('code')
            ->maxLength('code', 20)
            ->requirePresence('code')
            ->notEmptyString('code');

        return $validator;
    }

    public function queryListAttributes($params = []) 
    {
        $table = TableRegistry::get('Attributes');

        // sort
        $sort = !empty($params[SORT]) ? $params[SORT] : [];
        $sort_field = !empty($sort[FIELD]) ? $sort[FIELD] : null;
        $sort_type = !empty($sort[SORT]) && in_array($sort[SORT], [DESC, ASC]) ? $sort[SORT] : DESC;
        
        // filter
        $filter = !empty($params[FILTER]) ? $params[FILTER] : [];
        $lang = !empty($filter[LANG]) ? $filter[LANG] : TableRegistry::get('Languages')->getDefaultLanguage();        
        $keyword = !empty($filter['keyword']) ? trim($filter['keyword']) : null;
        $status = isset($filter[STATUS]) && $filter[STATUS] != '' ? intval($filter[STATUS]) : null;
        $attribute_type = !empty($filter['attribute_type']) ? $filter['attribute_type'] : null;
        $input_type = !empty($filter['input_type']) ? $filter['input_type'] : null;
        $has_image = isset($filter['has_image']) && $filter['has_image'] != '' ? intval($filter['has_image']) : null;
        $required = isset($filter['required']) && $filter['required'] != '' ? intval($filter['required']) : null;
        $attribute_ids = [];
        if(!empty($filter['attribute_ids']) && is_array($filter['attribute_ids'])){
            $attribute_ids = $filter['attribute_ids'];
        }

        $fields = ['Attributes.id', 'Attributes.attribute_type','Attributes.code', 'Attributes.input_type', 'Attributes.has_image', 'Attributes.required', 'Attributes.position', 'AttributesContent.name', 'AttributesContent.lang'];

        //contain
        $contain = [
            'AttributesContent' => function ($q) use ($lang) {
                return $q->where([
                    'AttributesContent.lang' => $lang
                ]);
            }
        ];

        $sort_string = 'Attributes.id DESC';
        if(!empty($params[SORT])){
            switch($sort_field){
                case 'id':
                    $sort_string = 'Attributes.id '. $sort_type;
                break;

                case 'name':
                    $sort_string = 'AttributesContent.name '. $sort_type .', Attributes.position DESC, Attributes.id DESC';
                break;

                case 'code':
                    $sort_string = 'Attributes.code '. $sort_type .', Attributes.position DESC, Attributes.id DESC';
                break;

                case 'attribute_type':
                    $sort_string = 'Attributes.attribute_type '. $sort_type .', Attributes.position DESC, Attributes.id DESC';
                break;

                case 'input_type':
                    $sort_string = 'Attributes.input_type '. $sort_type .', Attributes.position DESC, Attributes.id DESC';
                break;

                case 'position':
                    $sort_string = 'Attributes.position '. $sort_type .', Attributes.id DESC';
                break;

                case 'has_image':
                    $sort_string = 'Attributes.has_image '. $sort_type .', Attributes.id DESC';
                break;
                
            }
        }

        // filter by conditions
        $where = ['Attributes.deleted' => 0];

        if(!is_null($status)){
            $where['Attributes.status'] = $status;
        }

        if(!empty($keyword)){
            $where['OR'] = [
                'AttributesContent.search_unicode LIKE' => '%' . Text::slug(strtolower($keyword), ' ') . '%',
                'Attributes.code LIKE' => '%' . Text::slug(strtolower($keyword), ' ') . '%'
            ];
        }

        if(!empty($attribute_type)){
            $where['Attributes.attribute_type'] = $attribute_type;
        }

        if(!empty($input_type)){
            $where['Attributes.input_type'] = $input_type;
        }        

        if(!empty($attribute_ids)){
            $where['Attributes.id IN'] = $attribute_ids;
        }

        if(!is_null($has_image)){
            $where['Attributes.has_image'] = $has_image;
        }

        if(!is_null($required)){
            $where['Attributes.required'] = $required;
        }

        return $table->find()->contain($contain)->where($where)->select($fields)->group('Attributes.id')->order($sort_string);
    }

    public function getDetailAttribute($id = null, $lang = null)
    {
        $result = [];
        if(empty($id) || empty($lang)) return [];        

        $table = TableRegistry::get('Attributes');

        $contain = [
            'AttributesContent' => function ($q) use ($lang) {
                return $q->where([
                    'AttributesContent.lang' => $lang
                ]);
            }
        ];

        $result = $table->find()->contain($contain)
        ->where([
            'Attributes.id' => $id,
            'Attributes.deleted' => 0,
        ])->first();

        return $result;
    }

    public function getAll($lang = null)
    {
        if(empty($lang)) return [];

        $cache_key = ATTRIBUTE . '_all_' . $lang;
        $result = Cache::read($cache_key);
        if(is_null($result)){
            $table = TableRegistry::get('Attributes');

            $fields = ['Attributes.id', 'Attributes.attribute_type','Attributes.code', 'Attributes.input_type', 'Attributes.has_image', 'Attributes.required', 'AttributesContent.name', 'Attributes.position'];

            $contain = [
                'AttributesContent' => function ($q) use ($lang) {
                    return $q->where([
                        'AttributesContent.lang' => $lang
                    ]);
                }
            ];
            $attributes = $table->find()->contain($contain)->where(['Attributes.deleted' => 0, 'Attributes.status' => 1])->select($fields)->order('Attributes.position DESC, Attributes.id DESC')->toArray();
            $result = [];
            if(!empty($attributes)){
                foreach ($attributes as $key => $attribute) {
                    $attribute_id = !empty($attribute['id']) ? intval($attribute['id']) : null;
                    if(empty($attribute_id)) continue;

                    $result[$attribute_id] = [
                        'id' => $attribute_id,
                        'attribute_type' => !empty($attribute['attribute_type']) ? $attribute['attribute_type'] : null,
                        'code' => !empty($attribute['code']) ? $attribute['code'] : null,
                        'name' => !empty($attribute['AttributesContent']['name']) ? $attribute['AttributesContent']['name'] : null,
                        'input_type' => !empty($attribute['input_type']) ? $attribute['input_type'] : null,
                        'has_image' => !empty($attribute['has_image']) ? $attribute['has_image'] : null,
                        'required' => !empty($attribute['required']) ? true : false,
                        'position' => !empty($attribute['position']) ? intval($attribute['position']) : null,
                    ];
                }
            }            
            Cache::write($cache_key, $result);
        }
        
        return $result;
    }

    public function formatValueAttribute($input_type = null, $value = null, $lang = null)
    {
        $utilities_table = TableRegistry::get('Utilities');
        switch ($input_type) {
            case TEXT:
            case RICH_TEXT:
                if($utilities_table->isJson($value) && !empty($lang)){
                    $decode_value = json_decode($value, true);
                    $value = !empty($decode_value[$lang]) ? $decode_value[$lang] : null;
                }else{
                    $value = null;
                }
                break;

            case NUMERIC:
                $value = floatval($value);
                break;
            
            case DATE:
                $value = $utilities_table->convertIntgerToDateString($value);
                break;

            case DATE_TIME:
                $value = $utilities_table->convertIntgerToDateTimeString($value, 'd/m/Y - H:i');
                break;

            case SWITCH_INPUT:
                $value = !empty($value) ? 1 : 0;
                break;

            case SINGLE_SELECT:
                $value = !empty($value) ? intval($value) : null;
                break;

            case MULTIPLE_SELECT:
                $value = !empty($value) ? json_decode($value, true) : [];
                break;
        }

        return $value;
    }

    public function formatValueAttributeItem($input_type = null, $value = null, $lang = null)
    {
        $utilities_table = TableRegistry::get('Utilities');
        switch ($input_type) {
            case TEXT:
                $value = trim($value);
                break;

            case NUMERIC:
                $value = floatval($value);
                break;

            case SINGLE_SELECT:
                $value = !empty($value) ? intval($value) : null;
                break;

            case MULTIPLE_SELECT:
                $value = !empty($value) ? json_decode($value, true) : [];
                break;
            
            case DATE:
                $value = $utilities_table->convertIntgerToDateString($value);
                break;

            case DATE_TIME:
                $value = $utilities_table->convertIntgerToDateTimeString($value, 'd/m/Y - H:i');
                break;

            case SWITCH_INPUT:
                $value = !empty($value) ? 1 : 0;
                break;
        }

        return $value;
    }

    public function checkExistName($name = null, $lang = null, $id = null) 
    {
        if(empty($name) || empty($lang)) return false;

        $where = [
            'Attributes.deleted' => 0,
            'AttributesContent.name' => $name,
            'AttributesContent.lang' => $lang,
        ];

        if(!empty($id)){
            $where['Attributes.id !='] = $id;
        }

        $result = TableRegistry::get('Attributes')->find()->contain(['AttributesContent'])->where($where)->first();
        return !empty($result->id) ? true :false;
    }

    public function getAttributeByMainCategory($category_id = null, $type = null, $lang = null)
    {
        if(empty($type) || !in_array($type, [PRODUCT, ARTICLE, PRODUCT_ITEM])) return [];
        if(empty($lang)) $lang = TableRegistry::get('Languages')->getDefaultLanguage();

        $all_attributes = Hash::combine(TableRegistry::get('Attributes')->getAll($lang), '{n}.id', '{n}', '{n}.attribute_type');
        $attributes = !empty($all_attributes[$type]) ? $all_attributes[$type] : [];
        
        // nếu thuộc tính phiên bản sản phẩm thì chỉ lấy những thuộc tính thường, không lấy thuộc tính đặc biệt
        if(!empty($attributes) && $type == PRODUCT_ITEM){
            $attributes = Collection($attributes)->filter(function ($item, $key, $iterator) {
                return $item['input_type'] != SPECICAL_SELECT_ITEM;
            })->toArray();
        }

        if(empty($attributes)) return [];

        $settings = TableRegistry::get('Settings')->getSettingWebsite();

        $setting_category = [];
        switch($type){
            case PRODUCT:
                $setting_category = !empty($settings['attributes_category']) ? $settings['attributes_category'] : [];
            break;

            case ARTICLE:
                $setting_category = !empty($settings['article_attributes_category']) ? $settings['article_attributes_category'] : [];
            break;

            case PRODUCT_ITEM:
                $setting_category = !empty($settings['item_attributes_category']) ? $settings['item_attributes_category'] : [];
            break;
        }
        
        $status = !empty($setting_category['status']) ? true : false;

        if(empty($status)) return $attributes;
        if(empty($category_id)) return [];
        
        $apply_attributes = !empty($setting_category['apply_attributes']) ? json_decode($setting_category['apply_attributes'], true) : [];
        $ids = !empty($apply_attributes[$category_id]) ? array_filter(explode(',', $apply_attributes[$category_id])) : [];

        // nếu không có cấu hình thì kiểm tra danh mục có phải danh mục con ko
        if(empty($ids)){
            $category_info = TableRegistry::get('Categories')->find()->where([
                'id' => $category_id
            ])->select([
                'id', 
                'parent_id', 
                'path_id'
            ])->first();
            
            $path_id = !empty($category_info['path_id']) ? $category_info['path_id'] : null;
            if(empty($category_info['parent_id']) || empty($path_id)) return [];

            $split_path = @array_values(array_filter(explode('|', $path_id)));

            $parent_id = !empty($split_path[0]) ? intval($split_path[0]) : null;
            if(empty($parent_id)) return [];
            
            $ids = !empty($apply_attributes[$parent_id]) ? array_filter(explode(',', $apply_attributes[$parent_id])) : [];
            if(empty($ids)) return [];
        }
        $result = [];
        foreach($attributes as $attribute_id => $attribute){
            if(in_array($attribute_id, $ids) && empty($result[$attribute_id])){
                $result[$attribute_id] = $attribute;
            }
        }
        
        return $result;
    }

    public function getSpecialAttributeItemByMainCategory($category_id = null, $lang = null)
    {
        if(empty($lang)) $lang = TableRegistry::get('Languages')->getDefaultLanguage();

        $all_attributes = Hash::combine(TableRegistry::get('Attributes')->getAll($lang), '{n}.id', '{n}', '{n}.attribute_type');
        $attributes = !empty($all_attributes[PRODUCT_ITEM]) ? $all_attributes[PRODUCT_ITEM] : [];

        // chỉ lấy những thuộc tính đặc biệt của thuộc tính phiên bản sản phẩm
        if(!empty($attributes)){
            $attributes = Collection($attributes)->filter(function ($item, $key, $iterator) {
                return $item['input_type'] == SPECICAL_SELECT_ITEM;
            })->toArray();
        }

        if(empty($attributes)) return [];

        $settings = TableRegistry::get('Settings')->getSettingWebsite();
        $setting_category = !empty($settings['item_attributes_category']) ? $settings['item_attributes_category'] : [];
        $status = !empty($setting_category['status']) ? true : false;

        if(empty($status)) return Hash::combine($attributes,'{n}.id', '{n}.name');
        if(empty($category_id)) return [];
        
        $apply_attributes = !empty($setting_category['apply_attributes']) ? json_decode($setting_category['apply_attributes'], true) : [];
        $ids = !empty($apply_attributes[$category_id]) ? array_filter(explode(',', $apply_attributes[$category_id])) : [];

        // nếu không có cấu hình thì kiểm tra danh mục có phải danh mục con ko
        if(empty($ids)){
            $category_info = TableRegistry::get('Categories')->find()->where([
                'id' => $category_id
            ])->select([
                'id', 
                'parent_id', 
                'path_id'
            ])->first();
            
            $path_id = !empty($category_info['path_id']) ? $category_info['path_id'] : null;
            if(empty($category_info['parent_id']) || empty($path_id)) return [];

            $split_path = @array_values(array_filter(explode('|', $path_id)));

            $parent_id = !empty($split_path[0]) ? intval($split_path[0]) : null;
            if(empty($parent_id)) return [];
            
            $ids = !empty($apply_attributes[$parent_id]) ? array_filter(explode(',', $apply_attributes[$parent_id])) : [];
            if(empty($ids)) return [];
        }
        
        $result = [];
        foreach($attributes as $attribute_id => $attribute){
            if(in_array($attribute_id, $ids) && empty($result[$attribute_id])){
                $result[] = $attribute;
            }
        }

        if(!empty($result)) $result = Hash::combine($result,'{n}.id', '{n}.name');

        return $result;
    }

    public function getAttributeProductSpecialItemByName($name = null, $lang = null) 
    {
        if(empty($name) || empty($lang)) return [];

        $result = $this->find()->contain(['AttributesContent'])->where([
            'Attributes.deleted' => 0,
            'Attributes.attribute_type' => PRODUCT_ITEM,
            'Attributes.input_type' => SPECICAL_SELECT_ITEM,
            'AttributesContent.name' => $name,
            'AttributesContent.lang' => $lang,
        ])->first();

        return $result;
    }
}