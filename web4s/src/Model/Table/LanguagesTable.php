<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Validation\Validator;
use Cake\Cache\Cache;

class LanguagesTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('languages');
        $this->setPrimaryKey('id');
        $this->setDisplayField('id');
    }

    public function getList()
    {
        $cache_key = LANG . '_list';
        $result = Cache::read($cache_key);

        if(is_null($result)){
            $list_language = TableRegistry::get('Languages')->find()->where([
                'deleted' => 0, 
                'status' => 1
            ])
            ->select(['code', 'name'])
            ->order('Languages.is_default DESC, Languages.id ASC')
            ->toArray();
            $result = Hash::combine($list_language, '{n}.code', '{n}.name');
            
            Cache::write($cache_key, !empty($result) ? $result : []);
        }        
        
        return $result;
    }

    public function queryListLanguages($params = []) 
    {
        $table = TableRegistry::get('Languages');

        // fields select
        $fields = ['id', 'name', 'status', 'is_default', 'code'];

        // filter by conditions
        $where = ['deleted' => 0];        
        $filter = !empty($params['filter']) ? $params['filter'] : [];

        $keyword = !empty($filter['keyword']) ? trim($filter['keyword']) : null;
        $status = isset($filter['status']) ? intval($filter['status']) : null;
        if(!empty($keyword)){
            $where['name LIKE'] = '%' . $keyword . '%';
        }

        if(!is_null($status)){
            $where['status'] = $status;
        }
        return $table->find()->where($where)->select($fields);
    }

    public function checkUseMultipleLanguage()
    {
        $cache_key = LANG . '_use_multiple';
        $result = Cache::read($cache_key);

        if(is_null($result)){
            $language_table = TableRegistry::get('Languages');
            $count = $language_table->find()->where(['deleted' => 0, 'status' => 1])->select(['code', 'name'])->count();       
            $result = !empty($count) && $count > 1 ? true : false;

            Cache::write($cache_key, $result);
        }                
        return $result;
    }

    public function getDefaultLanguage()
    {
        $cache_key = LANG . '_default';
        $result = Cache::read($cache_key);

        if(is_null($result)){
            $result = TableRegistry::get('Languages')->find()->where([
                'deleted' => 0, 
                'status' => 1, 
                'is_default' => 1
            ])->select(['code', 'name'])
            ->first();        
            $result = !empty($result['code']) ? $result['code'] : LANGUAGE_DEFAULT;
            Cache::write($cache_key, $result);
        }        
        
        return $result;
    }

    public function getLangByQueryParams($params = [])
    {
        $table = TableRegistry::get('Languages');

        $lang = !empty($params[LANG]) ? $params[LANG] : null;
        $list_languages = $table->getList();
        
        if(empty($lang) || empty($list_languages[$lang])){
            $lang = $table->getDefaultLanguage();
        }
        return $lang;
    }


}