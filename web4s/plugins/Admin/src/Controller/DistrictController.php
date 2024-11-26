<?php

namespace Admin\Controller;

use Admin\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Http\Response;
use Cake\ORM\Query;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;

class DistrictController extends AppController {

    public function initialize(): void
    {
        parent::initialize();        
    }

    public function list($city_id = null) 
    {
        if(empty($city_id)) $this->showErrorPage();
        $city_info = TableRegistry::get('Cities')->find()->where(['id' => $city_id])->first();
        if(empty($city_info)) $this->showErrorPage();

        $this->js_page = '/assets/js/pages/list_district.js';
        $this->set('path_menu', 'setting');
        $this->set('city_id', $city_id);
        $this->set('city_info', $city_info);
        $this->set('title_for_layout', __d('admin', 'quan_huyen'));
    }

    public function listJson($city_id = null)
    {
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        if(empty($city_id)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
        }

        $table = TableRegistry::get('Districts');
        $utilities = $this->loadComponent('Utilities');
      
        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        
        // params query
        $params[QUERY] = !empty($data[QUERY]) ? $data[QUERY] : [];

        // params filter
        $params[FILTER] = !empty($data[DATA_FILTER]) ? $data[DATA_FILTER] : [];
        if(!empty($params[QUERY])){
            $params[FILTER] = array_merge($params[FILTER], $params[QUERY]);
        }
        $params[FILTER]['city_id'] = $city_id;

        // params sort
        $params[SORT] = !empty($data[SORT]) ? $data[SORT] : [];

        // page and limit
        $page = !empty($data[PAGINATION][PAGE]) ? intval($data[PAGINATION][PAGE]) : 1;
        $limit = !empty($data[PAGINATION][PERPAGE]) ? intval($data[PAGINATION][PERPAGE]) : PAGINATION_LIMIT_ADMIN;

        // sort 
        $sort_field = !empty($params[SORT][FIELD]) ? $params[SORT][FIELD] : null;
        $sort_type = !empty($params[SORT][SORT]) ? $params[SORT][SORT] : null;
      
        try {
            $districts = $this->paginate($table->queryListDistricts($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        } catch (Exception $e) {
            $page = 1;
            $districts = $this->paginate($table->queryListDistricts($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        }

        $pagination_info = !empty($this->request->getAttribute('paging')['Districts']) ? $this->request->getAttribute('paging')['Districts'] : [];
        $meta_info = $utilities->formatPaginationInfo($pagination_info);

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $districts, 
            META => $meta_info
        ]);
    }

    public function add($city_id = null)
    {
        if(empty($city_id)) $this->showErrorPage();

        $this->js_page = [
            '/assets/js/pages/district.js'
        ];

        $this->set('city_id', $city_id);
        $this->set('path_menu', 'setting');
        $this->set('title_for_layout', __d('admin', 'them_quan_huyen'));
        $this->render('update');
    }

    public function update($city_id = null, $id = null)
    {
        if(empty($city_id) || empty($id)) $this->showErrorPage();

        $district = TableRegistry::get('Districts')->find()->where(['id' => $id])->first();

        if(empty($district)) $this->showErrorPage();
        
        $this->set('path_menu', 'setting');
        $this->set('city_id', $city_id);
        $this->set('id', $id);
        $this->set('district', $district);

        $this->js_page = [
            '/assets/js/pages/district.js'
        ];
        $this->set('title_for_layout', __d('admin', 'cap_nhat_quan_huyen'));
    }

    public function detail($id = null)
    {
        if(empty($id)) $this->showErrorPage();

        $district = TableRegistry::get('Districts')->find()->where(['id' => $id])->first();

        if(empty($district)) $this->showErrorPage();

        $this->set('district', $district);
        $this->set('path_menu', 'setting');
        $this->set('title_for_layout', __d('admin', 'quan_huyen'));
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
        $table = TableRegistry::get('Districts');        

        if(!empty($id)){
            $district = $table->find()->where(['id' => $id])->first();
            if(empty($district)){
                $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
            }
        }

        // validate data
        if(empty($data['name'])){
            $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_ten_quoc_gia')]);
        }

        if(empty($data['city_id'])){
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $data_save = [
            'name' => !empty($data['name']) ? $data['name'] : null,
            'position' => !empty($data['position']) ? intval($data['position']) : null,
            'city_id' => !empty($data['city_id']) ? $data['city_id'] : null,
            'status' => 1
        ];

        // merge data with entity 
        if(empty($id)){
            $entity = $table->newEntity($data_save);
        }else{            
            $entity = $table->patchEntity($district, $data_save);
        }

        // show error validation in model
        if($entity->hasErrors()){
            $list_errors = $utilities->errorModel($entity->getErrors());            
            $this->responseJson([
                MESSAGE => !empty($list_errors[0]) ? $list_errors[0] : null,
                DATA => $list_errors
            ]);
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

        $table = TableRegistry::get('Districts');

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            foreach($ids as $id){
                $district = $table->get($id);
                if (empty($district)) {
                    throw new Exception(__d('admin', 'khong_tim_thay_thong_tin_ban_ghi'));
                }

                $entity = $table->patchEntity($district, ['id' => $id, 'deleted' => 1], ['validate' => false]);
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

        $table = TableRegistry::get('Districts');

        $districts = $table->find()->where([
            'Districts.id IN' => $ids,
            'Districts.deleted' => 0
        ])->select(['Districts.id', 'Districts.status'])->toArray();
        
        if(empty($districts)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_ban_ghi')]);
        }

        $patch_data = [];
        foreach ($ids as $k => $district_id) {
            $patch_data[] = [
                'id' => $district_id,
                'status' => $status,
                'draft' => 0
            ];
        }

        $entities = $table->patchEntities($districts, $patch_data, ['validate' => false]);

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

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        $id = !empty($data['id']) ? intval($data['id']) : null;
        $value = !empty($data['value']) ? $data['value'] : 0;
        $name = !empty($data['name']) ? $data['name'] : '';

        // validate data
        if (empty($id) || empty($name)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $districts_table = TableRegistry::get('Districts');

        $data_save['position'] = $value;
        $district = $districts_table->get($id);
        $district = $districts_table->patchEntity($district, $data_save);

        try{
            // save data
            $save = $districts_table->save($district);
            if (empty($save->id)){
                throw new Exception();
            }
            $this->responseJson([CODE => SUCCESS, DATA => ['id' => $save->id]]);

        }catch (Exception $e) {
            $this->responseJson([MESSAGE => $e->getMessage()]);
        }
    }
}