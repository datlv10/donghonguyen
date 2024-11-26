<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use App\Model\Behavior\UnixTimestampBehavior;
use Cake\Utility\Text;

class ObjectsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('objects');
        $this->setPrimaryKey('id');
    }

    public function queryListObjects($params = [])
    {
        $table = TableRegistry::get('Objects');

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
    		case LIST_INFO:
    			$fields = ['Objects.id', 'Objects.name'];
    		break;

            case FULL_INFO:
    		case SIMPLE_INFO:
    		default:
    			$fields = ['Objects.id', 'Objects.type', 'Objects.code', 'Objects.name', 'Objects.is_system', 'Objects.is_default', 'Objects.deleted'];
    		break;
    	}

        $sort_string = 'Objects.id DESC';
        if(!empty($params[SORT])){
            switch($sort_field){
                case 'id':
                    $sort_string = 'Objects.id '. $sort_type;
                break;

                case 'name':
                    $sort_string = 'Objects.name '. $sort_type .', Objects.id DESC';
                break;

                case 'code':
                    $sort_string = 'Objects.code '. $sort_type .', Objects.id DESC';
                break;             
            }
        }

        $where = ['Objects.deleted' => 0];

        if(!empty($keyword)){
            $where['Objects.name LIKE'] = '%' . $keyword . '%';
        }

    	return $table->find()->where($where)->select($fields);
    }
}