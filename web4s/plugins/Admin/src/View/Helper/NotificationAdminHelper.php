<?php
declare(strict_types=1);

namespace Admin\View\Helper;

use Cake\View\Helper;
class NotificationAdminHelper extends Helper
{    
    public function listPlatform()
    {
        return [
            'all' => __d('admin', 'tat_ca'),
            'web' => 'Website',
            'ios' => 'IOS',
            'android' => 'Android',
            'token' => 'Token'
        ];
    }

    public function listTypeNotification()
    {
        return [
            ALL => __d('admin', 'tat_ca'),
            WEBSITE => 'Website',
            MOBILE_APP => 'Mobile App',
        ];
    }
}
