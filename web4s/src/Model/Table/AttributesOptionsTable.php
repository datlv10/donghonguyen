<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Cache\Cache;

class AttributesOptionsTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('attributes_options');

        $this->setPrimaryKey('id');

        $this->hasOne('AttributesOptionsContent', [
            'className' => 'Publishing.AttributesOptionsContent',
            'foreignKey' => 'attribute_option_id',
            'propertyName' => 'AttributesOptionsContent'
        ]);

        $this->hasMany('ContentMutiple', [
            'className' => 'Publishing.AttributesOptionsContent',
            'foreignKey' => 'attribute_option_id',
            'joinType' => 'LEFT',
            'propertyName' => 'ContentMutiple'
        ]);

        $this->hasMany('ContentMutipleOption', [
            'className' => 'Publishing.AttributesOptionsContent',
            'foreignKey' => 'attribute_option_id',
            'joinType' => 'LEFT',
            'propertyName' => 'ContentMutipleOption'
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('code')
            ->maxLength('code', 20)
            ->requirePresence('code')
            ->notEmptyString('code');

        return $validator;
    }

    public function queryListAttributesOptions($params = []) 
    {
        $table = TableRegistry::get('AttributesOptions');

        // filter
        $filter = !empty($params[FILTER]) ? $params[FILTER] : [];
        $lang = !empty($filter[LANG]) ? $filter[LANG] : TableRegistry::get('Languages')->getDefaultLanguage();        
        $keyword = !empty($filter['keyword']) ? trim($filter['keyword']) : null;
        $attribute_id = !empty($filter['attribute_id']) ? $filter['attribute_id'] : null;

        $fields = ['AttributesOptions.id', 'AttributesOptions.attribute_id', 'AttributesOptions.code', 'AttributesOptions.position', 'AttributesOptionsContent.name', 'AttributesOptionsContent.lang'];

        //contain
        $contain = [
            'AttributesOptionsContent' => function ($q) use ($lang) {
                return $q->where([
                    'AttributesOptionsContent.lang' => $lang
                ]);
            }
        ];

        $sort_string = 'AttributesOptions.id DESC';       

        // filter by conditions
        $where = ['AttributesOptions.deleted' => 0];

        if(!empty($attribute_id)){
            $where['AttributesOptions.attribute_id'] = $attribute_id;
        }
        if(!empty($keyword)){
            $where['OR'] = [
                'AttributesOptionsContent.name LIKE' => '%' . $keyword . '%',
                'AttributesOptions.code LIKE' => '%' . $keyword . '%'
            ];
        }

        return $table->find()->contain($contain)->where($where)->select($fields)->group('AttributesOptions.id')->order($sort_string);
    }

    public function getAll($lang = null)
    {
        if(empty($lang)) return [];

        $cache_key = ATTRIBUTE_OPTION . '_all_' . $lang;
        $result = Cache::read($cache_key);
        if(is_null($result)){
            $table = TableRegistry::get('AttributesOptions');

            $fields = ['AttributesOptions.id', 'AttributesOptions.attribute_id','AttributesOptions.code', 'AttributesOptionsContent.name'];
            $contain = [
                'AttributesOptionsContent' => function ($q) use ($lang) {
                    return $q->where([
                        'AttributesOptionsContent.lang' => $lang
                    ]);
                }
            ];
            $options = $table->find()->contain($contain)->where(['AttributesOptions.deleted' => 0])->select($fields)->order('AttributesOptions.position DESC, AttributesOptions.id ASC')->toArray();
            
            $result = [];
            if(!empty($options)){
                foreach ($options as $key => $option) {
                    $option_id = !empty($option['id']) ? intval($option['id']) : null;
                    if(empty($option_id)) continue;

                    $result[$option_id] = [
                        'id' => $option_id,
                        'attribute_id' => !empty($option['attribute_id']) ? intval($option['attribute_id']) : null,
                        'code' => !empty($option['code']) ? $option['code'] : null,
                        'name' => !empty($option['AttributesOptionsContent']['name']) ? $option['AttributesOptionsContent']['name'] : null
                    ];
                }
            }            
            Cache::write($cache_key, $result);
        }
        
        return $result;
    }

    public function checkExistOptionByName($attribute_id = null, $name = null, $lang = null) 
    {
        if(empty($attribute_id) || empty($name) || empty($lang)) return false;

        $result = $this->find()->contain(['AttributesOptionsContent'])->where([
            'AttributesOptions.deleted' => 0,
            'AttributesOptionsContent.name' => $name,
            'AttributesOptionsContent.lang' => $lang,
        ])->select(['id'])->first();
        
        return !empty($result) ? true : false;

    }

}