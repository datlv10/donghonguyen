<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use App\Model\Behavior\UnixTimestampBehavior;
use Cake\Utility\Text;

class ContactsTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('contacts');

        $this->setPrimaryKey('id');

        $this->addBehavior('UnixTimestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                    'updated' => 'existing'
                ]
            ]
        ]);

        $this->belongsTo('ContactsForm', [
            'className' => 'Publishing.ContactsForm',
            'foreignKey' => 'form_id',
            'joinType' => 'LEFT',
            'propertyName' => 'ContactsForm'
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        return $validator;
    }

    public function queryListContacts($params = []) 
    {
        $get_form = !empty($params['get_form']) ? true : false;

        // get info params
        $field = !empty($params[FIELD]) ? $params[FIELD] : SIMPLE_INFO;

        // sort
        $sort = !empty($params[SORT]) ? $params[SORT] : [];
        $sort_field = !empty($sort[FIELD]) ? $sort[FIELD] : null;
        $sort_type = !empty($sort[SORT]) && in_array($sort[SORT], [DESC, ASC]) ? $sort[SORT] : DESC;

        // filter
        $filter = !empty($params[FILTER]) ? $params[FILTER] : [];
        $keyword = !empty($filter['keyword']) ? trim($filter['keyword']) : null;
        $status = isset($filter['status']) && $filter['status'] != '' ? intval($filter['status']) : null;
        $form_id = !empty($filter['form_id']) ? intval($filter['form_id']) : null;        
        $create_from = !empty($filter['create_from']) ? strtotime(date('Y-m-d 00:00:00', strtotime(str_replace('/', '-', $filter['create_from'])))) : null;
        $create_to = !empty($filter['create_to']) ? strtotime(date('Y-m-d 23:59:59', strtotime(str_replace('/', '-', $filter['create_to'])))) : null;

        // fields select
        $fields = ['Contacts.id', 'Contacts.form_id', 'Contacts.value', 'Contacts.created', 'Contacts.status'];

        $sort_string = 'Contacts.id DESC';
        if(!empty($params[SORT])){
            switch($sort_field){
                case 'id':
                case 'contact_id':
                    $sort_string = 'Contacts.id '. $sort_type;
                break;

                case 'status':
                    $sort_string = 'Contacts.status '. $sort_type .', Contacts.id DESC';
                break;

                case 'created':
                    $sort_string = 'Contacts.created '. $sort_type .', Contacts.id DESC';
                break;           
            }
        }

        // filter by conditions
        $where = [];    

        if(!empty($keyword)){
            $where['Contacts.search_unicode LIKE'] = '%' . Text::slug(strtolower($keyword), ' ') . '%';
        }

        if(!is_null($status)){
            $where['Contacts.status'] = $status;
        }

        if(!empty($form_id)){
            $where['Contacts.form_id'] = $form_id;
        }

        if(!empty($create_from)){
            $where['Contacts.created >='] = $create_from;
        }

        if(!empty($create_to)){
            $where['Contacts.created <='] = $create_to;
        }

        $contain = [];
        if($get_form){
            $fields[] = 'ContactsForm.id';
            $fields[] = 'ContactsForm.code';
            $fields[] = 'ContactsForm.name';
            $fields[] = 'ContactsForm.fields';
            $fields[] = 'ContactsForm.send_email';
            $fields[] = 'ContactsForm.template_email_code';

            $contain[] = 'ContactsForm';
        }

        return TableRegistry::get('Contacts')->find()->contain($contain)->where($where)->select($fields)->order($sort_string);
    }

    public function getDetailContact($id = null, $params = [])
    {
        $result = [];
        if(empty($id)) return [];      

        $get_form = !empty($params['get_form']) ? true : false;  

        $where = [
            'Contacts.id' => $id,
            'Contacts.deleted' => 0
        ];

        $contain = [];

        if($get_form){
            $contain[] = 'ContactsForm';
        }

        $result = TableRegistry::get('Contacts')->find()->contain($contain)->where($where)->first();

        return $result;
    }

    public function formatDataContactDetail($data = [])
    {
        if(empty($data)) return [];

        $result = [
            'id' => !empty($data['id']) ? intval($data['id']) : null,
            'form_id' => !empty($data['form_id']) ? intval($data['form_id']) : null,
            'value' => !empty($data['value']) ? json_decode($data['value'], true) : [],
            'created' => !empty($data['created']) ? date('H:i - d/m/Y', $data['created']) : null,
            'status' => !empty($data['status']) ? intval($data['status']) : 1,
        ];

        if(!empty($data['ContactsForm'])){
            $form_info = $data['ContactsForm'];
            $result['form'] = [
                'id' => !empty($form_info['id']) ? intval($form_info['id']) : null,
                'code' => !empty($form_info['code']) ? $form_info['code'] : null,
                'send_email' => !empty($form_info['send_email']) ? 1 : 0,
                'template_email_code' => !empty($form_info['template_email_code']) ? intval($form_info['template_email_code']) : null,
                'name' => !empty($form_info['name']) ? $form_info['name'] : null,
                'fields' => !empty($form_info['fields']) ? json_decode($form_info['fields'], true) : [],
                'created' => !empty($data['created']) ? $data['created'] : null,
            ];
        }

        return $result;
    }
}