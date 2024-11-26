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
use Cake\Database\Schema\TableSchema;
use Cake\Utility\Text;

class AttributeController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function list()
    {
        $all_attribute = [
            TEXT => 'TEXT',
            RICH_TEXT => 'RICH_TEXT',
            NUMERIC => 'NUMERIC',
            SINGLE_SELECT => 'SINGLE_SELECT',
            MULTIPLE_SELECT => 'MULTIPLE_SELECT',
            DATE => 'DATE',
            DATE_TIME => 'DATE_TIME',
            SWITCH_INPUT => 'SWITCH_INPUT',
            SPECICAL_SELECT_ITEM => 'SPECICAL_SELECT_ITEM'
        ];

        $this->js_page = '/assets/js/pages/list_attribute.js';
        $this->set('all_attribute', $all_attribute);
        $this->set('path_menu', 'setting');
        $this->set('title_for_layout', __d('admin', 'thuoc_tinh_mo_rong'));
    }

    public function listJson()
    {
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $table = TableRegistry::get('Attributes');
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

        // page and limit
        $page = !empty($data[PAGINATION][PAGE]) ? intval($data[PAGINATION][PAGE]) : 1;
        $limit = !empty($data[PAGINATION][PERPAGE]) ? intval($data[PAGINATION][PERPAGE]) : PAGINATION_LIMIT_ADMIN;


        // sort 
        $sort_field = !empty($params[SORT][FIELD]) ? $params[SORT][FIELD] : null;
        $sort_type = !empty($params[SORT][SORT]) ? $params[SORT][SORT] : null;

        try {
            $attributes = $this->paginate($table->queryListAttributes($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        } catch (Exception $e) {
            $page = 1;
            $attributes = $this->paginate($table->queryListAttributes($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        }

        $pagination_info = !empty($this->request->getAttribute('paging')['Attributes']) ? $this->request->getAttribute('paging')['Attributes'] : [];
        $meta_info = $utilities->formatPaginationInfo($pagination_info);

        $result = [];
        if(!empty($attributes)){
            $list_type = [
                PRODUCT => __d('admin', 'san_pham'),
                PRODUCT_ITEM => __d('admin', 'phien_ban_san_pham'),
                ARTICLE => __d('admin', 'bai_viet'),
                CATEGORY => __d('admin', 'danh_muc')
            ];

            $list_input_type = [
                TEXT => 'TEXT',
                RICH_TEXT => 'RICH_TEXT',
                NUMERIC => 'NUMERIC',
                SINGLE_SELECT => 'SINGLE_SELECT',
                MULTIPLE_SELECT => 'MULTIPLE_SELECT',
                DATE => 'DATE',
                SWITCH_INPUT => 'SWITCH_INPUT',
                SPECICAL_SELECT_ITEM => 'SPECICAL_SELECT_ITEM',
            ];

            $table_options = TableRegistry::get('AttributesOptions');
            $languages = TableRegistry::get('Languages')->getList();
            foreach ($attributes as $key => $attribute) {
                $attribute_id = !empty($attribute['id']) ? intval($attribute['id']) : null;
                $input_type = !empty($attribute['input_type']) ? $attribute['input_type'] : null;
                $number_options = $table_options->find()->where(['AttributesOptions.attribute_id' => $attribute_id])->select(['AttributesOptions.id'])->count();
                $has_option = 0;
                if(in_array($input_type, [SINGLE_SELECT, MULTIPLE_SELECT, SPECICAL_SELECT_ITEM])){
                    $has_option = 1;
                }
                $result[$key] = $attribute;
                $result[$key]['attribute_type_name'] = !empty($list_type[$attribute['attribute_type']]) ? $list_type[$attribute['attribute_type']] : null;
                $result[$key]['input_type_name'] = !empty($list_input_type[$attribute['input_type']]) ? $list_input_type[$attribute['input_type']] : null;                    
                $result[$key]['number_options'] = !empty($number_options) ? $number_options : 0;
                $result[$key]['has_option'] = $has_option;

                $mutiple_language = [];
                if(!empty($languages)){
                    foreach($languages as $lang => $language){
                        if($lang == $this->lang && !empty($attribute['attribute_type_name'])){
                            $mutiple_language[$lang] = true;
                        }else{
                            $content = TableRegistry::get('AttributesContent')->find()->where([
                                'attribute_id' => !empty($attribute['id']) ? intval($attribute['id']) : null,
                                'lang' => $lang
                            ])->select(['name'])->first();

                            $mutiple_language[$lang] = !empty($content['name']) ? true : false;
                        }                        
                    }
                }

                $result[$key]['mutiple_language'] = $mutiple_language;
            }
        }

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
            '/assets/js/pages/attribute.js',
        ];

        $this->set('path_menu', 'setting');
        $this->set('title_for_layout', __d('admin', 'them_thuoc_tinh'));
        $this->render('update');
    }

    public function update($id = null)
    {
        $table = TableRegistry::get('Attributes');
    
        $attribute = $table->find()->contain([
            'ContentMutiple', 
            'User'
        ])->where([
            'Attributes.id' => $id, 
            'Attributes.deleted' => 0
        ])->first();

        if(empty($attribute)){
            $this->showErrorPage();
        }
        $attribute['ContentMutiple'] =  Hash::combine($attribute['ContentMutiple'], '{n}.lang', '{n}.name');

        $this->set('id', $id);
        $this->set('attribute', $attribute);

        $this->js_page = [
            '/assets/js/pages/attribute.js',
        ];

        $this->set('path_menu', 'setting');
        $this->set('title_for_layout', __d('admin', 'cap_nhat_thuoc_tinh'));
    }
 
    public function save($id = null)
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $utilities = $this->loadComponent('Utilities');
        $table = TableRegistry::get('Attributes');

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

            $exist_name = $table->checkExistName($name, $this->lang, $id);
            if($exist_name){
                $this->responseJson([MESSAGE => __d('admin', 'ten_thuoc_tinh_da_ton_tai_tren_he_thong')]);
            }
        }

        $attribute_type = !empty($data['attribute_type']) ? $data['attribute_type'] : null;
        $input_type = !empty($data['input_type']) ? $data['input_type'] : null;

        if(empty($attribute_type)){
            $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_loai_thuoc_tinh')]);
        }

        if(empty($input_type)){
            $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_loai_input_thuoc_tinh')]);
        }

        if($input_type == SPECICAL_SELECT_ITEM && $attribute_type != PRODUCT_ITEM){
            $this->responseJson([MESSAGE => __d('admin', 'kieu_input_{0}_chi_ap_dung_voi_loai_thuoc_tinh_phien_ban_san_pham', ['SPECICAL_SELECT_ITEM'])]);
        }

        if(empty($data['code'])){
            $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_ma_thuoc_tinh')]);
        }

        $code = Text::slug(strtolower($data['code']), '');

        $has_image = !empty($data['has_image']) ? 1 : 0;        
        if($input_type != SPECICAL_SELECT_ITEM){
            $has_image = null;
        }

        if(empty($code)){
            $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_ma_thuoc_tinh')]);
        }

        // check exist code
        $where_check = ['code' => $code, 'deleted' => 0];
        if(!empty($id)){
            $where_check['id !='] = $id;
        }

        $check_code = $table->find()->where($where_check)->first();
        if(!empty($check_code)){
            $this->responseJson([MESSAGE => __d('admin', 'ma_thuoc_tinh_da_ton_tai_tren_he_thong')]);
        }

        // check code same field table system
        $collection = ConnectionManager::get('default')->getSchemaCollection();
        switch ($attribute_type) {
            case PRODUCT:
                $products_schema = $collection->describe('products')->columns();
                $products_content_schema = $collection->describe('products_content')->columns();

                if(in_array($code, $products_schema) || in_array($code, $products_content_schema)){
                    $this->responseJson([MESSAGE => __d('admin', 'ma_thuoc_tinh_khong_hop_le')]);
                }
            break;

            case PRODUCT_ITEM:
                $products_item_schema = $collection->describe('products')->columns();

                if(in_array($code, $products_item_schema)){
                    $this->responseJson([MESSAGE => __d('admin', 'ma_thuoc_tinh_khong_hop_le')]);
                }
            break;

            case ARTICLE:
                $articles_schema = $collection->describe('articles')->columns();
                $articles_content_schema = $collection->describe('articles_content')->columns();

                if(in_array($code, $articles_schema) || in_array($code, $articles_content_schema)){
                    $this->responseJson([MESSAGE => __d('admin', 'ma_thuoc_tinh_khong_hop_le')]);
                }
            break;
        }
        

        if(!empty($id)){
            $attribute = $table->find()->contain(['ContentMutiple'])->where([
                'Attributes.id' => $id, 
                'Attributes.deleted' => 0
            ])->first();

            if(empty($attribute)){
                $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_thuoc_tinh')]);
            }
        }

        // format data before save        
        $data_save = [
            'code' => $code,
            'attribute_type' => $attribute_type,
            'input_type' => $input_type,
            'has_image' => $has_image,
            'required' => !empty($data['required']) ? 1 : 0
        ];

        $data_save['ContentMutiple'] = [];
        foreach ($data['name'] as $lang => $name) {
            $data_save['ContentMutiple'][] = [
                'name' => $name,
                'lang' => $lang,
                'search_unicode' => strtolower($utilities->formatSearchUnicode([$name]))
            ];
        }

        // merge data with entity 
        if(empty($id)){
            $max_record = $table->find()->select('id')->max('id');
            $data_save['position'] = !empty($max_record['id']) ? intval($max_record['id']) + 1 : 1;
            $data_save['created_by'] = $this->Auth->user('id');
            
            $attribute = $table->newEntity($data_save, [
                'associated' => ['ContentMutiple']
            ]);

        }else{            
            $attribute = $table->patchEntity($attribute, $data_save);
        }

        // show error validation in model
        if($attribute->hasErrors()){
            $list_errors = $utilities->errorModel($attribute->getErrors());            
            $this->responseJson([
                MESSAGE => !empty($list_errors[0]) ? $list_errors[0] : null,
                DATA => $list_errors
            ]);             
        }

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            // clear attribute content
            if(!empty($id)){
                $clear_content = TableRegistry::get('AttributesContent')->deleteAll(['attribute_id' => $id]);    
            }

            $save = $table->save($attribute);

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

        $table = TableRegistry::get('Attributes');

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            foreach($ids as $id){
                $attribute = $table->get($id);
                if (empty($attribute)) {
                    throw new Exception(__d('admin', 'khong_lay_duoc_thong_tin_thuoc_tinh'));
                }

                $attribute = $table->patchEntity($attribute, ['id' => $id, 'deleted' => 1], ['validate' => false]);
                $delete = $table->save($attribute);
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

        $table = TableRegistry::get('Attributes');
        $attribute = $table->get($id);
        if(empty($attribute)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_thuoc_tinh')]);
        }
        
        $attribute = $table->patchEntity($attribute, ['position' => $value], ['validate' => false]);
        try{
            $save = $table->save($attribute);

            if (empty($save->id)){
                throw new Exception();
            }
            $this->responseJson([CODE => SUCCESS, DATA => ['id' => $save->id]]);

        }catch (Exception $e) {
            $this->responseJson([MESSAGE => $e->getMessage()]);
        }
    }

    public function getListInput()
    {

        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        $attribute_type = !empty($data['attribute_type']) ? $data['attribute_type'] : null;

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }
        
        if (empty($attribute_type)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        
        $result = Configure::read('LIST_ATTRIBUTE_NORMAL');

        if($attribute_type == PRODUCT_ITEM){
            $result = Configure::read('ATTRIBUTE_PRODUCT_ITEM');
        }

        $this->responseJson([
            CODE => SUCCESS, 
            DATA => $result
        ]);
    }


}