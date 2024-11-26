<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration25 extends AbstractMigration
{

    /**
     * 
     * - Cập nhật lại column `carrier_order_code` bảng `shippings`
     * 
     * 
     */

    public function up()
    {

        if ($this->table('shippings')->hasColumn('carrier_order_code')) {
            $this->execute('ALTER TABLE `shippings` MODIFY `carrier_order_code` varchar(50) DEFAULT NULL');
        }
    }

    public function down()
    {

    }
}
