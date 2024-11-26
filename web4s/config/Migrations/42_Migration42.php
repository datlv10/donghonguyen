<?php
declare(strict_types=1);

use Migrations\AbstractMigration;
use Cake\Utility\Hash;

class Migration42 extends AbstractMigration
{

    /**
     * 
     * - bảng `settings` cập nhật thông tin chung
     */

    public function up()
    {
        // lấy thông tin cấu hình chung của Website
        $website_info = $this->fetchAll('SELECT `code`, `value` FROM `settings` WHERE `group_setting` = "website_info"');
        $website_info = Hash::combine($website_info, '{n}.code', '{n}.value');

        // lấy danh sách ngôn ngữ
        $languages = $this->fetchAll('SELECT `code` FROM `languages` WHERE `status` = 1');
        $languages = Hash::combine($languages, '{n}.code', '{n}.code');

        $data = [];
        if(!empty($website_info) && !empty($languages)){            
            foreach($languages as $lang){
                if(empty($lang)) continue;

                foreach($website_info as $code => $value){
                    $data[] = [
                        'group_setting' => 'website_info',
                        'code' => $lang . '_' . $code,
                        'value' => $value
                    ];
                }
            }
        }

        // thêm dữ liệu mới
        if(!empty($data)){
            // xóa dữ liệu cũ
            $this->execute('DELETE FROM `settings` WHERE `group_setting` = "website_info"');

            $table = $this->table('settings');
            $table->insert($data)->saveData();
        }
    }

    public function down()
    {
   
    }
}
