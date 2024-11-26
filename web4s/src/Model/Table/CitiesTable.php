<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Utility\Hash;
use Cake\Cache\Cache;

class CitiesTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('cities');
        $this->setPrimaryKey('id');
    }

    public function queryListCities($params = [])
    {
    	$table = TableRegistry::get('Cities');

        // get info params
    	$fields = !empty($params[FIELD]) ? $params[FIELD] : SIMPLE_INFO;

        // sort
        $sort = !empty($params[SORT]) ? $params[SORT] : [];
        $sort_field = !empty($sort[FIELD]) ? $sort[FIELD] : null;
        $sort_type = !empty($sort[SORT]) && in_array($sort[SORT], [DESC, ASC]) ? $sort[SORT] : DESC;

        // filter
        $filter = !empty($params[FILTER]) ? $params[FILTER] : [];

        $keyword = !empty($filter['keyword']) ? trim($filter['keyword']) : null;        
        $country_id = !empty($filter['country_id']) ? intval($filter['country_id']) : null;
        $ids = !empty($filter['ids']) && is_array($filter['ids']) ? $filter['ids'] : [];

    	switch ($fields) {
    		case FULL_INFO:
    			$fields = ['Cities.id', 'Cities.name', 'Cities.type', 'Cities.country_id', 'Cities.position', 'Cities.status'];
    		break;

    		case LIST_INFO:
    			$fields = ['Cities.id', 'Cities.name'];
    		break;

    		case SIMPLE_INFO:
    		default:
    			$fields = ['Cities.id', 'Cities.name', 'Cities.position', 'Cities.country_id', 'Cities.status'];
    		break;
    	}

        $sort_string = 'Cities.id DESC';
        if(!empty($params[SORT])){
            switch($sort_field){
                case 'id':
                    $sort_string = 'Cities.id '. $sort_type;
                break;

                case 'name':
                    $sort_string = 'Cities.name '. $sort_type .', Cities.position DESC';
                break;

                case 'coutry_id':
                    $sort_string = 'Cities.coutry_id '. $sort_type .', Cities.position DESC';
                break;             
            }
        }

        $where = ['Cities.deleted' => 0];

        if(!empty($keyword)){
            $where['Cities.name LIKE'] = '%' . $keyword . '%';
        }

        if (!empty($country_id)) {
            $where['Cities.country_id'] = $country_id;
        }

        if(!empty($ids)){
            $where['Cities.id IN'] = $ids;
        }

    	return $table->find()->where($where)->select($fields);
    }

    public function getListCity()
    {
        $cache_key = 'city_list';
        $result = Cache::read($cache_key);
        if(is_null($result)){
            $cities = TableRegistry::get('Cities')->find()->where([
                'Cities.status' => 1,
                'Cities.deleted' => 0,
                'Cities.country_id' => 1,
            ])->select(['Cities.id', 'Cities.name'])->toArray();            
            $result = Hash::combine($cities, '{n}.id', '{n}.name');

            Cache::write($cache_key, $result);
        }

        return $result;
    }
}