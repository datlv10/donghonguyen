<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration22 extends AbstractMigration
{

    /**
     * 
     * - Bảng `shippings_carrier` thêm row `ghn` và `ghtk`
     * 
     */

    public function up()
    {
        $table = $this->table('shippings_carrier');
        
        // thêm row 'ghn'
        $ghn_row = $this->fetchRow('SELECT * FROM `shippings_carrier` WHERE `code` = "ghn"');
        if (empty($ghn_row)) {
            $save = $table->insert([
                'code'  => 'ghn',
                'name' => 'Giao hàng nhanh',
                'status' => 0
            ]);
            $table->saveData();
        }

        // thêm row 'ghtk'
        $ghtk_row = $this->fetchRow('SELECT * FROM `shippings_carrier` WHERE `code` = "ghtk"');
        if (empty($ghtk_row)) {
            $table->insert([
                'code'  => 'ghtk',
                'name' => 'Giao hàng tiết kiệm',
                'status' => 0
            ]);

            $table->saveData();
        }
    }

    public function down()
    {

    }
}
