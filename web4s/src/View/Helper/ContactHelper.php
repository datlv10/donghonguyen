<?php
declare(strict_types=1);

namespace App\View\Helper;

use Cake\View\Helper;
use Cake\ORM\TableRegistry;

class ContactHelper extends Helper
{
    /** Lấy chi tiết thông tin liên hệ trong gửi Email
     * 
     * $contact_id (*): ID của khách hàng(int)
     * $params['get_form']: lấy thông tin của form - ví dụ: 'true'| 'false'
     * 
     * {assign var = data value = $this->Contact->getDetailContact($id_record)}
     * 
    */
    public function getDetailContact($contact_id = null, $param = [])
    {
        if(empty($contact_id)) return [];

        $table = TableRegistry::get('Contacts');

        $contact = $table->getDetailContact($contact_id, $param);
        
        return $table->formatDataContactDetail($contact);
    }
}
