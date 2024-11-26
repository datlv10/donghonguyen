<?php

namespace Admin\Controller;

use Admin\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Http\Response;
use Cake\ORM\Query;
use Cake\Core\Configure;
use Cake\Core\Exception\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Cake\Datasource\ConnectionManager;
use Cake\Collection\Collection;

class GenealogyController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function list()
    {
        $table = TableRegistry::get('Genealogies');

        $education_level = Configure::read('education_level');
        $genealogy_select = $table->getList();

        // lấy số đời lớn nhất để tạo ra mảng danh sách đời
        $generation_max = $table->find()->select('generation')->max('generation');
        $generation_max = !empty($generation_max['generation']) ? intval($generation_max['generation']) : 1;

        $list_generation = [];
        for ($i=1; $i <= $generation_max ; $i++) { 
            $number_roman = $table->romanNumerals($i);
            $list_generation[$i] = !empty($number_roman) ? $number_roman : 'I';
        }

        $this->css_page = [
            '/assets/plugins/global/lightbox/lightbox.css',
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.css',
            '/assets/plugins/custom/jstree/jstree.bundle.css'
        ];
        
        $this->js_page = [
            '/assets/plugins/custom/tinymce6/tinymce.min.js',
            '/assets/js/pages/genealogy.js',
            '/assets/plugins/global/lightbox/lightbox.min.js',
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.js',
            '/assets/plugins/custom/jstree/jstree.bundle.js'
        ];

        $this->set('genealogy_select', $genealogy_select);
        $this->set('list_generation', $list_generation);
        $this->set('education_level', $education_level);
        $this->set('path_menu', 'genealogy');
        $this->set('title_for_layout', 'Phả đồ');
    }

    public function listJson()
    {
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $table = TableRegistry::get('Genealogies');
        $utilities = $this->loadComponent('Utilities');

        $data = $params = $genealogies = [];

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
        
        // page and limit
        $page = !empty($data[PAGINATION][PAGE]) ? intval($data[PAGINATION][PAGE]) : 1;
        $limit = !empty($data[PAGINATION][PERPAGE]) ? intval($data[PAGINATION][PERPAGE]) : PAGINATION_LIMIT_ADMIN;

        // năm hiện tại 
        $year_now = date('Y');

        $birthday = !empty($params[FILTER]['birthday']) ? $params[FILTER]['birthday'] : null;
        $birthday = !empty($birthday) ? explode('-', $birthday) : [];

        $age_from = isset($birthday[0]) ? intval($birthday[0]) : null; 
        $age_to = isset($birthday[1]) ? intval($birthday[1]) : null; 

        $year_from = isset($birthday[1]) ? intval($year_now) - intval($birthday[1]) : null;
        $year_to = isset($birthday[0]) && isset($birthday[1]) ? intval($year_now) - intval($birthday[0]) : null;

        $birthday_from = !empty($year_from) ? strtotime($year_from . '-01-01 00:00:00') : null;
        $birthday_to = !empty($year_to) ? strtotime($year_to . '-01-01 00:00:00') : null;

        if (!empty($params[FILTER]['birthday'])) {
            unset($params[QUERY]['birthday']);
            unset($params[FILTER]['birthday']);
        }

        if (!empty($age_from)) {
            $params[FILTER]['age_from'] = $age_from;
        }

        if (!empty($age_to)) {
            $params[FILTER]['age_to'] = $age_to;
        }

        if (!empty($birthday_from)) {
            $params[FILTER]['birthday_from'] = $birthday_from;
        }

        if (!empty($birthday_to)) {
            $params[FILTER]['birthday_to'] = $birthday_to;
        }

        // sort 
        $sort_field = !empty($params[SORT][FIELD]) ? $params[SORT][FIELD] : null;
        $sort_type = !empty($params[SORT][SORT]) ? $params[SORT][SORT] : null;

        if(!empty($data['export']) && $data['export'] == 'all') {
            $limit = 100000;
        }

        try {
            $genealogies = $this->paginate($table->queryListGenealogies($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        } catch (Exception $e) {
            $page = 1;
            $genealogies = $this->paginate($table->queryListGenealogies($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        }

        $result = [];
        if (!empty($genealogies)) {
            foreach ($genealogies as $key => $genealogy) {
                $result[$key] = $this->formatDataGenealogy($genealogy);
            }
        }

        if(!empty($data['export'])) {
            return $this->exportExcelGenealogies($result);
        }

        $pagination_info = !empty($this->request->getAttribute('paging')['Genealogies']) ? $this->request->getAttribute('paging')['Genealogies'] : [];
        $meta_info = $utilities->formatPaginationInfo($pagination_info);

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $result, 
            META => $meta_info
        ]);
    }

    public function exportExcelGenealogies($data = [])
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
                'name' => 'Danh sách phả đồ'
            ]
        ]);
    }

    public function initializationExcel($data = [])
    {    
        $data_dropdown = [
            'true_false' => 'Có,Không',
            'status' => 'Còn sống,Đã mất',
            'sex' => 'Nam,Nữ,Khác',
        ];

        $education_level = Configure::read('education_level');
        $data_dropdown['education_level'] = !empty($education_level) ? implode(',', $education_level) : '';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $spreadsheet->getActiveSheet()->setTitle('Danh sách phả đồ');

        $arr_header = [
            'id' => __d('admin', 'id'),
            'full_name' => 'Họ và tên',
            'self_name' => 'Tên tự',
            'education_level' => 'Trình tự học vấn',
            'generation' => 'Thuộc đời',
            'status' => 'Tình trạng',
            'sex' => 'Giới tính',
            'genealogical' => 'Thuộc phả đồ',
            'year_of_birth' => 'Năm sinh',
            'year_of_death' => 'Năm mất',
            'burial' => 'Nơi an táng'
        ];

        if (empty($arr_header)) return false;

        $column = $column_old = $column_end = 'A';
        $row = 1;

        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);

        foreach ($arr_header as $key => $header) {
            $sheet->setCellValue($column . $row, $header);
            $sheet->getStyle($column . $row)->getFont()->setBold(true);
            $sheet->getStyle($column . $row)->getAlignment()->setVertical('center');

            switch ($key) {
                case 'id':
                    $spreadsheet->getActiveSheet()->getColumnDimension($column)->setWidth(25, 'pt');
                    $sheet->getStyle($column . $row)->getAlignment()->setHorizontal('center');
                    break;
                case 'full_name':
                case 'year_of_birth':
                case 'year_of_death':
                    $spreadsheet->getActiveSheet()->getColumnDimension($column)->setWidth(200, 'pt');
                    break;
                case 'self_name':
                case 'education_level':
                    $spreadsheet->getActiveSheet()->getColumnDimension($column)->setWidth(150, 'pt');
                    break;
                case 'status':
                case 'sex':
                case 'genealogical':
                    $spreadsheet->getActiveSheet()->getColumnDimension($column)->setWidth(100, 'pt');
                    $sheet->getStyle($column . $row)->getAlignment()->setHorizontal('center');
                    break;
                case 'burial':
                    $spreadsheet->getActiveSheet()->getColumnDimension($column)->setWidth(250, 'pt');
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
                    case 'education_level':
                        $sheet->setCellValue($colum_excel . $row_excel, !empty($item['education_level_name']) ? $item['education_level_name'] : '');

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
                        $validation->setFormula1('"' . $data_dropdown['education_level'] . '"');

                        break;
                    case 'status':
                        $sheet->setCellValue($colum_excel . $row_excel, !empty($item['status_name']) ? $item['status_name'] : 'Đã mất');

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
                    case 'sex':
                        $sheet->setCellValue($colum_excel . $row_excel, !empty($item['sex_name']) ? $item['sex_name'] : '');

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
                        $validation->setFormula1('"' . $data_dropdown['sex'] . '"');

                        $sheet->getStyle($colum_excel . $row_excel)->getAlignment()->setHorizontal('center');

                        break;
                    case 'genealogical':
                        $sheet->setCellValue($colum_excel . $row_excel, !empty($item['genealogical']) ? 'Có' : 'Không');

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
                    case 'generation':
                        $sheet->setCellValue($colum_excel . $row_excel, !empty($item[$code]) ? $item[$code] : '');
                        $sheet->getStyle($colum_excel . $row_excel)->getAlignment()->setHorizontal('center');

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

    public function loadListGenealogy()
    {
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $table = TableRegistry::get('Genealogies');
        $result = $table->getListTreeGenealogies();

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $result
        ]);
    }

    public function loadRelationship()
    {
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $result = TableRegistry::get('Genealogies')->getList();

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $result
        ]);
    }

    public function formatDataGenealogy($data = [])
    {
        if (empty($data)) return [];

        $arr_sex = [
            'male' => 'Nam',
            'female' => 'Nữ',
            'other' => 'Khác',
        ];

        $arr_status = [
            0 => 'Đã mất',
            1 => 'Còn sống'
        ];

        $arr_education_level = Configure::read('education_level');

        $full_name = !empty($data['full_name']) ? $data['full_name'] : null;
        $image_avatar = !empty($data['image_avatar']) ? $data['image_avatar'] : null;
        $generation = !empty($data['generation']) ? $data['generation'] : null;

        $sex_name = !empty($data['sex']) && !empty($arr_sex[$data['sex']]) ? $arr_sex[$data['sex']] : null;
        $status_name = isset($data['status']) && !empty($arr_status[$data['status']]) ? $arr_status[$data['status']] : null;
        $education_level_name = !empty($data['education_level']) && !empty($arr_education_level[$data['education_level']]) ? $arr_education_level[$data['education_level']] : null;

        $generation = TableRegistry::get('Genealogies')->romanNumerals($generation);

        $result = [
            'id' => !empty($data['id']) ? intval($data['id']) : null,
            'full_name' => $full_name,
            'self_name' => !empty($data['self_name']) ? $data['self_name'] : null,
            'year_of_birth' => !empty($data['year_of_birth']) ? $data['year_of_birth'] : null,
            'year_of_death' => !empty($data['year_of_death']) ? $data['year_of_death'] : null,
            'burial' => !empty($data['burial']) ? $data['burial'] : null,
            'relationship_position' => !empty($data['relationship_position']) ? $data['relationship_position'] : null,
            'generation' => $generation,
            'image_avatar' => $image_avatar,
            'education_level_name' => $education_level_name,
            'sex_name' => $sex_name,
            'status_name' => $status_name
        ];

        return $result;
    }

    public function update($id = null)
    {
        $this->viewBuilder()->enableAutoLayout(false);

        // thông tin bài viết
        $table = TableRegistry::get('Genealogies');
        $genealogy = $table->getDetailGenealogy($id);

        $education_level = Configure::read('education_level');
        $genealogy_select = TableRegistry::get('Genealogies')->getList();
        
        $this->set('path_menu', 'genealogy');

        $this->set('id', $id);
        $this->set('genealogy', $genealogy);   
        $this->set('genealogy_select', $genealogy_select);
        $this->set('education_level', $education_level); 

        $this->css_page = [
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.css'
        ];

        $this->js_page = [
            '/assets/plugins/custom/tinymce6/tinymce.min.js',
            '/assets/js/pages/list_genealogy.js',
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.js'
        ];

        $this->render('element_update');
    }

    public function detail($id = null)
    {
        $this->viewBuilder()->enableAutoLayout(false);

        $table = TableRegistry::get('Genealogies');
        $genealogy = $table->getDetailGenealogy($id);

        $arr_sex = [
            'male' => 'Nam',
            'female' => 'Nữ',
            'other' => 'Khác',
        ];

        $arr_status = [
            0 => 'Đã mất',
            1 => 'Còn sống'
        ];

        $arr_education_level = Configure::read('education_level');

        $genealogy['sex_name'] = !empty($genealogy['sex']) && !empty($arr_sex[$genealogy['sex']]) ? $arr_sex[$genealogy['sex']] : null;
        $genealogy['status_name'] = isset($genealogy['status']) && !empty($arr_status[$genealogy['status']]) ? $arr_status[$genealogy['status']] : null;
        $genealogy['education_level_name'] = !empty($genealogy['education_level']) && !empty($arr_education_level[$genealogy['education_level']]) ? $arr_education_level[$genealogy['education_level']] : null;

        $genealogy['generation'] = !empty($genealogy['generation']) ? $table->romanNumerals($genealogy['generation']) : 'I';

        // danh sách vợ
        $list_wife = $list_husband = [];
        if ($genealogy['sex'] == 'male') {
            $list_wife = $table->getListWife($id);
            if (!empty($list_wife)) {
                foreach ($list_wife as $key => $wife) {
                    $list_wife[$key] = $this->formatDataGenealogy($wife);
                }
            }
        }

        if ($genealogy['sex'] == 'female') {
            $list_husband = $table->getListHusband($id);
            if (!empty($list_husband)) {
                foreach ($list_husband as $key => $husband) {
                    $list_husband[$key] = $this->formatDataGenealogy($husband);
                }
            }
        }
        

        // danh sách con
        $list_child = $table->getListChild($id);
        if (!empty($list_child)) {
            foreach ($list_child as $key => $child) {
                $list_child[$key] = $this->formatDataGenealogy($child);
            }
        }

        $this->css_page = [
            '/assets/css/pages/wizard/wizard-4.css',
            '/assets/plugins/global/lightbox/lightbox.css'
        ];
        $this->js_page = [
            '/assets/plugins/global/lightbox/lightbox.min.js'
        ];

        $this->set('path_menu', 'genealogy');
        $this->set('genealogy', $genealogy);
        $this->set('list_husband', $list_husband);
        $this->set('list_wife', $list_wife);
        $this->set('list_child', $list_child);

        $this->render('element_detail');
    }

    public function save($id = null)
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();

        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('Genealogies');
        $utilities = $this->loadComponent('Utilities');

        $update_path_id = false; // = true khi mà thông tin path_id khác với dữ lưu post từ form
        if(!empty($id)){
            $genealogy = $table->getDetailGenealogy($id);
            $data_relationship_info = !empty($genealogy['relationship_info']) ? intval($genealogy['relationship_info']) : null;

            if(empty($genealogy)){
                $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
            }
        }

        $full_name = !empty($data['full_name']) ? $data['full_name'] : null;
        $self_name = !empty($data['self_name']) ? $data['self_name'] : null;

        $relationship = isset($data['relationship']) ? intval($data['relationship']) : 0;
        $relationship_info = !empty($data['relationship_info']) ? intval($data['relationship_info']) : null;
        $relationship_position = !empty($data['relationship_position']) ? intval($data['relationship_position']) : null;

        $path_id = [];
        if (!empty($relationship_info)) {
            $path_info = $table->find()->where(['id' => $relationship_info])->select(['path_id'])->first();
            $path_id = !empty($path_info['path_id']) ? explode('|', $path_info['path_id']) : [];

            if (!in_array($relationship_info, $path_id)) {
                array_push($path_id, $relationship_info);
            }
        } 

        if (empty($full_name)) {
            $this->responseJson([MESSAGE => 'Vui lòng nhập thông tin Họ và tên']);
        }

        if (empty($relationship)) {
            $relationship_info = $relationship_position = null;
        }

        // láy thông tin thế hệ thứ bao nhiêu
        // mặc định nếu không có thì là thế hệ thứ nhất
        $generation = 1;
        if (!empty($relationship_info)) {
            $generation = $table->getGenerationById($relationship_info);
        }

        $data_save = [
            'image_avatar' => !empty($data['image_avatar']) ? $data['image_avatar'] : null,
            'full_name' => $full_name,
            'self_name' => !empty($data['self_name']) ? $data['self_name'] : null,
            'education_level' => isset($data['education_level']) ? intval($data['education_level']) : null,
            'status' => isset($data['status']) ? intval($data['status']) : null,
            'sex' => !empty($data['sex']) ? $data['sex'] : null,
            'relationship' => $relationship,
            'relationship_info' => $relationship_info,
            'relationship_position' => $relationship_position,
            'path_id' => !empty($path_id) ? '|' . implode('|', array_filter($path_id, 'strlen')) . '|' : null,
            'generation' => $generation,
            'genealogical' => isset($data['genealogical']) ? intval($data['genealogical']) : null,
            'description' => !empty($data['description']) ? trim($data['description']) : null,
            'content' => !empty($data['content']) ? trim($data['content']) : null,
            'birthday' => !empty($data['birthday']) ? $this->Utilities->stringDateClientToInt(trim($data['birthday'])) : null,
            'nam_mat' => !empty($data['nam_mat']) ? $this->Utilities->stringDateClientToInt(trim($data['nam_mat'])) : null,
            'age' => null,
            'year_of_birth' => !empty($data['year_of_birth']) ? $data['year_of_birth'] : null,
            'year_of_death' => !empty($data['year_of_death']) ? $data['year_of_death'] : null,
            'burial' => !empty($data['burial']) ? $data['burial'] : null,
            'city_id' => !empty($data['city_id']) ? intval($data['city_id']) : null,
            'district_id' => !empty($data['district_id']) ? intval($data['district_id']) : null,
            'search_unicode' => strtolower($utilities->formatSearchUnicode([$full_name, $self_name]))
        ];

        if (!empty($data_save['birthday']) && !empty($data_save['nam_mat'])) {
            $age = (intval($data_save['nam_mat']) - intval($data_save['birthday'])) / (60 * 60 * 24 * 360);
            $data_save['age'] = !empty($age) ? $age : null;
        }

        if (!empty($data_relationship_info) && !empty($relationship_info) && $data_relationship_info != $relationship_info) {
            $update_path_id = true;
        }

        // merge data with entity 
        if(empty($id)){
            $genealogy = $table->newEntity($data_save, ['validate' => false]);
        }else{            
            $genealogy = $table->patchEntity($genealogy, $data_save);
        }

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();
            
            $save = $table->save($genealogy);
            if (empty($save->id)){
                throw new Exception();
            }

            if ($update_path_id) {
                $save_path = $this->savePathId($save->id, $path_id);
                if (!$save_path){
                    throw new Exception();
                }
            }

            $conn->commit();
            $this->responseJson([CODE => SUCCESS, DATA => ['id' => $save->id]]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function savePathId($id = null, $path_id = null)
    {
        if (empty($id)) return true;

        $table = TableRegistry::get('Genealogies');

        $genealogies = $table->find()->where([
            'path_id LIKE' => '%|' . $id . '|%'
        ])->toList();

        if (empty($genealogies)) return true;

        $data_save = [];
        foreach ($genealogies as $key => $genealogy) {
            $genealogy_id = !empty($genealogy['id']) ? intval($genealogy['id']) : null;
            $relationship_info = !empty($genealogy['relationship_info']) ? intval($genealogy['relationship_info']) : null;

            if (empty($genealogy_id) || empty($relationship_info)) continue;

            if (!in_array($relationship_info, $path_id)) {
                array_push($path_id, $relationship_info);
            }

            $data_save[] = [
                'id' => $genealogy_id,
                'path_id' => '|' . implode('|', array_filter($path_id, 'strlen')) . '|'
            ];
        }

        $entities = $table->patchEntities($genealogies, $data_save, ['validate' => false]);
        $save_path = $table->saveMany($entities);

        if (empty($save_path)){
            return false;
        }

        return true;
    }

    public function delete()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        $id = !empty($data['id']) ? $data['id'] : null;

        if (!$this->getRequest()->is('post') || empty($id)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('Genealogies');

        $check_child = $table->find()->where([
            'relationship_info' => $id,
            'deleted' => 0
        ])->select(['id'])->first();
        if (!empty($check_child)) {
            $this->responseJson([MESSAGE => __d('admin', 'Vui lòng xóa các cấp con trước!')]);
        }

        $genealogy = $table->find()->where(['id' => $id])->first();
        if (empty($genealogy)) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_ban_ghi')]);
        }

        $entity = $table->patchEntity($genealogy, ['deleted' => 1], ['validate' => false]);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $delete = $table->save($entity);
            if (empty($delete->id)){
                throw new Exception();
            }
            
            $conn->commit();
            $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'cap_nhat_thanh_cong')]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }
}