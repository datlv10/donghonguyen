<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration53 extends AbstractMigration
{

    /**
     * 
     * - thêm bảng `logs`
     */

    public function up()
    {
        if (!$this->hasTable('logs')) {
            $query = "
                CREATE TABLE `logs`  (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `action` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'add, update, update_status, delete, rollback',
                    `type` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'data, template',
                    `sub_type` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'article, product, brand, block, template',
                    `record_id` int(11) NULL DEFAULT NULL,
                    `user_id` int(11) NULL DEFAULT NULL COMMENT 'ID tài khoản',
                    `description` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Mô tả ngắn',
                    `link` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Đường dẫn bản ghi',
                    `path_file` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                    `path_log` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                    `created` int(11) NULL DEFAULT NULL,
                    PRIMARY KEY (`id`) USING BTREE,
                    INDEX `action`(`action`) USING BTREE,
                    INDEX `user_id`(`user_id`) USING BTREE,
                    INDEX `created`(`created`) USING BTREE,
                    INDEX `user_id_2`(`user_id`, `created`) USING BTREE,
                    INDEX `action_2`(`action`, `user_id`) USING BTREE
                ) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;";
            $this->execute($query);
        }
    }

    public function down()
    {
   
    }
}
