<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;

class ShippingsCityTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('shippings_city');

        $this->setPrimaryKey('id');
    }

    public function getCityInfo($city_id = null, $carrier_code = null)
    {
        if(empty($city_id) || empty($carrier_code)) return [];

        $result = TableRegistry::get('ShippingsCity')->find()->where([
            'ShippingsCity.city_id' => $city_id,
            'ShippingsCity.carrier' => $carrier_code,
        ])->first();

        return !empty($result) ? $result : [];
    }
}