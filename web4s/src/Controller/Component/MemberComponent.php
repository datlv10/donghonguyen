<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;
use Cake\Core\Exception\Exception;
use Cake\Utility\Security;

class MemberComponent extends Component
{
    public $controller = null;
    public $components = ['System', 'Utilities', 'ReCaptcha', 'Email', 'SmsBrandname', 'Upload', 'PaginatorExtend', 'Admin.Customer', 'Admin.Order', 'CustomersPointFrontend'];

    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->controller = $this->_registry->getController();
    }

    public function login($data = [], $options = [])
    {
        $api = !empty($options['api']) ? true : false;

        $username = !empty($data['username']) ? trim($data['username']) : null;
        $password = !empty($data['password']) ? $data['password'] : null;
        $redirect = !empty($data['redirect']) ? $data['redirect'] : null;

        if(!$this->controller->getRequest()->is('post') || empty($username) || empty($password)){
           return $this->System->getResponse([MESSAGE => __d('template', 'vui_long_nhap_tai_khoan_va_mat_khau_de_dang_nhap')]);
        }

        // check recaptcha
        if(!$api){
            $token = !empty($data[TOKEN_RECAPTCHA]) ? $data[TOKEN_RECAPTCHA] : null;
            $check_recaptcha = $this->ReCaptcha->check($token);
            if($check_recaptcha[CODE] != SUCCESS){
               return $this->System->getResponse([MESSAGE => $check_recaptcha[MESSAGE]]);
            }
        }

        $password = Security::hash($password, 'md5', false);
        $account = TableRegistry::get('CustomersAccount')->loginMember($username, $password);

        if(empty($account)){
           return $this->System->getResponse([MESSAGE => __d('template', 'tai_khoan_hoac_mat_khau_khong_dung')]);
        }

        if(empty($account['status']) || empty($account['Customer']['status'])){
           return $this->System->getResponse([MESSAGE => __d('template', 'tai_khoan_cua_ban_hien_dang_bi_khoa')]);   
        }

        if(!empty($account['status']) && $account['status'] == 2){
            return $this->System->getResponse([
                CODE => SUCCESS,
                DATA => [
                    'username'=> $username,
                    'email' => !empty($account['Customer']['email']) ? $account['Customer']['email'] : null,
                    'wait_active' => true
                ]
            ]); 
        }

        $customer_id = !empty($account['customer_id']) ? intval($account['customer_id']) : null;
        if(empty($customer_id)){
           return $this->System->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_tai_khoan')]);   
        }

        $table = TableRegistry::get('Customers');
        $customer_info = $table->getDetailCustomer($customer_id, [
            'get_account' => true,
            'get_default_address' => true
        ]);    

        $customer_info = $table->formatDataCustomerDetail($customer_info);
        if(empty($customer_info)){
           return $this->System->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_tai_khoan')]);   
        }

        $session = $this->controller->getRequest()->getSession();
        $session->delete(MEMBER);
        $session->write(MEMBER, $customer_info);
        
        return $this->System->getResponse([
            CODE => SUCCESS,
            DATA => [
                'redirect' => $redirect
            ],
            MESSAGE => __d('template', 'dang_nhap_thanh_cong')
        ]);
    }

    public function socialLogin($data = [], $options = [])
    {
        $api = !empty($options['api']) ? true : false;

        if (empty($data)) {
            return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }      

        $social_id = !empty($data['social_id']) ? $data['social_id'] : null;
        $type = !empty($data['type']) ? $data['type'] : null;
        $full_name = !empty($data['full_name']) ? $data['full_name'] : null;
        $email = !empty($data['email']) ? $data['email'] : '';
        $picture = !empty($data['picture']) ? $data['picture'] : null;
        $redirect = !empty($data['redirect']) ? $data['redirect'] : null;        
        if(empty($social_id) || empty($type) || !in_array($type, ['facebook', 'google', 'apple'])){
            return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('Customers');
        $account_table = TableRegistry::get('CustomersAccount');

        $session = $this->controller->getRequest()->getSession();
        $session->delete(MEMBER);

        $account = $account_table->loginSocial($social_id, $type);
        $customer_id = !empty($account['customer_id']) ? intval($account['customer_id']) : null;

        $action = 'login';
        if(empty($customer_id)){
            // nếu thêm mới tài khoản thì bắt buộc phải có full_name
            if(empty($full_name)){
                return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
            }

            $action = 'add';

            $check_email = $table->find()->where(['deleted' => 0, 'email' => $email])->select(['id'])->first();
            if (!empty($check_email['id'])) {
                $action = 'update';
                $customer_id = intval($check_email['id']);
            }

            switch ($action) {
                case 'add':
                    $avatar = null;
                    if(!empty($picture)){                       
                        $upload = $this->Upload->uploadToCdnByUrl($picture, CUSTOMER);
                        $result_upload = !empty($upload[DATA]) ? $upload[DATA] : [];
                        if(!empty($upload[CODE]) && $upload[CODE] == SUCCESS && !empty($result_upload['url'])){
                            $avatar = $result_upload['url'];
                        }
                    }
                    
                    $data_save = [
                        'username' => $type . $social_id,
                        'password' => $this->Utilities->generateRandomString(),
                        'full_name' => $full_name,
                        'email' => $email,
                        'avatar' => $avatar,
                        'status_account' => 1
                    ];
  
                    $add_customer = $this->Customer->saveCustomer($data_save);
                    $new_customer = !empty($add_customer[DATA]) ? $add_customer[DATA] : null;
                    $customer_id = !empty($new_customer['id']) ? intval($new_customer['id']) : null;

                    $update_social_id = $account_table->updateSocialId($customer_id, $social_id, $type);
                    if(!$update_social_id){
                        return $this->System->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_tai_khoan')]);
                    }
                break;
                
                case 'update':

                    $update_social_id = $account_table->updateSocialId($customer_id, $social_id, $type);
                    if(!$update_social_id){
                        return $this->System->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_tai_khoan')]);
                    }
                break;
            }            
        }

        if(empty($customer_id)){
            return $this->System->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_tai_khoan')]);
        }

        $customer_info = $table->getDetailCustomer($customer_id, [
            'get_account' => true,
            'get_default_address' => true
        ]);    

        $customer_info = $table->formatDataCustomerDetail($customer_info);
        if(empty($customer_info)){
            return $this->System->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_tai_khoan')]);
        }

        $session->write(MEMBER, $customer_info);

        return $this->System->getResponse([
            CODE => SUCCESS,
            DATA => [
                'action' => $action,
                'redirect' => $redirect
            ],
            MESSAGE => __d('template', 'dang_nhap_thanh_cong')
        ]);
    }

    public function register($data = [], $options = [])
    {
        $api = !empty($options['api']) ? true : false;

        if (!$this->controller->getRequest()->is('post') || empty($data)) {
            return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $data['username'] = !empty($data['username']) ? trim(strip_tags($data['username'])) : null;
        $data['full_name'] = !empty($data['username']) ? trim(strip_tags($data['full_name'])) : null;
        $data['password'] = !empty($data['password']) ? $data['password'] : null;
        $data['verify_password'] = !empty($data['verify_password']) ? $data['verify_password'] : null;
        $data['email'] = !empty($data['email']) ? trim(strip_tags($data['email'])) : null;
        $data['phone'] = !empty($data['phone']) ? trim(strip_tags($data['phone'])) : null;
        
        if(empty($data['full_name'])) {
            return $this->System->getResponse([MESSAGE => __d('template', 'vui_long_nhap_ten_tai_khoan')]);
        }

        if(empty($data['username'])) {
            return $this->System->getResponse([MESSAGE => __d('template', 'vui_long_nhap_tai_khoan_dang_ky')]);
        }

        if(empty($data['password'])) {
            return $this->System->getResponse([MESSAGE => __d('template', 'vui_long_nhap_mat_khau')]);
        }

        if(empty($data['verify_password'])) {
            return $this->System->getResponse([MESSAGE => __d('template', 'vui_long_nhap_xac_nhan_mat_khau')]);
        }

        if($data['password'] != $data['verify_password']){
            return $this->System->getResponse([MESSAGE => __d('template', 'xac_nhan_mat_khau_khong_chinh_xac')]);
        }

        if(empty($data['phone'])) {
            return $this->System->getResponse([MESSAGE => __d('template', 'vui_long_nhap_so_dien_thoai')]);
        }

        // check recaptcha
        if(!$api){
            $token = !empty($data[TOKEN_RECAPTCHA]) ? $data[TOKEN_RECAPTCHA] : null;
            $check_recaptcha = $this->ReCaptcha->check($token);
            if($check_recaptcha[CODE] != SUCCESS){
                return $this->System->getResponse([MESSAGE => $check_recaptcha[MESSAGE]]);
            }
        }

        $username_exist = TableRegistry::get('CustomersAccount')->checkExistUsername($data['username']);
        if($username_exist){
            return $this->System->getResponse([MESSAGE => __d('template', 'tai_khoan_da_duoc_dang_ky')]);
        }

        $data_save = [
            'full_name' => !empty($data['full_name']) ? trim($data['full_name']) : null,
            'username' => !empty($data['username']) ? trim($data['username']) : null,
            'password' => !empty($data['password']) ? $data['password'] : null,
            'email' => !empty($data['email']) ? trim($data['email']) : null,
            'phone' => !empty($data['phone']) ? trim($data['phone']) : null,
            'birthday' => !empty($data['birthday']) ? trim($data['birthday']) : null,
            'sex' => !empty($data['sex']) ? trim($data['sex']) : null,
            'city_id' => !empty($data['city_id']) ? intval($data['city_id']) : null,
            'district_id' => !empty($data['district_id']) ? intval($data['district_id']) : null,
            'ward_id' => !empty($data['ward_id']) ? intval($data['ward_id']) : null,
            'address' => !empty($data['address']) ? trim($data['address']) : null,
            'address_name' => __d('template', 'mac_dinh'),
            'is_default' => 1
        ];
        
        $result = $this->Customer->saveCustomer($data_save);
        if($result[CODE] == SUCCESS){

            $result[DATA] = TableRegistry::get('Customers')->formatDataCustomerDetail($result[DATA]);
            $settings = TableRegistry::get('Settings')->getSettingWebsite();
            $waiting_confirm = !empty($settings['customer']['waiting_confirm']) ? 1 : 0;
            if (!empty($waiting_confirm)) {                
                
                $params_email = [
                    'to_email' => $result[DATA]['email'],
                    'code' => 'REGISTER_MEMBER',
                    'id_record' => $result[DATA]['customer_id'],
                    'generate_token' => 'active_account',
                    'from_website_template' => !empty($data['from_website_template']) ? true : false
                ];

                $this->Email->send($params_email);

                return $this->System->getResponse([
                    CODE => SUCCESS,
                    MESSAGE => __d('template', 'tai_khoan_cua_ban_da_duoc_dang_ky_thanh_cong'),
                    DATA => [
                        'waiting_confirm' => true,
                        'email' => $result[DATA]['email']
                    ]
                ]);
            }

            $result[MESSAGE] = __d('template', 'tai_khoan_cua_ban_da_duoc_dang_ky_thanh_cong');
            $session = $this->controller->getRequest()->getSession();
            $session->write(MEMBER, $result[DATA]);
        }

        return $result;
    }

    public function updateProfile($data = [], $options = [])
    {
        $api = !empty($options['api']) ? true : false;

        if (!$this->controller->getRequest()->is('post') || empty($data)) {
            return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        if(empty($data['full_name'])){
            return $this->System->getResponse([MESSAGE => __d('template', 'vui_long_nhap_ten_khach_hang')]);
        }

        if(empty($data['email'])){
            return $this->System->getResponse([MESSAGE => __d('template', 'vui_long_nhap_dia_chi_email')]);
        }

        $member = $this->controller->getRequest()->getSession()->read(MEMBER);
        $customer_id = !empty($member['customer_id']) ? intval($member['customer_id']) : null;
        if(empty($customer_id)){
            return $this->System->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_tai_khoan')]);
        }

        // không được thay đổi email
        if(!empty($member['email']) && !empty($data['email']) && $member['email'] != $data['email']){
            return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        // không được thay đổi số điện thoại
        if(!empty($member['phone']) && !empty($data['phone']) && $member['phone'] != $data['phone']){
            return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        // check recaptcha
        if(!$api){
            $token = !empty($data[TOKEN_RECAPTCHA]) ? $data[TOKEN_RECAPTCHA] : null;
            $check_recaptcha = $this->ReCaptcha->check($token);
            if($check_recaptcha[CODE] != SUCCESS){
                return $this->System->getResponse([MESSAGE => $check_recaptcha[MESSAGE]]);
            }
        }
        
        $data['full_name'] = !empty($data['full_name']) ? strip_tags(trim($data['full_name'])) : null;

        if(!empty($member['image_avatar'])){
            $path = !empty($data['path']) ? $data['path'] : COMMENT;
            $result_upload = $this->Upload->uploadToCdn($file, $path);            
            $result_upload = $this->Upload->uploadToCdn($member['image_avatar'], CUSTOMER);
            if(!empty($upload[CODE]) && $upload[CODE] == SUCCESS && !empty($result_upload['url'])){
                $data['avatar'] = $result_upload['url'];
            }
        }

        $table = TableRegistry::get('Customers');

        $result = $this->Customer->saveCustomer($data, $customer_id);
        
        if($result[CODE] == SUCCESS){
            $result[DATA] = $table->formatDataCustomerDetail($result[DATA]);

            // reset session
            $customer_info = $table->getDetailCustomer($customer_id, [
                'get_account' => true,
                'get_default_address' => true
            ]);    

            $customer_info = $table->formatDataCustomerDetail($customer_info);
            
            $session = $this->controller->getRequest()->getSession();
            $session->write(MEMBER, $customer_info);
        }

        return $result;
    }

    public function listAddress($data = [], $options = [])
    {
        $api = !empty($options['api']) ? true : false;
        if (!$this->controller->getRequest()->is('post')) {
            return $this->System->getResponse([MESSAGE => __d('template', 'phuong_thuc_khong_hop_le')]);
        }

        $member = $this->controller->getRequest()->getSession()->read(MEMBER);
        $customer_id = !empty($member['customer_id']) ? intval($member['customer_id']) : null;

        $table = TableRegistry::get('Customers');

        $member_info = $table->getDetailCustomer($customer_id, [
            'get_list_address' => true
        ]);
        $member_info = $table->formatDataCustomerDetail($member_info);

        $list_address = !empty($member_info['addresses']) ? $member_info['addresses'] : [];

        return $this->System->getResponse([
            CODE => SUCCESS,
            DATA => $list_address
        ]);
    }

    public function saveAddress($data = [], $options = [])
    {
        $api = !empty($options['api']) ? true : false;

        if (!$this->controller->getRequest()->is('post') || empty($data)) {
            return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        if(empty($data['name'])){
            return $this->System->getResponse([MESSAGE => __d('template', 'vui_long_nhap_ten_dia_chi')]);
        }

        // check recaptcha
        if(!$api){
            $token = !empty($data[TOKEN_RECAPTCHA]) ? $data[TOKEN_RECAPTCHA] : null;
            $check_recaptcha = $this->ReCaptcha->check($token);
            if($check_recaptcha[CODE] != SUCCESS){
                return $this->System->getResponse([MESSAGE => $check_recaptcha[MESSAGE]]);
            }
        }
        
        $session = $this->controller->getRequest()->getSession();

        $member = $session->read(MEMBER);
        $customer_id = !empty($member['customer_id']) ? intval($member['customer_id']) : null; 
        $table = TableRegistry::get('Customers');
        $result = $this->Customer->saveAddress($data, $customer_id);

        
        $callback = !empty($data['callback']) ? $data['callback'] : null;
        if($callback == CONTACT && isset($result[CODE]) && $result[CODE] == SUCCESS) {
            $address_id = $result[DATA]->toArray()['id'];
            $member_info = $table->getDetailCustomer($customer_id, ['address_id' => $address_id]);
            $contact_info = $table->formatDataCustomerDetail($member_info);

            $session->write(CONTACT, $contact_info);
        }

        return $this->System->getResponse($result);
    }

    public function setDefaultAddress($data = [], $options = [])
    {
        $api = !empty($options['api']) ? true : false;

        if (!$this->controller->getRequest()->is('post') || empty($data)) {
            return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $session = $this->controller->getRequest()->getSession();
        $member = $session->read(MEMBER);

        $data_update = [
            'id' => !empty($data['id']) ? $data['id'] : null,
            'customer_id' => !empty($member['customer_id']) ? intval($member['customer_id']) : null
        ];

        $result = $this->Customer->setDefault($data_update);

        return $this->System->getResponse($result);
    }

    public function deleteAddress($data = [], $options = [])
    {
        $api = !empty($options['api']) ? true : false;

        if (!$this->controller->getRequest()->is('post') || empty($data)) {
            return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $session = $this->controller->getRequest()->getSession();
        $member = $session->read(MEMBER);
        $customer_id = !empty($member['customer_id']) ? intval($member['customer_id']) : null;
        $address_id = !empty($data['id']) ? $data['id'] : null;
        if(empty($address_id)){
            return $this->System->getResponse([
                MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_dia_chi')
            ]);
        }

        $address_info = TableRegistry::get('CustomersAddress')->find()->where(['id' => $address_id])->first();
        if(empty($address_info['customer_id']) || $address_info['customer_id'] != $customer_id){
            return $this->System->getResponse([
                MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_dia_chi')
            ]);
        }

        $result = $this->Customer->deleteAddress([
            'id' => $address_id
        ]);

        return $this->System->getResponse($result);
    }

    public function infomation($data = [], $options = [])
    {
        $api = !empty($options['api']) ? true : false;

        if (!$this->controller->getRequest()->is('post')) {
            return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $session = $this->controller->getRequest()->getSession();

        $member = $session->read(MEMBER);
        $customer_id = !empty($member['customer_id']) ? intval($member['customer_id']) : null; 

        $table = TableRegistry::get('Customers');
        $member_info = $table->getDetailCustomer($customer_id, [
            'get_list_address' => true,
            'get_point' => true,
            'get_bank'=> true
        ]);
        $member_info = $table->formatDataCustomerDetail($member_info);

        return $this->System->getResponse([
            CODE => SUCCESS,
            DATA => $member_info
        ]);
    }

    public function changePassword($data = [], $options = [])
    {
        $api = !empty($options['api']) ? true : false;

        if (!$this->controller->getRequest()->is('post') || empty($data)) {
            return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        // check recaptcha
        if(!$api){
            $token = !empty($data[TOKEN_RECAPTCHA]) ? $data[TOKEN_RECAPTCHA] : null;
            $check_recaptcha = $this->ReCaptcha->check($token);
            if($check_recaptcha[CODE] != SUCCESS){
                return $this->System->getResponse([MESSAGE => $check_recaptcha[MESSAGE]]);
            }
        }

        $old_password = !empty($data['old_password']) ? $data['old_password'] : null;
        $new_password = !empty($data['new_password']) ? $data['new_password'] : null;
        $re_password = !empty($data['re_password']) ? $data['re_password'] : null;

        if(empty($old_password) || empty($new_password) || empty($re_password)){
            return $this->System->getResponse([MESSAGE => __d('template', 'vui_long_nhap_day_du_thong_tin')]);
        }

        if($new_password != $re_password){
            return $this->System->getResponse([MESSAGE => __d('template', 'xac_nhan_mat_khau_khong_chinh_xac')]);
        }

        $member = $this->controller->getRequest()->getSession()->read(MEMBER);
        $customer_id = !empty($member['customer_id']) ? intval($member['customer_id']) : null;
        
        $account_table = TableRegistry::get('CustomersAccount');        
        $account_info = $account_table->find()->where(['customer_id' => $customer_id])->first();
        if (empty($account_info)) {
            return $this->System->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_tai_khoan')]);
        }

        $database_password = !empty($account_info['password']) ? $account_info['password'] : null;
        $old_password = Security::hash($old_password, 'md5', false);
        $new_password = Security::hash($new_password, 'md5', false);

        if($database_password != $old_password) {
            return $this->System->getResponse([MESSAGE => __d('template', 'mat_khau_cu_nhap_khong_chinh_xac')]);
        }

        if($database_password == $new_password) {
            return $this->System->getResponse([MESSAGE => __d('template', 'mat_khau_thay_doi_khong_the_giong_mat_khau_cu')]);
        }

        $data_account = $account_table->patchEntity($account_info, [
            'password' => $new_password
        ]);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $save = $account_table->save($data_account);

            if (empty($save->id)){
                throw new Exception();
            }

            $conn->commit();
            return $this->System->getResponse([CODE => SUCCESS]);

        }catch (Exception $e) {
            $conn->rollback();
            return $this->System->getResponse([MESSAGE => $e->getMessage()]);
        }
    }

    public function forgotPassword($data = [], $options = [])
    {
        $api = !empty($options['api']) ? true : false;

        if (!$this->controller->getRequest()->is('post') || empty($data)) {
            return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $email = !empty($data['email']) ? trim($data['email']) : null;
        if(empty($email)) {
            return $this->System->getResponse([MESSAGE => __d('template', 'vui_long_nhap_dia_chi_email')]);
        }

        // check recaptcha
        if(!$api){
            $token = !empty($data[TOKEN_RECAPTCHA]) ? $data[TOKEN_RECAPTCHA] : null;
            $check_recaptcha = $this->ReCaptcha->check($token);
            if($check_recaptcha[CODE] != SUCCESS){
                return $this->System->getResponse([MESSAGE => $check_recaptcha[MESSAGE]]);
            }
        }

        $customer_info = TableRegistry::get('Customers')->find()->contain(['Account'])->where([
            'Customers.deleted' => 0,
            'Customers.email' => $email,
            'Account.deleted' => 0,
        ])->select(['Customers.id', 'Customers.email', 'Customers.status', 'Account.status'])->first();
        if(empty($customer_info['id'])) {
            return $this->System->getResponse([MESSAGE => __d('template', 'thong_tin_tai_khoan_khong_ton_tai')]);
        }

        if(empty($customer_info['status']) || empty($customer_info['Account']['status'])) {
            return $this->System->getResponse([MESSAGE => __d('template', 'tai_khoan_cua_ban_hien_dang_bi_khoa')]);
        }

        $params_email = [
            'to_email' => $email,
            'code' => 'FORGOT_PASSWORD',
            'id_record' => $customer_info['id'],
            'generate_token' => 'forgot_password',
            'from_website_template' => !empty($data['from_website_template']) ? true : false
        ];

        $send_email = $this->Email->send($params_email);
        return $this->System->getResponse([
            CODE => SUCCESS,
            MESSAGE => __d('template', 'gui_ma_xac_nhan_thanh_cong'),
            DATA => [
                'email' => $email
            ]
        ]);
    }

    public function resendVerifyCode($data = [], $options = [])
    {
        $api = !empty($options['api']) ? true : false;

        if (!$this->controller->getRequest()->is('post') || empty($data)) {
            return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $generate_token = !empty($data['generate_token']) ? trim($data['generate_token']) : null;
        $email = !empty($data['email']) ? trim($data['email']) : null;

        if(empty($generate_token)|| !in_array($generate_token, Configure::read('TYPE_TOKEN'))) {
            return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        if(empty($email)){
            return $this->System->getResponse([MESSAGE => __d('template', 'vui_long_nhap_dia_chi_email')]);
        }

        // check recaptcha
        if(!$api){
            $token = !empty($data[TOKEN_RECAPTCHA]) ? $data[TOKEN_RECAPTCHA] : null;
            $check_recaptcha = $this->ReCaptcha->check($token);
            if($check_recaptcha[CODE] != SUCCESS){
                return $this->System->getResponse([MESSAGE => $check_recaptcha[MESSAGE]]);
            }
        }

        $session = $this->controller->getRequest()->getSession();
        $time_verify_account = $session->read('time_verify_account');

        if(!empty($time_verify_account) && (($time_verify_account + 60) > time())) {
            return $this->System->getResponse([MESSAGE => __d('template', 'thoi_gian_giua_hai_lan_gui_la_mot_phut')]);
        }

        $customer_info = TableRegistry::get('Customers')->find()->contain(['Account'])->where([
            'Customers.deleted' => 0,
            'Customers.email' => $email,
            'Account.deleted' => 0,
        ])->select(['Customers.id', 'Customers.email', 'Customers.status', 'Account.status'])->first();

        if(empty($customer_info)) {
            return $this->System->getResponse([MESSAGE => __d('template', 'thong_tin_kich_hoat_khong_ton_tai')]);
        }

        if(empty($customer_info['status']) || empty($customer_info['Account']['status'])) {
            return $this->System->getResponse([MESSAGE => __d('template', 'tai_khoan_cua_ban_hien_dang_bi_khoa')]);
        }

        $session->write('time_verify_account', time());

        $params_email = [
            'code' => 'RESEND_VERIFY_CODE',
            'to_email' => $email,
            'generate_token' => $generate_token,
            'from_website_template' => !empty($data['from_website_template']) ? true : false
        ];

        $result = $this->Email->send($params_email);

        if(!empty($result[CODE]) && $result[CODE] == ERROR) {
            return $this->System->getResponse([MESSAGE => __d('template', 'khong_gui_duoc_email')]);
        }

        return $this->System->getResponse([
            CODE => SUCCESS,
            MESSAGE => __d('template', 'gui_ma_xac_nhan_thanh_cong')
        ]);
    }

    public function verifyForgotPassword($data = [], $options = [])
    {
        $api = !empty($options['api']) ? true : false;

        if (!$this->controller->getRequest()->is('post') || empty($data)) {
            return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $email = !empty($data['email']) ? trim($data['email']) : null;
        $code = !empty($data['code']) ? trim($data['code']) : null;
        $new_password = !empty($data['new_password']) ? trim($data['new_password']) : null;
        $re_password = !empty($data['re_password']) ? trim($data['re_password']) : null;

        if(empty($email)) {
            return $this->System->getResponse([MESSAGE => __d('template', 'vui_long_nhap_dia_chi_email')]);
        }

        if(empty($code)) {
            return $this->System->getResponse([MESSAGE => __d('template', 'vui_long_nhap_ma_xac_nhan')]);
        }

        if(empty($new_password) || empty($re_password)){
            return $this->System->getResponse([MESSAGE => __d('template', 'vui_long_nhap_day_du_thong_tin')]);
        }

        if($new_password != $re_password){
            return $this->System->getResponse([MESSAGE => __d('template', 'xac_nhan_mat_khau_khong_chinh_xac')]);
        }

        // check recaptcha
        if(!$api){
            $token = !empty($data[TOKEN_RECAPTCHA]) ? $data[TOKEN_RECAPTCHA] : null;
            $check_recaptcha = $this->ReCaptcha->check($token);
            if($check_recaptcha[CODE] != SUCCESS){
                return $this->System->getResponse([MESSAGE => $check_recaptcha[MESSAGE]]);
            }
        }    

        $account_table = TableRegistry::get('CustomersAccount');
        $account_info = $account_table->find()->contain(['Customer'])->where([
            'Customer.email' => $email,
            'CustomersAccount.deleted' => 0,
            'Customer.deleted' => 0
        ])->select(['CustomersAccount.id', 'CustomersAccount.status', 'Customer.status'])->first();
        if (empty($account_info)) {
            return $this->System->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_tai_khoan')]);
        }

        if(empty($account_info['status']) || empty($account_info['Customer']['status'])) {
            return $this->System->getResponse([MESSAGE => __d('template', 'tai_khoan_cua_ban_hien_dang_bi_khoa')]);
        }

        $data_account = $account_table->patchEntity($account_info, [
            'password' => Security::hash($new_password, 'md5', false)
        ]);

        $table_email_token = TableRegistry::get('EmailToken');
        $email_token_info = $table_email_token->find()->where([
            'email' => $email,
            'code' => $code,
            'type' => 'forgot_password',
            'status' => 0,
            'end_time >=' => time()
        ])->first();

        if(empty($email_token_info)) {
            return $this->System->getResponse([MESSAGE => __d('template', 'thong_tin_khong_chinh_xac_hoac_ma_xac_nhan_da_het_han')]);
        }

        $email_token = $table_email_token->patchEntity($email_token_info, ['status' => 1]);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();
                        
            $save_password = $account_table->save($data_account);

            if (empty($save_password->id)){
                throw new Exception();
            }

            $save = $table_email_token->save($email_token);
            if (empty($save->id)){
                throw new Exception();
            }

            $conn->commit();

            return $this->System->getResponse([
                CODE => SUCCESS, 
                MESSAGE => __d('template', 'thay_doi_mat_khau_thanh_cong')
            ]);

        }catch (Exception $e) {
            $conn->rollback();
            return $this->System->getResponse([MESSAGE => $e->getMessage()]);  
        }
    }

    public function verifyAccount($data = [], $options = [])
    {
        $api = !empty($options['api']) ? true : false;

        if (!$this->controller->getRequest()->is('post') || empty($data)) {
            return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $email = !empty($data['email']) ? trim($data['email']) : null;
        $code = !empty($data['code']) ? trim($data['code']) : null;

        if(empty($email)) {
            return $this->System->getResponse([MESSAGE => __d('template', 'vui_long_nhap_dia_chi_email')]);
        }

        if(empty($code)) {
            return $this->System->getResponse([MESSAGE => __d('template', 'vui_long_nhap_ma_xac_nhan')]);
        }

        // check recaptcha
        if(!$api){
            $token = !empty($data[TOKEN_RECAPTCHA]) ? $data[TOKEN_RECAPTCHA] : null;
            $check_recaptcha = $this->ReCaptcha->check($token);
            if($check_recaptcha[CODE] != SUCCESS){
                return $this->System->getResponse([MESSAGE => $check_recaptcha[MESSAGE]]);
            }
        }

        // check account
        $table_account = TableRegistry::get('CustomersAccount');
        $account_info = $table_account->find()->contain(['Customer'])->where([
            'Customer.deleted' => 0,
            'Customer.email' => $email,
            'CustomersAccount.status' => 2,
            'CustomersAccount.deleted' => 0
        ])->first();

        if(empty($account_info)){
            return $this->System->getResponse([MESSAGE => __d('template', 'khong_the_kich_hoat_tai_khoan_nay')]);
        }


        $table_email_token = TableRegistry::get('EmailToken');

        $email_token_info = $table_email_token->find()->where([
            'email' => $email,
            'code' => $code,
            'type' => 'active_account',
            'status' => 0,
            'end_time >=' => time()
        ])->first();

        if(empty($email_token_info)) {
            return $this->System->getResponse([MESSAGE => __d('template', 'thong_tin_khong_chinh_xac_hoac_ma_xac_nhan_da_het_han')]);
        }

        $email_token = $table_email_token->patchEntity($email_token_info, ['status' => 1]);

        $account = $table_account->patchEntity($account_info, ['status' => 1]);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();
                        
            $save = $table_email_token->save($email_token);
            if (empty($save->id)){
                throw new Exception();
            }

            $save_account = $table_account->save($account);
            if (empty($save_account->id)){
                throw new Exception();
            }

            $conn->commit();

            return $this->System->getResponse([
                CODE => SUCCESS, 
                MESSAGE => __d('template', 'kich_hoat_tai_khoan_thanh_cong')
            ]);

        }catch (Exception $e) {
            $conn->rollback();
            return $this->System->getResponse([MESSAGE => $e->getMessage()]);  
        }
    }

    public function updateAvatar($data = [], $options = [])
    {
        $api = !empty($options['api']) ? true : false;

        if(!$this->controller->getRequest()->is('post') || empty($data)) {
            return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $file = !empty($data['file']) ? $data['file'] : [];
        $path = !empty($data['path']) ? $data['path'] : CUSTOMER;
        if(empty($file)){
            return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        if(empty($file['type']) || !in_array($file['type'], ['image/png', 'image/jpeg'])){
            return $this->System->getResponse([MESSAGE => __d('template', 'tep_dang_tai_khong_hop_le')]);
        }

        $result_upload = $this->Upload->uploadToCdn($file, $path);
        if(empty($result_upload[CODE]) || $result_upload[CODE] != SUCCESS){
            return $this->System->getResponse([
                MESSAGE => !empty($result_upload[MESSAGE]) ? $result_upload[MESSAGE] : null
            ]);
        }

        $member = $this->controller->getRequest()->getSession()->read(MEMBER);
        $customer_id = !empty($member['customer_id']) ? intval($member['customer_id']) : null;
        if(empty($customer_id)){
            return $this->System->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_tai_khoan')]);
        }

        $customers_table = TableRegistry::get('Customers');

        $customers_info = $customers_table->find()->where(['id' => $customer_id])->select(['id', 'avatar'])->first();
        if(empty($customers_info)){
            return $this->System->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_tai_khoan')]);
        }

        $data_save = [
            'id' => $customer_id,
            'avatar' => !empty($result_upload[DATA]['url']) ? $result_upload[DATA]['url'] : null
        ];

        $customer = $customers_table->patchEntity($customers_info, $data_save, ['validate' => false]);
        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $save = $customers_table->save($customer);
            if (empty($save->id)){
                throw new Exception();
            }

            $conn->commit();

            return $this->System->getResponse([
                CODE => SUCCESS, 
                DATA => $result_upload[DATA] ? $result_upload[DATA] : []
            ]);
        }catch (Exception $e) {
            $conn->rollback();
            return $this->System->getResponse([MESSAGE => $e->getMessage()]);
        }        
    }

    public function listOrders($data = [], $options = [])
    {
        $api = !empty($options['api']) ? true : false;

        $member = $this->controller->getRequest()->getSession()->read(MEMBER);
        $customer_id = !empty($member['customer_id']) ? intval($member['customer_id']) : null;

        $orders_table = TableRegistry::get('Orders');

        //param
        $limit = 10;
        $page = !empty($data['page']) ? intval($data['page']) : 1;

        $sort_field = 'id';
        $sort_type = 'DESC';

        $keyword = !empty($data['keyword']) ? $data['keyword'] : null;
        $create_from = !empty($data['create_from']) ? $data['create_from'] : null;
        $create_to = !empty($data['create_to']) ? $data['create_to'] : null;
        $group_status = !empty($data['group_status']) ? $data['group_status'] : null;
        $status = !empty($data['status']) ? $data['status'] : null;

        $params = [
            'get_items' => !empty($options['get_items']) ? true : false,
            SORT => [
                FIELD => $sort_field,
                SORT => $sort_type
            ],
            FILTER => [
                TYPE => ORDER,
                'keyword' => $keyword,
                'customer_id' => $customer_id,
                'create_from' => $create_from,
                'create_to' => $create_to,
                'group_status' => $group_status,
                'status' => $status
            ]
        ];
        
        try {
            $orders = $this->PaginatorExtend->paginate($orders_table->queryListOrders($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        } catch (Exception $e) {
            $orders = $this->PaginatorExtend->paginate($orders_table->queryListOrders($params), [
                'limit' => $limit,
                'page' => 1,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        }
        
        $pagination = [];
        $pagination_info = !empty($this->controller->getRequest()->getAttribute('paging')['Orders']) ? $this->controller->getRequest()->getAttribute('paging')['Orders'] : [];
        $pagination = $this->Utilities->formatPaginationInfo($pagination_info);
        
        $result = [];
        if(!empty($orders)){
            foreach ($orders as $k => $order) {
                $result[$k] = $orders_table->formatDataOrderDetail($order, LANGUAGE);
            }
        }

        return $this->System->getResponse([
            CODE => SUCCESS,
            DATA => [
                'orders' => $result,
                PAGINATION => $pagination
            ]
        ]);
    }

    public function orderInfomation($data = [], $options = [])
    {
        $api = !empty($options['api']) ? true : false;

        $code = !empty($data['code']) ? $data['code'] : null;
        if(empty($code)){
            return $this->System->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_don_hang')]);
        }

        $member = $this->controller->getRequest()->getSession()->read(MEMBER);
        $customer_id = !empty($member['customer_id']) ? intval($member['customer_id']) : null;

        $table = TableRegistry::get('Orders');

        $check_order = $table->find()->contain(['OrdersContact'])->where([
            'OrdersContact.customer_id' => $customer_id,
            'Orders.code' => $code,
            'Orders.deleted' => 0
        ])->select(['Orders.id'])->first();

        $order_id = !empty($check_order['id']) ? intval($check_order['id']) : null;
        if(empty($order_id)){
            return $this->System->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_don_hang')]);
        }

        $order = $table->getDetailOrder($order_id, [
            'get_items' => true,
            'get_contact' => true
        ]);
        $order_info = $table->formatDataOrderDetail($order, LANGUAGE);

        return $this->System->getResponse([
            CODE => SUCCESS,
            DATA => $order_info
        ]);
    }

    public function cancelOrder($data = [], $options = [])
    {
        $api = !empty($options['api']) ? true : false;

        if(!$this->controller->getRequest()->is('post') || empty($data)) {
            return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }
        
        $member = $this->controller->getRequest()->getSession()->read(MEMBER);
        $customer_id = !empty($member['customer_id']) ? intval($member['customer_id']) : null;
        $customer_order_id = !empty($data['customer_order_id']) ? intval($data['customer_order_id']) : null;
        if(empty($customer_id)){
            return $this->System->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_tai_khoan')]);
        }

        if(empty($customer_order_id)){
            return $this->System->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_don_hang')]);
        }

        $orders_table = TableRegistry::get('Orders');
        $order_info = $orders_table->find()->contain(['OrdersContact'])->where([
            'Orders.id' => $customer_order_id,
            'OrdersContact.customer_id' => $customer_id,
            'Orders.deleted' => 0,
            'Orders.type' => ORDER
        ])->first();

        if(empty($order_info)) {
            return $this->System->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_don_hang')]);
        }

        $result = $this->Order->cancelOrder($customer_order_id);
        if(empty($result[CODE]) || $result[CODE] == ERROR){
            return $this->System->getResponse([MESSAGE => __d('template', 'huy_don_hang_khong_thanh_cong')]);
        }

        $data_save = [
            'customer_cancel' => 1,
            'customer_note_cancel' => !empty($data['customer_note_cancel']) ? $data['customer_note_cancel'] : null
        ];

        $order = $orders_table->patchEntity($order_info, $data_save);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $save = $orders_table->save($order);

            if (empty($save->id)){
                throw new Exception();
            }

            $conn->commit();
            return $this->System->getResponse([
                CODE => SUCCESS
            ]);

        }catch (Exception $e) {
            $conn->rollback();
            return $this->System->getResponse([MESSAGE => $e->getMessage()]);
        }
    }

    public function logout()
    {
        $session = $this->controller->getRequest()->getSession();        
        $session->delete(CONTACT);
        $session->delete(MEMBER);
        $session->delete(COUPON);
        $session->delete(POINT);    
        $session->delete(AFFILIATE);
        $session->delete(SHIPPING);

        return $this->System->getResponse([
            CODE => SUCCESS,
            MESSAGE => __d('template', 'dang_xuat_tai_khoan_thanh_cong')
        ]);
    }

    public function getVerifyCode($data = [], $options = [])
    {
        if (!$this->controller->getRequest()->is('post') || empty($data)) {
            return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        // check recaptcha
        $api = !empty($options['api']) ? true : false;
        if(!$api){
            $token = !empty($data[TOKEN_RECAPTCHA]) ? $data[TOKEN_RECAPTCHA] : null;
            $check_recaptcha = $this->ReCaptcha->check($token);
            if($check_recaptcha[CODE] != SUCCESS){
                return $this->System->getResponse([MESSAGE => $check_recaptcha[MESSAGE]]);
            }
        }
        // loại email_token [verify_change_email | verify_change_phone]
        $type_token = !empty($data['type_token']) ? trim($data['type_token']) : null;

        //xác minh qua [email | phone]
        $type_verify = !empty($data['type_verify']) ? trim($data['type_verify']) : null;

        if(empty($type_token) || !in_array($type_token, Configure::read('TYPE_TOKEN'))) {
            return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        if(empty($type_verify)){
            return $this->System->getResponse([MESSAGE => __d('template', 'vui_long_chon_hinh_thuc_nhan_ma_xac_nhan')]);
        }

        $session = $this->controller->getRequest()->getSession();
        $member = $session->read(MEMBER);
        $customer_id = !empty($member['customer_id']) ? intval($member['customer_id']) : null;
        if(empty($customer_id)){
            return $this->System->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_tai_khoan')]);
        }
        $customer_info = TableRegistry::get('Customers')->find()->where([
            'Customers.deleted' => 0,
            'Customers.id' => $customer_id
        ])->select(['Customers.id', 'Customers.email', 'Customers.phone'])->first();

        if(empty($customer_info)) {
            return $this->System->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_tai_khoan')]);
        }

        $email = !empty($customer_info['email']) ? $customer_info['email'] : null;
        $phone = !empty($customer_info['phone']) ? $customer_info['phone'] : null;
        if($type_verify == 'phone' && empty($phone)){
            return $this->System->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_so_dien_thoai')]);
        }

        if($type_verify == 'email' && empty($email)){
            return $this->System->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_email')]);
        }

        $time_verify = $session->read($type_token);
        if(!empty($time_verify) && (($time_verify + 60) > time())) {
            return $this->System->getResponse([MESSAGE => __d('template', 'thoi_gian_giua_hai_lan_gui_la_mot_phut')]);
        }

        $session->write($type_token, time());

        switch ($type_verify) {
            // xác minh qua gửi email
            case 'email':
                $params_email = [
                    'to_email' => $email,
                    'code' => 'CHANGE_VERIFY',
                    'id_record' => $customer_id,
                    'generate_token' => $type_token,
                    'from_website_template' => $api ? true : false
                ];

                $result = $this->Email->send($params_email);

                if(!empty($result[CODE]) && $result[CODE] == ERROR) {
                    return $this->System->getResponse([MESSAGE => __d('template', 'khong_gui_duoc_email')]);
                }
                break;
            
            // xác minh qua gửi tin nhắn điện thoại
            case 'phone':
                $params_phone = [
                    'type_token' => $type_token,
                    'phone' => $phone,
                ];
                
                $result = $this->SmsBrandname->sendToken($params_phone);

                if(!empty($result[CODE]) && $result[CODE] == ERROR) {
                    return $this->System->getResponse([MESSAGE => $result[MESSAGE]]);
                }

                break;      
        }

        return $this->System->getResponse([
            CODE => SUCCESS,
            MESSAGE => __d('template', 'gui_ma_xac_nhan_thanh_cong')
        ]);
    }

    public function changeImportantInfo($data = [], $options = [])
    {
        if (!$this->controller->getRequest()->is('post') || empty($data)) {
            return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        // check recaptcha
        $api = !empty($options['api']) ? true : false;        
        if(!$api){
            $token = !empty($data[TOKEN_RECAPTCHA]) ? $data[TOKEN_RECAPTCHA] : null;
            $check_recaptcha = $this->ReCaptcha->check($token);
            if($check_recaptcha[CODE] != SUCCESS){
                return $this->System->getResponse([MESSAGE => $check_recaptcha[MESSAGE]]);
            }
        }   

        $type = !empty($data['type']) ? trim($data['type']) : null; //loại thông tin thay đổi[phone | email]
        $type_verify = !empty($data['type_verify']) ? $data['type_verify'] : null;
        $verify_code = !empty($data['code']) ? trim($data['code']) : null;
        $new_phone = !empty($data['new_phone']) ? trim($data['new_phone']) : null;
        $new_email = !empty($data['new_email']) ? trim($data['new_email']) : null;

        if(empty($type)) {
            return $this->System->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_loai_thong_tin_thay_doi')]);
        }

        if(empty($verify_code)) {
            return $this->System->getResponse([MESSAGE => __d('template', 'vui_long_nhap_ma_xac_nhan')]);
        }

        if(empty($new_phone) && empty($new_email)) {
            return $this->System->getResponse([MESSAGE => __d('template', 'vui_long_nhap_thong_tin')]);
        }

        if(empty($type_verify) || !in_array($type_verify, ['phone', 'email'])) {
            return $this->System->getResponse([MESSAGE => __d('template', 'vui_long_nhap_ma_xac_nhan')]);
        }

        $session = $this->controller->getRequest()->getSession();
        $member = $session->read(MEMBER);
        $customer_id = !empty($member['customer_id']) ? intval($member['customer_id']) : null;
        if(empty($customer_id)) {
            return $this->System->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_tai_khoan')]);
        }


        $customers_table = TableRegistry::get('Customers');
        $customer_info = $customers_table->find()->where([
            'id' => $customer_id
        ])->select(['id', 'email', 'phone', 'full_name', 'code'])->first();

        if(empty($customer_info)) {
            return $this->System->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_tai_khoan')]);
        }

        $email = !empty($customer_info['email']) ? trim($customer_info['email']) : null;
        $phone = !empty($customer_info['phone']) ? trim($customer_info['phone']) : null;
        $full_name = !empty($customer_info['full_name']) ? trim($customer_info['full_name']) : null;
        $code_customer = !empty($customer_info['code']) ? trim($customer_info['code']) : null;

        if($type == 'phone' && empty($phone)){
            return $this->System->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_so_dien_thoai')]);
        }

        if($type == 'email' && empty($email)){
            return $this->System->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_email')]);
        }

        $data_save = [];
        $table_email_token = TableRegistry::get('EmailToken');

        switch($type){
            //thay đổi thông tin số điện thoại
            case 'phone':
                $phone_exist = $customers_table->checkPhoneExist($new_phone, $customer_id);
                if($phone_exist){
                    return $this->System->getResponse([MESSAGE => __d('template', 'so_dien_thoai_da_duoc_dang_ky')]);
                }

                // xác minh mã hợp lệ
                $where = [
                    'code' => $verify_code,
                    'type' => VERIFY_CHANGE_PHONE,
                    'status' => 0,
                    'end_time >=' => time()
                ];

                if($type_verify == 'email'){
                    $where['email'] = $email;
                }

                if($type_verify == 'phone'){
                    $where['phone'] = $phone;
                }

                $email_token_info = $table_email_token->find()->where($where)->first();

                if(empty($email_token_info)) {
                    return $this->System->getResponse([MESSAGE => __d('template', 'thong_tin_khong_chinh_xac_hoac_ma_xac_nhan_da_het_han')]);
                }
                $email_token = $table_email_token->patchEntity($email_token_info, ['status' => 1]);

                $data_save['phone'] = $new_phone;
                $data_save['search_unicode'] = strtolower($this->Utilities->formatSearchUnicode([$code_customer, $full_name, $email, $new_phone]));

            break;

            //thay đổi thông tin email
            case 'email':
                $email_exist = $customers_table->checkEmailAccountExist($new_email, $customer_id);
                if($email_exist){
                    return $this->System->getResponse([MESSAGE => __d('admin', 'email_da_duoc_dang_ky')]);
                }

                $where = [
                    'code' => $verify_code,
                    'type' => VERIFY_CHANGE_EMAIL,
                    'status' => 0,
                    'end_time >=' => time()
                ];
                if($type_verify == 'email'){
                    $where['email'] = $email;
                }

                if($type_verify == 'phone'){
                    $where['phone'] = $phone;
                }

                $email_token_info = $table_email_token->find()->where($where)->first();
                if(empty($email_token_info)) {
                    return $this->System->getResponse([MESSAGE => __d('template', 'thong_tin_khong_chinh_xac_hoac_ma_xac_nhan_da_het_han')]);
                }
                $email_token = $table_email_token->patchEntity($email_token_info, ['status' => 1]);

                $data_save['email'] = $new_email;
                $data_save['search_unicode'] = strtolower($this->Utilities->formatSearchUnicode([$code_customer, $full_name, $new_email, $phone]));

            break;

            // case 'account':
            // break;
        } 

        $customer = $customers_table->patchEntity($customer_info, $data_save, ['validate' => false]);

        // show error validation in model
        if($customer->hasErrors()){
            $list_errors = $this->Utilities->errorModel($customer->getErrors());
            
            return $this->System->getResponse([
                MESSAGE => !empty($list_errors[0]) ? $list_errors[0] : null,
                DATA => $list_errors
            ]);             
        }
        
        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            // save data
            $save_customer = $customers_table->save($customer);
            if (empty($save_customer->id)){
                throw new Exception();
            }

            $save = $table_email_token->save($email_token);
            if (empty($save->id)){
                throw new Exception();
            }

            $conn->commit();
            return $this->System->getResponse([CODE => SUCCESS, DATA => $save]);

        }catch (Exception $e) {
            $conn->rollback();
            return $this->System->getResponse([MESSAGE => $e->getMessage()]);
        }
    }

    public function registerByNumberPhone($data = [], $options = [])
    {
        $api = !empty($options['api']) ? true : false;

        if (!$this->controller->getRequest()->is('post') || empty($data)) {
            return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        // check recaptcha
        if(!$api){
            $token = !empty($data[TOKEN_RECAPTCHA]) ? $data[TOKEN_RECAPTCHA] : null;
            $check_recaptcha = $this->ReCaptcha->check($token);
            if($check_recaptcha[CODE] != SUCCESS){
                return $this->System->getResponse([MESSAGE => $check_recaptcha[MESSAGE]]);
            }
        }

        $full_name = !empty($data['full_name']) ? $data['full_name'] : null;
        $phone = !empty($data['phone']) ? $data['phone'] : null;
        $username = $phone;
        $password = strtolower($this->Utilities->generateRandomNumber(8));
        
        if(empty($full_name)) {
            return $this->System->getResponse([MESSAGE => __d('template', 'vui_long_nhap_ten')]);
        }

        if(empty($phone)){
            return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $username_exist = TableRegistry::get('CustomersAccount')->checkExistUsername($phone);
        if($username_exist){
            return $this->System->getResponse([MESSAGE => __d('template', 'so_dien_thoai_da_duoc_dang_ky')]);
        }        

        $data_save = [
            'full_name' => $full_name,
            'username' => $username,
            'password' => $password,
            'phone' => $phone,
            'status_account' => 1
        ];
        
        $result = $this->Customer->saveCustomer($data_save);
        if(!empty($result[CODE]) && $result[CODE] == ERROR) {
            return $this->System->getResponse([MESSAGE => $result[MESSAGE]]);
        }

        $this->SmsBrandname->sendSms([
            'phone' => $phone,
            'message' => __d('template', 'mat_khau_tai_khoan_cua_ban_la_{0}', $password)
        ]);

        return $this->System->getResponse([
            CODE => SUCCESS,
            DATA => [
                'customer_id' => !empty($result[DATA]['Account']['customer_id']) ? $result[DATA]['Account']['customer_id'] : null
            ],
            MESSAGE => __d('template', 'tai_khoan_cua_ban_da_duoc_dang_ky_thanh_cong')
        ]);
    }

    public function customerLogin($data = [], $options = [])
    {
        $api = !empty($options['api']) ? true : false;

        $customer_id = !empty($data['customer_id']) ? trim($data['customer_id']) : null;

        if(!$this->controller->getRequest()->is('post') || empty($customer_id)){
           return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        // check recaptcha
        if(!$api){
            $token = !empty($data[TOKEN_RECAPTCHA]) ? $data[TOKEN_RECAPTCHA] : null;
            $check_recaptcha = $this->ReCaptcha->check($token);
            if($check_recaptcha[CODE] != SUCCESS){
               return $this->System->getResponse([MESSAGE => $check_recaptcha[MESSAGE]]);
            }
        }
        
        $table = TableRegistry::get('Customers');
        $customer_info = $table->find()->contain(['Account'])->where([
            'Customers.id' => $customer_id,
            'Customers.deleted' => 0,
            'Customers.status' => 1,
            'Account.deleted' => 0,
        ])->first();

        if(empty($customer_info)){
           return $this->System->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_tai_khoan')]);   
        }

        if(!empty($customer_info['Account']['status']) && $customer_info['Account']['status'] == 2){
           return $this->System->getResponse([MESSAGE => __d('template', 'tai_khoan_cua_ban_hien_dang_bi_khoa')]);   
        }

        $customer_info = $table->formatDataCustomerDetail($customer_info);
        $session = $this->controller->getRequest()->getSession();
        $session->delete(MEMBER);
        $session->write(MEMBER, $customer_info);
        
        return $this->System->getResponse([
            CODE => SUCCESS,
            MESSAGE => __d('template', 'dang_nhap_thanh_cong')
        ]);
    }

        public function listBankOfPartner($options = [])
    {
        $api = !empty($options['api']) ? true : false;

        $member = $this->controller->getRequest()->getSession()->read(MEMBER);
        $customer_id = !empty($member['customer_id']) ? intval($member['customer_id']) : null;
        if(empty($customer_id)) return $this->System->getResponse([CODE => SUCCESS]);

        $table = TableRegistry::get('CustomersBank');

        $banks = $table->queryListCustomersBank([
            FILTER => [
                'customer_id' => $customer_id
            ]
        ])->toArray();

        $result = [];
        if(!empty($banks)) {
            foreach ($banks as $bank_info) {
                $result[] = $table->formatDataCustomersBankDetail($bank_info);
            }
        }

        return $this->System->getResponse([
            CODE => SUCCESS,
            DATA => $result
        ]);
    }

    public function deleteBank($data = [], $options = [])
    {
        if (!$this->controller->getRequest()->is('post') || empty($data)) {
            return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }
        
        $bank_id = !empty($data['bank_id']) ? $data['bank_id'] : null;

        $member = $this->controller->getRequest()->getSession()->read(MEMBER);
        $customer_id = !empty($member['customer_id']) ? intval($member['customer_id']) : null;

        if(empty($customer_id)){
            return $this->System->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_tai_khoan')]);
        }

        if(empty($bank_id))  {
            return $this->System->getResponse([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('CustomersBank');
        $bank_info = $table->find()->where([
            'id' => $bank_id,
            'customer_id' => $customer_id
        ])->select('id')->first();
        if(empty($bank_info)){
            return $this->System->getResponse([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ngan_hang')]);
        }

        if(!empty($bank_info) && $bank_info['is_default'] == 1){
            return $this->System->getResponse([MESSAGE => __d('admin', 'ban_khong_the_xoa_ngan_hang_mac_dinh')]);
        }

        $bank = $table->patchEntity($bank_info, ['deleted' => 1]);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $save_bank = $table->save($bank);
            if (empty($save_bank->id)){
                throw new Exception();
            }

            $conn->commit();  
            return $this->System->getResponse([CODE => SUCCESS, MESSAGE => __d('admin', 'xoa_lien_ket_ngan_hang_thanh_cong')]);

        }catch (Exception $e) {
            $conn->rollback();
            return $this->System->getResponse([MESSAGE => $e->getMessage()]);  
        }
    }

    public function saveBank($data = [], $options = [])
    {
        $api = !empty($options['api']) ? true : false;
        // check recaptcha
        if(!$api){
            $token = !empty($data[TOKEN_RECAPTCHA]) ? $data[TOKEN_RECAPTCHA] : null;
            $check_recaptcha = $this->ReCaptcha->check($token);
            if($check_recaptcha[CODE] != SUCCESS){
                return $this->System->getResponse([MESSAGE => $check_recaptcha[MESSAGE]]);
            }
        }

        if (!$this->controller->getRequest()->is('post') || empty($data)) {
            return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $list_bank = Configure::read('LIST_BANK');

        $bank_id = !empty($data['bank_id']) ? $data['bank_id'] : null;
        $bank_key = !empty($data['bank_key']) ? $data['bank_key'] : null;
        $bank_name = !empty($list_bank[$bank_key]) ? $list_bank[$bank_key] : null;
        $bank_branch = !empty($data['bank_branch']) ? trim($data['bank_branch']) : null;
        $account_holder = !empty($data['account_holder']) ? trim($data['account_holder']) : null;
        $account_number = !empty($data['account_number']) ? trim($data['account_number']) : null;


        if(empty($bank_key) || empty($bank_name)){
            return $this->System->getResponse([MESSAGE => __d('template', 'vui_long_nhap_ten_ngan_hang')]);
        }

        if(empty($bank_branch)){
            return $this->System->getResponse([MESSAGE => __d('template', 'vui_long_nhap_chi_nhanh')]);
        }

        if(empty($account_holder)){
            return $this->System->getResponse([MESSAGE => __d('template', 'vui_long_nhap_ten_chu_tai_khoan')]);
        }

        if(empty($account_number)){
            return $this->System->getResponse([MESSAGE => __d('template', 'vui_long_nhap_so_tai_khoan')]);
        }

        $member = $this->controller->getRequest()->getSession()->read(MEMBER);
        $customer_id = !empty($member['customer_id']) ? intval($member['customer_id']) : null;
        if(empty($customer_id)){
            return $this->System->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_tai_khoan')]);
        }

        
        $data_save = [
            'customer_id' => $customer_id,
            'bank_key' => $bank_key,
            'bank_name' => $bank_name,
            'bank_branch' => $bank_branch,
            'account_holder' => $account_holder,
            'account_number' => $account_number,
        ];

        $table = TableRegistry::get('CustomersBank');
        if(empty($bank_id)){

            $bank_info = $table->find()->where([
                'customer_id' => $customer_id,
                'deleted' => 0
            ])->select('id')->first();

            if(empty($bank_info)) {
                $data_save['is_default'] = 1;
            }

            $customers_bank = $table->newEntity($data_save);
        }else{            
            $bank_info = $table->find()->where([
                'id' => $bank_id,
                'customer_id' => $customer_id,
                'deleted' => 0
            ])->select('id')->first();

            if(empty($bank_info)){
                return $this->System->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_ngan_hang')]);
            }
            $customers_bank = $table->patchEntity($bank_info, $data_save);
        }

        // show error validation in model
        if($customers_bank->hasErrors()){
            $list_errors = $this->Utilities->errorModel($customers_bank->getErrors());            
            $this->responseJson([
                MESSAGE => !empty($list_errors[0]) ? $list_errors[0] : null,
                DATA => $list_errors
            ]);
        }

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            // save data
            $save_bank = $table->save($customers_bank);
            if (empty($save_bank->id)){
                throw new Exception();
            }

            $conn->commit();
            
            return $this->System->getResponse([
                CODE => SUCCESS, 
                MESSAGE => __d('template', 'cap_nhat_tai_khoan_ngan_hang_thanh_cong')
            ]);

        }catch (Exception $e) {
            $conn->rollback();
            return $this->System->getResponse([MESSAGE => $e->getMessage()]);
        }
    }

    public function memberDoesntExistLogout($customer_id = null)
    {
        if(empty($customer_id)) return false;
        $member_info = TableRegistry::get('Customers')->find()->contain(['Account'])->where([
            'Customers.id' => $customer_id,
            'Customers.status' => 1,
            'Customers.deleted' => 0,
            'Account.deleted' => 0
        ])->select(['id'])->first();

        if(!empty($member_info)) return false;

        $session = $this->controller->getRequest()->getSession();
        $session->delete(CONTACT);
        $session->delete(MEMBER);
        $session->delete(POINT);    
        $session->delete(SHIPPING);
        
        return true;
    }

    public function deleteAccount($data = [], $options = [])
    {
        $api = !empty($options['api']) ? true : false;

        if (!$this->controller->getRequest()->is('post')) {
            return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $session = $this->controller->getRequest()->getSession();
        $member = $session->read(MEMBER);        
        $customer_id = !empty($member['customer_id']) ? intval($member['customer_id']) : null;
        if(empty($customer_id)){
            return $this->System->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_tai_khoan')]);
        }

        $table = TableRegistry::get('Customers');
        $customer_info = $table->find()->where([
            'Customers.id' => $customer_id,
            'Customers.deleted' => 0
        ])->contain(['Account'])->first();
        if (empty($customer_info)) {
            return $this->System->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_khach_hang')]);
        }
        
        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();
            
            if(!empty($customer_info['Account'])){
                $customer = $table->patchEntity($customer_info, [
                    'deleted' => 1,
                    'Account' => [
                        'deleted' => 1
                    ]
                ], ['validate' => false]);
            } else {
                $customer = $table->patchEntity($customer_info, [
                    'deleted' => 1
                ], ['validate' => false]);
            }

            $delete_customer = $table->save($customer);
            if (empty($delete_customer)){
                throw new Exception();
            }

            $conn->commit();

            $session->delete(CONTACT);
            $session->delete(MEMBER);
            $session->delete(COUPON);
            $session->delete(POINT);    
            $session->delete(AFFILIATE);
            $session->delete(SHIPPING);
            
            return $this->System->getResponse([
                CODE => SUCCESS, 
                MESSAGE => __d('template', 'xoa_tai_khoan_thanh_cong')
            ]);

        }catch (Exception $e) {
            $conn->rollback();
            return $this->System->getResponse([MESSAGE => $e->getMessage()]);
        }

        return $result;
    }
}