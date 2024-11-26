<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

class CustomersPointTickTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('customers_point_tick');
        $this->setPrimaryKey('id');       
    }

    public function getInfoAttendanceByMemberId($member_id = null, $limit = null)
    {
        if (empty($member_id)) return [];

        $result = TableRegistry::get('CustomersPointTick')->find()->where([
            'CustomersPointTick.member_id' => $member_id
        ])->select(['CustomersPointTick.tick_time', 'CustomersPointTick.id'])->order('CustomersPointTick.tick_time DESC')->limit($limit)->toArray();
        
        if (!empty($result)) {
            $result = Hash::combine($result, '{n}.tick_time', '{n}.id');
        }

        return !empty($result) ? $result : [];
    }

}