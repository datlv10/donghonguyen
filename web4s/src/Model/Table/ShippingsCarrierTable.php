<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Cache\Cache;

class ShippingsCarrierTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('shippings_carrier');

        $this->setPrimaryKey('id');
    }

    public function getList()
    {
        $cache_key = SHIPPING_CARRIER . '_list';
        $result = Cache::read($cache_key);

        if(is_null($result)){
            $carriers = TableRegistry::get('ShippingsCarrier')->find()->where([
                'ShippingsCarrier.status' => 1
            ])->toArray();

            $result = [];
            if(!empty($carriers)){
                foreach ($carriers as $k => $carrier) {
                    $code = !empty($carrier['code']) ? $carrier['code'] : null;
                    if(empty($code)) continue;

                    $result[$code] = [
                        'id' => !empty($carrier['id']) ? intval($carrier['id']) : null,
                        'code' => $code,
                        'name' => !empty($carrier['name']) ? $carrier['name'] : null,
                        'config' => !empty($carrier['config']) ? json_decode($carrier['config'], true) : []
                    ];
                }
            }
            Cache::write($cache_key, $result);
        }

        return $result;
    }
}