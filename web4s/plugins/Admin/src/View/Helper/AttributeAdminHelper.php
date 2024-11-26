<?php
declare(strict_types=1);

namespace Admin\View\Helper;

use Cake\View\Helper;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

class AttributeAdminHelper extends Helper
{   
    public $helpers = ['Admin.UtilitiesAdmin'];

    public function generateInput($params = [], $lang = null)
    {
        $input_type = !empty($params['input_type']) ? $params['input_type'] : null;
        if(!in_array($input_type, Configure::read('ALL_ATTRIBUTE')) || empty($params['code'])){
            return;
        }
        $attribute_type = !empty($params['attribute_type']) ? $params['attribute_type'] : null;

        $value = !empty($params['value']) ? $params['value'] : null;
        if ($attribute_type == PRODUCT_ITEM && $input_type == MULTIPLE_SELECT) {
            $value = !empty($value) ? json_decode($value, true) : [];
        }

        $view_element = $input_type;
        $code = !empty($params['code']) ? $params['code'] : null;
        $data_input = [
            'code' => $code,
            'id' => !empty($params['id']) ? $params['id'] : $code,
            'name' => !empty($params['name']) ? $params['name'] : $code,
            'value' => $value,
            'label' => !empty($params['label']) ? $params['label'] : null,
            'has_image' => !empty($params['has_image']) ? 1 : 0,
            'required' => !empty($params['required']) ? 1 : 0,
            'options' =>!empty($params['options']) ? $params['options'] : [],
            'class' => !empty($params['class']) ? $params['class'] : null,
            'disabled' => !empty($params['disabled']) ? true : false
        ];

        if($input_type == SPECICAL_SELECT_ITEM){
            $view_element = MULTIPLE_SELECT_ITEM;
        }

        if($input_type == SPECICAL_SELECT_ITEM && !empty($data_input['has_image'])){
            $view_element = SINGLE_SELECT_ITEM;
        }

        return $this->_View->element('Admin.attribute/' . $view_element, $data_input);
    }

    public function getList($lang = null)
    {
        if(empty($lang)) return [];
        $all_attributes = Hash::combine(TableRegistry::get('Attributes')->getAll($lang), '{n}.id', '{n}.name');
        
        return !empty($all_attributes) ? $all_attributes : [];
    }

    public function getSpecialItem($lang = [])
    {
        if(empty($lang)) return [];

        $all_attributes = Hash::combine(TableRegistry::get('Attributes')->getAll($lang), '{n}.id', '{n}', '{n}.attribute_type');

        $list_attributes_special = [];
        if(!empty($all_attributes[PRODUCT_ITEM])){
            $list_attributes_special = Hash::combine(Collection($all_attributes[PRODUCT_ITEM])->filter(function ($item, $key, $iterator) {
                return $item['input_type'] == SPECICAL_SELECT_ITEM;
            })->toArray(),'{n}.id', '{n}.name');
        }
        
        return !empty($list_attributes_special) ? $list_attributes_special : [];
    }

    public function getListType()
    {
        $result = [
            PRODUCT => __d('admin', 'san_pham'),
            PRODUCT_ITEM => __d('admin', 'phien_ban_san_pham'),
            ARTICLE => __d('admin', 'bai_viet'),
            CATEGORY => __d('admin', 'danh_muc')
        ];

        return $result;
    }

    public function getListTypeInput($attribute_type = null)
    {
        $result = Configure::read('LIST_ATTRIBUTE_NORMAL');;

        if($attribute_type == PRODUCT_ITEM){
            $result = Configure::read('ATTRIBUTE_PRODUCT_ITEM');
        }

        return $result;
    }

    public function getAttributeByMainCategory($category_id = null, $type = null, $lang = null)
    {
        $result = TableRegistry::get('Attributes')->getAttributeByMainCategory($category_id, $type, $lang);
        return !empty($result) ? $result : [];
    }

    public function getSpecialAttributeItemByMainCategory($category_id = null, $lang = null)
    {
        $result = TableRegistry::get('Attributes')->getSpecialAttributeItemByMainCategory($category_id, $lang);
        return !empty($result) ? $result : [];
    }
}
