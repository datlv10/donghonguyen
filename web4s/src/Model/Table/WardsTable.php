<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Core\Configure;
use Cake\Utility\Hash;

class WardsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('wards');
        $this->setPrimaryKey('id');
    }

    public function queryListWards($params = [])
    {
    	$table = TableRegistry::get('Wards');

    	// get info params
        $fields = !empty($params[FIELD]) ? $params[FIELD] : SIMPLE_INFO;

        // sort
        $sort = !empty($params[SORT]) ? $params[SORT] : [];
        $sort_field = !empty($sort[FIELD]) ? $sort[FIELD] : null;
        $sort_type = !empty($sort[SORT]) && in_array($sort[SORT], [DESC, ASC]) ? $sort[SORT] : DESC;

        // filter
        $filter = !empty($params[FILTER]) ? $params[FILTER] : [];

        $keyword = !empty($filter['keyword']) ? trim($filter['keyword']) : null;        
        $district_id = !empty($filter['district_id']) ? intval($filter['district_id']) : null;

    	switch ($fields) {
    		case FULL_INFO:
    			$fields = ['Wards.id', 'Wards.name', 'Wards.type', 'Wards.location', 'Wards.district_id', 'Wards.position', 'Wards.status'];
    		break;

    		case LIST_INFO:
    			$fields = ['Wards.id', 'Wards.name'];
    		break;

    		case SIMPLE_INFO:
    		default:
    			$fields = ['Wards.id', 'Wards.name', 'Wards.location', 'Wards.position', 'Wards.district_id', 'Wards.status'];
    		break;
    	}

        $sort_string = 'Wards.id DESC';
        if(!empty($params[SORT])){
            switch($sort_field){
                case 'id':
                    $sort_string = 'Wards.id '. $sort_type;
                break;

                case 'name':
                    $sort_string = 'Wards.name '. $sort_type .', Wards.position DESC';
                break;

                case 'city_id':
                    $sort_string = 'Wards.city_id '. $sort_type .', Wards.position DESC';
                break;             
            }
        }

        $where = ['Wards.deleted' => 0];

        if(!empty($keyword)){
            $where['Wards.name LIKE'] = '%' . $keyword . '%';
        }
        
        if (!empty($district_id)) {
            $where['Wards.district_id'] = $district_id;
        }

    	return $table->find()->where($where)->select($fields);

    }

    public function getListWard($district_id = null){
        if(empty($district_id)) return [];

        $wards = TableRegistry::get('Wards')->find()->where([
            'Wards.status' => 1,
            'Wards.deleted' => 0,
            'Wards.district_id' => $district_id,
        ])->select(['Wards.id', 'Wards.name'])->toArray();            
        $result = Hash::combine($wards, '{n}.id', '{n}.name');

        return !empty($result) ? $result : null;

    }
}