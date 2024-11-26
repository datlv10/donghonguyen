<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use App\Model\Behavior\UnixTimestampBehavior;
use Cake\Utility\Text;
use Cake\Cache\Cache;

class MobileTemplateBlockTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('mobile_template_block');
        $this->setPrimaryKey('id');
    }

    public function queryListMobileBlocks($params = []) 
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
                $fields = ['MobileTemplateBlock.code', 'MobileTemplateBlock.name'];
            break;

            case FULL_INFO:
            case SIMPLE_INFO:
            default:
                $fields = ['MobileTemplateBlock.id', 'MobileTemplateBlock.template_code', 'MobileTemplateBlock.code', 'MobileTemplateBlock.name', 'MobileTemplateBlock.type', 'MobileTemplateBlock.config', 'MobileTemplateBlock.status'];
            break;
        }

        $sort_string = 'MobileTemplateBlock.id DESC';
        if(!empty($params[SORT])){
            switch($sort_field){
                case 'id':
                    $sort_string = 'MobileTemplateBlock.id '. $sort_type;
                break;

                case 'name':
                    $sort_string = 'MobileTemplateBlock.name '. $sort_type .', MobileTemplateBlock.id DESC';
                break;

                case 'type':
                    $sort_string = 'MobileTemplateBlock.type '. $sort_type .', MobileTemplateBlock.id DESC';
                break;

                case 'status':
                    $sort_string = 'MobileTemplateBlock.status '. $sort_type .', MobileTemplateBlock.id DESC';
                break;           
            }
        }

        // filter by conditions
        $where = [];
        if(!empty(CODE_MOBILE_TEMPLATE)){
            $where = [
                'MobileTemplateBlock.template_code' => CODE_MOBILE_TEMPLATE
            ];
        }
        

        if(!empty($ids)){
            $where['MobileTemplateBlock.id IN'] = $ids;
        }

        if(!empty($keyword)){
            $where['MobileTemplateBlock.search_unicode LIKE'] = '%' . Text::slug(strtolower($keyword), ' ') . '%';
        }

        if(!is_null($status)){
            $where['MobileTemplateBlock.status'] = $status;
        }

        if(!empty($type)){
            $where['MobileTemplateBlock.type'] = $type;   
        }

        if(!empty($template_code)){
            $where['MobileTemplateBlock.template_code'] = $template_code;
        }

        return TableRegistry::get('MobileTemplateBlock')->find()->where($where)->select($fields)->group('MobileTemplateBlock.id')->order($sort_string);
    }
    
    public function getInfoBlock($code = null)
    {
        if(empty($code)) return [];

        $cache_key = MOBILE_BLOCK . '_' . $code;
        $result = Cache::read($cache_key);

        if(is_null($result)){
            $where = [
                'MobileTemplateBlock.template_code' => CODE_MOBILE_TEMPLATE,
                'MobileTemplateBlock.code' => $code
            ];

            $fields = ['MobileTemplateBlock.id', 'MobileTemplateBlock.template_code', 'MobileTemplateBlock.code', 'MobileTemplateBlock.name', 'MobileTemplateBlock.type', 'MobileTemplateBlock.config', 'MobileTemplateBlock.status'];

            $result = TableRegistry::get('MobileTemplateBlock')->find()->where($where)->select($fields)->first();            
            if(!empty($result)){
                $result['config'] = !empty($result['config']) ? json_decode($result['config'], true) : [];
            }            

            Cache::write($cache_key, !empty($result) ? $result : []);
        }
        
        return $result;
    }
}