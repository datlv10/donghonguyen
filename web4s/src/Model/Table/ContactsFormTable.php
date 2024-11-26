<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use App\Model\Behavior\UnixTimestampBehavior;
use Cake\Utility\Text;

class ContactsFormTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('contacts_form');

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

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        return $validator;
    }

    public function queryListContactsForm($params = []) 
    {
        // get info params
        $field = !empty($params[FIELD]) ? $params[FIELD] : SIMPLE_INFO;      

        // sort
        $sort = !empty($params[SORT]) ? $params[SORT] : [];
        $sort_field = !empty($sort[FIELD]) ? $sort[FIELD] : null;
        $sort_type = !empty($sort[SORT]) && in_array($sort[SORT], [DESC, ASC]) ? $sort[SORT] : DESC;

        // filter
        $filter = !empty($params[FILTER]) ? $params[FILTER] : [];        
        $keyword = !empty($filter['keyword']) ? trim($filter['keyword']) : null;
        
        // fields select
        switch($field){
            case FULL_INFO:
                $fields = ['ContactsForm.id', 'ContactsForm.code', 'ContactsForm.send_email', 'ContactsForm.template_email_code', 'ContactsForm.name', 'ContactsForm.fields', 'ContactsForm.created', 'ContactsForm.updated', 'ContactsForm.created_by'];
            break;

            case LIST_INFO:
                $fields = ['ContactsForm.id', 'ContactsForm.name'];
            break;
            
            case SIMPLE_INFO:
            default:
                $fields = ['ContactsForm.id', 'ContactsForm.code', 'ContactsForm.send_email', 'ContactsForm.template_email_code', 'ContactsForm.name', 'ContactsForm.created', 'ContactsForm.updated', 'ContactsForm.created_by'];
            break;
        }

        $sort_string = 'ContactsForm.id DESC';
        if(!empty($params[SORT])){
            switch($sort_field){
                case 'id':
                case 'form_id':
                    $sort_string = 'ContactsForm.id '. $sort_type;
                break;

                case 'name':
                    $sort_string = 'ContactsForm.name '. $sort_type .', ContactsForm.id DESC';
                break;
      
            }
        }

        // filter by conditions
        $where = [
            'deleted' => 0
        ];
        
        if(!empty($keyword)){
            $where['ContactsForm.search_unicode LIKE'] = '%' . Text::slug(strtolower($keyword), ' ') . '%';
        }        

        return TableRegistry::get('ContactsForm')->find()->where($where)->select($fields)->group('ContactsForm.id')->order($sort_string);
    }
}