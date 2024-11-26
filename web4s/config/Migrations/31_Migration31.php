<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration31 extends AbstractMigration
{

    /**
     * 
     * - bảng `customers_bank` thêm column `bank_key`
     * - bảng `customers_affiliate` thêm column `number_order_success`, `number_order_failed`
     */

    public function up()
    {

        // - bảng `customers_bank` thêm column `bank_key`
        $customer_bank_table = $this->table('customers_bank');
        $customer_affiliate_table = $this->table('customers_affiliate');

        if (!$customer_bank_table->hasColumn('bank_key')) {
            $customer_bank_table->addColumn('bank_key', 'string', [
                'limit' => 50,
                'after' => 'customer_id',
                'null' => true
            ])->update();
        }

        // - bảng `customers_affiliate` thêm column `number_order_success`, `number_order_failed`

        if (!$customer_affiliate_table->hasColumn('number_order_success')) {
            $customer_affiliate_table->addColumn('number_order_success', 'integer', [
                'limit' => 11,
                'after' => 'number_referral',
                'null' => true
            ])->update();
        }

        if (!$customer_affiliate_table->hasColumn('number_order_failed')) {
            $customer_affiliate_table->addColumn('number_order_failed', 'integer', [
                'limit' => 11,
                'after' => 'total_order_success',
                'null' => true
            ])->update();
        }
    }

    public function down()
    {
   
    }
}
