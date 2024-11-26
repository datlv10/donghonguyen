<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;

class MobileTemplatePageTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('mobile_template_page');
        $this->setPrimaryKey('id');

        $this->addBehavior('UnixTimestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                    'updated' => 'always'
                ]
            ]
        ]);
    }

    public function getInfoPage($params = [])
    {
        $code = !empty($params['code']) ? trim($params['code']) : null;
        $type = !empty($params['type']) ? $params['type'] : null;

        if(empty($code) && empty($type)) return [];

        $where = [
            'MobileTemplatePage.template_code' => CODE_MOBILE_TEMPLATE
        ];

        if(!empty($code)){
            $where['MobileTemplatePage.code'] = $code;
        }

        if(!empty($type)){
            $where['MobileTemplatePage.type'] = $type;
        }

        $result = TableRegistry::get('MobileTemplatePage')->find()->where($where)->group('MobileTemplatePage.id')->first();

        return $result;
    }

    public function filterPage($params = [])
    {
        $type = !empty($params['type']) ? $params['type'] : null;
        $category_id = !empty($params['category_id']) ? intval($params['category_id']) : null;
        if(empty($type)) return [];

        $where = [
            'MobileTemplatePage.template_code' => CODE_MOBILE_TEMPLATE,
            'MobileTemplatePage.type' => $type,
            'MobileTemplatePage.page_type' => PAGE
        ];

        if(!empty($category_id)){
            $order = 'MobileTemplatePage.category_id DESC';
            $pages = TableRegistry::get('MobileTemplatePage')->find()->where($where)->group('MobileTemplatePage.id')->order($order)->toArray();
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
            $where['MobileTemplatePage.category_id'] = 0;
            $page = TableRegistry::get('MobileTemplatePage')->find()->where($where)->group('MobileTemplatePage.id')->order('MobileTemplatePage.category_id ASC')->first();
            return $page;
        }
    }

    public function getHomePage()
    {
        return TableRegistry::get('MobileTemplatePage')->find()->where([
            'MobileTemplatePage.template_code' => CODE_MOBILE_TEMPLATE,
            'MobileTemplatePage.type' => HOME,
        ])->first();
    }

    public function getListPageContent()
    {
        $list_page = TableRegistry::get('MobileTemplatePage')->find()
        ->contain(['ContentMutiple'])
        ->where([
            'MobileTemplatePage.template_code' => CODE_MOBILE_TEMPLATE,
            'MobileTemplatePage.page_type' => PAGE
        ])->order('MobileTemplatePage.id ASC')->toArray();
        
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
        $result = TableRegistry::get('MobileTemplatePage')->find()
        ->where([
            'MobileTemplatePage.name' => $name,
            'MobileTemplatePage.template_code' => CODE_MOBILE_TEMPLATE
        ])->first();
        return !empty($result) ? true : false;
    }
}