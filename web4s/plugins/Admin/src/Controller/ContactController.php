<?php

namespace Admin\Controller;

use Admin\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Http\Response;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;
use Cake\Utility\Hash;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ContactController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function list()
    {
        $this->js_page = '/assets/js/pages/list_contact.js';
        $this->set('path_menu', 'contact');
        $this->set('title_for_layout', __d('admin', 'lien_he_cua_khach_hang'));
    }

    public function listJson()
    {
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $table = TableRegistry::get('Contacts');
        $utilities = $this->loadComponent('Utilities');

        $data = $params = [];

        $limit = PAGINATION_LIMIT_ADMIN;
        $page = 1;
        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        // params query
        $params[QUERY] = !empty($data[QUERY]) ? $data[QUERY] : [];
        $params['get_form'] = true;

        // params filter
        $params[FILTER] = !empty($data[DATA_FILTER]) ? $data[DATA_FILTER] : [];
        if(!empty($params[QUERY])){
            $params[FILTER] = array_merge($params[FILTER], $params[QUERY]);
        }

        // page and limit
        $page = !empty($data[PAGINATION][PAGE]) ? intval($data[PAGINATION][PAGE]) : 1;
        $limit = !empty($data[PAGINATION][PERPAGE]) ? intval($data[PAGINATION][PERPAGE]) : PAGINATION_LIMIT_ADMIN;
        
        // sort 
        $params[SORT] = !empty($data[SORT]) ? $data[SORT] : [];
        $sort_field = !empty($params[SORT][FIELD]) ? $params[SORT][FIELD] : null;
        $sort_type = !empty($params[SORT][SORT]) ? $params[SORT][SORT] : null;

        if(!empty($data['export']) && $data['export'] == 'all') {
            $limit = 1000;
            $count_eport_contact = $table->queryListContacts($params)->count();
            if(!empty($count_eport_contact) && $count_eport_contact > $limit){
                $page_number = ceil($count_eport_contact / $limit);
                
                if($page_number < 1)  $page_number = 1;
                $page_export = 0;
                $contact_export = [];
                for ($i = 0; $i < $page_number; $i++) {
                    $page_export ++;
                    $contact_export[] = $table->queryListContacts($params)->limit($limit)->page($page_export)->toArray();
                    if(empty($contact_export)) continue;
                }

                $result_export = [];
                if(!empty($contact_export)){
                    foreach ($contact_export as $export) {  
                        foreach ($export as $k => $contact) {
                            $result_export[] = $table->formatDataContactDetail($contact);
                        }
                    }
                }

                return $this->exportExcel(Hash::combine($result_export, '{n}.id', '{n}', '{n}.form_id'));
            }
        }

        try {
            $contacts = $this->paginate($table->queryListContacts($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        } catch (Exception $e) {
            $contacts = $this->paginate($table->queryListContacts($params), [
                'limit' => $limit,
                'page' => 1,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        }
        $pagination_info = !empty($this->request->getAttribute('paging')['Contacts']) ? $this->request->getAttribute('paging')['Contacts'] : [];
        $meta_info = $utilities->formatPaginationInfo($pagination_info);        
        $result = [];
        if(!empty($contacts)){
            foreach ($contacts as $k => $contact) {                
                $result[$k] = $table->formatDataContactDetail($contact);
                
                $list_fields = !empty($result[$k]['form']['fields']) ? $result[$k]['form']['fields'] : [];
                $list_fields = Hash::combine($list_fields, '{n}.code', '{n}.label');
                $content = [];
                if($list_fields) {
                    foreach($result[$k]['value'] as $label => $value){
                        if(!empty($list_fields[$label])){
                            $content[$list_fields[$label]] = $value;
                        } else {
                            $content[$label] = $value;
                        }
                    }
                }
                $result[$k]['content'] = $content;
            }
        }

        if(!empty($data['export'])) {
            return $this->exportExcel(Hash::combine($result, '{n}.id', '{n}', '{n}.form_id'));
        }

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $result, 
            META => $meta_info
        ]);
    }

    public function detail($id = null)
    {
        $table = TableRegistry::get('Contacts');
        
        $contact = $table->find()->contain(['ContactsForm'])->where(['Contacts.id' => $id, 'Contacts.deleted' => 0])->first();
        $contact_info = $table->formatDataContactDetail($contact);

        if (empty($contact_info)) {
            $this->showErrorPage();
        }
        
        $fields = !empty($contact_info['form']['fields']) ? $contact_info['form']['fields'] : [];
        $fields = Hash::combine($fields, '{n}.code', '{n}.label');
        
        // update status
        $contact_entity = $table->patchEntity($contact, ['status' => 1]);
        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();           
            
            $save = $table->save($contact_entity);
            if (empty($save->id)){
                throw new Exception();
            }

            $conn->commit();
        }catch (Exception $e) {
            $conn->rollback();
        }

        $this->css_page = ['/assets/css/pages/wizard/wizard-4.css'];

        $this->set('contact', $contact_info);
        $this->set('fields', $fields);
        $this->set('path_menu', 'contact');
        $this->set('title_for_layout', __d('admin', 'thong_tin_lien_he'));
    }

    private function exportExcel($data)
    {
        if(empty($data)) return [];

        $spreadsheet = new Spreadsheet();

        foreach(range('A','M') as $columnID) {
            $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
        }

        $arr_alphabet = [];
        foreach(range('A','Z') as $alphabet){
            $arr_alphabet[] = $alphabet;
        }
        $k_export = 0;
        foreach ($data as $export) {
            $k_export ++;
            $sheet_name = "sheet_$k_export";

            if($k_export == 1){
                $$sheet_name = $spreadsheet->getActiveSheet();
                $spreadsheet->getActiveSheet()->setTitle(array_values($export)['0']['form']['name']);
            } else {
                $$sheet_name = $spreadsheet->createSheet();
                $spreadsheet->setActiveSheetIndex($k_export - 1);
                $spreadsheet->getActiveSheet()->setTitle(array_values($export)['0']['form']['name']);
                foreach(range('A','M') as $columnID) {
                    $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
                }
            }
            if(!empty(array_values($export)['0']['form']['fields'])) {
                foreach(array_values($export)['0']['form']['fields'] as $k_field => $field) {
                    $$sheet_name->setCellValue($arr_alphabet[$k_field].'1', $field['label']);              
                }
            }

            $count = 2;
            if(!empty($export)){
                foreach ($export as $cellValue) {
                    foreach (array_values($cellValue['value']) as $k_cell => $cell) {
                        $$sheet_name->setCellValue($arr_alphabet[$k_cell] . $count, $cell);
                    }
                    
                    $count ++;
                }
            }
            
        }
        
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
                'name' => 'thong_tin_lien_he_'. time()
            ]
        ]);

    }

}