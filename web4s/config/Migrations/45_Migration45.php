<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration45 extends AbstractMigration
{

    /**
     * 
     * - thêm bảng `nh_notifications`
     */

    public function up()
    {
        if (!$this->hasTable('nh_notifications')) {
            $query = "
                CREATE TABLE `nh_notifications` (
                    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                    `type` varchar(20) DEFAULT NULL COMMENT 'upgrade, news, promotion, order, contact',
                    `group_notification` varchar(20) DEFAULT NULL COMMENT 'my_notification, general',
                    `title` varchar(255) DEFAULT NULL,
                    `link` varchar(255) DEFAULT NULL COMMENT 'Đường dẫn',
                    `created` int(11) DEFAULT NULL,
                    `crm_notification_id` int(11) DEFAULT NULL,
                    PRIMARY KEY (`id`) USING BTREE,
                    KEY `type_2` (`created`) USING BTREE
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;";
            $this->execute($query);
        }
    }

    public function down()
    {
   
    }
}
