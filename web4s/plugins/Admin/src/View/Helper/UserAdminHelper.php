<?php
declare(strict_types=1);

namespace Admin\View\Helper;

use Cake\View\Helper;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

class UserAdminHelper extends Helper
{   

    public function getListUser()
    {
        $list_user = Hash::combine(TableRegistry::get('Users')->queryListUsers()->toArray(), '{n}.id', '{n}.full_name');
        return $list_user;
    }
}
