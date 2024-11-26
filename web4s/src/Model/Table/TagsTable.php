<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Text;

class TagsTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('tags');
        $this->setPrimaryKey('id');

        $this->addBehavior('UnixTimestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                    'updated' => 'always'
                ]
            ]
        ]);

        $this->hasMany('TagsRelation', [
            'className' => 'TagsRelation',
            'foreignKey' => 'tag_id',
            'joinType' => 'LEFT',
            'propertyName' => 'TagsRelation'
        ]);
    }

    public function queryListTags($params = []) 
    {
        $table = TableRegistry::get('Tags');

        // get info params
        $field = !empty($params[FIELD]) ? $params[FIELD] : SIMPLE_INFO;

        // sort
        $sort = !empty($params[SORT]) ? $params[SORT] : [];
        $sort_field = !empty($sort[FIELD]) ? $sort[FIELD] : null;
        $sort_type = !empty($sort[SORT]) ? $sort[SORT] : DESC;

        // filter
        $filter = !empty($params[FILTER]) ? $params[FILTER] : [];

        $lang = !empty($filter[LANG]) ? $filter[LANG] : null;
        $keyword = !empty($filter['keyword']) ? trim($filter['keyword']) : null;
        $ids = !empty($filter['ids']) ? $filter['ids'] : [];

        // fields select
        switch($field){
            case FULL_INFO:
                $fields = ['Tags.id', 'Tags.name', 'Tags.url', 'Tags.content', 'Tags.seo_title', 'Tags.seo_description', 'Tags.seo_keyword', 'Tags.lang'];
            break;

            case LIST_INFO:
                $fields = ['Tags.id', 'Tags.name'];
            break;

            case SIMPLE_INFO:
            default:
                $fields = ['Tags.id', 'Tags.name', 'Tags.url', 'Tags.lang'];
            break;
        }

        $sort_string = 'Tags.id ASC';
        if(!empty($params[SORT])){
            switch($sort_field){
                case 'id':
                case 'tag_id':
                    $sort_string = 'Tags.id '. $sort_type;
                break;

                case 'name':
                    $sort_string = 'Tags.name '. $sort_type .', Tags.id ASC';
                break;

                case 'lang':
                    $sort_string = 'Tags.lang '. $sort_type .', Tags.id ASC';
                break;
            }
        }

        // filter by conditions
        $where = [];    

        if(!empty($keyword)){
            $where['Tags.search_unicode LIKE'] = '%' . Text::slug(strtolower($keyword), ' ') . '%';
        }

        if(!empty($ids)){
            $where['Tags.id IN'] = $ids;
        }

        if(!empty($lang)){
            $where['Tags.lang'] = $lang;
        }

        return $table->find()->where($where)->select($fields)->order($sort_string);
    }

    public function checkTagExist($name = null, $id = null)
    {
        if(empty($tag)) return false;

        $where = ['Tags.name' => $name];
        if(!empty($id)){
            $where['Tags.id !='] = $id;
        }
        $result = TableRegistry::get('Tags')->find()->where($where)->first();

        return !empty($result) ? true : false;
    }

    public function checkUrlTagExist($url = null, $id = null)
    {
        if(empty($url)) return false;

        $where = ['Tags.url' => $url];
        if(!empty($id)){
            $where['Tags.id !='] = $id;
        }

        $result = TableRegistry::get('Tags')->find()->where($where)->first();

        return !empty($result) ? true : false;
    }

    public function getUrlUnique($url = null, $index = 0)
    {
        if(empty($url)) return null;

        $result = $url;
        if($index > 0){
            $result .= '-'. $index;
        }

        if($index >= 100) return $result;

        $check = TableRegistry::get('Tags')->checkUrlTagExist($result);
        
        if($check){
            $index ++;
            $result = $this->getUrlUnique($url, $index);
        }

        return $result;
    }

    public function getTagByUrl($url = null, $params = [])
    {
        if(empty($url)) return [];

        $field = !empty($params[FIELD]) ? $params[FIELD] : SIMPLE_INFO;
        switch($field){
            case FULL_INFO:
                $fields = ['Tags.id', 'Tags.name', 'Tags.url', 'Tags.content', 'Tags.seo_title', 'Tags.seo_description', 'Tags.seo_keyword', 'Tags.lang'];
            break;

            case LIST_INFO:
                $fields = ['Tags.id', 'Tags.name'];
            break;

            case SIMPLE_INFO:
            default:
                $fields = ['Tags.id', 'Tags.name', 'Tags.url', 'Tags.lang'];
            break;
        }

        $result = TableRegistry::get('Tags')->find()->where(['Tags.url' => $url])->select($fields)->first();

        return !empty($result) ? $result : [];
    }

    public function getDetailTag($id = null, $lang = null)
    {
        $result = [];
        if(empty($id) || empty($lang)) return [];        

        $result = TableRegistry::get('Tags')->find()
        ->where([
            'Tags.id' => $id,
            'Tags.lang' => $lang
        ])->first();

        return $result;
    }
}