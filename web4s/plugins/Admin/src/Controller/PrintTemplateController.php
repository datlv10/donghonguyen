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

class PrintTemplateController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function printConfig()
    {
        $this->js_page = [
            '/assets/js/pages/setting_print.js',
            '/assets/plugins/global/ace/ace.js',
            '/assets/plugins/global/ace/theme-monokai.js',
            '/assets/plugins/global/ace/mode-json.js',
            '/assets/plugins/global/ace/mode-html.js',
            '/assets/plugins/global/ace/mode-smarty.js',
            '/assets/plugins/global/ace/ext-language_tools.js',
        ];

        $this->set('title_for_layout', __d('admin', 'cau_hinh_mau_in'));
        $this->set('path_menu', 'setting');
    }

    public function loadInfoTemplate()
    {
        $this->viewBuilder()->enableAutoLayout(false);

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        $template_code = !empty($data['code']) ? $data['code'] : null;

        $template_info = TableRegistry::get('PrintTemplates')->getPrintTemplateBycode($template_code);

        $file_content = null;
        if(!empty($template_info['template'])){
            $path_template = TableRegistry::get('Templates')->getPathTemplate();
            $file = new File($path_template . 'Print' . DS . $template_info['template'], false);

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
        $file = new File($path_template . 'Print' . DS . $template_file, false);

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
            $this->responseJson([MESSAGE => __d('admin', 'vui_long_chon_mau_in')]);
        }

        $path_template = TableRegistry::get('Templates')->getPathTemplate();
        $file = new File($path_template . 'Print' . DS . $data['view_template'], false);
        if(!$file->exists()){
            $this->responseJson([MESSAGE => __d('admin', 'khong_ton_tai_file_mau_in')]);
        }

        $template_content = !empty($data['template_content']) ? $data['template_content'] : '';        
        try{
            $file->write($template_content, 'w', true);
        } catch (Exception $e) {
            $this->responseJson([MESSAGE => $e->getMessage()]);
        }

        $this->responseJson([CODE => SUCCESS]);
    }

    public function savePrintTemplate() 
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $table = TableRegistry::get('PrintTemplates');
        if(empty($data['template_code'])){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_mau_in')]);
        }

        $template_info = $table->getPrintTemplateBycode($data['template_code']);
        if(empty($template_info)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_mau_in')]);
        }

        if(empty($data['title_print'])){
            $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_ten_mau_in')]);
        }

        if(empty($data['template'])){
            $this->responseJson([MESSAGE => __d('admin', 'vui_long_chon_mau_in')]);
        }

        $data_save = [
            'title_print' => !empty($data['title_print']) ? trim($data['title_print']) : null,
            'template' => !empty($data['template']) ? $data['template'] : null
        ];

        $entity = $table->patchEntity($template_info, $data_save);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $save = $table->save($entity);
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
    
}