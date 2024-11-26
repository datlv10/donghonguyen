<?php
declare(strict_types=1);

namespace Admin\View\Helper;

use Cake\View\Helper;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

class TemplateAdminHelper extends Helper
{
    public function getTypeBlockForDropdown()
    {
        $result = [
            __d('admin', 'san_pham') => [
                PRODUCT => __d('admin', 'danh_sach_san_pham'),
                PRODUCT_DETAIL => __d('admin', 'chi_tiet_san_pham'),
                CATEGORY_PRODUCT => __d('admin', 'danh_muc_san_pham'),
                BRAND_PRODUCT => __d('admin', 'thuong_hieu'),
                RATING => __d('admin', 'danh_gia'),
                TAB_PRODUCT => __d('admin', 'tab_san_pham')
            ],
            __d('admin', 'bai_viet') => [
                ARTICLE => __d('admin', 'danh_sach_bai_viet'),      
                ARTICLE_DETAIL => __d('admin', 'chi_tiet_bai_viet'),
                CATEGORY_ARTICLE => __d('admin', 'danh_muc_bai_viet'),
                TAB_ARTICLE => __d('admin', 'tab_bai_viet')
            ],
            __d('admin', 'he_thong') => [
                MENU => 'MENU',
                HTML => 'HTML',
                SLIDER => 'SLIDER',
                COMMENT => __d('admin', 'binh_luan')
            ],
        ];

        $plugins = TableRegistry::get('Plugins')->getList();
        if(empty($plugins[PRODUCT])){
            unset($result[__d('admin', 'san_pham')]);
        }
        return $result;
    }

    public function listTypePageTemplate()
    {
        $result = [];
        $result[__d('admin', 'loai_trang_dong_tinh_tuy_bien_cao')] = [
            NORMAL => __d('admin', 'trang_thuong'),
            PRODUCT => __d('admin', 'danh_sach_san_pham'),
            ARTICLE => __d('admin', 'danh_sach_bai_viet'),
            PRODUCT_DETAIL => __d('admin', 'chi_tiet_san_pham'),
            ARTICLE_DETAIL => __d('admin', 'chi_tiet_bai_viet')
        ];

        $result[__d('admin', 'loai_trang_he_thong')] = [
            HOME => __d('admin', 'trang_chu'),
            MEMBER => __d('admin', 'tai_khoan_thanh_vien'),
            ORDER => __d('admin', 'don_hang'),
            GENEALOGY => 'Phả đồ',
            TAG => __d('admin', 'the_bai_viet')
        ];

        $plugins = TableRegistry::get('Plugins')->getList();
        if(empty($plugins[PRODUCT])){
            unset($result[__d('admin', 'loai_trang_dong_tinh_tuy_bien_cao')][PRODUCT]);
            unset($result[__d('admin', 'loai_trang_dong_tinh_tuy_bien_cao')][PRODUCT_DETAIL]);

            unset($result[__d('admin', 'loai_trang_he_thong')][ORDER]);
        }

        return $result;
    }

    public function getListTypeBlock()
    {
        $list_type = $this->getTypeBlockForDropdown();
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
        $list_layout = Hash::combine(TableRegistry::get('TemplatesPage')->find()->where([
            'TemplatesPage.template_code' => CODE_TEMPLATE,
            'TemplatesPage.page_type' => LAYOUT
        ])->order('TemplatesPage.id ASC')->toArray(), '{n}.code', '{n}.name');

        $list_page = Hash::combine(TableRegistry::get('TemplatesPage')->find()->where([
            'TemplatesPage.template_code' => CODE_TEMPLATE,
            'TemplatesPage.page_type' => PAGE
        ])->order('TemplatesPage.id ASC')->toArray(), '{n}.code', '{n}.name');
        
        $result = [];
        $result[__d('admin', 'trang_bo_cuc')] = $list_layout;
        $result[__d('admin', 'trang_thuong')] = $list_page;

        return $result;
    }

    public function getListLayoutForDropdown()
    {
        $result = Hash::combine(TableRegistry::get('TemplatesPage')->find()->where([
            'TemplatesPage.template_code' => CODE_TEMPLATE,
            'TemplatesPage.page_type' => LAYOUT
        ])->order('TemplatesPage.id ASC')->toArray(), '{n}.code', '{n}.name');

        return $result;
    }

    public function getListPageForDropdown()
    {
        $result = Hash::combine(TableRegistry::get('TemplatesPage')->find()->where([
            'TemplatesPage.template_code' => CODE_TEMPLATE,
            'TemplatesPage.page_type' => PAGE
        ])->order('TemplatesPage.id ASC')->toArray(), '{n}.code', '{n}.name');

        return $result;
    }

    public function getListSortFieldOfProduct()
    {
        return [
            'position' => __d('admin', 'vi_tri'),
            'name' => __d('admin', 'ten'),
            'price' => __d('admin', 'gia'),
            'view' => __d('admin', 'luot_xem'),
            'like' => __d('admin', 'luot_thich'),
            'comment' => __d('admin', 'luot_binh_luan'),
            'featured' => __d('admin', 'noi_bat'),
            'created' => __d('admin', 'ngay_tao'),
            'updated' => __d('admin', 'ngay_cap_nhat')
        ];
    }

    public function getListSortFieldOfCategory()
    {
        return [
            'position' => __d('admin', 'vi_tri'),
            'name' => __d('admin', 'ten')            
        ];
    }

    public function getListSortFieldOfBrand()
    {
        return [
            'position' => __d('admin', 'vi_tri'),
            'name' => __d('admin', 'ten')            
        ];
    }

    public function getListTypeDataOfProduct()
    {
        return [            
            CATEGORY_PRODUCT => __d('admin', 'danh_muc_san_pham'),
            PRODUCT => __d('admin', 'san_pham'),
        ];
    }

    public function getListSortFieldOfArticle()
    {
        return [            
            'name' => __d('admin', 'ten'),
            'position' => __d('admin', 'vi_tri'),
            'view' => __d('admin', 'luot_xem'),
            'like' => __d('admin', 'luot_thich'),
            'comment' => __d('admin', 'luot_binh_luan'),
            'featured' => __d('admin', 'noi_bat'),
            'created' => __d('admin', 'ngay_tao'),
            'updated' => __d('admin', 'ngay_cap_nhat')
        ];
    }

    public function getListSortFieldOfComment()
    {
        return [
            'number_reply' => __d('admin', 'noi_bat'),
            'number_like' => __d('admin', 'luot_thich'),
            'created' => __d('admin', 'moi_nhat')
        ];
    }

    public function getListSortFieldOfRating()
    {
        return [
            'number_reply' => __d('admin', 'noi_bat'),            
            'created' => __d('admin', 'moi_nhat')
        ];
    }

    public function getTypeLoadBlockForDropdown()
    {
        return [            
            NORMAL => 'Normal',
            TIMEOUT => 'Timeout',
            SCROLL => 'Scroll',
            // ACTIVED => 'Actived'
        ];
    }

    public function getMoreFilterDataProduct()
    {
        return [            
            'featured' => __d('admin', 'noi_bat'),
            'discount' => __d('admin', 'khuyen_mai'),
        ];
    }

    public function getMoreFilterDataArticle()
    {
        return [            
            'featured' => __d('admin', 'noi_bat')
        ];
    }
}
