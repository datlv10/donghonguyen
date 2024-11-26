<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration9 extends AbstractMigration
{

    /**
     * 
     * - thêm  row 'mobile_app' và 'promotion' trong bảng `plugins`
     * 
     * - xóa column `point` bảng `customers`
     * 
     * - bảng `shippings`
     *      + xóa field `branch_id`
     * 
     */

    public function up()
    {
        $plugins_table = $this->table('plugins');

        // thêm row 'mobile_app' trong bảng `plugins`        
        $row = $this->fetchRow('SELECT * FROM `plugins` WHERE `code` = "mobile_app"');
        if (empty($row)) {
            $plugins_table->insert([
                'code'  => 'mobile_app',
                'name' => 'Mobile App',
                'status' => 0
            ]);

            $plugins_table->saveData();
        }

        // thêm row 'promotion' trong bảng `plugins`        
        $row = $this->fetchRow('SELECT * FROM `plugins` WHERE `code` = "promotion"');
        if (empty($row)) {
            $plugins_table->insert([
                'code'  => 'promotion',
                'name' => 'Quản lý khuyến mãi',
                'status' => 0
            ]);

            $plugins_table->saveData();
        }


        // xóa column `branch_id` trong bảng `customers`
        if ($this->table('customers')->hasColumn('point')) {            
            $this->table('customers')->removeColumn('point')->save();
        }

        // xóa column `branch_id` trong bảng `shippings`
        if ($this->table('shippings')->hasColumn('branch_id')) {            
            $this->table('shippings')->removeColumn('branch_id')->save();
        }
    }

    public function down()
    {

    }
}
