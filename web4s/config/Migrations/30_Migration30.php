<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration30 extends AbstractMigration
{

    /**
     * 
     * - thống kê truy cập
     * - thêm bảng `counters` bảng `log_access`
     */

    public function up()
    {

        if (!$this->hasTable('counters')) {
            $query = "
                    CREATE TABLE `counters` (
                        `id` int(11) NOT NULL,
                        `counts` int(11) DEFAULT NULL COMMENT 'số lượng truy cập theo ngày',
                        `date` date DEFAULT NULL
                    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=COMPRESSED;";
            $this->execute($query);
        }

        if (!$this->hasTable('log_access')) {
            $query = "
                    CREATE TABLE `log_access` (
                        `id` int(11) NOT NULL,
                        `ip` varchar(50) DEFAULT NULL COMMENT 'IP truy cập',
                        `time_access` datetime DEFAULT NULL,
                        `close_brower` int(1) DEFAULT 0,
                        `session_id` varchar(255) DEFAULT NULL,
                        `counts` int(11) DEFAULT NULL
                    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=COMPRESSED;";
            $this->execute($query);
        }
    }

    public function down()
    {
   
    }
}
