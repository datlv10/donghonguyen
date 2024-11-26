<?php

namespace Admin\Controller;

use Admin\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Http\Response;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;


class ContactFormController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function list()
    {
        $this->js_page = '/assets/js/pages/list_contact_form.js';
        $this->set('path_menu', 'setting');
        $this->set('title_for_layout', __d('admin', 'form_lien_he'));
    }

    public function listJson()
    {
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $table = TableRegistry::get('ContactsForm');
        $utilities = $this->loadComponent('Utilities');

        $data = $params = [];

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

        // page and limit
        $page = !empty($data[PAGINATION][PAGE]) ? intval($data[PAGINATION][PAGE]) : 1;
        $limit = !empty($data[PAGINATION][PERPAGE]) ? intval($data[PAGINATION][PERPAGE]) : PAGINATION_LIMIT_ADMIN;
        
        // sort 
        $params[SORT] = !empty($data[SORT]) ? $data[SORT] : [];
        $sort_field = !empty($params[SORT][FIELD]) ? $params[SORT][FIELD] : null;
        $sort_type = !empty($params[SORT][SORT]) ? $params[SORT][SORT] : null;
        
        try {
            $result = $this->paginate($table->queryListContactsForm($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        } catch (Exception $e) {
            $result = $this->paginate($table->queryListContactsForm($params), [
                'limit' => $limit,
                'page' => 1,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        }

        $pagination_info = !empty($this->request->getAttribute('paging')['Payments']) ? $this->request->getAttribute('paging')['Payments'] : [];
        $meta_info = $utilities->formatPaginationInfo($pagination_info);

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $result, 
            META => $meta_info
        ]);
    }

    public function add()
    {
        $this->js_page = [
            '/assets/js/pages/contact_form.js',
        ];

        $this->set('path_menu', 'setting');
        $this->set('title_for_layout', __d('admin', 'them_form_lien_he'));
        $this->render('update');
    }

    public function update($id = null)
    {
        $form = TableRegistry::get('ContactsForm')->find()->where(['id' => $id, 'deleted' => 0])->first();
        if(empty($form)){
            return $this->redirect(ADMIN_PATH . '/404');
        }
        
        $form['fields'] = !empty($form['fields']) ? json_decode($form['fields'], true) : [];

        $this->set('path_menu', 'setting');
        $this->set('id', $id);      
        $this->set('form', $form);

        $this->js_page = [
            '/assets/js/pages/contact_form.js',
        ];
        $this->set('title_for_layout', __d('admin', 'cap_nhat_form_lien_he'));
    }

    public function save($id = null)
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
        
        $utilities = $this->loadComponent('Utilities');        
        $table = TableRegistry::get('ContactsForm');

        if(!empty($id)){
            $form = $table->find()->where(['id' => $id, 'deleted' => 0])->first();
            if(empty($form)){
                $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
            }
        }

        // validate data
        if(empty($data['name'])){
            $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_ten_form')]);
        }

        if(empty($data['fields']) || !is_array($data['fields'])){
            $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_day_du_cac_truong_thong_tin')]);
        }

        foreach ($data['fields'] as $key => $field) {
            if(empty($field['code'])){
                $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_day_du_cac_truong_thong_tin')]);
            }

            if(empty($field['label'])){
                $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_day_du_cac_truong_thong_tin')]);
            }
        }

        $code = !empty($data['code']) ? trim($data['code']) : $utilities->generateRandomString();
        $data_save = [
            'name' => !empty($data['name']) ? trim($data['name']) : null,
            'code' => $code,
            'send_email' => !empty($data['send_email']) ? 1 : 0,
            'template_email_code' => !empty($data['template_email_code']) ? $data['template_email_code'] : null,
            'fields' => json_encode($data['fields']),
            'search_unicode' => strtolower($utilities->formatSearchUnicode([$data['name'], $code]))
        ];

        // merge data with entity 
        if(empty($id)){
            $data_save['created_by'] = $this->Auth->user('id');
            $form = $table->newEntity($data_save);
        }else{            
            $form = $table->patchEntity($form, $data_save);
        }

        // show error validation in model
        if($form->hasErrors()){
            $list_errors = $utilities->errorModel($form->getErrors());
            
            $this->responseJson([
                MESSAGE => !empty($list_errors[0]) ? $list_errors[0] : null,
                DATA => $list_errors
            ]);             
        }

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();           
            
            $save = $table->save($form);
            if (empty($save->id)){
                throw new Exception();
            }

            $conn->commit();
            $this->responseJson([CODE => SUCCESS, DATA => ['id' => $save->id]]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
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

        $table = TableRegistry::get('ContactsForm');

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            foreach($ids as $id){
                $form = $table->find()->where(['id' => $id])->first();
                if (empty($form)) {
                    throw new Exception(__d('admin', 'khong_tim_thay_thong_tin_form'));
                }

                $form = $table->patchEntity($form, ['deleted' => 1], ['validate' => false]);
                $delete = $table->save($form);
                if (empty($delete)){
                    throw new Exception();
                }

            }

            $conn->commit();
            $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'xoa_du_lieu_thanh_cong')]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

}