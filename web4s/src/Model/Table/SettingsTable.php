<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Core\Configure;
use Cake\Cache\Cache;
use Cake\Utility\Hash;

class SettingsTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('settings');
        $this->setPrimaryKey('id');

    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        return $validator;
    }

    public function getSettingWebsite()
    {
        $cache_key = SETTING . '_all';
        $result = Cache::read($cache_key);

        if(is_null($result)){
            $settings = TableRegistry::get('Settings')->find()->toArray();

            $result = [];
            if(!empty($settings)){
                foreach ($settings as $k => $setting) {
                    $group_setting = !empty($setting['group_setting']) ? trim($setting['group_setting']) : null;
                    $code = !empty($setting['code']) ? trim($setting['code']) : null;
                    if(empty($group_setting) || empty($code)) continue;

                    if(!isset($result[$group_setting])) $result[$group_setting] = [];
                    $result[$group_setting][$code] = !empty($setting['value']) ? $setting['value'] : null;
                }
            }
            Cache::write($cache_key, $result);
        }

        return $result;
    }

    public function getSettingAutoTranslate()
    {
        $settings = TableRegistry::get('Settings')->getSettingWebsite();
        $setting_language = !empty($settings['language']) ? $settings['language'] : null;
        $languages = TableRegistry::get('Languages')->getList();

        $result = false;
        if(!empty($setting_language['auto_translate']) && count($languages) > 1){
            $result = true;
        }
        return $result;
    }

    public function getSettingByGroup($group = null)
    {
        if(empty($group)) return [];

        $setting = TableRegistry::get('Settings')->find()->where(['group_setting' => $group])->toArray();
        $setting = Hash::combine($setting, '{n}.code', '{n}.value');
        return $setting;
    }

    public function getValueCommissionDiscountForPartner($customer_id = null, $exist_coupon = null)
    {
        $result = 0;
        if(empty($customer_id)) return $result;

        $customers_info = TableRegistry::get('Customers')->find()->where([
            'is_partner_affiliate' => 1,
            'status' => 1,
            'deleted' => 0,
            'id' => $customer_id
        ])->select(['id', 'level_partner_affiliate'])->first();
        if(empty($customers_info)) return $result;


        $settings = $this->getSettingWebsite();
        $affiliate_setting = !empty($settings['affiliate']) ? $settings['affiliate'] : [];

        $commissions = !empty($affiliate_setting['commissions']) ? json_decode($affiliate_setting['commissions'], true) : [];
        $commissions = Hash::combine($commissions, '{n}.key', '{n}');
        $level_partner_affiliate = !empty($customers_info['level_partner_affiliate']) ? intval($customers_info['level_partner_affiliate']) : 0;

        if(empty($commissions[$level_partner_affiliate])) return $result;

        $result = !empty($commissions[$level_partner_affiliate]['profit']) ? floatval($commissions[$level_partner_affiliate]['profit'])  : 0;
        if($exist_coupon && !empty($commissions[$level_partner_affiliate]['status_discount_sale'])) {
            $result = !empty($commissions[$level_partner_affiliate]['profit_sale']) ? floatval($commissions[$level_partner_affiliate]['profit_sale']) : 0;
        }

        return $result;
    }

    public function getValueCommissionDiscountForCustomer($exist_coupon = null){
        $result = 0;
        
        $settings = $this->getSettingWebsite();
        $affiliate_setting = !empty($settings['affiliate']) ? $settings['affiliate'] : [];

        $result = !empty($affiliate_setting['value_discount']) ? $affiliate_setting['value_discount'] : 0;

        if($exist_coupon && !empty($affiliate_setting['status_discount_sale'])) {
            $result = !empty($affiliate_setting['value_discount_sale']) ? $affiliate_setting['value_discount_sale'] : 0;
        }

        return $result;
    }

    public function formatDataWebsiteInfoByLang($data = [])
    {
        $languages = TableRegistry::get('Languages')->getList();  
        if(empty($data) || empty($languages)) return [];
        
        foreach ($languages as $lang => $val) {
            if (empty($lang)) continue;

            $result[$lang] = [
                'company_logo' => !empty($data['company_logo']) ? $data['company_logo'] : null,
                'favicon' => !empty($data['favicon']) ? $data['favicon'] : null,
                'address' => !empty($data[$lang . '_address']) ? $data[$lang . '_address'] : null,
                'website_name' => !empty($data[$lang . '_website_name']) ? $data[$lang . '_website_name'] : null,
                'company_name' => !empty($data[$lang . '_company_name']) ? $data[$lang . '_company_name'] : null,
                'copyright' => !empty($data[$lang . '_copyright']) ? $data[$lang . '_copyright'] : null,
                'email' => !empty($data[$lang . '_email']) ? $data[$lang . '_email'] : null,
                'hotline' => !empty($data[$lang . '_hotline']) ? $data[$lang . '_hotline'] : null,
                'phone' => !empty($data[$lang . '_phone']) ? $data[$lang . '_phone'] : null,
                'sub_branch' => !empty($data[$lang . '_sub_branch']) ? $data[$lang . '_sub_branch'] : null,
            ];
        }

        return $result;
    }
}