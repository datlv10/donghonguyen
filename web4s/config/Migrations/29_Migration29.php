<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration29 extends AbstractMigration
{

    /**
     * 
     * 
     * - bảng `customers` `level_partner_affiliate` mặc định bằng 0
     * - bảng `customers_affiliate_request` `identity_card` varchar 255
     */

    public function up()
    {
        // bảng `customers` thêm column `is_partner_affiliate`, `level_partner_affiliate`, `identity_card_id`, `identity_card_date`
   
        if ($this->table('customers')->hasColumn('level_partner_affiliate')) {
            $this->execute('ALTER TABLE `customers` MODIFY `level_partner_affiliate` int(2) DEFAULT 0');
        }

        if ($this->table('customers_affiliate_request')->hasColumn('width')) {
            $this->execute('ALTER TABLE `customers_affiliate_request` MODIFY `identity_card` varchar(255) DEFAULT NULL');
        }
    }

    public function down()
    {
   
    }
}
