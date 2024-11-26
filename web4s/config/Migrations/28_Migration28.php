<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration28 extends AbstractMigration
{

    /**
     * 
     * 
     * - bảng `customers` thêm column `is_partner_affiliate`, `level_partner_affiliate`, `identity_card_id`, `identity_card_date`
     * - bảng `orders` thêm coloumn `affiliate_discount_type`, `affiliate_discount_value`, `total_affiliate`, `affiliate_code`
     */

    public function up()
    {

        // bảng `customers` thêm column `is_partner_affiliate`, `level_partner_affiliate`, `identity_card_id`, `identity_card_date`
        $customer_table = $this->table('customers');
        $orders_table = $this->table('orders');
   
        if (!$customer_table->hasColumn('is_partner_affiliate')) {
            $customer_table->addColumn('is_partner_affiliate', 'integer', [
                'limit' => 1,
                'after' => 'staff_name',
                'null' => true
            ])->update();
        }

        if (!$customer_table->hasColumn('level_partner_affiliate')) {
            $customer_table->addColumn('level_partner_affiliate', 'integer', [
                'limit' => 2,
                'after' => 'is_partner_affiliate',
                'null' => true
            ])->update();
        }

        if (!$customer_table->hasColumn('identity_card_id')) {
            $customer_table->addColumn('identity_card_id', 'string', [
                'limit' => 20,
                'after' => 'level_partner_affiliate',
                'null' => true
            ])->update();
        }

        if (!$customer_table->hasColumn('identity_card_date')) {
            $customer_table->addColumn('identity_card_date', 'string', [
                'limit' => 11,
                'after' => 'identity_card_id',
                'null' => true
            ])->update();
        }

        // bảng `orders` thêm coloumn `affiliate_discount_type`, `affiliate_discount_value`, `total_affiliate`, `affiliate_code`
        if (!$orders_table->hasColumn('affiliate_discount_type')) {
            $orders_table->addColumn('affiliate_discount_type', 'string', [
                'limit' => 20,
                'after' => 'discount_value',
                'null' => true
            ])->update();
        }

        if (!$orders_table->hasColumn('affiliate_discount_value')) {
            $orders_table->addColumn('affiliate_discount_value', 'decimal', [
                'after' => 'affiliate_discount_type',
                'null' => true,
                'precision' => 15,
                'scale'=> 2
            ])->update();
        }

        if (!$orders_table->hasColumn('total_affiliate')) {
            $orders_table->addColumn('total_affiliate', 'decimal', [
                'after' => 'affiliate_discount_value',
                'null' => true,
                'precision' => 15,
                'scale'=> 2
            ])->update();
        }

        if (!$orders_table->hasColumn('affiliate_code')) {
            $orders_table->addColumn('affiliate_code', 'string', [
                'limit' => 100,
                'after' => 'total_affiliate',
                'null' => true
            ])->update();
        }
    }

    public function down()
    {
   
    }
}
