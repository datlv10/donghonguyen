<?php

namespace App\Controller;
use Cake\ORM\TableRegistry;

class BlockController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

	public function ajaxLoadContent($block_code = null) 
	{
        $this->viewBuilder()->enableAutoLayout(false);
        $data = $this->getRequest()->getData();

        // định nghĩa 1 số biến mặc định của giao diện
        $page_params = $this->getPageByUrl($this->request->referer());

        $url = !empty($page_params['url']) ? $page_params['url'] : null;
        $code = !empty($page_params['code']) ? $page_params['code'] : null;
        $type = !empty($page_params['type']) ? $page_params['type'] : null;
        $page_record_id = !empty($page_params['page_record_id']) ? intval($page_params['page_record_id']) : null;
        $product_id = !empty($page_params['product_id']) ? intval($page_params['product_id']) : null;
        $article_id = !empty($page_params['article_id']) ? intval($page_params['article_id']) : null;
        $tag_id = !empty($page_params['tag_id']) ? intval($page_params['tag_id']) : null;
        $page_category_id = !empty($page_params['category_id']) ? intval($page_params['category_id']) : null;

        $page_categories_id = [];
        if(!empty($page_category_id)){
            $page_categories_id = TableRegistry::get('Categories')->getAllChildCategoryId($page_category_id);
        }

        define('PAGE_URL', $url);
        define('PAGE_RECORD_ID', $page_record_id);
        define('PAGE_TAG_ID', $tag_id);
        define('PAGE_CATEGORY_ID', $page_category_id);
        define('PAGE_CATEGORIES_ID', $page_categories_id);
        define('PAGE_TYPE', $type);
        define('PAGE_CODE', $code);


        //lấy thông tin block
        $block_info = TableRegistry::get('TemplatesBlock')->getInfoBlock($block_code);
        $block_type = !empty($block_info['type']) ? $block_info['type'] : '';
        if(empty($block_info) || empty($block_type)){
            $this->autoRender = false;
            exit;
        };

        $view = !empty($block_info['view']) ? $block_info['view'] : 'view.tpl';
        $read_cache = true;

        // load dữ liệu cho block tab
        if(in_array($block_type, [TAB_PRODUCT, TAB_ARTICLE])){
            $tab_index = !empty($data['tab_index']) ? $data['tab_index'] : 0;
            if(!empty($tab_index)) {
                $block_info['tab_index'] = $tab_index;
            }

            $tab_item = !empty($block_info['config']['item'][$tab_index]) ? $block_info['config']['item'][$tab_index] : [];

            // lấy cấu hình riêng của tab đẩy vào block
            $block_info['config']['data_ids'] = !empty($tab_item['data_ids']) ? $tab_item['data_ids'] : [];
            $block_info['config']['data_type'] = !empty($tab_item['data_type']) ? $tab_item['data_type'] : null;
            $block_info['config']['filter_data'] = !empty($tab_item['filter_data']) ? $tab_item['filter_data'] : null;            
            $block_info['config'][HAS_PAGINATION] = !empty($tab_item[HAS_PAGINATION]) ? $tab_item[HAS_PAGINATION] : null;
            $block_info['config'][SORT_FIELD] = !empty($tab_item[SORT_FIELD]) ? $tab_item[SORT_FIELD] : null;
            $block_info['config'][SORT_TYPE] = !empty($tab_item[SORT_TYPE]) ? $tab_item[SORT_TYPE] : null;
            $block_info['config'][NUMBER_RECORD] = !empty($block_info['config'][NUMBER_RECORD]) ? intval($block_info['config'][NUMBER_RECORD]) : 12;

            $view = !empty($tab_item['view_child']) ? $tab_item['view_child'] : 'view.tpl';

            switch ($block_type) {
                case TAB_PRODUCT:
                    $block_type = PRODUCT;
                    break;

                case TAB_ARTICLE:
                    $block_type = ARTICLE;
                    break;
            }

            $read_cache = false;
        }

        $filter = [];
        if(!empty($data)){
            $filter = $data;
            $read_cache = false;
        }

        $data_block = $this->loadComponent('Block')->getDataBlock($block_info, $filter, $read_cache);

        $this->set('block_info', $block_info);
        $this->set('block_config', !empty($block_info['config']) ? $block_info['config'] : []);
        $this->set(DATA_EXTEND, !empty($block_info[DATA_EXTEND]) ? $block_info[DATA_EXTEND] : []);
        $this->set('data_block', $data_block);        
        $this->set('block_type', $block_type);
        $this->render('/block/' . $block_type . DS . str_replace('.tpl', '', $view));
    }

}