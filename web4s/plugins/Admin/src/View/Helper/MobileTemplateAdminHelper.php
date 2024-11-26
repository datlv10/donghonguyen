<?php
declare(strict_types=1);

namespace Admin\View\Helper;

use Cake\View\Helper;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

class MobileTemplateAdminHelper extends Helper
{
    public function getTypeMobileBlockForDropdown()
    {
        $result = [
            __d('admin', 'san_pham') => [
                PRODUCT => __d('admin', 'danh_sach_san_pham'),
                PRODUCT_DETAIL => __d('admin', 'chi_tiet_san_pham'),
                CATEGORY_PRODUCT => __d('admin', 'danh_muc_san_pham'),
                API_RATING => __d('admin', 'danh_gia'),
                API_COMMENT => __d('admin', 'binh_luan'),
                API_VIEWED => __d('admin', 'san_pham_da_xem'),
                TAB_PRODUCT => __d('admin', 'tab_san_pham')
            ],
             __d('admin', 'bai_viet') => [
                ARTICLE => __d('admin', 'danh_sach_bai_viet'),      
                ARTICLE_DETAIL => __d('admin', 'chi_tiet_bai_viet'),
                CATEGORY_ARTICLE => __d('admin', 'danh_muc_bai_viet'),
                TAB_ARTICLE => __d('admin', 'tab_bai_viet'),
            ],
            __d('admin', 'he_thong') => [
                TEXT => 'TEXT',
                IMAGE => 'IMAGE',
                SLIDER => 'SLIDER',
                SEARCH_BOX => __d('admin', 'tim_kiem')
            ],
        ];

        $plugins = TableRegistry::get('Plugins')->getList();
        if(empty($plugins[PRODUCT])){
            unset($result[__d('admin', 'san_pham')]);
        }
        return $result;
    }

    public function getListTypeMobileBlock()
    {
        $list_type = $this->getTypeMobileBlockForDropdown();
        if(empty($list_type)) return [];

        $result = [];
        foreach ($list_type as $k => $item) {
            if(is_array ($item)){
                foreach ($item as $key => $name) {
                    $result[$key] = $name;
                }
            }else{
                $result[$k] = $item;
            }
        }
        return $result;
    }

    public function getAllPageForDropdown()
    {
        return Hash::combine(TableRegistry::get('MobileTemplatePage')->find()->where([
            'MobileTemplatePage.template_code' => CODE_MOBILE_TEMPLATE
        ])->order('MobileTemplatePage.id ASC')->toArray(), '{n}.code', '{n}.name');    
    }

    public function listTypePageTemplate()
    {
        $result = [
            HOME => __d('admin', 'trang_chu'),
            NORMAL => __d('admin', 'trang_thuong'),
            SEARCH => __d('admin', 'trang_tim_kiem'),
            PRODUCT => __d('admin', 'danh_sach_san_pham'),
            PRODUCT_DETAIL => __d('admin', 'chi_tiet_san_pham'),
            CATEGORY_PRODUCT => __d('admin', 'danh_muc_san_pham'),
            ARTICLE => __d('admin', 'danh_sach_bai_viet'),
            ARTICLE_DETAIL => __d('admin', 'chi_tiet_bai_viet')
        ];

        return $result;
    }

    public function listAlign()
    {
        $result = [
            LEFT => __d('admin', 'trai'),
            RIGHT => __d('admin', 'phai'),
            CENTER => __d('admin', 'giua')
        ];

        return $result;
    }

    public function listFormatItem()
    {
        $result = [
            'default' => __d('admin', 'mac_dinh'),
            'scroll' => 'Scroll',
            'slider' => 'Slider'
        ];

        return $result;
    }

    public function listFormatItemSlider()
    {
        $result = [
            'slider' => 'Slider',            
            'scroll' => 'Scroll',
            'default' => __d('admin', 'mac_dinh')            
        ];

        return $result;
    }

    public function listElementProduct()
    {
        $result = [
            'item_product_price' => 'ITEM_PRODUCT_PRICE',
            'item_product_name_price' => 'ITEM_PRODUCT_NAME_PRICE',
        ];

        return $result;
    }

    public function listStyleView()
    {
        $result = [
            'default' => __d('admin', 'mac_dinh'),
            'super_sale' => 'SUPER_SALE'
        ];

        return $result;
    }
}
