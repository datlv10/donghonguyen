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

class ObjectController extends AppController {

    public function initialize(): void
    {
        parent::initialize(); 
    }

    public function list($type = null) 
    {
        $this->js_page = '/assets/js/pages/list_order_source.js';

        $this->set('path_menu', 'setting');
        $this->set('title_for_layout', __d('admin', 'nguon_don_hang'));
    }

    public function listJson()
    {

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $table = TableRegistry::get('Objects');
        $utilities = $this->loadComponent('Utilities');
      
        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        
        // params query
        $params[QUERY] = !empty($data[QUERY]) ? $data[QUERY] : [];

        // params filter
        $params[FILTER] = !empty($data[DATA_FILTER]) ? $data[DATA_FILTER] : [];
        if(!empty($params[QUERY])){
            $params[FILTER] = array_merge($params[FILTER], $params[QUERY]);
        }

        // params sort
        $params[SORT] = !empty($data[SORT]) ? $data[SORT] : [];

        // page and limit
        $page = !empty($data[PAGINATION][PAGE]) ? intval($data[PAGINATION][PAGE]) : 1;
        $limit = !empty($data[PAGINATION][PERPAGE]) ? intval($data[PAGINATION][PERPAGE]) : PAGINATION_LIMIT_ADMIN;

        // sort 
        $sort_field = !empty($params[SORT][FIELD]) ? $params[SORT][FIELD] : null;
        $sort_type = !empty($params[SORT][SORT]) ? $params[SORT][SORT] : null;

        try {
            $order_source = $this->paginate($table->queryListObjects($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
            
        
        } catch (Exception $e) {
            $page = 1;
            $order_source = $this->paginate($table->queryListObjects($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        }

        $pagination_info = !empty($this->request->getAttribute('paging')['Objects']) ? $this->request->getAttribute('paging')['Objects'] : [];
        $meta_info = $utilities->formatPaginationInfo($pagination_info);

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $order_source, 
            META => $meta_info
        ]);
    }

    public function isDefault()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        $id = !empty($data['id']) ? intval($data['id']) : null;

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        if (empty($id)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }
        
        $data['is_default'] = 1;

        $table = TableRegistry::get('Objects');    
        $language = $table->get($id);
        $language = $table->patchEntity($language, $data);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $table->updateAll(
                [  
                    'is_default' => 0
                ],
                [  
                    'is_default' => 1
                ]
            );

            $save = $table->save($language);
            if(empty($save->id)) {
                throw new Exception();
            }
            
            $conn->commit();
            $this->responseJson([CODE => SUCCESS, DATA => ['id' => $save->id]]);

        }catch (Exception $e) {
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function save($id = null)
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        if (empty($data)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $utilities = $this->loadComponent('Utilities');
        $objects_table = TableRegistry::get('Objects'); 
        
        $type = 'order_source';
        $data_save = [
            'name' => !empty($data['name']) ? $data['name'] : null,
            'type' => $type,
            'code' => !empty($data['code']) ? $data['code'] : null,
        ];

        // merge data with entity  
        if(empty($id)){
            $object = $objects_table->newEntity($data_save);
        }else{
            $object = $objects_table->get($id);
            $object = $objects_table->patchEntity($object, $data_save);
        }   

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            // save data
            $save = $objects_table->save($object);
            if (empty($save->id)){
                throw new Exception();
            }

            if(empty($save['code'])){
                $object = $objects_table->get($save->id);
                $code = strtolower($utilities->formatSearchUnicode([$data['name']]));
                $code = str_replace(' ', '_', $code);
                $object = $objects_table->patchEntity($object, ['code' => $code]);
                $update_code = $objects_table->save($object);
                if (empty($update_code->id)){
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

    public function delete()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        $id = !empty($data['ids']) ? $data['ids'][0] : null;

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        if (empty($id)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $objects_table = TableRegistry::get('Objects');
        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();
      
            // get info object and delete object
            $object = $objects_table->get($id);     

            if($object['is_system'] == 1 || $object['is_default'] == 1){
                $this->responseJson([MESSAGE => __d('admin', 'khong_cho_phep_xoa_nguon_don_hang_nay')]);
            }

            $data_object = $objects_table->patchEntity($object, ['deleted' => 1]);
            $delete_object = $objects_table->save($data_object);
            if (empty($delete_object)){
                throw new Exception();
            }     

            $conn->commit();

            $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'xoa_du_lieu_thanh_cong')]);
        } catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }
}