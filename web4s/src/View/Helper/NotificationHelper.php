<?php
declare(strict_types=1);

namespace App\View\Helper;
use Cake\View\Helper;
use Cake\ORM\TableRegistry;

class NotificationHelper extends Helper
{
	/** Lấy khoảng thời gian kể từ lúc khởi tạo ví dụ: 1 giờ trước
     * 
     * $int_time: thời gian khởi tạo
     * 
     *  {assign var = time_format value = $this->Notification->formatTimeClient($int_time)}
    */
	public function formatTimeClient($int_time = null)
	{
		return TableRegistry::get('Notifications')->parseTimeComment($int_time);
	}
}
