<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;

class ShippingsDistrictTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('shippings_district');

        $this->setPrimaryKey('id');
    }

    public function getDistrictInfo($district_id = null, $carrier_code = null)
    {
        if(empty($district_id) || empty($carrier_code)) return [];

        $result = TableRegistry::get('ShippingsDistrict')->find()->where([
            'ShippingsDistrict.district_id' => $district_id,
            'ShippingsDistrict.carrier' => $carrier_code,
        ])->first();

        return !empty($result) ? $result : [];
    }
}