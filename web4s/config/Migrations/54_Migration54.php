<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration54 extends AbstractMigration
{

    /**
     * Thêm cấu trúc dữ liệu cho plugin kết nối Kho của Kiotviet
     * - thêm bảng `products_partner_quantity`
     * - thêm bảng `products_partner_store`
     * 
     * - Thêm field vào bảng `products_item`
            + kiotviet_id
            + kiotviet_code
     * 
     * - Thêm field vào bảng `orders`
            + kiotviet_code
     * 
     */

    public function up()
    {
        // thêm bảng `products_partner_store`
        if (!$this->hasTable('products_partner_store')) {
            $query = "
                CREATE TABLE `products_partner_store`  (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `partner` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'kiotviet,nhanh',
                    `code` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Mã kho hàng',
                    `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Tên kho hàng của đối tác (Kiot Viet, Nhanh)',
                    `phone` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                    `email` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                    `address` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                    `partner_store_id` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
                    `is_default` int(11) NULL DEFAULT 0 COMMENT 'Kho hàng mặc định',
                    `deleted` int(1) UNSIGNED ZEROFILL NULL DEFAULT 0,
                  PRIMARY KEY (`id`) USING BTREE,
                  INDEX `partner`(`partner`) USING BTREE,
                  INDEX `deleted`(`deleted`) USING BTREE,
                  INDEX `partner_2`(`partner`, `deleted`) USING BTREE
                ) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;";
            $this->execute($query);
        }

        // thêm bảng `products_partner_quantity`
        if (!$this->hasTable('products_partner_quantity')) {
            $query = "
                CREATE TABLE `products_partner_quantity`  (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `partner` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'kiotviet, nhanh',
                    `product_id` int(11) NULL DEFAULT NULL,
                    `product_item_id` int(11) NULL DEFAULT NULL,
                    `store_id` int(11) NULL DEFAULT NULL,
                    `partner_product_id` int(11) NULL DEFAULT NULL COMMENT 'ID sản phẩm của đối tác',
                    `quantity` int(11) NULL DEFAULT NULL,
                    `deleted` int(1) UNSIGNED ZEROFILL NULL DEFAULT 0,
                    PRIMARY KEY (`id`) USING BTREE,
                    INDEX `deleted`(`deleted`) USING BTREE,
                    INDEX `partner`(`partner`, `deleted`) USING BTREE
                ) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;";
            $this->execute($query);
        }

        // thêm field `kiotviet_id` vào bảng `products_item` 
        $item_table = $this->table('products_item');
        if (!$item_table->hasColumn('kiotviet_id')) {
            $item_table->addColumn('kiotviet_id', 'integer', [
                'after' => 'quantity_available',
                'limit' => 11,
                'null' => true,
                'comment' => ''
            ])->update();
        }

        // thêm field `kiotviet_code` vào bảng `products_item` 
        if (!$item_table->hasColumn('kiotviet_code')) {
            $item_table->addColumn('kiotviet_code', 'string', [
                'after' => 'kiotviet_id',
                'limit' => 20,
                'null' => true,
                'comment' => ''
            ])->update();
        }

        // thêm field `kiotviet_code` vào bảng `orders` 
        if (!$this->table('orders')->hasColumn('kiotviet_code')) {
            $this->table('orders')->addColumn('kiotviet_code', 'string', [
                'after' => 'customer_cancel',
                'limit' => 20,
                'null' => true,
                'comment' => ''
            ])->update();
        }
    }

    public function down()
    {
   
    }
}
