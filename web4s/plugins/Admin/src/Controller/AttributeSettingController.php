<?php

namespace Admin\Controller;

use Admin\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Utility\Hash;
use Cake\Http\Response;
use Cake\ORM\Query;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;

class AttributeSettingController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function index(){
        $this->set('title_for_layout', __d('admin', 'thiet_lap_thuoc_tinh_theo_danh_muc'));
    }

    public function configByCategory()
    {       
        $settings = TableRegistry::get('Settings')->getSettingWebsite();

        // cấu hình thuộc tính theo danh mục sản phẩm
        $product_setting = !empty($settings['attributes_category']) ? $settings['attributes_category'] : [];

        $apply_by_product = [];
        if(!empty($product_setting['apply_attributes'])){

            $apply_by_product = !empty($product_setting['apply_attributes']) ? json_decode($product_setting['apply_attributes'], true) : [];

            if(!empty($apply_by_product)){
                foreach($apply_by_product as $k => $item){
                    $apply_by_product[$k] = !empty($item) ? array_filter(explode(',', $item)) : [];
                }
            }

            $product_setting['apply_attributes'] = $apply_by_product;
        }

        $product_setting['apply_attributes'] = $apply_by_product;
        $product_setting['categories_selected'] = !empty($apply_by_product) ? array_keys($apply_by_product) : [];

        // cấu hình thuộc tính theo danh mục bài viết
        $article_setting = !empty($settings['article_attributes_category']) ? $settings['article_attributes_category'] : [];

        $apply_by_article = [];
        if(!empty($article_setting['apply_attributes'])){

            $apply_by_article = !empty($article_setting['apply_attributes']) ? json_decode($article_setting['apply_attributes'], true) : [];

            if(!empty($apply_by_article)){
                foreach($apply_by_article as $k => $item){
                    $apply_by_article[$k] = !empty($item) ? array_filter(explode(',', $item)) : [];
                }
            }

            $article_setting['apply_attributes'] = $apply_by_article;
        }

        $article_setting['apply_attributes'] = $apply_by_article;
        $article_setting['categories_selected'] = !empty($apply_by_article) ? array_keys($apply_by_article) : [];

        // cấu hình phiên bản theo danh mục sản phâm
        $product_item_setting = !empty($settings['item_attributes_category']) ? $settings['item_attributes_category'] : [];
        $apply_by_product_item = [];
        if(!empty($product_item_setting['apply_attributes'])){

            $apply_by_product_item = !empty($product_item_setting['apply_attributes']) ? json_decode($product_item_setting['apply_attributes'], true) : [];

            if(!empty($apply_by_product_item)){
                foreach($apply_by_product_item as $k => $item){
                    $apply_by_product_item[$k] = !empty($item) ? array_filter(explode(',', $item)) : [];
                }
            }

            $product_item_setting['apply_attributes'] = $apply_by_product_item;
        }

        $product_item_setting['apply_attributes'] = $apply_by_product_item;
        $product_item_setting['categories_selected'] = !empty($apply_by_product_item) ? array_keys($apply_by_product_item) : [];

        // cấu hình thương hiệu theo danh mục sản phâm
        $brand_setting = !empty($settings['brands_category']) ? $settings['brands_category'] : [];

        $apply_by_brands = [];
        if(!empty($brand_setting['apply_brands'])){

            $apply_by_brands = !empty($brand_setting['apply_brands']) ? json_decode($brand_setting['apply_brands'], true) : [];

            if(!empty($apply_by_brands)){
                foreach($apply_by_brands as $k => $item){
                    $apply_by_brands[$k] = !empty($item) ? array_filter(explode(',', $item)) : [];
                }
            }

            $brand_setting['apply_brands'] = $apply_by_brands;
        }

        $brand_setting['apply_brands'] = $apply_by_brands;
        $brand_setting['categories_selected'] = !empty($apply_by_brands) ? array_keys($apply_by_brands) : [];

        $this->set('brand_setting', $brand_setting);
        $this->set('product_setting', $product_setting);
        $this->set('article_setting', $article_setting);
        $this->set('product_item_setting', $product_item_setting);
        $this->css_page = [
            '/assets/css/pages/todo/todo.css',
            '/assets/plugins/global/lightbox/lightbox.css',
            '/assets/css/pages/wizard/wizard-2.css'
        ];
        $this->js_page = [
            '/assets/js/pages/attributes_setting.js',
            '/assets/plugins/global/lightbox/lightbox.min.js'

        ];
        $this->set('path_menu', 'setting');
        $this->set('title_for_layout', __d('admin', 'thuoc_tinh_ap_dung_theo_danh_muc'));    
    }

    public function loadAttributesByCategory()
    {
        $this->viewBuilder()->enableAutoLayout(false);

        $lang = $this->lang;
        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        $category_id = !empty($data['category_id']) ? intval($data['category_id']) : null;
        $type = !empty($data['type']) ? $data['type'] : null;
        if(empty($category_id) || empty($type) || !in_array($type, [PRODUCT, ARTICLE, PRODUCT_ITEM, BRAND])) die;

        // lấy danh sách thuộc tính theo loại
        $attributes = Hash::combine(TableRegistry::get('Attributes')->getAll($lang), '{n}.id', '{n}', '{n}.attribute_type');

        $attributes = !empty($attributes[$type]) ? $attributes[$type] : [];

        // lấy cấu hìnhh thuộc tính theo loại danh mục
        $settings = TableRegistry::get('Settings')->getSettingWebsite();

        $attributes_selected = [];
        switch($type){
            case PRODUCT:
                $setting = !empty($settings['attributes_category']) ? $settings['attributes_category'] : [];
                $apply_attributes = !empty($setting['apply_attributes']) ? json_decode($setting['apply_attributes'], true) : [];

                if(!empty($apply_attributes)){
                    foreach($apply_attributes as $k => $item){
                        $apply_attributes[$k] = !empty($item) ? array_filter(explode(',', $item)) : [];
                    }
                }

            break;

            case PRODUCT_ITEM:
                $setting = !empty($settings['item_attributes_category']) ? $settings['item_attributes_category'] : [];
                $apply_attributes = !empty($setting['apply_attributes']) ? json_decode($setting['apply_attributes'], true) : [];

                if(!empty($apply_attributes)){
                    foreach($apply_attributes as $k => $item){
                        $apply_attributes[$k] = !empty($item) ? array_filter(explode(',', $item)) : [];
                    }
                }

            break;

            case ARTICLE:
                    $setting = !empty($settings['article_attributes_category']) ? $settings['article_attributes_category'] : [];
                    $apply_attributes = !empty($setting['apply_attributes']) ? json_decode($setting['apply_attributes'], true) : [];

                    if(!empty($apply_attributes)){
                        foreach($apply_attributes as $k => $item){
                            $apply_attributes[$k] = !empty($item) ? array_filter(explode(',', $item)) : [];
                        }
                    }
            break;

            case BRAND:
                $brands = TableRegistry::get('Brands')->getListBrands($lang);
                $attributes = !empty($brands) ? $brands : [];
                $format_brand = [];
                if(!empty($attributes)) {
                    foreach ($attributes as $id_attr => $name) {
                        $format_brand[] = [
                            'id' => $id_attr,
                            'name' => $name
                        ];
                    }
                }

                $attributes = $format_brand;

                $setting = !empty($settings['brands_category']) ? $settings['brands_category'] : [];
                $apply_attributes = !empty($setting['apply_brands']) ? json_decode($setting['apply_brands'], true) : [];

                if(!empty($apply_attributes)){
                    foreach($apply_attributes as $k => $item){
                        $apply_attributes[$k] = !empty($item) ? array_filter(explode(',', $item)) : [];

                    }
                }

            break;
        }

        $attributes_selected = !empty($apply_attributes[$category_id]) ? $apply_attributes[$category_id] : [];
        $this->set('attributes', $attributes);
        $this->set('attributes_selected', $attributes_selected);

        $this->render('element_attributes');
    }
}