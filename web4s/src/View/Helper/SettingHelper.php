<?php
declare(strict_types=1);

namespace App\View\Helper;

use Cake\View\Helper;
use Cake\ORM\TableRegistry;

class SettingHelper extends Helper
{
    public function getConfigCacheView($code = null, $type = null, $block_info = [])
    {
        if(empty($code) || WEBSITE_MODE == DEVELOP) return [];
        
        $suffix = '_' . DEVICE . '_' . LANGUAGE . '_' . CURRENCY_CODE;
        $cache_key = $code . $suffix;
        
        $page_record_id = null;
        if(defined('PAGE_RECORD_ID') && !empty(PAGE_RECORD_ID)){
            $page_record_id = PAGE_RECORD_ID;
        }

        switch ($type) {
            case PAGE:
                $cache_key = 'page_' . $code . $suffix;
                if(defined('PAGE_RECORD_ID') && !empty(PAGE_RECORD_ID)){
                    $cache_key = 'page_' . $code . '_' . PAGE_RECORD_ID . $suffix;
                }
            break;

            case BLOCK:
                $cache_key = 'block_' . $code . $suffix;

                $config = !empty($block_info['config']) ? $block_info['config'] : [];
                $data_type = !empty($config['data_type']) ? $config['data_type'] : null;
                if(defined('PAGE_RECORD_ID') && !empty(PAGE_RECORD_ID) && $data_type == BY_URL){
                    $cache_key = 'block_' . $code . '_' . PAGE_RECORD_ID . $suffix;
                }
            break;

            case LAYOUT:
                $cache_key = 'layout_' . $code . '_' . DEVICE . '_' . LANGUAGE;
            break;
        }

        return [
            'cache' => [
                'config' => TEMPLATE,
                'key' => $cache_key
            ]
        ];
    }

    /** Lấy thông tin website
     * 
     * {assign var = data value = $this->Setting->getWebsiteInfo()}
     * 
    */
    public function getWebsiteInfo()
    {
        $settings = TableRegistry::get('Settings')->getSettingWebsite();
        $website_info = !empty($settings['website_info']) ? $settings['website_info'] : [];

        $website_info = TableRegistry::get('Settings')->formatDataWebsiteInfoByLang($website_info);
        $lang = LANGUAGE_DEFAULT;
        if(defined('LANGUAGE')){
            $lang = LANGUAGE;
        }
        $website_info = !empty($website_info[$lang]) ? $website_info[$lang] : [];

        if (!empty($website_info['sub_branch'])) {
            $website_info['sub_branch'] = json_decode($website_info['sub_branch'], true);
        }
        
        return $website_info;
    }

    /** Thông tin thiết lập website
     * $group*: nhóm thiết lập của website ví dụ: website_info | embed_code | point | affiliate
     * 
     * {assign var = data value = $this->Setting->getSettingWebsite('website_info')}
     * 
    */
    public function getSettingWebsite($group = null)
    {
        if(empty($group)) return [];
        $settings = TableRegistry::get('Settings')->getSettingWebsite();

        return !empty($settings[$group]) ? $settings[$group] : [];
    }

    /** Danh sách các plugin 
     * 
     * {assign var = data value = $this->Setting->getListPlugins()}
     * 
    */
    public function getListPlugins()
    {
        return TableRegistry::get('Plugins')->getList();
    }

    /** Kiểm tra xem có sử dụng sms brand 
     * 
     * {assign var = sms_usage value = $this->Setting->checkSmsBrandUsage()}
     * 
    */
    public function checkSmsBrandUsage()
    {
        $sms = $this->getSettingWebsite('sms_brandname');
        $default_partner = !empty($sms['default_partner']) ? $sms['default_partner'] : null;
        if(empty($default_partner)) return false;
        $sms_brandname = !empty($sms[$default_partner]) ? json_decode($sms[$default_partner], true) : [];
        
        if(empty($sms_brandname['status'])) return false;
        return true;
    }
}
