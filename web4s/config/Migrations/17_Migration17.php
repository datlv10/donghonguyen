<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration17 extends AbstractMigration
{

    /**
     * 
     * - thêm  column `phone` vào bảng `customers`
     * 
     * 
     */

    public function up()
    {
        // thêm column 'phone'
        $table = $this->table('customers');
        if (!$table->hasColumn('phone')) {
            $table->addColumn('phone', 'string', [
                'limit' => 20,
                'after' => 'email',
                'null' => true
            ])->update();
        }
    }

    public function down()
    {

    }
}
