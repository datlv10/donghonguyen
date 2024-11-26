<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration10 extends AbstractMigration
{

    /**
     * 
     * - thêm  column `public` vào bảng `promotions`
     * 
     * 
     */

    public function up()
    {
        // thêm column `public`
        $table = $this->table('promotions');
        if (!$table->hasColumn('public')) {
            $table->addColumn('public', 'integer', [
                'limit' => 11,
                'after' => 'level'
            ])->update();
        }
    }

    public function down()
    {

    }
}
