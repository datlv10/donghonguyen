<?php
declare(strict_types=1);

namespace Admin\View\Helper;

use Cake\View\Helper;

class CommentAdminHelper extends Helper
{   

    public function listStatus()
    {
        $result = [
            0 => __d('admin', 'khong_duyet'),
            1 => __d('admin', 'duyet'),
            2 => __d('admin', 'cho_duyet'),
        ];

        return $result;
    }

    public function typeComment()
    {
        $result = [
            COMMENT => __d('admin', 'binh_luan'),
            RATING => __d('admin', 'danh_gia'),
        ];

        return $result;
    }
}
