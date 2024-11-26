<?php
declare(strict_types=1);

use Migrations\AbstractMigration;
use Cake\Utility\Hash;

class Migration44 extends AbstractMigration
{

    /**
     * 
     * - bảng `settings` cập nhật thêm cấu hình dịch tự động đa ngôn ngữ
     */

    public function up()
    {
        // lấy thông tin cấu hình ngôn ngữ
        $language_setting = $this->fetchAll('SELECT `code`, `value` FROM `settings` WHERE `group_setting` = "language" AND `code` = "auto_translate"');
        $language_setting = Hash::combine($language_setting, '{n}.code', '{n}.value');

        // nếu chưa có bản ghi -> thêm cấu hình dịch tự động
        if(!isset($language_setting['auto_translate'])){
            $data = [
                'group_setting' => 'language',
                'code' => 'auto_translate',
                'value' => 1
            ];
            $table = $this->table('settings');
            $table->insert($data)->saveData();
        }
    }

    public function down()
    {
   
    }
}
