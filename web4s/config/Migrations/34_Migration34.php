<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration34 extends AbstractMigration
{

    /**
     * 
     * - sửa bảng `counters` thêm AUTO_INCREMENT `id` và đánh key cho `id`
     * - xóa bảng `log_access` và thêm lại bảng `log_access`
     */

    public function up()
    {
        $counters_table = $this->table('counters');

        if ($counters_table->hasColumn('id')) {
            $query = "ALTER TABLE `counters` MODIFY id INT PRIMARY KEY AUTO_INCREMENT;";
            $this->execute($query);

            $counters_table->addIndex(['date'])->save();
        }

        if ($this->hasTable('log_access')) {
            $this->table('log_access')->drop()->save();
            $query = "
            CREATE TABLE `log_access`  (
                `session_id` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
                `time_access` int(11) NULL DEFAULT NULL,
                PRIMARY KEY (`session_id`) USING BTREE,
                INDEX `time_access`(`time_access`) USING BTREE,
                INDEX `session_id`(`session_id`) USING BTREE
            ) ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;
            ";
            $this->execute($query);
        }
    }

    public function down()
    {
    
    }
}
