<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration16 extends AbstractMigration
{

    /**
     * 
     * - Bỏ rằng buộc bắt nhập của các field đã thêm ở migration14
     * 
     * 
     */

    public function up()
    {
        if ($this->table('orders')->hasColumn('point_paid')) {
            $this->execute('ALTER TABLE `orders` MODIFY `point_paid` int(11) DEFAULT NULL');
        }

        if ($this->table('orders')->hasColumn('point_promotion_paid')) {
            $this->execute('ALTER TABLE `orders` MODIFY `point_promotion_paid` int(11) DEFAULT NULL');
        }

        if ($this->table('orders')->hasColumn('point')) {
            $this->execute('ALTER TABLE `orders` MODIFY `point` int(11) DEFAULT NULL');
        }

        if ($this->table('orders')->hasColumn('point_promotion')) {
            $this->execute('ALTER TABLE `orders` MODIFY `point_promotion` int(11) DEFAULT NULL');
        }

        if ($this->table('customers_point_history')->hasColumn('customer_related_id')) {
            $this->execute('ALTER TABLE `customers_point_history` MODIFY `customer_related_id` int(11) DEFAULT NULL');
        }
    }

    public function down()
    {

    }
}
