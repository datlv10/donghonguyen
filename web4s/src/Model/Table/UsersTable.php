<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use App\Model\Behavior\UnixTimestampBehavior;
use Cake\Utility\Text;
use Cake\Utility\Hash;

class UsersTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('users');
        $this->setPrimaryKey('id');
        $this->setDisplayField('id');

        $this->addBehavior('UnixTimestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                    'updated' => 'always'
                ]
            ]
        ]);

        $this->belongsTo('Roles', [
            'className' => 'Publishing.Roles',
            'foreignKey' => 'role_id',
            'propertyName' => 'role'
        ]);

    }

    public function findAuth(\Cake\ORM\Query $query, array $options)
    {
        $query->select(['id',  'username', 'password', 'role_id', 'full_name', 'image_avatar', 'email', 'phone', 'birthday', 'address', 'language_admin'])->where([
            'Users.status' => 1,
            'Users.deleted' => 0
        ]);
        return $query;
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('username')
            ->maxLength('username', 255)
            ->requirePresence('username', 'create')
            ->notEmptyString('username');

        $validator
            ->scalar('password')
            ->maxLength('password', 255)
            ->requirePresence('password', 'create')
            ->notEmptyString('password');

        return $validator;
    }

    public function queryListUsers($params = []) 
    {

        $table = TableRegistry::get('Users');

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
        $role_id = isset($filter['role_id']) && $filter['role_id'] != '' ? intval($filter['role_id']) : null;

        // fields select
        switch($field){
            case FULL_INFO:
                $fields = ['Users.id', 'Users.username', 'Users.image_avatar', 'Users.full_name', 'Users.email', 'Users.phone', 'Users.address', 'Users.birthday', 'Users.status', 'Users.created', 'Users.updated', 'Users.language_admin' , 'Roles.id', 'Roles.name'];
            break;

            case LIST_INFO:
                $fields = ['Users.id', 'Users.full_name' ];
            break;

            case SIMPLE_INFO:
            default:
                $fields = ['Users.id', 'Users.username', 'Users.image_avatar', 'Users.full_name', 'Users.email', 'Users.phone', 'Users.address', 'Users.birthday', 'Users.status', 'Users.language_admin','Roles.id', 'Roles.name'];
            break;
        }

        $sort_string = 'Users.id DESC';
        if(!empty($params[SORT])){
            switch($sort_field){
                case 'id':
                    $sort_string = 'Users.id '. $sort_type;
                break;

                case 'full_name':
                    $sort_string = 'Users.full_name '. $sort_type .', Users.id DESC';
                break;

                case 'role_id':
                    $sort_string = 'Users.role_id '. $sort_type .', Users.id DESC';
                break;

                case 'email':
                    $sort_string = 'Users.email '. $sort_type .', Users.id DESC';
                break;

                case 'phone':
                    $sort_string = 'Users.phone '. $sort_type .', Users.id DESC';
                break;

                case 'status':
                    $sort_string = 'Users.status '. $sort_type .', Users.id DESC';
                break;          
            }
        }

        // filter by conditions
        $where = ['Users.deleted' => 0];  

        if(!is_null($status)){
            $where['Users.status'] = $status;
        }

        if(!is_null($role_id)){
            $where['Users.role_id'] = $role_id;
        }

        if(!empty($keyword)){
            $where['Users.search_unicode LIKE'] = '%' . Text::slug(strtolower($keyword), ' ') . '%';
        }

        $contain = ['Roles'];

        return $table->find()->contain($contain)->where($where)->select($fields)->group('Users.id')->order($sort_string);
    }

    public function getDetailUsers($id = null, $params = [])
    {
        $result = [];

        $get_role = !empty($params['get_role']) ? true : false;
        $table = TableRegistry::get('Users');

        $contain = [];

        if($get_role){
            $contain[] = 'Roles';
        }

        $result = $table->find()->contain($contain)
        ->where([
            'Users.id' => $id,
            'Users.deleted' => 0
        ])->first();
        return $result;
    }

    public function checkExistEmail($email = null, $id = null)
    {
        if(empty($email)) return false;

        $where = [
            'deleted'   => 0,
            'email'  => $email,
        ];

        if(!empty($id)){
            $where['id !='] = $id;
        }

        $user = TableRegistry::get('Users')->find()->where($where)->first();
        return !empty($user->id) ? true : false;
    }

    public function checkExistUsername($username = null, $id = null)
    {
        if(empty($username)) return false;

        $where = [
            'deleted'   => 0,
            'username'  => $username,
        ];

        if(!empty($id)){
            $where['id !='] = $id;
        }

        $user = TableRegistry::get('Users')->find()->where($where)->first();
        return !empty($user->id) ? true : false;
    }

    public function formatDataUserDetail($data = [])
    {
        if(empty($data)) return [];

        $result = [
            'id' => !empty($data['id']) ? intval($data['id']) : null,
            'username' => !empty($data['username']) ? $data['username'] : null,
            'full_name' => !empty($data['full_name']) ? $data['full_name'] : null,
            'email' => !empty($data['email']) ? $data['email'] : null,
            'status' => !empty($data['status']) ? 1 : 0,
            'birthday' => !empty($data['birthday']) ? $data['birthday'] : null,
            'phone' => !empty($data['phone']) ? $data['phone'] : null,
            'image_avatar' => !empty($data['image_avatar']) ? $data['image_avatar'] : null,
            'address' => !empty($data['address']) ? $data['address'] : null,
            'language_admin' => !empty($data['language_admin']) ? $data['language_admin'] : null,
            'created' => !empty($data['created']) ? $data['created'] : null,
            'updated' => !empty($data['updated']) ? $data['updated'] : null,
            'role_id' => !empty($data['role_id']) ? intval($data['role_id']) : null,
        ];

        if(!empty($data['role_id'])){
            $roles_info = TableRegistry::get('Roles')->find()->where(['deleted' => 0])->select(['id', 'name'])->toArray();
            $roles = Hash::combine($roles_info, '{n}.id', '{n}.name');

            if(!empty($roles[$data['role_id']])){
                $result['role_name'] = $roles[$data['role_id']];
            }
        }

        return $result;
    }

}