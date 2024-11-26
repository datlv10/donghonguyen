<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Core\Configure;
use Cake\Utility\Hash;
use Cake\Cache\Cache;


class DistrictsTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('districts');
        $this->setPrimaryKey('id');
    }

    public function queryListDistricts($params = [])
    {
    	$table = TableRegistry::get('Districts');

    	// get info params
        $fields = !empty($params[FIELD]) ? $params[FIELD] : SIMPLE_INFO;

        // sort
        $sort = !empty($params[SORT]) ? $params[SORT] : [];
        $sort_field = !empty($sort[FIELD]) ? $sort[FIELD] : null;
        $sort_type = !empty($sort[SORT]) && in_array($sort[SORT], [DESC, ASC]) ? $sort[SORT] : DESC;

        // filter
        $filter = !empty($params[FILTER]) ? $params[FILTER] : [];

        $keyword = !empty($filter['keyword']) ? trim($filter['keyword']) : null;        
        $city_id = !empty($filter['city_id']) ? intval($filter['city_id']) : null;

    	switch ($fields) {
    		case FULL_INFO:
    			$fields = ['Districts.id', 'Districts.name', 'Districts.type', 'Districts.location', 'Districts.city_id', 'Districts.position', 'Districts.status'];
    		break;

    		case LIST_INFO:
    			$fields = ['Districts.id', 'Districts.name'];
    		break;

    		case SIMPLE_INFO:
    		default:
    			$fields = ['Districts.id', 'Districts.name', 'Districts.location', 'Districts.position', 'Districts.city_id', 'Districts.status'];
    		break;
    	}

        $sort_string = 'Districts.id DESC';
        if(!empty($params[SORT])){
            switch($sort_field){
                case 'id':
                    $sort_string = 'Districts.id '. $sort_type;
                break;

                case 'name':
                    $sort_string = 'Districts.name '. $sort_type .', Districts.position DESC';
                break;

                case 'city_id':
                    $sort_string = 'Districts.city_id '. $sort_type .', Districts.position DESC';
                break;             
            }
        }

    	$where = ['Districts.deleted' => 0];

        if(!empty($keyword)){
            $where['Districts.name LIKE'] = '%' . $keyword . '%';
        }
        
    	if (!empty($city_id)) {
            $where['Districts.city_id'] = $city_id;
        }

    	return $table->find()->where($where)->select($fields);
    }

    public function getListDistrict($city_id)
    {
        $cache_key = 'district_all';
        $all_district = Cache::read($cache_key);
        if(is_null($all_district)){
            $all_district = TableRegistry::get('Districts')->find()->where([
                'Districts.status' => 1,
                'Districts.deleted' => 0
            ])->select(['Districts.id', 'Districts.name', 'Districts.city_id'])->toArray();  
            $all_district = Hash::combine($all_district, '{n}.id', '{n}.name', '{n}.city_id');

            Cache::write($cache_key, $all_district);
        }

        return !empty($all_district[$city_id]) ? $all_district[$city_id] : [];
    }
}