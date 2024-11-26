<?php

namespace Admin\Controller;

use Admin\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Http\Response;
use Cake\ORM\Query;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;


class CityController extends AppController {

    public function initialize(): void
    {
        parent::initialize();        
    }

    public function list($country_id = null) 
    {
        if(empty($country_id)) $this->showErrorPage();
        
        $this->js_page = '/assets/js/pages/list_city.js';
        $this->set('country_id', $country_id);
        $this->set('path_menu', 'setting');
        $this->set('title_for_layout', __d('admin', 'tinh_thanh'));
    }

    public function listJson($country_id = null)
    {
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        if(empty($country_id)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
        }

        $table = TableRegistry::get('Cities');
        $utilities = $this->loadComponent('Utilities');
      
        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        
        // params query
        $params[QUERY] = !empty($data[QUERY]) ? $data[QUERY] : [];

        // params filter
        $params[FILTER] = !empty($data[DATA_FILTER]) ? $data[DATA_FILTER] : [];
        if(!empty($params[QUERY])){
            $params[FILTER] = array_merge($params[FILTER], $params[QUERY]);
        }

        $params[FILTER]['country_id'] = $country_id;

        // params sort
        $params[SORT] = !empty($data[SORT]) ? $data[SORT] : [];

        // page and limit
        $page = !empty($data[PAGINATION][PAGE]) ? intval($data[PAGINATION][PAGE]) : 1;
        $limit = !empty($data[PAGINATION][PERPAGE]) ? intval($data[PAGINATION][PERPAGE]) : PAGINATION_LIMIT_ADMIN;

        // sort 
        $sort_field = !empty($params[SORT][FIELD]) ? $params[SORT][FIELD] : null;
        $sort_type = !empty($params[SORT][SORT]) ? $params[SORT][SORT] : null;

        try {
            $cities = $this->paginate($table->queryListCities($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        
        } catch (Exception $e) {
            $page = 1;
            $cities = $this->paginate($table->queryListCities($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        }

        $pagination_info = !empty($this->request->getAttribute('paging')['Cities']) ? $this->request->getAttribute('paging')['Cities'] : [];
        $meta_info = $utilities->formatPaginationInfo($pagination_info);

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $cities, 
            META => $meta_info
        ]);
    }

    public function add($country_id = null)
    {
        if(empty($country_id)) $this->showErrorPage();

        $this->js_page = [
            '/assets/js/pages/city.js'
        ];

        $this->set('country_id', $country_id);
        $this->set('path_menu', 'setting');
        $this->set('title_for_layout', __d('admin', 'them_tinh_thanh'));
        $this->render('update');
    }

    public function update($country_id = null, $id = null)
    {
        if(empty($country_id) || empty($id)) $this->showErrorPage();

        $city = TableRegistry::get('Cities')->find()->where(['id' => $id])->first();

        if(empty($city)) $this->showErrorPage();
        
        $this->set('path_menu', 'setting');
        $this->set('country_id', $country_id);
        $this->set('id', $id);
        $this->set('city', $city);

        $this->js_page = [
            '/assets/js/pages/city.js'
        ];
        $this->set('title_for_layout', __d('admin', 'cap_nhat_tinh_thanh'));
    }

    public function detail($id = null)
    {
        if(empty($id)) $this->showErrorPage();

        $city = TableRegistry::get('Cities')->find()->where(['id' => $id])->first();

        if(empty($city)) $this->showErrorPage();

        $this->set('city', $city);
        $this->set('path_menu', 'setting');
        $this->set('title_for_layout', __d('admin', 'tinh_thanh'));
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
        $table = TableRegistry::get('Cities');        

        if(!empty($id)){
            $city = $table->find()->where(['id' => $id])->first();
            if(empty($city)){
                $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
            }
        }

        // validate data
        if(empty($data['name'])){
            $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_ten_quoc_gia')]);
        }

        if(empty($data['country_id'])){
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $data_save = [
            'name' => !empty($data['name']) ? $data['name'] : null,
            'position' => !empty($data['position']) ? intval($data['position']) : null,
            'country_id' => !empty($data['country_id']) ? $data['country_id'] : null,
            'status' => 1
        ];

        // merge data with entity 
        if(empty($id)){
            $entity = $table->newEntity($data_save);
        }else{            
            $entity = $table->patchEntity($city, $data_save);
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

        $table = TableRegistry::get('Cities');

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            foreach($ids as $id){
                $city = $table->get($id);
                if (empty($city)) {
                    throw new Exception(__d('admin', 'khong_tim_thay_thong_tin_bai_viet'));
                }

                $entity = $table->patchEntity($city, ['id' => $id, 'deleted' => 1], ['validate' => false]);
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

        $table = TableRegistry::get('Cities');

        $citites = $table->find()->where([
            'Cities.id IN' => $ids,
            'Cities.deleted' => 0
        ])->select(['Cities.id', 'Cities.status'])->toArray();
        
        if(empty($citites)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_ban_ghi')]);
        }

        $patch_data = [];
        foreach ($ids as $k => $city_id) {
            $patch_data[] = [
                'id' => $city_id,
                'status' => $status,
                'draft' => 0
            ];
        }

        $entities = $table->patchEntities($citites, $patch_data, ['validate' => false]);

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

        $cities_table = TableRegistry::get('Cities');

        $data_save['position'] = $value;
        $city = $cities_table->get($id);
        $city = $cities_table->patchEntity($city, $data_save);

        try{
            // save data
            $save = $cities_table->save($city);
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
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $table = TableRegistry::get('Cities');
        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        $filter = !empty($data[FILTER]) ? $data[FILTER] : [];
        $filter[STATUS] = 1;

        $citites = $table->queryListCities([
            FILTER => $filter,
            FIELD => LIST_INFO,
        ])->limit(10)->toArray();

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $citites, 
        ]);
    }
}