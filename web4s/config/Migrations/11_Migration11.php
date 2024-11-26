<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration11 extends AbstractMigration
{

    /**
     * 
     * - thêm  column `sub_method` vào bảng `payments`
     * 
     * 
     */

    public function up()
    {
        // thêm column 'sub_method'
        $table = $this->table('payments');
        if (!$table->hasColumn('sub_method')) {
            $table->addColumn('sub_method', 'string', [
                'limit' => 20,
                'after' => 'payment_method',
                'comment' => 'Xử dụng với cổng azpay'
            ])->update();
        }
    }

    public function down()
    {

    }
}
