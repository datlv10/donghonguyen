<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration49 extends AbstractMigration
{
    /**
     * 
     * - thêm  index `order_id` vào bảng `orders_contact`
     * 
     * 
     */

    public function up()
    {
        if ($this->hasTable('orders_contact')) {
            $query = "
                ALTER TABLE `orders_contact` 
                DROP INDEX `order_id`,
                ADD INDEX `order_id`(`order_id`) USING BTREE;
            ";
            $this->execute($query);
        }
    }

    public function down()
    {
   
    }
}