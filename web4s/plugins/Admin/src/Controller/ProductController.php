<?php

namespace Admin\Controller;

use Admin\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Utility\Hash;
use Cake\Http\Response;
use Cake\ORM\Query;
use Cake\Core\Exception\Exception;
use Cake\I18n\Time;
use Cake\Datasource\ConnectionManager;
use Cake\Collection\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\Utility\Text;


class ProductController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    protected $limit_import = 5;

    public function list()
    {
        $this->css_page = [
            '/assets/plugins/global/lightbox/lightbox.css',
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.css'
        ];

        $this->js_page = [
            '/assets/plugins/global/lightbox/lightbox.min.js',
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.js',
            '/assets/js/pages/list_product.js'            
        ];
        // kiểm tra có sử dụng kho kiotviet không
        $settings = TableRegistry::get('Settings')->getSettingWebsite();

        $store_kiotviet = !empty($settings['store_kiotviet']) ? $settings['store_kiotviet'] : [];    
        $config_kiotviet = !empty($store_kiotviet['config']) ? json_decode($store_kiotviet['config'], true) : [];
        $use_kiotviet = !empty($config_kiotviet['status']) ? 1 : 0;
        
        $this->set('kiotviet', $use_kiotviet);
        $this->set('path_menu', 'product');
        $this->set('title_for_layout', __d('admin', 'san_pham'));
    }

    public function listJson()
    {
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $table = TableRegistry::get('Products');
        $utilities = $this->loadComponent('Utilities');

        $data = $params = $products = [];

        $limit = PAGINATION_LIMIT_ADMIN;
        $page = 1;
        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        // params query
        $params[QUERY] = !empty($data[QUERY]) ? $data[QUERY] : [];  

        // params filter
        $params[FILTER] = !empty($data[DATA_FILTER]) ? $data[DATA_FILTER] : [];
        if(!empty($params[QUERY])){
            $params[FILTER] = array_merge($params[FILTER], $params[QUERY]);
        }

        if(isset($params[FILTER]['display_product']) && $params[FILTER]['display_product'] != ''){
            $display_product = $params[FILTER]['display_product'];
        }

        $lang = !empty($params[FILTER][LANG]) ? $params[FILTER][LANG] : TableRegistry::get('Languages')->getDefaultLanguage();
        $params[FILTER][LANG] = $lang;

        // params         
        $params[SORT] = !empty($data[SORT]) ? $data[SORT] : [];
        $params['get_item'] = true;
        $params['get_empty_name'] = true;
        $params['get_item_attributes'] = true;
        $params['get_attributes'] = !empty($data['get_attributes']) ? true : false;
        $params['get_categories'] = !empty($data['get_categories']) ? true : false;
        $params['get_quantity_partner'] = true;

        // page and limit
        $page = !empty($data[PAGINATION][PAGE]) ? intval($data[PAGINATION][PAGE]) : 1;
        $limit = !empty($data[PAGINATION][PERPAGE]) ? intval($data[PAGINATION][PERPAGE]) : PAGINATION_LIMIT_ADMIN;

        // sort 
        $sort_field = !empty($params[SORT][FIELD]) ? $params[SORT][FIELD] : null;
        $sort_type = !empty($params[SORT][SORT]) ? $params[SORT][SORT] : null;

        if(!empty($data['export']) && $data['export'] == 'all') {
            $limit = 100000;
        }
        
        try {            
            $products = $this->paginate($table->queryListProducts($params), [
                'limit' => $limit,
                'maxLimit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();

        } catch (Exception $e) {
            $products = $this->paginate($table->queryListProducts($params), [
                'limit' => $limit,
                'maxLimit' => $limit,
                'page' => 1,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        }
        // kiểm tra có sử dụng kho kiotviet không
        $settings = TableRegistry::get('Settings')->getSettingWebsite();

        // parse data before output
        $result = [];
        if(!empty($products)){
            $languages = TableRegistry::get('Languages')->getList();
            
            $store_kiotviet = !empty($settings['store_kiotviet']) ? $settings['store_kiotviet'] : [];    
            $config_kiotviet = !empty($store_kiotviet['config']) ? json_decode($store_kiotviet['config'], true) : [];
            $use_kiotviet = !empty($config_kiotviet['status']) ? 1 : 0;

            foreach($products as $k => $product){
                $product_format = $table->formatDataProductDetail($product, $lang);
                
                // check multiple language
                $mutiple_language = [];                
                if(!empty($languages)){
                    foreach($languages as $k_lang => $language){
                        if($k_lang == $this->lang && !empty($product['name'])){
                            $mutiple_language[$k_lang] = true;
                        }else{
                            $content = TableRegistry::get('ProductsContent')->find()->where([
                                'product_id' => !empty($product['id']) ? intval($product['id']) : null,
                                'lang' => $k_lang
                            ])->select(['name'])->first();

                            $mutiple_language[$k_lang] = !empty($content['name']) ? true : false;
                        }                        
                    }
                }
                $product_format['mutiple_language'] = $mutiple_language;

                // lấy dữ liệu kiotviet                                
                if(!empty($use_kiotviet) && !empty($product_format['items'])){
                    $stores = TableRegistry::get('ProductsPartnerStore')->getAllStore(KIOTVIET);   
                    $stores = !empty($stores) ? Hash::combine($stores, '{n}.partner_store_id', '{n}.name') : [];

                    foreach($product_format['items'] as $k_item => $item){
                        $product_item_id = !empty($item['product_item_id']) ? intval($item['product_item_id']) : null;
                        if(empty($product_item_id)) continue;

                        $kiotviet_quantity = TableRegistry::get('ProductsPartnerQuantity')->find()->where([
                            'partner' => KIOTVIET,
                            'product_item_id' => $product_item_id,
                            'deleted' => 0
                        ])->select(['product_item_id', 'store_id', 'quantity'])->toList();
                        
                        $data_store = [];
                        $total_quantity = 0;
                        if(!empty($kiotviet_quantity)){
                            foreach($kiotviet_quantity as $partner){
                                $store_id = !empty($partner['store_id']) ? intval($partner['store_id']) : null;
                                $quantity = !empty($partner['quantity']) ? intval($partner['quantity']) : 0;
                                if(empty($store_id)) continue;
                                $total_quantity += $quantity;
                                $data_store[] = [
                                    'store_id' => $store_id,
                                    'name' => !empty($stores[$store_id]) ? $stores[$store_id] : null,
                                    'quantity' => $quantity
                                ];
                            }

                        }

                        $product_format['items'][$k_item]['kiotviet_store'] = $data_store;
                        $product_format['items'][$k_item]['total_quantity_kiotviet'] = $total_quantity;
                    }
                }

                $result[$k] = $product_format;
            }
        }
 
        if(!empty($data['export'])) {
            return $this->exportExcelProduct($result);
        }

        $pagination_info = !empty($this->request->getAttribute('paging')['Products']) ? $this->request->getAttribute('paging')['Products'] : [];
        $meta_info = $utilities->formatPaginationInfo($pagination_info);
            
        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $result, 
            META => $meta_info
        ]);
    }

    // khởi tạo file excel
    // Dùng để export dữ liệu excel và download file excel mẫu
    public function initializationExcel($data = [])
    {
        $all_attributes = Hash::combine(TableRegistry::get('Attributes')->getAll($this->lang), '{n}.id', '{n}', '{n}.attribute_type');

        // lấy thông tin thuộc tính sản phẩm
        $attributes_product = !empty($all_attributes[PRODUCT]) ? Hash::combine($all_attributes[PRODUCT], '{n}.code', '{n}') : [];

        // lấy thông tin thuộc tính phiên bản sản phẩm
        $attributes_item = [];
        if(!empty($all_attributes[PRODUCT_ITEM])){
            $attributes_item = Hash::combine(Collection($all_attributes[PRODUCT_ITEM])->filter(function ($item, $key, $iterator) {
                return $item['input_type'];
            })->toArray(), '{n}.code', '{n}');
        }

        $attribute_component = $this->loadComponent('Admin.Attribute');

        $languages = TableRegistry::get('Languages')->getList();
        $brands = TableRegistry::get('Brands')->getListBrands($this->lang);

        $categories_product = TableRegistry::get('Categories')->queryListCategories([
            FILTER => [
                TYPE => PRODUCT,
                LANG => $this->lang,
                STATUS => 1
            ]
        ])->all()->nest('id', 'parent_id')->toArray();

        $categories = [];
        if(!empty($categories_product)){
            $categories = Hash::combine(TableRegistry::get('Categories')->parseDataCategoriesExcel($categories_product), '{n}.id', '{n}.CategoriesContent.name');
            ;
        }      

        $data_dropdown = [
            'languages' => !empty($languages) ? implode(',', $languages) : __d('admin', 'tieng_viet'),
            'brands' => !empty($brands) ? implode(',', $brands) : '',
            'true_false' => __d('admin', 'co') .','.__d('admin', 'khong'),
            'status' => __d('admin', 'hoat_dong') .','.__d('admin', 'ngung_hoat_dong'),
        ];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $spreadsheet->getActiveSheet()->setTitle(__d('admin', 'thong_tin_san_pham'));

        $sheet_category = $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(1);
        $spreadsheet->getActiveSheet()->setTitle(__d('admin', 'thong_tin_danh_muc'));

        $arr_header = [
            'id' => __d('admin', 'id'),
            'name' => __d('admin', 'ten_san_pham'),
            'lang' => __d('admin', 'ngon_ngu'),
            'category' => __d('admin', 'danh_muc'),
            'brand' => __d('admin', 'thuong_hieu'),
            'featured' => __d('admin', 'noi_bat'),
            'catalogue' => __d('admin', 'muc_luc'),
            'position' => __d('admin', 'vi_tri'),
            'status' => __d('admin', 'trang_thai_sp'),
            'description' => __d('admin', 'mo_ta_ngan')
        ];
        
        if (!empty($attributes_product)) {
            foreach ($attributes_product as $key => $attribute) {
                $attribute_code = !empty($attribute['code']) ? $attribute['code'] : null;
                $attribute_name = !empty($attribute['name']) ? $attribute['name'] : null;
                $input_type = !empty($attribute['input_type']) ? $attribute['input_type'] : null;

                if (!empty($input_type) && ($input_type == ARTICLE_SELECT || $input_type == PRODUCT_SELECT || $input_type == CITY_DISTRICT || $input_type == RICH_TEXT || $input_type == ALBUM_IMAGE || $input_type == ALBUM_VIDEO || $input_type == VIDEO)) continue;

                if (!empty($attribute_code) && !empty($attribute_name)) {
                    $arr_header['attribute_' . $attribute_code] = $attribute_name;
                }
            }
        }

        $arr_header['items_code'] = __d('admin', 'ma_sp');
        $arr_header['items_images'] = __d('admin', 'Ảnh phiên bản');
        $arr_header['items_price'] = __d('admin', 'gia');
        $arr_header['items_price_special'] = __d('admin', 'gia_km');
        $arr_header['items_time_start_special'] = __d('admin', 'ngay_giam_gia');
        $arr_header['items_time_end_special'] = __d('admin', 'ngay_ket_thuc_giam_gia');
        $arr_header['items_quantity_available'] = __d('admin', 'so_luong');
        $arr_header['items_status_item'] = __d('admin', 'trang_thai_phien_ban');

        if (!empty($attributes_item)) {
            foreach ($attributes_item as $key => $attribute_item) {
                $attribute_item_code = !empty($attribute_item['code']) ? $attribute_item['code'] : null;
                $attribute_item_name = !empty($attribute_item['name']) ? $attribute_item['name'] : null;
                $attribute_item_input_type = !empty($attribute_item['input_type']) ? $attribute_item['input_type'] : null;

                if (!empty($attribute_item_input_type) && $attribute_item_input_type == SPECICAL_SELECT_ITEM) continue;

                if (!empty($attribute_item_code) && !empty($attribute_item_name)) {
                    $arr_header['item_attribute_'.$attribute_item_code] = $attribute_item_name;
                }
            }
        }

        if (empty($arr_header)) return false;

        $column = $column_old = $column_end_product = $column_start_item = $column_end_item = 'A';
        $row = 2;
        $row_category = 2;

        $sheet_category->setCellValue('A1', __d('admin', 'danh_muc_san_pham'));
        $sheet_category->getStyle('A1')->getAlignment()->setHorizontal('center');
        $sheet_category->getStyle('A1')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(660, 'pt');
        foreach ($categories as $key => $data_cate) {
            $sheet_category->setCellValue('A' . $row_category, $data_cate);
            $row_category++;

        }

        foreach ($arr_header as $key => $header) {
            if ($key == 'items_code') {
                $column_end_product = $column_old;
                $column_start_item = $column;
            }

            $sheet->setCellValue($column . $row, $header);
            $sheet->getStyle($column . $row)->getFont()->setBold(true);

            switch ($key) {
                case 'id':
                    $spreadsheet->getActiveSheet()->getColumnDimension($column)->setWidth(25, 'pt');
                    $sheet->getStyle($column . $row)->getAlignment()->setHorizontal('center');
                    break;
                case 'name':
                    $spreadsheet->getActiveSheet()->getColumnDimension($column)->setWidth(300, 'pt');
                    break;
                case 'lang':
                    $spreadsheet->getActiveSheet()->getColumnDimension($column)->setWidth(100, 'pt');
                    $sheet->getStyle($column . $row)->getAlignment()->setHorizontal('center');
                    break;
                case 'category':
                    $spreadsheet->getActiveSheet()->getColumnDimension($column)->setWidth(120, 'pt');
                    break;
                case 'brand':
                    $spreadsheet->getActiveSheet()->getColumnDimension($column)->setWidth(110, 'pt');
                    break;
                case 'featured':
                    $spreadsheet->getActiveSheet()->getColumnDimension($column)->setWidth(60, 'pt');
                    $sheet->getStyle($column . $row)->getAlignment()->setHorizontal('center');
                    break;
                case 'catalogue':
                    $spreadsheet->getActiveSheet()->getColumnDimension($column)->setWidth(60, 'pt');
                    $sheet->getStyle($column . $row)->getAlignment()->setHorizontal('center');
                    break;
                case 'position':
                    $spreadsheet->getActiveSheet()->getColumnDimension($column)->setWidth(50, 'pt');
                    $sheet->getStyle($column . $row)->getAlignment()->setHorizontal('center');
                    break;
                case 'status':
                    $spreadsheet->getActiveSheet()->getColumnDimension($column)->setWidth(90, 'pt');
                    $sheet->getStyle($column . $row)->getAlignment()->setHorizontal('center');
                    break;
                case 'description':
                    $spreadsheet->getActiveSheet()->getColumnDimension($column)->setWidth(250, 'pt');
                    break;
                case 'items_code':
                    $spreadsheet->getActiveSheet()->getColumnDimension($column)->setWidth(130, 'pt');
                    break;
                case 'items_price':
                    $spreadsheet->getActiveSheet()->getColumnDimension($column)->setWidth(80, 'pt');
                    $sheet->getStyle($column . $row)->getAlignment()->setHorizontal('center');
                    break;
                case 'items_price_special':
                    $spreadsheet->getActiveSheet()->getColumnDimension($column)->setWidth(80, 'pt');
                    $sheet->getStyle($column . $row)->getAlignment()->setHorizontal('center');
                    break;
                case 'items_time_start_special':
                    $spreadsheet->getActiveSheet()->getColumnDimension($column)->setWidth(100, 'pt');
                    $sheet->getStyle($column . $row)->getAlignment()->setHorizontal('center');
                    break;
                case 'items_time_end_special':
                    $spreadsheet->getActiveSheet()->getColumnDimension($column)->setWidth(100, 'pt');
                    break;
                case 'items_quantity_available':
                    $spreadsheet->getActiveSheet()->getColumnDimension($column)->setWidth(80, 'pt');
                    $sheet->getStyle($column . $row)->getAlignment()->setHorizontal('center');
                    break;
                case 'items_status_item':
                    $spreadsheet->getActiveSheet()->getColumnDimension($column)->setWidth(120, 'pt');
                    $sheet->getStyle($column . $row)->getAlignment()->setHorizontal('center');
                    break;
                default: 
                    $spreadsheet->getActiveSheet()->getColumnDimension($column)->setAutoSize(true);
            }

            $column_old = $column_end_item = $column;
            $column++;

        }

        if (!empty($column_end_product)) {
            $sheet->setCellValue('A1', __d('admin', 'thong_tin_san_pham'));
            $spreadsheet->getActiveSheet()->mergeCells('A1:' . $column_end_product . '1');
            $sheet->getStyle('A1:' . $column_end_product . '1')->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A1:' . $column_end_product . '1')->getAlignment()->setVertical('center');
            $spreadsheet->getActiveSheet()->getStyle('A1')->getFont()->setSize(16);
            $spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(30);
            $sheet->getStyle('A1')->getFont()->setBold(true);
        }

        if (!empty($column_start_item)) {
            $sheet->setCellValue($column_start_item . '1', __d('admin', 'thong_tin_phien_ban_san_pham'));
            $sheet->getStyle($column_start_item . '1')->getFont()->setBold(true);
            $spreadsheet->getActiveSheet()->getStyle($column_start_item . '1')->getFont()->setSize(16);
            $spreadsheet->getActiveSheet()->mergeCells($column_start_item . '1:' . $column_end_item . '1');
            $sheet->getStyle($column_start_item . '1:' . $column_end_item . '1')->getAlignment()->setHorizontal('center');
            $sheet->getStyle($column_start_item . '1:' . $column_end_item . '1')->getAlignment()->setVertical('center');
        }

        $row_excel = 3;
        foreach ($data as $key => $item) { 

            // thêm dữ liệu full vào row excel
            $colum_excel = 'A';
            foreach ($arr_header as $code => $header) {

                switch ($code) {
                    case 'id':
                        $sheet->setCellValue($colum_excel . $row_excel, !empty($item[$code]) ? $item[$code] : '');
                        $sheet->getStyle($colum_excel . $row_excel)->getAlignment()->setHorizontal('center');

                        break;
                    case 'lang':
                        $sheet->setCellValue($colum_excel . $row_excel, !empty($item[$code]) ? $languages[$item[$code]] : '');

                        $validation = $spreadsheet->getActiveSheet()->getCell($colum_excel.$row_excel)->getDataValidation();
                        $validation->setType( \PhpOffice\PhpSpreadsheet\Cell\Datavalidation::TYPE_LIST );
                        $validation->setErrorStyle( \PhpOffice\PhpSpreadsheet\Cell\Datavalidation::STYLE_INFORMATION );
                        $validation->setAllowBlank(false);
                        $validation->setShowInputMessage(true);
                        $validation->setShowErrorMessage(true);
                        $validation->setShowDropDown(true);
                        $validation->setErrorTitle('Input error');
                        $validation->setError('Value is not in list.');
                        $validation->setPromptTitle('Pick from list');
                        $validation->setPrompt('Please pick a value from the drop-down list.');
                        $validation->setFormula1('"' . $data_dropdown['languages'] . '"');

                        $sheet->getStyle($colum_excel . $row_excel)->getAlignment()->setHorizontal('center');

                        break;
                    case 'category':
                        $categories_item = [];
                        if (!empty($item['categories'])) {
                            foreach ($item['categories'] as $key => $val_cate) {
                                $categories_item_name = !empty($val_cate['id']) ? $categories[$val_cate['id']] : null;

                                if (empty($categories_item_name)) continue;
                                array_push($categories_item, $categories_item_name);
                            }
                        }

                        $sheet->setCellValue($colum_excel . $row_excel, !empty($item['categories']) ? implode('||', $categories_item) : '');

                        break;
                    case 'brand':
                        $sheet->setCellValue($colum_excel . $row_excel, !empty($item['brand_name']) ? $item['brand_name'] : '');

                        $validation = $spreadsheet->getActiveSheet()->getCell($colum_excel.$row_excel)->getDataValidation();
                        $validation->setType( \PhpOffice\PhpSpreadsheet\Cell\Datavalidation::TYPE_LIST );
                        $validation->setErrorStyle( \PhpOffice\PhpSpreadsheet\Cell\Datavalidation::STYLE_INFORMATION );
                        $validation->setAllowBlank(false);
                        $validation->setShowInputMessage(true);
                        $validation->setShowErrorMessage(true);
                        $validation->setShowDropDown(true);
                        $validation->setErrorTitle('Input error');
                        $validation->setError('Value is not in list.');
                        $validation->setPromptTitle('Pick from list');
                        $validation->setPrompt('Please pick a value from the drop-down list.');
                        $validation->setFormula1('"' . $data_dropdown['brands'] . '"');

                        break;
                    case 'featured':
                        $sheet->setCellValue($colum_excel . $row_excel, !empty($item['featured']) ? __d('admin', 'co') : __d('admin', 'khong'));

                        $validation = $spreadsheet->getActiveSheet()->getCell($colum_excel.$row_excel)->getDataValidation();
                        $validation->setType( \PhpOffice\PhpSpreadsheet\Cell\Datavalidation::TYPE_LIST );
                        $validation->setErrorStyle( \PhpOffice\PhpSpreadsheet\Cell\Datavalidation::STYLE_INFORMATION );
                        $validation->setAllowBlank(false);
                        $validation->setShowInputMessage(true);
                        $validation->setShowErrorMessage(true);
                        $validation->setShowDropDown(true);
                        $validation->setErrorTitle('Input error');
                        $validation->setError('Value is not in list.');
                        $validation->setPromptTitle('Pick from list');
                        $validation->setPrompt('Please pick a value from the drop-down list.');
                        $validation->setFormula1('"' . $data_dropdown['true_false'] . '"');

                        $sheet->getStyle($colum_excel . $row_excel)->getAlignment()->setHorizontal('center');

                        break;
                    case 'catalogue':
                        $sheet->setCellValue($colum_excel . $row_excel, !empty($item['catalogue']) ? __d('admin', 'co') : __d('admin', 'khong'));

                        $validation = $spreadsheet->getActiveSheet()->getCell($colum_excel.$row_excel)->getDataValidation();
                        $validation->setType( \PhpOffice\PhpSpreadsheet\Cell\Datavalidation::TYPE_LIST );
                        $validation->setErrorStyle( \PhpOffice\PhpSpreadsheet\Cell\Datavalidation::STYLE_INFORMATION );
                        $validation->setAllowBlank(false);
                        $validation->setShowInputMessage(true);
                        $validation->setShowErrorMessage(true);
                        $validation->setShowDropDown(true);
                        $validation->setErrorTitle('Input error');
                        $validation->setError('Value is not in list.');
                        $validation->setPromptTitle('Pick from list');
                        $validation->setPrompt('Please pick a value from the drop-down list.');
                        $validation->setFormula1('"' . $data_dropdown['true_false'] . '"');

                        $sheet->getStyle($colum_excel . $row_excel)->getAlignment()->setHorizontal('center');

                        break;
                    case 'position':
                        $sheet->setCellValue($colum_excel . $row_excel, !empty($item[$code]) ? $item[$code] : '');
                        $sheet->getStyle($colum_excel . $row_excel)->getAlignment()->setHorizontal('center');

                        break;
                    case 'status':
                        $sheet->setCellValue($colum_excel . $row_excel, !empty($item['status']) ? __d('admin', 'hoat_dong') : __d('admin', 'ngung_hoat_dong'));

                        $validation = $spreadsheet->getActiveSheet()->getCell($colum_excel.$row_excel)->getDataValidation();
                        $validation->setType( \PhpOffice\PhpSpreadsheet\Cell\Datavalidation::TYPE_LIST );
                        $validation->setErrorStyle( \PhpOffice\PhpSpreadsheet\Cell\Datavalidation::STYLE_INFORMATION );
                        $validation->setAllowBlank(false);
                        $validation->setShowInputMessage(true);
                        $validation->setShowErrorMessage(true);
                        $validation->setShowDropDown(true);
                        $validation->setErrorTitle('Input error');
                        $validation->setError('Value is not in list.');
                        $validation->setPromptTitle('Pick from list');
                        $validation->setPrompt('Please pick a value from the drop-down list.');
                        $validation->setFormula1('"' . $data_dropdown['status'] . '"');

                        $sheet->getStyle($colum_excel . $row_excel)->getAlignment()->setHorizontal('center');

                        break;
                    case 'description':
                        $sheet->setCellValue($colum_excel . $row_excel, !empty($item['description']) ? html_entity_decode(strip_tags($item['description'])) : '');
                        $spreadsheet->getActiveSheet()->getStyle($colum_excel . $row_excel)->getAlignment()->setWrapText(true);

                        break;
                    case stristr($code, 'attribute_'):
                        $attribute_code = !empty($code) ? str_replace('attribute_', '', $code) : null;
                        $attribute_id = !empty($attributes_product[$attribute_code]['id']) ? intval($attributes_product[$attribute_code]['id']) : null;
                        $input_type = !empty($attributes_product[$attribute_code]['input_type']) ? $attributes_product[$attribute_code]['input_type'] : null;
                        $options = $attribute_component->getListOptionsByAttributeId($attribute_id);

                        $attribute_value = !empty($item['attributes'][$attribute_code]['value']) ? $item['attributes'][$attribute_code]['value'] : '';
                        
                        switch ($input_type) {
                            case SWITCH_INPUT:
                                $sheet->setCellValue($colum_excel . $row_excel, !empty($attribute_value) ? __d('admin', 'co') : __d('admin', 'khong'));

                                $validation = $spreadsheet->getActiveSheet()->getCell($colum_excel.$row_excel)->getDataValidation();
                                $validation->setType( \PhpOffice\PhpSpreadsheet\Cell\Datavalidation::TYPE_LIST );
                                $validation->setErrorStyle( \PhpOffice\PhpSpreadsheet\Cell\Datavalidation::STYLE_INFORMATION );
                                $validation->setAllowBlank(false);
                                $validation->setShowInputMessage(true);
                                $validation->setShowErrorMessage(true);
                                $validation->setShowDropDown(true);
                                $validation->setErrorTitle('Input error');
                                $validation->setError('Value is not in list.');
                                $validation->setPromptTitle('Pick from list');
                                $validation->setPrompt('Please pick a value from the drop-down list.');
                                $validation->setFormula1('"' . $data_dropdown['true_false'] . '"');

                                $sheet->getStyle($colum_excel . $row_excel)->getAlignment()->setHorizontal('center');

                                $attribute_value = !empty($attribute_value) ? __d('admin', 'co') : __d('admin', 'khong');
                                break;

                            case SINGLE_SELECT:
                                $dropdown_options = implode(',', $options);

                                $validation = $spreadsheet->getActiveSheet()->getCell($colum_excel.$row_excel)->getDataValidation();
                                $validation->setType( \PhpOffice\PhpSpreadsheet\Cell\Datavalidation::TYPE_LIST );
                                $validation->setErrorStyle( \PhpOffice\PhpSpreadsheet\Cell\Datavalidation::STYLE_INFORMATION );
                                $validation->setAllowBlank(false);
                                $validation->setShowInputMessage(true);
                                $validation->setShowErrorMessage(true);
                                $validation->setShowDropDown(true);
                                $validation->setErrorTitle('Input error');
                                $validation->setError('Value is not in list.');
                                $validation->setPromptTitle('Pick from list');
                                $validation->setPrompt('Please pick a value from the drop-down list.');
                                $validation->setFormula1('"' . $dropdown_options . '"');

                                $attribute_value = !empty($attribute_value) ? $options[$attribute_value] : '';
                                break;

                            case MULTIPLE_SELECT:
                                if (!empty($attribute_value)) {
                                    foreach ($attribute_value as $key => $attr_val) {
                                        $val_attribute = !empty($options[intval($attr_val)]) ? $options[intval($attr_val)] : '';
                                        $attribute_value[$key] = $val_attribute;
                                    }
                                }

                                $attribute_value = !empty($attribute_value) ? implode('||', $attribute_value) : '';
                                
                                break;

                            case IMAGES:
                            case FILES:
                                $attribute_value = !empty($attribute_value) ? json_decode($attribute_value, true) : [];

                                if (!empty($attribute_value)) {
                                    foreach ($attribute_value as $key => $image) {
                                        $attribute_value[$key] = !empty($image) ? CDN_URL . $image : '';
                                    }
                                }

                                $attribute_value = !empty($attribute_value) ? implode('||', $attribute_value) : '';

                                break;

                            case IMAGE:
                                $attribute_value = !empty($attribute_value) ? CDN_URL . $attribute_value : '';

                                break;

                            case DATE_TIME:
                                if (empty($attribute_value)) break;

                                $datetime = Time::createFromFormat('d/m/Y - H:i', $attribute_value, null);
                                $datetime = strtotime($datetime->format('Y-m-d H:i:s'));

                                $attribute_value = !empty($datetime) ? date('H:i - d/m/Y', $datetime) : '';

                                break;

                            default:
                                $attribute_value = !empty($attribute_value) ? html_entity_decode(strip_tags($attribute_value)) : '';
                                break;
                        }

                        $sheet->setCellValue($colum_excel . $row_excel, $attribute_value);
                        $spreadsheet->getActiveSheet()->getStyle($colum_excel . $row_excel)->getAlignment()->setWrapText(true);
                        break;
                    case stristr($code, 'items_'):
                        $code = !empty($code) ? str_replace('items_', '', $code) : null;
                        $first_item = !empty($item['items'][0]) ? $item['items'][0] : [];

                        switch ($code) {
                            case 'images':
                                $item_images = [];
                                if (!empty($first_item[$code])) {
                                    foreach ($first_item[$code] as $key => $image) {
                                        if (empty($image)) continue;

                                        $item_images[$key] = CDN_URL . $image;
                                    }
                                }

                                $item_images = !empty($item_images) ? implode('||', $item_images) : '';
                                $sheet->setCellValue($colum_excel . $row_excel, $item_images);

                                break;
                            case 'price':
                            case 'price_special':
                                $price = !empty($first_item[$code]) ? number_format(floatval($first_item[$code])) : '';

                                $sheet->setCellValue($colum_excel . $row_excel, $price);
                                $sheet->getStyle($colum_excel . $row_excel)->getAlignment()->setHorizontal('center');

                                break;
                            case 'time_start_special':
                            case 'time_end_special':
                                $time_special = !empty($first_item[$code]) ? date('H:i - d/m/Y', $first_item[$code]) : '';

                                $sheet->setCellValue($colum_excel . $row_excel, $time_special);
                                $sheet->getStyle($colum_excel . $row_excel)->getAlignment()->setHorizontal('center');

                                break;
                            case 'status_item':
                                $sheet->setCellValue($colum_excel . $row_excel, !empty($first_item['status']) ? __d('admin', 'hoat_dong') : __d('admin', 'ngung_hoat_dong'));

                                $validation = $spreadsheet->getActiveSheet()->getCell($colum_excel.$row_excel)->getDataValidation();
                                $validation->setType( \PhpOffice\PhpSpreadsheet\Cell\Datavalidation::TYPE_LIST );
                                $validation->setErrorStyle( \PhpOffice\PhpSpreadsheet\Cell\Datavalidation::STYLE_INFORMATION );
                                $validation->setAllowBlank(false);
                                $validation->setShowInputMessage(true);
                                $validation->setShowErrorMessage(true);
                                $validation->setShowDropDown(true);
                                $validation->setErrorTitle('Input error');
                                $validation->setError('Value is not in list.');
                                $validation->setPromptTitle('Pick from list');
                                $validation->setPrompt('Please pick a value from the drop-down list.');
                                $validation->setFormula1('"' . $data_dropdown['status'] . '"');

                                $sheet->getStyle($colum_excel . $row_excel)->getAlignment()->setHorizontal('center');

                                break;
                            default:
                                $sheet->setCellValue($colum_excel . $row_excel, !empty($first_item[$code]) ? $first_item[$code] : '');
                                break;
                        }

                        break;
                    case stristr($code, 'item_attribute_'):
                        $attribute_code = !empty($code) ? str_replace('item_attribute_', '', $code) : null;
                        $attribute_id = !empty($attributes_item[$attribute_code]['id']) ? intval($attributes_item[$attribute_code]['id']) : null;
                        $input_type = !empty($attributes_item[$attribute_code]['input_type']) ? $attributes_item[$attribute_code]['input_type'] : null;
                        $options = $attribute_component->getListOptionsByAttributeId($attribute_id);

                        $first_item = !empty($item['items'][0]) ? $item['items'][0] : [];
                        $item_attribute = !empty($first_item['attributes']) ? Hash::combine($first_item['attributes'], '{n}.code', '{n}') : [];

                        $attribute_value = !empty($item_attribute[$attribute_code]['value']) ? $item_attribute[$attribute_code]['value'] : null;

                        switch ($input_type) {
                            case SWITCH_INPUT:
                                $sheet->setCellValue($colum_excel . $row_excel, !empty($attribute_value) ? __d('admin', 'co') : __d('admin', 'khong'));

                                $validation = $spreadsheet->getActiveSheet()->getCell($colum_excel.$row_excel)->getDataValidation();
                                $validation->setType( \PhpOffice\PhpSpreadsheet\Cell\Datavalidation::TYPE_LIST );
                                $validation->setErrorStyle( \PhpOffice\PhpSpreadsheet\Cell\Datavalidation::STYLE_INFORMATION );
                                $validation->setAllowBlank(false);
                                $validation->setShowInputMessage(true);
                                $validation->setShowErrorMessage(true);
                                $validation->setShowDropDown(true);
                                $validation->setErrorTitle('Input error');
                                $validation->setError('Value is not in list.');
                                $validation->setPromptTitle('Pick from list');
                                $validation->setPrompt('Please pick a value from the drop-down list.');
                                $validation->setFormula1('"' . $data_dropdown['true_false'] . '"');

                                $sheet->getStyle($colum_excel . $row_excel)->getAlignment()->setHorizontal('center');

                                $attribute_value = !empty($attribute_value) ? __d('admin', 'co') : __d('admin', 'khong');
                                break;

                            case SINGLE_SELECT:
                                $dropdown_options = implode(',', $options);

                                $validation = $spreadsheet->getActiveSheet()->getCell($colum_excel.$row_excel)->getDataValidation();
                                $validation->setType( \PhpOffice\PhpSpreadsheet\Cell\Datavalidation::TYPE_LIST );
                                $validation->setErrorStyle( \PhpOffice\PhpSpreadsheet\Cell\Datavalidation::STYLE_INFORMATION );
                                $validation->setAllowBlank(false);
                                $validation->setShowInputMessage(true);
                                $validation->setShowErrorMessage(true);
                                $validation->setShowDropDown(true);
                                $validation->setErrorTitle('Input error');
                                $validation->setError('Value is not in list.');
                                $validation->setPromptTitle('Pick from list');
                                $validation->setPrompt('Please pick a value from the drop-down list.');
                                $validation->setFormula1('"' . $dropdown_options . '"');

                                $attribute_value = !empty($attribute_value) ? $options[$attribute_value] : '';
                                
                                break;

                            case MULTIPLE_SELECT:
                                $attribute_value = !empty($attribute_value) ? json_decode($attribute_value, true) : [];

                                if ($attribute_value) {
                                    foreach ($attribute_value as $key => $attr_val) {
                                        $val_attribute = !empty($options[intval($attr_val)]) ? $options[intval($attr_val)] : '';
                                        $attribute_value[$key] = $val_attribute;
                                    }
                                }

                                $attribute_value = !empty($attribute_value) ? implode('||', $attribute_value) : '';
                                
                                break;

                            case DATE:
                                $attribute_value = !empty($attribute_value) ? date('d/m/Y', $attribute_value) : '';
                                
                                break;

                            case DATE_TIME:
                                $attribute_value = !empty($attribute_value) ? date('H:i - d/m/Y', $attribute_value) : '';
                                
                                break;
                            
                            default:
                                $attribute_value = !empty($attribute_value) ? html_entity_decode(strip_tags($attribute_value)) : '';

                                break;
                        }

                        $sheet->setCellValue($colum_excel . $row_excel, $attribute_value);
                        $spreadsheet->getActiveSheet()->getStyle($colum_excel . $row_excel)->getAlignment()->setWrapText(true);
                        break;
                    default:
                        $sheet->setCellValue($colum_excel . $row_excel, !empty($item[$code]) ? $item[$code] : '');
                        break;
                }

                $sheet->getStyle($colum_excel.$row_excel)->getAlignment()->setVertical('center');
                $colum_excel ++;
            }

            // thêm dữ liệu của item vào row excel
            if(!empty($item['items']) && count($item['items']) > 1){
                foreach ($item['items'] as $key => $item) {
                    if ($key > 0) {
                        $this->insertDataRowItemExcel($sheet, $spreadsheet, $row_excel, $arr_header, $item, $data_dropdown);
                    }
                }
            }

            $row_excel ++;
        }

        return $spreadsheet;
    }

    public function exportExcelProduct($data = [])
    {
        if(empty($data)) return false;

        $spreadsheet = $this->initializationExcel($data);
        $writer = new Xlsx($spreadsheet);

        ob_start();
        $writer->save('php://output');
        $xlsData = ob_get_contents();
        ob_end_clean();
        
        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData),
            META => [
                'name' => __d('admin', 'thong_tin_san_pham') . time()
            ]
        ]);
    }

    private function insertDataRowItemExcel($sheet = null, $spreadsheet = null, &$row_excel = null, $arr_header = [], $data_items = [], $data_dropdown = [])
    {
        $row_excel ++;
        $colum_excel = 'A';
        $attribute_component = $this->loadComponent('Admin.Attribute');

        foreach ($arr_header as $code => $header) {

            switch ($code) {
                case stristr($code, 'items_'):
                    $code = !empty($code) ? str_replace('items_', '', $code) : null;

                    switch ($code) {
                        case 'images':
                            $item_images = [];
                            if (!empty($data_items[$code])) {
                                foreach ($data_items[$code] as $key => $image) {
                                    if (empty($image)) continue;

                                    $item_images[$key] = CDN_URL . $image;
                                }
                            }

                            $item_images = !empty($item_images) ? implode('||', $item_images) : '';
                            $sheet->setCellValue($colum_excel . $row_excel, $item_images);

                            break;
                        case 'price':
                        case 'price_special':
                            $price = !empty($data_items[$code]) ? number_format(floatval($data_items[$code])) : '';

                            $sheet->setCellValue($colum_excel . $row_excel, $price);
                            $sheet->getStyle($colum_excel . $row_excel)->getAlignment()->setHorizontal('center');

                            break;
                        case 'time_start_special':
                        case 'time_end_special':
                            $time_special = !empty($data_items[$code]) ? date('H:i - d/m/Y', $data_items[$code]) : '';

                            $sheet->setCellValue($colum_excel . $row_excel, $time_special);
                            $sheet->getStyle($colum_excel . $row_excel)->getAlignment()->setHorizontal('center');

                            break;
                        case 'quantity_available':
                            $sheet->setCellValue($colum_excel . $row_excel, !empty($data_items[$code]) ? $data_items[$code] : '');
                            $sheet->getStyle($colum_excel . $row_excel)->getAlignment()->setHorizontal('center');

                            break;
                        case 'status_item':
                            $sheet->setCellValue($colum_excel . $row_excel, !empty($data_items['status']) ? __d('admin', 'hoat_dong') : __d('admin', 'ngung_hoat_dong'));

                            $validation = $spreadsheet->getActiveSheet()->getCell($colum_excel.$row_excel)->getDataValidation();
                            $validation->setType( \PhpOffice\PhpSpreadsheet\Cell\Datavalidation::TYPE_LIST );
                            $validation->setErrorStyle( \PhpOffice\PhpSpreadsheet\Cell\Datavalidation::STYLE_INFORMATION );
                            $validation->setAllowBlank(false);
                            $validation->setShowInputMessage(true);
                            $validation->setShowErrorMessage(true);
                            $validation->setShowDropDown(true);
                            $validation->setErrorTitle('Input error');
                            $validation->setError('Value is not in list.');
                            $validation->setPromptTitle('Pick from list');
                            $validation->setPrompt('Please pick a value from the drop-down list.');
                            $validation->setFormula1('"' . $data_dropdown['status'] . '"');

                            $sheet->getStyle($colum_excel . $row_excel)->getAlignment()->setHorizontal('center');
                            break;
                        default:
                            $sheet->setCellValue($colum_excel . $row_excel, !empty($data_items[$code]) ? $data_items[$code] : '');
                            break;
                    }

                    break;
                case stristr($code, 'item_attribute_'):
                    $attribute_code = !empty($code) ? str_replace('item_attribute_', '', $code) : null;

                    $item_attribute = !empty($data_items['attributes']) ? Hash::combine($data_items['attributes'], '{n}.code', '{n}') : [];
                    $attribute_id = !empty($item_attribute[$attribute_code]['attribute_id']) ? intval($item_attribute[$attribute_code]['attribute_id']) : null;
                    $input_type = !empty($item_attribute[$attribute_code]['input_type']) ? $item_attribute[$attribute_code]['input_type'] : null;

                    $attribute_value = !empty($item_attribute[$attribute_code]['value']) ? $item_attribute[$attribute_code]['value'] : null;

                    switch ($input_type) {
                        case SWITCH_INPUT:
                            $sheet->setCellValue($colum_excel . $row_excel, !empty($attribute_value) ? __d('admin', 'co') : __d('admin', 'khong'));

                            $validation = $spreadsheet->getActiveSheet()->getCell($colum_excel.$row_excel)->getDataValidation();
                            $validation->setType( \PhpOffice\PhpSpreadsheet\Cell\Datavalidation::TYPE_LIST );
                            $validation->setErrorStyle( \PhpOffice\PhpSpreadsheet\Cell\Datavalidation::STYLE_INFORMATION );
                            $validation->setAllowBlank(false);
                            $validation->setShowInputMessage(true);
                            $validation->setShowErrorMessage(true);
                            $validation->setShowDropDown(true);
                            $validation->setErrorTitle('Input error');
                            $validation->setError('Value is not in list.');
                            $validation->setPromptTitle('Pick from list');
                            $validation->setPrompt('Please pick a value from the drop-down list.');
                            $validation->setFormula1('"' . $data_dropdown['true_false'] . '"');

                            $sheet->getStyle($colum_excel . $row_excel)->getAlignment()->setHorizontal('center');

                            $attribute_value = !empty($attribute_value) ? __d('admin', 'co') : __d('admin', 'khong');
                            break;

                        case SINGLE_SELECT:
                            $options = $attribute_component->getListOptionsByAttributeId($attribute_id);

                            $dropdown_options = implode(',', $options);

                            $validation = $spreadsheet->getActiveSheet()->getCell($colum_excel.$row_excel)->getDataValidation();
                            $validation->setType( \PhpOffice\PhpSpreadsheet\Cell\Datavalidation::TYPE_LIST );
                            $validation->setErrorStyle( \PhpOffice\PhpSpreadsheet\Cell\Datavalidation::STYLE_INFORMATION );
                            $validation->setAllowBlank(false);
                            $validation->setShowInputMessage(true);
                            $validation->setShowErrorMessage(true);
                            $validation->setShowDropDown(true);
                            $validation->setErrorTitle('Input error');
                            $validation->setError('Value is not in list.');
                            $validation->setPromptTitle('Pick from list');
                            $validation->setPrompt('Please pick a value from the drop-down list.');
                            $validation->setFormula1('"' . $dropdown_options . '"');

                            $attribute_value = !empty($attribute_value) ? $options[$attribute_value] : '';
                            
                            break;

                        case MULTIPLE_SELECT:
                            $options = $attribute_component->getListOptionsByAttributeId($attribute_id);
                            $attribute_value = !empty($attribute_value) ? json_decode($attribute_value, true) : [];

                            if ($attribute_value) {
                                foreach ($attribute_value as $key => $attr_val) {
                                    $val_attribute = !empty($options[intval($attr_val)]) ? $options[intval($attr_val)] : '';
                                    $attribute_value[$key] = $val_attribute;
                                }
                            }

                            $attribute_value = !empty($attribute_value) ? implode('||', $attribute_value) : '';
                            
                            break;

                        case DATE:
                            $attribute_value = !empty($attribute_value) ? date('d/m/Y', $attribute_value) : '';
                            
                            break;

                        case DATE_TIME:
                            $attribute_value = !empty($attribute_value) ? date('H:i - d/m/Y', $attribute_value) : '';
                            
                            break;
                        
                        default:
                            $attribute_value = !empty($attribute_value) ? html_entity_decode(strip_tags($attribute_value)) : '';

                            break;
                    }

                    $sheet->setCellValue($colum_excel . $row_excel, $attribute_value);
                    $spreadsheet->getActiveSheet()->getStyle($colum_excel . $row_excel)->getAlignment()->setWrapText(true);
                    break;
            }

            $sheet->getStyle($colum_excel.$row_excel)->getAlignment()->setVertical('center');
            $colum_excel ++;
        }
    }

    public function add()
    {
        $settings = TableRegistry::get('Settings')->getSettingWebsite();        

        // thuộc tính phiên bản sản phẩm theo danh mục
        $attributes_item = TableRegistry::get('Attributes')->getAttributeByMainCategory(null, PRODUCT_ITEM, $this->lang);
        $list_attributes_special = TableRegistry::get('Attributes')->getSpecialAttributeItemByMainCategory(null, $this->lang);        
        // debug($list_attributes_special);die;
        // cấu hình thuộc tính sản phẩm theo danh mục
        $setting_attributes_category = !empty($settings['attributes_category']) ? $settings['attributes_category'] : null;
        $attribute_by_category = !empty($setting_attributes_category['status']) ? true : false;

        // cấu hình thương hiệu theo danh mục
        $brand_setting = !empty($settings['brands_category']) ? $settings['brands_category'] : [];
        $brand_by_category = !empty($brand_setting['status']) ? true : false;

        // cấu hình mã nhúng thuộc tính mở rộng vào nội dung bài viết
        $embed_attribute = [];
        $setting_embed_attribute = !empty($settings['attribute_product']) ? $settings['attribute_product'] : [];
        if(!empty($setting_embed_attribute['use_embed_attribute'])){
            $embed_attribute = !empty($setting_embed_attribute['config_embed_attribute']) ? json_decode($setting_embed_attribute['config_embed_attribute'], true) : [];
        }

        // options attribute
        $all_options = Hash::combine(TableRegistry::get('AttributesOptions')->getAll($this->lang), '{n}.id', '{n}.name','{n}.attribute_id');

        $max_record = TableRegistry::get('Products')->find()->select('id')->max('id');

        $this->set('list_attributes_special', $list_attributes_special);
        $this->set('attributes_item', $attributes_item);
        
        $this->set('all_options', $all_options);
        $this->set('attributes_id_selected', []);

        $this->set('length_unit', Configure::read('LENGTH_UNIT'));
        $this->set('weight_unit', Configure::read('WEIGTH_UNIT'));
        $this->set('position', !empty($max_record->id) ? $max_record->id + 1 : 1);

        $this->set('main_categories', []);
        $this->set('main_category_id', null);
        $this->set('embed_attribute', $embed_attribute);

        $this->set('brand_by_category', $brand_by_category);
        $this->set('attribute_by_category', $attribute_by_category);
        
        $this->css_page = [
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.css'
        ];

        $this->js_page = [
            '/assets/plugins/custom/tinymce6/tinymce.min.js',
            '/assets/js/seo_analysis.js',
            '/assets/js/pages/product.js',
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.js'
        ];

        $this->set('path_menu', 'product_add');
        $this->set('title_for_layout', __d('admin', 'them_san_pham'));
        $this->render('update');
    }

    public function update($id = null)
    {
        // thông tin sản phẩm
        $table = TableRegistry::get('Products');    
        $product_detail = $table->getDetailProduct($id, $this->lang, [
            'get_user' => true, 
            'get_categories' => true,
            'get_attributes' => true,
            'get_item_attributes' => true,
            'get_tags' => true
        ]);

        $product = $table->formatDataProductDetail($product_detail, $this->lang);
        if(empty($product)) $this->showErrorPage();

        
        // danh mục chính
        $main_category_id = !empty($product['main_category_id']) ? intval($product['main_category_id']) : null;

        $main_categories = [];
        if(!empty($product['categories'])) {
            foreach($product['categories'] as $category_id => $category_main){
                if(empty($category_main['id']) || empty($category_main['name'])) continue;
                $main_categories[$category_id] = $category_main['name'];
            }
        }

        // tất cả cấu hình
        $settings = TableRegistry::get('Settings')->getSettingWebsite();

        // thuộc tính phiên bản sản phẩm theo danh mục
        $attributes_item = TableRegistry::get('Attributes')->getAttributeByMainCategory($main_category_id, PRODUCT_ITEM, $this->lang);
        $list_attributes_special = TableRegistry::get('Attributes')->getSpecialAttributeItemByMainCategory($main_category_id, $this->lang);
        
        // options thuộc tính mở rộng
        $all_options = Hash::combine(TableRegistry::get('AttributesOptions')->getAll($this->lang), '{n}.id', '{n}.name', '{n}.attribute_id');

        // thuộc tính đặc biệt
        $has_attribute_image = false;
        $attributes_special = $options_special_selected = $item_images = $attributes_id_selected = $attribute_item_value = [];
        
        if(!empty($product_detail['ProductsItemAttribute'])){
            $all_attributes = Hash::combine(TableRegistry::get('Attributes')->getAll($this->lang), '{n}.id', '{n}', '{n}.attribute_type');
            foreach($product_detail['ProductsItemAttribute'] as $item){
                $attribute_id = !empty($item['attribute_id']) ? intval($item['attribute_id']) : null;
                $product_item_id = !empty($item['product_item_id']) ? intval($item['product_item_id']) : null;
                $attribute_info = !empty($all_attributes[PRODUCT_ITEM][$attribute_id]) ? $all_attributes[PRODUCT_ITEM][$attribute_id] : [];
                
                $code = !empty($attribute_info['code']) ? $attribute_info['code'] : null;
                $input_type = !empty($attribute_info['input_type']) ? $attribute_info['input_type'] : null;
                $list_options = !empty($all_options[$attribute_id]) ? $all_options[$attribute_id] : [];

                switch ($input_type) {
                    case DATE:
                        $value = !empty($item['value']) ? date('d/m/Y', $item['value']) : null;
                        break;

                    case DATE_TIME:
                        $value = !empty($item['value']) ? date('d/m/Y - H:i', $item['value']) : null;
                        break;

                    case SWITCH_INPUT:
                        $value = isset($item['value']) ? intval($item['value']) : 0;
                        break;
                    
                    default:
                        $value = !empty($item['value']) ? $item['value'] : null;
                        break;
                }

                $attribute_item_value[$product_item_id][$attribute_id] = $value;

                if(empty($product_item_id) || $input_type != SPECICAL_SELECT_ITEM) continue;

                if(!in_array($attribute_id, $attributes_id_selected)){
                    $attributes_id_selected[] = $attribute_id;
                }
                
                if(!isset($options_special_selected[$code])) $options_special_selected[$code] = [];
                if(!in_array($value, $options_special_selected[$code])){
                    $options_special_selected[$code][$value] = !empty($list_options[$value]) ? $list_options[$value] : null;
                }
                
                if(!empty($product['items'])){
                    foreach($product['items'] as $product_item){ 
                        if($product_item['id'] == $product_item_id && !empty($attribute_info['has_image'])){
                            $item_images[$code . '_' . $value] = $product_item['images'];
                        }
                    }
                }
            }
            
            $parse_data = $this->parseDataAttributeSpecialSelected($attributes_id_selected);

            $has_attribute_image = !empty($parse_data['has_attribute_image']) ? true : false;

            // kiểm tra nếu có trên 2 thuộc tính đặc biệt được áp dụng
            $attributes_special = !empty($parse_data['attributes_special']) ? $parse_data['attributes_special'] : [];
            
            if(!empty(($attributes_special))){
                $has_image = false;
                foreach($attributes_special as $k => $attribute){
                    if(!empty($attribute['has_image']) && !$has_image){
                        $has_image = true;
                        continue;
                    }

                    if(!empty($attribute['has_image']) && $has_image){
                        $attribute['has_image'] = 0;
                        $attributes_special[$k] = $attribute;
                    }
                }

                // sắp xếp lại theo column `has_image`
                array_multisort( array_column($attributes_special, 'has_image'), SORT_ASC, $attributes_special);
            } 
        }

        // cấu hình thuộc tính theo danh mục
        $setting_attributes_category = !empty($settings['attributes_category']) ? $settings['attributes_category'] : null;
        $attribute_by_category = !empty($setting_attributes_category['status']) ? true : false;

        // cấu hình thương hiệu theo danh mục
        $brand_setting = !empty($settings['brands_category']) ? $settings['brands_category'] : [];
        $brand_by_category = !empty($brand_setting['status']) ? true : false;

        // cấu hình mã nhúng thuộc tính mở rộng vào nội dung bài viết
        $embed_attribute = [];
        $setting_embed_attribute = !empty($settings['attribute_product']) ? $settings['attribute_product'] : [];
        if(!empty($setting_embed_attribute['use_embed_attribute'])){
            $embed_attribute = !empty($setting_embed_attribute['config_embed_attribute']) ? json_decode($setting_embed_attribute['config_embed_attribute'], true) : [];
        }

        $this->set('all_options', $all_options);

        $this->set('list_attributes_special', $list_attributes_special);
        $this->set('attributes_item', $attributes_item);
        
        $this->set('attributes_special', $attributes_special);
        $this->set('has_attribute_image', $has_attribute_image);
        $this->set('options_special_selected', $options_special_selected);
        $this->set('attributes_id_selected', $attributes_id_selected);
        $this->set('attribute_item_value', $attribute_item_value);

        $this->set('item_images', $item_images);
        $this->set('id', $id);
        $this->set('length_unit', Configure::read('LENGTH_UNIT'));
        $this->set('weight_unit', Configure::read('WEIGTH_UNIT'));
        $this->set('position', !empty($product['position']) ? $product['position'] : 1);
        $this->set('product', $product);

        $this->set('main_categories', $main_categories);
        $this->set('main_category_id', $main_category_id);
        $this->set('embed_attribute', $embed_attribute);
        $this->set('brand_by_category', $brand_by_category);
        $this->set('attribute_by_category', $attribute_by_category);

        $this->css_page = [
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.css'
        ];

        $this->js_page = [
            '/assets/plugins/custom/tinymce6/tinymce.min.js',
            '/assets/js/seo_analysis.js',
            '/assets/js/pages/product.js',
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.js'
        ];

        $this->set('path_menu', 'product');
        $this->set('title_for_layout', __d('admin', 'cap_nhat_san_pham'));
    }

    public function ajaxSeletAttributeSpecial()
    {
        $this->viewBuilder()->enableAutoLayout(false);

        $data = $this->getRequest()->getData();
        $attribute_ids = !empty($data['attribute_selected']) ? $data['attribute_selected'] : [];
        $main_category_id = !empty($data['main_category_id']) ? intval($data['main_category_id']) : null;

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        // attributes all
        $attributes_item = TableRegistry::get('Attributes')->getAttributeByMainCategory($main_category_id, PRODUCT_ITEM, $this->lang);

        $all_options = Hash::combine(TableRegistry::get('AttributesOptions')->getAll($this->lang), '{n}.id', '{n}.name', '{n}.attribute_id');
        $result = $this->parseDataAttributeSpecialSelected($attribute_ids);

        // kiểm tra nếu có trên 2 thuộc tính đặc biệt được áp dụng
        $attributes_special = !empty($result['attributes_special']) ? $result['attributes_special'] : [];     
        if(!empty(($attributes_special))){
            $has_image = false;
            foreach($attributes_special as $k => $attribute){
                if(!empty($attribute['has_image']) && !$has_image){
                    $has_image = true;
                    continue;
                }

                if(!empty($attribute['has_image']) && $has_image){
                    $attribute['has_image'] = 0;
                    $attributes_special[$k] = $attribute;
                }
            }

            // sắp xếp lại theo column `has_image`
            array_multisort( array_column($attributes_special, 'has_image'), SORT_ASC, $attributes_special);
        }        

        $this->set('attributes_special', $attributes_special);
        $this->set('has_attribute_image', !empty($result['has_attribute_image']) ? true : false);
        $this->set('attributes_item', $attributes_item); 
        $this->set('all_options', $all_options);
        $this->set('attribute_item_value', []);
        $this->render('items');
    }

    private function parseDataAttributeSpecialSelected($attribute_ids = [])
    {
        $result = [
            'attributes_special' => [],
            'has_attribute_image' => false
        ];

        if(empty($attribute_ids)) return $result;

        $attributes = $attributes_special = [];
        $has_attribute_image = false;

        if (!empty($attribute_ids)) {
            // get list attributes sort by has_image field
            $attributes = TableRegistry::get('Attributes')->queryListAttributes([
                FILTER => [
                    LANG => $this->lang,
                    'attribute_ids' => $attribute_ids,
                    'attribute_type' => PRODUCT_ITEM,
                    'input_type' => SPECICAL_SELECT_ITEM
                ],
                SORT => [
                    FIELD => 'position',
                    SORT => DESC
                ]
            ])->toArray();
        }

        if(!empty($attributes)){
            $all_options = Hash::combine(TableRegistry::get('AttributesOptions')->getAll($this->lang), '{n}.id', '{n}.name', '{n}.attribute_id');
            foreach($attributes as $attribute){
                $options = [];
                if(in_array($attribute['input_type'], Configure::read('ATTRIBUTE_HAS_LIST_OPTIONS'))){
                    $options = !empty($all_options[$attribute['id']]) ? $all_options[$attribute['id']] : [];
                }

                $has_image = !empty($attribute['has_image']) ? 1 : 0;
                $item = [
                    'id' => !empty($attribute['id']) ? intval($attribute['id']) : null,
                    'code' => !empty($attribute['code']) ? $attribute['code'] : null,
                    'input_type' => !empty($attribute['input_type']) ? $attribute['input_type'] : null,
                    'name' => !empty($attribute['AttributesContent']->name) ? $attribute['AttributesContent']->name : null,
                    'label' => !empty($attribute['AttributesContent']->name) ? $attribute['AttributesContent']->name : null,
                    'has_image' => $has_image,
                    'required' => !empty($attribute['required']) ? 1 : 0,
                    'options' => $options,
                ];

                $attributes_special[] = $item;

                if(!empty($has_image)){
                    $has_attribute_image = true;
                }
            }

            $result['attributes_special'] = $attributes_special;
            $result['has_attribute_image'] = $has_attribute_image;
        }

        return $result;
    }

    public function detail($id = null)
    {
        if(empty($id)){
            $this->showErrorPage();
        }
        $products_table = TableRegistry::get('Products');
        $product_detail = $products_table->getDetailProduct($id, $this->lang, [
            'get_user' => true, 
            'get_categories' => true,
            'get_attributes' => true,
            'get_item_attributes' => true,
            'get_tags' => true
        ]);
        $product = $products_table->formatDataProductDetail($product_detail, $this->lang);
        if(empty($product)){
            $this->showErrorPage();
        }

        // tạo mã QR code
        $domain = $this->request->scheme() . '://' . $this->request->host() . '/';
        $url = !empty($product['url']) ? $domain . $product['url'] : null;

        $create_qr = $this->loadComponent('QrCode')->generateQrCode(['url' => $url], URL);

        $qrcode_url = !empty($create_qr[DATA][URL]) ? $create_qr[DATA][URL] : null;

        $this->set('product', $product);
        $this->set('qrcode_url', $qrcode_url);

        $this->css_page = [
            '/assets/css/pages/wizard/wizard-4.css',
            '/assets/plugins/global/lightbox/lightbox.css'
        ];
        $this->js_page = [
            '/assets/plugins/global/lightbox/lightbox.min.js'
        ];

        $this->set('path_menu', 'product');
        $this->set('title_for_layout', __d('admin', 'chi_tiet_san_pham'));
    }

    public function save($id = null)
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();  
   
        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }
        
        $product_component = $this->loadComponent('Admin.Product');
        $attribute_component = $this->loadComponent('Admin.Attribute');
        $utilities = $this->loadComponent('Utilities');        
        $products_table = TableRegistry::get('Products');        
        $links_table = TableRegistry::get('Links');
        $attributes_table = TableRegistry::get('Attributes');
        $categories_product_table = TableRegistry::get('CategoriesProduct');
        $product_attribute_table = TableRegistry::get('ProductsAttribute');
        $item_attribute_table = TableRegistry::get('ProductsItemAttribute');
        $tags_table = TableRegistry::get('Tags');

        $product = [];
        if(!empty($id)){
            $product = $products_table->getDetailProduct($id, $this->lang, [
                'get_user' => false, 
                'get_categories' => true,
                'get_attributes' => true
            ]);

            if(empty($product)){
                $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
            }
        }

        // validate data
        if(empty($data['name'])){
            $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_ten_san_pham')]);
        }

        $link = !empty($data['link']) ? $utilities->formatToUrl(trim($data['link'])) : null;
        if(empty($link)){
            $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_duong_dan')]);
        }

        $link_id = !empty($product['Links']) ? $product['Links']['id'] : null;
        if($links_table->checkExist($link, $link_id)){
            $this->responseJson([MESSAGE => __d('admin', 'duong_dan_da_ton_tai_tren_he_thong')]);
        }

        $items = !empty($data['items']) ? json_decode($data['items'], true) : [];
        if(empty($items)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_phien_ban_san_pham')]);
        }

        // attributes
        $all_attributes = Hash::combine(TableRegistry::get('Attributes')->getAll($this->lang), '{n}.code', '{n}', '{n}.attribute_type');        
        $attributes_item = !empty($all_attributes[PRODUCT_ITEM]) ? $all_attributes[PRODUCT_ITEM] : [];

        // products item
        $products_item = $products_item_attribute = $images = $attribute_item_code = $list_code = [];
        foreach($items as $k => $item){
            $product_item_id = !empty($item['id']) ? intval($item['id']) : null;
            
            $code = !empty($item['code']) ? trim($item['code']) : null;        
            if(empty($code)){
                $code = $utilities->generateRandomString(10);
            }

            if(in_array($code, $list_code)){
                $this->responseJson([MESSAGE => __d('admin', 'ma_phien_ban_san_pham_khong_the_trung_nhau_vui_long_dieu_chinh_lai')]);
            }
            $list_code[] = $code;
            
            $price = !empty($item['price']) ? floatval(str_replace(',', '', $item['price'])) : null;
            $price_special = !empty($item['price_special']) ? floatval(str_replace(',', '', $item['price_special'])) : null;
            if(!empty($price) && $price <= $price_special){
                $this->responseJson([MESSAGE => __d('admin', 'gia_dac_biet_cua_san_pham_phai_nho_hon_gia_ban')]);
            }
            $status = !empty($item['status']) ? 1 : 0;
            $quantity_available = !empty($item['quantity_available']) ? intval(str_replace(',', '', $item['quantity_available'])) : null;

            $date_special = !empty($item['date_special']) ? trim($item['date_special']) : null;
            
            $time_start_special = $time_end_special = null;
            if(!empty($date_special)){
                $date_explode = explode(' → ', $date_special);

                $time_start = !empty($date_explode[0]) ? $date_explode[0] : null;
                $time_end = !empty($date_explode[1]) ? $date_explode[1] : null;
                

                // time_start_special
                if($utilities->isDateTimeClient($time_start)){
                    $time_start_special = !empty($time_start) ? Time::createFromFormat('H:i - d/m/Y', $time_start)->format('Y-m-d H:i:00') : null;
                }

                if($utilities->isDateClient($time_start)){
                    $time_start_special = !empty($time_start) ? Time::createFromFormat('d/m/Y', $time_start)->format('Y-m-d 00:00:00') : null;
                }


                // time_end_special
                if($utilities->isDateTimeClient($time_end)){
                    $time_end_special = !empty($time_end) ? Time::createFromFormat('H:i - d/m/Y', $time_end)->format('Y-m-d H:i:00') : null;
                }

                if($utilities->isDateClient($time_end)){
                    $time_start_special = !empty($time_end) ? Time::createFromFormat('d/m/Y', $time_end)->format('Y-m-d 23:59:59') : null;
                }
            }
            
            $discount_percent = 0;            
            if(!empty($price) && !empty($price_special) && $price > $price_special){
                $discount_percent = round(($price - $price_special) / $price * 100);
            }
       
            if(!empty($item['attribute'])){
                $attribute_item = $check_code = [];            
                foreach($item['attribute'] as $attribute){
                    if(empty($attribute['attribute_code'])) continue;
                    
                    $attribute_id = !empty($attributes_item[$attribute['attribute_code']]) ? intval($attributes_item[$attribute['attribute_code']]['id']) : null;
                    $input_type = !empty($attributes_item[$attribute['attribute_code']]) ? $attributes_item[$attribute['attribute_code']]['input_type'] : null;
                    $input_type = !empty($attributes_item[$attribute['attribute_code']]) ? $attributes_item[$attribute['attribute_code']]['input_type'] : null;

                    if(empty($attribute_id)) continue;

                    $value = !empty($attribute['value']) ? $attribute['value'] : null;
                    
                    switch ($input_type) {
                        case NUMERIC:
                            $value = !empty($value) ? floatval(str_replace(',', '', $value)) : 0;
                            break;

                        case DATE:
                            if(!$utilities->isDateClient($value)){
                                $value = null;
                            }
                            $value = !empty($value) ? $utilities->stringDateClientToInt($value) : null;
                            break;

                        case DATE_TIME:
                            if(!empty($value)){
                                $time = Time::createFromFormat('d/m/Y - H:i', $value, null);
                                $time = $time->format('Y-m-d H:i:s');
                                $value = strtotime($time);
                            }
                            break;

                        case SWITCH_INPUT:
                            $value = !empty($value) ? 1 : 0;
                            break;

                        case TEXT:
                        case RICH_TEXT:
                            $value = !empty($value) ? trim($value) : null;
                            break;

                        case SINGLE_SELECT:
                            $value = !empty($value) ? intval($value) : null;
                            break;

                        case MULTIPLE_SELECT:
                            $value = !empty($value) ? json_encode($value) : null;
                            break;
                        case SPECICAL_SELECT_ITEM:
                            $value = !empty($value) ? intval($value) : null;
                            $check_code[] = $attribute['attribute_code'];
                            $check_code[] = $value;
                        break;
                    }

                    $attribute_item[] = [
                        'product_item_id' => $product_item_id,
                        'attribute_id' => $attribute_id,
                        'value' => $value
                    ];                    
                }

                if(!empty($check_code)){
                    if(in_array(implode('_', $check_code), $attribute_item_code)){
                        $this->responseJson([MESSAGE => __d('admin', 'gia_tri_thuoc_tinh_cua_phien_ban_san_pham_bi_trung_lap_vui_long_chon_lai')]);
                    }else{
                        $attribute_item_code[] = implode('_', $check_code);
                    }
                }

                $products_item_attribute[] = $attribute_item;
            }

            $products_item[] = [
                'id' => $product_item_id,
                'code' => $code,                
                'price' => $utilities->formatToDecimal($price),
                'discount_percent' => $utilities->formatToDecimal($discount_percent),
                'price_special' => $utilities->formatToDecimal($price_special),
                'time_start_special' => !empty($time_start_special) ? strtotime($time_start_special) : null,
                'time_end_special' => !empty($time_end_special) ? strtotime($time_end_special) : null,
                'images' => null,
                'quantity_available' => $quantity_available,
                'position' => $k + 1,
                'status' => $status,
                'images' => !empty($item['image']) ? json_encode($item['image']) : null
            ];
        }

        // format data before save
        $list_keyword = !empty($data['seo_keyword']) ? array_column(json_decode($data['seo_keyword'], true), 'value') : null;
        $seo_keyword = !empty($list_keyword) ? implode(', ', $list_keyword) : null;

        $data_categories = [];
        if(!empty($data['categories'])){
            foreach($data['categories'] as $category_id){
                $data_categories[] = [
                    'product_id' => $id,
                    'category_id' => $category_id
                ];
            }
        }

        $status = isset($product['status']) ? intval($product['status']) : 1;
        $draft = !empty($data['draft']) ? 1 : 0;
        if(!empty($draft)){
            $status = 0;
        }

        // kiểm tra duyệt bài
        $settings = TableRegistry::get('Settings')->getSettingWebsite();
        $approved_product = !empty($settings['approved_product']) ? $settings['approved_product'] : [];
        $approved_role_id = !empty($approved_product['role_id']) ? array_map('intval', explode('|', $approved_product['role_id'])) : [];
        if(empty($draft) && !empty($approved_product['approved']) && !in_array($this->Auth->user('id'), $approved_role_id)){            
            // đổi trạng thái bài viết = -1 (chờ duyệt)
            $status = -1;
        }

        $url_video = !empty($data['url_video']) ? $data['url_video'] : null;
        $type_video = null;
        if(!empty($url_video)){
            $type_video = !empty($data['type_video']) ? $data['type_video'] : null;
        }

        $tags = !empty($data['tags']) ? array_filter(array_column(json_decode($data['tags'], true), 'value')) : null;  

        $data_tags = [];
        if(!empty($tags)){
            foreach ($tags as $tag) {
                $tag_info = $tags_table->find()->where(['Tags.name' => $tag])->select(['Tags.id', 'Tags.name'])->first();
                $tag_id = !empty($tag_info['id']) ? intval($tag_info['id']) : null;
                if(empty($tag_info)){
                    $new_tag = $this->loadComponent('Admin.Tag')->saveTag([
                        'name' => $tag,
                        'link' => $tags_table->getUrlUnique($utilities->formatToUrl($tag)),
                        'seo_title' => $tag,
                        'lang' => $this->lang,
                        'search_unicode' => strtolower($this->Utilities->formatSearchUnicode([$tag]))
                    ]);
                    $tag_id = !empty($new_tag[DATA]['id']) ? intval($new_tag[DATA]['id']) : null;
                }

                if(empty($tag_id)) continue;
                $data_tags[] = [
                    'type' => PRODUCT_DETAIL,
                    'tag_id' => $tag_id
                ];
            }
        }

        $files = [];
        if(!empty($data['files'])){
            foreach (json_decode($data['files'], true) as $key => $file) {
                $files[] = str_replace(CDN_URL , '', $file);
            }
        }

        $data_save = [
            'brand_id' => !empty($data['brand_id']) ? intval($data['brand_id']) : null,
            'url_video' => $url_video,
            'type_video' => $type_video,
            'files' => !empty($files) ? json_encode($files) : null,
            'width' => !empty($data['width']) ? floatval(str_replace(',', '', $data['width'])) : null,
            'length' => !empty($data['length']) ? floatval(str_replace(',', '', $data['length'])) : null,
            'height' => !empty($data['height']) ? floatval(str_replace(',', '', $data['height'])) : null,
            'weight' => !empty($data['weight']) ? floatval(str_replace(',', '', $data['weight'])) : null,

            'width_unit' => !empty($data['width_unit']) ? $data['width_unit'] : null,
            'length_unit' => !empty($data['length_unit']) ? $data['length_unit'] : null,
            'height_unit' => !empty($data['height_unit']) ? $data['height_unit'] : null,
            'weight_unit' => !empty($data['weight_unit']) ? $data['weight_unit'] : null,

            'main_category_id' => !empty($data['main_category_id']) ? intval($data['main_category_id']) : null,

            'featured' => !empty($data['featured']) ? 1 : 0,
            'catalogue' => !empty($data['catalogue']) ? 1 : 0,
            'seo_score' => !empty($data['seo_score']) ? $data['seo_score'] : null,
            'keyword_score' => !empty($data['keyword_score']) ? $data['keyword_score'] : null,
            'position' => !empty($data['position']) ? intval($data['position']) : 0,
            'draft' => $draft,
            'status' => $status,
            'products_item_attribute' => $products_item_attribute
        ];

        if(empty($id)){
            $data_save['created_by'] = $this->Auth->user('id');
        }

        $name = !empty($data['name']) ? trim(strip_tags($data['name'])) : null;
        $seo_title = !empty($data['seo_title']) ? trim(strip_tags($data['seo_title'])) : null;
        $seo_description = !empty($data['seo_description']) ? trim(strip_tags($data['seo_description'])) : null;

        $data_save['ProductsContent'] = [
            'name' => $name,
            'description' => !empty($data['description']) ? trim($data['description']) : null,
            'content' => !empty($data['content']) ? trim($data['content']) : null,
            'seo_title' => $seo_title,
            'seo_description' => $seo_description,
            'seo_keyword' => $seo_keyword,
            'lang' => $this->lang,
            'search_unicode' => strtolower($utilities->formatSearchUnicode([$name]))
        ];

        $data_save['Links'] = [
            'type' => PRODUCT_DETAIL,
            'url' => $link,
            'lang' => $this->lang,
        ];
        
        $data_save['CategoriesProduct'] = $data_categories;
        $data_save['ProductsItem'] = $products_item;
        $data_save['ProductsAttribute'] = $attribute_component->formatDataAttributesBeforeSave($data, $this->lang, PRODUCT, $id);
        $data_save['TagsRelation'] = $data_tags;

        $result = $product_component->saveProduct($data_save, $id, $product);
        if($result[CODE] == SUCCESS){

            $product_id = !empty($result[DATA]->id) ? $result[DATA]->id : null;
            // dich tự động các ngôn ngữ khác khi thêm mới bản ghi
            $auto_translate = TableRegistry::get('Settings')->getSettingAutoTranslate();
            if(empty($id) && $auto_translate){
                $this->translateProduct($product_id, $this->lang);
            }

            $result[MESSAGE] = __d('admin', 'cap_nhat_thong_tin_san_pham_thanh_cong');
            $result[DATA] = [
                'id' => $product_id
            ];
        }

        exit(json_encode($result));
    }

    private function translateProduct($product_id = null, $lang_from = null)
    {
        if(empty($product_id) || empty($lang_from)) return false;

        $languages = TableRegistry::get('Languages')->getList();
        if(empty($languages)) return false;

        $utilities = $this->loadComponent('Utilities');
        $translate_component = $this->loadComponent('Admin.Translate');

        $table = TableRegistry::get('Products');
        $links_table = TableRegistry::get('Links');
        $product_info = $table->getDetailProduct($product_id, $lang_from);
        if(empty($product_info)) return false;

        foreach($languages as $lang => $language){
            if($lang == $lang_from) continue;

            $name = !empty($product_info['ProductsContent']['name']) ? $product_info['ProductsContent']['name'] : null;
            $translates = $translate_component->translate([$name], $lang_from, $lang);
            
            $name_translate = !empty($translates[0]) ? $translates[0] : $name;

            $link = $utilities->formatToUrl($name_translate);
            if(empty($link)) continue;

            $link = $links_table->getUrlUnique($link);
            $data_save = [
                'id' => $product_id,
                'ProductsContent' => [
                    'name' => $name_translate,
                    'seo_title' => $name_translate,
                    'lang' => $lang,
                    'search_unicode' => strtolower($utilities->formatSearchUnicode([$name_translate]))
                ],
                'Links' => [
                    'type' => PRODUCT_DETAIL,
                    'url' => $link,
                    'lang' => $lang,
                ]
            ];

            $entity = $table->newEntity($data_save);
            if($entity->hasErrors()) continue;

            $save = $table->save($entity);
        }

        return true;
    }

    public function quickSave()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();       

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        if (empty($data)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        // validate data
        if(empty($data['name'])){
            $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_ten_san_pham')]);
        }

        $product_component = $this->loadComponent('Admin.Product');
        $utilities = $this->loadComponent('Utilities');
        $system = $this->loadComponent('System');

        $url = $system->getUrlUnique($utilities->formatToUrl(trim($data['name']), 1));

        $data_save = [
            'status' => ENABLE,
            'ProductsContent' => [
                'name' => trim($data['name']),
                'lang' => $this->lang,
                'search_unicode' => strtolower($utilities->formatSearchUnicode([$data['name']]))
            ],
            'Links' => [
                'type' => PRODUCT,
                'url' => $url,
                'lang' => $this->lang
            ],
            'ProductsItem' => [
                [
                    'code' => !empty($data['code']) ? trim($data['code']) : $utilities->generateRandomString(10),
                    'price' => !empty($data['price']) ? str_replace(',', '', $data['price']) : null,
                    'status' => ENABLE
                ]
            ]
        ];

        $result = $product_component->saveProduct($data_save, null);
        if($result[CODE] == SUCCESS){
            $products_item_table = TableRegistry::get('ProductsItem');

            $data_response = !empty($result[DATA]) ? $result[DATA] : [];

            $item_info = !empty($data_response->ProductsItem[0]) ? $data_response->ProductsItem[0] : null;
            $data_result = [];
            if(!empty($item_info)){               
                $data_result = [
                    'id' => !empty($item_info->id) ? intval($item_info->id) : null,
                    'product_id' => !empty($item_info->product_id) ? intval($item_info->product_id) : null,
                    'code' => !empty($item_info->code) ? intval($item_info->code) : null,
                    'price' => !empty(floatval($item_info->price)) ? floatval($item_info->price) : null,
                    'price_special' => 0,
                    'discount_percent' => 0,
                    'time_start_special' => null,
                    'time_end_special' => null,                    
                    'avatar' => null,
                    'images' => null,
                    'status' => !empty($item_info->status) ? 1 : 0,
                    'name' => !empty($data_response->ProductsContent->name) ? $data_response->ProductsContent->name : null,
                    'name_extend' => !empty($data_response->ProductsContent->name) ? $data_response->ProductsContent->name : null
                ];
            }
            $result[DATA] = $data_result;
        }

        exit(json_encode($result));
    }

    public function delete()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        $ids = !empty($data['ids']) ? $data['ids'] : [];

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        if (empty($ids) || !is_array($ids)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }
        
        $product_item_table = TableRegistry::get('ProductsItem');
        $product_table = TableRegistry::get('Products');
        $conn = ConnectionManager::get('default');

        try{
            $conn->begin();

            foreach($ids as $id){
                // delete product
                $products_item = $product_item_table->find()->where(['product_id' => $id])->select(['id', 'deleted'])->toArray();
                if (empty($products_item)) continue;

                $product_info = $product_table->find()->where(['id' => $id])->select(['id', 'deleted'])->first();
                if (empty($product_info)){
                    throw new Exception();
                }
                
                // check product item exist in order
                $exist_in_order = TableRegistry::get('OrdersItem')->find()->where(['product_id' => $id])->select('id')->first();
                if(!empty($exist_in_order)){
                    // delete by flag
                    $entities_data = [];
                    foreach ($products_item as $product_item) {
                        $entities_data[] = [
                            'id' => $product_item['id'],
                            'deleted' => 1
                        ];
                    }

                    $data_product_item = $product_item_table->patchEntities($products_item, $entities_data, ['validate' => false]);
                    $delete_product_item = $product_item_table->saveMany($data_product_item);

                    if (empty($delete_product_item)){
                        throw new Exception();
                    }

                    $entity_data = $product_table->patchEntity($product_info, ['deleted' => 1], ['validate' => false]);               
                    $delete = $product_table->save($entity_data);
                    if (empty($delete)){
                        throw new Exception();
                    }
                    
                    // delete product by flag
                    $delete_link = TableRegistry::get('Links')->updateAll(
                        [  
                            'deleted' => 1
                        ],
                        [
                            'foreign_id' => $id,
                            'type' => PRODUCT_DETAIL
                        ]
                    );

                }else{
                    // delete
                    $product_item_table->deleteAll([
                        'product_id' => $id
                    ]);

                    $product_table->delete($product_info);

                    TableRegistry::get('Links')->deleteAll([
                        'foreign_id' => $id,
                        'type' => PRODUCT_DETAIL
                    ]);

                    TableRegistry::get('ProductsContent')->deleteAll([
                        'product_id' => $id
                    ]);

                    TableRegistry::get('TagsRelation')->deleteAll([
                        'foreign_id' => $id,
                        'type' => PRODUCT_DETAIL
                    ]);

                    TableRegistry::get('ProductsAttribute')->deleteAll([
                        'product_id' => $id
                    ]);

                    TableRegistry::get('ProductsItemAttribute')->deleteAll([
                        'product_id' => $id
                    ]);
                }     
            }           

            $conn->commit();

            $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'xoa_san_pham_thanh_cong')]);
        } catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function changeStatus()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        $ids = !empty($data['ids']) ? $data['ids'] : [];
        $status = !empty($data['status']) ? intval($data['status']) : 0;

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        if (empty($ids) || !is_array($ids)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('Products');

        $products = $table->find()->where([
            'Products.id IN' => $ids,
            'Products.deleted' => 0
        ])->select(['Products.id', 'Products.status'])->toArray();
        
        if(empty($products)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_san_pham')]);
        }

        $patch_data = [];
        foreach ($ids as $k => $product_id) {
            $patch_data[] = [
                'id' => $product_id,
                'status' => $status,
                'draft' => 0
            ];
        }
        
        $data_entities = $table->patchEntities($products, $patch_data);
        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $change_status = $table->saveMany($data_entities);            
            if (empty($change_status)){
                throw new Exception();
            }

            $conn->commit();
            $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'cap_nhat_trang_thai_san_pham_thanh_cong')]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function quickChange()
    {
        $this->layout = false;
        $this->autoRender = false;

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $data = $this->getRequest()->getData();

        $field = !empty($data['name']) ? $data['name'] : '';
        if(empty($field) || !in_array($field, ['price', 'price_special', 'quantity_available', 'position'])){
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        switch ($field) {
            case 'price':
            case 'price_special':
            case 'quantity_available':
                $result = $this->quickChangeProductItem($data);
            break;

            case 'position':
                $result = $this->quickChangeProduct($data);
            break;
        }

        $this->responseJson($result);       
    }

    private function quickChangeProductItem($data = [])
    {
        $id = !empty($data['id']) ? $data['id'] : null;
        $value = !empty($data['value']) ? $data['value'] : 0;
        $field = !empty($data['name']) ? $data['name'] : '';

        $system = $this->loadComponent('System');

        // validate data
        if (empty($id) || empty($field)) {
            return $system->getResponse([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('ProductsItem');

        $product_item = $table->find()->where(['id' => $id])->first();

        $origin_price = !empty(floatval($product_item['price'])) ? abs(floatval($product_item['price'])) : null;
        $origin_price_special = !empty(floatval($product_item['price_special'])) ? abs(floatval($product_item['price_special'])) : null;

        $discount_percent = 0;        
        $data_save = [
            'id' => $id
        ];

        switch ($field) {
            case 'price':
                $data_save['price'] = abs(floatval(str_replace(',', '', $value)));
                if($origin_price_special > $data_save['price']){
                    return $system->getResponse([MESSAGE => __d('admin', 'gia_san_pham_khong_duoc_nho_hon_gia_dac_biet')]);
                }
                if(!empty($data_save['price']) && !empty($origin_price_special) && $data_save['price'] > $origin_price_special){
                    $discount_percent = round((($data_save['price'] - $origin_price_special) / $data_save['price'] * 100), 2);
                }

                $data_save['discount_percent'] = $discount_percent;
                break;

            case 'price_special':
                $data_save['price_special'] = abs(floatval(str_replace(',', '', $value)));

                if(empty($origin_price)){
                    return $system->getResponse([MESSAGE => __d('admin', 'gia_san_pham_hien_dang_de_trong')]);
                }

                if($data_save['price_special'] > $origin_price){
                    return $system->getResponse([MESSAGE => __d('admin', 'gia_san_pham_khong_duoc_nho_hon_gia_dac_biet')]);
                }
                if(!empty($origin_price) && !empty($data_save['price_special']) && $origin_price > $data_save['price_special']){
                    $discount_percent = round((($origin_price - $data_save['price_special']) / $origin_price * 100), 2);
                }
                $data_save['discount_percent'] = $discount_percent;
                break;

            case 'quantity_available':
                $data_save['quantity_available'] = abs(floatval(str_replace(',', '', $value)));
                break; 
        }

        $product_item = $table->patchEntity($product_item, $data_save);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            // save data
            $save = $table->save($product_item);
            if (empty($save->id)){
                throw new Exception();
            }

            $conn->commit();

            return $system->getResponse([CODE => SUCCESS, DATA => ['id' => $save->id]]);

        }catch (Exception $e) {
            $conn->rollback();
            return $system->getResponse([MESSAGE => $e->getMessage()]);
        }
    }

    private function quickChangeProduct($data = [])
    {
        $id = !empty($data['id']) ? $data['id'] : null;
        $value = !empty($data['value']) ? $data['value'] : 0;
        $field = !empty($data['name']) ? $data['name'] : '';

        $system = $this->loadComponent('System');

        // validate data
        if (empty($id) || empty($field)) {
            return $system->getResponse([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('Products');

        $product = $table->find()->where(['id' => $id])->first();

        $data_save = [
            'id' => $id,
            'position' => !empty($value) ? abs(intval(str_replace(',', '', $value))) : null
        ];

        $entity = $table->patchEntity($product, $data_save);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            // save data
            $save = $table->save($entity);
            if (empty($save->id)){
                throw new Exception();
            }

            $conn->commit();

            return $system->getResponse([CODE => SUCCESS, DATA => ['id' => $save->id]]);

        }catch (Exception $e) {
            $conn->rollback();
            return $system->getResponse([MESSAGE => $e->getMessage()]);
        }
    }


    public function autoSuggest()
    {
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $table = TableRegistry::get('ProductsItem');
        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        $filter = !empty($data[FILTER]) ? $data[FILTER] : [];
        $filter[STATUS] = 1;
        $filter[LANG] = $this->lang;

        $products_item = $table->queryListProductsItem([
            FILTER => $filter,
            FIELD => FULL_INFO
        ])->limit(20)->toArray();

        $result = [];
        if(!empty($products_item)){
            foreach($products_item as $product){
                $item = $table->formatProductItemDetail($product, $this->lang);

                $name_extend = !empty($item['name_extend']) ? $item['name_extend'] : null;
                $code = !empty($item['code']) ? $item['code'] : null;
                $price = !empty($item['price']) ? $item['price'] : null;

                $item['name_code'] = $item['name_price'] = $name_extend;
                if(!empty($code)) {
                    $item['name_code'] = $name_extend . ' - ' . $code;
                }

                if(!empty($price)) {
                    $item['name_price'] = $name_extend . ' - ' . number_format($price);
                }

                $result[] = $item;
            }
        }

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $result, 
        ]);
    }

    public function autoSuggestNormalProduct()
    {
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $table = TableRegistry::get('Products');
        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        $filter = !empty($data[FILTER]) ? $data[FILTER] : [];
        $filter[STATUS] = 1;
        $filter[LANG] = $this->lang;
        $products = $table->queryListProducts([
            FILTER => $filter,
            FIELD => LIST_INFO
        ])->limit(10)->toArray();

        $result = [];
        if(!empty($products)){
            foreach($products as $product){
                $item = [];
                $item['id'] = !empty($product['id']) ? intval($product['id']) : null;
                $item['name'] = !empty($product['ProductsContent']['name']) ? $product['ProductsContent']['name'] : null;
                $result[] = $item;
            }
        }
  
        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $result, 
        ]);
    }

    public function viewListItems($id = null)
    {
        $this->viewBuilder()->enableAutoLayout(false);

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $table = TableRegistry::get('Products');
        $product_detail = $table->getDetailProduct($id, $this->lang, [
            'get_item_attributes' => true
        ]);
        $product = $table->formatDataProductDetail($product_detail, $this->lang);
        $items = !empty($product['items']) ? $product['items'] : [];

        if(!empty($items)){
            $all_options = TableRegistry::get('AttributesOptions')->getAll($this->lang);
            $all_attributes = Hash::combine(TableRegistry::get('Attributes')->getAll($this->lang), '{n}.id', '{n}', '{n}.attribute_type');
            $attributes_item = !empty($all_attributes[PRODUCT_ITEM]) ? $all_attributes[PRODUCT_ITEM] : [];

            foreach ($items as $k => $item) {
                if(empty($item['attributes'])) continue;
                $name_extend = [];
                foreach ($item['attributes'] as $k_attribute => $attribute_item) {
                    $attribute_info = !empty($attributes_item[$attribute_item['attribute_id']]) ? $attributes_item[$attribute_item['attribute_id']] : [];
                    $input_type = !empty($attribute_info['input_type']) ? $attribute_info['input_type'] : null;

                    if($input_type == SPECICAL_SELECT_ITEM && !empty($attribute_item['value'])){
                        $option_name = !empty($all_options[$attribute_item['value']]['name']) ? $all_options[$attribute_item['value']]['name'] : null;
                        if(!empty($option_name)){
                            $name_extend[] = $option_name;
                        }
                    }
                }

                $items[$k]['name'] = !empty($name_extend) ? implode(' - ', $name_extend) : null;
            }
        }

        $this->set('items', $items);
    }

    public function quickUpload()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();

        $id = !empty($data['id']) ? $data['id'] : null;
        $images = !empty($data['images']) ? $data['images'] : [];

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        // validate data
        if (empty($id)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('ProductsItem');

        $product_item = $table->find()->where([
            'id' => $id,            
            'deleted' => 0,
        ])->select(['id', 'images'])->first();

        if (empty($product_item)) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
        }
    

        $product_item = $table->patchEntity($product_item, [
            'images' => $images
        ]);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            // save data
            $save = $table->save($product_item);
            if (empty($save->id)){
                throw new Exception();
            } 

            $conn->commit();

            $this->responseJson([CODE => SUCCESS, DATA => $product_item]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);
        }
    }

    public function uploadModal($id = null)
    {
        $this->viewBuilder()->enableAutoLayout(false);

        if(empty($id)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $products_item_table = TableRegistry::get('ProductsItem');   
        $product_item = $products_item_table->find()->where([
            'id' => $id,
            'deleted' => 0,
        ])->first();

        $product_item['images'] = !empty($product_item['images']) ? json_decode($product_item['images'], true) : [];


        if(empty($product_item)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
        }

        $this->set('product', $product_item);
    }

    public function downloadFileImportProduct()
    {
        // khởi tạo dữ liệu mẫu
        $data = [
            0 => [
                'name' => 'Sản phẩm inport',
                'lang' => 'vi',
                'items' => [
                    0 => [
                        'code' => 'NHIMPORT',
                        'price' => 5000000,
                        'discount_percent' => '20.00',
                        'time_start_special' => time(),
                        'time_end_special' => time(),
                        'price_special' => 4000000,
                        'quantity_available' => 10,
                        'position' => 1,
                        'status' => 1
                    ]
                ]
            ]
        ];

        $spreadsheet = $this->initializationExcel($data);

        $writer = new Xlsx($spreadsheet);

        ob_start();
        $writer->save('php://output');
        $xlsData = ob_get_contents();
        ob_end_clean();
        
        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData),
            META => [
                'name' => __d('admin', 'thong_tin_san_pham') . time()
            ]
        ]);
    }

    public function importDataByExcel()
    {
        $this->layout = false;
        $this->autoRender = false;

        $excel_file = !empty($_FILES['excel_file']) ? $_FILES['excel_file'] : [];

        if (!$this->getRequest()->is('post') || empty($excel_file)) {
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }
        $upload = $this->loadComponent('Upload');
        $files = !empty($excel_file['tmp_name']) ? $excel_file['tmp_name'] : null;

        /**  Identify the type of $inputFileName  **/
        $file_type = \PhpOffice\PhpSpreadsheet\IOFactory::identify($files);
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($file_type);
        $spreadsheet = $reader->load($files);

        $data_excel = $spreadsheet->getActiveSheet()->toArray();

        if (empty($data_excel) || count($data_excel) < 3) {
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        // xóa dữ liệu tiêu đề và header
        unset($data_excel[0]);
        unset($data_excel[1]);

        $all_attributes = Hash::combine(TableRegistry::get('Attributes')->getAll($this->lang), '{n}.id', '{n}', '{n}.attribute_type');

        // lấy thông tin thuộc tính sản phẩm
        $attributes_product = !empty($all_attributes[PRODUCT]) ? Hash::combine($all_attributes[PRODUCT], '{n}.code', '{n}') : [];

        // lấy thông tin thuộc tính phiên bản sản phẩm
        $attributes_item = [];
        if(!empty($all_attributes[PRODUCT_ITEM])){
            $attributes_item = Hash::combine(Collection($all_attributes[PRODUCT_ITEM])->filter(function ($item, $key, $iterator) {
                return $item['input_type'];
            })->toArray(), '{n}.code', '{n}');
        }

        $attribute_component = $this->loadComponent('Admin.Attribute');
        $utilities = $this->loadComponent('Utilities');
        $product_component = $this->loadComponent('Admin.Product');
        $products_table = TableRegistry::get('Products');

        $languages = TableRegistry::get('Languages')->getList();
        $brands = TableRegistry::get('Brands')->getListBrands($this->lang);
        $arr_yes_no = [
            0 => __d('admin', 'khong'),
            1 => __d('admin', 'co')
        ];
        $arr_status = [
            0 => __d('admin', 'ngung_hoat_dong'),
            1 => __d('admin', 'hoat_dong')
        ];
        $categories_product = TableRegistry::get('Categories')->queryListCategories([
            FILTER => [
                TYPE => PRODUCT,
                LANG => $this->lang,
                STATUS => 1
            ]
        ])->all()->nest('id', 'parent_id')->toArray();

        $categories = [];
        if(!empty($categories_product)){
            $categories = Hash::combine(TableRegistry::get('Categories')->parseDataCategoriesExcel($categories_product), '{n}.id', '{n}.CategoriesContent.name');
            ;
        }

        $arr_header = [
            'id',
            'name',
            'lang',
            'category',
            'brand',
            'featured',
            'catalogue',
            'position',
            'status',
            'description'
        ];

        if (!empty($attributes_product)) {
            foreach ($attributes_product as $key => $attribute) {
                $attribute_code = !empty($attribute['code']) ? $attribute['code'] : null;
                $attribute_name = !empty($attribute['name']) ? $attribute['name'] : null;
                $input_type = !empty($attribute['input_type']) ? $attribute['input_type'] : null;

                if (!empty($input_type) && ($input_type == ARTICLE_SELECT || $input_type == PRODUCT_SELECT || $input_type == CITY_DISTRICT || $input_type == RICH_TEXT || $input_type == ALBUM_IMAGE || $input_type == ALBUM_VIDEO || $input_type == VIDEO)) continue;

                if (!empty($attribute_code) && !empty($attribute_name)) {
                    array_push($arr_header, 'attribute_'.$attribute_code);
                }
            }
        }

        array_push($arr_header, 'items_code', 'items_images', 'items_price', 'items_price_special', 'items_time_start_special', 'items_time_end_special', 'items_quantity_available', 'items_status_item');

        if (!empty($attributes_item)) {
            foreach ($attributes_item as $key => $attribute_item) {
                $attribute_item_code = !empty($attribute_item['code']) ? $attribute_item['code'] : null;
                $attribute_item_input_type = !empty($attribute_item['input_type']) ? $attribute_item['input_type'] : null;

                if (!empty($attribute_item_input_type) && $attribute_item_input_type == SPECICAL_SELECT_ITEM) continue;

                if (!empty($attribute_item_code)) {
                    array_push($arr_header, 'item_attribute_'.$attribute_item_code);
                }
            }
        }

        // loại bọ các trường thông tin null trong data_excel
        $data_excel = array_filter(array_map('array_filter', $data_excel));

        $data_products = [];
        if (!empty($data_excel)) {
            foreach ($data_excel as $key => $val) { 

                $data_products[$key] = [
                    'products_item_attribute' => [],
                    'ProductsContent' => [],
                    'Links' => [],
                    'CategoriesProduct' => [],
                    'ProductsItem' => [],
                    'ProductsAttribute' => []
                ];
                $items_product = $attribute_items = [];
                // đọc dữ liệu từ excel
                foreach ($arr_header as $k => $code) {

                    switch ($code) {
                        case 'id':
                            $product_id = !empty($val[$k]) ? intval($val[$k]) : null;
                            $data_products[$key]['id'] = $product_id;

                            break;
                        case 'name':
                            $name = !empty($val[$k]) ? trim($val[$k]) : null;
                            $search_unicode = !empty($name) ? strtolower($utilities->formatSearchUnicode([$name])) : null;

                            $exit_name = TableRegistry::get('Products')->checkNameExist($name, $product_id);

                            if ($exit_name && !empty($name)) {
                                $this->responseJson([MESSAGE => __d('admin', 'san_pham_{0}_da_ton_tai_tren_he_thong_vui_long_nhap_dung_id_san_pham_de_cap_nhat_san_pham_nay', [$name])]);
                            }

                            $data_products[$key]['ProductsContent']['name'] = !empty($name) ? $name : null;
                            $data_products[$key]['ProductsContent']['seo_title'] = !empty($name) ? $name : null;
                            $data_products[$key]['ProductsContent']['search_unicode'] = !empty($search_unicode) ? $search_unicode : null;

                            break;
                        case 'lang':
                            $lang = !empty($val[$k]) ? array_search($val[$k], $languages) : null;

                            if (empty($lang) && !empty($name)) {
                                $this->responseJson([MESSAGE => __d('admin', 'san_pham_{0}_khong_lay_duoc_thong_tin_ngon_ngu', [$name])]);
                            }

                            $data_products[$key]['ProductsContent']['lang'] = $lang;
                            break;
                        case 'category':
                            $categories_item = !empty($val[$k]) ? explode('||', $val[$k]) : [];

                            foreach ($categories_item as $k_cate => $category_name) {
                                $category_id = !empty($category_name) ? array_search($category_name, $categories) : null;
                                if (empty($category_id)) continue;

                                $data_products[$key]['CategoriesProduct'][$k_cate] = [
                                    'product_id' => !empty($product_id) ? $product_id : null,
                                    'category_id' => $category_id
                                ];
                            }

                            break;
                        case 'brand':
                            $data_products[$key]['brand_id'] = !empty($val[$k]) ? intval(array_search($val[$k], $brands)) : null;

                            break;
                        case 'featured':
                            $data_products[$key]['featured'] = !empty($val[$k]) ? intval(array_search($val[$k], $arr_yes_no)) : 0;

                            break;
                        case 'catalogue':
                            $data_products[$key]['catalogue'] = !empty($val[$k]) ? intval(array_search($val[$k], $arr_yes_no)) : 0;

                            break;
                        case 'position':
                            $data_products[$key]['position'] = !empty($val[$k]) ? intval($val[$k]) : null;

                            break;
                        case 'status':
                            $data_products[$key]['status'] = !empty($val[$k]) ? intval(array_search($val[$k], $arr_status)) : 0;

                            break;
                        case 'description':
                            $description = !empty($val[$k]) ? trim($val[$k]) : null;

                            $data_products[$key]['ProductsContent']['description'] = $description;

                            break;
                        case stristr($code, 'attribute_'):
                            $attribute_code = !empty($code) ? str_replace('attribute_', '', $code) : null;
                            $input_type = !empty($attributes_product[$attribute_code]['input_type']) ? $attributes_product[$attribute_code]['input_type'] : null;
                            $attribute_id = !empty($attributes_product[$attribute_code]['id']) ? intval($attributes_product[$attribute_code]['id']) : null;
                            $options = $attribute_component->getListOptionsByAttributeId($attribute_id);

                            $attribute_value = isset($val[$k]) ? $val[$k] : null;
                            if (empty($attribute_value)) break;

                            switch ($input_type) {
                                case SWITCH_INPUT:
                                    $attribute_value = intval(array_search(trim($attribute_value), $arr_yes_no));

                                    $attribute = [
                                        'attribute_id' => $attribute_id,
                                        'value' => $attribute_value
                                    ];
                                    array_push($data_products[$key]['ProductsAttribute'], $attribute);
                                    
                                    break;

                                case SINGLE_SELECT:
                                    $attribute_value = array_search(trim($attribute_value), $options);

                                    $attribute = [
                                        'attribute_id' => $attribute_id,
                                        'value' => $attribute_value
                                    ];
                                    array_push($data_products[$key]['ProductsAttribute'], $attribute);

                                    break;

                                case MULTIPLE_SELECT:
                                    $attribute_value = explode('||', $attribute_value);
                                    if (empty($attribute_value)) break;

                                    foreach ($attribute_value as $k_attr => $val_atr) {
                                        $attribute_value[$k_attr] = array_search(trim($val_atr), $options);
                                    }

                                    $attribute = [
                                        'attribute_id' => $attribute_id,
                                        'value' => json_encode($attribute_value)
                                    ];
                                    array_push($data_products[$key]['ProductsAttribute'], $attribute);

                                    break;

                                case IMAGE:
                                    $attribute_value = str_replace(CDN_URL, '', trim($attribute_value));

                                    $attribute = [
                                        'attribute_id' => $attribute_id,
                                        'value' => $attribute_value
                                    ];
                                    array_push($data_products[$key]['ProductsAttribute'], $attribute);

                                    break;

                                case IMAGES:
                                case FILES:
                                    $attribute_value = explode('||', $attribute_value);
                                    if (empty($attribute_value)) break;

                                    foreach ($attribute_value as $k_attr => $val_atr) {
                                        $attribute_value[$k_attr] = str_replace(CDN_URL, '', trim($val_atr));
                                    }

                                    $attribute = [
                                        'attribute_id' => $attribute_id,
                                        'value' => json_encode($attribute_value)
                                    ];
                                    array_push($data_products[$key]['ProductsAttribute'], $attribute);

                                    break;

                                case DATE:
                                    if (!$utilities->isDateClient($attribute_value)) break;
                                    $attribute_value = $utilities->stringDateClientToInt($attribute_value);

                                    $attribute = [
                                        'attribute_id' => $attribute_id,
                                        'value' => $attribute_value
                                    ];
                                    array_push($data_products[$key]['ProductsAttribute'], $attribute);

                                    break;

                                case DATE_TIME:
                                    if (!$utilities->isDateTimeClient($attribute_value)) break;
                                    $attribute_value = $utilities->stringDateTimeClientToInt($attribute_value);

                                    $attribute = [
                                        'attribute_id' => $attribute_id,
                                        'value' => $attribute_value
                                    ];
                                    array_push($data_products[$key]['ProductsAttribute'], $attribute);

                                    break;

                                default:
                                    $attribute_value = !empty($val[$k]) ? $val[$k] : null;
                                    $attribute_value = [
                                        $lang => $attribute_value
                                    ];

                                    $attribute = [
                                        'attribute_id' => $attribute_id,
                                        'value' => json_encode($attribute_value)
                                    ];
                                    array_push($data_products[$key]['ProductsAttribute'], $attribute);

                                    break;
                            }

                            break;
                        case stristr($code, 'items_'):
                            $code = !empty($code) ? str_replace('items_', '', $code) : null;
                            switch ($code) {
                                case 'code':
                                    $item_code = !empty($val[$k]) ? trim($val[$k]) : null;
                                    if(empty($item_code) && !empty($name)){
                                        $item_code = $utilities->generateRandomString(10);
                                    }
                                    $items_product[$code] = $item_code;

                                    break;
                                case 'images':
                                    $images = !empty($val[$k]) ? explode('||', trim($val[$k])) : [];
                                    if (empty($images)) break;

                                    foreach ($images as $k_image => $val_image) {
                                        $images[$k_image] = str_replace(CDN_URL, '', trim($val_image));
                                    }

                                    $items_product[$code] = json_encode($images);

                                    break;
                                case 'price':
                                    $price = !empty($val[$k]) ? floatval(str_replace(',', '', $val[$k])) : 0;
                                    $items_product[$code] = $utilities->formatToDecimal($price);

                                    break;
                                case 'price_special':
                                    $price_special = !empty($val[$k]) ? floatval(str_replace(',', '', $val[$k])) : 0;
                                    $items_product[$code] = $utilities->formatToDecimal($price_special);

                                    break;
                                case 'time_start_special':
                                case 'time_end_special':
                                    $time_special = !empty($first_item[$code]) ? $first_item[$code] : null;
                                    if ($utilities->isDateClient($time_special)) {
                                        $time_special = $utilities->stringDateClientToInt($time_special);
                                    };

                                    if ($utilities->isDateTimeClient($time_special)) {
                                        $time_special = $utilities->stringDateTimeClientToInt($time_special);
                                    };

                                    $items_product[$code] = $time_special;

                                    break;
                                case 'quantity_available':
                                    $quantity_available = !empty($val[$k]) ? intval($val[$k]) : null;
                                    $items_product[$code] = $quantity_available;

                                    break;
                                case 'status_item':
                                    $status = !empty($val[$k]) ? 1 : 0;
                                    $items_product['status'] = $status;

                                    break;
                            }

                            break;
                        case stristr($code, 'item_attribute_'):
                            $attribute_code = !empty($code) ? str_replace('item_attribute_', '', $code) : null;
                            $attribute_id = !empty($attributes_item[$attribute_code]['id']) ? intval($attributes_item[$attribute_code]['id']) : null;
                            $input_type = !empty($attributes_item[$attribute_code]['input_type']) ? $attributes_item[$attribute_code]['input_type'] : null;
                            $options = $attribute_component->getListOptionsByAttributeId($attribute_id);

                            $attribute_value = isset($val[$k]) ? $val[$k] : null;
                            if (empty($attribute_value)) break;

                            switch ($input_type) {
                                case SWITCH_INPUT:
                                    $attribute_value = intval(array_search(trim($attribute_value), $arr_yes_no));

                                    $attribute_item = [
                                        'attribute_id' => $attribute_id,
                                        'value' => $attribute_value
                                    ];
                                    array_push($attribute_items, $attribute_item);

                                    break;

                                case SINGLE_SELECT:
                                    $attribute_value = array_search($attribute_value, $options);

                                    $attribute_item = [
                                        'attribute_id' => $attribute_id,
                                        'value' => $attribute_value
                                    ];
                                    array_push($attribute_items, $attribute_item);

                                    break;

                                case MULTIPLE_SELECT:
                                    $attribute_value = explode('||', $attribute_value);
                                    if (empty($attribute_value)) break;

                                    foreach ($attribute_value as $k_attr => $val_atr) {
                                        $attribute_value[$k_attr] = array_search(trim($val_atr), $options);
                                    }

                                    $attribute_item = [
                                        'attribute_id' => $attribute_id,
                                        'value' => json_encode($attribute_value)
                                    ];
                                    array_push($attribute_items, $attribute_item);

                                    break;

                                case DATE:
                                    if (!$utilities->isDateClient($attribute_value)) break;
                                    $attribute_value = $utilities->stringDateClientToInt($attribute_value);

                                    $attribute_item = [
                                        'attribute_id' => $attribute_id,
                                        'value' => $attribute_value
                                    ];
                                    array_push($attribute_items, $attribute_item);

                                    break;

                                case DATE_TIME:
                                    if (!$utilities->isDateTimeClient($attribute_value)) break;
                                    $attribute_value = $utilities->stringDateTimeClientToInt($attribute_value);

                                    $attribute_item = [
                                        'attribute_id' => $attribute_id,
                                        'value' => $attribute_value
                                    ];
                                    array_push($attribute_items, $attribute_item);

                                    break;

                                default:
                                    array_push($attribute_items, [
                                        'attribute_id' => $attribute_id,
                                        'value' => $attribute_value
                                    ]);

                                break;
                            }
                    }
                    
                    $discount_percent = 0;            
                    if(!empty($price) && !empty($price_special) && $price > $price_special){
                        $discount_percent = round(($price - $price_special) / $price * 100);
                    }

                    $items_product['discount_percent'] = $utilities->formatToDecimal($discount_percent);
                }

                if (!empty($items_product)) {
                    array_push($data_products[$key]['ProductsItem'], $items_product);
                }

                if (!empty($attribute_items)) {
                    array_push($data_products[$key]['products_item_attribute'], $attribute_items);
                }

                if (!empty($name)) {
                    $url = $utilities->formatToUrl($name);                    
                    $check_url_exist = TableRegistry::get('Links')->checkExistUrl($url, $product_id, PRODUCT_DETAIL);

                    if ($check_url_exist) {
                        $url = $url . $product_id;
                    }

                    $data_products[$key]['Links'] = [
                        'type' => PRODUCT_DETAIL,
                        'url' => $url,
                        'lang' => $lang
                    ];

                    $key_last = $key;

                }

                // check nếu row excel không có tên sản phẩm thì mặc định quy vào là phiên bản của sản phẩm trước
                // nếu row tiếp theo là sản phẩm mới thi key_last sẽ là key hiện tại và là 1 sản phẩm
                if (empty($name) && !empty($item_code) && empty($lang)) {
                    if (empty($key_last) || $key_last == $key) {
                        $key_last = intval($key) - 1;
                    }   

                    $items_product_now = !empty($data_products[$key]['ProductsItem'][0]) ? $data_products[$key]['ProductsItem'][0] : [];

                    if (!empty($items_product_now)) {
                        array_push($data_products[$key_last]['ProductsItem'], $items_product_now);
                    }

                    $attribute_items_now = !empty($data_products[$key]['products_item_attribute'][0]) ? $data_products[$key]['products_item_attribute'][0] : [];

                    if (!empty($attribute_items_now)) {
                        array_push($data_products[$key_last]['products_item_attribute'], $attribute_items_now);
                    }

                    unset($data_products[$key]);
                }
            }            
        }

        if (empty($data_products)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $result = [
            CODE => ERROR,
            MESSAGE => __d('admin', 'cap_nhat_thong_tin_san_pham_khong_thanh_cong'),
            DATA => [],
        ];


        $dataExcel = $upload->_createTmpDataJson('excel', $data_products, $this->limit_import);

        if(empty($dataExcel)) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_tao_duoc_du_lieu')]);
        }

        exit(json_encode([
            CODE => SUCCESS,
            DATA => [
                'folder' => $dataExcel
            ]
        ]));
    }

    private function _createTmpDataJson($folder_name = 'example', $data = null, $limit = 10)
    {
        if(empty($data)) return null;

        $dir_folder = TMP . $folder_name . DS;
        $create_dir_folder = new Folder($dir_folder, true, 0755);

        $folder_child_name = Text::uuid();
        $dir_folder_child = $dir_folder . $folder_child_name . DS;


        $create_folder_child = new Folder($dir_folder_child, true, 0755);
        $files = $create_folder_child->find('.*\.json', true);

        $data = array_chunk($data, $limit);
        foreach ($data as $k_data => $v_data) {
            $file = new File($dir_folder_child . str_pad($k_data, 5, '0', STR_PAD_LEFT). '.json', true, 0755);
            $file->write(json_encode($v_data), 'w');
            $file->close();
        }

        return $folder_child_name;
    }

    public function processImportExcel()
    {   
        $this->viewBuilder()->enableAutoLayout(false);

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $data = $this->getRequest()->getData();

        $page = !empty($data['page']) ? $data['page'] : 0;
        $folder = !empty($data['folder']) ? $data['folder'] : null;

        if(empty($folder)) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_ten_thu_muc_excel')]);
        }

        $utilities = $this->loadComponent('Utilities');
        $dir_folder = TMP . 'excel' . DS . $folder . DS;
        $create_dir_folder = new Folder($dir_folder);
        $files = $create_dir_folder->find('.*\.json', true);
        $file = new File($dir_folder . $files[$page]);

        $data = [];
        if($file->exists()){                        
            $content = $file->read();
            $file->close();
            $data = $utilities->isJson($content) ? json_decode($content, true) : [];
        }
        
        if(empty($data)) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_co_du_lieu_excel')]);
        }

        $new_entities = [];
        $patch_entities = [];
        $product_info = [];
        $products_deleted = [];
        $product_component = $this->loadComponent('Admin.Product');

        foreach ($data as $key => $item_save) {
            $id = !empty($item_save['id']) ? intval($item_save['id']) : null;

            if(!empty($id)) {
                $patch_entities[] = $item_save;

                $product_item = TableRegistry::get('Products')->getDetailProduct($id, $this->lang, [
                    'get_user' => false, 
                    'get_categories' => true,
                    'get_attributes' => true
                ]);

                if(empty($product_item)){
                    $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_san_pham_co_id_la_{0}', [$id])]);
                }

                $product_info[] = $product_item;

                array_push($products_deleted, $id);
                
            } else {
                unset($item_save['id']);
                $new_entities[] = $item_save;
            }
        }

        if(!empty($products_deleted)) {
            TableRegistry::get('CategoriesProduct')->deleteAll(['product_id IN' => $products_deleted]);
            TableRegistry::get('ProductsItemAttribute')->deleteAll(['product_id IN' => $products_deleted]);
            TableRegistry::get('ProductsItem')->deleteAll(['product_id IN' => $products_deleted]);
            TableRegistry::get('ProductsAttribute')->deleteAll(['product_id IN' => $products_deleted]);
        }

        if(!empty($new_entities)) {
            $result = $product_component->saveManyProduct($new_entities);
        }

        if(!empty($patch_entities) && !empty($product_info)) {
            $result = $product_component->saveManyProduct($patch_entities, $product_info);
        }

        if(!empty($result[CODE]) && $result[CODE] == ERROR)
        {
            $this->responseJson($result);
        }

        $percent = $total_product = 0;
        $perpage = $this->limit_import;
        $total_page = count($files);

        if($total_page > ($page + 1) )
        {
            $percent = (($page + 1) * $perpage) / ((($total_page - 1) * $perpage) + count($data)) * 100;
            $this->responseJson([
                CODE => SUCCESS,
                DATA => [
                    'continue' => true,
                    'page' => $page + 1,
                    'product' => (($page + 1) * $perpage),
                    'folder' => $folder,
                    'percent' => $percent
                ]
            ]);
        }

        $total_product = count($data);
        if($total_page > 1)
        {
            $total_product = $page * $perpage + count($data);
        }

        $delete = @$create_dir_folder->delete();
        $percent = 100;
        $this->responseJson([
            CODE => SUCCESS,
            DATA => [
                'continue' => false,
                'product' => $total_product,
                'percent' => $percent
            ]
        ]);
    }

    public function duplicate()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        $ids = !empty($data['ids']) ? $data['ids'] : [];
        $id = !empty($ids[0]) ? intval($ids[0]) : null;
        if (!$this->getRequest()->is('post') || empty($id)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('Products');
        $system = $this->loadComponent('System');
        $utilities = $this->loadComponent('Utilities');

        $product = $table->find()->contain([
            'ContentMutiple',
            'CategoriesProduct',
            'LinksMutiple',
            'ProductsItem',
            'TagsRelation',
            'ProductsAttribute',
            'ProductsItemAttribute'
        ])->where([
            'Products.id' => $id,
            'Products.deleted' => 0,
        ])->first()->toArray();        
        if(empty($product['ProductsItem']) || empty($product['ContentMutiple']) || empty($product['LinksMutiple'])) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
        }

        // format data before mere entity
        unset($product['id']);
        unset($product['created']);
        unset($product['updated']);

        unset($product['view']);
        unset($product['like']);
        unset($product['comment']);

        $product['created_by'] = $this->Auth->user('id');
        foreach($product['ProductsItem'] as $k_item => $item){
            unset($product['ProductsItem'][$k_item]['id']);
            unset($product['ProductsItem'][$k_item]['product_id']);
            $product['ProductsItem'][$k_item]['code'] = $utilities->generateRandomString(10);
        }

        foreach($product['ContentMutiple'] as $k_content => $content){
            $name = $system->getNameUnique('Products', $content['name'], 1);
            $product['ContentMutiple'][$k_content]['name'] = $name;

            unset($product['ContentMutiple'][$k_content]['id']);
            unset($product['ContentMutiple'][$k_content]['category_id']);
        }

        foreach($product['LinksMutiple'] as $k_link => $link){
            $product['LinksMutiple'][$k_link]['url'] = $system->getUrlUnique($link['url'], 1);
            unset($product['LinksMutiple'][$k_link]['id']);
            unset($product['LinksMutiple'][$k_link]['foreign_id']);
        }

        if(!empty($product['CategoriesProduct'])){
            foreach($product['CategoriesProduct'] as $k_category => $category_product){
                unset($product['CategoriesProduct'][$k_category]['id']);
                $product['CategoriesProduct'][$k_category]['product_id'] = null;
            }
        }

        if(!empty($product['TagsRelation'])){
            foreach($product['TagsRelation'] as $k_tag => $tag){
                unset($product['TagsRelation'][$k_tag]['id']);
                unset($product['TagsRelation'][$k_tag]['foreign_id']);
            }
        }

        if(!empty($product['ProductsAttribute'])){
            foreach($product['ProductsAttribute'] as $k_attribute => $attribute){
                unset($product['ProductsAttribute'][$k_attribute]['id']);
                $product['ProductsAttribute'][$k_attribute]['product_id'] = null;
            }
        }
        
        $data_item_attribute = $old_item_ids = [];
        if(!empty($product['ProductsItemAttribute'])){

            $data_item_attribute = $product['ProductsItemAttribute'];
            foreach($data_item_attribute as $k_attribute_item => $attribute){
                $product_item_id = !empty($attribute['product_item_id']) ? intval($attribute['product_item_id']) : null;
                if(empty($product_item_id)) continue;
                if(in_array($product_item_id, $old_item_ids)) continue;
                $old_item_ids[] = $product_item_id;
            }
            unset($product['ProductsItemAttribute']);
        }        

        $entity = $table->newEntity($product, [
            'associated' => ['ProductsItem', 'ContentMutiple', 'LinksMutiple', 'CategoriesProduct', 'ProductsAttribute', 'TagsRelation']
        ]);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $dulicate = $table->save($entity);
            $product_id = !empty($dulicate['id']) ? intval($dulicate['id']) : null;
            if (empty($dulicate->id)){
                throw new Exception();
            }

            if(!empty($data_item_attribute)){
                $data_item_attribute = array_reverse($data_item_attribute);
                $old_item_ids = array_reverse($old_item_ids);

                $item_ids = [];
                foreach($dulicate['ProductsItem'] as $item){
                    if(empty($item['id'])) {
                        throw new Exception();
                    }
                    $item_ids[] = $item['id'];
                }
         
                if(count($item_ids) != count($old_item_ids)){
                    throw new Exception();
                }
                
                foreach($data_item_attribute as $k => $attribute){
                    unset($attribute['id']);

                    $old_id = !empty($attribute['product_item_id']) ? intval($attribute['product_item_id']) : null;
                    $key_old = array_search($old_id, $old_item_ids);
                    if($key_old === false) throw new Exception();

                    $new_id = !empty($item_ids[$key_old]) ? $item_ids[$key_old] : null;
                    if(empty($new_id)) throw new Exception();

                    $attribute['product_id'] = $product_id;
                    $attribute['product_item_id'] = $new_id;

                    $data_item_attribute[$k] = $attribute;
                }
            
                $entities = $table->newEntities($data_item_attribute);
                $save_item_attributes = TableRegistry::get('ProductsItemAttribute')->saveMany($entities);
                
                if(empty($save_item_attributes)){
                    throw new Exception();
                }
            }
            
            $conn->commit();
            $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'nhan_ban_du_lieu_thanh_cong')]);
        }catch (Exception $e) {
            $conn->rollback();
            $message = !empty($e->getMessage()) ? $e->getMessage() : __d('admin', 'nhan_ban_du_lieu_khong_thanh_cong');
            $this->responseJson([MESSAGE => $message]);
        }        
    }

    public function discountProduct() 
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        $discount_percent = !empty($data['discount_percent']) ? floatval($data['discount_percent']) : 0;
        $all_product = !empty($data['all_product']) ? intval($data['all_product']) : null;
        $categories = !empty($data['categories']) ? $data['categories'] : null;
        $brands = !empty($data['brands']) ? $data['brands'] : null;

        if(empty($discount_percent) && $discount_percent < 0) {
            $this->responseJson([MESSAGE => __d('admin', 'ban_vui_long_nhap_thong_tin_giam_gia')]);
        }

        // kiem tra gia tri
        if($discount_percent < 0 || $discount_percent > 100) {
            $this->responseJson([MESSAGE => __d('admin', 'gia_khuyen_mai_trong_khoang_0_den_100')]);
        }

        $check = false;

        if(!empty($all_product)) {
            $params = [];
            $check = true;
        }elseif(!empty($categories) || !empty($brands)) {
            $params['category_id'] = $categories;
            $params['brand_id'] = $brands;
            $check = true;
        }

        if(empty($check)) {
            $this->responseJson([
                CODE => ERROR,
                MESSAGE => __d('admin', 'ban_vui_long_chon_thong_tin_ap_dung_giam_gia')
            ]);
        }

        $this->_discountMultiple($params, $discount_percent);

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'cap_nhat_thanh_cong')
        ]);
    }

    private function _discountMultiple($filter = [], $discount_percent = null, $page = 1)
    {
        if(empty($discount_percent) && $discount_percent < 0) return false;

        $limit = 50;
        $table = TableRegistry::get('ProductsItem');
        $utilities = $this->loadComponent('Utilities');

        $id_categories = !empty($filter['category_id']) ? $filter['category_id'] : [];
        $id_brands = !empty($filter['brand_id']) ? $filter['brand_id'] : [];

        // lấy dữ liệu phân trang
        $where = [
            'ProductsItem.deleted' => 0,
            'ProductsItem.price >' => 0
        ];

        $contain = [];
        if(!empty($id_categories)) {
            // lay id danh muc con
            $all_category_ids = [];
            foreach($id_categories as $category_id){
                $child_category_ids = TableRegistry::get('Categories')->getAllChildCategoryId($category_id);
                $all_category_ids = array_unique(array_merge($all_category_ids, $child_category_ids));
            }

            if(!empty($all_category_ids)){
                $contain[] = 'CategoryProduct';
                $where['CategoryProduct.category_id IN'] = $all_category_ids;
            }
        }

        if (!empty($id_brands)) {
            $contain[] = 'Products';
            $where['Products.brand_id IN'] = $id_brands;
        }

        $query = $table->find()->contain($contain)->where($where)->select([
            'ProductsItem.id', 'ProductsItem.price', 'ProductsItem.time_start_special', 'ProductsItem.time_end_special'
        ])->order('ProductsItem.id DESC');

        $products_item = $this->paginate($query, [
            'limit' => $limit,
            'page' => $page
        ])->toArray();

        if(empty($products_item)) return true;

        $pagination_info = !empty($this->request->getAttribute('paging')['ProductsItem']) ? $this->request->getAttribute('paging')['ProductsItem'] : [];
        $meta_info = $utilities->formatPaginationInfo($pagination_info);

        $data_save = [];
        $conn = ConnectionManager::get('default');

        foreach($products_item as $k_item => $item) {
            $price = !empty($item['price']) ? floatval($item['price']) : 0;
            $price_special = $price - ($price * $discount_percent / 100);

            $data_save[] = [
                'id' => !empty($item['id']) ? intval($item['id']) : null,
                'discount_percent' => $discount_percent,
                'price_special' => empty($discount_percent) ? 0 : $price_special,
                'time_start_special' => null,
                'time_end_special' => null
            ];
        }

        $data_entities = $table->patchEntities($products_item, $data_save);

        try{
            $conn->begin();
            $save = $table->saveMany($data_entities, ['associated' => false]);

            if (empty($save)){
                throw new Exception();
            }

            $conn->commit();
        }catch (Exception $e) {
            $conn->rollback();
            return false;
        }
        
        // kiểm tra xem trang hiện tại đã phải là trang cuối cùng chưa
        if($page < $meta_info['pages']) {
            $this->_discountMultiple($filter, $discount_percent, $page + 1);
        }

        return true;
        
    }

    public function loadAttributeByCategory()
    {
        $this->viewBuilder()->enableAutoLayout(false);

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        $category_id = !empty($data['category_id']) ? intval($data['category_id']) : null;
        // options thuộc tính mở rộng
        $all_options = Hash::combine(TableRegistry::get('AttributesOptions')->getAll($this->lang), '{n}.id', '{n}.name', '{n}.attribute_id');

        $this->set('main_category_id', $category_id);
        $this->set('all_options', $all_options);
        $this->render('element_attributes');
    }

    public function loadAttributeItemByCategory()
    {
        $this->viewBuilder()->enableAutoLayout(false);
        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        // clear lại list items sản phẩm sau khi chọn lại danh mục chính
        $this->render('items');
    }

    public function loadSpecialAttributeItemByCategory()
    {
        $this->viewBuilder()->enableAutoLayout(false);

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        $category_id = !empty($data['category_id']) ? intval($data['category_id']) : null;

        // thuộc tính phiên bản sản phẩm theo danh mục
        $attributes_item = TableRegistry::get('Attributes')->getAttributeByMainCategory($category_id, PRODUCT_ITEM, $this->lang);
        $list_attributes_special = TableRegistry::get('Attributes')->getSpecialAttributeItemByMainCategory($category_id, $this->lang);

        $this->set('main_category_id', $category_id);
        $this->set('list_attributes_special', $list_attributes_special);
        $this->set('attributes_item', $attributes_item);

        $this->render('element_change_attribute_item');
    }

    public function loadBrandByCategory()
    {
        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        $category_id = !empty($data['category_id']) ? intval($data['category_id']) : null;
      
        if(empty($category_id)) {
            $this->responseJson([
                CODE => SUCCESS,
                MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
                DATA => [], 
            ]);
        }

        $brands = TableRegistry::get('Brands')->getBrandByMainCategory($category_id, $this->lang);
        
        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $brands, 
        ]);
    }

}