<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration8 extends AbstractMigration
{

    /**
     * 
     * - Xóa bảng cũ
     *      + `promotions`
     *      + `promotions_coupon`
     *      + `promotions_coupon_code`
     * 
     * 
     * - Thêm bảng khuyến mãi theo cấu trúc mới
     *      +  `promotions`
     *      +  `promotions_coupon`
     * 
     */

    public function up()
    {   
        // xóa bảng `promotions`
        if ($this->hasTable('promotions')) {
            $this->table('promotions')->drop()->save();
        }

        // xóa bảng `promotions_coupon`
        if ($this->hasTable('promotions_coupon')) {
            $this->table('promotions_coupon')->drop()->save();
        }

        // xóa bảng `promotions_coupon_code`
        if ($this->hasTable('promotions_coupon_code')) {
            $this->table('promotions_coupon_code')->drop()->save();
        }
    

        // thêm bảng `promotions`
        if (!$this->hasTable('promotions')) {
            $query = "
                    CREATE TABLE `promotions`  (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `code` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                        `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                        `type` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'promotion, coupon',
                        `type_discount` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'percent, money, free_ship, give_product',
                        `value` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Json value',
                        `condition_product` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Json điều kiện áp dụng với sản phẩm',
                        `condition_order` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Json điều kiện áp dụng với đơn hàng',
                        `condition_customer` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Json điều kiện áp dụng với khách hàng',
                        `condition_location` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Json điều kiện áp dụng với tỉnh thành, quận huyện',
                        `start_time` int(11) NULL DEFAULT NULL,
                        `end_time` int(11) NULL DEFAULT NULL,
                        `max_uses` int(11) NULL DEFAULT NULL COMMENT 'Số lượt sử dụng, nếu null -> không giới hạn số lần sử dụng',
                        `level` int(1) NULL DEFAULT NULL COMMENT 'Mức độ áp dụng',
                        `number_coupon` int(11) NULL DEFAULT NULL COMMENT 'Số coupon đã phát hành',
                        `used` int(11) NULL DEFAULT NULL COMMENT 'Số lượt sử dụng',
                        `article_id` int(11) NULL DEFAULT NULL COMMENT 'Liên kết bài viết',
                        `position` int(11) NULL DEFAULT NULL,
                        `status` int(1) NULL DEFAULT 1,
                        `created` int(11) NULL DEFAULT NULL,
                        `updated` int(11) NULL DEFAULT NULL,
                        `created_by` int(11) NULL DEFAULT NULL,
                        `search_unicode` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                        `deleted` int(1) NULL DEFAULT 0,
                        PRIMARY KEY (`id`) USING BTREE,
                        INDEX `status`(`status`) USING BTREE,
                        INDEX `start_time`(`start_time`, `end_time`, `status`) USING BTREE,
                        INDEX `type`(`type`, `status`) USING BTREE,
                        INDEX `type_2`(`type`, `type_discount`, `status`) USING BTREE
                    ) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;";
            $this->execute($query);
        }

        // thêm bảng `promotions`
        if (!$this->hasTable('promotions_coupon')) {
            $query = "
                    CREATE TABLE `promotions_coupon`  (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `promotion_id` int(11) NULL DEFAULT NULL,
                        `code` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                        `used` int(11) NULL DEFAULT NULL COMMENT 'Số lần đã sử dụng',
                        `number_use` int(11) NULL DEFAULT NULL COMMENT 'Số lần được sử dụng, 0 -> không giới hạn',
                        `created` int(11) NULL DEFAULT NULL,
                        `status` int(1) NULL DEFAULT 1 COMMENT '0 => hủy, 1 => hoạt động, 2 => Đã sử dụng',
                        PRIMARY KEY (`id`) USING BTREE,
                        INDEX `promotion_id`(`promotion_id`) USING BTREE,
                        INDEX `promotion_id_2`(`promotion_id`, `status`) USING BTREE
                    ) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;";
            $this->execute($query);
        }

    }

    public function down()
    {

    }
}
