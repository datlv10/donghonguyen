<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration27 extends AbstractMigration
{

    /**
     * 
     * - bảng `plugins` thêm rows
     *      + thêm rows 'affiliate'
     *      + thêm rows 'notification'
     * 
     * - thêm bảng `customers_affiliate`
     * - thêm bảng `customers_affiliate_request`
     * - thêm bảng `customers_affiliate_order`
     * - thêm bảng `customers_point_tomoney`
     * - thêm bảng `customers_bank`
     * - thêm bảng `notifications`
     * - thêm bảng `notifications_subscribe`
     * 
     * - bảng `customers` thêm column `is_partner_affiliate`, `level_partner_affiliate`, `identity_card_id`, `identity_card_date`
     * - bảng `orders` thêm coloumn `affiliate_discount_type`, `affiliate_discount_value`, `total_affiliate`, `affiliate_code`
     */

    public function up()
    {
        $plugins_table = $this->table('plugins');

        // thêm row 'affiliate' trong bảng `plugins`    
        $row = $this->fetchRow('SELECT * FROM `plugins` WHERE `code` = "affiliate"');
        if (empty($row)) {
            $plugins_table->insert([
                'code'  => 'affiliate',
                'name' => 'Liên kết đối tác',
                'status' => 0
            ]);

            $plugins_table->saveData();
        }

        // thêm row 'notification' trong bảng `plugins`    
        $row = $this->fetchRow('SELECT * FROM `plugins` WHERE `code` = "notification"');
        if (empty($row)) {
            $plugins_table->insert([
                'code'  => 'notification',
                'name' => 'Quản lý thông báo',
                'status' => 0
            ]);

            $plugins_table->saveData();
        }

        if (!$this->hasTable('customers_affiliate')) {
            $query = "
                    CREATE TABLE `customers_affiliate`  (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `customer_id` int(11) NULL DEFAULT NULL,
                        `number_referral` int(11) NULL DEFAULT 0 COMMENT 'Số lượt giới thiệu mua hàng',
                        `total_order_success` decimal(15, 2) NULL DEFAULT 0.00 COMMENT 'Tổng tiền của đơn hàng thành công',
                        `total_order_failed` decimal(15, 2) NULL DEFAULT 0.00 COMMENT 'Tổng tiền của đơn hàng hủy',
                        `total_point` int(11) NULL DEFAULT 0 COMMENT 'Số điểm(hoa hồng) đã được nhận',
                        PRIMARY KEY (`id`) USING BTREE,
                        INDEX `customer_id`(`customer_id`) USING BTREE,
                        INDEX `total_order_referral`(`total_order_success`) USING BTREE
                    ) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;";
            $this->execute($query);
        }

        if (!$this->hasTable('customers_affiliate_request')) {
            $query = "
                    CREATE TABLE `customers_affiliate_request`  (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `customer_id` int(11) NULL DEFAULT NULL,
                        `bank` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'json ngan hang',
                        `identity_card` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'json chứng minh thư / thẻ căn cước',
                        `survey` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'json khảo sát',
                        `status` int(1) NULL DEFAULT 2,
                        `created` int(11) NULL DEFAULT NULL,
                        PRIMARY KEY (`id`) USING BTREE,
                        INDEX `customer_id`(`customer_id`) USING BTREE,
                        INDEX `status`(`status`) USING BTREE,
                        INDEX `status_2`(`status`, `created`) USING BTREE
                    ) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;";
            $this->execute($query);
        }

        if (!$this->hasTable('customers_affiliate_order')) {
            $query = "
                    CREATE TABLE `customers_affiliate_order`  (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `customer_id` int(11) NULL DEFAULT NULL COMMENT 'ID Khách hàng',
                        `order_id` int(11) NULL DEFAULT NULL COMMENT 'ID đơn hàng',
                        `profit_value` decimal(11, 2) NULL DEFAULT NULL COMMENT 'giá trị % hoa hồng được nhận',
                        `profit_point` int(11) NULL DEFAULT NULL COMMENT 'giá trị hoa hồng được quy ra điểm',
                        `profit_money` decimal(15, 2) NULL DEFAULT NULL COMMENT 'giá trị hoa hồng được quy ra tiền',
                        PRIMARY KEY (`id`) USING BTREE,
                        INDEX `customer_id`(`customer_id`) USING BTREE
                    ) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;";
            $this->execute($query);
        }

        if (!$this->hasTable('customers_point_tomoney')) {
            $query = "
                    CREATE TABLE `customers_point_tomoney`  (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `customer_id` int(11) NULL DEFAULT NULL,
                        `bank_id` int(11) NULL DEFAULT NULL,
                        `point` int(11) NULL DEFAULT NULL,
                        `money` int(11) NULL DEFAULT NULL COMMENT 'Số tiền tương ứng với số điểm yêu cầu rút',
                        `note_admin` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                        `note` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                        `status` int(1) NULL DEFAULT 2 COMMENT '0->hủy, 1-> thành công, 2-> chờ duyệt ',
                        `created` int(11) NULL DEFAULT NULL,
                        `time_confirm` int(11) NULL DEFAULT NULL COMMENT 'Thời gian xác nhận',
                        PRIMARY KEY (`id`) USING BTREE,
                        INDEX `customer_id`(`customer_id`) USING BTREE,
                        INDEX `customer_id_2`(`customer_id`, `status`) USING BTREE,
                        INDEX `customer_id_3`(`customer_id`, `created`) USING BTREE
                    ) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;";
            $this->execute($query);
        }

        if (!$this->hasTable('customers_bank')) {
            $query = "
                    CREATE TABLE `customers_bank`  (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `customer_id` int(11) NULL DEFAULT NULL,
                        `bank_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                        `bank_branch` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                        `account_number` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                        `account_holder` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                        `is_default` int(1) NULL DEFAULT 0,
                        `deleted` int(1) NULL DEFAULT 0,
                        PRIMARY KEY (`id`) USING BTREE,
                        INDEX `customer_id`(`customer_id`) USING BTREE,
                        INDEX `customer_id_2`(`customer_id`, `deleted`) USING BTREE
                    ) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;";
            $this->execute($query);
        }

        if (!$this->hasTable('notifications')) {
            $query = "
                    CREATE TABLE `notifications`  (
                        `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                        `type` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'all, website, mobile_app',
                        `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                        `body` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '',
                        `link` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Đường dẫn trên website',
                        `image` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                        `icon` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                        `mobile_action` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Action trên app',
                        `created` int(11) NULL DEFAULT NULL,
                        `created_by` int(11) NULL DEFAULT NULL,
                        `sent` int(1) NULL DEFAULT 0 COMMENT '0 -> chưa gửi thông báo, 1-> đã gửi',
                        `status` int(1) NULL DEFAULT 0,
                        `search_unicode` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                        PRIMARY KEY (`id`) USING BTREE,
                        INDEX `type_2`(`created`) USING BTREE
                    ) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic";
            $this->execute($query);
        }

        if (!$this->hasTable('notifications_subscribe')) {
            $query = "
                    CREATE TABLE `notifications_subscribe`  (
                        `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                        `token` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                        `platform` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'web, ios, android',
                        `browser` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Tên trình duyệt',
                        `user_agent` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Useragent trình duyệt',
                        `customer_id` int(11) NULL DEFAULT NULL,
                        `user_admin_id` int(11) NULL DEFAULT NULL,
                        `created` int(11) NULL DEFAULT NULL,
                        `updated` int(11) NULL DEFAULT NULL,
                        PRIMARY KEY (`id`) USING BTREE,
                        INDEX `customer_id`(`customer_id`) USING BTREE,
                        INDEX `updated`(`created`) USING BTREE,
                        INDEX `device`(`platform`) USING BTREE,
                        INDEX `updated_2`(`updated`) USING BTREE,
                        INDEX `user_admin_id`(`user_admin_id`) USING BTREE,
                        INDEX `customer_id_2`(`customer_id`) USING BTREE,
                        INDEX `platform`(`platform`) USING BTREE
                    ) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;";
            $this->execute($query);
        }
    }

    public function down()
    {
   
    }
}
