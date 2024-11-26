<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration38 extends AbstractMigration
{

    /**
     * 
     * - thêm bảng `notifications_sent`
     */

    public function up()
    {
        if (!$this->hasTable('notifications_sent')) {
            $query = "
                    CREATE TABLE `notifications_sent`  (
                      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                      `notification_id` int(11) NULL DEFAULT NULL,
                      `platform` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'all, web, ios, android, token',
                      `token` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Sử dụng khi thực thi với platform token',
                      `created` int(11) NULL DEFAULT NULL,
                      `created_by` int(11) NULL DEFAULT NULL,
                      PRIMARY KEY (`id`) USING BTREE,
                      INDEX `notification_id`(`notification_id`) USING BTREE,
                      INDEX `platform`(`platform`) USING BTREE
                    ) ENGINE = MyISAM AUTO_INCREMENT = 95 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
                    ";
            $this->execute($query);
        }
    }

    public function down()
    {
   
    }
}
