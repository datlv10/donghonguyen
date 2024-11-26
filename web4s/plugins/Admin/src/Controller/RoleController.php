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

class RoleController extends AppController {

    public function initialize(): void
    {        
        parent::initialize();        
    }

    public function list() 
    {
        $this->js_page = '/assets/js/pages/list_role.js';       

        $this->set('path_menu', 'setting');
        $this->set('title_for_layout', __d('admin', 'nhom_quyen'));
    }

    public function listJson()
    {
        $table = TableRegistry::get('Roles');

        $data_post = $params = $roles = $sort = [];
        $limit = PAGINATION_LIMIT_ADMIN;
        $page = 1;
        if ($this->request->is('post')) {
            $data_post = !empty($this->request->getData()) ? $this->request->getData() : [];
            $params['filter'] = !empty($data_post['query']) ? $data_post['query'] : [];
            $page = !empty($data_post[PAGINATION][PAGE]) ? intval($data_post[PAGINATION][PAGE]) : 1;
            $limit = !empty($data_post[PAGINATION]['perpage']) ? intval($data_post[PAGINATION]['perpage']) : PAGINATION_LIMIT_ADMIN;
            $sort_data = !empty($data_post[SORT]) ? $data_post[SORT] : [];
            $sort_field = !empty($sort_data[FIELD]) ? $sort_data[FIELD] : null;
            $sort_type = !empty($sort_data[SORT]) ? $sort_data[SORT] : null;
            if(!empty($sort_data)){
                $sort = [$sort_field => $sort_type];
            }
        }

        try {
            $roles = $this->paginate($table->queryListRoles($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => $sort
            ])->toArray();
        } catch (Exception $e) {
            $page = 1;
            $roles = $this->paginate($table->queryListRoles(), [
                'limit' => $limit,
                'page' => $page,
                'order' => $sort
            ])->toArray();
        }

        $pagination_info = !empty($this->request->getAttribute('paging')['Roles']) ? $this->request->getAttribute('paging')['Roles'] : [];
        $meta_info = $this->loadComponent('Utilities')->formatPaginationInfo($pagination_info);

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $roles, 
            META => $meta_info
        ]);
    }

    public function add()
    {
        $this->js_page = '/assets/js/pages/role.js';

        $this->set('path_menu', 'setting');
        $this->set('title_for_layout', __d('admin', 'them_nhom_quyen'));
        $this->render('update');
    }

    public function update($id = null)
    {
        $role = TableRegistry::get('Roles')->find()->where([
            'Roles.id' => $id,
            'Roles.deleted' => 0
        ])->first();

        if(empty($role)){
            $this->showErrorPage();
        }        
        
        $this->set('role', $role);
        $this->set('id', $id);
        $this->js_page = '/assets/js/pages/role.js';

        $this->set('path_menu', 'setting');
        $this->set('title_for_layout', __d('admin', 'cap_nhat_nhom_quyen'));
    }

    public function detail($id = null)
    {
        $role = TableRegistry::get('Roles')->find()->where([
            'Roles.id' => $id,
            'Roles.deleted' => 0
        ])->first();

        if(empty($role)){
            $this->showErrorPage();
        }
        
        $this->set('role', $role);

        $this->set('path_menu', 'setting');
        $this->set('title_for_layout', __d('admin', 'chi_tiet_nhom_quyen'));
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
        $table = TableRegistry::get('Roles');
        
        // validate data
        if(!empty($data['name'])){
            $data['name'] = trim($data['name']);
            $exist_name = $table->checkExistName($data['name'], $id);           
            if($exist_name){
                $this->responseJson([MESSAGE => __d('admin', 'ten_nhom_quyen_da_ton_tai_tren_he_thong')]);
            }
        }

        // merge data with entity  
        if(empty($id)){
            $data['created_by'] = $this->Auth->user('id');
            $role = $table->newEntity($data);
        }else{
            $role = $table->get($id);
            $role = $table->patchEntity($role, $data);
        }

        // show error validation in model
        if($role->hasErrors()){
            $list_errors = $utilities->errorModel($role->getErrors());
            $this->responseJson([
                MESSAGE => !empty($list_errors[0]) ? $list_errors[0] : null,
                DATA => $list_errors
            ]);             
        }
                
        try{
            // save data
            $save = $table->save($role);    
            if (empty($save->id)){
                throw new Exception();
            }
            $this->responseJson([CODE => SUCCESS, DATA => ['id' => $save->id]]);

        }catch (Exception $e) {
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

        $roles_table = TableRegistry::get('Roles');
        try {
            $roles_table->updateALL(
                [
                    'deleted' => 1
                ],
                [
                    'id IN' => $ids
                ]
            );

            $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'xoa_du_lieu_thanh_cong')]);
        } catch (Exception $e) {
            $this->responseJson([MESSAGE => $e->getMessage()]);
        }
    }

    public function permissionSetup()
    {
        $this->js_page = '/assets/js/pages/role_permission_setup.js';

        $table = TableRegistry::get('Roles');

        $permission_default = $table->getPermissionDefault();
        $roles = $table->queryListRoles([FIELD => FULL_INFO])->toArray();
        
        // lấy chiều rộng của cột hiển thị ngoài view (mặc định cột tên là 20% nên các cột còn lại tổng bằng 80%)
        $with_column = !empty($roles) ? round(80/count($roles)) : 0;
        
        if(!empty($roles)){
            foreach($roles as $k => $role){
                $roles[$k]['permission'] = !empty($role['permission']) ? json_decode($role['permission'], true) : [];
            }
        }
        
        $this->set('permission_default', $permission_default);
        $this->set('with_column', $with_column);
        $this->set('roles', $roles);
        $this->set('locales_key', Configure::read('LOCALES_KEY_CONTROLLER'));
        $this->set('path_menu', 'setting');
        $this->set('title_for_layout', __d('admin', 'thiet_lap_phan_quyen'));
    }

    public function permissionSave()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        $roles = TableRegistry::get('Roles')->queryListRoles([FIELD => FULL_INFO])->toArray();

        if (!$this->getRequest()->is('post') || empty($roles)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('Roles');

        $patch_data = [];
        foreach($roles as $role){
            $role_id = !empty($role['id']) ? intval($role['id']) : null;
            $permission = !empty($data[$role_id]) ? $data[$role_id] : [];
            $patch_data[] = [
                'id' => !empty($role['id']) ? intval($role['id']) : null,
                'permission' => !empty($permission) ? json_encode($permission) : null
            ];
        }

        $data_permission = $table->patchEntities($roles, $patch_data, ['validate' => false]);                
        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $save_permission = $table->saveMany($data_permission);            
            if (empty($save_permission)){
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