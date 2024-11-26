<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration7 extends AbstractMigration
{

    /**
     * 
     * - thêm bảng  
     *      + `customers_point` 
     *      + `customers_point_history` 
     * 
     *      + `mobile_app`
     *      + `mobile_template` 
     *      + `mobile_template_block`
     *      + `mobile_template_page`
     *      + `mobile_template_row`
     * 
     */

    public function up()
    {   
        // thêm bảng `customers_point`
        if (!$this->hasTable('customers_point')) {
            $query = "
                    CREATE TABLE `customers_point`  (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `customer_id` int(11) NULL DEFAULT NULL,
                        `point` int(11) NULL DEFAULT NULL COMMENT 'Điểm nạp của khách hàng',
                        `point_promotion` int(11) NULL DEFAULT NULL COMMENT 'Điểm khuyến mãi',
                        `expiration_time` int(11) NULL DEFAULT NULL COMMENT 'Thời gian hết hạn điểm khuyến mãi',
                        PRIMARY KEY (`id`) USING BTREE,
                        INDEX `customer_id`(`customer_id`) USING BTREE,
                        INDEX `customer_id_2`(`customer_id`, `expiration_time`) USING BTREE
                    ) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Fixed;";
            $this->execute($query); 
        }


        // thêm bảng `customers_point_history`
        if (!$this->hasTable('customers_point_history')) {
            $query = "
                    CREATE TABLE `customers_point_history`  (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `customer_id` int(11) NULL DEFAULT NULL,
                        `point` int(11) NULL DEFAULT NULL COMMENT 'Số điểm thêm, sử dụng',
                        `point_type` int(11) NULL DEFAULT NULL COMMENT '0 -> khuyến mãi, 1 -> mặc định',
                        `action` int(11) NULL DEFAULT NULL COMMENT '0 => trừ, 1 => cộng',
                        `action_type` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '[order, promotion, attendance, other]',
                        `staff_id` int(11) NULL DEFAULT NULL COMMENT 'Nhân viên thực hiện',
                        `note` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                        `status` int(11) NULL DEFAULT 1 COMMENT '0 -> hủy, 1 -> thành công, 2-> chờ duyệt',
                        `created` int(11) NULL DEFAULT NULL,
                        `updated` int(11) NULL DEFAULT NULL,
                        PRIMARY KEY (`id`) USING BTREE,
                        INDEX `customer_id`(`customer_id`) USING BTREE,
                        INDEX `customer_id_2`(`customer_id`, `status`) USING BTREE,
                        INDEX `customer_id_3`(`customer_id`, `action`) USING BTREE,
                        INDEX `customer_id_4`(`customer_id`, `created`) USING BTREE
                    ) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;";
            $this->execute($query); 
        }


        // thêm bảng `mobile_app`
        if (!$this->hasTable('mobile_app')) {
            $query = "
                    CREATE TABLE `mobile_app`  (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `app_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                        `app_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                        `config` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
                        PRIMARY KEY (`id`) USING BTREE
                    ) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;";
            $this->execute($query); 
        }

        // thêm bảng `mobile_template`
        if (!$this->hasTable('mobile_template')) {
            $query = "
                    CREATE TABLE `mobile_template`  (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `code` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Mã template',
                        `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                        `description` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                        `author` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                        `config` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'Json lưu các cấu hình của template',
                        `images` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'json lưu các các ảnh hệ thống của template',
                        `text` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
                        `is_default` int(1) NULL DEFAULT 0,
                        PRIMARY KEY (`id`) USING BTREE,
                        INDEX `is_default`(`is_default`) USING BTREE
                    ) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;";
            $this->execute($query);
        }

        // thêm bảng `mobile_template_block`
        if (!$this->hasTable('mobile_template_block')) {
            $query = "
                    CREATE TABLE `mobile_template_block`  (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `template_code` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                        `code` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                        `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                        `type` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                        `config` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
                        `search_unicode` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                        `status` int(1) NULL DEFAULT 1,
                        PRIMARY KEY (`id`) USING BTREE,
                        INDEX `status`(`status`) USING BTREE,
                        INDEX `template_code`(`template_code`) USING BTREE,
                        INDEX `template_code_2`(`template_code`, `status`) USING BTREE
                    ) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;";
            $this->execute($query);
        }

        // thêm bảng `mobile_template_page`
        if (!$this->hasTable('mobile_template_page')) {
            $query = "
                    CREATE TABLE `mobile_template_page`  (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `template_code` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                        `code` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                        `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                        `type` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'home, product, product_detail, article, article_detail',
                    PRIMARY KEY (`id`) USING BTREE,
                    INDEX `template_code`(`template_code`) USING BTREE,
                    INDEX `template_code_2`(`template_code`, `type`) USING BTREE
                    ) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;";
            $this->execute($query);

        }

        // thêm bảng `mobile_template_row`
        if (!$this->hasTable('mobile_template_row')) {
            $query = "
                    CREATE TABLE `mobile_template_row`  (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `template_code` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                        `page_code` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                        `block_code` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                        PRIMARY KEY (`id`) USING BTREE,
                        INDEX `template_code`(`template_code`) USING BTREE,
                        INDEX `template_code_2`(`template_code`, `page_code`) USING BTREE
                    ) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;";
            $this->execute($query);
        }
    }

    public function down()
    {

    }
}
