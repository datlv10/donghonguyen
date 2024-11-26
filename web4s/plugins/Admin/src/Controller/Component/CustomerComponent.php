<?php

namespace Admin\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;
use Cake\Utility\Security;
use Cake\Core\Configure;

class CustomerComponent extends AppComponent
{
	public $controller = null;
    public $components = ['System', 'Utilities', 'Location'];

    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->controller = $this->_registry->getController();
    }
  

    public function saveCustomer($data = [], $id = null)
    {
        if(empty($data)) {
            return $this->System->getResponse([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $customers_table = TableRegistry::get('Customers');

        if(empty($data['full_name'])){
            return $this->System->getResponse([MESSAGE => __d('admin', 'vui_long_nhap_ten_khach_hang')]);
        }

        if(!empty($data['email'])){
            $email_exist = $customers_table->checkEmailAccountExist($data['email'], $id);
            if($email_exist){
                return $this->System->getResponse([MESSAGE => __d('admin', 'email_da_duoc_dang_ky')]);
            }
        }

        if(!empty($data['phone'])){
            $phone_exist = $customers_table->checkPhoneExist($data['phone'], $id);
            if($phone_exist){
                return $this->System->getResponse([MESSAGE => __d('template', 'so_dien_thoai_da_duoc_dang_ky')]);
            }
        }

        if(!empty($data['staff_id'])){
            $users_table = TableRegistry::get('Users'); 
            $user_info = $users_table->find()->where(['Users.id' => $data['staff_id']])->first();
            $data['staff_name'] = !empty($user_info['full_name']) ? $user_info['full_name'] : null;
        }

        if(!empty($data['birthday']) ){
            if(!$this->Utilities->isDateClient($data['birthday'])){
                return $this->System->getResponse([MESSAGE => __d('admin', 'ngay_sinh_chua_dung_dinh_dang_ngay_thang')]);
            }

            $data['birthday'] = $this->Utilities->stringDateClientToInt(trim($data['birthday']));
        }

        if(!empty($data['username']) && !empty($data['password'])) {
            $username_exist = TableRegistry::get('CustomersAccount')->checkExistUsername($data['username']);
            if($username_exist){
                return $this->System->getResponse([MESSAGE => __d('template', 'tai_khoan_da_duoc_dang_ky')]);
            }

            $status_account = !empty($data['status_account']) ? intval($data['status_account']) : 1;
            if(!isset($data['status_account'])) {
                $settings = TableRegistry::get('Settings')->getSettingWebsite();
                $customer_settings = !empty($settings['customer']['waiting_confirm']) ? 1 : 0;

                $status_account = 1;
                if(!empty($customer_settings)) {
                    $status_account = 2;
                }
            }

            $data['Account'] = [
                'username' => $data['username'],
                'password' => Security::hash($data['password'], 'md5', false),
                'status' => $status_account
            ];
        }

        // merge data with entity
        $data['full_name'] = !empty($data['full_name']) ? trim(strip_tags($data['full_name'])) : null;
        $data['email'] = !empty($data['email']) ? trim($data['email']) : null;

        if(empty($id)){
            $data['phone'] = !empty($data['phone']) ? strip_tags(trim($data['phone'])) : null;
            $address = !empty($data['address']) ? strip_tags(trim($data['address'])) : null;

            $location = $this->Location->getFullAddress([
                'city_id' => !empty($data['city_id']) ? $data['city_id'] : null,
                'district_id' => !empty($data['district_id']) ? $data['district_id'] : null,
                'ward_id' => !empty($data['ward_id']) ? $data['ward_id'] : null,
                'address' => !empty($data['address']) ? strip_tags(trim($data['address'])) : null
            ]);

            if(!empty($data['phone']) || !empty($address) || !empty($data['address_name'])|| !empty($data['city_id'])){
                $data['Addresses'][] = [
                    'name' => !empty($data['address_name']) ? strip_tags(trim($data['address_name'])) : null,
                    'phone' => $data['phone'],
                    'address' => $address,
                    'country_id' => 1,
                    'city_id' => !empty($data['city_id']) ? $data['city_id'] : null,
                    'district_id' => !empty($data['district_id']) ? $data['district_id'] : null,
                    'ward_id' => !empty($data['ward_id']) ? $data['ward_id'] : null,                
                    'country_name' => !empty($location['country_name']) ? $location['country_name'] : null,
                    'city_name' => !empty($location['city_name']) ? $location['city_name'] : null,
                    'district_name' => !empty($location['district_name']) ? $location['district_name'] : null,
                    'ward_name' => !empty($location['ward_name']) ? $location['ward_name'] : null,
                    'full_address' => !empty($location['full_address']) ? $location['full_address'] : null,
                    'zip_code' => !empty($data['zip_code']) ? $data['zip_code'] : null,
                    'is_default' => !empty($data['is_default']) ? 1 : 0,
                    'search_unicode' => strtolower($this->Utilities->formatSearchUnicode([$data['phone']]))
                ];
            }

            if(empty($data['code'])) {
                $data['code'] = 'CUS' . $this->Utilities->generateRandomNumber(9);  
            }
            
            $data['search_unicode'] = strtolower($this->Utilities->formatSearchUnicode([$data['full_name'], $data['code'], $data['email'], $data['phone']]));

            $customer = $customers_table->newEntity($data, [
                'associated' => ['Addresses', 'Account']
            ]);
        }else{            
            $customer_old = $customers_table->find()->where([
                'id' => $id
            ])->first();

            $code = !empty($customer_old['code']) ? trim($customer_old['code']) : null;

            if(empty($code)) {
                return $this->System->getResponse([MESSAGE => __d('template', 'khong_tim_thay_ma_khach_hang')]);
            }

            // không được thay đổi code
            if(!empty($code) && !empty($data['code']) && $code != $data['code']){
                return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
            }
            
            if(empty($data['full_name'])){
                $data['full_name'] = !empty($customer_old['full_name']) ? $customer_old['full_name'] : null;                
            }

            if(empty($data['email'])){
                $data['email'] = !empty($customer_old['email']) ? $customer_old['email'] : null;
            }

            if(empty($data['phone'])){
                $data['phone'] = !empty($customer_old['phone']) ? $customer_old['phone'] : null;
            }

            $data['search_unicode'] = strtolower($this->Utilities->formatSearchUnicode([$data['full_name'], $code, $data['email'], $data['phone']]));
            $customer = $customers_table->patchEntity($customer_old, $data);
        }

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
            $save = $customers_table->save($customer);

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

    public function saveAddress($data = [], $customer_id = null)
    {
        if(empty($data) || empty($customer_id)) {
            return $this->System->getResponse([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $address_table = TableRegistry::get('CustomersAddress');

        if(empty($data['name'])){
            return $this->System->getResponse([MESSAGE => __d('admin', 'vui_long_nhap_ten_dia_chi')]);   
        }

        $address_id = !empty($data['address_id']) ? intval($data['address_id']) : null;
        $check_name_exist = $address_table->checkExistName(trim($data['name']), $customer_id, $address_id);

        if($check_name_exist){
            return $this->System->getResponse([MESSAGE => __d('admin', 'ten_dia_chi_nay_da_duoc_su_dung_vui_long_chon_mot_ten_khac')]);
        }

        if(!empty($address_id)){
            $address_info = $address_table->find()->where(['id' => $address_id])->first();
            if(empty($address_info)){
                return $this->System->getResponse([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_dia_chi')]);
            }
        }

        $location = $this->Location->getFullAddress([
            'city_id' => !empty($data['city_id']) ? $data['city_id'] : null,
            'district_id' => !empty($data['district_id']) ? $data['district_id'] : null,
            'ward_id' => !empty($data['ward_id']) ? $data['ward_id'] : null,
            'address' => !empty($data['address']) ? strip_tags(trim($data['address'])) : null
        ]);

        $phone = !empty($data['phone']) ? trim($data['phone']) : null;
        $data_save = [
            'customer_id' => $customer_id,
            'name' => !empty($data['name']) ? trim($data['name']) : null,
            'phone' => $phone,
            'country_id' => 1,
            'city_id' => !empty($data['city_id']) ? $data['city_id'] : null,
            'district_id' => !empty($data['district_id']) ? $data['district_id'] : null,
            'ward_id' => !empty($data['ward_id']) ? $data['ward_id'] : null,                
            'country_name' => !empty($location['country_name']) ? $location['country_name'] : null,
            'city_name' => !empty($location['city_name']) ? $location['city_name'] : null,
            'district_name' => !empty($location['district_name']) ? $location['district_name'] : null,
            'ward_name' => !empty($location['ward_name']) ? $location['ward_name'] : null,
            'full_address' => !empty($location['full_address']) ? $location['full_address'] : null,
            'address' => !empty($data['address']) ? strip_tags(trim($data['address'])) : null,
            'zip_code' => !empty($data['zip_code']) ? $data['zip_code'] : null,
            'search_unicode' => strtolower($this->Utilities->formatSearchUnicode([$phone]))
        ];
        
        if(empty($address_id)){            
            $address = $address_table->newEntity($data_save);
        }else{
            $address = $address_table->patchEntity($address_info, $data_save);
        }

        // show error validation in model
        if($address->hasErrors()){
            $list_errors = $this->Utilities->errorModel($address->getErrors());
            
            return $this->System->getResponse([
                MESSAGE => !empty($list_errors[0]) ? $list_errors[0] : null,
                DATA => $list_errors
            ]);             
        }

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $save = $address_table->save($address);
            if (empty($save->id)){
                throw new Exception();
            }

            $conn->commit();

            $data_ouput = $save;
            $data_ouput['address_name'] = !empty($save['name']) ? $save['name'] : null;
            return $this->System->getResponse([CODE => SUCCESS, DATA => $data_ouput]);

        }catch (Exception $e) {
            $conn->rollback();
            return $this->System->getResponse([MESSAGE => $e->getMessage()]);
        }
    }

    public function setDefault($data = [])
    {
        $id = !empty($data['id']) ? intval($data['id']) : null;
        $customer_id = !empty($data['customer_id']) ? intval($data['customer_id']) : null;

        if(empty($id) || empty($customer_id))  {
            return $this->System->getResponse([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }
        
        $address_table = TableRegistry::get('CustomersAddress');
        $address_info = $address_table->find()->where(['id' => $id])->first();
        if(empty($address_info) || intval($address_info['customer_id']) != $customer_id){
            return $this->System->getResponse([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_dia_chi')]);
        }        
        $address_entity = $address_table->patchEntity($address_info, ['id' => $id, 'is_default' => 1]);
        
        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();
            $address_table->updateAll(
                [  
                    'is_default' => 0
                ],
                [  
                    'customer_id' => $customer_id
                ]
            );
            
            $update_default = $address_table->save($address_entity); 

            if (empty($update_default)){
                throw new Exception();
            }    

            $conn->commit();
            return $this->System->getResponse([CODE => SUCCESS, MESSAGE => __d('admin', 'cap_nhat_thanh_cong')]);

        }catch (Exception $e) {
            $conn->rollback();
            return $this->System->getResponse([MESSAGE => $e->getMessage()]);  
        }
    }

    public function deleteAddress($data = [])
    {
        $id = !empty($data['id']) ? $data['id'] : null;

        if(empty($id))  {
            return $this->System->getResponse([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('CustomersAddress');
        $customer_address = $table->find()->where(['id' => $id])->first();
        if(empty($customer_address)){
            return $this->System->getResponse([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_dia_chi')]);
        }

        if(!empty($customer_address) && $customer_address['is_default'] == 1){
            return $this->System->getResponse([MESSAGE => __d('admin', 'ban_khong_the_xoa_dia_chi_mac_dinh')]);
        }

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();
            $delete = $table->delete($customer_address);

            if (empty($delete)){
                throw new Exception();
            } 

            $conn->commit();  
            return $this->System->getResponse([CODE => SUCCESS, MESSAGE => __d('admin', 'cap_nhat_thanh_cong')]);

        }catch (Exception $e) {
            $conn->rollback();
            return $this->System->getResponse([MESSAGE => $e->getMessage()]);  
        }
    } 

    public function saveLevelForPartner($customer_code = null){
        if(empty($customer_code)) {
            return $this->System->getResponse([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $customers_table = TableRegistry::get('Customers');
        $partner_affiliate_info = $customers_table->find()->where([
            'is_partner_affiliate' => 1,
            'status' => 1,
            'deleted' => 0,
            'code' => $customer_code
        ])->select(['id', 'level_partner_affiliate'])->first();

        if(empty($partner_affiliate_info)) {
            return $this->System->getResponse([MESSAGE => __d('template', 'khong_tim_thay_thong_tin_doi_tac')]);
        }

        $level_partner = TableRegistry::get('CustomersAffiliate')->checkLevelForPartner($partner_affiliate_info['id']);

        //kiểm tra xem có thay đổi thứ hạng đối tác
        if($partner_affiliate_info['level_partner_affiliate'] == $level_partner) {
            return $this->System->getResponse([CODE => SUCCESS]);
        }

        $partner_affiliate = $customers_table->patchEntity($partner_affiliate_info, ['level_partner_affiliate' => $level_partner], ['validate' => false]);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            // save data
            $save = $customers_table->save($partner_affiliate);

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
}
