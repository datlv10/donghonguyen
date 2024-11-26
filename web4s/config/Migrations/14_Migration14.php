<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration14 extends AbstractMigration
{

    /**
     * 
     * - bảng `orders` 
     *      + thêm column `point_paid`, `point_promotion_paid`, `point`, `point_promotion`
     * 
     * - bảng `customers_point_history`
     *      + thêm column `customer_related_id`
     * 
     * - bảng `plugins`
     *      + thêm rows 'point'
     * 
     */

    public function up()
    {
        $orders_table = $this->table('orders');
        $customers_point_history_table = $this->table('customers_point_history');
        $plugins_table = $this->table('plugins');

        // thêm column 'point_paid'        
        if (!$orders_table->hasColumn('point_paid')) {
            $orders_table->addColumn('point_paid', 'integer', [
                'limit' => 11,
                'after' => 'voucher_paid'
            ])->update();
        }

        // thêm column 'point_promotion_paid'
        if (!$orders_table->hasColumn('point_promotion_paid')) {
            $orders_table->addColumn('point_promotion_paid', 'integer', [
                'limit' => 11,
                'after' => 'point_paid'
            ])->update();
        }

        // thêm column 'point'
        if (!$orders_table->hasColumn('point')) {
            $orders_table->addColumn('point', 'integer', [
                'limit' => 11,
                'after' => 'point_promotion_paid'
            ])->update();
        }

        // thêm column 'point_promotion'
        if (!$orders_table->hasColumn('point_promotion')) {
            $orders_table->addColumn('point_promotion', 'integer', [
                'limit' => 11,
                'after' => 'point'
            ])->update();
        }



        // thêm column 'customer_related_id'
        if (!$customers_point_history_table->hasColumn('customer_related_id')) {
            $customers_point_history_table->addColumn('customer_related_id', 'integer', [
                'limit' => 11,
                'after' => 'staff_id'
            ])->update();
        }




        // thêm row 'point' trong bảng `plugins`    
        $row = $this->fetchRow('SELECT * FROM `plugins` WHERE `code` = "point"');
        if (empty($row)) {
            $plugins_table->insert([
                'code'  => 'point',
                'name' => 'Quản lý tích điểm tài khoản',
                'status' => 0
            ]);

            $plugins_table->saveData();
        }
    }

    public function down()
    {

    }
}
