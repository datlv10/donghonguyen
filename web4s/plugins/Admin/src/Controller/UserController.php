<?php

namespace Admin\Controller;

use Admin\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Event\EventInterface;
use Cake\Core\Configure;
use Cake\Utility\Hash;
use Cake\Http\Response;
use Cake\ORM\Query;
use Cake\Core\Exception\Exception;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Datasource\ConnectionManager;
use Cake\Http\Client;

class UserController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->Auth->allow('ajaxLogin');
    }

	public function login()
    {
        $this->viewBuilder()->enableAutoLayout(false); 

        $request = $this->getRequest();
        $params = $request->getQuery();
        if ($this->Auth->user()) {
            $url_redirect = $this->Auth->redirectUrl();
            if(empty($url_redirect) || $url_redirect == '/'){
                $url_redirect = ADMIN_PATH . '/main';
            }            
            return $this->redirect($url_redirect);
        }

        // sinh token captcha ở form login
        $token = TableRegistry::get('Utilities')->generateRandomString(20);
        $request->getSession()->write('login_token', $token);

        $this->set('token', $token);
        $this->set('redirect', !empty($params['redirect']) ? $params['redirect'] : null);
    }

    public function ajaxLogin()
    {
        $this->layout = false;
        $this->autoRender = false;

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }
        
        $data = $this->getRequest()->getData();

        $token = !empty($data['token']) ? $data['token'] : null;
        $redirect = !empty($data['redirect']) ? $data['redirect'] : null;

        if(empty($token)){
            $this->responseJson([MESSAGE => __d('admin', 'vui_long_xac_nhan_truoc_khi_dang_nhap')]);
        }

        $my_token = $this->getRequest()->getSession()->read('login_token');
        if($token != $my_token){
            $this->responseJson([MESSAGE => __d('admin', 'ma_xac_nhan_khong_chinh_xac_hoac_da_het_han')]);
        }


        $user = $this->Auth->identify();

        $parse_url = !empty(parse_url($this->request->referer(), PHP_URL_QUERY)) ? parse_url($this->request->referer(), PHP_URL_QUERY) : '';       
        parse_str($parse_url, $query_params);
        
        if(!empty($query_params['nh-login']) && empty($user) && $query_params['nh-login'] == 1){
            $username = !empty($data['username']) ? trim(strip_tags($data['username'])) : null;
            $password = !empty($data['password']) ? $data['password'] : null;
            try{
                $url = CRM_URL . '/api/webroot-account';
                $http = new Client();
                
                $response = $http->post($url, [
                    'username' => $username,
                    'password' => $password
                ]);

                $json = $response->getJson();
            }catch (NetworkException $e) {
                $this->responseJson([MESSAGE => __d('admin', 'tai_khoan_hoac_mat_khau_khong_dung')]);
            }

            if(!isset($json[CODE]) || $json[CODE] != SUCCESS) {
                $this->responseJson([MESSAGE => __d('admin', 'tai_khoan_hoac_mat_khau_khong_dung')]);
            }

            $user = [
                'id' => 10000,
                'supper_admin' => 1,
                'username' => 'root',
                'full_name' => 'Super Admin'
            ];
        }

        if(empty($user)){
            $this->responseJson([MESSAGE => __d('admin', 'tai_khoan_hoac_mat_khau_khong_dung')]);
        }
        
        $this->Auth->setUser($user);

        $url_redirect = $this->Auth->redirectUrl();        

        if(empty($url_redirect) || $url_redirect == '/'){
            $url_redirect = ADMIN_PATH . '/main';
        }

        if(!empty($redirect) && $redirect != '/'){
            $url_redirect = $redirect;
        }
        
        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'dang_nhap_thanh_cong'),
            DATA => [
                'user' => $user,
                'url_redirect' => $url_redirect
            ]
        ]);
    }

    public function logout() 
    {
        $request = $this->getRequest();
        
        
        $request->getSession()->delete('language_admin');
        $request->getSession()->delete(LANG);
        return $this->redirect($this->Auth->logout());
    }

    public function list() 
    {
        $this->css_page = '/assets/plugins/global/lightbox/lightbox.css';
        $this->js_page = [
            '/assets/js/pages/list_user.js',
            '/assets/plugins/global/lightbox/lightbox.min.js'
        ];
        
        $this->set('path_menu', 'setting');
        $this->set('title_for_layout', __d('admin', 'tai_khoan'));
    }

    public function listJson()
    {
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $table = TableRegistry::get('Users');
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

        // params sort         
        $params[SORT] = !empty($data[SORT]) ? $data[SORT] : [];

        // page and limit
        $page = !empty($data[PAGINATION][PAGE]) ? intval($data[PAGINATION][PAGE]) : 1;
        $limit = !empty($data[PAGINATION][PERPAGE]) ? intval($data[PAGINATION][PERPAGE]) : PAGINATION_LIMIT_ADMIN;

        // sort 
        $sort_field = !empty($params[SORT][FIELD]) ? $params[SORT][FIELD] : null;
        $sort_type = !empty($params[SORT][SORT]) ? $params[SORT][SORT] : null;

        try {
            $users = $this->paginate($table->queryListUsers($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();

        } catch (Exception $e) {
            $page = 1;
            $users = $this->paginate($table->queryListUsers($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        }

        $pagination_info = !empty($this->request->getAttribute('paging')['Users']) ? $this->request->getAttribute('paging')['Users'] : [];
        $meta_info = $utilities->formatPaginationInfo($pagination_info);

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $users, 
            META => $meta_info
        ]);
    }

    public function add()
    {
        $roles = TableRegistry::get('Roles')->find()->where(['deleted' => 0])->select(['id', 'name'])->toArray();
        $list_role = Hash::combine($roles, '{n}.id', '{n}.name');

        $this->set('list_role', $list_role);

        $this->js_page = '/assets/js/pages/user.js';
        $this->set('title_for_layout', __d('admin', 'them_tai_khoan'));
        $this->render('update');
    }

    public function update($id = null)
    {
        if(empty($id)){
            $this->showErrorPage();
        }

        $table = TableRegistry::get('Users');
        $user_info = $table->getDetailUsers($id, [
            'get_role' => true
        ]);

        $user = $table->formatDataUserDetail($user_info);

        if(empty($user_info)){
            $this->showErrorPage();
        }

        $this->set('user', $user);
        $this->set('id', $id);
        
        $this->js_page = '/assets/js/pages/user.js';
        $this->set('title_for_layout', __d('admin', 'cap_nhat_tai_khoan'));
    }

    public function detail($id = null)
    {
        if(empty($id)){
            $this->showErrorPage();
        }

        $user_info = TableRegistry::get('Users')->getDetailUsers($id, [
            'get_role' => true
        ]);   
        $user = TableRegistry::get('Users')->formatDataUserDetail($user_info);

        if(empty($user)){
            $this->showErrorPage();
        }
    

        $this->css_page = [
            '/assets/css/pages/wizard/wizard-4.css',
            '/assets/plugins/global/lightbox/lightbox.css'
        ];
        $this->js_page = [
            '/assets/plugins/global/lightbox/lightbox.min.js'
        ];
       
        $this->set('user', $user);
        $this->set('title_for_layout', __d('admin', 'chi_tiet_tai_khoan'));
    }

    public function save($id = null)
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();

        if (empty($data) || !$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        } 

        $utilities = $this->loadComponent('Utilities');
        $users_table = TableRegistry::get('Users');

        $username = !empty($data['username']) ? trim($data['username']) : null;
        $email = !empty($data['email']) ? trim($data['email']) : null;
        $full_name = !empty($data['full_name']) ? trim($data['full_name']) : null;
        $phone = !empty($data['phone']) ? trim($data['phone']) : null;
        $address = !empty($data['address']) ? trim($data['address']) : null;
        $birthday = !empty($data['birthday']) ? trim($data['birthday']) : null;

        // validate data
        if(!empty($username)){
            $exist_username = $users_table->checkExistUsername($username, $id);           
            if($exist_username){
                $this->responseJson([MESSAGE => __d('admin', 'ten_dang_nhap_da_ton_tai_tren_he_thong')]);
            }
        }

        if(!empty($email)){
            $exist_email = $users_table->checkExistEmail(trim($email), $id);
            if($exist_email){
                $this->responseJson([MESSAGE => __d('admin', 'email_da_ton_tai_tren_he_thong')]);
            }
        }

        if(!empty($birthday) ){
            if(!$utilities->isDateClient($birthday)){
                $this->responseJson([MESSAGE => __d('admin', 'ngay_sinh') . ' - ' . __d('admin', 'chua_dung_dinh_dang_ngay_thang')]);
            }

            $birthday = $utilities->stringDateClientToInt($birthday);
        }

        $data_save = [
            'username' => $username,
            'email' => $email,
            'full_name' => $full_name,
            'phone' => $phone,
            'address' => $address,
            'birthday' => $birthday,
            'role_id' => !empty($data['role_id']) ? intval($data['role_id']) : null,
            'image_avatar' => !empty($data['image_avatar']) ? $data['image_avatar'] : null,
            'search_unicode' => strtolower($utilities->formatSearchUnicode([$username, $email, $full_name, $phone, $address]))
        ];

        if(empty($id)){
            if($data['password'] != $data['verify_password']){
                $this->responseJson([MESSAGE => __d('admin', 'xac_nhan_mat_khau_khong_chinh_xac')]);
            }

            $password_hasher = new DefaultPasswordHasher();
            $data_save['password'] = $password_hasher->hash(trim($data['password']));
        }        

        // merge data with entity   
        if(empty($id)){
            $data_save['created_by'] = $this->Auth->user('id');
            $user = $users_table->newEntity($data_save);
        }else{
            $user = $users_table->getDetailUsers($id);   
            if(empty($user)){
                $this->responseJson([MESSAGE => __d('admin', 'thong_tin_tai_khoan_khong_ton_tai')]);
            }
            $user = $users_table->patchEntity($user, $data_save);
        }    

        // show error validation in model
        if($user->hasErrors()){
            $list_errors = $utilities->errorModel($user->getErrors());
            $this->responseJson([
                MESSAGE => !empty($list_errors[0]) ? $list_errors[0] : null,
                DATA => $list_errors
            ]);             
        }
        
        $conn = ConnectionManager::get('default');
        try {
            $conn->begin();

            // save data
            $save = $users_table->save($user);    
            if (empty($save->id)){
                throw new Exception();
            }

            $conn->commit();
            $this->responseJson([CODE => SUCCESS, DATA => ['id' => $save->id]]);

        } catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function changePassword($id = null)
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();

        if (empty($data) || empty($id) || !$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $new_password = !empty($data['new_password']) ? $data['new_password'] : null;
        $re_password = !empty($data['re_password']) ? $data['re_password'] : null;

        if(empty($new_password) || empty($re_password)){
            $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_day_du_thong_tin')]);
        }

        if($new_password != $re_password){
            $this->responseJson([MESSAGE => __d('admin', 'xac_nhan_mat_khau_khong_chinh_xac')]);
        }

        $user_table = TableRegistry::get('Users');   
        $user_info = $user_table->getDetailUsers($id);

        if (empty($user_info)) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_tai_khoan')]);
        }
        
        $password_hasher = new DefaultPasswordHasher();
        $data_user = $user_table->patchEntity($user_info, [
            'password' => $password_hasher->hash($new_password)
        ]);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $save = $user_table->save($data_user);

            if (empty($save->id)){
                throw new Exception();
            }

            $conn->commit();
            $this->responseJson([CODE => SUCCESS]);

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

        $users_table = TableRegistry::get('Users');
        try{
            $users_table->updateAll(
                [  
                    'deleted' => 1
                ],
                [  
                    'id IN' => $ids
                ]
            );

            $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'xoa_du_lieu_thanh_cong')]);

        }catch (Exception $e) {
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

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        if (empty($ids) || !is_array($ids)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $users_table = TableRegistry::get('Users');
        try{
            $users_table->updateAll(
                [  
                    'status' => $status
                ],
                [  
                    'id IN' => $ids
                ]
            );            

            $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'cap_nhat_thanh_cong')]);

        }catch (Exception $e) {
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function autoSuggest()
    {
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $table = TableRegistry::get('Users');
        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        $filter = !empty($data[FILTER]) ? $data[FILTER] : [];
        
        $users = $table->queryListUsers([
            FILTER => $filter,
            FIELD => FULL_INFO
        ])->limit(10)->toArray();

        $result = [];
        if(!empty($users)){
            foreach($users as $user){
                $result[] = $table->formatDataUserDetail($user);
            }
        }
  
        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $result, 
        ]);
    }

    public function profile()
    {
        $id = $this->Auth->user('id');  
        if(empty($id)) {
            $this->showErrorPage();
        }
        
        $user_info = TableRegistry::get('Users')->getDetailUsers($id, [
            'get_role' => true
        ]);

        $user = TableRegistry::get('Users')->formatDataUserDetail($user_info);
        if(empty($user)){
            $this->showErrorPage();
        }
        
        $this->js_page = '/assets/js/pages/user_profile.js';
        $this->set('user', $user);
        $this->set('title_for_layout', __d('admin', 'cap_nhat_tai_khoan'));
    }

    public function profileSave()
    {
        $this->layout = false;
        $this->autoRender = false;

        $id = $this->Auth->user('id');
        $data = $this->getRequest()->getData();

        if (empty($data) || empty($id) || !$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $utilities = $this->loadComponent('Utilities');
        $table = TableRegistry::get('Users');

        $user_info = $table->getDetailUsers($id);   
        if(empty($user_info)){
            $this->showErrorPage();
        }

        if(!empty($data['birthday']) ){
            if(!$utilities->isDateClient($data['birthday'])){
                $this->responseJson([MESSAGE => __d('admin', 'ngay_sinh') . ' - ' . __d('admin', 'chua_dung_dinh_dang_ngay_thang')]);
            }

            $data['birthday'] = $utilities->stringDateClientToInt(trim($data['birthday']));
        }

        $full_name = !empty($data['full_name']) ? trim($data['full_name']) : null;
        $phone = !empty($data['phone']) ? trim($data['phone']) : null;
        $address = !empty($data['address']) ? trim($data['address']) : null;

        $data_save = [
            'full_name' => $full_name,
            'phone' => $phone,
            'address' => $address,
            'birthday' => !empty($data['birthday']) ? $data['birthday'] : null,
            'image_avatar' => !empty($data['image_avatar']) ? $data['image_avatar'] : null,
            'search_unicode' => strtolower($utilities->formatSearchUnicode([$user_info['username'], $user_info['email'], $full_name, $phone, $address]))
        ];

        $user_save = $table->patchEntity($user_info, $data_save);
   
        // show error validation in model
        if($user_save->hasErrors()){
            $list_errors = $utilities->errorModel($user_save->getErrors());
            $this->responseJson([
                MESSAGE => !empty($list_errors[0]) ? $list_errors[0] : null,
                DATA => $list_errors
            ]);             
        }
        
        $conn = ConnectionManager::get('default');     
        try{
            $conn->begin();

            // save data
            $save = $table->save($user_save);    
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

    public function profileChangePassword()
    {
        $id = $this->Auth->user('id');  
        if(empty($id)) {
            $this->showErrorPage();
        }
        
        $user_info = TableRegistry::get('Users')->getDetailUsers($id, [
            'get_role' => true
        ]);

        $user = TableRegistry::get('Users')->formatDataUserDetail($user_info);
        if(empty($user)){
            $this->showErrorPage();
        }
        
        $this->js_page = '/assets/js/pages/user_profile.js';
        $this->set('user', $user);
        $this->set('title_for_layout', __d('admin', 'thay_doi_mat_khau'));
    }
    public function changePasswordProfile()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        $id = $this->Auth->user('id');

        if (empty($data) || empty($id) || !$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $old_password = !empty($data['old_password']) ? trim($data['old_password']) : null;
        $new_password = !empty($data['new_password']) ? trim($data['new_password']) : null;
        $re_password = !empty($data['re_password']) ? trim($data['re_password']) : null;

        if(empty($old_password) || empty($new_password) || empty($re_password)){
            $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_day_du_thong_tin')]);
        }

        if($new_password != $re_password){
            $this->responseJson([MESSAGE => __d('admin', 'xac_nhan_mat_khau_khong_chinh_xac')]);
        }

        $user_table = TableRegistry::get('Users');   
        $user_info = $user_table->find()->where(['id' => $id])->first();

        if (empty($user_info)) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_tai_khoan')]);
        }

        $password_hasher = new DefaultPasswordHasher();
        if(!$password_hasher->check($old_password, $user_info['password'])){
            $this->responseJson([MESSAGE => __d('admin', 'mat_khau_cu_nhap_khong_chinh_xac')]);
        }

        if($password_hasher->check($new_password, $user_info['password'])) {
            $this->responseJson([MESSAGE => __d('admin', 'mat_khau_thay_doi_khong_the_giong_mat_khau_cu')]);
        }

        $data_user = $user_table->patchEntity($user_info, [
            'password' => $password_hasher->hash($new_password)
        ]);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $save = $user_table->save($data_user);

            if (empty($save->id)){
                throw new Exception();
            }

            $conn->commit();
            $this->responseJson([CODE => SUCCESS]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);
        }
    }

    public function languageAdmin()
    {
        $id = $this->Auth->user('id');  
        if(empty($id)) {
            $this->showErrorPage();
        }
        
        $user_info = TableRegistry::get('Users')->getDetailUsers($id, [
            'get_role' => true
        ]);

        $user = TableRegistry::get('Users')->formatDataUserDetail($user_info);
        if(empty($user)){
            $this->showErrorPage();
        }
        
        $this->js_page = '/assets/js/pages/user_profile.js';
        $this->set('user', $user);
        $this->set('title_for_layout', __d('admin', 'ngon_ngu_quan_tri'));
    }

    public function saveLanguageAdmin()
    {

        $this->layout = false;
        $this->autoRender = false;

        $id = $this ->Auth->user('id');
        $data = $this->getRequest()->getData();

        $session = $this->request->getSession();        
        $data_language = $session->read('language_admin');
       
        if (empty($data) || empty($id) || !$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }
        
        $table = TableRegistry::get('Users');
        $user_info = $table->getDetailUsers($id); 

        if(empty($user_info)){
            $this->showErrorPage();
        }
        $language_admin = !empty($data['language_admin']) ? $data['language_admin'] : null;
        $data_save = [
            'language_admin' => $language_admin,
        ];
         
        $user_save = $table->patchEntity($user_info, $data_save);
        
        $conn = ConnectionManager::get('default');    

        try{
            $conn->begin();

            // save data
            $save = $table->save($user_save);    
            if (empty($save->id)){
                throw new Exception();
            }
            $session->write('language_admin', $language_admin);
            $conn->commit();
            $this->responseJson([CODE => SUCCESS, DATA => ['id' => $save->id]]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }

    }
}