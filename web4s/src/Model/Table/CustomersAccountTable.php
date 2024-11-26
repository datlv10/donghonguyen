<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use App\Model\Behavior\UnixTimestampBehavior;
use Cake\Core\Exception\Exception;

class CustomersAccountTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('customers_account');
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

        $this->belongsTo('Customers', [
            'className' => 'Publishing.Customers',
            'foreignKey' => 'customer_id',
            'joinType' => 'LEFT',
            'propertyName' => 'Customers'
        ]);

        $this->hasOne('Customer', [
            'className' => 'Publishing.Customers',
            'foreignKey' => 'id',
            'bindingKey' => 'customer_id',
            'joinType' => 'INNER',
            'propertyName' => 'Customer'
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('username')
            ->minLength('username', 6)
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

    public function checkExistUsername($username = null, $id = null)
    {
        if(empty($username)) return false;

        $where = [
            'CustomersAccount.deleted' => 0,
            'CustomersAccount.username' => $username,
            'Customer.deleted' => 0
        ];

        if(!empty($id)){
            $where['CustomersAccount.id !='] = $id;
        }

        $account = TableRegistry::get('CustomersAccount')->find()->contain(['Customer'])->where($where)->first();
        return !empty($account->id) ? true : false;
    }

    public function loginMember($username = null, $password = null)
    {
        if(empty($username) || empty($password)) return [];

        $where = [
            'CustomersAccount.deleted' => 0,
            'CustomersAccount.password' => $password,
            'Customer.deleted' => 0
        ];

        if(strpos($username, '@') > 0){
            $where['Customer.email'] = $username;
        }else{
            $where['CustomersAccount.username'] = $username;
        }

        $fields = ['CustomersAccount.id', 'CustomersAccount.customer_id', 'CustomersAccount.status', 'Customer.status', 'Customer.email'];

        $account = TableRegistry::get('CustomersAccount')->find()->contain(['Customer'])->where($where)->select($fields)->first();
        return $account;
    }

    public function loginSocial($social_id = null, $type = null)
    {
        if(empty($social_id) || empty($type) || !in_array($type, ['facebook', 'google'])) return [];

        $where = [];
        if($type == 'facebook'){
            $where = ['CustomersAccount.facebook_id' => $social_id];
        }

        if($type == 'google'){
            $where = ['CustomersAccount.google_id' => $social_id];
        }

        if($type == 'apple'){
            $where = ['CustomersAccount.apple_id' => $social_id];
        }

        if(empty($where)) return [];

        $where['CustomersAccount.deleted'] = 0;
        $where['Customer.deleted'] = 0;

        return TableRegistry::get('CustomersAccount')->find()->contain(['Customer'])->where($where)->select([
            'CustomersAccount.id',
            'CustomersAccount.customer_id',
            'CustomersAccount.status'
        ])->first();
    }

    public function updateSocialId($customer_id = null, $social_id = null, $type = null)
    {
        if(empty($customer_id) || empty($social_id) || empty($type)) return false;

        $table = TableRegistry::get('CustomersAccount');

        $account = $table->find()->where([
            'deleted' => 0,
            'customer_id' => $customer_id
        ])->select(['id'])->first();
        if(empty($account)) return false;

        $data_save = [];                   
        if ($type == 'facebook') {
            $data_save['facebook_id'] = $social_id;
        }

        if ($type == 'google') {
            $data_save['google_id'] = $social_id;
        }

        if ($type == 'apple') {
            $data_save['apple_id'] = $social_id;
        }

        if(empty($data_save)) return false;

        $entity = $table->patchEntity($account, $data_save);
        try{
            $update = $table->save($entity);
            if(empty($update->id)) return false;

        }catch (Exception $e) {
            return false;
        }

        return true;
    }
}