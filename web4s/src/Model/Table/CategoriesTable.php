<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Core\Configure;
use Cake\Utility\Text;
use Cake\Cache\Cache;
use Cake\Utility\Hash;

class CategoriesTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('categories');
        $this->setPrimaryKey('id');

        $this->addBehavior('UnixTimestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                    'updated' => 'always'
                ]
            ]
        ]);

        $this->hasOne('CategoriesContent', [
            'className' => 'CategoriesContent',
            'foreignKey' => 'category_id',
            'joinType' => 'LEFT',
            'propertyName' => 'CategoriesContent'
        ]);

        $this->hasOne('Links', [
            'className' => 'Links',
            'foreignKey' => 'foreign_id',
            'joinType' => 'LEFT',
            'propertyName' => 'Links'
        ]);

        $this->belongsTo('User', [
            'className' => 'Publishing.Users',
            'foreignKey' => 'created_by',
            'propertyName' => 'User'
        ]);

        $this->hasMany('ContentMutiple', [
            'className' => 'CategoriesContent',
            'foreignKey' => 'category_id',
            'joinType' => 'LEFT',
            'propertyName' => 'ContentMutiple'
        ]);

        $this->hasMany('LinksMutiple', [
            'className' => 'Links',
            'foreignKey' => 'foreign_id',
            'joinType' => 'LEFT',
            'propertyName' => 'LinksMutiple'
        ]);

        $this->hasMany('CategoriesAttribute', [
            'className' => 'CategoriesAttribute',
            'foreignKey' => 'category_id',
            'joinType' => 'LEFT',
            'propertyName' => 'CategoriesAttribute'
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('type')
            ->maxLength('type', 20)
            ->notEmptyString('type');

        return $validator;
    }

    public function queryListCategories($params = []) 
    {
        $table = TableRegistry::get('Categories');

        // get info params
        $field = !empty($params[FIELD]) ? $params[FIELD] : SIMPLE_INFO;
        $get_user = !empty($params['get_user']) ? $params['get_user'] : false;
        $get_empty_name = !empty($params['get_empty_name']) ? true : false;
        $get_parent = !empty($params['get_parent']) ? true : false;
        $get_attributes = !empty($params['get_attributes']) ? true : false;

        // sort
        $sort = !empty($params[SORT]) ? $params[SORT] : [];
        $sort_field = !empty($sort[FIELD]) ? $sort[FIELD] : null;
        $sort_type = !empty($sort[SORT]) && in_array($sort[SORT], [DESC, ASC]) ? $sort[SORT] : DESC;

        // filter
        $filter = !empty($params[FILTER]) ? $params[FILTER] : [];
        $lang = !empty($filter[LANG]) ? $filter[LANG] : TableRegistry::get('Languages')->getDefaultLanguage();
        $type = !empty($filter[TYPE]) ? $filter[TYPE] : null;
        $not_id = !empty($filter[NOT_ID]) ? $filter[NOT_ID] : null;
        $keyword = !empty($filter['keyword']) ? trim($filter['keyword']) : null;
        $status = isset($filter['status']) && $filter['status'] != '' ? intval($filter['status']) : null;
        $ids = !empty($filter['ids']) ? $filter['ids'] : [];
        $parent_id = !empty($filter['parent_id']) ? intval($filter['parent_id']) : [];
        $created_by = !empty($filter['created_by']) ? intval($filter['created_by']) : null;

        // fields select
        switch($field){
            case FULL_INFO:
                $fields = ['Categories.id', 'Categories.type', 'Categories.parent_id', 'Categories.path_id', 'Categories.image_avatar', 'Categories.images', 'Categories.url_video', 'Categories.type_video', 'Categories.status', 'Categories.created_by', 'Categories.created', 'Categories.position', 'CategoriesContent.name', 'CategoriesContent.description', 'CategoriesContent.content', 'CategoriesContent.lang', 'Links.id', 'Links.url'];
            break;

            case LIST_INFO:
                $fields = ['Categories.id', 'Categories.parent_id', 'Categories.path_id', 'CategoriesContent.name',];
            break;

            case SIMPLE_INFO:
            default:
                $fields = ['Categories.id', 'Categories.type', 'Categories.parent_id', 'Categories.image_avatar', 'Categories.images', 'Categories.url_video', 'Categories.type_video', 'Categories.status', 'Categories.type_video', 'Categories.created_by', 'Categories.created', 'Categories.position', 'CategoriesContent.name', 'CategoriesContent.description', 'CategoriesContent.lang', 'Links.id', 'Links.url'];
            break;
        }
        
        $where = ['Categories.deleted' => 0];  

        //contain
        if(!$get_empty_name){
            $contain = ['CategoriesContent', 'Links'];

            $where['CategoriesContent.lang'] = $lang;
            $where['Links.lang'] = $lang;
            $where['Links.type'] = 'category_' . $type;
            $where['Links.deleted'] = 0;
        }else{
            $contain = [
                'CategoriesContent' => function ($q) use ($lang) {
                    return $q->where([
                        'CategoriesContent.lang' => $lang
                    ]);
                }, 
                'Links' => function ($q) use ($type, $lang) {
                    return $q->where([
                        'Links.type' => 'category_' . $type,
                        'Links.lang' => $lang,
                        'Links.deleted' => 0
                    ]);
                } 
            ];
        }
        

        if(!empty($get_user)){
            $fields[] = 'User.id';
            $fields[] = 'User.full_name';

            $contain[] = 'User';
        }

        if($get_attributes){
            $contain[] = 'CategoriesAttribute';
        }

        if(!empty($get_parent)){
            $where['Categories.parent_id IS'] = null;
        }

        if(!empty($parent_id)){
            $where['Categories.parent_id'] = $parent_id;
        }

        // filter by conditions 
        if(!empty($type)){
            $where['Categories.type'] = $type;
        }

        if(!empty($ids)){
            $where['Categories.id IN'] = $ids;
        }
        
        if(!empty($keyword)){
            $where['CategoriesContent.search_unicode LIKE'] = '%' . Text::slug(strtolower($keyword), ' ') . '%';
        }

        if(!is_null($status)){
            $where['Categories.status'] = $status;
        }

        if(!empty($not_id)){
            $where['Categories.id <>'] = $not_id;
        }

        if(!empty($created_by)){
            $where['Categories.created_by'] = $created_by;
        }
        
        // sort by
        $sort_string = 'Categories.position DESC, Categories.id ASC';
        if(!empty($sort_field)){
            switch($sort_field){
                case 'id':
                case 'category_id':
                    $sort_string = 'Categories.id '. $sort_type;
                break;

                case 'name':
                    $sort_string = 'CategoriesContent.name '. $sort_type .', Categories.position DESC, Categories.id DESC';
                break;

                case 'status':
                    $sort_string = 'Categories.status '. $sort_type .', Categories.position DESC, Categories.id DESC';
                break;

                case 'position':
                    $sort_string = 'Categories.position '. $sort_type .', Categories.id DESC';
                break;

                case 'created':
                    $sort_string = 'Categories.created '. $sort_type .', Categories.position DESC, Categories.id DESC';
                break;

                case 'updated':
                    $sort_string = 'Categories.updated '. $sort_type .', Categories.position DESC, Categories.id DESC';
                break;

                case 'created_by':
                    $sort_string = 'Categories.created_by '. $sort_type .', Categories.position DESC, Categories.id DESC';
                break;             
            }
        }

        return $table->find()->contain($contain)->where($where)->select($fields)->group('Categories.id')->order($sort_string);
    }

    public function getDetailCategory($type = null, $id = null, $lang = null, $params = [])
    {
        $result = [];
        if(empty($type) || !in_array($type, Configure::read('LIST_TYPE_CATEGORY'))) return [];
        if(empty($id) || empty($lang)) return [];        

        $get_user = !empty($params['get_user']) ? true : false;
        $get_attributes = !empty($params['get_attributes']) ? true : false;

        $contain = [
            'CategoriesContent' => function ($q) use ($lang) {
                return $q->where([
                    'CategoriesContent.lang' => $lang
                ]);
            }, 
            'Links' => function ($q) use ($type, $lang) {
                return $q->where([
                    'Links.type' => 'category_' . $type,
                    'Links.lang' => $lang,
                    'Links.deleted' => 0
                ]);
            }
        ];

        if($get_user){
            $contain[] = 'User';
        }

        if($get_attributes){
            $contain[] = 'CategoriesAttribute';
        }

        $result = TableRegistry::get('Categories')->find()->contain($contain)
        ->where([
            'Categories.id' => $id,
            'Categories.deleted' => 0,
        ])->first();

        return $result;
    }

    public function formatDataCategoryDetail($data = [])
    {
        if(empty($data)) return [];

        $lang = !empty($data['CategoriesContent']['lang']) ? $data['CategoriesContent']['lang'] : null;
        $result = [
            'id' => !empty($data['id']) ? intval($data['id']) : null,
            'type' => !empty($data['type']) ? $data['type'] : null,
            'parent_id' => !empty($data['parent_id']) ? intval($data['parent_id']) : null,
            'path_id' => !empty($data['path_id']) ? $data['path_id'] : null,
            'image_avatar' => !empty($data['image_avatar']) ? $data['image_avatar'] : null,
            'images' => !empty($data['images']) ? json_decode($data['images'], true) : null,
            'url_video' => !empty($data['url_video']) ? $data['url_video'] : null,
            'type_video' => !empty($data['type_video']) ? $data['type_video'] : null,            
            'created_by' => !empty($data['created_by']) ? intval($data['created_by']) : null,
            'created_by_user' => !empty($data['User']['full_name']) ? $data['User']['full_name'] : null,
            'created' => !empty($data['created']) ? date('H:i - d/m/Y', $data['created']) : null,
            'updated' => !empty($data['updated']) ? date('H:i - d/m/Y', $data['updated']) : null,
            'position' => !empty($data['position']) ? intval($data['position']) : null,            
            'status' => isset($data['status']) ? intval($data['status']) : null,
            
            'name' => !empty($data['CategoriesContent']['name']) ? $data['CategoriesContent']['name'] : null,
            'description' => !empty($data['CategoriesContent']['description']) ? $data['CategoriesContent']['description'] : null,
            'content' => !empty($data['CategoriesContent']['content']) ? $data['CategoriesContent']['content'] : null,                      
            'seo_title' => !empty($data['CategoriesContent']['seo_title']) ? $data['CategoriesContent']['seo_title'] : null,
            'seo_description' => !empty($data['CategoriesContent']['seo_description']) ? $data['CategoriesContent']['seo_description'] : null,
            'seo_keyword' => !empty($data['CategoriesContent']['seo_keyword']) ? $data['CategoriesContent']['seo_keyword'] : null,
            'lang' => $lang,
            'url_id' => !empty($data['Links']['id']) ? intval($data['Links']['id']) : null,
            'url' => !empty($data['Links']['url']) ? $data['Links']['url'] : null,
        ];

        if(!empty($data['User'])){
            $result['user_full_name'] = !empty($data['User']['full_name']) ? $data['User']['full_name'] : null;
        }

        if(!empty($data['CategoriesAttribute'])){
            $all_attributes = TableRegistry::get('Attributes')->getAll($lang);

            $attributes = [];
            foreach ($data['CategoriesAttribute'] as $key => $attribute) {
                $attribute_id = !empty($attribute['attribute_id']) ? intval($attribute['attribute_id']) : null;
                $attribute_info = !empty($all_attributes[$attribute_id]) ? $all_attributes[$attribute_id] : [];
                $attribute_code = !empty($attribute_info['code']) ? $attribute_info['code'] : null;
                if(empty($attribute_code)) continue;

                $attributes[$attribute_code] = !empty($attribute['value']) ? $attribute['value'] : null;
            }
            $result['attributes'] = $attributes;

        }

        if(!empty($data['children'])){
            $childrens = [];
            foreach ($data['children'] as $k => $children) {
                $childrens[$k] = $this->formatDataCategoryDetail($children);    
            }
            $result['children'] = $childrens;
        }

        return $result;
    }

    public function checkNameExist($name = null)
    {
        if(empty($name)) return false;
        $category = TableRegistry::get('Categories')->find()->contain(['CategoriesContent'])
        ->where([
            'CategoriesContent.name' => $name,
            'Categories.deleted' => 0,
        ])->first();
        return !empty($category) ? true : false;
    }

    public function getCategoriesChild($parent_id = null)
    {
        if(empty($parent_id)) return [];

        $categories = TableRegistry::get('Categories')->find()
        ->where([
            'Categories.path_id LIKE' => '%|' . $parent_id . '|%',
            'Categories.deleted' => 0,
        ])
        ->toArray();

        return $categories;
    }

    public function getAllChildCategoryId($category_id = null)
    {
        if(empty($category_id)) return [];
        $result = [intval($category_id)];

        $list_child = TableRegistry::get('Categories')->getCategoriesChild($category_id);
        if(empty($list_child)) return $result;

        foreach ($list_child as $key => $category) {
            if(!in_array(intval($category['id']), $result)){
                $result[] = intval($category['id']);
            }
        }

        return $result;
    }

    public function getAll($type = null, $lang = null)
    {
        if(empty($lang)) return [];

        $cache_key = CATEGORY . '_all_' . $type . '_' . $lang;
        $result = Cache::read($cache_key);

        if(is_null($result)){
            $result = [];
            $categories = TableRegistry::get('Categories')->find()
            ->where([
                'Categories.type' => $type,
                'Categories.deleted' => 0,
            ])
            ->contain([
                'CategoriesContent' => function ($q) use ($lang) {
                    return $q->where([
                        'CategoriesContent.lang' => $lang
                    ]);
                }, 
                'Links' => function ($q) use ($type, $lang) {
                    return $q->where([
                        'Links.type' => 'category_' . $type,
                        'Links.lang' => $lang,
                        'Links.deleted' => 0
                    ]);
                } 
            ])
            ->select([
                'Categories.id',
                'CategoriesContent.name', 
                'Categories.parent_id', 
                'Categories.status',
                'Categories.position',
                'Links.url'
            ])->toArray();        
            
            if(!empty($categories)){
                foreach ($categories as $key => $category) {
                    $category_id = !empty($category['id']) ? intval($category['id']) : null;
                    if(empty($category_id)) continue;

                    $result[$category_id] = [
                        'id' => !empty($category['id']) ? intval($category['id']) : null,
                        'name' => !empty($category['CategoriesContent']['name']) ? $category['CategoriesContent']['name'] : null,
                        'parent_id' => !empty($category['parent_id']) ? intval($category['parent_id']) : null,
                        'status' => !empty($category['status']) ? 1 : 0,
                        'position' => !empty($category['position']) ? intval($category['position']) : 0,
                        'url' => !empty($category['Links']['url']) ? $category['Links']['url'] : null                       
                    ];
                }
            }

            Cache::write($cache_key, $result);
        }

        return $result;
    }

    public function getAllNameContent($category_id = null)
    {
        if(empty($category_id)) return false;

        $category = TableRegistry::get('Categories')->find()->where([
            'Categories.id' => $category_id,
            'Categories.deleted' => 0
        ])->contain(['CategoriesContent'])->select(['CategoriesContent.lang', 'CategoriesContent.name'])->toArray();

        $result = Hash::combine($category, '{*}.CategoriesContent.lang', '{*}.CategoriesContent.name');

        return !empty($result) ? $result : null;
    }

    public function parseDataCategories($categories = [], $loop = 0)
    {
        if(empty($categories)) return [];

        $result = [];
        $loop ++;
        $char = '---- ';
        $char_level = '';

        for ($i = 1; $i < $loop; $i++) {
            $char_level .= $char;
        }        

        foreach($categories as $category){
            if(empty($category['id']) || empty($category['CategoriesContent']->name)) continue; 
            $category['CategoriesContent']->name = $char_level . $category['CategoriesContent']->name;
            $result[$category['id']] = $category; 

            if(!empty($category['children'])){
                $result += $this->parseDataCategories($category['children'], $loop);    
            }
        }

        return $result;
    }

    public function parseDataCategoriesExcel($categories = [], $char = '')
    {
        if(empty($categories)) return [];

        $result = [];
        $char_level = '';     

        foreach($categories as $category){
            $category_name = !empty($category['CategoriesContent']['name']) ? $category['CategoriesContent']['name'] : null;
            if(empty($category['id']) || empty($category_name)) continue; 

            $category['CategoriesContent']->name = $char . $category['CategoriesContent']->name;
            $result[$category['id']] = $category; 

            $char_level = $char. '[' . $category_name . ']-';
            if(!empty($category['children'])){
                $result += $this->parseDataCategoriesExcel($category['children'], $char_level);    
            }
        }

        return $result;
    }
}