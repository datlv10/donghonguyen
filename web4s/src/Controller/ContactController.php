<?php

namespace App\Controller;

use Cake\ORM\TableRegistry;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;

class ContactController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

	public function sendInfo() 
	{
        $this->layout = false;
        $this->autoRender = false;

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('template', 'phuong_thuc_khong_hop_le')]);
        }

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];    
        if(empty($data)){
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        // check recaptcha
        $token = !empty($data[TOKEN_RECAPTCHA]) ? $data[TOKEN_RECAPTCHA] : null;
        $check_recaptcha = $this->loadComponent('ReCaptcha')->check($token);
        if($check_recaptcha[CODE] != SUCCESS){
            $this->responseJson([MESSAGE => $check_recaptcha[MESSAGE]]);
        }

        $form_code = !empty($data['form_code']) ? $data['form_code'] : null;
        if(empty($form_code)){
            $this->responseJson([MESSAGE => __d('template', 'vui_long_cau_hinh_ma_form')]);
        }

        $table = TableRegistry::get('Contacts');
        
        $form_info = TableRegistry::get('ContactsForm')->find()->where(['code' => $form_code, 'deleted' => 0])->first();
        if(empty($form_info)){
            $this->responseJson([MESSAGE => __d('template', 'ma_form_duoc_cau_hinh_khong_ton_tai_tren_he_thong')]);
        }

        $fields = !empty($form_info['fields']) ? json_decode($form_info['fields'], true) : [];
        if(empty($fields) || !is_array($fields)){
            $this->responseJson([MESSAGE => __d('template', 'vui_long_cau_hinh_cac_truong_cua_form')]);
        }

        $data_value = [];
        foreach ($fields as $key => $field) {
            $code = !empty($field['code']) ? $field['code'] : null;
            if(empty($code)) continue;
            $data_value[$code] = !empty($data[$code]) ? $data[$code] : null;
        }

        $data_save = [
            'form_id' => !empty($form_info['id']) ? $form_info['id'] : null,
            'value' => !empty($data_value) ? json_encode($data_value) : null,
            'status' => 2,
            'search_unicode' => strtolower($this->loadComponent('Utilities')->formatSearchUnicode($data_value))
        ];

        $contact = $table->newEntity($data_save);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();
                        
            $save = $table->save($contact);

            if (empty($save->id)){
                throw new Exception();
            }

            if(!empty($save) && !empty($form_info['send_email']) && !empty($form_info['template_email_code'])) {
                $settings = TableRegistry::get('Settings')->getSettingWebsite();
                $email_management = !empty($settings['email']['email_administrator']) ? $settings['email']['email_administrator'] : null;
                $params_email = [
                    'to_email' => $email_management,
                    'code' => $form_info['template_email_code'],
                    'id_record' => $save['id']
                ];
  
                $send = $this->loadComponent('Email')->send($params_email);
            }
            
            $conn->commit();

            $this->loadComponent('SendMessage')->send(CONTACT, $save['id']);
            
            $this->responseJson([
                CODE => SUCCESS, 
                MESSAGE => __d('template', 'gui_thong_tin_lien_he_thanh_cong')
            ]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

}