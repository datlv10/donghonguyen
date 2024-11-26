<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration20 extends AbstractMigration
{

    /**
     * - Xóa bảng `shippings_rule`, `shippings_rule_location`, `shippings_rule_order`, `shippings_city`, `shippings_district`, `shippings_ward`
     * 
     * - Khởi tạo lại bảng `shippings_city`, `shippings_district`, `shippings_ward`
     * 
     * 
     * - Bảng `shippings_carrier`
     *      + thêm column `config`
     */

    public function up()
    {   
        if ($this->hasTable('shippings_rule')) {
            $this->table('shippings_rule')->drop()->save();
        }

        if ($this->hasTable('shippings_rule_location')) {
            $this->table('shippings_rule_location')->drop()->save();
        }

        if ($this->hasTable('shippings_rule_order')) {
            $this->table('shippings_rule_order')->drop()->save();
        }

        if ($this->hasTable('shippings_city')) {
            $this->table('shippings_city')->drop()->save();
        }

        if ($this->hasTable('shippings_district')) {
            $this->table('shippings_district')->drop()->save();
        }

        if ($this->hasTable('shippings_ward')) {
            $this->table('shippings_ward')->drop()->save();
        }


        // khởi tạo bảng `shippings_city`
        if (!$this->hasTable('shippings_city')) {
            $query = "
                    CREATE TABLE `shippings_city`  (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `city_id` int(11) NULL DEFAULT NULL COMMENT 'ID tỉnh thành hệ thống',
                        `carrier` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Mã hãng vận chuyển',
                        `carrier_city_id` int(11) NULL DEFAULT NULL,
                        `carrier_city_code` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Mã tỉnh thành hãng vận chuyển',
                        PRIMARY KEY (`id`) USING BTREE,
                        INDEX `city_id`(`city_id`) USING BTREE,
                        INDEX `carrier_code`(`carrier`) USING BTREE,
                        INDEX `city_id_2`(`city_id`, `carrier`) USING BTREE
                    ) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;";
            $this->execute($query);
        }


        // khởi tạo bảng `shippings_district`
        if (!$this->hasTable('shippings_district')) {
            $query = "
                    CREATE TABLE `shippings_district`  (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `district_id` int(11) NULL DEFAULT NULL COMMENT 'ID quận huyện hệ thống',
                        `carrier` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Mã hãng vận chuyển',
                        `carrier_district_id` int(11) NULL DEFAULT NULL COMMENT 'ID quận huyện hãng vận chuyển',
                        `carrier_district_code` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Mã quận huyện hãng vận chuyển',
                        PRIMARY KEY (`id`) USING BTREE,
                        INDEX `district_id`(`district_id`) USING BTREE,
                        INDEX `carrier_code`(`carrier`) USING BTREE,
                        INDEX `district_id_2`(`district_id`, `carrier`) USING BTREE
                    ) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;";
            $this->execute($query);
        }


        // khởi tạo bảng `shippings_ward`
        if (!$this->hasTable('shippings_ward')) {
            $query = "
                    CREATE TABLE `shippings_ward`  (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `ward_id` int(11) NULL DEFAULT NULL COMMENT 'ID phường xã hệ thống',
                        `carrier` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Mã hãng vận chuyển',
                        `carrier_ward_id` int(11) NULL DEFAULT NULL COMMENT 'ID phường xã hãng vận chuyển',
                        `carrier_ward_code` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Mã phường xã hãng vận chuyển',
                        PRIMARY KEY (`id`) USING BTREE,
                        INDEX `ward_id`(`ward_id`) USING BTREE,
                        INDEX `carrier`(`carrier`) USING BTREE,
                        INDEX `ward_id_2`(`ward_id`, `carrier`) USING BTREE
                    ) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;";
            $this->execute($query);
        }


        // thêm column `config` vào bảng `shippings_carrier`
        $shippings_carrier_table = $this->table('shippings_carrier');
        if (!$shippings_carrier_table->hasColumn('config')) {
            $shippings_carrier_table->addColumn('config', 'text', [
                'after' => 'status',
                'null' => true
            ])->update();
        }

    }

    public function down()
    {

    }
}
