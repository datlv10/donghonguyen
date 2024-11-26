<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use App\Model\Behavior\UnixTimestampBehavior;
use Cake\Utility\Text;
use Cake\Cache\Cache;

class TemplatesBlockTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('templates_block');
        $this->setPrimaryKey('id');

        $this->addBehavior('UnixTimestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                    'updated' => 'existing'
                ]
            ]
        ]);
    }


    public function queryListBlocks($params = []) 
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
        $template_code = !empty($filter['template_code']) ? $filter['template_code'] : null;
        $ids = !empty($filter['ids']) ? $filter['ids'] : [];

        // fields select
        switch($field){
            case LIST_INFO:
                $fields = ['TemplatesBlock.code', 'TemplatesBlock.name'];
            break;

            case FULL_INFO:
            case SIMPLE_INFO:
            default:
                $fields = ['TemplatesBlock.id', 'TemplatesBlock.template_code', 'TemplatesBlock.code', 'TemplatesBlock.name', 'TemplatesBlock.type', 'TemplatesBlock.action', 'TemplatesBlock.view', 'TemplatesBlock.element_view', 'TemplatesBlock.config', 'TemplatesBlock.data_extend', 'TemplatesBlock.created', 'TemplatesBlock.created_by', 'TemplatesBlock.updated', 'TemplatesBlock.status'];
            break;
        }

        $sort_string = 'TemplatesBlock.id DESC';
        if(!empty($params[SORT])){
            switch($sort_field){
                case 'id':
                    $sort_string = 'TemplatesBlock.id '. $sort_type;
                break;

                case 'name':
                    $sort_string = 'TemplatesBlock.name '. $sort_type .', TemplatesBlock.id DESC';
                break;

                case 'type':
                    $sort_string = 'TemplatesBlock.type '. $sort_type .', TemplatesBlock.id DESC';
                break;

                case 'status':
                    $sort_string = 'TemplatesBlock.status '. $sort_type .', TemplatesBlock.id DESC';
                break;

                case 'created':
                    $sort_string = 'TemplatesBlock.created '. $sort_type .', TemplatesBlock.id DESC';
                break;

                case 'updated':
                    $sort_string = 'TemplatesBlock.updated '. $sort_type .', TemplatesBlock.id DESC';
                break;

                case 'created_by':
                    $sort_string = 'TemplatesBlock.created_by '. $sort_type .', TemplatesBlock.id DESC';
                break;             
            }
        }

        // filter by conditions
        $where = [
            'TemplatesBlock.template_code' => CODE_TEMPLATE,
            'TemplatesBlock.deleted' => 0
        ];

        if(!empty($ids)){
            $where['TemplatesBlock.id IN'] = $ids;
        }

        if(!empty($keyword)){
            $where['TemplatesBlock.search_unicode LIKE'] = '%' . Text::slug(strtolower($keyword), ' ') . '%';
        }

        if(!is_null($status)){
            $where['TemplatesBlock.status'] = $status;
        }

        if(!empty($type)){
            $where['TemplatesBlock.type'] = $type;   
        }

        if(!empty($template_code)){
            $where['TemplatesBlock.template_code'] = $template_code;
        }

        return TableRegistry::get('TemplatesBlock')->find()->where($where)->select($fields)->group('TemplatesBlock.id')->order($sort_string);
    }
    
    public function getInfoBlock($code = null)
    {
        if(empty($code)) return [];

        $cache_key = BLOCK . '_' . $code;
        $result = Cache::read($cache_key);

        if(is_null($result)){
            $where = [
                'TemplatesBlock.template_code' => CODE_TEMPLATE,
                'TemplatesBlock.code' => $code,
                'TemplatesBlock.deleted' => 0
            ];

            $fields = ['TemplatesBlock.id', 'TemplatesBlock.template_code', 'TemplatesBlock.code', 'TemplatesBlock.name', 'TemplatesBlock.type', 'TemplatesBlock.view', 'TemplatesBlock.config', 'TemplatesBlock.data_extend', 'TemplatesBlock.normal_data_extend', 'TemplatesBlock.status'];

            $result = TableRegistry::get('TemplatesBlock')->find()->where($where)->select($fields)->first();            
            if(!empty($result)){
                $result['config'] = !empty($result['config']) ? json_decode($result['config'], true) : [];
                $result['data_extend'] = !empty($result['data_extend']) ? json_decode($result['data_extend'], true) : [];
                $result['normal_data_extend'] = !empty($result['normal_data_extend']) ? json_decode($result['normal_data_extend'], true) : [];
            }            

            Cache::write($cache_key, !empty($result) ? $result : []);
        }
        
        return $result;
    }

    public function checkNameExist($name = null)
    {
        if(empty($name)) return false;
        $block = $this->find()->where(['name' => $name, 'deleted' => 0])->first();
        return !empty($block) ? true : false;
    }
}