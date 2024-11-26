<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use App\Model\Behavior\UnixTimestampBehavior;
use Cake\Utility\Text;
use Cake\Utility\Hash;
use Cake\Cache\Cache;
use Cake\Core\Configure;

class GenealogiesTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('genealogies');

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

    public function queryListGenealogies($params = []) 
    {
        $table = TableRegistry::get('Genealogies');

        // get info params
        $field = !empty($params[FIELD]) ? $params[FIELD] : SIMPLE_INFO;

        // sort
        $sort = !empty($params[SORT]) ? $params[SORT] : [];
        $sort_field = !empty($sort[FIELD]) ? $sort[FIELD] : null;
        $sort_type = !empty($sort[SORT]) ? $sort[SORT] : DESC;

        // filter
        $filter = !empty($params[FILTER]) ? $params[FILTER] : [];
        $keyword = !empty($filter['keyword']) ? trim($filter['keyword']) : null;
        $status = isset($filter['status']) && $filter['status'] != '' ? intval($filter['status']) : null;
        $generation = !empty($filter['generation']) && $filter['generation'] != '' ? intval($filter['generation']) : null;
        $genealogical = isset($filter['genealogical']) && $filter['genealogical'] != '' ? intval($filter['genealogical']) : null;
        $sex = !empty($filter['sex']) ? trim($filter['sex']) : null;

        $city_id = !empty($filter['city_id']) ? intval($filter['city_id']) : null;
        $district_id = !empty($filter['district_id']) ? intval($filter['district_id']) : null;

        $birthday_from = !empty($filter['birthday_from']) ? intval($filter['birthday_from']) : null;
        $birthday_to = !empty($filter['birthday_to']) ? intval($filter['birthday_to']) : null;

        $age_from = !empty($filter['age_from']) ? intval($filter['age_from']) : null;
        $age_to = !empty($filter['age_to']) ? intval($filter['age_to']) : null;

        // fields select
        switch($field){
            case FULL_INFO:
            case SIMPLE_INFO:
                $fields = ['Genealogies.id', 'Genealogies.full_name', 'Genealogies.self_name', 'Genealogies.education_level', 'Genealogies.genealogical', 'Genealogies.sex', 'Genealogies.image_avatar', 'Genealogies.generation', 'Genealogies.description', 'Genealogies.content', 'Genealogies.relationship', 'Genealogies.relationship_info', 'Genealogies.relationship_position', 'Genealogies.path_id', 'Genealogies.year_of_birth', 'Genealogies.year_of_death', 'Genealogies.burial', 'Genealogies.city_id', 'Genealogies.district_id', 'Genealogies.status'];
            break;

            case LIST_INFO:
                $fields = ['Genealogies.id', 'Genealogies.full_name'];
            break;
        }

        $where = [
            'Genealogies.deleted' => 0
        ];

        // filter by conditions  
        if(!empty($keyword)){
            $where['Genealogies.search_unicode LIKE'] = '%' . Text::slug(strtolower($keyword), ' ') . '%';
        }

        if(!is_null($status)){
            $where['Genealogies.status'] = $status;
        }

        if(!is_null($genealogical)){
            $where['Genealogies.genealogical'] = $genealogical;
        }

        if(!empty($ids)){
            $where['Genealogies.id IN'] = $ids;
        }

        if(!empty($generation)){
            $where['Genealogies.generation'] = $generation;
        }

        if(!empty($sex)){
            $where['Genealogies.sex'] = $sex;
        }

        if(!empty($city_id)){
            $where['Genealogies.city_id'] = $city_id;
        }

        if(!empty($district_id)){
            $where['Genealogies.district_id'] = $district_id;
        }

        if(!empty($birthday_from)){
            $where['Genealogies.birthday >='] = $birthday_from;

            if(!empty($age_from)){
                $where['OR']['Genealogies.age >='] = $age_from;
            }
        }

        if(!empty($birthday_to)){
            $where['Genealogies.birthday <='] = $birthday_to;

            if(!empty($age_to)){
                $where['OR']['Genealogies.age <='] = $age_to;
            }
        }

        // sort by
        $sort_string = 'Genealogies.id DESC';
        if(!empty($params[SORT])){
            switch($sort_field){
                case 'generation':
                    $sort_string = 'Genealogies.generation '. $sort_type . ', Genealogies.relationship_position ASC';
                break;     
            }
        }

        return $this->find()->where($where)->select($fields)->group('Genealogies.id')->order($sort_string);
    }

    public function getListTreeGenealogies($params = [])
    {
        $where = [
            'Genealogies.deleted' => 0,
            'Genealogies.genealogical' => 1
        ];

        $genealogies = $this->find()->where($where)->select()->all()->nest('id', 'relationship_info')->toArray();
        if (empty($genealogies)) return [];

        $result = $this->formatDataGenealogiesTree($genealogies);

        return $result;
    }

    public function getListHusband($id = null)
    {
        if (empty($id)) return [];

        $where = [
            'Genealogies.deleted' => 0,
            'Genealogies.relationship' => 1,
            'Genealogies.relationship_info' => $id
        ];

        return $this->find()->where($where)->select()->toArray();
    }

    public function getListWife($id = null)
    {
        if (empty($id)) return [];

        $where = [
            'Genealogies.deleted' => 0,
            'Genealogies.relationship' => 2,
            'Genealogies.relationship_info' => $id
        ];

        return $this->find()->where($where)->select()->toArray();
    }

    public function getListChild($id = null)
    {
        if (empty($id)) return [];

        $where = [
            'Genealogies.deleted' => 0,
            'Genealogies.relationship' => 3,
            'Genealogies.relationship_info' => $id
        ];

        return $this->find()->where($where)->select()->toArray();
    }

    public function romanNumerals($num) { 
        $n = intval($num); 
        $res = ''; 

        /*** roman_numerals array  ***/ 
        $roman_numerals = array( 
            'M'  => 1000, 
            'CM' => 900, 
            'D'  => 500, 
            'CD' => 400, 
            'C'  => 100, 
            'XC' => 90, 
            'L'  => 50, 
            'XL' => 40, 
            'X'  => 10, 
            'IX' => 9, 
            'V'  => 5, 
            'IV' => 4, 
            'I'  => 1); 

        foreach ($roman_numerals as $roman => $number){ 
            /*** divide to get  matches ***/ 
            $matches = intval($n / $number); 

            /*** assign the roman char * $matches ***/ 
            $res .= str_repeat($roman, $matches); 

            /*** substract from the number ***/ 
            $n = $n % $number; 
        } 

        /*** return the res ***/ 
        return $res; 
    } 

    public function formatDataGenealogiesTree($genealogies = [])
    {
        if (empty($genealogies)) return [];

        $result = [];
        foreach ($genealogies as $key => $genealogy) {
            $id = !empty($genealogy['id']) ? intval($genealogy['id']) : null;
            if (empty($id)) continue; 

            $item = [];

            $tree = !empty($genealogy['generation']) ? intval($genealogy['generation']) : 1;
            $full_name = !empty($genealogy['full_name']) ? trim($genealogy['full_name']) : '';
            $sex = !empty($genealogy['sex']) ? $genealogy['sex'] : '';
            $relationship = !empty($genealogy['relationship']) ? intval($genealogy['relationship']) : 0;
            $relationship_info = !empty($genealogy['relationship_info']) ? intval($genealogy['relationship_info']) : null;
            $relationship_position = !empty($genealogy['relationship_position']) ? intval($genealogy['relationship_position']) : 1;

            $tree_roman = $this->romanNumerals($tree);
            $name_format = $tree_roman . '.' . $relationship_position . '. ' . $full_name;

            $item = [
                'id' => $id,
                'tree' => $tree_roman,
                'sex' => $sex,
                'relationship_info' => $relationship_info,
                'position' => $relationship_position,
                'text' => $name_format
            ];

            if (!empty($genealogy['children'])) {
                $children = $this->formatDataGenealogiesTree($genealogy['children']);
                usort($children, fn($a, $b) => $a['position'] <=> $b['position']);

                $item['children'] = !empty($children) ? $children : [];
            }

            $result[] = $item;
        }

        return $result;
    }

    public function getDetailGenealogy($id = null)
    {
        $result = [];
        if(empty($id)) return [];        

        $where = [
            'Genealogies.id' => $id,
            'Genealogies.deleted' => 0,
        ];

        $result = $this->find()->where($where)->first();

        return $result;
    }

    public function getList()
    {
        $genealogies = $this->find()->where([
            'Genealogies.deleted' => 0
        ])->select(['Genealogies.id', 'Genealogies.full_name', 'Genealogies.generation', 'Genealogies.relationship_position'])->order(['Genealogies.generation ASC', 'Genealogies.relationship_position ASC'])->toArray();   

        if (empty($genealogies)) return [];

        $result = [];
        foreach ($genealogies as $key => $genealogy) {
            $id = !empty($genealogy['id']) ? intval($genealogy['id']) : null;
            if (empty($id)) continue; 

            $tree = !empty($genealogy['generation']) ? intval($genealogy['generation']) : 1;
            $full_name = !empty($genealogy['full_name']) ? trim($genealogy['full_name']) : '';
            $relationship_position = !empty($genealogy['relationship_position']) ? intval($genealogy['relationship_position']) : 1;

            $tree_roman = $this->romanNumerals($tree);
            $name_format = $tree_roman . '.' . $relationship_position . '. ' . $full_name;

            $result[$id] = $name_format;          
        }     

        return $result;
    }

    public function getGenerationById($genealogy_id = null)
    {
        $result = 1;
        if(empty($genealogy_id)) return result;        

        $where = [
            'Genealogies.id' => $genealogy_id,
            'Genealogies.deleted' => 0,
        ];

        $genealogy_info = $this->find()->where($where)->select(['id', 'generation'])->first();
        $result = !empty($genealogy_info['generation']) ? intval($genealogy_info['generation']) + 1 : 1;

        return $result;
    }
}