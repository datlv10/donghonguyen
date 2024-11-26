<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;
use Cake\Core\Exception\Exception;
use Cake\Utility\Hash;
use Cake\Cache\Cache;

class BlockComponent extends Component
{
    public $controller = null;
    public $components = ['PaginatorExtend', 'Utilities', 'Comment'];

    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->controller = $this->_registry->getController();
    }
  
    public function getDataBlock($block_info = [], $params_url_filter = [], $read_cache = true)
    {
        $code = !empty($block_info['code']) ? $block_info['code'] : null;
        $type = !empty($block_info['type']) ? $block_info['type'] : null;
        $config = !empty($block_info['config']) ? $block_info['config'] : [];
        $data_type = !empty($config['data_type']) ? $config['data_type'] : null;

        if(empty($code) || empty($type) || $type == HTML) return [];    

        $page_record_id = defined('PAGE_RECORD_ID') ? intval(PAGE_RECORD_ID) : null;
        $page_type = defined('PAGE_TYPE') ? PAGE_TYPE : null;

        // update the number of views of the product
        if($type == PRODUCT_DETAIL && $page_type == PRODUCT_DETAIL && !empty($page_record_id)){
            $table = TableRegistry::get('Products');
            $get_view = $table->find()->where(['id' => $page_record_id])->select(['view'])->first();
            $view = !empty($get_view['view']) ? intval($get_view['view']) : 0;

            $table->updateAll(['view' => $view + 1], ['id' => $page_record_id]);
        }

        // update the number of views of the article
        if($type == ARTICLE_DETAIL && $page_type == ARTICLE_DETAIL && !empty($page_record_id)){
            $table = TableRegistry::get('Articles');

            $get_view = $table->find()->where(['id' => $page_record_id])->select(['view'])->first();
            $view = !empty($get_view['view']) ? intval($get_view['view']) : 0;

            $table->updateAll(['view' => $view + 1], ['id' => $page_record_id]);
        }        

        // get data of block
        $use_cache = !empty($config['cache']) ? true : false;
        if(!$read_cache){
            $use_cache = false;
        }
        
        if($use_cache){
            $suffix = '_' . LANGUAGE . '_' . CURRENCY_CODE;
            $cache_key = $code . $suffix;

            
            if(!empty($page_record_id) && $data_type == BY_URL){
                $cache_key = $code . '_' . $page_record_id . $suffix;
            }       

            $result = Cache::read($cache_key, DATA_BLOCK);
            if(!is_null($result)) return $result;
        }
        
        $result = [];
        switch ($type) {
            case CATEGORY_PRODUCT:
            case CATEGORY_ARTICLE:
                $result = $this->blockCategory($block_info);
                break;

            case BRAND_PRODUCT:
                $result = $this->blockBrand($block_info);
                break;

            case PRODUCT:
                $result = $this->blockListProducts($block_info, $params_url_filter);
                break;

            case PRODUCT_DETAIL:
                $result = $this->blockDetailProduct($block_info);
                break;

            case ARTICLE:
            case MEDIA:
                $result = $this->blockListArticles($block_info, $params_url_filter);
                break;

            case ARTICLE_DETAIL:
                $result = $this->blockDetailArticle($block_info);
                break;

            case MENU:
                $result = $this->blockMenu($block_info);
                break;

            case SLIDER:
                $result = $this->blockSlider($block_info);
                break;

            case API_RATING:
            case API_COMMENT:
                $result = $this->blockApiComment($block_info, $params_url_filter);
            break;

            case TAB_PRODUCT:
            case TAB_ARTICLE:
                $result = $this->blockTab($block_info, $params_url_filter);
                break;
        }

        if($use_cache){
            Cache::write($cache_key, $result, DATA_BLOCK);
        }

        return $result;
    }

    private function blockListProducts($block_info = [], $params_url_filter = [])
    {
        $result = [
            DATA => [],
            PAGINATION => []
        ];

        $table = TableRegistry::get('Products');
        $config = !empty($block_info['config']) ? $block_info['config'] : [];

        // filter data block by config block
        $ids = !empty($config['data_ids']) ? $config['data_ids'] : [];
        $data_type = !empty($config['data_type']) ? $config['data_type'] : null;
        $number_record = !empty($config[NUMBER_RECORD]) ? intval($config[NUMBER_RECORD]) : 12;
        $has_pagination = !empty($config[HAS_PAGINATION]) ? true : false;

        $sort_field = !empty($config[SORT_FIELD]) ? $config[SORT_FIELD] : null;
        $sort_type = !empty($config[SORT_TYPE]) ? $config[SORT_TYPE] : null;

        $filter_data = !empty($config['filter_data']) ? $config['filter_data'] : null;
        $page = 1;
        
        // set params filter default
        $params = [
            'get_item' => true,
            'get_categories' => true,
            'get_attributes' => true,
            'get_item_attributes' => true,
            SORT => [
                FIELD => $sort_field,
                SORT => $sort_type
            ],
            FILTER => [
                LANG => LANGUAGE,
                STATUS => 1,
                STATUS_ITEM => 1
            ]
        ];

        // filter params
        if($data_type == BY_URL){
            $ids = [];

            if(defined('PAGE_CATEGORIES_ID') && !empty(PAGE_CATEGORIES_ID)){
                $params[FILTER]['id_categories'] = PAGE_CATEGORIES_ID;
            }

            if(defined('PAGE_BRAND_ID') && !empty(PAGE_BRAND_ID)){
                $params[FILTER]['id_brands'] = [PAGE_BRAND_ID];
            }

            if(defined('PAGE_TAG_ID') && !empty(PAGE_TAG_ID)){
                $params[FILTER]['tag_id'] = PAGE_TAG_ID;
            }

            // nếu block danh sách cấu hình ở trang chi tiết sản phẩm và dữ liệu lấy tự động theo đường dẫn -> block sản phẩm liên quan
            // trong trường hợp này thì ta loại bỏ id của sản phẩm đang hiển thị đi
            if(PAGE_TYPE == PRODUCT_DETAIL){
                $params[FILTER]['not_ids'] = [PAGE_RECORD_ID];
            }
        }

        // trường hợp này chỉ hoạt động với api của mobile app, BY_PAGE_ID chỉ có trên cấu hình block mobile app
        if($data_type == BY_PAGE_ID && defined('PAGE_RECORD_ID') && defined('PAGE_TYPE') && PAGE_TYPE == PRODUCT){
            $params[FILTER]['id_categories'] = [PAGE_RECORD_ID];
        }

        if($has_pagination) {
            // lay du lieu theo param tu duong dan
            $params_url = $this->controller->getRequest()->getQueryParams();

            // neu $params_url_filter co gia tri thi thay the param duong dan bang $params_url_filter
            if(!empty($params_url_filter)){
                $params_url = $params_url_filter;
            }

            if(!empty($params_url['limit'])){
                $number_record = intval($params_url['limit']);
            }
            
            if(!empty($params_url['page'])){
                $page = intval($params_url['page']);
            }

            if(!empty($params_url[KEYWORD])){
                $params[FILTER][KEYWORD] = trim($params_url[KEYWORD]);
            }

            if(!empty($params_url['id_categories'])){
                $category_filter = array_unique(array_filter(explode('-', $params_url['id_categories'])));
                if(!empty($category_filter)){
                    $params[FILTER]['id_categories'] = [];
                    foreach ($category_filter as $category_id) {
                        if(!empty(intval($category_id))){
                            $params[FILTER]['id_categories'][] = intval($category_id);
                        }
                    }
                }
            }

            if(!empty($params_url['sort'])){
                $sort_param = explode('-', $params_url['sort']);
                $sort_field = !empty($sort_param[0]) ? $sort_param[0] : null;
                $sort_type = !empty($sort_param[1]) ? $sort_param[1] : null;

                $params[SORT] = [
                    FIELD => $sort_field,
                    SORT => !empty($sort_type) ? $sort_type : DESC
                ];
            }

            if(!empty($params_url['price_from'])){
                $params[FILTER]['price_from'] = floatval($params_url['price_from']);
            }

            if(!empty($params_url['price_to'])){
                $params[FILTER]['price_to'] = floatval($params_url['price_to']);
            }

            if(!empty($params_url['brand'])){
                $brand_filter = array_unique(array_filter(explode('-', $params_url['brand'])));

                if(!empty($brand_filter)){
                    foreach ($brand_filter as $brand_id) {
                        if(!empty(intval($brand_id))){
                            $params[FILTER]['id_brands'][] = intval($brand_id);
                        }
                    }
                }
            }

            if(!empty($params_url['status'])){
                $list_status = array_unique(array_filter(explode('-', $params_url['status'])));
                if(in_array('featured', $list_status)){
                    $params[FILTER]['featured'] = 1;
                }

                if(in_array('discount', $list_status)){
                    $params[FILTER]['discount'] = 1;
                }

                if(in_array('stocking', $list_status)){
                    $params[FILTER]['stocking'] = 1;
                }                
            }

            // lọc dữ liệu theo params của thuộc tính mở rộng
            if(!empty($params_url)){
                $params_attribute = $params_item = [];

                foreach ($params_url as $key => $values) {
                    if(empty($values) || strpos($key, 'attr_') === false) continue;
                    $params_attribute[$key] = $values;
                }

                foreach ($params_url as $key => $values) {
                    if(empty($values) || strpos($key, 'item_') === false) continue;
                    $params_item[$key] = $values;
                }


                $filter_ids = [];
                $check_params = false;
                if(!empty($params_attribute)){
                    $check_params = true;
                    $attribute_record_ids = $this->getRecordIdsByParamsUrl(PRODUCT, $params_attribute);
                    $filter_ids = $attribute_record_ids;
                }
           
                if(!empty($params_item)){
                    $check_params = true;
                    $item_record_ids = $this->getRecordIdsByParamsUrl(PRODUCT_ITEM, $params_item);
                    $filter_ids = $item_record_ids;
                }

                // nếu lọc cả theo thuộc tính mở rộng của sản phẩm và phiên bản thì lấy ids sản phẩm chung
                if(!empty($attribute_record_ids) && !empty($item_record_ids)){
                    $filter_ids = array_values(array_intersect($attribute_record_ids, $item_record_ids));
                }
                
                if($check_params && empty($filter_ids)) return $result;
                $params[FILTER]['ids'] = $filter_ids;
            }
        }

        if(!empty($ids)){
            if($data_type == CATEGORY_PRODUCT){
                $params[FILTER]['id_categories'] = $ids;
            }

            if($data_type == BRAND_PRODUCT){
                $params[FILTER]['id_brands'] = $ids;
            }

            if($data_type == PRODUCT){
                $params[FILTER]['ids'] = $ids;
            }
        } 

        if($data_type == WISHLIST_PRODUCT){
            $request = $this->controller->getRequest();
            $wishlist = !empty($request->getCookie(WISHLIST)) ? json_decode($request->getCookie(WISHLIST), true) : null;
            if(!empty($request->getSession()->read(MEMBER))){
                $member = $request->getSession()->read(MEMBER);

                $wishlist_info = TableRegistry::get('Wishlists')->find()->where([
                    'customer_account_id' => $member['account_id'],
                    'type' => PRODUCT
                ])->select(['record_id'])->toArray();
                if(empty($wishlist_info)) return $result;

                $wishlist_ids = Hash::extract($wishlist_info, '{n}.record_id');
                $wishlist[PRODUCT] = $wishlist_ids;
            }
            
            if(empty($wishlist[PRODUCT])) return $result;

            $params[FILTER]['ids'] = $wishlist[PRODUCT];
        }   

        if($data_type == COMPARE){
            $request = $this->controller->getRequest();
            $compare = !empty($request->getCookie(COMPARE)) ? json_decode($request->getCookie(COMPARE), true) : null;
            
            if(empty($compare)) return $result;

            $params[FILTER]['ids'] = $compare;
            $params[SORT][FIELD] = 'order_field_id';
        }  

        if($data_type == PRODUCTS_VIEWED){
            $request = $this->controller->getRequest();
            $product_viewed = !empty($request->getCookie(PRODUCTS_VIEWED)) ? json_decode($request->getCookie(PRODUCTS_VIEWED), true) : null;
            if(empty($product_viewed)) return $result;

            $params[FILTER]['ids'] = $product_viewed;
        }

        if(!empty($filter_data) && $filter_data == 'featured'){
            $params[FILTER]['featured'] = 1;
        }

        if(!empty($filter_data) && $filter_data == 'discount'){
            $params[FILTER]['discount'] = 1;
        }
        
        $data = $pagination = [];
        if(!$has_pagination){
            $products = $table->queryListProducts($params)->limit($number_record)->toArray();

        }else{
            try {                
                $products = $this->PaginatorExtend->paginate($table->queryListProducts($params), [
                    'limit' => $number_record,
                    'page' => $page
                ])->toArray();
            } catch (Exception $e) {
                $products = $this->PaginatorExtend->paginate($table->queryListProducts($params), [
                    'limit' => $number_record,
                    'page' => 1
                ])->toArray();
            }

            // pagination info
            $pagination_info = !empty($this->controller->getRequest()->getAttribute('paging')['Products']) ? $this->controller->getRequest()->getAttribute('paging')['Products'] : [];
            $pagination = $this->Utilities->formatPaginationInfo($pagination_info);       
        }        

        // format output        
        if(!empty($products)){
            $all_categories = TableRegistry::get('Categories')->getAll(PRODUCT, LANGUAGE);
            $attributes_table = TableRegistry::get('Attributes');
            $all_attributes = Hash::combine($attributes_table->getAll(LANGUAGE), '{n}.id', '{n}', '{n}.attribute_type');
            $all_attributes_product = !empty($all_attributes[PRODUCT]) ? $all_attributes[PRODUCT] : [];

            foreach ($products as $product) {
                if(empty($product['ProductsItem']) || empty($product['ProductsContent']) || empty($product['Links'])) continue;
                $product_items = !empty($product['ProductsItem']) ? $product['ProductsItem'] : [];

                // format data categories
                $categories = [];
                if(!empty($product['CategoriesProduct'])){
                    foreach ($product['CategoriesProduct'] as $k => $category) {
                        $category_id = !empty($category['category_id']) ? intval($category['category_id']) : null;
                        $category_info = !empty($all_categories[$category_id]) ? $all_categories[$category_id] : [];
                        if(empty($category_info)) continue;

                        $categories[$category_id] = [
                            'id' => $category_id,
                            'name' => !empty($category_info['name']) ? $category_info['name'] : null,
                            'url' => !empty($category_info['url']) ? $category_info['url'] : null,
                            'status' => !empty($category_info['status']) ? $category_info['status'] : null,
                        ];
                    }
                }

                $attributes = [];
                if(!empty($all_attributes_product) && !empty($product['ProductsAttribute'])){
                    $attribute_value = Hash::combine($product['ProductsAttribute'], '{n}.attribute_id', '{n}');

                    foreach ($all_attributes_product as $attribute_id => $attribute_info) {
                        $attribute_code = !empty($attribute_info['code']) ? $attribute_info['code'] : null;
                        $attribute_name = !empty($attribute_info['name']) ? $attribute_info['name'] : null;
                        $attribute_input_type = !empty($attribute_info['input_type']) ? $attribute_info['input_type'] : null;
                        if(empty($attribute_code) || empty($attribute_name)) continue;

                        $value = !empty($attribute_value[$attribute_id]['value']) ? $attribute_value[$attribute_id]['value'] : null;
                        $value = $attributes_table->formatValueAttribute($attribute_input_type, $value, LANGUAGE);
                        $attributes[$attribute_code] = [
                            'id' => $attribute_id,
                            'name' => $attribute_name,
                            'value' => $value
                        ];
                    }
                }

                $data_product = [
                    'id' => !empty($product['id']) ? intval($product['id']) : null,
                    'name' => !empty($product['ProductsContent']['name']) ? $product['ProductsContent']['name'] : null,
                    'description' => !empty($product['ProductsContent']['description']) ? $product['ProductsContent']['description'] : null,
                    'url_video' => !empty($product['url_video']) ? $product['url_video'] : null,
                    'type_video' => !empty($product['type_video']) ? $product['type_video'] : null,
                    'files' => !empty($product['files']) ? $product['files'] : null,
                    'rating' => !empty($product['rating']) ? $product['rating'] : null,
                    'rating_number' => !empty($product['rating_number']) ? $product['rating_number'] : null,
                    'view' => !empty($product['view']) ? $product['view'] : null,
                    'created' => !empty($product['created']) ? $product['created'] : null,
                    'created_by' => !empty($product['created_by']) ? $product['created_by'] : null,
                    'featured' => !empty($product['featured']) ? $product['featured'] : null,                    
                    'url' => !empty($product['Links']['url']) ? $product['Links']['url'] : null,
                    'categories' => $categories,
                    'attributes' => $attributes,
                    'price' => null,
                    'price_special' => null,
                    'apply_special' => false,
                    'discount_percent'=> null
                ];

                // format data items
                $data_items = $table->formatDataProductItems($product, LANGUAGE);

                $items = !empty($data_items['items']) ? $data_items['items'] : [];
                $first_item = !empty($items[0]) ? $items[0] : [];
                    
                $price = !empty($first_item['price']) ? round(floatval($first_item['price'] / CURRENCY_RATE), 2) : null;
                $price_special = !empty($first_item['price_special']) ? round(floatval($first_item['price_special'] / CURRENCY_RATE), 2) : null;
                $apply_special = !empty($first_item['apply_special']) ? true : false;                
                $discount_percent = !empty($first_item['discount_percent']) ? intval($first_item['discount_percent']) : null;                
        
                $data_product['items'] = $items;
                $data_product['number_item'] = !empty($items) ? count($items) : 1;
                $data_product['all_images'] = !empty($data_items['all_images']) ? array_unique($data_items['all_images']) : [];
                $data_product['total_quantity_available'] = !empty($data_items['total_quantity_available']) ? intval($data_items['total_quantity_available']) : 0;

                $data_product['price'] = !empty($price) ? $price : null;
                $data_product['price_special'] = !empty($price_special) ? $price_special : null;
                $data_product['apply_special'] = $apply_special;
                $data_product['discount_percent'] = !empty($discount_percent) ? $discount_percent : null;

                // format dữ liệu thuộc tính phiên bản sản phẩm
                if(!empty($product['ProductsItemAttribute'])){
                    $items_formated = $this->formatDataProductAttributeItems($product['ProductsItemAttribute'], $items);
                    if(!empty($items_formated)) {
                        $data_product['items'] = !empty($items_formated['items']) ? $items_formated['items'] : [];
                        $data_product['attributes_item_apply'] = !empty($items_formated['attributes_item_apply']) ? $items_formated['attributes_item_apply'] : [];
                        $data_product['attributes_item_special'] = !empty($items_formated['attributes_item_special']) ? $items_formated['attributes_item_special'] : [];
                    }
                }

                $data[] = $data_product;
            }
        }

        $result[DATA] = $data;
        $result[PAGINATION] = !empty($data) && !empty($pagination) ? $pagination : [];

        return $result;
    }

    private function blockDetailProduct($block_info = [])
    {
        $table = TableRegistry::get('Products');
        $config = !empty($block_info['config']) ? $block_info['config'] : [];

        // params
        $ids = !empty($config['data_ids']) ? $config['data_ids'] : [];

        $product_id = null;
        // lấy product_id theo đường dẫn 
        if($config[DATA_TYPE] == BY_URL){
            $url = null;
            if(defined('PAGE_URL') && !empty(PAGE_URL)){
                $url = PAGE_URL;
            }

            $link_info = TableRegistry::get('Links')->getLinkByUrl($url, ['type' => PRODUCT_DETAIL]);
            $product_id = !empty($link_info['foreign_id']) ? intval($link_info['foreign_id']) : null;

            // lấy product_id theo page_record_id (trường hợp này chỉ hoạt động với api của mobile app, BY_PAGE_ID chỉ có trên cấu hình block mobile app)
        }elseif($config[DATA_TYPE] == BY_PAGE_ID && defined('PAGE_RECORD_ID') && defined('PAGE_TYPE') && PAGE_TYPE == PRODUCT_DETAIL){
            $product_id = PAGE_RECORD_ID;
        }else{
            $product_id = !empty($ids[0]) ? intval($ids[0]) : null;
        }

        $product = $table->getDetailProduct($product_id, LANGUAGE, [
            'get_categories' => true,
            'get_attributes' => true,
            'get_item_attributes' => true,
            'get_tags' => true,
            STATUS_ITEM => 1
        ]);

        $data = [];
        if(!empty($product)){
            $data = $table->formatDataProductDetail($product, LANGUAGE);
        }   

        // format giá sản phẩm theo currency hiện tại
        if(!empty($data['items'])){
            foreach ($data['items'] as $key => $item) {
                if(!empty($item['price'])){
                    $data['items'][$key]['price'] = !empty($item['price']) ? round(floatval($item['price'] / CURRENCY_RATE), 10) : null;
                }

                if(!empty($item['price_special'])){
                    $data['items'][$key]['price_special'] = !empty($item['price_special']) ? round(floatval($item['price_special'] / CURRENCY_RATE), 10) : null;
                }
            }
        }

        // format dữ liệu thuộc tính phiên bản sản phẩm
        if(!empty($product['ProductsItemAttribute'])){
            $items_formated = $this->formatDataProductAttributeItems($product['ProductsItemAttribute'], $data['items']);
            if(!empty($items_formated)) {
                $data['items'] = !empty($items_formated['items']) ? $items_formated['items'] : [];
                $data['attributes_item_apply'] = !empty($items_formated['attributes_item_apply']) ? $items_formated['attributes_item_apply'] : [];
                $data['attributes_item_special'] = !empty($items_formated['attributes_item_special']) ? $items_formated['attributes_item_special'] : [];
            }
        }

        return [DATA => $data];
    }

    private function blockCategory($block_info = [])
    {
        $config = !empty($block_info['config']) ? $block_info['config'] : [];
        $block_type = !empty($block_info['type']) ? $block_info['type'] : null;

        // params
        $ids = !empty($config['data_ids']) ? $config['data_ids'] : [];
        $type = !empty($block_type) && strpos($block_type, 'category_') > -1 ? str_replace('category_', '', $block_type) : null;

        $sort_field = !empty($config[SORT_FIELD]) ? $config[SORT_FIELD] : null;
        $sort_type = !empty($config[SORT_TYPE]) ? $config[SORT_TYPE] : null;

        // trường hợp này chỉ hoạt động với api của mobile app, BY_PAGE_ID chỉ có trên cấu hình block mobile app
        $parent_id = null;
        if(!empty($config[DATA_TYPE]) && $config[DATA_TYPE] == BY_PAGE_ID && defined('PAGE_RECORD_ID') && defined('PAGE_TYPE') && in_array(PAGE_TYPE, [PRODUCT, ARTICLE])) {
            $parent_id = PAGE_RECORD_ID;
        }

        $params = [
            'get_attributes' => true,
            SORT => [
                FIELD => $sort_field,
                SORT => $sort_type
            ],
            FILTER => [
                LANG => LANGUAGE,
                STATUS => 1,
                TYPE => $type,
                'ids' => $ids
            ]
        ];

        if(!empty($parent_id)){
            $params[FILTER]['parent_id'] = $parent_id;
        }

        $categories = TableRegistry::get('Categories')->queryListCategories($params)->all()->nest('id', 'parent_id')->toArray();
        $categories = $this->parseDataCategories($categories);

        $max_level = Hash::maxDimensions($categories);

        $result = [
            DATA => $categories,
            'max_level' => !empty($max_level) ? intval($max_level/2) : 1
        ];
        return $result;
    }

    private function blockBrand($block_info = [])
    {
        $config = !empty($block_info['config']) ? $block_info['config'] : [];

        // params
        $ids = !empty($config['data_ids']) ? $config['data_ids'] : [];
        $sort_field = !empty($config[SORT_FIELD]) ? $config[SORT_FIELD] : null;
        $sort_type = !empty($config[SORT_TYPE]) ? $config[SORT_TYPE] : null;

        $params = [
            SORT => [
                FIELD => $sort_field,
                SORT => $sort_type
            ],
            FILTER => [
                LANG => LANGUAGE,
                STATUS => 1,
                'ids' => $ids
            ]
        ];


        $brands = TableRegistry::get('Brands')->queryListBrands($params)->toArray();

        // format output   
        $result = [];
        if(!empty($brands)){
            foreach ($brands as $brand) {
                if(empty($brand['BrandsContent']) || empty($brand['Links'])) continue;

                $result[] = [
                    'id' => !empty($brand['id']) ? intval($brand['id']) : null,
                    'image_avatar' => !empty($brand['image_avatar']) ? $brand['image_avatar'] : null,
                    'images' => !empty($brand['images']) ? json_decode($brand['images'], true) : null,
                    'url_video' => !empty($brand['url_video']) ? $brand['url_video'] : null,
                    'type_video' => !empty($brand['type_video']) ? $brand['type_video'] : null,
                    'files' => !empty($brand['files']) ? json_decode($brand['files'], true) : null,
                    'name' => !empty($brand['BrandsContent']['name']) ? $brand['BrandsContent']['name'] : null,
                    'url' => !empty($brand['Links']['url']) ? $brand['Links']['url'] : null,
                ];
            }
        }        

        return [DATA => $result];
    }

    private function blockListArticles($block_info = [], $params_url_filter = [])
    {
        $result = [
            DATA => [],
            PAGINATION => []
        ];

        $table = TableRegistry::get('Articles');

        $config = !empty($block_info['config']) ? $block_info['config'] : [];
        
        // params
        $ids = !empty($config['data_ids']) ? $config['data_ids'] : [];
        $data_type = !empty($config['data_type']) ? $config['data_type'] : null;
        $number_record = !empty($config[NUMBER_RECORD]) ? intval($config[NUMBER_RECORD]) : 12;
        $has_pagination = !empty($config[HAS_PAGINATION]) ? true : false;

        $sort_field = !empty($config[SORT_FIELD]) ? $config[SORT_FIELD] : null;
        $sort_type = !empty($config[SORT_TYPE]) ? $config[SORT_TYPE] : null;

        $filter_data = !empty($config['filter_data']) ? $config['filter_data'] : null;
        $params = [
            'get_categories' => true,
            'get_attributes' => true,
            SORT => [
                FIELD => $sort_field,
                SORT => $sort_type
            ],
            FILTER => [
                LANG => LANGUAGE,
                STATUS => 1
            ]
        ];

        // filter params
        if($data_type == BY_URL){
            $ids = [];

            if(defined('PAGE_CATEGORIES_ID') && !empty(PAGE_CATEGORIES_ID)){
                $params[FILTER]['id_categories'] = PAGE_CATEGORIES_ID;
            }

            if(defined('PAGE_TAG_ID') && !empty(PAGE_TAG_ID)){
                $params[FILTER]['tag_id'] = PAGE_TAG_ID;
            }

            // nếu block danh sách bài viết cấu hình ở trang chi tiết bài viết và dữ liệu lấy tự động theo đường dẫn -> block bài viết liên quan
            // trong trường hợp này thì ta loại bỏ id của bài viết đang hiển thị ở trang này đi đi
            if(PAGE_TYPE == ARTICLE_DETAIL){
                $params[FILTER]['not_ids'] = [PAGE_RECORD_ID];
            }
        }

        // trường hợp này chỉ hoạt động với api của mobile app, BY_PAGE_ID chỉ có trên cấu hình block mobile app
        if(!empty($data_type) && $data_type == BY_PAGE_ID && defined('PAGE_RECORD_ID') && defined('PAGE_TYPE') && PAGE_TYPE == ARTICLE){
            $params[FILTER]['id_categories'] = [PAGE_RECORD_ID];
        }

        if($has_pagination) {
            $params_url = $this->controller->getRequest()->getQueryParams();

            // neu $params_url_filter co gia tri thi thay the param duong dan bang $params_url_filter
            if(!empty($params_url_filter)){
                $params_url = $params_url_filter;
            }

            if(!empty($params_url['limit'])){
                $number_record = intval($params_url['limit']);
            }

            $page = 1;
            if(!empty($params_url['page'])){
                $page = intval($params_url['page']);
            }

            if(!empty($params_url[KEYWORD])){
                $params[FILTER][KEYWORD] = trim($params_url[KEYWORD]);
            }
            
            if(!empty($params_url['sort'])){
                $sort_param = explode('-', $params_url['sort']);
                $sort_field = !empty($sort_param[0]) ? $sort_param[0] : null;
                $sort_type = !empty($sort_param[1]) ? $sort_param[1] : null;

                $params[SORT] = [
                    FIELD => $sort_field,
                    SORT => !empty($sort_type) ? $sort_type : DESC
                ];
            }

            // lọc dữ liệu theo params của thuộc tính mở rộng
            if(!empty($params_url)){
                $params_attribute = [];
                foreach ($params_url as $key => $values) {
                    if(empty($values) || strpos($key, 'article_') === false) continue;
                    $params_attribute[$key] = $values;
                }

                $filter_ids = [];
                $check_params = false;
                if(!empty($params_attribute)){
                    $check_params = true;
                    $filter_ids = $this->getRecordIdsByParamsUrl(ARTICLE, $params_attribute);
                }
                
                if($check_params && empty($filter_ids)) return $result;
                $params[FILTER]['ids'] = $filter_ids;
            }
        }

        if(!empty($ids)){
            if($data_type == CATEGORY_ARTICLE){
                $params[FILTER]['id_categories'] = $ids;
            }

            if($data_type == ARTICLE){
                $params[FILTER]['ids'] = $ids;
            }
        }

        if($data_type == WISHLIST_ARTICLE){
            $request = $this->controller->getRequest();
            $wishlist = !empty($request->getCookie(WISHLIST)) ? json_decode($request->getCookie(WISHLIST), true) : null;
            if(!empty($request->getSession()->read(MEMBER))){
                $member = $request->getSession()->read(MEMBER);
                $account_id = !empty($member['account_id']) ? intval($member['account_id']) : null;
                if(empty($account_id)) return $result;

                $wishlist_info = TableRegistry::get('Wishlists')->find()->where([
                    'customer_account_id' => $account_id,
                    'type' => ARTICLE
                ])->select(['record_id'])->toArray();

                if(empty($wishlist_info)) return $result;
                $wishlist_ids = Hash::extract($wishlist_info, '{n}.record_id');
                
                $wishlist[ARTICLE] = $wishlist_ids;
            }
            
            if(empty($wishlist[ARTICLE])) return $result;
            
            $params[FILTER]['ids'] = $wishlist[ARTICLE];
        }   

        if($data_type == ARTICLES_VIEWED){
            $request = $this->controller->getRequest();
            $article_viewed = !empty($request->getCookie(ARTICLES_VIEWED)) ? json_decode($request->getCookie(ARTICLES_VIEWED), true) : null;
            if(empty($article_viewed)) return $result;

            $params[FILTER]['ids'] = $article_viewed;
        }

        if(!empty($filter_data) && $filter_data == 'featured'){
            $params[FILTER]['featured'] = 1;
        }

        // query
        $data = $pagination = [];

        if(!$has_pagination){
            $articles = $table->queryListArticles($params)->limit($number_record)->toArray();
        }else{
            try {
                $articles = $this->PaginatorExtend->paginate($table->queryListArticles($params), [
                    'limit' => $number_record,
                    'page' => $page
                ])->toArray();
            } catch (Exception $e) {
                $articles = $this->PaginatorExtend->paginate($table->queryListArticles($params), [
                    'limit' => $number_record,
                    'page' => 1
                ])->toArray();
            }
            
            $pagination_info = !empty($this->controller->getRequest()->getAttribute('paging')['Articles']) ? $this->controller->getRequest()->getAttribute('paging')['Articles'] : [];
            $pagination = $this->Utilities->formatPaginationInfo($pagination_info);       
        }

        // format output
        if(!empty($articles)){
            $all_categories = TableRegistry::get('Categories')->getAll(ARTICLE, LANGUAGE);
            $attributes_table = TableRegistry::get('Attributes');
            $all_attributes = Hash::combine($attributes_table->getAll(LANGUAGE), '{n}.id', '{n}', '{n}.attribute_type');
            $all_attributes_article = !empty($all_attributes[ARTICLE]) ? $all_attributes[ARTICLE] : [];

            foreach ($articles as $article) {
                if(empty($article['ArticlesContent']) || empty($article['Links'])) continue;
                
                $categories = [];
                if(!empty($article['CategoriesArticle'])){
                    foreach ($article['CategoriesArticle'] as $k => $category) {
                        $category_id = !empty($category['category_id']) ? intval($category['category_id']) : null;
                        $category_info = !empty($all_categories[$category_id]) ? $all_categories[$category_id] : [];
                        if(empty($category_info)) continue;

                        $categories[$category_id] = [
                            'id' => $category_id,
                            'name' => !empty($category_info['name']) ? $category_info['name'] : null,
                            'url' => !empty($category_info['url']) ? $category_info['url'] : null,
                        ];
                    }
                }

                $attributes = [];
                if(!empty($all_attributes_article) && !empty($article['ArticlesAttribute'])){

                    $attribute_value = Hash::combine($article['ArticlesAttribute'], '{n}.attribute_id', '{n}');
                    foreach ($all_attributes_article as $attribute_id => $attribute_info) {
                        $attribute_code = !empty($attribute_info['code']) ? $attribute_info['code'] : null;
                        $attribute_name = !empty($attribute_info['name']) ? $attribute_info['name'] : null;
                        $attribute_input_type = !empty($attribute_info['input_type']) ? $attribute_info['input_type'] : null;
                        if(empty($attribute_code) || empty($attribute_name)) continue;

                        $value = !empty($attribute_value[$attribute_id]['value']) ? $attribute_value[$attribute_id]['value'] : null;
                        $value = $attributes_table->formatValueAttribute($attribute_input_type, $value, LANGUAGE);
                        
                        $attributes[$attribute_code] = [
                            'id' => $attribute_id,
                            'name' => $attribute_name,
                            'value' => $value
                        ];
                    }
                }

                $data[] = [
                    'id' => !empty($article['id']) ? intval($article['id']) : null,
                    'name' => !empty($article['ArticlesContent']['name']) ? $article['ArticlesContent']['name'] : null,
                    'description' => !empty($article['ArticlesContent']['description']) ? $article['ArticlesContent']['description'] : null,
                    'image_avatar' => !empty($article['image_avatar']) ? $article['image_avatar'] : null,
                    'images' => !empty($article['images']) ? json_decode($article['images'], true) : [],
                    'url_video' => !empty($article['url_video']) ? $article['url_video'] : null,
                    'type_video' => !empty($article['type_video']) ? $article['type_video'] : null,
                    'files' => !empty($article['files']) ? $article['files'] : null,
                    'rating' => !empty($article['rating']) ? $article['rating'] : null,
                    'rating_number' => !empty($article['rating_number']) ? $article['rating_number'] : null,
                    'has_album' => !empty($article['has_album']) ? true : false,
                    'has_file' => !empty($article['has_file']) ? true : false,
                    'has_video' => !empty($article['has_video']) ? true : false,
                    'created_by' => !empty($article['created_by']) ? $article['created_by'] : null,
                    'created' => !empty($article['created']) ? $article['created'] : null,
                    'featured' => !empty($article['featured']) ? $article['featured'] : null,
                    'url' => !empty($article['Links']['url']) ? $article['Links']['url'] : null,
                    'categories' => $categories,
                    'attributes' => $attributes
                ];
            }
        }

        $result[DATA] = $data;
        $result[PAGINATION] = !empty($data) && !empty($pagination) ? $pagination : [];

        return $result;
    }

    private function blockDetailArticle($block_info = [])
    {   
        $table = TableRegistry::get('Articles');
        $config = !empty($block_info['config']) ? $block_info['config'] : [];

        // params
        $ids = !empty($config['data_ids']) ? $config['data_ids'] : [];

        $article_id = null;
        if($config[DATA_TYPE] == BY_URL){
            $url = null;
            if(defined('PAGE_URL') && !empty(PAGE_URL)){
                $url = PAGE_URL;
            }

            $link_info = TableRegistry::get('Links')->getLinkByUrl($url, ['type' => ARTICLE_DETAIL]);
            $article_id = !empty($link_info['foreign_id']) ? intval($link_info['foreign_id']) : null;
        } elseif ($config[DATA_TYPE] == BY_PAGE_ID && defined('PAGE_RECORD_ID') && defined('PAGE_TYPE') && PAGE_TYPE == ARTICLE_DETAIL){
            $article_id = PAGE_RECORD_ID;
        } else {
            $article_id = !empty($ids[0]) ? intval($ids[0]) : null;
        }
        
        $article = $table->getDetailArticle($article_id, LANGUAGE, [
            'get_categories' => true,
            'get_tags' => true,
            'get_attributes' => true,
            'get_user' => true
        ]);

        $data = [];
        if(!empty($article)){
            $data = $table->formatDataArticleDetail($article, LANGUAGE);
        }
 
        $result = [
            DATA => $data
        ];
        return $result;
    }

    private function blockTab($block_info = [], $params_url_filter = [])
    {
        $result = [
            'tabs' => [],
            DATA => [],
            PAGINATION => []
        ];

        if(empty($block_info)) return $result;

        $config = !empty($block_info['config']) ? $block_info['config'] : [];
        $items = !empty($config['item']) ? $config['item'] : [];
        $tab_index = !empty($block_info['tab_index']) ? intval($block_info['tab_index']) : 0;

        $tabs_config = [];
        $tab_info = [];
        if(!empty($items)){
            foreach ($items as $item) {
                $tabs_config[] = [
                    'config' => [
                        'data_ids' => !empty($item['data_ids']) ? $item['data_ids'] : [],
                        'data_type' => !empty($item['data_type']) ? $item['data_type'] : null,
                        'filter_data' => !empty($item['filter_data']) ? $item['filter_data'] : null,
                        NUMBER_RECORD => !empty($config[NUMBER_RECORD]) ? intval($config[NUMBER_RECORD]) : 12,
                        HAS_PAGINATION => !empty($config[HAS_PAGINATION]) ? $config[HAS_PAGINATION] : null,
                        SORT_FIELD => !empty($item[SORT_FIELD]) ? $item[SORT_FIELD] : null,
                        SORT_TYPE => !empty($item[SORT_TYPE]) ? $item[SORT_TYPE] : null
                    ]
                ];
                $tab_info[] = [
                    'name' => !empty($item['name_' . LANGUAGE]) ? $item['name_' . LANGUAGE] : null
                ];
            }
        }

        if(!empty($tab_info)) {
            $result['tabs'] = $tab_info;
        }

        $list_data = [];

        if(!empty($tabs_config[$tab_index]) && !empty($block_info['type']) && $block_info['type'] == TAB_PRODUCT){
            $list_data = $this->blockListProducts($tabs_config[$tab_index], $params_url_filter);
        }

        if(!empty($tabs_config[$tab_index]) && !empty($block_info['type']) && $block_info['type'] == TAB_ARTICLE){
            $list_data = $this->blockListArticles($tabs_config[$tab_index], $params_url_filter);
        }

        $result[DATA] = !empty($list_data[DATA]) ? $list_data[DATA] : [];
        $result[PAGINATION] = !empty($list_data[PAGINATION]) ? $list_data[PAGINATION] : [];

        return $result;
    }

    private function blockMenu($block_info = [])
    {
        $config = !empty($block_info['config']) ? $block_info['config'] : [];

        $list_item = !empty($config['item']) ? $config['item'] : null;
        if(empty($list_item)) return [];

        $result = [];
        foreach ($list_item as $k => $item) {
            $type = !empty($item['type']) ? $item['type'] : null;
            $menu = [
                'type' => $type,
                'name' => !empty($item['name_' . LANGUAGE]) ? $item['name_' . LANGUAGE] : null,
                'class_item' => !empty($item['class_item']) ? $item['class_item'] : null,                
                'image' => !empty($item['image']) ? $item['image'] : null,
                'image_source' => !empty($item['image_source']) ? $item['image_source'] : null,
                'blank_link' => !empty($item['blank_link']) ? $item['blank_link'] : null,
                'url' => null,
                'has_sub_menu' => false,
                'view_item' => null,
                'type_sub_menu' => null,
                'sub_categories_id' => [],
                'data_sub_menu' => [],
                'data_extend_sub_menu' => [],
                'max_level_sub_menu' => null
            ];

            $has_sub_menu = !empty($item['has_sub_menu']) ? true : false;
            if(!empty($has_sub_menu)){
                $sub_categories_id = !empty($item['sub_categories_id']) ? $item['sub_categories_id'] : [];
                $type_sub_menu = !empty($item['type_sub_menu']) ? $item['type_sub_menu'] : null;

                $menu['has_sub_menu'] = $has_sub_menu;
                $menu['view_item'] = !empty($item['view_item']) ? pathinfo($item['view_item'], PATHINFO_FILENAME) : null;
                $menu['type_sub_menu'] = $type_sub_menu;
                $menu['sub_categories_id'] = $sub_categories_id;
                $menu['data_extend_sub_menu'] = !empty($item['data_extend_sub_menu']) ? json_decode($item['data_extend_sub_menu'], true) : [];
                if(!empty($sub_categories_id)){
                    $type_category = !empty($type_sub_menu) ? str_replace('category_', '', $type_sub_menu) : null;
                    $categories = TableRegistry::get('Categories')->queryListCategories([
                        FILTER => [
                            LANG => LANGUAGE,
                            STATUS => 1,
                            TYPE => $type_category,
                            'ids' => $sub_categories_id
                        ]
                    ])->all()->nest('id', 'parent_id')->toArray();                    
                    $categories = $this->parseDataCategories($categories);

                    $max_level = Hash::maxDimensions($categories);
                    $menu['max_level_sub_menu'] = !empty($max_level) ? intval($max_level/2) : 1;
                    $menu['data_sub_menu'] = $categories;
                }
            }
            
            switch ($type) {
                case CATEGORY_PRODUCT:
                case CATEGORY_ARTICLE:
                    $category_id = !empty($item['category_id']) ? intval($item['category_id']) : null;
                    $link_info = TableRegistry::get('Links')->getInfoLink([
                        'foreign_id' => $category_id,
                        'lang' => LANGUAGE,
                        TYPE => $type
                    ]);
                    $menu['url'] = !empty($link_info['url']) ? $link_info['url'] : null;
                    break;
                case CUSTOM:
                    $menu['url'] = !empty($item['url_' . LANGUAGE]) ? $item['url_' . LANGUAGE] : null;
                    break;
                default:
                    $page_code = $type;
                    $page_info = TableRegistry::get('TemplatesPage')->getInfoPage([
                        'code' => $page_code,
                        'lang' => LANGUAGE,
                        'get_content' => true
                    ]);

                    $menu['url'] = !empty($page_info['url']) ? $page_info['url'] : null;
                    break;
            }

            $result[] = $menu;
        }        
        return $result;
    }

    public function parseDataCategories($categories = [], $loop = 0)
    {
        if(empty($categories)) return [];

        $result = [];
        $loop ++;
        if($loop > 10){
            return [];
        }

        foreach($categories as $category){
            if(empty($category['id']) || empty($category['CategoriesContent']['name'])) continue;
            $item = [
                'id' => intval($category['id']),
                'name' => !empty($category['CategoriesContent']['name']) ? $category['CategoriesContent']['name'] : null,
                'parent_id' => !empty($category['parent_id']) ? intval($category['parent_id']) : null,
                'image_avatar' => !empty($category['image_avatar']) ? $category['image_avatar'] : null,
                'images' => !empty($category['images']) ? json_decode($category['images']) : null,
                'url_video' => !empty($category['url_video']) ? $category['url_video'] : null,
                'type_video' => !empty($category['type_video']) ? $category['type_video'] : null,
                'description' => !empty($category['CategoriesContent']['description']) ? $category['CategoriesContent']['description'] : null,
                'url_id' => !empty($category['Links']['id']) ? intval($category['Links']['id']) : null,
                'url' => !empty($category['Links']['url']) ? $category['Links']['url'] : null,
                'attributes' => [],
                'children' => []
            ];
            if(!empty($category['CategoriesAttribute'])){
                $all_attributes = TableRegistry::get('Attributes')->getAll(LANGUAGE);    
                
                $attributes = [];
                foreach ($category['CategoriesAttribute'] as $key => $attribute) {
                    $attribute_id = !empty($attribute['attribute_id']) ? intval($attribute['attribute_id']) : null;
                    $attribute_info = !empty($all_attributes[$attribute_id]) ? $all_attributes[$attribute_id] : [];
                    $attribute_code = !empty($attribute_info['code']) ? $attribute_info['code'] : null;
                    if(empty($attribute_code)) continue;

                    $attributes[$attribute_code] = [
                        'id' => $attribute_id,
                        'name' => !empty($attribute_info['name']) ? $attribute_info['name'] : null,
                        'value' => !empty($attribute['value']) ? $attribute['value'] : null
                    ];

                    if(!empty($attribute_info['attribute_type']) && $attribute_info['attribute_type'] == CATEGORY && !empty($attribute_info['input_type']) &&  $attribute_info['input_type'] == TEXT) {
                        $attributes[$attribute_code] = [
                            'id' => $attribute_id,
                            'name' => !empty($attribute_info['name']) ? $attribute_info['name'] : null,
                            'value' => !empty($attribute['value']) ? json_decode($attribute['value'], true)[LANGUAGE] : null,
                        ];
                    }
                }
                $item['attributes'] = $attributes;
            }

            if(!empty($category['children'])){
                $item['children'] = $this->parseDataCategories($category['children'], $loop);    
            }
            $result[intval($category['id'])] = $item;
        }

        return $result;
    }

    private function blockSlider($block_info = [])
    {
        $config = !empty($block_info['config']) ? $block_info['config'] : [];

        $list_item = !empty($config['item']) ? $config['item'] : null;
        if(empty($list_item)) return [];

        $result = [];
        foreach ($list_item as $k => $item) {
            $result[] = [
                'name' => !empty($item['name_' . LANGUAGE]) ? $item['name_' . LANGUAGE] : null,                
                'url' => !empty($item['url_' . LANGUAGE]) ? $item['url_' . LANGUAGE] : null,
                'image' => !empty($item['image']) ? $item['image'] : null,
                'image_source' => !empty($item['image_source']) ? $item['image_source'] : null,
                'class_item' => !empty($item['class_item']) ? $item['class_item'] : null,
                'blank_link' => !empty($item['blank_link']) ? $item['blank_link'] : null,
                'description' => !empty($item['description_' . LANGUAGE]) ? $item['description_' . LANGUAGE] : null,
                'description_short' => !empty($item['description_short_' . LANGUAGE]) ? $item['description_short_' . LANGUAGE] : null,
            ];
        }
        return $result;
    }

    public function getPathViewBlock($block_code = null)
    {
        if(empty($block_code)) return null;

        $block_table = TableRegistry::get('TemplatesBlock');
        $block = $block_table->find()->where([
            'TemplatesBlock.code' => $block_code,
            'TemplatesBlock.deleted' => 0
        ])->first();

        $type = !empty($block['type']) ? $block['type'] : null;
        if(empty($type)){
            return null;
        }

        $path_template = TableRegistry::get('Templates')->getPathTemplate();
        if(empty($path_template)){
            return null;
        }

        return $path_template . BLOCK . DS . $type;
    }

    private function blockApiComment($block_info = [], $params_url_filter = [])
    {
        $result = [
            DATA => [],
            PAGINATION => []
        ];

        $type = !empty($block_info['type']) ? $block_info['type'] : null;

        $type_comment = [];
        switch ($type) {
            case API_RATING:
                $type_comment = RATING;
                break;

            case API_COMMENT:
                $type_comment = COMMENT;
            break;
        }

        if(!defined('PAGE_RECORD_ID') || !PAGE_RECORD_ID > 0) return $result;
        if(!defined('PAGE_TYPE') || PAGE_TYPE != PRODUCT_DETAIL) return $result;
        if(empty($type_comment)) return $result;

        $data = $this->formatDataComment($type_comment, $block_info, $params_url_filter);

        $result_comment = $this->Comment->list($data);

        if(empty($result_comment[CODE]) || $result_comment[CODE] == ERROR){
            return $result = [
                CODE => ERROR,
                MESSAGE => !empty($result_comment[MESSAGE]) ? $result_comment[MESSAGE] : null
            ];
        }

        $list_comment = !empty($result_comment[DATA]['comments']) ? $result_comment[DATA]['comments'] : [];
        $pagination = !empty($result_comment[DATA][PAGINATION]) ? $result_comment[DATA][PAGINATION] : [];

        $result = [
            DATA => $list_comment,
            PAGINATION => $pagination
        ];

        if(!empty($type_comment) && $type_comment == RATING){
            $rating_info = TableRegistry::get('Comments')->getInfoRating([
                'foreign_id' => PAGE_RECORD_ID,
                'type' => PAGE_TYPE
            ]);

            $result['rating_info'] = $rating_info;
        }

        return $result;
    }

    private function formatDataComment($type_comment = null, $block_info = [], $params_url = [])
    {
        $config = !empty($block_info['config']) ? $block_info['config'] : [];
        $config_number_record = !empty($config['number_record']) ? $config['number_record'] : null;
        $config_sort_field = !empty($config['sort_field']) ? $config['sort_field'] : null;
        
        $number_record = !empty($params_url['limit']) ? intval($params_url['limit']) : $config_number_record;
        $page = !empty($params_url['page']) ? intval($params_url['page']) : 1;
        
        if(!empty($params_url['sort'])){
            $sort_param = explode('-', $params_url['sort']);
        }
        $sort_field = !empty($sort_param[0]) ? $sort_param[0] : $config_sort_field;
        $sort_type = !empty($sort_param[1]) ? $sort_param[1] : null;

        $member = $this->controller->getRequest()->getSession()->read(MEMBER);
        $customer_account_id = !empty($member['account_id']) ? $member['account_id'] : null;
        
        return [
            'type_comment' => $type_comment,
            'type' => PAGE_TYPE,
            'foreign_id' => PAGE_RECORD_ID,
            'customer_account_id' => $customer_account_id,
            'number_record' => $number_record,
            'page' => $page,
            'sort_field' => $sort_field,
            'sort_type' => $sort_type
        ];
    }

    private function getRecordIdsByParamsUrl($type = null, $params = null)
    {
        if(!in_array($type, [ARTICLE, PRODUCT, PRODUCT_ITEM]) || empty($params)) return [];

        $all_attributes = Hash::combine(TableRegistry::get('Attributes')->getAll(LANGUAGE), '{n}.code', '{n}', '{n}.attribute_type');
        $attributes = !empty($all_attributes[$type]) ? $all_attributes[$type] : [];

        $prefix = null;
        switch($type){
            case PRODUCT:
                $prefix = 'attr_';
            break;

            case PRODUCT_ITEM:
                $prefix = 'item_';
            break;

            case ARTICLE:
                $prefix = 'article_';
            break;
        }

        $i = 0;
        $join_table = $where_attribute = [];

        foreach ($params as $key => $values) {
            if(empty($values) && strpos($key, $prefix) == -1) continue;
            
            $split = explode($prefix, $key);
            $attribute_code = !empty($split[1]) ? $split[1] : null;
            if(empty($attribute_code)) continue;

            $attribute_id = !empty($attributes[$attribute_code]['id']) ? intval($attributes[$attribute_code]['id']) : null;
            $input_type = !empty($attributes[$attribute_code]['input_type']) ? $attributes[$attribute_code]['input_type'] : null;
            if(empty($attribute_id)) continue;

            $i++;

            $where_item = [];
            switch($type){
                case PRODUCT:
                    $property_name = 'ProductsAttribute' . $i;
                    $join_table[$property_name] = [
                        'table' => 'products_attribute',
                        'type' => 'INNER',
                        'conditions' => "$property_name.product_id = Products.id"
                    ];
                break;

                case PRODUCT_ITEM:
                    $property_name = 'ProductsItemAttribute' . $i;
                    $join_table[$property_name] = [
                        'table' => 'products_item_attribute',
                        'type' => 'INNER',
                        'conditions' => "$property_name.product_id = Products.id"
                    ];
                break;

                case ARTICLE:
                    $property_name = 'ArticlesAttribute' . $i;
                    $join_table[$property_name] = [
                        'table' => 'articles_attribute',
                        'type' => 'INNER',
                        'conditions' => "$property_name.article_id = Articles.id"
                    ];
                break;
            }

            foreach (array_unique(array_filter(explode('-', $values))) as $attribute_value) {
                $split_value = explode('_', $attribute_value);
                $value = !empty($split_value[0]) ? $split_value[0] : null;
                $extend = !empty($split_value[1]) ? $split_value[1] : 'eq';

                // xử lý value và extend tuỳ theo loại input_type của thuộc tính mở rộng  
                switch ($input_type) {
                    case SWITCH_INPUT:
                    case SINGLE_SELECT:
                    case NUMERIC:
                    case CITY:
                    case CITY_DISTRICT:
                    case CITY_DISTRICT_WARD:
                        break;

                    case DATE:                                                                
                        $value = strtotime(date('Y-m-d 00:00:00', strtotime(str_replace('/', '-', $value))));
                        break;

                    case DATE_TIME:
                        $value = strtotime(date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $value))));
                        break;

                    case MULTIPLE_SELECT:
                    case PRODUCT_SELECT:
                    case ARTICLE_SELECT:
                        $extend = 'like_quotes';
                        break;

                    case TEXT:
                        $extend = 'like';
                        break;
                }
                
                // xử lý điều kiện where theo extend
                switch($extend) {
                    case 'or':
                        $where_item['OR'][] = "$property_name.value = $value";
                    break;

                    case 'eq':
                        $where_item[] = "$property_name.value = $value";
                    break;

                    case 'neq':
                        $where_item[] = "$property_name.value != CAST($value as DECIMAL)";
                    break;

                    case 'gt':
                        $where_item[] = "$property_name.value > CAST($value as DECIMAL)";
                    break;

                    case 'lt':
                        $where_item[] = "$property_name.value < CAST($value as DECIMAL)";
                    break;

                    case 'gte':
                        $where_item[] = "$property_name.value >= CAST($value as DECIMAL)";
                    break;

                    case 'lte':
                        $where_item[] = "$property_name.value <= CAST($value as DECIMAL)";
                    break;
                
                    case 'like':
                        $where_item[] = "$property_name.value LIKE '%$value%'";
                    break;

                    case 'like_quotes':
                        $where_item[] = "$property_name.value LIKE '%\"" . $value . "\"%'";
                    break;

                    case 'city':
                    case 'district':
                    case 'ward':
                        $city_id = $district_id = $ward_id = null;
                        if($extend == 'city') $city_id = $value;
                        if($extend == 'district') $district_id = $value;
                        if($extend == 'ward') $ward_id = $value;

                        if(!empty($city_id) && empty($district_id) && empty($ward_id)) {
                            $where_item[] = "$property_name.value LIKE '%\"city_id\":\"$city_id\"%'";
                        }

                        if(!empty($district_id) && empty($ward_id)) {
                            $where_item[] = "$property_name.value LIKE '%\"district_id\":\"$district_id\"%'";
                        }

                        if(!empty($ward_id)) {
                            $where_item[] = "$property_name.value LIKE '%\"ward_id\":\"$ward_id\"%'";
                        }

                    break;
                }
            }
            $where_item[] = "$property_name.attribute_id = $attribute_id";
            $where_attribute['AND'][] = $where_item;
        }

        $records_ids = [];
        switch($type){
            case PRODUCT:
            case PRODUCT_ITEM:
                $records_ids = TableRegistry::get('Products')->find()->join($join_table)->where($where_attribute)->select(['Products.id'])->toArray();
                $records_ids = Hash::extract($records_ids, '{n}.id');
                break;
            case ARTICLE:
                $records_ids = TableRegistry::get('Articles')->find()->join($join_table)->where($where_attribute)->select(['Articles.id'])->toArray();
                $records_ids = Hash::extract($records_ids, '{n}.id');
            break;
        }

        return $records_ids;
    }

    // format dữ liệu thuộc tính phiên bản sản phẩm
    private function formatDataProductAttributeItems($data_item_attribute = [], $items = [])
    {
        if(empty($data_item_attribute) || empty($items)) return [];

        $attributes_table = TableRegistry::get('Attributes');
        $options_table = TableRegistry::get('AttributesOptions');

        $all_attributes = Hash::combine($attributes_table->getAll(LANGUAGE), '{n}.id', '{n}', '{n}.attribute_type');
        $all_attributes_item = !empty($all_attributes[PRODUCT_ITEM]) ? $all_attributes[PRODUCT_ITEM] : [];
        $all_options = $options_table->getAll(LANGUAGE);        

        // sắp xếp thuộc tính phiên bản sản phẩm trước khi format , kiểu săp xếp: special_select_item DESC -> has_image DESC -> position DESC -> id DESC
        if(!empty($all_attributes_item)){
            foreach ($all_attributes_item as $k => $attribute) {

                // thêm cột sort
                $special = !empty($attribute['input_type']) && $attribute['input_type'] == 'special_select_item' ? 1 : 0;
                $has_image = !empty($attribute['has_image']) ? 1 : 0;
                $position = !empty($attribute['position']) ? intval($attribute['position']) : 0;
                $id = !empty($attribute['id']) ? intval($attribute['id']) : 0;
                $sort = $special . '_' . $has_image . '_' . str_pad(strval($position), 8, '0', STR_PAD_LEFT) . '_' . str_pad(strval($id), 8, '0', STR_PAD_LEFT);

                $attribute['sort'] = $sort;
                $all_attributes_item[$k] = $attribute;
            }

            //sắp xếp
            array_multisort(array_column($all_attributes_item, 'sort'), SORT_DESC, $all_attributes_item);
        }

        // format dữ liệu
        $attributes_apply = $attributes_special = [];
        foreach($items as $k => $item){
            $product_item_id = !empty($item['id']) ? intval($item['id']) : null;
            $code = !empty($item['code']) ? $item['code'] : null;
            $price = !empty($item['price']) ? floatval($item['price']) : null;
            $price_special = !empty($item['price_special']) ? floatval($item['price_special']) : null;
            $apply_special = !empty($item['apply_special']) ? true : false;
            $quantity_available = !empty($item['quantity_available']) ? intval($item['quantity_available']) : null;
            $images = !empty($item['images']) ? $item['images'] : [];

            $list_special_code = $list_extend_name = $list_normal = $attributes_of_item = [];
            
            foreach($all_attributes_item as $attribute_info){
                unset($attribute_info['sort']);

                $input_type = !empty($attribute_info['input_type']) ? $attribute_info['input_type'] : null;
                $attribute_id = !empty($attribute_info['id']) ? intval($attribute_info['id']) : null;
                $attribute_code = !empty($attribute_info['code']) ? $attribute_info['code'] : null;

                foreach($data_item_attribute as $item_attribute){
                    if(empty($item_attribute['product_item_id']) || $item_attribute['product_item_id'] != $product_item_id) continue;                    
                    if(empty($item_attribute['attribute_id']) || $item_attribute['attribute_id'] != $attribute_id) continue;

                    if(empty($attributes_apply[$attribute_id])){
                        $attributes_apply[$attribute_id] = $attribute_info;
                        $attributes_apply[$attribute_id]['options'] = [];
                    }

                    $value = !empty($item_attribute['value']) ? $item_attribute['value'] : null;

                    if($input_type == SPECICAL_SELECT_ITEM && !empty($value) && !empty($all_options[$value])){
                        $option_id = $value;

                        $option_info = $all_options[$option_id];
                        $option_code = !empty($option_info['code']) ? $option_info['code'] : null;
                        $option_name = !empty($option_info['name']) ? $option_info['name'] : null;
                        if(!empty($attribute_info['has_image']) && !empty($option_info)){
                           $option_info['image'] = !empty($images[0]) ? $images[0] : null;
                        }

                        $attributes_apply[$attribute_id]['options'][$option_id] = $option_info;

                        if(empty($list_special_code[$attribute_code])){
                            $list_special_code[$attribute_code] = $option_code;
                            $list_extend_name[] = $option_name;
                        }
                    }else{
                        // format value attribute
                        $value_format = $attributes_table->formatValueAttribute($input_type, $value, LANGUAGE);
                        if($input_type == SINGLE_SELECT){
                            $option_selected = $value_format;
                            $option_info = !empty($all_options[$option_selected]) ? $all_options[$option_selected] : [];
                            $value_format = !empty($option_info['name']) ? $option_info['name'] : null;
                        }

                        if($input_type == MULTIPLE_SELECT && is_array($value_format)){
                            $options_selected = $value_format;
                            $value_format = [];
                            foreach($options_selected as $option_selected){
                                $option_info = !empty($all_options[$option_selected]) ? $all_options[$option_selected] : [];
                                $name = !empty($option_info['name']) ? $option_info['name'] : null;
                                $value_format[] = $name;
                            }
                            $value_format = implode(',', array_filter($value_format));
                        }

                        $attribute_info['value_format'] = $value_format;
                        $attribute_info['value'] = $value;
                        

                        $list_normal[$attribute_code] = $attribute_info;
                    }
                    $attributes_of_item[] = [                        
                        'code' => $attribute_code,
                        'attribute_id' => $attribute_id,
                        'input_type' => $input_type,
                        'value' => $value
                    ];
                }
            }

            $special_code = null;
            if(!empty($list_special_code)){
                $explode_code = [];
                foreach($list_special_code as $attribute_code => $option_code){
                    $explode_code[] = $attribute_code;
                    $explode_code[] = $option_code;
                }
                $special_code = implode('_', $explode_code);
            }

            $item['special_code'] = $special_code;
            $item['extend_name'] = !empty($list_extend_name) ? implode(' - ', array_filter($list_extend_name)) : null;
            $item['attributes_normal'] = $list_normal;
            $item['attributes'] = $attributes_of_item;
            
            $items[$k] = $item;
            $attributes_special[$special_code] = $item;
        }

        // kiểm tra nếu có trên 2 thuộc tính áp dụng ảnh đại diện thì bỏ đi 1 thuộc tính

        if(!empty($attributes_apply)){
            $has_image = false;
            foreach($attributes_apply as $k => $attribute){
                if(!empty($attribute['has_image']) && $has_image){
                    $attributes_apply[$k]['has_image'] = 0;
                }

                if(!empty($attribute['has_image']) && !$has_image){
                    $has_image = true;
                }
            }
        }
 
        return [
            'items' => $items,
            'attributes_item_apply' => $attributes_apply,
            'attributes_item_special' => $attributes_special
        ];
    }
}
