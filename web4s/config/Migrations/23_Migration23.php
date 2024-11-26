<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration23 extends AbstractMigration
{

    /**
     * 
     * - thêm  column `carrier_service_type_code` vào bảng `shippings`
     * - thêm  column `carrier_shop_id` vào bảng `shippings`
     * - thêm  column `carrier_shipping_fee` vào bảng `shippings`
     * - thêm  column `required_note` vào bảng `shippings`
     * - Cập nhật column `carrier_order_code` độ dài -> limit 50
     * 
     * 
     * - thêm  column `shipping_method_id` vào bảng `orders`
     * 
     * - cập nhật kiểu dữ liệu column `exchange_rate` trong bảng `currencies`
     * 
     */

    public function up()
    {
        $table = $this->table('shippings');
   
        if (!$table->hasColumn('carrier_service_type_code')) {
            $table->addColumn('carrier_service_type_code', 'string', [
                'limit' => 20,
                'after' => 'carrier_service_code',
                'null' => true
            ])->update();
        }

        if (!$table->hasColumn('carrier_shop_id')) {
            $table->addColumn('carrier_shop_id', 'string', [
                'limit' => 50,
                'after' => 'carrier_service_type_code',
                'null' => true
            ])->update();
        }

        if (!$table->hasColumn('carrier_shipping_fee')) {
            $table->addColumn('carrier_shipping_fee', 'decimal', [
                'after' => 'carrier_order_code',
                'null' => true,
                'precision' => 11,
                'scale'=> 2
            ])->update();
        }

        if (!$table->hasColumn('required_note')) {
            $table->addColumn('required_note', 'string', [
                'limit' => 50,
                'after' => 'carrier_shipping_fee',
                'null' => true
            ])->update();
        }

        if ($table->hasColumn('carrier_order_code')) {
            $table->changeColumn('carrier_order_code', 'string', ['limit' => 50])->save();
        }

        $orders_table = $this->table('orders');
        if (!$orders_table->hasColumn('shipping_method_id')) {
            $orders_table->addColumn('shipping_method_id', 'integer', [
                'limit' => 20,
                'after' => 'discount_note',
                'null' => true
            ])->update();
        }


        $currencies_table = $this->table('currencies');
        if ($currencies_table->hasColumn('exchange_rate')) {
            $currencies_table->changeColumn('exchange_rate', 'string', ['limit' => 50])->save();
        }
    }

    public function down()
    {

    }
}
