<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration3 extends AbstractMigration
{

    /**
     * Tạo bảng `wishlists` 
     */

    public function up()
    {
        // kiểm tra bảng `wishlists` đã tồn hay chưa
        if (!$this->hasTable('wishlists')) {
            $query = "
                    CREATE TABLE `wishlists`  (
                      `id` int(11) NOT NULL AUTO_INCREMENT,
                      `customer_account_id` int(11) NULL DEFAULT NULL,
                      `type` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'product, article',
                      `record_id` int(11) NULL DEFAULT NULL,
                      PRIMARY KEY (`id`) USING BTREE,
                      INDEX `customer_account_id`(`customer_account_id`, `type`) USING BTREE,
                      INDEX `type`(`type`) USING BTREE,
                      INDEX `customer_account_id_2`(`customer_account_id`) USING BTREE
                    ) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;";
            $this->execute($query); 
        }
    }

    public function down()
    {

    }
}
