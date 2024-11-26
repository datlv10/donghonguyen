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

class AttributeOptionController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function list($attribute_id = null)
    {
        if(empty($attribute_id)){
            $this->showErrorPage();
        }

        $attribute_info = TableRegistry::get('Attributes')->getDetailAttribute($attribute_id, $this->lang);
        if (empty($attribute_info)) {
            $this->showErrorPage();
        }
        $name = !empty($attribute_info['AttributesContent']['name']) ? $attribute_info['AttributesContent']['name'] : null;

        $this->js_page = '/assets/js/pages/list_attribute_option.js';
        $this->set('attribute_id', $attribute_id);
        $this->set('path_menu', 'setting');
        $this->set('title_for_layout', __d('admin', 'tuy_chon_cua_thuoc_tinh').': ' . $name);
    }

    public function listJson($attribute_id = null)
    {
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        if (empty($attribute_id)) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_thuoc_tinh')]);
        }

        $attribute_info = TableRegistry::get('Attributes')->find()->where([
            'Attributes.id' => $attribute_id, 
            'Attributes.deleted' => 0
        ])->first();
        if (empty($attribute_info)) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_thuoc_tinh')]);
        }

        $table = TableRegistry::get('AttributesOptions');
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
        $params[FILTER]['attribute_id'] = $attribute_id;

        $params[FILTER][LANG] = !empty($params[FILTER][LANG]) ? $params[FILTER][LANG] : TableRegistry::get('Languages')->getDefaultLanguage();

        // params         
        $params[SORT] = !empty($data[SORT]) ? $data[SORT] : [];
        $params['get_user'] = true;

        // page and limit
        $page = !empty($data[PAGINATION][PAGE]) ? intval($data[PAGINATION][PAGE]) : 1;
        $limit = !empty($data[PAGINATION][PERPAGE]) ? intval($data[PAGINATION][PERPAGE]) : PAGINATION_LIMIT_ADMIN;


        // sort 
        $sort_field = !empty($params[SORT][FIELD]) ? $params[SORT][FIELD] : null;
        $sort_type = !empty($params[SORT][SORT]) ? $params[SORT][SORT] : null;

        try {
            $options = $this->paginate($table->queryListAttributesOptions($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        } catch (Exception $e) {
            $options = $this->paginate($table->queryListAttributesOptions($params), [
                'limit' => $limit,
                'page' => 1,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        }

        $pagination_info = !empty($this->request->getAttribute('paging')['AttributesOptions']) ? $this->request->getAttribute('paging')['AttributesOptions'] : [];
        $meta_info = $utilities->formatPaginationInfo($pagination_info);

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $options, 
            META => $meta_info
        ]);
    }

    public function add($attribute_id = null)
    {
        if(empty($attribute_id)){
            $this->showErrorPage();
        }

        $attribute_info = TableRegistry::get('Attributes')->getDetailAttribute($attribute_id, $this->lang);
        if (empty($attribute_info)) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_thuoc_tinh')]);
        }
        $name = !empty($attribute_info['AttributesContent']['name']) ? $attribute_info['AttributesContent']['name'] : null;

        $this->js_page = [
            '/assets/js/pages/attribute_option.js',
        ];

        $this->set('attribute_id', $attribute_id);
        $this->set('path_menu', 'setting');
        $this->set('title_for_layout', __d('admin', 'them_tuy_chon_thuoc_tinh') . ': ' . $name);
        $this->render('update');
    }

    public function update($attribute_id = null, $option_id = null)
    {
        if(empty($attribute_id)){
            $this->showErrorPage();
        }

        $attribute_info = TableRegistry::get('Attributes')->getDetailAttribute($attribute_id, $this->lang);
        if (empty($attribute_info)) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_thuoc_tinh')]);
        }
        $name = !empty($attribute_info['AttributesContent']['name']) ? $attribute_info['AttributesContent']['name'] : null;


        $option = TableRegistry::get('AttributesOptions')->find()->contain([
            'ContentMutiple'
        ])->where([
            'AttributesOptions.id' => $option_id,
            'AttributesOptions.attribute_id' => $attribute_id,
            'AttributesOptions.deleted' => 0
        ])->first();

        if(empty($option)){
            $this->showErrorPage();
        }
        $option['ContentMutiple'] =  Hash::combine($option['ContentMutiple'], '{n}.lang', '{n}.name');;

        $this->set('id', $option_id);
        $this->set('option_id', $option_id);
        $this->set('attribute_id', $attribute_id);
        $this->set('option', $option);

        $this->js_page = [
            '/assets/js/pages/attribute_option.js',
        ];

        $this->set('path_menu', 'setting');
        $this->set('title_for_layout', __d('admin', 'cap_nhat_tuy_chon'). ': ' . $name);
    }

    public function save($attribute_id = null, $option_id = null)
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $utilities = $this->loadComponent('Utilities');
        $table = TableRegistry::get('AttributesOptions');

        // validate data
        if (empty($data)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        if(empty($data['name']) || !is_array($data['name'])){
            $this->responseJson([MESSAGE => __d('admin', 'ten_thuoc_tinh_khong_hop_le')]);
        }

        foreach ($data['name'] as $lang => $name) {
            if(empty($name)){
                $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_ten_thuoc_tinh')]);
            }
        }

        if(empty($data['code'])){
            $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_ma_thuoc_tinh')]);
        }        

        if(empty($attribute_id)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_thuoc_tinh')]);
        }

        $attribute_info = TableRegistry::get('Attributes')->getDetailAttribute($attribute_id, $this->lang);
        if (empty($attribute_info)) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_thuoc_tinh')]);
        }

        if(!empty($option_id)){
            $option = $table->find()->contain(['ContentMutiple'])->where([
                'AttributesOptions.id' => $option_id,
                'AttributesOptions.attribute_id' => $attribute_id,
                'AttributesOptions.deleted' => 0
            ])->first();
            if(empty($option)){
                $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_tuy_chon')]);
            }
        }
       
        // format data before save 
        $code = !empty($data['code']) ? preg_replace('/[^A-Za-z0-9_]/', '', $utilities->formatUnicode(strtolower(trim($data['code'])))) : null;
        $data_save = [
            'attribute_id' => $attribute_id,
            'code' => $code
        ];

        $data_save['ContentMutiple'] = [];
        foreach ($data['name'] as $lang => $name) {
            $data_save['ContentMutiple'][] = [
                'name' => $name,
                'lang' => $lang,
                'search_unicode' => strtolower($utilities->formatSearchUnicode([$name]))
            ];
        }

        if(empty($option_id)){
            $number_record = $table->find()->where(['attribute_id' => $attribute_id])->select('id')->count();        
            $data_save['position'] = !empty($number_record) ? intval($number_record) + 1 : 1;
        }

        // merge data with entity 
        if(empty($option_id)){
            $option = $table->newEntity($data_save, [
                'associated' => ['ContentMutiple']
            ]);
        }else{            
            $option = $table->patchEntity($option, $data_save);
        }
        
        // show error validation in model
        if($option->hasErrors()){
            $list_errors = $utilities->errorModel($option->getErrors());            
            $this->responseJson([
                MESSAGE => !empty($list_errors[0]) ? $list_errors[0] : null,
                DATA => $list_errors
            ]);             
        }

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();
            if(!empty($option_id)){
                $clear_content = TableRegistry::get('AttributesOptionsContent')->deleteAll(['attribute_option_id' => $option_id]);    
            }

            $save = $table->save($option);
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

        $table = TableRegistry::get('AttributesOptions');

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            foreach($ids as $id){
                $option = $table->get($id);                
                if (empty($option)) {
                    throw new Exception(__d('admin', 'khong_lay_duoc_thong_tin_thuoc_tinh'));
                }

                $option = $table->patchEntity($option, ['id' => $id, 'deleted' => 1], ['validate' => false]);                
                $delete = $table->save($option);
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

    public function changePosition()
    {
        $this->layout = false;
        $this->autoRender = false;

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        $id = !empty($data['id']) ? intval($data['id']) : null;
        $value = !empty($data['value']) ? $data['value'] : 0;

        if (empty($id)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('AttributesOptions');
        $option = $table->get($id);
        if(empty($option)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_thuoc_tinh')]);
        }
        
        $option = $table->patchEntity($option, ['position' => $value], ['validate' => false]);
        try{
            $save = $table->save($option);

            if (empty($save->id)){
                throw new Exception();
            }
            $this->responseJson([CODE => SUCCESS, DATA => ['id' => $save->id]]);

        }catch (Exception $e) {
            $this->responseJson([MESSAGE => $e->getMessage()]);
        }
    }
}