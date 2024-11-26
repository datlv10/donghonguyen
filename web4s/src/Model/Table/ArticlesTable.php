<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use App\Model\Behavior\UnixTimestampBehavior;
use Cake\Utility\Text;
use Cake\Utility\Hash;

class ArticlesTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('articles');

        $this->setPrimaryKey('id');

        $this->addBehavior('UnixTimestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                    'updated' => 'existing'
                ]
            ]
        ]);

        $this->hasOne('ArticlesContent', [
            'className' => 'ArticlesContent',
            'foreignKey' => 'article_id',
            'propertyName' => 'ArticlesContent'
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

        $this->hasOne('CategoryArticle', [
            'className' => 'CategoriesArticle',
            'foreignKey' => 'article_id',
            'joinType' => 'LEFT',
            'propertyName' => 'CategoryArticle'
        ]);

        $this->hasMany('CategoriesArticle', [
            'className' => 'CategoriesArticle',
            'foreignKey' => 'article_id',
            'joinType' => 'LEFT',
            'propertyName' => 'CategoriesArticle'
        ]);

        $this->hasMany('ContentMutiple', [
            'className' => 'ArticlesContent',
            'foreignKey' => 'article_id',
            'joinType' => 'LEFT',
            'propertyName' => 'ContentMutiple'
        ]);

        $this->hasMany('LinksMutiple', [
            'className' => 'Links',
            'foreignKey' => 'foreign_id',
            'joinType' => 'LEFT',
            'conditions' => [
                'LinksMutiple.type' => ARTICLE_DETAIL,
                'LinksMutiple.deleted' => 0
            ],
            'propertyName' => 'LinksMutiple'
        ]);

        $this->hasOne('SingleAttribute', [
            'className' => 'Publishing.ArticlesAttribute',
            'foreignKey' => 'article_id',
            'joinType' => 'INNER',
            'propertyName' => 'SingleAttribute'
        ]);

        $this->hasMany('ArticlesAttribute', [
            'className' => 'ArticlesAttribute',
            'foreignKey' => 'article_id',
            'joinType' => 'LEFT',
            'propertyName' => 'ArticlesAttribute'
        ]);

        $this->hasMany('TagsRelation', [
            'className' => 'TagsRelation',
            'foreignKey' => 'foreign_id',
            'conditions' => [
                'TagsRelation.type' => ARTICLE_DETAIL
            ],
            'joinType' => 'LEFT',
            'propertyName' => 'TagsRelation'
        ]);

        $this->hasOne('TagArticle', [
            'className' => 'TagsRelation',
            'foreignKey' => 'foreign_id',
            'conditions' => [
                'TagArticle.type' => ARTICLE_DETAIL
            ],
            'joinType' => 'LEFT',
            'propertyName' => 'TagArticle'
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        return $validator;
    }

    public function queryListArticles($params = []) 
    {
        $table = TableRegistry::get('Articles');

        // get info params
        $field = !empty($params[FIELD]) ? $params[FIELD] : SIMPLE_INFO;
        $get_user = !empty($params['get_user']) ? $params['get_user'] : false;
        $get_categories = !empty($params['get_categories']) ? true : false;
        $get_attributes = !empty($params['get_attributes']) ? true : false;
        $get_empty_name = !empty($params['get_empty_name']) ? true : false;

        // sort
        $sort = !empty($params[SORT]) ? $params[SORT] : [];
        $sort_field = !empty($sort[FIELD]) ? $sort[FIELD] : null;
        $sort_type = !empty($sort[SORT]) ? $sort[SORT] : DESC;

        // filter
        $filter = !empty($params[FILTER]) ? $params[FILTER] : [];
        $lang = !empty($filter[LANG]) ? $filter[LANG] : TableRegistry::get('Languages')->getDefaultLanguage();
        $keyword = !empty($filter['keyword']) ? trim($filter['keyword']) : null;
        $status = isset($filter['status']) && $filter['status'] != '' ? intval($filter['status']) : null;
        $has_album = isset($filter['has_album']) && $filter['has_album'] != '' ? intval($filter['has_album']) : null;
        $has_video = isset($filter['has_video']) && $filter['has_video'] != '' ? intval($filter['has_video']) : null;
        $has_file = isset($filter['has_file']) && $filter['has_file'] != '' ? intval($filter['has_file']) : null;
        $featured = isset($filter['featured']) && $filter['featured'] != '' ? intval($filter['featured']) : null;
        $catalogue = isset($filter['catalogue']) && $filter['catalogue'] != '' ? intval($filter['catalogue']) : null;
        $seo_score = !empty($filter['seo_score']) ? trim($filter['seo_score']) : null;
        $keyword_score = !empty($filter['keyword_score']) ? trim($filter['keyword_score']) : null;
        $ids = !empty($filter['ids']) && is_array($filter['ids']) ? $filter['ids'] : [];
        $not_ids = !empty($filter['not_ids']) && is_array($filter['not_ids']) ? $filter['not_ids'] : [];
        $id_categories = !empty($filter['id_categories']) && is_array($filter['id_categories']) ? $filter['id_categories'] : [];
        $tag_id = !empty($filter['tag_id']) ? intval($filter['tag_id']) : null;
        $create_from = !empty($filter['create_from']) ? strtotime(date('Y-m-d 00:00:00', strtotime(str_replace('/', '-', $filter['create_from'])))) : null;
        $create_to = !empty($filter['create_to']) ? strtotime(date('Y-m-d 23:59:59', strtotime(str_replace('/', '-', $filter['create_to'])))) : null;

        // fields select
        switch($field){
            case FULL_INFO:
                $fields = ['Articles.id', 'Articles.image_avatar', 'Articles.images', 'Articles.url_video', 'Articles.type_video', 'Articles.files', 'Articles.view', 'Articles.like', 'Articles.rating', 'Articles.rating_number', 'Articles.comment', 'Articles.created_by', 'Articles.created', 'Articles.position', 'Articles.featured', 'Articles.catalogue', 'Articles.seo_score', 'Articles.keyword_score', 'Articles.status', 'Articles.draft', 'ArticlesContent.name', 'ArticlesContent.description', 'ArticlesContent.content', 'ArticlesContent.seo_title', 'ArticlesContent.seo_description', 'ArticlesContent.seo_keyword', 'ArticlesContent.lang', 'Links.id', 'Links.url'];
            break;

            case LIST_INFO:
                $fields = ['Articles.id', 'ArticlesContent.name'];
            break;

            case SIMPLE_INFO:
            default:
                $fields = ['Articles.id', 'Articles.image_avatar', 'Articles.images', 'Articles.url_video', 'Articles.type_video', 'Articles.rating', 'Articles.rating_number', 'Articles.view', 'Articles.files', 'Articles.has_album', 'Articles.has_file', 'Articles.has_video', 'Articles.created_by', 'Articles.created', 'Articles.position', 'Articles.featured', 'Articles.catalogue', 'Articles.seo_score', 'Articles.keyword_score', 'Articles.status', 'Articles.draft', 'ArticlesContent.name', 'ArticlesContent.description', 'ArticlesContent.lang', 'Links.id', 'Links.url'];
            break;
        }

        $where = ['Articles.deleted' => 0];
        
        //contain        
        if(!$get_empty_name){
            $contain = ['ArticlesContent', 'Links'];

            $where['ArticlesContent.lang'] = $lang;
            $where['Links.lang'] = $lang;
            $where['Links.type'] = ARTICLE_DETAIL;
            $where['Links.deleted'] = 0;
        }else{
            $contain = [
                'ArticlesContent' => function ($q) use ($lang) {
                    return $q->where([
                        'ArticlesContent.lang' => $lang
                    ]);
                }, 
                'Links' => function ($q) use ($lang) {
                    return $q->where([
                        'Links.type' => ARTICLE_DETAIL,
                        'Links.lang' => $lang,
                        'Links.deleted' => 0
                    ]);
                } 
            ];            
        }


        // filter by conditions  
        if(!empty($keyword)){
            $where['ArticlesContent.search_unicode LIKE'] = '%' . Text::slug(strtolower($keyword), ' ') . '%';
        }

        if(!empty($ids)){
            $where['Articles.id IN'] = $ids;
        }

        if(!empty($not_ids)){
            $where['Articles.id NOT IN'] = $not_ids;
        }

        if(!empty($id_categories)){
            // lay id danh muc con
            $all_category_ids = [];
            foreach($id_categories as $category_id){
                $child_category_ids = TableRegistry::get('Categories')->getAllChildCategoryId($category_id);
                $all_category_ids = array_unique(array_merge($all_category_ids, $child_category_ids));
            }

            $contain[] = 'CategoryArticle';
            $where['CategoryArticle.category_id IN'] = $all_category_ids;
        }

        if(!empty($tag_id)){
            $contain[] = 'TagArticle';
            $where['TagArticle.tag_id'] = $tag_id;
        }

        if(!is_null($status)){
            $where['Articles.status'] = $status;
        }

        if(!is_null($featured)){
            $where['Articles.featured'] = $featured;
        }

        if(!is_null($has_album)){
            $where['Articles.has_album'] = $has_album;
        }

        if(!is_null($has_video)){
            $where['Articles.has_video'] = $has_video;
        }

        if(!is_null($has_file)){
            $where['Articles.has_file'] = $has_file;
        }

        if(!is_null($catalogue)){
            $where['Articles.catalogue'] = $catalogue;
        }

        if(!empty($seo_score)){
            $where['Articles.seo_score'] = $seo_score;
        }

        if(!empty($keyword_score)){
            $where['Articles.keyword_score'] = $keyword_score;
        }

        if(!empty($create_from)){
            $where['Articles.created >='] = $create_from;
        }

        if(!empty($create_to)){
            $where['Articles.created <='] = $create_to;
        }

        if(!empty($get_user)){
            $fields[] = 'User.id';
            $fields[] = 'User.full_name';

            $contain[] = 'User';
        }

        if(!empty($get_categories)){
            $contain[] = 'CategoriesArticle';
        }

        if($get_attributes){
            $contain[] = 'ArticlesAttribute';
        }

        if(!empty($get_tags)){
            $contain[] = 'TagArticle';
        }

        // sort by
        $sort_string = 'Articles.id DESC';
        if(!empty($params[SORT])){
            switch($sort_field){
                case 'id':
                case 'article_id':
                    $sort_string = 'Articles.id '. $sort_type;
                break;

                case 'name':
                    $sort_string = 'ArticlesContent.name '. $sort_type .', Articles.position DESC, Articles.id DESC';
                break;

                case 'status':
                    $sort_string = 'Articles.status '. $sort_type .', Articles.position DESC, Articles.id DESC';
                break;

                case 'view':
                    $sort_string = 'Articles.view '. $sort_type .', Articles.id DESC';
                break;

                case 'position':
                    $sort_string = 'Articles.position '. $sort_type .', Articles.id DESC';
                break;

                case 'created':
                    $sort_string = 'Articles.created '. $sort_type .', Articles.position DESC, Articles.id DESC';
                break;

                case 'updated':
                    $sort_string = 'Articles.updated '. $sort_type .', Articles.position DESC, Articles.id DESC';
                break;

                case 'featured':
                    $sort_string = 'Articles.featured '. $sort_type .', Articles.position DESC, Articles.id DESC';
                break;

                case 'created_by':
                    $sort_string = 'Articles.created_by '. $sort_type .', Articles.position DESC, Articles.id DESC';
                break;             
            }
        }

        $query = $this->find()->contain($contain)->where($where)->select($fields)->order($sort_string);

        // khi có filter theo danh mục để tránh bị lặp lại
        if(!empty($id_categories)){
            $query = $query->group('Articles.id');
        }
        return $query;
    }

    public function getDetailArticle($id = null, $lang = null, $params = [])
    {
        $result = [];
        if(empty($id) || empty($lang)) return [];        

        $get_user = !empty($params['get_user']) ? true : false;
        $get_categories = !empty($params['get_categories']) ? true : false;
        $get_tags = !empty($params['get_tags']) ? true : false;
        $get_attributes = !empty($params['get_attributes']) ? true : false;
        $status = !empty($params['status']) ? intval($params['status']) : null;

        $contain = [
            'ArticlesContent' => function ($q) use ($lang) {
                return $q->where([
                    'ArticlesContent.lang' => $lang
                ]);
            }, 
            'Links' => function ($q) use ($lang) {
                return $q->where([
                    'Links.type' => ARTICLE_DETAIL,
                    'Links.lang' => $lang,
                    'Links.deleted' => 0
                ]);
            }
        ];


        $where = [
            'Articles.id' => $id,
            'Articles.deleted' => 0,
        ];
        if(!is_null($status)) {
            $where['Articles.status'] = $status;
        }

        if($get_user){
            $contain[] = 'User';
        }

        if($get_categories){
            $contain[] = 'CategoriesArticle';
        }

        if($get_attributes){
            $contain[] = 'ArticlesAttribute';
        }

        if($get_tags){
            $contain[] = 'TagsRelation';
        }

        $result = $this->find()->contain($contain)->where($where)->first();

        return $result;
    }

    public function formatDataArticleDetail($data = [], $lang = null)
    {
        if(empty($data) || empty($lang)) return [];
        $result = [
            'id' => !empty($data['id']) ? intval($data['id']) : null,
            'image_avatar' => !empty($data['image_avatar']) ? $data['image_avatar'] : null,
            'images' => !empty($data['images']) ? json_decode($data['images'], true) : null,
            'url_video' => !empty($data['url_video']) ? $data['url_video'] : null,
            'type_video' => !empty($data['type_video']) ? $data['type_video'] : null,
            'files' => !empty($data['files']) ? json_decode($data['files'], true) : null,
            'view' => !empty($data['view']) ? intval($data['view']) : null,
            'like' => !empty($data['like']) ? intval($data['like']) : null,
            'main_category_id' => !empty($data['main_category_id']) ? intval($data['main_category_id']) : null,
            'rating' => !empty($data['rating']) ? floatval($data['rating']) : null,
            'rating_number' => !empty($data['rating_number']) ? intval($data['rating_number']) : null,
            'has_album' => !empty($data['has_album']) ? 1 : 0,
            'has_file' => !empty($data['has_file']) ? 1 : 0,
            'has_video' => !empty($data['has_video']) ? 1 : 0,
            'comment' => !empty($data['comment']) ? intval($data['comment']) : null,
            'created_by' => !empty($data['created_by']) ? intval($data['created_by']) : null,
            'created_by_user' => !empty($data['User']['full_name']) ? $data['User']['full_name'] : null,
            'created' => !empty($data['created']) ? date('H:i - d/m/Y', $data['created']) : null,
            'updated' => !empty($data['updated']) ? date('H:i - d/m/Y', $data['updated']) : null,
            'position' => !empty($data['position']) ? intval($data['position']) : null,
            'featured' => !empty($data['featured']) ? 1 : 0,
            'catalogue' => !empty($data['catalogue']) ? 1 : 0,
            'seo_score' => !empty($data['seo_score']) ? $data['seo_score'] : null,
            'keyword_score' => !empty($data['keyword_score']) ? $data['keyword_score'] : null,
            'draft' => !empty($data['draft']) ? 1 : 0,
            'status' => isset($data['status']) ? intval($data['status']) : null,
            
            'name' => !empty($data['ArticlesContent']['name']) ? $data['ArticlesContent']['name'] : null,
            'description' => !empty($data['ArticlesContent']['description']) ? $data['ArticlesContent']['description'] : null,
            'content' => !empty($data['ArticlesContent']['content']) ? $data['ArticlesContent']['content'] : null,
            'tags' => [],

            'seo_title' => !empty($data['ArticlesContent']['seo_title']) ? $data['ArticlesContent']['seo_title'] : null,
            'seo_description' => !empty($data['ArticlesContent']['seo_description']) ? $data['ArticlesContent']['seo_description'] : null,
            'seo_keyword' => !empty($data['ArticlesContent']['seo_keyword']) ? $data['ArticlesContent']['seo_keyword'] : null,
            'lang' => !empty($data['ArticlesContent']['lang']) ? $data['ArticlesContent']['lang'] : null,

            'url_id' => !empty($data['Links']['id']) ? intval($data['Links']['id']) : null,
            'url' => !empty($data['Links']['url']) ? $data['Links']['url'] : null,
        ];

        if(!empty($data['User'])){
            $result['user_full_name'] = !empty($data['User']['full_name']) ? $data['User']['full_name'] : null;
        }

        if(!empty($data['CategoriesArticle'])){
            $categories = [];
            $all_categories = TableRegistry::get('Categories')->getAll(ARTICLE, $lang);
            foreach ($data['CategoriesArticle'] as $k => $category) {
                $category_id = !empty($category['category_id']) ? intval($category['category_id']) : null;
                $category_info = !empty($all_categories[$category_id]) ? $all_categories[$category_id] : [];
                if(empty($category_info)) continue;

                $categories[$category_id] = [
                    'id' => $category_id,
                    'name' => !empty($category_info['name']) ? $category_info['name'] : null,
                    'url' => !empty($category_info['url']) ? $category_info['url'] : null,
                ];
            }
            $result['categories'] = $categories;
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

        
        $attributes_table = TableRegistry::get('Attributes');
        $all_attributes = Hash::combine($attributes_table->getAll($lang), '{n}.id', '{n}', '{n}.attribute_type');
        $all_attributes_article = !empty($all_attributes[ARTICLE]) ? $all_attributes[ARTICLE] : [];

        if(!empty($all_attributes_article) && !empty($data['ArticlesAttribute'])){
            $attributes = [];
            $attribute_value = Hash::combine($data['ArticlesAttribute'], '{n}.attribute_id', '{n}');
            foreach ($all_attributes_article as $attribute_id => $attribute_info) {
                $attribute_code = !empty($attribute_info['code']) ? $attribute_info['code'] : null;
                $attribute_name = !empty($attribute_info['name']) ? $attribute_info['name'] : null;
                $attribute_input_type = !empty($attribute_info['input_type']) ? $attribute_info['input_type'] : null;
                if(empty($attribute_code) || empty($attribute_name)) continue;

                $value = !empty($attribute_value[$attribute_id]['value']) ? $attribute_value[$attribute_id]['value'] : null;
                $value = $attributes_table->formatValueAttribute($attribute_input_type, $value, $lang);
                $attributes[$attribute_code] = [
                    'id' => $attribute_id,
                    'name' => $attribute_name,
                    'value' => $value
                ];
            }
            
            $result['attributes'] = $attributes;
        }

        return $result;
    }

    public function checkNameExist($name = null)
    {
        if(empty($name)) return false;
        $article = $this->find()->contain(['ArticlesContent'])
        ->where([
            'ArticlesContent.name' => $name,
            'Articles.deleted' => 0,
        ])->first();
        return !empty($article) ? true : false;
    }

    public function getAllNameContent($article_id = null)
    {
        if(empty($article_id)) return false;

        $article = $this->find()->where([
            'Articles.id' => $article_id,
            'Articles.deleted' => 0
        ])->contain(['ArticlesContent'])->select(['ArticlesContent.lang', 'ArticlesContent.name'])->toArray();

        $result = Hash::combine($article, '{*}.ArticlesContent.lang', '{*}.ArticlesContent.name');

        return !empty($result) ? $result : null;
    }
}