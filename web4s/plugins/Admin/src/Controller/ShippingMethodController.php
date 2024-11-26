<?php

namespace Admin\Controller;

use Admin\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Http\Response;
use Cake\ORM\Query;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;


class ShippingMethodController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function list()
    {
        $this->js_page = [
            '/assets/js/pages/list_shipping_method.js',
        ];

        $this->set('path_menu', 'setting');
        $this->set('title_for_layout', __d('admin', 'phuong_thuc_van_chuyen'));
    }

    public function listJson()
    {
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $table = TableRegistry::get('ShippingsMethod');
        $utilities = $this->loadComponent('Utilities');

        $data = $params = $shippings_method = [];

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


        // sort 
        $sort_field = !empty($params[SORT][FIELD]) ? $params[SORT][FIELD] : null;
        $sort_type = !empty($params[SORT][SORT]) ? $params[SORT][SORT] : null;

        try {
            $shippings_method = $this->paginate($table->queryListShippingsMethod($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        } catch (Exception $e) {
            $page = 1;
            $shippings_method = $this->paginate($table->queryListShippingsMethod($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        }

        // parse data before output
        $result = [];
        if(!empty($shippings_method)){
            $languages = TableRegistry::get('Languages')->getList();
            foreach($shippings_method as $k => $method){
                $result[$k] = $table->formatDataShippingMethodDetail($method, $this->lang);
                
                // check multiple language
                $mutiple_language = [];                
                if(!empty($languages)){
                    foreach($languages as $lang => $language){
                        if($lang == $this->lang && !empty($method['name'])){
                            $mutiple_language[$lang] = true;

                        }else{
                            $content = TableRegistry::get('ShippingsMethodContent')->find()->where([
                                'shipping_method_id' => !empty($method['id']) ? intval($method['id']) : null,
                                'lang' => $lang
                            ])->select(['name'])->first();
                            
                            $mutiple_language[$lang] = !empty($content['name']) ? true : false;
                        }                        
                    }
                }


                $result[$k]['mutiple_language'] = $mutiple_language;
            }
        }

        $pagination_info = !empty($this->request->getAttribute('paging')['ShippingsMethod']) ? $this->request->getAttribute('paging')['ShippingsMethod'] : [];
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
        $max_record = TableRegistry::get('ShippingsMethod')->find()->select('id')->max('id');

        $this->set('position', !empty($max_record->id) ? $max_record->id + 1 : 1);

        $this->js_page = [
            '/assets/plugins/custom/tinymce6/tinymce.min.js',
            '/assets/js/pages/shipping_method.js'
        ];

        $this->set('path_menu', 'setting');
        $this->set('title_for_layout', __d('admin', 'them_phuong_thuc_van_chuyen'));
        $this->render('update');
    }

    public function update($id = null)
    {
        $shipping_method = TableRegistry::get('ShippingsMethod')->find()->contain(['ContentMutiple'])->where([
            'ShippingsMethod.id' => $id,
            'ShippingsMethod.deleted' => 0,
        ])->first();

        if(empty($shipping_method)){
            $this->showErrorPage();
        }

        $shipping_method['custom_config'] = !empty($shipping_method['custom_config']) ? json_decode($shipping_method['custom_config'], true) : [];
        $content = !empty($shipping_method['ContentMutiple']) ? Hash::combine($shipping_method['ContentMutiple'], '{n}.lang', '{n}') : [];
        
        $this->set('id', $id);
        $this->set('shipping_method', $shipping_method);
        $this->set('content', $content);
        $this->set('path_menu', 'setting');
        $this->js_page = [
            '/assets/plugins/custom/tinymce6/tinymce.min.js',
            '/assets/js/pages/shipping_method.js'
        ];
        $this->set('title_for_layout', __d('admin', 'cap_nhat_phuong_thuc_van_chuyen'));
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
        $table = TableRegistry::get('ShippingsMethod');        

        if(!empty($id)){
            $shipping_method = $table->getDetailShippingMethod($id, $this->lang);
            if(empty($shipping_method)){
                $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
            }
        }

        // validate data
        if(empty($data['name']) || !is_array($data['name'])){
            $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_ten_phuong_thuc')]);
        }

        $data_content = [];
        foreach($data['name'] as $k_lang => $name){
            $name = trim(strip_tags($name));
            if(empty($name)) continue;
            $description = !empty($data['description'][$k_lang]) ? $data['description'][$k_lang] : null;
            $data_content[] = [
                'id' => !empty($data['content_id'][$k_lang]) ? $data['content_id'][$k_lang] : null,
                'shipping_method_id' => $id,
                'name' => $name,
                'description' => $description,
                'lang' => $k_lang,
                'search_unicode' => strtolower($utilities->formatSearchUnicode([$name]))
            ];
        }

        if(empty($data_content)){
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $data_save = [
            'general_shipping_fee' => !empty($data['general_shipping_fee']) ? intval(str_replace(',', '', $data['general_shipping_fee'])) : null,
            'type_fee' => !empty($data['type_fee']) ? $data['type_fee'] : null,
            'custom_config' => !empty($data['custom_config']) ? $data['custom_config'] : null,            
            'position' => !empty($data['position']) ? intval($data['position']) : 1,
            'status' => 1,
            'ContentMutiple' => $data_content
        ];

        // merge data with entity 
        if(empty($id)){
            $entity = $table->newEntity($data_save, [
                'associated' => ['ContentMutiple']
            ]);
        }else{            
            $entity = $table->patchEntity($shipping_method, $data_save);
        }

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();
    
            $save = $table->save($entity);
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

        if (!$this->getRequest()->is('post') || empty($ids) || !is_array($ids)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('ShippingsMethod');

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            foreach($ids as $id){
                $shipping_method = $table->find()->where(['id' => $id, 'deleted' => 0])->first();
                if (empty($shipping_method)) {
                    throw new Exception(__d('admin', 'khong_tim_thay_thong_tin_bai_viet'));
                }

                $entity = $table->patchEntity($shipping_method, ['id' => $id, 'deleted' => 1]);
                $delete = $table->save($entity);
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

        $table = TableRegistry::get('ShippingsMethod');

        $shippings_method = $table->find()->where([
            'ShippingsMethod.id IN' => $ids,
            'ShippingsMethod.deleted' => 0
        ])->select(['ShippingsMethod.id', 'ShippingsMethod.status'])->toArray();
        
        if(empty($shippings_method)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_ban_ghi')]);
        }

        $patch_data = [];
        foreach ($ids as $k => $method_id) {
            $patch_data[] = [
                'id' => $method_id,
                'status' => $status,
                'draft' => 0
            ];
        }

        $entities = $table->patchEntities($shippings_method, $patch_data);

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

        $table = TableRegistry::get('ShippingsMethod');
        $shipping_method = $table->find()->where(['id' => $id, 'deleted' => 0])->first();
        if(empty($shipping_method)) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
        }

        $entity = $table->patchEntity($shipping_method, ['position' => $value]);

        try{
            $save = $table->save($entity);

            if (empty($save->id)){
                throw new Exception();
            }
            $this->responseJson([CODE => SUCCESS, DATA => ['id' => $save->id]]);

        }catch (Exception $e) {
            $this->responseJson([MESSAGE => $e->getMessage()]);
        }
    }
}