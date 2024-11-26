<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration46 extends AbstractMigration
{

    /**
     * 
     * - Thêm bảng `print_templates`
     * - Xoá bảng cũ `print_template`
     */

    public function up()
    {
        // thêm bảng mới
        if (!$this->hasTable('print_templates')) {
            $query = "
                CREATE TABLE `print_templates` (
                    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                    `code` varchar(50) DEFAULT NULL,
                    `name` varchar(255) DEFAULT NULL,
                    `title_print` varchar(255) DEFAULT NULL,
                    `template` varchar(50) DEFAULT NULL,
                    PRIMARY KEY (`id`) USING BTREE
                ) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;";
            $this->execute($query);

            // thêm dữ liệu khởi tạo
            $query = "INSERT INTO `print_templates` (`id`, `code`, `name`, `title_print`, `template`) VALUES(1,'ORDER','Đơn hàng','Hóa đơn bán hàng','order.tpl');";
            $this->execute($query);
        }

        // xoá bảng cũ
        if ($this->hasTable('print_template')) {
            $this->table('print_template')->drop()->save();
        }
    }

    public function down()
    {
        
    }
}
