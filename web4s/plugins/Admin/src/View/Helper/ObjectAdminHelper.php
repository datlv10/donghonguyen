<?php
declare(strict_types=1);

namespace Admin\View\Helper;

use Cake\View\Helper;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

class ObjectAdminHelper extends Helper
{   
    public function getListOrderSource()
    {
        $list_source = Hash::combine(TableRegistry::get('Objects')->find()->where([
            'type' => ORDER_SOURCE,
            'deleted' => 0
        ])->order('is_default DESC')->toArray(), '{n}.code', '{n}.name');

        $list_source = !empty($list_source) ? $list_source : [];
        $list_source[WEBSITE] = __d('admin', 'website');
        
        return $list_source;
    }
}