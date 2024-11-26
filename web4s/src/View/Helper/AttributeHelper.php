<?php
declare(strict_types=1);

namespace App\View\Helper;

use Cake\View\Helper;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

class AttributeHelper extends Helper
{   
    /** Lấy danh sách thuộc tính
     * 
     * $attribute_code (*): mã thuộc tính(string)
     * $lang (*): Mã ngôn ngữ(string)  - ví dụ: 'en'| 'vi'
     *
     * 
     * {assign var = data value = $this->Attribute->getListOptions('color', {LANGUAGE})}
     * 
    */
    public function getListOptions($attribute_code = null, $lang = null)
    {
        if(empty($attribute_code)) return [];

        if(empty($lang)){
            $lang = LANGUAGE;
        }        

        $attribute_info = TableRegistry::get('Attributes')->find()->where([
            'deleted' => 0,
            'code' => $attribute_code
        ])->select()->first();

        if(empty($attribute_info)) return [];        
        $attribute_id = !empty($attribute_info['id']) ? intval($attribute_info['id']) : null;

        $all_options = Hash::combine(TableRegistry::get('AttributesOptions')->getAll($lang), '{n}.id', '{n}.name', '{n}.attribute_id'); 
        $result = !empty($all_options[$attribute_id]) ? $all_options[$attribute_id] : [];
        
        return $result;
    }

    /** Lấy thông tin thuộc tính theo id
     * 
     * $attribute_id (*): id thuộc tính
     * $lang (*): ngôn ngữ
     *
     * 
     * {assign var = data value = $this->Attribute->getInfoAttribute($id)}
     * 
    */
    public function getInfoAttribute($id = null, $lang = null)
    {
        if(empty($id)) return [];

        if(empty($lang)){
            $lang = LANGUAGE;
        } 

        $attribute_info = TableRegistry::get('Attributes')->getDetailAttribute($id, $lang);
        
        return !empty($attribute_info) ? $attribute_info : [];
    }

    /** Lấy danh sách thuộc tính áp dụng cho danh mục
     * 
     * {PAGE_CATEGORY_ID} (*): id danh mục
     * $setting (*): Dữ liệu cấu hình thuộc tính áp dụng cho danh mục
     *
     * 
     * {assign var = data value = $this->Attribute->getListAttributesApply($setting, {PAGE_CATEGORY_ID})}
     * 
    */
    public function getListAttributesApply($setting = [], $category_id = null, $lang = null)
    {
        $result = [];
        $languages = TableRegistry::get('Languages')->getList();
        if(empty($lang) || empty($languages[$lang])) $lang = LANGUAGE;


        $status = !empty($setting['status']) ? intval($setting['status']) : null;
        $apply_attributes = !empty($setting['apply_attributes']) ? json_decode($setting['apply_attributes'], true) : [];
        $apply_attributes = !empty($apply_attributes[$category_id]) ? explode(',', $apply_attributes[$category_id]) : [];

        if(empty($status) || empty($apply_attributes)) return $result;

        $result = [];
        foreach ($apply_attributes as $key => $attribute_id) {
            $attribute_info = $this->getInfoAttribute($attribute_id, $lang);

            $attribute_type = !empty($attribute_info['attribute_type']) ? $attribute_info['attribute_type'] : null;
            $attribute_code = !empty($attribute_info['code']) ? $attribute_info['code'] : null;
            $attribute_name = !empty($attribute_info['AttributesContent']['name']) ? $attribute_info['AttributesContent']['name'] : null;

            if (!empty($attribute_code) && !empty($attribute_name) && !empty($attribute_type) && $attribute_type == PRODUCT_ITEM) {
                $result[$attribute_code] = $attribute_name;
            }
        }
        
        return $result;
    }

    /** Lấy danh sách thuộc tính
     * 
     * $lang (*): Mã ngôn ngữ(string)  - ví dụ: 'en'| 'vi'
     *
     * 
     * {assign var = data value = $this->Attribute->getListAttributes([], {LANGUAGE})}
     * 
    */
    public function getListAttributes($params = [], $lang = null)
    {
        $result = [];

        $languages = TableRegistry::get('Languages')->getList();
        if(empty($lang) || empty($languages[$lang])) $lang = LANGUAGE;
        $params[FILTER][LANG] = $lang;
        
        $result = TableRegistry::get('Attributes')->queryListAttributes($params)->toArray();
        
        return $result;
    }

    /** Lấy danh sách options thuộc tính
     * 
     * $lang (*): Mã ngôn ngữ(string)  - ví dụ: 'en'| 'vi'
     *
     * 
     * {assign var = data value = $this->Attribute->getAllAttributeOption({LANGUAGE})}
     * 
    */
    public function getAllAttributeOption($lang = null)
    {
        $languages = TableRegistry::get('Languages')->getList();
        if(empty($lang) || empty($languages[$lang])) $lang = LANGUAGE;

        return TableRegistry::get('AttributesOptions')->getAll($lang);
    }
}
