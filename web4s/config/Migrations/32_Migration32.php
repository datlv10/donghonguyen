<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration32 extends AbstractMigration
{

    /**
     * 
     * - bảng `customers_point_tomoney` thêm column `type`
     */

    public function up()
    {

        // - bảng `customers_point_tomoney` thêm column `type`
        $customer_bank_table = $this->table('customers_point_tomoney');

        if (!$customer_bank_table->hasColumn('type')) {
            $customer_bank_table->addColumn('type', 'integer', [
                'limit' => 1,
                'after' => 'note',
                'null' => true
            ])->update();
        }
    }

    public function down()
    {
   
    }
}
