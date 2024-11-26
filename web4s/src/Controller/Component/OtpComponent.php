<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;
use Cake\Core\Exception\Exception;
use Cake\Utility\Security;

class OtpComponent extends Component
{
	public $controller = null;
    public $components = ['System', 'Utilities', 'ReCaptcha', 'SmsBrandname'];

    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->controller = $this->_registry->getController();
    }

    public function otpNumberPhone($data = [], $options = [])
    {
        $phone = !empty($data['phone']) ? $data['phone'] : null;
        $api = !empty($options['api']) ? true : false;

        if (!$this->controller->getRequest()->is('post') || empty($phone)) {
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

        $session = $this->controller->getRequest()->getSession();
        $time_verify = $session->read('otp_number_phone');
        if(!empty($time_verify) && (($time_verify + 60) > time())) {
            return $this->System->getResponse([MESSAGE => __d('template', 'thoi_gian_giua_hai_lan_gui_la_mot_phut')]);
        }

        $session->write('otp_number_phone', time());

        $result = $this->SmsBrandname->sendToken([
            'type_token' => VERIFY_PHONE,
            'phone' => $phone
        ]);

        if(!empty($result[CODE]) && $result[CODE] == ERROR) {
            return $this->System->getResponse([MESSAGE => $result[MESSAGE]]);
        }

        return $this->System->getResponse([
            CODE => SUCCESS,
            DATA => [
                'phone' => $phone
            ],
            MESSAGE => __d('template', 'gui_ma_xac_nhan_thanh_cong')
        ]);
    }

    public function verifyNumberPhone($data = [], $options = [])
    {
        
        $api = !empty($options['api']) ? true : false;

        if (!$this->controller->getRequest()->is('post') || empty($data)) {
            return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $verify_code = !empty($data['verify_code']) ? trim($data['verify_code']) : null;
        $phone = !empty($data['phone']) ? trim($data['phone']) : null;

        if(empty($phone) || empty($verify_code)){
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

        $customer_id = null;
        if(!empty($data['check_phone'])) {
            $customer_info = TableRegistry::get('Customers')->find()->contain(['Account'])->where([
                'Customers.deleted' => 0,
                'Customers.phone' => $phone
            ])->select(['Customers.id', 'Account.username'])->first();

            $customer_id = !empty($customer_info['id']) ? intval($customer_info['id']) : null;
            // nếu khách hàng chưa có tài khoản thì tạo 1 tài khoản mới theo số điện thoại 
            if(empty($customer_info['Account']['username']) && !empty($customer_id)) {
                $username_exist = TableRegistry::get('CustomersAccount')->checkExistUsername($phone);
                if($username_exist){
                    return $this->System->getResponse([MESSAGE => __d('template', 'so_dien_thoai_da_duoc_dang_ky')]);
                }
                
                $data_account = [
                    'username' => $phone,
                    'password' => Security::hash(strtolower($this->Utilities->generateRandomString(6)), 'md5', false),
                    'status' => 1,
                    'customer_id' => $customer_id
                ];
                $data_account = TableRegistry::get('CustomersAccount')->newEntity($data_account);
            }
        }

        // xác minh mã hợp lệ
        $table_email_token = TableRegistry::get('EmailToken');
        $token_info = $table_email_token->find()->where([
            'code' => $verify_code,
            'type' => VERIFY_PHONE,
            'status' => 0,
            'end_time >=' => time(),
            'phone' => $phone
        ])->first();

        if(empty($token_info)) {
            return $this->System->getResponse([MESSAGE => __d('template', 'thong_tin_khong_chinh_xac_hoac_ma_xac_nhan_da_het_han')]);
        }

        $email_token = $table_email_token->patchEntity($token_info, ['status' => 1]);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();
                        
            $save = $table_email_token->save($email_token);
            if (empty($save->id)){
                throw new Exception();
            }

            if(empty($customer_info['Account']['username']) && !empty($data_account)) {
                $save_account = TableRegistry::get('CustomersAccount')->save($data_account);
                if (empty($save_account->id)){
                    throw new Exception();
                }
            }

            $conn->commit();

            $result_data = [
                'phone' => $phone,
                'verify_code' => $verify_code
            ];
            
            if(!empty($data['check_phone']) && $data['check_phone'] == true) {
                $result_data['customer_id'] = $customer_id;
            }

            return $this->System->getResponse([
                CODE => SUCCESS, 
                DATA => $result_data,
                MESSAGE => __d('template', 'ma_otp_hop_le')
            ]);

        }catch (Exception $e) {
            $conn->rollback();
            return $this->System->getResponse([MESSAGE => $e->getMessage()]);  
        }
    }
}