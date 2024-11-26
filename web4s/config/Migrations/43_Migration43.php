<?php
declare(strict_types=1);

use Migrations\AbstractMigration;
use Cake\Utility\Hash;

class Migration43 extends AbstractMigration
{

    /**
     * 
     * - bảng `settings` cập nhật lại thông tin logo và favicon
     */

    public function up()
    {

        // lấy thông tin cấu hình chung của Website
        $website_info = $this->fetchAll('SELECT `code`, `value` FROM `settings` WHERE `group_setting` = "website_info"');
        $website_info = Hash::combine($website_info, '{n}.code', '{n}.value');

        // lấy danh sách ngôn ngữ
        $languages = $this->fetchAll('SELECT `code` FROM `languages` WHERE `status` = 1');
        $languages = Hash::combine($languages, '{n}.code', '{n}.code');

        $lang = reset($languages);
        $data = [];
        if(!empty($website_info) && !empty($lang)){            
            $company_logo = !empty($website_info[$lang . '_company_logo']) ? $website_info[$lang . '_company_logo'] : null;
            $favicon = !empty($website_info[$lang . '_favicon']) ? $website_info[$lang . '_favicon'] : null;

            $data[] = [
                'group_setting' => 'website_info',
                'code' => 'company_logo',
                'value' => $company_logo
            ];

            $data[] = [
                'group_setting' => 'website_info',
                'code' => 'favicon',
                'value' => $favicon
            ];

            // xóa dữ liệu cũ
            $this->execute('DELETE FROM `settings` WHERE `group_setting` = "website_info" AND `code` = "'. $lang . '_company_logo' . '"');
            $this->execute('DELETE FROM `settings` WHERE `group_setting` = "website_info" AND `code` = "'. $lang . '_favicon' . '"');
        }

        // thêm dữ liệu mới
        if(!empty($data)){            
            $table = $this->table('settings');
            $table->insert($data)->saveData();
        }
    }

    public function down()
    {
   
    }
}
