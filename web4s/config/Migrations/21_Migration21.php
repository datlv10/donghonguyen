<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class Migration21 extends AbstractMigration
{

    /**
     * - Bảng `mobile_template_page` thêm column `config`
     * 
     * - Bảng `email_token` thêm column `phone`
     * 
     * - Bảng `shippings` thêm column `carrier_shipping_fee`
     * 
     * - Bảng `email_templates` thêm row `CHANGE_VERIFY`
     * 
     */

    public function up()
    {        
        if (!$this->table('mobile_template_page')->hasColumn('config')) {
            $this->table('mobile_template_page')->addColumn('config', 'text', [
                'after' => 'type',
                'null' => true
            ])->update();
        }

        if (!$this->table('email_token')->hasColumn('phone')) {
            $this->table('email_token')->addColumn('phone', 'string', [
                'limit' => 20,
                'after' => 'email',
                'null' => true
            ])->update();
        }

        if (!$this->table('shippings')->hasColumn('carrier_shipping_fee')) {
            $query = "ALTER TABLE `shippings` ADD `carrier_shipping_fee` decimal(15, 2);";
            $this->execute($query);
        }


        // thêm row 'CHANGE_VERIFY' trong bảng `email_templates`
        $row = $this->fetchRow('SELECT * FROM `email_templates` WHERE `code` = "CHANGE_VERIFY"');
        if (empty($row)) {
            $this->table('email_templates')->insert([
                'code'  => 'CHANGE_VERIFY',
                'name' => 'Xác nhận thay đổi thông tin',
                'title_email' => 'Xác nhận thay đổi thông tin',
                'template' => 'change_verify.tpl'
            ]);

            $this->table('email_templates')->saveData();
        }


    }

    public function down()
    {

    }
}
