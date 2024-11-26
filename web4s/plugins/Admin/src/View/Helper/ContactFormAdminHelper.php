<?php
declare(strict_types=1);

namespace Admin\View\Helper;

use Cake\View\Helper;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

class ContactFormAdminHelper extends Helper
{   
    public function getListForm()
    {
        $list_form = Hash::combine(TableRegistry::get('ContactsForm')->queryListContactsForm([
        	FIELD => LIST_INFO
        ])->toArray(), '{n}.id', '{n}.name');

        if(empty($list_form)){
            return [];
        }

        return $list_form;
    }
}