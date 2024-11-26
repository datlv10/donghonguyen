<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration15 extends AbstractMigration
{

    /**
     * 
     * - thêm  column `code` vào bảng `customers_point_history`
     * 
     * 
     */

    public function up()
    {
        // thêm column 'code'
        $table = $this->table('customers_point_history');
        if (!$table->hasColumn('code')) {
            $table->addColumn('code', 'string', [
                'limit' => 20,
                'after' => 'id',
                'null' => true
            ])->update();
        }
    }

    public function down()
    {

    }
}
