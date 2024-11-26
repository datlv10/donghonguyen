<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration24 extends AbstractMigration
{

    /**
     * 
     * thêm bảng `shippings_method` và `shippings_method_content`
     * 
     */

    public function up()
    {
        if (!$this->hasTable('shippings_method')) {
            $query = "
                    CREATE TABLE `shippings_method`  (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `general_shipping_fee` int(11) NULL DEFAULT NULL,
                        `type_fee` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                        `custom_config` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
                        `position` int(11) NULL DEFAULT NULL,
                        `status` int(1) NULL DEFAULT 1,
                        `deleted` int(1) NULL DEFAULT 0,
                        PRIMARY KEY (`id`) USING BTREE,
                        INDEX `type_fee`(`type_fee`) USING BTREE,
                        INDEX `deleted`(`deleted`) USING BTREE,
                        INDEX `status`(`status`, `deleted`) USING BTREE,
                        INDEX `position`(`position`, `status`, `deleted`) USING BTREE
                    ) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;";
            $this->execute($query);
        }

        if (!$this->hasTable('shippings_method_content')) {
            $query = "
                    CREATE TABLE `shippings_method_content`  (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `shipping_method_id` int(11) NULL DEFAULT NULL,
                        `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                        `description` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
                        `content` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
                        `search_unicode` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                        `lang` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                        PRIMARY KEY (`id`) USING BTREE,
                        INDEX `shipping_method`(`shipping_method_id`) USING BTREE,
                        INDEX `shipping_method_2`(`shipping_method_id`, `lang`) USING BTREE
                    ) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;";
            $this->execute($query);
        }

    }

    public function down()
    {

    }
}
