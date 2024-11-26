<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration41 extends AbstractMigration
{

    /**
     * 
     * - bảng `templates_block` thêm column `normal_data_extend`
     */

    public function up()
    {
        $table = $this->table('templates_block');

        if (!$table->hasColumn('normal_data_extend')) {
            $table->addColumn('normal_data_extend', 'text', [
                'after' => 'data_extend',
                'null' => true
            ])->update();
        }
    }

    public function down()
    {
   
    }
}
