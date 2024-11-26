<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Cache\Cache;
use Cake\Utility\Hash;

class PluginsTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('plugins');

        $this->setPrimaryKey('id');  
    }

    public function getList()
    {
        $cache_key = PLUGIN . '_list';
        $result = Cache::read($cache_key);

        if(is_null($result)){
            $plugins = TableRegistry::get('Plugins')->find()->where([ 'status' => 1])->select(['code', 'name'])->toArray();
            $result =  Hash::combine($plugins, '{n}.code', '{n}.name');

            Cache::write($cache_key, !empty($result) ? $result : []);
        }

        return $result;
    }

    public function queryListPlugin($params = []) 
    {
        // get info params
        $field = !empty($params[FIELD]) ? $params[FIELD] : SIMPLE_INFO;

        // sort
        $sort = !empty($params[SORT]) ? $params[SORT] : [];
        $sort_field = !empty($sort[FIELD]) ? $sort[FIELD] : null;
        $sort_type = !empty($sort[SORT]) && in_array($sort[SORT], [DESC, ASC]) ? $sort[SORT] : DESC;

        // filter
        $filter = !empty($params[FILTER]) ? $params[FILTER] : [];       
        $type = !empty($filter[TYPE]) ? $filter[TYPE] : null;
        $keyword = !empty($filter['keyword']) ? trim($filter['keyword']) : null;
        $status = isset($filter['status']) && $filter['status'] != '' ? intval($filter['status']) : null;

        // fields select
        switch($field){
            case LIST_INFO:
                $fields = ['Plugins.code', 'Plugins.name'];
            break;

            case FULL_INFO:
            case SIMPLE_INFO:
            default:
                $fields = ['Plugins.id', 'Plugins.code', 'Plugins.name', 'Plugins.status'];
            break;
        }

        $sort_string = 'Plugins.id DESC';
        if(!empty($params[SORT])){
            switch($sort_field){
                case 'id':
                    $sort_string = 'Plugins.id '. $sort_type;
                break;

                case 'name':
                    $sort_string = 'Plugins.name '. $sort_type .', Plugins.id DESC';
                break;

                case 'status':
                    $sort_string = 'Plugins.status '. $sort_type .', Plugins.id DESC';
                break;           
            }
        }

        return TableRegistry::get('Plugins')->find()->select($fields)->group('Plugins.id')->order($sort_string);
    }
}