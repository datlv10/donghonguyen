<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Cache\Cache;

class MobileTemplateTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('mobile_template');
        $this->setPrimaryKey('id');
    }

    public function getTemplateDefault()
    {
        $cache_key = MOBILE_TEMPLATE . '_default';

        $result = Cache::read($cache_key);
        if(is_null($result)){
            $result = TableRegistry::get('MobileTemplate')->find()->where([
                'MobileTemplate.is_default' => 1
            ])->first();

            Cache::write($cache_key, !empty($result) ? $result : []);
        }    	
    	return $result;
    }
    
}