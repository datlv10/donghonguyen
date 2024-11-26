<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;

class ShippingsWardTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('shippings_ward');

        $this->setPrimaryKey('id');
    }

    public function getWardInfo($ward_id = null, $carrier_code = null)
    {
        if(empty($ward_id) || empty($carrier_code)) return [];

        $result = TableRegistry::get('ShippingsWard')->find()->where([
            'ShippingsWard.ward_id' => $ward_id,
            'ShippingsWard.carrier' => $carrier_code,
        ])->first();

        return !empty($result) ? $result : [];
    }
}