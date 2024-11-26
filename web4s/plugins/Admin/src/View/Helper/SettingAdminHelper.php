<?php
declare(strict_types=1);

namespace Admin\View\Helper;

use Cake\View\Helper;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;

class SettingAdminHelper extends Helper
{   
    public function getWebsiteInfo($lang = LANGUAGE_ADMIN)
    {
        $settings = TableRegistry::get('Settings')->getSettingWebsite();
        $settings_website_info = !empty($settings['website_info']) ? $settings['website_info'] : [];
        
        $website_info = TableRegistry::get('Settings')->formatDataWebsiteInfoByLang($settings_website_info);
        $website_info = !empty($website_info[$lang]) ? $website_info[$lang] : [];

        if (!empty($website_info['sub_branch'])) {
            $website_info['sub_branch'] = json_decode($website_info['sub_branch'], true);
        }
        
        return $website_info;
    }

    public function getPointToMoneyInfo()
    {
        $settings = TableRegistry::get('Settings')->getSettingWebsite();

        $point_setting_info = !empty($settings['point']) ? $settings['point'] : 0;
        $point_tomoney = !empty($point_setting_info['point_to_money']) ? intval($point_setting_info['point_to_money']) : 0;
        
        return $point_tomoney;
    }
}
