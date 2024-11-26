<?php
declare(strict_types=1);

namespace App\View\Helper;

use Cake\View\Helper;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;
use Cake\I18n\Time;

class CounterHelper extends Helper
{
    /** Lấy thông tin thống kê truy cập
     * 
     * 
     * {assign var = data value = $this->Counter->statisticalAccess()}
     * 
    */
    public function statisticalAccess() 
    {
        $log_access_model = TableRegistry::get('LogAccess');
        $counter_model = TableRegistry::get('Counters');

        $result = [
            'online' => 1,
            'all' => 0,
            'day' => 0,
            'week' => 0,
            'month' => 0
        ];

        $day = $counter_model->getCounterDay();
        $result['day'] = !empty($day) ? $day : 1;
        
        $week = $counter_model->getCounterWeek();
        $result['week'] = !empty($week) ? $week : $result['day'];
        
        $month = $counter_model->getCounterMonth();
        $result['month'] = !empty($month) ? $month : $result['week'];

        $all = $counter_model->getCounterAll();
        $result['all'] = !empty($all) ? $all : $result['month'];

        $online = $log_access_model->getCounterOnline();
        $result['online'] = !empty($online) ? $online : 1;

        return $result;
    }
}
