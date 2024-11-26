<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration13 extends AbstractMigration
{

    /**
     * 
     * - thêm bảng `customers_point_tick`
     * 
     */

    public function up()
    {
        // thêm bảng `customers_point_tick`
        if (!$this->hasTable('customers_point_tick')) {
            $query = "
                    CREATE TABLE `customers_point_tick`  (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `member_id` int(11) NULL DEFAULT NULL,
                        `tick_time` int(11) NULL DEFAULT NULL,
                        PRIMARY KEY (`id`) USING BTREE,
                        INDEX `member_id`(`member_id`) USING BTREE,
                        INDEX `tick_time`(`tick_time`) USING BTREE,
                        INDEX `member_id_2`(`member_id`, `tick_time`) USING BTREE
                    ) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Fixed;";
            $this->execute($query);
        }
    }

    public function down()
    {

    }
}
