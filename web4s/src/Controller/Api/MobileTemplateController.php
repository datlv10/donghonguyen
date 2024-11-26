<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\Api\AppController;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\ORM\TableRegistry;
use Cake\Core\Exception\Exception;
use Cake\Utility\Hash;
use Cake\I18n\Time;

class MobileTemplateController extends AppController {

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        if(!defined('CODE_MOBILE_TEMPLATE')){
            $this->responseJson([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_giao_dien')]);
        }
    }

    public function settingInfo()
    {
        $data = $this->data_bearer;

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('template', 'phuong_thuc_khong_hop_le')]);
        }

        $app_info = TableRegistry::get('MobileApp')->getMobileAppDefault();
        $template_info = TableRegistry::get('MobileTemplate')->getTemplateDefault();

        $settings_website = TableRegistry::get('Settings')->getSettingWebsite();
        $settings_website_info = !empty($settings_website['website_info']) ? $settings_website['website_info'] : [];

        $website_info = TableRegistry::get('Settings')->formatDataWebsiteInfoByLang($settings_website_info);
        $website_info = !empty($settings_website_info[LANGUAGE]) ? $settings_website_info[LANGUAGE] : [];

        $config_app = !empty($app_info['config']) ? json_decode($app_info['config'], true) : [];
        $config_vfone = !empty($config_app['vphone']) ? $config_app['vphone'] : [];
        $config_comment = !empty($config_app['comment']) ? $config_app['comment'] : [];
        $config_social_login = !empty($config_app['social_login']) ? $config_app['social_login'] : [];
        $config_social = !empty($config_app['social']) ? $config_app['social'] : [];
        $config_contact = !empty($config_app['contact']) ? $config_app['contact'] : [];
        $config_momo = !empty($config_app['momo']) ? $config_app['momo'] : [];
        $settings = [
            'website_info' => !empty($website_info) ? $website_info : [],
            'website_mode' => !empty($settings_website['website_mode']) ? $settings_website['website_mode'] : [],
            'product' => !empty($settings_website['product']) ? $settings_website['product'] : [],
            'customer' => !empty($settings_website['customer']) ? $settings_website['customer'] : [],
            'vfone' => [
                'vfone_hotline_name' => !empty($config_vfone['vfone_hotline_name']) ? $config_vfone['vfone_hotline_name'] : null,
                'vfone_domain' => !empty($config_vfone['vfone_domain']) ? $config_vfone['vfone_domain'] : null,
                'vfone_username' => !empty($config_vfone['vfone_username']) ? $config_vfone['vfone_username'] : null,
                'vfone_password' => !empty($config_vfone['vfone_password']) ? $config_vfone['vfone_password'] : null,
                'vfone_port' => !empty($config_vfone['vfone_port']) ? $config_vfone['vfone_port'] : null
            ],
            'comment' => [
                'awaiting_approval' => !empty($config_comment['awaiting_approval']) ? $config_comment['awaiting_approval'] : null,
                'max_upload' => !empty($config_comment['max_upload']) ? $config_comment['max_upload'] : null
            ],
            'social' => [
                'youtube' => !empty($config_social['youtube']) ? $config_social['youtube'] : null,
                'facebook' => !empty($config_social['facebook']) ? $config_social['facebook'] : null,
                'instagram' => !empty($config_social['instagram']) ? $config_social['instagram'] : null
            ],
            'point' => [
                'point_to_money' => !empty($settings_website['point']['point_to_money']) ? intval($settings_website['point']['point_to_money']) : null
            ],
            'contact' => [
                'phone' => !empty($config_contact['phone']) ? $config_contact['phone'] : null,
                'zalo' => !empty($config_contact['zalo']) ? $config_contact['zalo'] : null
            ],
            'momo' => [
                'momo_merchant_id' => !empty($config_momo['momo_merchant_id']) ? $config_momo['momo_merchant_id'] : null,
                'momo_partner_code' => !empty($config_momo['momo_partner_code']) ? $config_momo['momo_partner_code'] : null,
                'momo_merchant_name' => !empty($config_momo['momo_merchant_name']) ? $config_momo['momo_merchant_name'] : null,
                'momo_public_key' => !empty($config_momo['momo_public_key']) ? $config_momo['momo_public_key'] : null,
                'momo_secret_key' => !empty($config_momo['momo_secret_key']) ? $config_momo['momo_secret_key'] : null
            ],
        ];

        $result = [
            'app_id' => !empty($app_info['app_id']) ? $app_info['app_id'] : null,
            'template' => [
                'code' => !empty($template_info['code']) ? $template_info['code'] : null,
                'name' => !empty($template_info['name']) ? $template_info['name'] : null,
                'config' => !empty($template_info['config']) ? json_decode($template_info['config'], true) : [],
            ],
            'setting' => $settings,
            'images' => !empty($template_info['images']) ? json_decode($template_info['images'], true) : [],
            'text' => !empty($template_info['text']) ? json_decode($template_info['text'], true) : []
        ];

        $this->responseApi([
            CODE => SUCCESS,
            DATA => $result
        ]);
    }

    public function pageInfo()
    {
        $data_bearer = $this->data_bearer;
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('template', 'phuong_thuc_khong_hop_le')]);
        }

        $page_type = !empty($data_bearer['page_type']) ? $data_bearer['page_type'] : null;
        $category_id = !empty($data_bearer['category_id']) ? intval($data_bearer['category_id']) : null;
        $product_id = !empty($data_bearer['product_id']) ? intval($data_bearer['product_id']) : null;
        $article_id = !empty($data_bearer['article_id']) ? intval($data_bearer['article_id']) : null;
        $block_code = !empty($data_bearer['block_code']) ? $data_bearer['block_code'] : null;
        $page_code = !empty($data_bearer['code']) ? $data_bearer['code'] : null;
        if(empty($page_type)){
            $this->responseErrorApi([
                MESSAGE => __d('template', 'tham_so_api_chua_hop_le')
            ]);
        }

        $result = [];

        $table = TableRegistry::get('MobileTemplatePage');
        $row_table = TableRegistry::get('MobileTemplateRow');
        
        $condition_page_info = ['type' => $page_type];
        if(!empty($page_code)) {
            $condition_page_info['code'] = $page_code;
        }
        $page_info = $table->getInfoPage($condition_page_info);

        if(empty($page_info)){
            $this->responseErrorApi([CODE => SUCCESS]);
        }

        if(!defined('PAGE_TYPE')){
            define('PAGE_TYPE', $page_type);
        }

        if(!defined('PAGE_RECORD_ID') && !empty($category_id)){
            define('PAGE_RECORD_ID', $category_id);
        }

        if(!defined('PAGE_RECORD_ID') && !empty($product_id)){
            define('PAGE_RECORD_ID', $product_id);
        }

        if(!defined('PAGE_RECORD_ID') && !empty($article_id)){
            define('PAGE_RECORD_ID', $article_id);
        }

        $page_code = !empty($page_info['code']) ? $page_info['code'] : null;

        if(!empty($block_code)){
            $block_info = TableRegistry::get('MobileTemplateBlock')->getInfoBlock($block_code);
            $rows = [$block_info];
        }else{
            $rows = $row_table->getStructureRowOfPage($page_code);
        }        
        
        if(!empty($rows)){
            $block_component = $this->loadComponent('Block');
            foreach($rows as $block_info){
                $type = !empty($block_info['type']) ? $block_info['type'] : null;
                $status = !empty($block_info['status']) ? 1 : 0;
                if(empty($type) || empty($status)) continue;

                $config = !empty($block_info['config']) ? $block_info['config'] : [];
                $config_layout = !empty($config['layout']) ? $config['layout'] : null;

                if(!empty($config_layout['countdown_timer'])){
                    $time = Time::createFromFormat('d/m/Y - H:i', $config_layout['countdown_timer'], null);
                    $time = $time->format('Y-m-d H:i:s');
                    $config_layout['countdown_timer'] = strtotime($time);
                }
                
                $config_data = !empty($config['data']) ? $config['data'] : null;

                $data = [];
                switch($type){
                    case PRODUCT:
                        $block = $block_info;
                        $block['config'] = $config_data;

                        $params_url_filter = [];
                        if(!empty($data_bearer['params']) && $this->loadComponent('Utilities')->isJson($data_bearer['params'])) {
                            $params_url_filter = json_decode($data_bearer['params'], true);
                        }
                        $data = $block_component->getDataBlock($block, $params_url_filter);

                        // format data object to array
                        $products = !empty($data[DATA]) ? $data[DATA] : [];
                        if(!empty($products)){
                            foreach($products as $k => $product){
                                $attributes_item_apply = !empty($product['attributes_item_apply']) ? array_values($product['attributes_item_apply']) : [];
                                if(!empty($attributes_item_apply)){
                                    foreach($attributes_item_apply as $k_attribute_item => $attribute_item){
                                        $attributes_item_apply[$k_attribute_item]['options'] = !empty($attribute_item['options']) ? array_values($attribute_item['options']) : [];
                                    }
                                }

                                $items = !empty($product['items']) ? $product['items'] : [];
                                if(!empty($items)){
                                    foreach($items as $k_item => $item){
                                        $items[$k_item]['attributes'] = !empty($item['attributes']) ? array_values($item['attributes']) : [];
                                    }
                                }

                                $products[$k]['attributes_item_apply'] = $attributes_item_apply;
                                $products[$k]['attributes_item_special'] = !empty($product['attributes_item_special']) ? array_values($product['attributes_item_special']) : [];
                                $products[$k]['items'] = $items;
                                $products[$k]['categories'] = !empty($product['categories']) ? array_values($product['categories']) : [];
                                $products[$k]['all_images'] = !empty($product['all_images']) ? array_values($product['all_images']) : [];
                                
                            }
                        }

                        $data[DATA] = $products;
                    break;

                    case PRODUCT_DETAIL:
                        $block = $block_info;
                        $block['config'] = $config_data;

                        $data = $block_component->getDataBlock($block);

                        $product = !empty($data[DATA]) ? $data[DATA] : [];
                        if(!empty($product)){
                            $attributes_item_apply = !empty($product['attributes_item_apply']) ? array_values($product['attributes_item_apply']) : [];
                            if(!empty($attributes_item_apply)){
                                foreach($attributes_item_apply as $k_attribute_item => $attribute_item){
                                    $attributes_item_apply[$k_attribute_item]['options'] = !empty($attribute_item['options']) ? array_values($attribute_item['options']) : [];
                                }
                            }


                            $items = !empty($product['items']) ? $product['items'] : [];
                            if(!empty($items)){
                                foreach($items as $k_item => $item){
                                    $items[$k_item]['attributes'] = !empty($item['attributes']) ? array_values($item['attributes']) : [];
                                }
                            }

                            $product['attributes_item_apply'] = $attributes_item_apply;
                            $product['attributes_item_special'] = !empty($product['attributes_item_special']) ? array_values($product['attributes_item_special']) : [];
                            $product['items'] = $items;
                            $product['categories'] = !empty($product['categories']) ? array_values($product['categories']) : [];
                            $product['all_images'] = !empty($product['all_images']) ? array_values($product['all_images']) : [];
                        }

                        $data[DATA] = $product;
                    break;

                    case ARTICLE:
                        $block = $block_info;
                        $block['config'] = $config_data;

                        $params_url_filter = [];
                        if(!empty($data_bearer['params']) && $this->loadComponent('Utilities')->isJson($data_bearer['params'])) {
                            $params_url_filter = json_decode($data_bearer['params'], true);
                        }
                        $data = $block_component->getDataBlock($block, $params_url_filter);
                        $articles = [];
                        foreach ($data[DATA] as $k => $article) {
                            unset($article['attributes']);
                            $articles[$k] = $article;
                            $articles[$k]['categories'] = array_values($article['categories']);
                        }

                        $data[DATA] = $articles;
                    break;

                    case ARTICLE_DETAIL:
                        $block = $block_info;
                        $block['config'] = $config_data;

                        $data = $block_component->getDataBlock($block);

                        $data[DATA] = !empty($data[DATA]) ? $data[DATA] : [];
                    break;

                    case CATEGORY_PRODUCT:
                    case CATEGORY_ARTICLE:
                        $block = $block_info;
                        $block['config'] = $config_data;    
           
                        $data = $block_component->getDataBlock($block);

                        // format data object to array
                        $categories = !empty($data[DATA]) ? array_values($data[DATA]) : [];
                        if(!empty($categories)){
                            foreach($categories as $k => $category){
                                $categories[$k]['children'] = !empty($category['children']) ? array_values($category['children']) : [];
                                if(!empty($type) && $type == CATEGORY_PRODUCT) {
                                    $categories[$k]['product_count'] = $this->countPostByCatId(intval($category['id']), PRODUCT);
                                    
                                    if(empty($categories[$k]['children'])) continue;
                                    foreach ($categories[$k]['children'] as $k_child => $category_child) {
                                        $categories[$k]['children'][$k_child]['product_count'] = $this->countPostByCatId(intval($category_child['id']), PRODUCT);
                                    }
                                }
                            }
                        }
                        
                        $data[DATA] = $categories;
                    break;

                    case TEXT:
                    case IMAGE:
                        $data = ['data' => $config_data];
                        
                    break;

                    case SLIDER:
                        $data = !empty($config_data['items']) ? ['data' => $config_data['items']] : [];
                    break;

                    case API_RATING:
                        $block = $block_info;
                        $block['config'] = $config_data;

                        $params_url_filter = [];
                        if(!empty($data_bearer['params']) && $this->loadComponent('Utilities')->isJson($data_bearer['params'])) {
                            $params_url_filter = json_decode($data_bearer['params'], true);
                        }

                        $data = $block_component->getDataBlock($block, $params_url_filter);
                    break;

                    case API_COMMENT:
                        $block = $block_info;
                        $block['config'] = $config_data;

                        $params_url_filter = [];
                        if(!empty($data_bearer['params']) && $this->loadComponent('Utilities')->isJson($data_bearer['params'])) {
                            $params_url_filter = json_decode($data_bearer['params'], true);
                        }
                        
                        $data = $block_component->getDataBlock($block, $params_url_filter);
                    break;

                    case TAB_PRODUCT:
                    case TAB_ARTICLE:
                        $block = $block_info;
                        $block['config'] = $config_data;
                        $block['tab_index'] = !empty($data_bearer['tab_index']) ? $data_bearer['tab_index'] : 0;
                        $type = !empty($block['type']) ? $block['type'] : null;

                        $params_url_filter = [];
                        if(!empty($data_bearer['params']) && $this->loadComponent('Utilities')->isJson($data_bearer['params'])) {
                            $params_url_filter = json_decode($data_bearer['params'], true);
                        }

                        $data = $block_component->getDataBlock($block, $params_url_filter);

                        if(!empty($type) && $type == TAB_PRODUCT){
                            // format data object to array
                            $products = !empty($data[DATA]) ? $data[DATA] : [];
                            if(!empty($products)){
                                foreach($products as $k => $product){
                                    $attributes_item_apply = !empty($product['attributes_item_apply']) ? array_values($product['attributes_item_apply']) : [];
                                    if(!empty($attributes_item_apply)){
                                        foreach($attributes_item_apply as $k_attribute_item => $attribute_item){
                                            $attributes_item_apply[$k_attribute_item]['options'] = !empty($attribute_item['options']) ? array_values($attribute_item['options']) : [];
                                        }
                                    }

                                    $items = !empty($product['items']) ? $product['items'] : [];
                                    if(!empty($items)){
                                        foreach($items as $k_item => $item){
                                            $items[$k_item]['attributes'] = !empty($item['attributes']) ? array_values($item['attributes']) : [];
                                        }
                                    }

                                    $products[$k]['attributes_item_apply'] = $attributes_item_apply;
                                    $products[$k]['attributes_item_special'] = !empty($product['attributes_item_special']) ? array_values($product['attributes_item_special']) : [];
                                    $products[$k]['items'] = $items;
                                    $products[$k]['categories'] = !empty($product['categories']) ? array_values($product['categories']) : [];
                                    $products[$k]['all_images'] = !empty($product['all_images']) ? array_values($product['all_images']) : [];
                                    
                                }
                            }
                            $data[DATA] = $products;
                        }

                        if(!empty($type) && $type == TAB_ARTICLE){
                            $articles = [];
                            foreach ($data[DATA] as $k => $article) {
                                unset($article['attributes']);
                                $articles[$k] = $article;
                                $articles[$k]['categories'] = array_values($article['categories']);
                            }

                            $data[DATA] = $articles;
                        }
                    break;
                }

                $item = [
                    'block_code' => !empty($block_info['code']) ? $block_info['code'] : null,
                    'name' => !empty($block_info['name']) ? $block_info['name'] : null,
                    'type' => !empty($block_info['type']) ? $block_info['type'] : null,
                    'config_layout' => $config_layout,
                    'block_data' => $data
                ];

                $result[] = $item;
            }
        }        

        $page_title = !empty($page_info['name']) ? $page_info['name'] : null;

        $config = !empty($page_info['config']) ? json_decode($page_info['config'], true) : null;
        if(!empty($config['page_title'][LANGUAGE])) {
            $page_title = !empty($config['page_title'][LANGUAGE]) ? $config['page_title'][LANGUAGE] : null;
        }

        if(!empty($category_id)){
            $category_info = TableRegistry::get('CategoriesContent')->find()->where([
                'category_id' => $category_id,
                'lang' => LANGUAGE
            ])->select(['name'])->first();

            $page_title = !empty($category_info['name']) ? $category_info['name'] : null;
        }

        if(!empty($product_id)) {
            $product_info = TableRegistry::get('ProductsContent')->find()->where([
                'product_id' => $product_id,
                'lang' => LANGUAGE
            ])->select(['name'])->first();

            $page_title = !empty($product_info['name']) ? $product_info['name'] : null;
        }

        if(!empty($article_id)) {
            $article_info = TableRegistry::get('ArticlesContent')->find()->where([
                'article_id' => $article_id,
                'lang' => LANGUAGE
            ])->select(['name'])->first();
            
            $page_title = !empty($article_info['name']) ? $article_info['name'] : null;
        }

        $this->responseApi([
            CODE => SUCCESS,
            EXTEND => [
                'page' => [
                    'name' => !empty($page_info['name']) ? $page_info['name'] : null,
                    'code' => !empty($page_info['code']) ? $page_info['code'] : null,
                    'page_title' => $page_title
                ]
            ],
            DATA => $result
        ]);
    }

    public function advancedSearch()
    {
        $data = $this->data_bearer;

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('template', 'phuong_thuc_khong_hop_le')]);
        }

        $template_info = TableRegistry::get('MobileTemplate')->getTemplateDefault();

        if (empty($template_info)) {
            $this->responseJson([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_thiet_lap')]);
        }

        $this->responseApi([
            CODE => SUCCESS,
            DATA => $this->formatAdvancedSearch($template_info['config'])
        ]);
    }

    private function formatAdvancedSearch($config = [])
    {

        if(empty($config) || !$this->loadComponent('Utilities')->isJson($config)) return [];
        $config = json_decode($config , true);

        $config_advanced_search = !empty($config['advanced_search']) ? $config['advanced_search'] : [];

        if(empty($config_advanced_search)) return [];

        //data advanced search
        $categories = [];
        $show_category = !empty($config_advanced_search['category']['show']) ? true : false;
        $selected_all_category = !empty($config_advanced_search['category']['select_all']) ? true : false;
        $selected_category_ids = !empty($config_advanced_search['category']['category_id']) ? $config_advanced_search['category']['category_id'] : [];

        if(($show_category && $selected_all_category) || ($show_category && !empty($selected_category_ids))) {
            $params_category = [
                FIELD => LIST_INFO,
                FILTER => [
                    TYPE => PRODUCT,
                    LANG => LANGUAGE,
                    'status' => 1,
                ],
                'get_parent' => true
            ];

            if(!empty($selected_category_ids)){
                $params_category[FILTER]['ids'] = $selected_category_ids;
            }
            $parent_categories = TableRegistry::get('Categories')->queryListCategories($params_category)->toArray();
            if(!empty($parent_categories)){
                foreach ($parent_categories as $category) {
                    $categories[] = [
                        'id' => !empty($category['id']) ? intval($category['id']) : null,
                        'name' => !empty($category['CategoriesContent']['name']) ? $category['CategoriesContent']['name'] : null,
                    ];            
                }
            }
        }

        $brands = [];
        $show_brand = !empty($config_advanced_search['brand']['show']) ? true : false;
        $selected_all_brand = !empty($config_advanced_search['brand']['select_all']) ? true : false;
        $selected_brand_ids = !empty($config_advanced_search['brand']['brand_id']) ? $config_advanced_search['brand']['brand_id'] : [];

        if(($show_brand && $selected_all_brand) || $show_brand && !empty($selected_brand_ids)) {
            $params_brand = [
                FIELD => LIST_INFO,
                FILTER => [
                    'status' => 1
                ],
            ];

            if(!empty($selected_brand_ids)){
                $params_brand[FILTER]['ids'] = $selected_brand_ids;
            }
            $list_brands = TableRegistry::get('Brands')->queryListBrands($params_brand)->toArray();
            
            if(!empty($list_brands)){
                foreach ($list_brands as $brand) {
                    $brands[] = [
                        'id' => !empty($brand['id']) ? intval($brand['id']) : null,
                        'name' => !empty($brand['BrandsContent']['name']) ? $brand['BrandsContent']['name'] : null,
                    ];            
                }
            }
        }

        $attributes = [];
        $show_attribute = !empty($config_advanced_search['attribute']['show']) ? true : false;
        $selected_all_attribute = !empty($config_advanced_search['attribute']['select_all']) ? true : false;
        $selected_attribute_ids = !empty($config_advanced_search['attribute']['attribute_id']) ? $config_advanced_search['attribute']['attribute_id'] : [];

        if(($show_attribute && $selected_all_attribute) || ($show_attribute && !empty($selected_attribute_ids))) {
            $all_attributes = Hash::combine(TableRegistry::get('Attributes')->getAll(LANGUAGE), '{n}.id', '{n}', '{n}.attribute_type');
            $all_options = Hash::combine(TableRegistry::get('AttributesOptions')->getAll(LANGUAGE), '{n}.id', '{n}', '{n}.attribute_id');
            $attributes_item = !empty($all_attributes[PRODUCT_ITEM]) ? Hash::combine($all_attributes[PRODUCT_ITEM], '{n}.id', '{n}') : [];
 
            if(empty($selected_attribute_ids)) {
                $selected_attribute_ids = Hash::extract($attributes_item, '{n}.id');
            }

            foreach ($selected_attribute_ids as $k => $attribute_id) {
                if(empty($attributes_item[$attribute_id]['code'])) continue;
                $attr_code = 'item_' . $attributes_item[$attribute_id]['code'];
                $list = [];
                if (!empty($all_options[$attribute_id])) {
                    foreach ($all_options[$attribute_id] as $item) {
                        array_push($list, [
                            'id' => $item['id'],
                            'name' => $item['name']
                        ]);
                    }
                }
                
                $attributes[$attr_code] = [
                    'show' => 1,
                    'title' => $attributes_item[$attribute_id]['name'],
                    'list' => $list
                ];
            }
        }

        $status = [];
        $show_status = !empty($config_advanced_search['status']['show']) ? true : false;
        $selected_all_status = !empty($config_advanced_search['status']['select_all']) ? true : false;
        $selected_status_ids = !empty($config_advanced_search['status']['status_id']) ? $config_advanced_search['status']['status_id'] : [];

        $list_status = [
            FEATURED => __d('template', 'noi_bat'),
            DISCOUNT => __d('template', 'giam_gia'),
            STOCKING => __d('template', 'con_hang')
        ];

        if(($show_status && $selected_all_status) || ($show_status && !empty($selected_status_ids))) {
            if(empty($selected_status_ids)) {
                $selected_status_ids = [FEATURED, DISCOUNT, STOCKING];
            }

            foreach ($selected_status_ids as $status_id) {
                $status[] = [
                    'id' => $status_id,
                    'name' => $list_status[$status_id]
                ];
            }
        }

        $data_advanced_search = [
            'keyword' => [
                'show' => !empty($config_advanced_search['keyword']['show']) ? intval($config_advanced_search['keyword']['show']) : 0,
                'title' => __d('template', 'tim_kiem_tu_khoa')
            ],
            'status' => [
                'show' => !empty($config_advanced_search['status']['show']) ? intval($config_advanced_search['status']['show']) : 0,
                'title' => __d('template', 'tinh_trang'),
                'list' => $status
            ],
            'id_categories' => [
                'show' => !empty($config_advanced_search['category']['show']) ? intval($config_advanced_search['category']['show']) : 0,
                'title' => __d('template', 'danh_muc_san_pham'),
                'list' => $categories
            ],
            'price' => [
                'show' => !empty($config_advanced_search['price']['show']) ? intval($config_advanced_search['price']['show']) : 0,
                'title' => __d('template', 'tim_kiem_khoang_gia'),
                'price_from' => !empty($config_advanced_search['price']['price_from']) ? floatval(str_replace(',', '', $config_advanced_search['price']['price_from'])) : 0,
                'price_to' => !empty($config_advanced_search['price']['price_to']) ? floatval(str_replace(',', '', $config_advanced_search['price']['price_to'])) : 0,
            ],
            'brand' => [
                'show' => !empty($config_advanced_search['brand']['show']) ? intval($config_advanced_search['brand']['show']) : 0,
                'title' => __d('template', 'thuong_hieu'),
                'list' => !empty($brands) ? $brands : [],
            ]
        ];

        return array_merge($data_advanced_search, $attributes);
    }

    private function countPostByCatId($id = null, $type = null) 
    {

        if(empty($id)) return 0;
        if(empty($type) || !in_array($type, Configure::read('LIST_TYPE_CATEGORY'))) return [];

        $parent_category = TableRegistry::get('Categories')->getAllChildCategoryId($id);
        $result = 0;

        if($type == PRODUCT) {
            $result = TableRegistry::get('Products')->find()->contain('CategoryProduct')->where([
                'Products.deleted' => 0,
                'Products.status' => ENABLE,
                'CategoryProduct.category_id IN' => $parent_category
            ])->select('Products.id')->group('Products.id')->count();

            if(empty($result)) return 0;
        }
        
        if($type == ARTICLE) {
            $result = TableRegistry::get('Articles')->find()->contain('CategoryArticle')->where([
                'Articles.deleted' => 0,
                'Articles.status' => ENABLE,
                'CategoryArticle.article_id IN' => $parent_category
            ])->select('Articles.id')->group('Articles.id')->count();

            if(empty($result)) return 0;
        }

        return $result;
    }
}