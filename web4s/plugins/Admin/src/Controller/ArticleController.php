<?php

namespace Admin\Controller;

use Admin\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Http\Response;
use Cake\ORM\Query;
use Cake\Core\Exception\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Cake\Datasource\ConnectionManager;

class ArticleController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function list()
    {
        $this->css_page = [
            '/assets/plugins/global/lightbox/lightbox.css',
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.css'
        ];
        $this->js_page = [
            '/assets/js/pages/list_article.js',
            '/assets/plugins/global/lightbox/lightbox.min.js',
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.js'
        ];

        $this->set('path_menu', 'article');
        $this->set('title_for_layout', __d('admin', 'danh_sach_bai_viet'));
    }

    public function listJson()
    {
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $table = TableRegistry::get('Articles');
        $utilities = $this->loadComponent('Utilities');

        $data = $params = $articles = [];

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

        $params[FILTER][LANG] = !empty($params[FILTER][LANG]) ? $params[FILTER][LANG] : TableRegistry::get('Languages')->getDefaultLanguage();

        // params         
        $params[SORT] = !empty($data[SORT]) ? $data[SORT] : [];
        $params['get_user'] = true;
        $params['get_empty_name'] = true;

        
        // page and limit
        $page = !empty($data[PAGINATION][PAGE]) ? intval($data[PAGINATION][PAGE]) : 1;
        $limit = !empty($data[PAGINATION][PERPAGE]) ? intval($data[PAGINATION][PERPAGE]) : PAGINATION_LIMIT_ADMIN;


        // sort 
        $sort_field = !empty($params[SORT][FIELD]) ? $params[SORT][FIELD] : null;
        $sort_type = !empty($params[SORT][SORT]) ? $params[SORT][SORT] : null;

        if (!empty($data['export'])) {
            $params['get_attributes'] = !empty($data['get_attributes']) ? true : false;
            $params['get_categories'] = !empty($data['get_categories']) ? true : false;
        }

        if(!empty($data['export']) && $data['export'] == 'all') {
            $limit = 100000;
        }

        try {
            $articles = $this->paginate($table->queryListArticles($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        } catch (Exception $e) {
            $page = 1;
            $articles = $this->paginate($table->queryListArticles($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        }

        // parse data before output
        $result = [];
        if(!empty($articles)){
            $languages = TableRegistry::get('Languages')->getList();
            foreach($articles as $k => $article){
                $result[$k] = $table->formatDataArticleDetail($article, $this->lang);
                
                // check multiple language
                $mutiple_language = [];
                if(!empty($languages)){
                    foreach($languages as $lang => $language){
                        if($lang == $this->lang && !empty($article['name'])){
                            $mutiple_language[$lang] = true;

                        }else{
                            $content = TableRegistry::get('ArticlesContent')->find()->where([
                                'article_id' => !empty($article['id']) ? intval($article['id']) : null,
                                'lang' => $lang
                            ])->select(['name'])->first();
                            
                            $mutiple_language[$lang] = !empty($content['name']) ? true : false;
                        }                        
                    }
                }


                $result[$k]['mutiple_language'] = $mutiple_language;
            }
        }

        if(!empty($data['export'])) {
            return $this->exportExcelArticle($result);
        }

        $pagination_info = !empty($this->request->getAttribute('paging')['Articles']) ? $this->request->getAttribute('paging')['Articles'] : [];
        $meta_info = $utilities->formatPaginationInfo($pagination_info);

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $result, 
            META => $meta_info
        ]);
    }

    public function exportExcelArticle($data = [])
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
                'name' => __d('admin', 'danh_sach_bai_viet')
            ]
        ]);
    }

    // khởi tạo file excel
    // Dùng để export dữ liệu excel và download file excel mẫu
    public function initializationExcel($data = [])
    {
        $all_attributes = Hash::combine(TableRegistry::get('Attributes')->getAll($this->lang), '{n}.id', '{n}', '{n}.attribute_type');

        // lấy thông tin thuộc tính
        $attributes_article = !empty($all_attributes[ARTICLE]) ? Hash::combine($all_attributes[ARTICLE], '{n}.code', '{n}') : [];

        $attribute_component = $this->loadComponent('Admin.Attribute');
        $languages = TableRegistry::get('Languages')->getList();

        $categories_article = TableRegistry::get('Categories')->queryListCategories([
            FILTER => [
                TYPE => ARTICLE,
                LANG => $this->lang,
                STATUS => 1
            ]
        ])->all()->nest('id', 'parent_id')->toArray();

        $categories = [];
        if(!empty($categories_article)){
            $categories = Hash::combine(TableRegistry::get('Categories')->parseDataCategoriesExcel($categories_article), '{n}.id', '{n}.CategoriesContent.name');
            ;
        }      

        $data_dropdown = [
            'languages' => !empty($languages) ? implode(',', $languages) : __d('admin', 'tieng_viet'),
            'true_false' => __d('admin', 'co') .','.__d('admin', 'khong'),
            'status' => __d('admin', 'hoat_dong') .','.__d('admin', 'ngung_hoat_dong'),
            'type_video' => __d('admin', 'youtube') .','.__d('admin', 'he_thong'),
        ];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $spreadsheet->getActiveSheet()->setTitle(__d('admin', 'thong_tin_bai_viet'));

        $sheet_category = $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(1);
        $spreadsheet->getActiveSheet()->setTitle(__d('admin', 'thong_tin_danh_muc'));

        $arr_header = [
            'id' => __d('admin', 'id'),
            'name' => __d('admin', 'tieu_de'),
            'lang' => __d('admin', 'ngon_ngu'),
            'category' => __d('admin', 'danh_muc'),
            'featured' => __d('admin', 'noi_bat'),
            'catalogue' => __d('admin', 'muc_luc'),
            'position' => __d('admin', 'vi_tri'),
            'view' => __d('admin', 'luot_xem'),
            'status' => __d('admin', 'trang_thai_sp'),
            'description' => __d('admin', 'mo_ta_ngan'),
            'image_avatar' => __d('admin', 'anh_dai_dien'),
            'images' => __d('admin', 'thu_vien_anh'),
            'url_video' => __d('admin', 'duong_dan_video'),
            'type_video' => __d('admin', 'loai_video')
        ];
        
        if (!empty($attributes_article)) {
            foreach ($attributes_article as $key => $attribute) {
                $attribute_code = !empty($attribute['code']) ? $attribute['code'] : null;
                $attribute_name = !empty($attribute['name']) ? $attribute['name'] : null;
                $input_type = !empty($attribute['input_type']) ? $attribute['input_type'] : null;

                if (!empty($input_type) && ($input_type == ARTICLE_SELECT || $input_type == PRODUCT_SELECT || $input_type == CITY_DISTRICT || $input_type == CITY_DISTRICT_WARD || $input_type == RICH_TEXT || $input_type == ALBUM_IMAGE || $input_type == ALBUM_VIDEO || $input_type == VIDEO)) continue;

                if (!empty($attribute_code) && !empty($attribute_name)) {
                    $arr_header['attribute_' . $attribute_code] = $attribute_name;
                }
            }
        }

        if (empty($arr_header)) return false;

        $column = $column_old = $column_end = 'A';
        $row = 1;
        $row_category = 1;

        $sheet_category->setCellValue('A1', __d('admin', 'danh_muc_bai_viet'));
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);

        foreach ($categories as $key => $data_cate) {
            $sheet_category->setCellValue('A' . $row_category, $data_cate);
            $row_category++;
        }

        foreach ($arr_header as $key => $header) {
            $sheet->setCellValue($column . $row, $header);
            $sheet->getStyle($column . $row)->getFont()->setBold(true);
            $sheet->getStyle($column . $row)->getAlignment()->setVertical('center');

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
                case 'view':
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
                case 'type_video':
                    $spreadsheet->getActiveSheet()->getColumnDimension($column)->setWidth(90, 'pt');
                    $sheet->getStyle($column . $row)->getAlignment()->setHorizontal('center');
                    break;
                default: 
                    $spreadsheet->getActiveSheet()->getColumnDimension($column)->setAutoSize(true);
            }

            $column_old = $column_end = $column;
            $column++;
        }

        // style excel
        $spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(25);
        $spreadsheet->getActiveSheet()->getStyle('A1:' . $column_end . '1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('fcb789');

        $row_excel = 2;
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
                                $categories_item_name = !empty($val_cate['id']) && !empty($categories[$val_cate['id']]) ? $categories[$val_cate['id']] : null;

                                if (empty($categories_item_name)) continue;
                                array_push($categories_item, $categories_item_name);
                            }
                        }

                        $sheet->setCellValue($colum_excel . $row_excel, !empty($item['categories']) ? implode('||', $categories_item) : '');
                        $spreadsheet->getActiveSheet()->getStyle($colum_excel . $row_excel)->getAlignment()->setWrapText(true);

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
                    case 'view':
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
                    case 'image_avatar':
                        $image_avatar = !empty($item['image_avatar']) ? CDN_URL . $item['image_avatar'] : '';

                        $sheet->setCellValue($colum_excel . $row_excel, $image_avatar);
                        $spreadsheet->getActiveSheet()->getStyle($colum_excel . $row_excel)->getAlignment()->setWrapText(true);

                        break;
                    case 'images':
                        $images = [];
                        if (!empty($item['images'])) {
                            foreach ($item['images'] as $key => $image) {
                                $images[] = !empty($image) ? CDN_URL . $image : '';
                            }
                        }

                        $sheet->setCellValue($colum_excel . $row_excel, !empty($images) ? implode('||', $images) : '');

                        break;
                    case 'url_video':
                        $sheet->setCellValue($colum_excel . $row_excel, !empty($item[$code]) ? $item[$code] : '');

                        break;
                    case 'type_video':
                        $type_video = !empty($item['type_video']) ? $item['type_video'] : null;

                        $type_video_name = '';
                        if ($type_video == 'video_youtube') {
                            $type_video_name = __d('admin', 'youtube');
                        } else if ($type_video == 'he_thong') {
                            $type_video_name = __d('admin', 'he_thong');
                        }

                        $sheet->setCellValue($colum_excel . $row_excel, $type_video_name);

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
                        $validation->setFormula1('"' . $data_dropdown['type_video'] . '"');

                        $sheet->getStyle($colum_excel . $row_excel)->getAlignment()->setHorizontal('center');

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
                    default:
                        $sheet->setCellValue($colum_excel . $row_excel, !empty($item[$code]) ? $item[$code] : '');
                        break;
                }

                $sheet->getStyle($colum_excel . $row_excel)->getAlignment()->setVertical('center');
                $colum_excel ++;
            }

            $row_excel ++;
        }

        return $spreadsheet;
    }

    public function add()
    {
        $settings = TableRegistry::get('Settings')->getSettingWebsite();

        // cấu hình thuộc tính theo danh mục
        $setting_attributes_category = !empty($settings['article_attributes_category']) ? $settings['article_attributes_category'] : null;
        $attribute_by_category = !empty($setting_attributes_category['status']) ? true : false;
        

        // cấu hình mã nhúng thuộc tính mở rộng vào nội dung bài viết
        $embed_attribute = [];
        $setting_embed_attribute = !empty($settings['attribute_article']) ? $settings['attribute_article'] : [];
        if(!empty($setting_embed_attribute['use_embed_attribute'])){

            $embed_attribute = !empty($setting_embed_attribute['config_embed_attribute']) ? json_decode($setting_embed_attribute['config_embed_attribute'], true) : [];
        }

        // options attribute
        $all_options = Hash::combine(TableRegistry::get('AttributesOptions')->getAll($this->lang), '{n}.id', '{n}.name','{n}.attribute_id');
        $max_record = TableRegistry::get('Articles')->find()->select('id')->max('id');

        $this->set('attribute_by_category', $attribute_by_category);
        $this->set('all_options', $all_options);
        $this->set('position', !empty($max_record->id) ? $max_record->id + 1 : 1);
        $this->set('list_category_main', []);
        $this->set('embed_attribute', $embed_attribute);

        $this->css_page = [
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.css'
        ];

        $this->js_page = [
            '/assets/plugins/custom/tinymce6/tinymce.min.js',
            '/assets/js/seo_analysis.js',
            '/assets/js/pages/article.js',
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.js'
        ];

        $this->set('path_menu', 'article_add');
        $this->set('title_for_layout', __d('admin', 'them_bai_viet'));
        $this->render('update');
    }

    public function update($id = null)
    {
         // thông tin bài viết
        $table = TableRegistry::get('Articles');
        $article = $table->getDetailArticle($id, $this->lang, [
            'get_user' => true, 
            'get_categories' => true,
            'get_tags' => true,
            'get_attributes' => true
        ]);

        $article = $table->formatDataArticleDetail($article, $this->lang);
        if(empty($article)) $this->showErrorPage();

        // danh mục chính
        $main_category_id = !empty($article['main_category_id']) ? intval($article['main_category_id']) : null;

        $list_category_main = [];
        if(!empty($article['categories'])) {
            foreach($article['categories'] as $category_id => $category_main){
                if(empty($category_main['id']) || empty($category_main['name'])) continue;
                $list_category_main[$category_id] = $category_main['name'];
            }
        }

        // tất cả cấu hình
        $settings = TableRegistry::get('Settings')->getSettingWebsite();
        
        // cấu hình thuộc tính theo danh mục
        $setting_attributes_category = !empty($settings['article_attributes_category']) ? $settings['article_attributes_category'] : null;
        $attribute_by_category = !empty($setting_attributes_category['status']) ? true : false;

        // cấu hình mã nhúng thuộc tính mở rộng vào nội dung bài viết
        $embed_attribute = [];
        $setting_embed_attribute = !empty($settings['attribute_article']) ? $settings['attribute_article'] : [];
        if(!empty($setting_embed_attribute['use_embed_attribute'])){
            $embed_attribute = !empty($setting_embed_attribute['config_embed_attribute']) ? json_decode($setting_embed_attribute['config_embed_attribute'], true) : [];
        }

        $all_options = Hash::combine(TableRegistry::get('AttributesOptions')->getAll($this->lang), '{n}.id', '{n}.name','{n}.attribute_id');
        
        $this->set('path_menu', 'article');

        $this->set('id', $id);
        $this->set('position', !empty($article['position']) ? $article['position'] : 1);
        $this->set('all_options', $all_options);
        $this->set('article', $article);
        
        $this->set('attribute_by_category', $attribute_by_category);
        $this->set('main_category_id', $main_category_id);
        $this->set('list_category_main', $list_category_main);
        $this->set('embed_attribute', $embed_attribute);
        

        $this->css_page = [
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.css'
        ];

        $this->js_page = [
            '/assets/plugins/custom/tinymce6/tinymce.min.js',
            '/assets/js/seo_analysis.js',
            '/assets/js/pages/article.js',
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.js'
        ];
        $this->set('title_for_layout', __d('admin', 'cap_nhat_bai_viet'));
    }

    public function detail($id = null)
    {
        if(empty($id)){
            $this->showErrorPage();
        }

        $table = TableRegistry::get('Articles');

        $article_detail = $table->getDetailArticle($id, $this->lang, [
            'get_user' => true, 
            'get_categories' => true,
            'get_tags' => true,
            'get_attributes' => true
        ]);

        if(empty($article_detail)){
            $this->showErrorPage();
        }

        $article = $table->formatDataArticleDetail($article_detail, $this->lang);

        $this->css_page = [
            '/assets/css/pages/wizard/wizard-4.css',
            '/assets/plugins/global/lightbox/lightbox.css'
        ];
        $this->js_page = [
            '/assets/plugins/global/lightbox/lightbox.min.js'
        ];

        $this->set('path_menu', 'article');
        $this->set('article', $article);
        $this->set('title_for_layout', __d('admin', 'chi_tiet_bai_viet'));
    }

    public function save($id = null)
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();

        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }
        
        $utilities = $this->loadComponent('Utilities');
        $attribute_component = $this->loadComponent('Admin.Attribute');
        $table = TableRegistry::get('Articles');        
        $tags_table = TableRegistry::get('Tags');

        if(!empty($id)){
            $article = $table->getDetailArticle($id, $this->lang, [
                'get_user' => false, 
                'get_categories' => true,
                'get_attributes' => true
            ]);

            if(empty($article)){
                $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
            }
        }

        // validate data
        if(empty($data['name'])){
            $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_tieu_de')]);
        }

        $link = !empty($data['link']) ? $utilities->formatToUrl(trim($data['link'])) : null;
        if(empty($link)){
            $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_duong_dan')]);
        }

        $link_id = !empty($article['Links']) ? $article['Links']['id'] : null;
        if(TableRegistry::get('Links')->checkExist($link, $link_id)){
            $this->responseJson([MESSAGE => __d('admin', 'duong_dan_da_ton_tai_tren_he_thong')]);
        }

        // format data before save
        $list_keyword = !empty($data['seo_keyword']) ? array_column(json_decode($data['seo_keyword'], true), 'value') : null;
        $seo_keyword = !empty($list_keyword) ? implode(', ', $list_keyword) : null;
        
        $url_video = !empty($data['url_video']) ? $data['url_video'] : null;
        $type_video = null;
        if(!empty($url_video)){
            $type_video = !empty($data['type_video']) ? $data['type_video'] : null;
        }

        $has_album = $has_video = $has_file = 0;
        if(!empty($data['images'])){
            $has_album = 1;
        }

        if(!empty($data['url_video'])){
            $has_video = 1;
        }

        if(!empty($data['files'])){
            $has_file = 1;
        }

        $files = [];
        if(!empty($data['files'])){
            foreach (json_decode($data['files'], true) as $key => $file) {
                $files[] = str_replace(CDN_URL , '', $file);
            }
        }

        $status = isset($article['status']) ? intval($article['status']) : 1;
        $draft = !empty($data['draft']) ? 1 : 0;
        if(!empty($draft)){
            $status = 0;
        }
        
        // kiểm tra duyệt bài
        $settings = TableRegistry::get('Settings')->getSettingWebsite();
        $approved_article = !empty($settings['approved_article']) ? $settings['approved_article'] : [];
        $approved_role_id = !empty($approved_article['role_id']) ? array_map('intval', explode('|', $approved_article['role_id'])) : [];
        if(empty($draft) && !empty($approved_article['approved']) && !in_array($this->Auth->user('role_id'), $approved_role_id)){            
            // đổi trạng thái bài viết = -1 (chờ duyệt)
            $status = -1;
        }

        $data_save = [
            'image_avatar' => !empty($data['image_avatar']) ? $data['image_avatar'] : null,
            'images' => !empty($data['images']) ? $data['images'] : null,
            'url_video' => $url_video,
            'type_video' => $type_video,
            'files' => !empty($files) ? json_encode($files) : null,
            'has_album' => $has_album,
            'has_file' => $has_file,
            'has_video' => $has_video,
            'position' => !empty($data['position']) ? intval($data['position']) : 1,
            'main_category_id' => !empty($data['main_category_id']) ? intval($data['main_category_id']) : null,
            'view' => !empty($data['view']) ? intval(str_replace(',', '', $data['view'])) : 0,
            'featured' => !empty($data['featured']) ? 1 : 0,
            'catalogue' => !empty($data['catalogue']) ? 1 : 0,
            'seo_score' => !empty($data['seo_score']) ? $data['seo_score'] : null,
            'keyword_score' => !empty($data['keyword_score']) ? $data['keyword_score'] : null,
            'draft' => $draft,
            'status' => $status
        ];

        $name = !empty($data['name']) ? trim(strip_tags($data['name'])) : null;
        $seo_title = !empty($data['seo_title']) ? trim(strip_tags($data['seo_title'])) : null;
        $seo_description = !empty($data['seo_description']) ? trim(strip_tags($data['seo_description'])) : null;
        
        $data_save['ArticlesContent'] = [
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
            'type' => ARTICLE_DETAIL,
            'url' => $link,
            'lang' => $this->lang,
        ];

        $data_categories = [];
        if(!empty($data['categories'])){
            foreach($data['categories'] as $category_id){
                $data_categories[] = [
                    'article_id' => $id,
                    'category_id' => $category_id
                ];
            }
        }


        $data_save['CategoriesArticle'] = $data_categories;    
        $data_save['ArticlesAttribute'] = $attribute_component->formatDataAttributesBeforeSave($data, $this->lang, ARTICLE, $id);

        // merge data with entity 
        if(empty($id)){
            $data_save['created_by'] = $this->Auth->user('id');
            $article = $table->newEntity($data_save, [
                'associated' => ['ArticlesContent', 'Links', 'CategoriesArticle', 'ArticlesAttribute']
            ]);
        }else{            
            $article = $table->patchEntity($article, $data_save);
        }

        // show error validation in model
        if($article->hasErrors()){
            $list_errors = $utilities->errorModel($article->getErrors());            
            $this->responseJson([
                MESSAGE => !empty($list_errors[0]) ? $list_errors[0] : null,
                DATA => $list_errors
            ]);
        }

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();
            if(!empty($id)){
                $clear_categories = TableRegistry::get('CategoriesArticle')->deleteAll(['article_id' => $id]);
                $clear_attributes = TableRegistry::get('ArticlesAttribute')->deleteAll(['article_id' => $id]);

                $clear_tags = TableRegistry::get('TagsRelation')->deleteAll([
                    'foreign_id' => $id,
                    'type' => ARTICLE_DETAIL
                ]);
            }
            
            $save = $table->save($article);
            if (empty($save->id)){
                throw new Exception();
            }

            $tags = !empty($data['tags']) ? array_filter(array_column(json_decode($data['tags'], true), 'value')) : null;
            if(!empty($tags)){
                $tag_data = [];
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
                    $tag_data[] = [
                        'foreign_id' => $save->id,
                        'type' => ARTICLE_DETAIL,
                        'tag_id' => $tag_id
                    ];
                }

                $entities_tags = TableRegistry::get('TagsRelation')->newEntities($tag_data);
                $save_tags = TableRegistry::get('TagsRelation')->saveMany($entities_tags);
                if (empty($save_tags)){
                    throw new Exception();
                }
            }

            $conn->commit();

            // dich tự động các ngôn ngữ khác khi thêm mới bản ghi
            $auto_translate = TableRegistry::get('Settings')->getSettingAutoTranslate();
            if(empty($id) && $auto_translate){
                $this->translateArticle($save->id, $this->lang);
            }

            $this->responseJson([CODE => SUCCESS, DATA => ['id' => $save->id]]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    private function translateArticle($article_id = null, $lang_from = null)
    {
        if(empty($article_id) || empty($lang_from)) return false;

        $languages = TableRegistry::get('Languages')->getList();
        if(empty($languages)) return false;

        $utilities = $this->loadComponent('Utilities');
        $translate_component = $this->loadComponent('Admin.Translate');

        $table = TableRegistry::get('Articles');
        $links_table = TableRegistry::get('Links');
        $article_info = $table->getDetailArticle($article_id, $lang_from);
        if(empty($article_info)) return false;

        foreach($languages as $lang => $language){
            if($lang == $lang_from) continue;

            $name = !empty($article_info['ArticlesContent']['name']) ? $article_info['ArticlesContent']['name'] : null;
            $translates = $translate_component->translate([$name], $lang_from, $lang);
            
            $name_translate = !empty($translates[0]) ? $translates[0] : $name;

            $link = $utilities->formatToUrl($name_translate);
            if(empty($link)) continue;

            $link = $links_table->getUrlUnique($link);
            $data_save = [
                'id' => $article_id,
                'ArticlesContent' => [
                    'name' => $name_translate,
                    'seo_title' => $name_translate,
                    'lang' => $lang,
                    'search_unicode' => strtolower($utilities->formatSearchUnicode([$name_translate]))
                ],
                'Links' => [
                    'type' => ARTICLE_DETAIL,
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

    public function delete()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        $ids = !empty($data['ids']) ? $data['ids'] : [];

        if (!$this->getRequest()->is('post') || empty($ids) || !is_array($ids)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('Articles');

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            foreach($ids as $id){

                // delete article
                $article = $table->get($id);
                if (empty($article)) {
                    throw new Exception(__d('admin', 'khong_tim_thay_thong_tin_bai_viet'));
                }

                $article = $table->patchEntity($article, ['id' => $id, 'deleted' => 1], ['validate' => false]);
                $delete = $table->save($article);
                if (empty($delete)){
                    throw new Exception();
                }

                $delete_link = TableRegistry::get('Links')->updateAll(
                    [  
                        'deleted' => 1
                    ],
                    [  
                        'foreign_id' => $id,
                        'type' => ARTICLE_DETAIL
                    ]
                );
            }

            $conn->commit();
            $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'xoa_du_lieu_thanh_cong')]);

        }catch (Exception $e) {
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
        $status = !empty($data['status']) ? 1 : 0;

        if (!$this->getRequest()->is('post') || empty($ids) || !is_array($ids)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('Articles');

        $articles = $table->find()->where([
            'Articles.id IN' => $ids,
            'Articles.deleted' => 0
        ])->select(['Articles.id', 'Articles.status', 'Articles.draft'])->toArray();
        
        if(empty($articles)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_bai_viet')]);
        }

        $patch_data = [];
        foreach ($ids as $k => $article_id) {
            $patch_data[] = [
                'id' => $article_id,
                'status' => $status,
                'draft' => 0
            ];
        }

        $entities = $table->patchEntities($articles, $patch_data, ['validate' => false]);
        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $change_status = $table->saveMany($entities);
            if (empty($change_status)){
                throw new Exception();
            }
            
            $conn->commit();
            $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'cap_nhat_thanh_cong')]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function duplicate()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        $ids = !empty($data['ids']) ? $data['ids'] : [];

        if (!$this->getRequest()->is('post') || empty($ids) || !is_array($ids)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $articles_table = TableRegistry::get('Articles');
        $system = $this->loadComponent('System');
        $utilities = $this->loadComponent('Utilities');

        $data_dulicate = [];
        foreach($ids as $id){
            $article = $articles_table->find()
            ->contain([
                'ContentMutiple',
                'CategoriesArticle',
                'LinksMutiple',
                'TagsRelation',
                'ArticlesAttribute',
            ])
            ->where([
                'Articles.id' => $id,
                'Articles.deleted' => 0,
            ])->first()->toArray();
            
            if(empty($article)) continue;
            
            // format data before mere entity
            unset($article['id']);
            unset($article['created_by']);
            unset($article['created']);
            unset($article['updated']);

            unset($article['view']);
            unset($article['like']);
            unset($article['has_album']);
            unset($article['has_file']);
            unset($article['has_video']);
            unset($article['comment']);

            if(!empty($article['ContentMutiple'])){
                foreach($article['ContentMutiple'] as $k_content => $content){
                    $name = $system->getNameUnique('Articles', $content['name'], 1);
                    $article['ContentMutiple'][$k_content]['name'] = $name;

                    unset($article['ContentMutiple'][$k_content]['id']);
                    unset($article['ContentMutiple'][$k_content]['category_id']);
                }
            }

            if(!empty($article['LinksMutiple'])){
                foreach($article['LinksMutiple'] as $k_link => $link){
                    $article['LinksMutiple'][$k_link]['url'] = $system->getUrlUnique($link['url'], 1);

                    unset($article['LinksMutiple'][$k_link]['id']);
                    unset($article['LinksMutiple'][$k_link]['foreign_id']);
                }
            }

            if(!empty($article['CategoriesArticle'])){
                foreach($article['CategoriesArticle'] as $k_category => $category_article){
                    unset($article['CategoriesArticle'][$k_category]['id']);
                    $article['CategoriesArticle'][$k_category]['article_id'] = null;
                }
            }

            if(!empty($article['TagsRelation'])){
                foreach($article['TagsRelation'] as $k_tag => $tag){
                    unset($article['TagsRelation'][$k_tag]['id']);
                    unset($article['TagsRelation'][$k_tag]['foreign_id']);
                }
            }

            if(!empty($product['ArticlesAttribute'])){
                foreach($product['ArticlesAttribute'] as $k_attribute => $attribute){
                    unset($product['ArticlesAttribute'][$k_attribute]['id']);
                    $product['ArticlesAttribute'][$k_attribute]['article_id'] = null;
                }
            }

            $data_dulicate[] = $article;            
        }

        $article_entities = $articles_table->newEntities($data_dulicate, [
            'associated' => ['ContentMutiple', 'LinksMutiple', 'CategoriesArticle', 'TagsRelation', 'ArticlesAttribute']
        ]);

        try{
            // save data
            $save = $articles_table->saveMany($article_entities);  
            
            $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'nhan_ban_du_lieu_thanh_cong')]);
        }catch (Exception $e) {
            $this->responseJson([MESSAGE => $e->getMessage()]);
        }        
    }

    public function changePosition()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        $id = !empty($data['id']) ? intval($data['id']) : null;
        $value = !empty($data['value']) ? $data['value'] : 0;

        if(!$this->getRequest()->is('post') || empty($id)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('Articles');
        $article = $table->get($id);
        if(empty($article)) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
        }

        $article = $table->patchEntity($article, ['position' => $value], ['validate' => false]);

        try{
            $save = $table->save($article);

            if (empty($save->id)){
                throw new Exception();
            }
            $this->responseJson([CODE => SUCCESS, DATA => ['id' => $save->id]]);

        }catch (Exception $e) {
            $this->responseJson([MESSAGE => $e->getMessage()]);
        }
    }

    public function autoSuggest()
    {
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('Articles');
        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        $filter = !empty($data[FILTER]) ? $data[FILTER] : [];
        $filter[LANG] = $this->lang;
        
        $articles = $table->queryListArticles([
            FILTER => $filter,
            FIELD => FULL_INFO
        ])->limit(10)->toArray();

        $result = [];
        if(!empty($articles)){
            foreach($articles as $article){
                $result[] = $table->formatDataArticleDetail($article, $this->lang);
            }
        }
  
        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $result, 
        ]);
    }

    public function quickUpload()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();

        $id = !empty($data['id']) ? $data['id'] : null;
        $images = !empty($data['images']) ? $data['images'] : [];
        $image_avatar = !empty($data['image_avatar']) ? $data['image_avatar'] : [];

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        // validate data
        if (empty($id)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('Articles');

        $has_album = 0;
        if(!empty($images)){
            $has_album = 1;
        }

        $article_info = $table->find()->where([
            'id' => $id,            
            'deleted' => 0,
        ])->select(['id', 'images', 'image_avatar'])->first();

        if (empty($article_info)) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
        }

        $article = $table->patchEntity($article_info, [
            'images' => $images,
            'image_avatar' => $image_avatar,
            'has_album' => $has_album
        ]);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            // save data
            $save = $table->save($article);
            if (empty($save->id)){
                throw new Exception();
            } 

            $conn->commit();

            $this->responseJson([CODE => SUCCESS, DATA => $article]);

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

        $table = TableRegistry::get('Articles');   
        $article = $table->find()->where([
            'id' => $id,
            'deleted' => 0,
        ])->select(['id', 'images', 'image_avatar'])->first();

        $article['images'] = !empty($article['images']) ? json_decode($article['images'], true) : [];
        $article['image_avatar'] = !empty($article['image_avatar']) ? $article['image_avatar'] : [];


        if(empty($article)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
        }
        
        $this->set('article', $article);
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
}