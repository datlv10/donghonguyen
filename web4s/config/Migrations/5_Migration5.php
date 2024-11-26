<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration5 extends AbstractMigration
{

    /**
     * - bảng `payments`
     *      + xóa column `branch_id`
     *      + đổi `order_id` -> `foreign_id`        
     *      + thêm column `foreign_type` (mặc định value = 'order')
     * 
     * 
     * - bảng `payments_gateway`
     *      + thêm row 'onepay'
     *      + xóa 2 row 'onepay_domestic' và 'onepay_international'
     * 
     * 
     * - bảng `payments_gateway_content`
     *      + thêm row 'onepay'
     *      + xóa 2 row 'onepay_domestic' và 'onepay_international'
     * 
     */

    public function up()
    {   

        
        // =============================  cập nhật bảng `payments`
        $payments_table = $this->table('payments');

        // xóa column `branch_id` trong bảng `payments`
        if ($payments_table->hasColumn('branch_id')) {            
            $payments_table->removeColumn('branch_id')->save();
        }

        // đổi tên column 'order_id' => 'foreign_id'
        if ($payments_table->hasColumn('order_id')) {            
            $payments_table->renameColumn('order_id', 'foreign_id');
        }

        // thêm column 'foreign_type'
        if (!$payments_table->hasColumn('foreign_type')) {
            $payments_table->addColumn('foreign_type', 'string', [
                'limit' => 20,
                'after' => 'foreign_id',
                'comment' => 'order, point, other'
            ])->update();
        }






        // =============================  cập nhật bảng `payments_gateway`

        // xóa row 'onepay_domestic' và 'onepay_international' trong bảng `payments_gateway` và
        $this->execute('DELETE FROM `payments_gateway` WHERE `code` = "onepay_domestic"');
        $this->execute('DELETE FROM `payments_gateway` WHERE `code` = "onepay_international"');

        $this->execute('DELETE FROM `payments_gateway_content` WHERE `payment_code` = "onepay_domestic"');
        $this->execute('DELETE FROM `payments_gateway_content` WHERE `payment_code` = "onepay_international"');

        // thêm row 'onepay' trong bảng `payments_gateway`
        $gateway_table = $this->table('payments_gateway');
        $row_gateway = $this->fetchRow('SELECT * FROM `payments_gateway` WHERE `code` = "onepay"');
        if (empty($row_gateway)) {
            $gateway_table->insert([
                'code'  => 'onepay',
                'config' => null,
                'is_installment' => 0,
                'status' => 0
            ]);

            $gateway_table->saveData();
        }

        

        // thêm row 'onepay' trong bảng `payments_gateway_content`
        $gateway_content_table = $this->table('payments_gateway_content');
        $row_gateway_content = $this->fetchRow('SELECT * FROM `payments_gateway_content` WHERE `payment_code` LIKE "onepay"');
        if (empty($row_gateway_content)) {

            $gateway_content_table->insert([
                [
                    'payment_code'  => 'onepay',
                    'name' => 'Thanh toán OnePay',
                    'content' => null,
                    'lang' => 'vi'
                ],
                [
                    'payment_code'  => 'onepay',
                    'name' => 'Payment of OnePay',
                    'content' => null,
                    'lang' => 'en'
                ]
            ])->save();
        }

    }

    public function down()
    {

    }
}
