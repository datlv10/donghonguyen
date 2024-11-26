<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Core\Configure;
use Cake\Cache\Cache;
use Cake\Utility\Hash;

class CurrenciesTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('currencies');
        $this->setPrimaryKey('id');
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('name')
            ->maxLength('name', 255)
            ->requirePresence('name')
            ->notEmptyString('name');

        $validator
            ->scalar('code')
            ->maxLength('code', 20)
            ->requirePresence('code')
            ->notEmptyString('code');

        $validator
            ->scalar('unit')
            ->maxLength('unit', 20)
            ->requirePresence('unit')
            ->notEmptyString('unit');

        return $validator;
    }

    public function getDefaultCurrency()
    {
        $cache_key = CURRENCY_PARAM . '_default';
        $result = Cache::read($cache_key);

        if(is_null($result)){
            $fields = ['id', 'name', 'code', 'unit', 'exchange_rate'];
            $result = TableRegistry::get('Currencies')->find()->where([
                'status' => 1, 
                'is_default' => 1
            ])->select()->first();
            
            Cache::write($cache_key, !empty($result) ? $result : []);
        }        
        
        return $result;
    }

    public function getList()
    {
        $cache_key = CURRENCY_PARAM . '_list';
        $result = Cache::read($cache_key);

        if(is_null($result)){
            $currencies = TableRegistry::get('Currencies')->find()->where([
                'status' => 1
            ])
            ->select(['code', 'name'])
            ->order('is_default DESC, id ASC')
            ->toArray();

            $result = Hash::combine($currencies, '{n}.code', '{n}.name');
            
            Cache::write($cache_key, !empty($result) ? $result : []);
        }        
        
        return $result;
    }

    public function getAll()
    {
        $cache_key = CURRENCY_PARAM . '_all';
        $result = Cache::read($cache_key);

        if(is_null($result)){
            $currencies = TableRegistry::get('Currencies')->find()->order('is_default DESC, id ASC')->toArray();
            $result = Hash::combine($currencies, '{n}.code', '{n}');
            
            Cache::write($cache_key, !empty($result) ? $result : []);
        }        
        
        return $result;
    }

    public function queryListCurrencies($params = []) 
    {
        // filter by conditions                
        $filter = !empty($params[FILTER]) ? $params[FILTER] : [];

        $keyword = !empty($filter[KEYWORD]) ? trim($filter[KEYWORD]) : null;
        $status = isset($filter[STATUS]) ? intval($filter[STATUS]) : null;

        // field
        $field = !empty($params[FIELD]) ? $params[FIELD] : SIMPLE_INFO;

        switch($field){
            case FULL_INFO:
            case SIMPLE_INFO:
                $fields = ['Currencies.id', 'Currencies.name', 'Currencies.code', 'Currencies.unit', 'Currencies.exchange_rate', 'Currencies.is_default', 'Currencies.status'];
            break;

            case LIST_INFO:
                $fields = ['Currencies.code', 'Currencies.name'];
            break;
        }

        $where = [];
        if(!empty($keyword)){
            $where['Currencies.name LIKE'] = '%' . $keyword . '%';
        }

        if(!is_null($status)){
            $where['Currencies.status'] = $status;
        }

        return TableRegistry::get('Currencies')->find()->where($where)->select($fields);
    }
}