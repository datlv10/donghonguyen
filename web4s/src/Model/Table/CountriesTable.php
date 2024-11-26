<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Core\Configure;

class CountriesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('countries');
        $this->setPrimaryKey('id');
    }

    public function queryListCountries($params = [])
    {
        // get info params
    	$fields = !empty($params[FIELD]) ? $params[FIELD] : SIMPLE_INFO;

        // sort
        $sort = !empty($params[SORT]) ? $params[SORT] : [];
        $sort_field = !empty($sort[FIELD]) ? $sort[FIELD] : null;
        $sort_type = !empty($sort[SORT]) && in_array($sort[SORT], [DESC, ASC]) ? $sort[SORT] : DESC;

        // filter
        $filter = !empty($params[FILTER]) ? $params[FILTER] : [];

        $keyword = !empty($filter['keyword']) ? trim($filter['keyword']) : null;        

    	switch ($fields) {
    		case FULL_INFO:
    			$fields = ['Countries.id', 'Countries.name', 'Countries.position', 'Countries.status'];
    		break;

    		case LIST_INFO:
    			$fields = ['Countries.id', 'Countries.name'];
    		break;

    		case SIMPLE_INFO:
    		default:
    			$fields = ['Countries.id', 'Countries.name', 'Countries.position', 'Countries.status'];
    		break;
    	}

        $sort_string = 'Countries.id DESC';
        if(!empty($params[SORT])){
            switch($sort_field){
                case 'id':
                    $sort_string = 'Countries.id '. $sort_type;
                break;

                case 'name':
                    $sort_string = 'Countries.name '. $sort_type .', Countries.position DESC';
                break;

                case 'coutry_id':
                    $sort_string = 'Countries.coutry_id '. $sort_type .', Countries.position DESC';
                break;             
            }
        }

        $where = ['Countries.deleted' => 0];

        if(!empty($keyword)){
            $where['Countries.name LIKE'] = '%' . $keyword . '%';
        }

    	return $this->find()->where($where)->select($fields);
    }
}