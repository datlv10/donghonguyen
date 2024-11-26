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

class ShippingsMethodTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('shippings_method');

        $this->setPrimaryKey('id');

        $this->hasOne('ShippingsMethodContent', [
            'className' => 'ShippingsMethodContent',
            'foreignKey' => 'shipping_method_id',
            'propertyName' => 'ShippingsMethodContent'
        ]);


        $this->hasMany('ContentMutiple', [
            'className' => 'ShippingsMethodContent',
            'foreignKey' => 'shipping_method_id',
            'joinType' => 'LEFT',
            'propertyName' => 'ContentMutiple'
        ]);

    }

    public function getList($lang = null)
    {
        if(empty($lang)) return [];

        $cache_key = SHIPPING_METHOD . '_list_' . $lang;
        $result = Cache::read($cache_key);

        if(is_null($result)){
            $contain = [
                'ShippingsMethodContent' => function ($q) use ($lang) {
                    return $q->where([
                        'ShippingsMethodContent.lang' => $lang
                    ]);
                }
            ];

            $fields = ['ShippingsMethod.id', 'ShippingsMethod.general_shipping_fee', 'ShippingsMethod.type_fee', 'ShippingsMethod.custom_config', 'ShippingsMethodContent.name', 'ShippingsMethodContent.description'];
            
            $shipping_methods = TableRegistry::get('ShippingsMethod')->find()->contain($contain)->where([
                'ShippingsMethod.status' => 1,
                'ShippingsMethod.deleted' => 0
            ])->select($fields)->toArray();

            $result = [];
            if(!empty($shipping_methods)){
                foreach ($shipping_methods as $k => $item) {
                    $result[$item['id']] = [
                        'id' => !empty($item['id']) ? intval($item['id']) : null,
                        'general_shipping_fee' => !empty($item['general_shipping_fee']) ? intval($item['general_shipping_fee']) : null,
                        'type_fee' => !empty($item['type_fee']) ? $item['type_fee'] : null,
                        'custom_config' => !empty($item['custom_config']) ? json_decode($item['custom_config'], true) : [],
                        'name' => !empty($item['ShippingsMethodContent']['name']) ? $item['ShippingsMethodContent']['name'] : null,
                        'description' => !empty($item['ShippingsMethodContent']['description']) ? $item['ShippingsMethodContent']['description'] : null
                    ];
                }
            }
            Cache::write($cache_key, $result);
        }        
        return $result;
    }

    public function queryListShippingsMethod($params = []) 
    {
        $table = TableRegistry::get('ShippingsMethod');

        // get info params
        $field = !empty($params[FIELD]) ? $params[FIELD] : SIMPLE_INFO;

        // sort
        $sort = !empty($params[SORT]) ? $params[SORT] : [];
        $sort_field = !empty($sort[FIELD]) ? $sort[FIELD] : null;
        $sort_type = !empty($sort[SORT]) ? $sort[SORT] : DESC;

        // filter
        $filter = !empty($params[FILTER]) ? $params[FILTER] : [];
        $lang = !empty($filter[LANG]) ? $filter[LANG] : TableRegistry::get('Languages')->getDefaultLanguage();
        $keyword = !empty($filter['keyword']) ? trim($filter['keyword']) : null;
        $status = isset($filter['status']) && $filter['status'] != '' ? intval($filter['status']) : null;      
        $ids = !empty($filter['ids']) && is_array($filter['ids']) ? $filter['ids'] : [];
        $not_ids = !empty($filter['not_ids']) && is_array($filter['not_ids']) ? $filter['not_ids'] : [];

        // fields select
        switch($field){
            case FULL_INFO:
                $fields = ['ShippingsMethod.id', 'ShippingsMethod.general_shipping_fee', 'ShippingsMethod.type_fee', 'ShippingsMethod.custom_config', 'ShippingsMethod.status', 'ShippingsMethod.position', 'ShippingsMethodContent.name', 'ShippingsMethodContent.description', 'ShippingsMethodContent.content', 'ShippingsMethodContent.lang'];
            break;

            case LIST_INFO:
                $fields = ['ShippingsMethod.id', 'ShippingsMethodContent.name'];
            break;

            case SIMPLE_INFO:
            default:
                $fields =  ['ShippingsMethod.id', 'ShippingsMethod.general_shipping_fee', 'ShippingsMethod.status', 'ShippingsMethod.position', 'ShippingsMethodContent.name', 'ShippingsMethodContent.description', 'ShippingsMethodContent.lang'];
            break;
        }

        $where = ['ShippingsMethod.deleted' => 0];
        
        //contain        
        $contain = [
            'ShippingsMethodContent' => function ($q) use ($lang) {
                return $q->where([
                    'ShippingsMethodContent.lang' => $lang
                ]);
            }
        ];


        // filter by conditions  
        if(!empty($keyword)){
            $where['ShippingsMethodContent.search_unicode LIKE'] = '%' . Text::slug(strtolower($keyword), ' ') . '%';
        }

        if(!empty($ids)){
            $where['ShippingsMethod.id IN'] = $ids;
        }

        if(!empty($not_ids)){
            $where['ShippingsMethod.id NOT IN'] = $not_ids;
        }

        if(!is_null($status)){
            $where['ShippingsMethod.status'] = $status;
        }
      

        // sort by
        $sort_string = 'ShippingsMethod.id DESC';
        if(!empty($params[SORT])){
            switch($sort_field){
                case 'id':
                case 'shipping_method_id':
                    $sort_string = 'ShippingsMethod.id '. $sort_type;
                break;

                case 'name':
                    $sort_string = 'ShippingsMethodContent.name '. $sort_type .', ShippingsMethod.position DESC, ShippingsMethod.id DESC';
                break;

                case 'status':
                    $sort_string = 'ShippingsMethod.status '. $sort_type .', ShippingsMethod.position DESC, ShippingsMethod.id DESC';
                break;

                case 'position':
                    $sort_string = 'ShippingsMethod.position '. $sort_type .', ShippingsMethod.id DESC';
                break;
            }
        }

        return $table->find()->contain($contain)->where($where)->select($fields)->group('ShippingsMethod.id')->order($sort_string);
    }

    public function getDetailShippingMethod($id = null, $lang = null, $params = [])
    {
        $result = [];
        if(empty($id) || empty($lang)) return [];

        $contain = [
            'ShippingsMethodContent' => function ($q) use ($lang) {
                return $q->where([
                    'ShippingsMethodContent.lang' => $lang
                ]);
            }
        ];
        
        $result = TableRegistry::get('ShippingsMethod')->find()->contain($contain)->where([
            'ShippingsMethod.id' => $id,
            'ShippingsMethod.deleted' => 0,
        ])->first();

        return $result;
    }

    public function formatDataShippingMethodDetail($data = [], $lang = null)
    {
        if(empty($data) || empty($lang)) return [];

        $result = [
            'id' => !empty($data['id']) ? intval($data['id']) : null,
            'general_shipping_fee' => !empty($data['general_shipping_fee']) ? intval($data['general_shipping_fee']) : null,
            'type_fee' => !empty($data['type_fee']) ? $data['type_fee'] : null,
            'custom_config' => !empty($data['custom_config']) ? json_decode($data['custom_config'], true) : [],
            'position' => !empty($data['position']) ? intval($data['position']) : null,
            'status' => isset($data['status']) ? intval($data['status']) : null,
            
            'name' => !empty($data['ShippingsMethodContent']['name']) ? $data['ShippingsMethodContent']['name'] : null,
            'description' => !empty($data['ShippingsMethodContent']['description']) ? $data['ShippingsMethodContent']['description'] : null,
            'content' => !empty($data['ShippingsMethodContent']['content']) ? $data['ShippingsMethodContent']['content'] : null,
            'lang' => !empty($data['ShippingsMethodContent']['lang']) ? $data['ShippingsMethodContent']['lang'] : null,
        ];    

        return $result;
    }
}