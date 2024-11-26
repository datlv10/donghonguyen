<?php

namespace Admin\Controller;

use Admin\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Utility\Hash;
use Cake\Http\Response;
use Cake\ORM\Query;
use Cake\Core\Exception\Exception;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\Datasource\ConnectionManager;
use Cake\Console\ShellDispatcher;


class EmailTemplateController extends AppController {

    public function initialize(): void
    {
        parent::initialize();        
    }

    public function email()
    {
        $group = 'email';
        $setting_info = TableRegistry::get('Settings')->find()->where(['group_setting' => $group])->toArray();  
        $config = Hash::combine($setting_info, '{n}.code', '{n}.value');

        
        $this->set('group', $group);
        $this->set('config', $config);
        $this->set('path_menu', 'setting');

        $this->js_page = [
            '/assets/js/pages/setting_email.js',
            '/assets/plugins/global/ace/ace.js',
            '/assets/plugins/global/ace/theme-monokai.js',
            '/assets/plugins/global/ace/mode-json.js',
            '/assets/plugins/global/ace/mode-html.js',
            '/assets/plugins/global/ace/mode-smarty.js',
            '/assets/plugins/global/ace/ext-language_tools.js',
        ];

        $this->set('title_for_layout', __d('admin', 'cau_hinh_email'));
        $this->render('email');
    }

    public function loadInfoTemplate()
    {
        $this->viewBuilder()->enableAutoLayout(false);

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        $template_code = !empty($data['code']) ? $data['code'] : null;

        $template_info = TableRegistry::get('EmailTemplates')->getEmailTemplateBycode($template_code);

        $file_content = null;
        if(!empty($template_info['template'])){
            $path_template = TableRegistry::get('Templates')->getPathTemplate();
            $file = new File($path_template . DS . 'email' . DS . 'html' . DS . $template_info['template'], false);
            if($file->exists()){
                $file_content = $file->read();
            }
        }

        $this->set('template_info', $template_info);
        $this->set('file_content', $file_content);
        $this->set('load_form', empty($template_code) ? false : true);

        $this->render('template_info');
    }

    public function loadViewContentFileTemplate()
    {
        $this->viewBuilder()->enableAutoLayout(false);

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        $template_file = !empty($data['template']) ? $data['template'] : null;

        $path_template = TableRegistry::get('Templates')->getPathTemplate();
        $file = new File($path_template . 'email' . DS . 'html' . DS . $template_file, false);

        $load_form = true;
        $file_content = null;
        if($file->exists()){
            $file_content = $file->read();
        }else{
            $load_form = false;
        }
        
        $this->set('file_content', $file_content);
        $this->set('load_form', $load_form);
        $this->render('template_view_content');
    }

    public function saveViewContentFileTemplate()
    {
        $this->layout = false;
        $this->autoRender = false;

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        if(empty($data['view_template'])){
            $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_chon_mau_email')]);
        }

        $path_template = TableRegistry::get('Templates')->getPathTemplate();
        $file = new File($path_template . DS . 'email' . DS . 'html' . DS . $data['view_template'], false);
        if(!$file->exists()){
            $this->responseJson([MESSAGE => __d('admin', 'khong_ton_tai_file_mau_email')]);
        }

        $template_content = !empty($data['template_content']) ? $data['template_content'] : '';        
        try{
            $file->write($template_content, 'w', true);
        } catch (Exception $e) {
            $this->responseJson([MESSAGE => $e->getMessage()]);
        }

        $this->responseJson([CODE => SUCCESS]);
    }

    public function saveEmailTemplate() 
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $table = TableRegistry::get('EmailTemplates');
        if(empty($data['template_code'])){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_mau_email')]);
        }

        $template_info = $table->getEmailTemplateBycode($data['template_code']);
        if(empty($template_info)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_mau_email')]);
        }

        if(empty($data['title_email'])){
            $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_tieu_de_email')]);
        }

        if(empty($data['template'])){
            $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_chon_mau_email')]);
        }        

        // format data before save
        $list_cc_email = !empty($data['cc_email']) ? array_column(json_decode($data['cc_email'], true), 'value') : null;
        $cc_email = !empty($list_cc_email) ? implode(', ', $list_cc_email) : null;

        $list_bcc_email = !empty($data['bcc_email']) ? array_column(json_decode($data['bcc_email'], true), 'value') : null;
        $bcc_email = !empty($list_bcc_email) ? implode(', ', $list_bcc_email) : null;

        $data_save = [
            'title_email' => !empty($data['title_email']) ? trim($data['title_email']) : null,
            'cc_email' => $cc_email,
            'bcc_email' => $bcc_email,
            'template' => !empty($data['template']) ? $data['template'] : null
        ];

        $template = $table->patchEntity($template_info, $data_save);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $save = $table->save($template);
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

    public function emailSendTry() 
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }
        
        $to_email = !empty($data['email_send_try']) ? trim($data['email_send_try']) : null;
        $type_send_try = !empty($data['type_send_try']) ? $data['type_send_try'] : null;
        $content = !empty($data['content_send_try']) ? trim($data['content_send_try']) : null;
        $template_code = !empty($data['template_code']) ? $data['template_code'] : null;
        $id_record = !empty($data['id_record']) ? intval($data['id_record']) : null;
        if(empty($to_email)){
            $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_email_gui_thu')]);
        }

        if($type_send_try == 'content'){
            if(empty($content)){
                $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_noi_dung_gui_thu')]);
            }
        }

        if($type_send_try == 'template'){
            if(empty($template_code)){
                $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_chon_mau_email')]);
            }

            if(empty($id_record)){
                $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_id_ban_ghi_gui_thu')]);
            }
        }
        
        $params = [
            'to_email' => $to_email,
            'code' => $template_code,
            'id_record' => $id_record,
            'send_try_content' => $type_send_try == 'content' ? true : false,
            'content' => $content
        ];

        $send_email = $this->loadComponent('Email')->send($params);
        exit(json_encode($send_email));

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'gui_email_thanh_cong')
        ]);
    }
    
}