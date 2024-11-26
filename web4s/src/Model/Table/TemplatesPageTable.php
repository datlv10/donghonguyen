<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;

class TemplatesPageTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('templates_page');
        $this->setPrimaryKey('id');

        $this->addBehavior('UnixTimestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                    'updated' => 'always'
                ]
            ]
        ]);

        $this->hasOne('TemplatesPageContent', [
            'className' => 'Publishing.TemplatesPageContent',
            'foreignKey' => 'page_code',
            'bindingKey' => 'code',
            'propertyName' => 'TemplatesPageContent'
        ]);

        $this->hasMany('ContentMutiple', [
            'className' => 'TemplatesPageContent',
            'foreignKey' => 'page_code',
            'bindingKey' => 'code',
            'joinType' => 'LEFT',
            'conditions' => [
                'ContentMutiple.template_code' => CODE_TEMPLATE
            ],
            'propertyName' => 'ContentMutiple'
        ]);

        $this->hasMany('TemplatesRow', [
            'className' => 'TemplatesRow',
            'foreignKey' => 'page_code',
            'bindingKey' => 'code',
            'joinType' => 'LEFT',
            'propertyName' => 'TemplatesRow'
        ]);

        $this->hasMany('TemplatesColumn', [
            'className' => 'TemplatesColumn',
            'foreignKey' => 'page_code',
            'bindingKey' => 'code',
            'joinType' => 'LEFT',
            'propertyName' => 'TemplatesColumn'
        ]);
    }

    public function getInfoPage($params = [])
    {
        $url = !empty($params['url']) ? trim($params['url']) : null;
        $code = !empty($params['code']) ? trim($params['code']) : null;
        $type = !empty($params['type']) ? $params['type'] : null;
        $lang = !empty($params['lang']) ? $params['lang'] : null;
        $page_type = !empty($params['page_type']) ? $params['page_type'] : null;
        $get_content = !empty($params['get_content']) ? true : false;

        if(empty($code) && empty($url) && empty($type)) return [];

        $where = [
            'TemplatesPage.template_code' => CODE_TEMPLATE
        ];

        $contain = [];

        if(!empty($page_type)){
            $where['TemplatesPage.page_type'] = $page_type;
        }

        if(!empty($url)){
            $get_content = true;
            $where['TemplatesPageContent.url'] = $url;
        }

        if(!empty($code)){
            $where['TemplatesPage.code'] = $code;
        }

        if(!empty($type)){
            $where['TemplatesPage.type'] = $type;
        }

        if(!empty($page_type)){
            $where['TemplatesPage.page_type'] = $page_type;
        }

        if($get_content && empty($lang)) return [];
        if($get_content){
            $contain = [
                'TemplatesPageContent' => function ($q) use ($lang) {
                    return $q->where([
                        'TemplatesPageContent.lang' => $lang
                    ]);
                }
            ];
        }

        $result = $this->find()->contain($contain)->where($where)->group('TemplatesPage.id')->first();
        if($get_content && !empty($result)){
            $result['url'] = !empty($result['TemplatesPageContent']['url']) ? $result['TemplatesPageContent']['url'] : null;
            $result['lang'] = $lang;
            unset($result['TemplatesPageContent']);
        }
        
        return $result;
    }

    public function filterPage($params = [])
    {
        $type = !empty($params['type']) ? $params['type'] : null;
        $category_id = !empty($params['category_id']) ? intval($params['category_id']) : null;
        if(empty($type)) return [];

        $where = [
            'TemplatesPage.template_code' => CODE_TEMPLATE,
            'TemplatesPage.type' => $type,
            'TemplatesPage.page_type' => PAGE
        ];

        if(!empty($category_id)){
            $order = 'TemplatesPage.category_id DESC';
            $pages = $this->find()->where($where)->group('TemplatesPage.id')->order($order)->toArray();
            if(empty($pages)) return [];

            $categories_table = TableRegistry::get('Categories');
            foreach ($pages as $key => $page) {                
                if(!empty($page['category_id'])){
                    $list_category_ids = !empty($page['category_id']) ? $categories_table->getAllChildCategoryId(intval($page['category_id'])) : [];
                    if(in_array($category_id, $list_category_ids)){
                        return $page;
                    }
                }else{
                    return $page;
                }                
            }
        }else{
            $where['OR'] = [
                'TemplatesPage.category_id' => 0,
                'TemplatesPage.category_id IS' => null
            ];
            $page = $this->find()->where($where)->group('TemplatesPage.id')->order('TemplatesPage.category_id ASC')->first();
            return $page;
        }
    }

    public function getHomePage()
    {
        return TableRegistry::get('TemplatesPage')->find()->where([
            'TemplatesPage.template_code' => CODE_TEMPLATE,
            'TemplatesPage.type' => HOME,
        ])->first();
    }

    public function getListPageContent()
    {
        $list_page = TableRegistry::get('TemplatesPage')->find()
        ->contain(['ContentMutiple'])
        ->where([
            'TemplatesPage.template_code' => CODE_TEMPLATE,
            'TemplatesPage.page_type' => PAGE
        ])->order('TemplatesPage.id ASC')->toArray();
        
        $result = [];
        if(!empty($list_page)) {
            foreach ($list_page as $k => $v) {
                $contents = [];
                if(!empty($v['ContentMutiple'])) {
                    foreach($v['ContentMutiple'] as $k => $content) {
                        $lang = !empty($content['lang']) ? $content['lang'] : null;
                        $contents[$lang] = $content;
                    }
                }
                $v['content'] = $contents;
                unset($v['ContentMutiple']);
                $result[] = $v;
            }
        }  

        return $result;
    }

    public function checkNameExist($name = null)
    {
        if(empty($name)) return false;
        $result = TableRegistry::get('TemplatesPage')->find()
        ->where([
            'TemplatesPage.name' => $name,
            'TemplatesPage.template_code' => CODE_TEMPLATE
        ])->first();
        return !empty($result) ? true : false;
    }
}