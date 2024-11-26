<?php
declare(strict_types=1);

namespace Admin\View\Helper;

use Cake\View\Helper;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\I18n\FrozenTime;

class NhNotificationAdminHelper extends Helper
{    
    public function getFirstPageNotifcation($group = null)
    {
        $result = TableRegistry::get('NhNotifications')->getFirstPageNotifcation($group);
        return $result;
    }

    public function existmorePageNotifcation($group = null)
    {
        $result = TableRegistry::get('NhNotifications')->existmorePageNotifcation($group);
        return $result;
    }

    public function getLastTimeNotification()
    {
        $result = TableRegistry::get('NhNotifications')->getLastTimeNotification();
        return $result;
    }

    public function parseTimeNotification($time = null)
    {
        $result = [
            'time' => '',
            'full_time' => ''
        ];

        if(empty($time)){
            return $result;
        }

        $time = date('Y-m-d H:i:s', $time);

        $time_input = new FrozenTime($time);
        $now = new FrozenTime();

        $interval = $now->diff($time_input);
        if (!empty($interval->format('%i'))) {
            $result['time'] = $interval->format('%i') . ' ' . __d('admin', 'phut_truoc');
        }

        if (!empty($interval->format('%h'))) {
            $result['time'] = $interval->format('%h') . ' ' . __d('admin', 'gio_truoc');
        }        

        if (!empty($interval->format('%d'))) {
            $result['time'] = $interval->format('%d') . ' ' . __d('admin', 'ngay_truoc');
            $result['full_time'] = date(("d \M\O\N\T\H m, Y \A\T H:i"), strtotime($time));
        }

        if (!empty($interval->format("%m"))) {
            $result['time'] = $interval->format("%m") . ' ' . __d('admin', 'thang_truoc');
            $result['full_time'] = date(("d \M\O\N\T\H m, Y \A\T H:i"), strtotime($time));
        }

        if (!empty($interval->format('%y'))) {
            $result['time'] = $interval->format('%y') . ' ' . __d('admin', 'nam_truoc');
            $result['full_time'] = date(("d \M\O\N\T\H m, Y \A\T H:i"), strtotime($time));
        }

        if (empty($result['time'])) {
            $result['time'] = __d('admin', 'vua_xong');
        }
        
        $result['full_time'] = str_replace('MONTH', __d('admin', 'thang'), trim($result['full_time']));
        $result['full_time'] = str_replace('AT', __d('admin', 'luc'), trim($result['full_time']));

        return $result;
    }

}
