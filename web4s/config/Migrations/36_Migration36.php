<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration36 extends AbstractMigration
{

    /**
     * 
     * - bảng `customers_account` thêm column `apple_id`
     */

    public function up()
    {
        // - bảng `customers_account` thêm column `apple_id`
        $table = $this->table('customers_account');

        if (!$table->hasColumn('apple_id')) {
            $table->addColumn('apple_id', 'string', [
                'limit' => 255,
                'after' => 'facebook_id',
                'null' => true
            ])->update();
        }
    }

    public function down()
    {
   
    }
}
