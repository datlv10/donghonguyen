<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use App\Model\Behavior\UnixTimestampBehavior;
use Cake\Core\Configure;
use Cake\Utility\Text;

class CustomersAddressTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('customers_address');
        $this->setPrimaryKey('id');

        $this->addBehavior('UnixTimestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                    'updated' => 'existing'
                ]
            ]
        ]);

        $this->hasOne('Customers', [
            'className' => 'Customers',
            'foreignKey' => 'id',
            'bindingKey' => 'customer_id',
            'joinType' => 'LEFT',
            'propertyName' => 'Customers'
        ]);
    }

    public function checkExistName($name = null, $customer_id = null, $id = null)
    {
        if(empty($name) || empty($customer_id)) return false;

        $where = [
            'name' => $name,
            'customer_id' => $customer_id,
        ];

        if(!empty($id)){
            $where['id !='] = $id;
        }

        $address = TableRegistry::get('CustomersAddress')->find()->where($where)->first();
        return !empty($address->id) ? true : false;
    }
}