<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Cache\Cache;

class MobileAppTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('mobile_app');
        $this->setPrimaryKey('id');
    }

    public function getMobileAppDefault()
    {
        $cache_key = MOBILE_APP . '_default';

        $result = Cache::read($cache_key);
        if(is_null($result)){
            $result = TableRegistry::get('MobileApp')->find()->where([
                'MobileApp.id IS NOT' => null
            ])->first();

            Cache::write($cache_key, !empty($result) ? $result : []);
        }       
        return $result;
    }
    
}