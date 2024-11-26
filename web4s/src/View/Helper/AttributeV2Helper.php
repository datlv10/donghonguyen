<?php
declare(strict_types=1);

namespace App\View\Helper;

use Cake\View\Helper;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

class AttributeV2Helper extends Helper
{   
    /** kiểm tra trạng thái áp dụng thuộc tính theo danh mục
     *      
     * 
     * {assign var = check_attribute_category value = $this->AttributeV2->checkApplyAttributeByCategory()}
     * 
    */
    public function checkApplyAttributeByCategory()
    {
        $settings = TableRegistry::get('Settings')->getSettingWebsite();
        $attributes_category = !empty($settings['attributes_category']) ? $settings['attributes_category'] : [];
        $result = !empty($attributes_category['status']) ? true : false;
        return $result;
    }

    /** Lấy danh sách thuộc tính được áp dụng trong admin
     * 
     * $category_id (*): ID danh mục(int)
     * $lang (*): Mã ngôn ngữ(string)  - ví dụ: 'en'| 'vi'
     *
     * 
     * {assign var = attributes_apply_category value = $this->AttributeV2->attributesApplyCategory({PAGE_CATEGORY_ID}, {LANGUAGE})}
     * 
    */
    public function attributesApplyCategory($category_id = null, $lang = null, $type = PRODUCT)
    {
        if(empty($type) || !in_array($type, [PRODUCT, ARTICLE, PRODUCT_ITEM])) return [];
        if(empty($lang)) $lang = TableRegistry::get('Languages')->getDefaultLanguage();

        $all_attributes = Hash::combine(TableRegistry::get('Attributes')->getAll($lang), '{n}.id', '{n}', '{n}.attribute_type');
        
        $attributes = !empty($all_attributes[$type]) ? $all_attributes[$type] : [];

        if(empty($attributes)) return [];

        $settings = TableRegistry::get('Settings')->getSettingWebsite();        
                
        $setting_category = [];
        switch($type){
            case PRODUCT:
                $setting_category = !empty($settings['attributes_category']) ? $settings['attributes_category'] : [];
            break;

            case ARTICLE:
                $setting_category = !empty($settings['article_attributes_category']) ? $settings['article_attributes_category'] : [];
            break;

            case PRODUCT_ITEM:
                $setting_category = !empty($settings['item_attributes_category']) ? $settings['item_attributes_category'] : [];
            break;
        }
        
        $status = !empty($setting_category['status']) ? true : false;
        // lấy option cho thuộc tính
        $all_options = Hash::combine(TableRegistry::get('AttributesOptions')->getAll(LANGUAGE), '{n}.id', '{n}', '{n}.attribute_id');
        foreach ($attributes as $attribute_id => $attribute) {
            $attributes[$attribute_id]['options'] = !empty($all_options[$attribute_id]) ? array_values($all_options[$attribute_id]) : [];
        }

        if(empty($status)) return $attributes;
        if(empty($category_id)) return [];
        
        $apply_attributes = !empty($setting_category['apply_attributes']) ? json_decode($setting_category['apply_attributes'], true) : [];
        $ids = !empty($apply_attributes[$category_id]) ? array_filter(explode(',', $apply_attributes[$category_id])) : [];

        // nếu không có cấu hình thì kiểm tra danh mục có phải danh mục con ko
        if(empty($ids)){
            $category_info = TableRegistry::get('Categories')->find()->where([
                'id' => $category_id
            ])->select([
                'id', 
                'parent_id', 
                'path_id'
            ])->first();
            
            $path_id = !empty($category_info['path_id']) ? $category_info['path_id'] : null;
            if(empty($category_info['parent_id']) || empty($path_id)) return [];

            $split_path = @array_values(array_filter(explode('|', $path_id)));

            $parent_id = !empty($split_path[0]) ? intval($split_path[0]) : null;
            if(empty($parent_id)) return [];
            
            $ids = !empty($apply_attributes[$parent_id]) ? array_filter(explode(',', $apply_attributes[$parent_id])) : [];
            if(empty($ids)) return [];
        }
        
        $result = [];
        foreach($attributes as $attribute_id => $attribute){
            $code = !empty($attribute['code']) ? $attribute['code'] : null;
            if(in_array($attribute_id, $ids) && empty($result[$attribute_id])){
                $result[$code] = $attribute;
            }
        }

        return $result;
    }

    /** Lấy danh sách thuộc tính
     * 
     * $lang (*): Mã ngôn ngữ(string)  - ví dụ: 'en'| 'vi'
     *
     * 
     * {assign var = attributes_apply_category value = $this->AttributeV2->getAllAttributes({LANGUAGE})}
     * 
    */
    public function getAllAttributes($lang = null){
        $result = null;
        $languages = TableRegistry::get('Languages')->getList();
        if(empty($lang) || empty($languages[$lang])) $lang = LANGUAGE;

        $all_attributes = TableRegistry::get('Attributes')->getAll(LANGUAGE);
        
        $all_options = Hash::combine(TableRegistry::get('AttributesOptions')->getAll(LANGUAGE), '{n}.id', '{n}', '{n}.attribute_id');

        if(empty($all_attributes)) return $result;

        foreach ($all_attributes as $k => $attribute) {
            $attribute_id = !empty($attribute['id']) ? $attribute['id'] : null;
            $all_attributes[$k]['options'] = !empty($all_options[$attribute_id]) ? array_values($all_options[$attribute_id]) : null;
        }

        $result = !empty($all_attributes) ? Hash::combine($all_attributes, '{n}.code', '{n}') : null;
        return $result;
    }
}
