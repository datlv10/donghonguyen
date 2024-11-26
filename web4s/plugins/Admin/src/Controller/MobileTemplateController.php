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
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;
use Cake\Database\Schema\TableSchema;
use Cake\Database\Schema\Collection;
use ZipArchive;

class MobileTemplateController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function list()
    {
        // chỉ cho phép tài khoản root vào chức năng này
        $user = $this->Auth->user();
        if(empty($user['supper_admin'])){
            $this->showErrorPage('denied');
        }
        
        $template = TableRegistry::get('MobileTemplate')->find()->where()->toArray();

        $this->js_page = '/assets/js/pages/mobile_template_list.js';

        $this->set('template', $template);        

        $this->set('path_menu', 'mobile_app');
        $this->set('title_for_layout', __d('admin', 'danh_sach_giao_dien'));
    }

    public function loadFormExportTemplate()
    {
        $this->viewBuilder()->enableAutoLayout(false);

        $data = $this->getRequest()->getData();

        $template_id = !empty($data['template_id']) ? intval($data['template_id']) : null;
        if (!$this->getRequest()->is('post') || empty($template_id)) exit;

        $template = TableRegistry::get('MobileTemplate')->find()->where(['MobileTemplate.id' => $template_id])->first();
        if(empty($template)) exit;

        $this->set('template', $template);
        $this->render('view_form_export_template');
    }

    public function exportTemplate()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        } 

        $result = $this->loadComponent('Admin.MobileTemplate')->exportTemplate($data);
        $this->responseJson($result);  
    }

    public function importTemplate()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        // validate params 
        $template_file = !empty($data['template_file']) ? $data['template_file'] : null;
        $set_default = !empty($data['set_default']) ? 1 : 0;
        if(empty($template_file)){
            $this->responseJson([MESSAGE => __d('admin', 'vui_long_chon_tep_giao_dien')]);
        }    

        $result = $this->loadComponent('Admin.MobileTemplate')->installationTemplate($template_file, [
            'set_default' => $set_default
        ]);

        $this->responseJson($result);
    }

    public function checkExistTemplate()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        $template_code = !empty($data['template_code']) ? trim($data['template_code']) : null;
        if (!$this->getRequest()->is('post') || empty($template_code)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $template_info = TableRegistry::get('MobileTemplate')->find()->where(['code' => $template_code])->first();

        $this->responseJson([
            CODE => SUCCESS, 
            DATA => [
                'exist' => !empty($template_info) ? true : false
            ]
        ]);
    }

    public function setDefault()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        $id = !empty($data['id']) ? intval($data['id']) : null;

        if (!$this->getRequest()->is('post') || empty($id)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }
        
        $data['is_default'] = 1;

        $table = TableRegistry::get('MobileTemplate');   
        
        $template = $table->get($id);
        $template = $table->patchEntity($template, $data);

        try{
            $table->updateAll(
                [  
                    'is_default' => 0
                ],
                [  
                    'is_default' => 1
                ]
            );

            $save = $table->save($template);
            if(empty($save->id)) {
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
        $id = !empty($data['id']) ? intval($data['id']) : null;

        if (!$this->getRequest()->is('post') || empty($id)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $template_info = TableRegistry::get('MobileTemplate')->find()->where(['id' => $id])->first();
        $code = !empty($template_info['code']) ? $template_info['code'] : null;
        if(empty($code)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_giao_dien')]);
        }    

        $result = $this->loadComponent('Admin.MobileTemplate')->deleteTemplate($code);
        $this->responseJson($result);
    }

    public function customize()
    {
        // get info template default
        $template_default = TableRegistry::get('MobileTemplate')->getTemplateDefault();
        $template_code = !empty($template_default['code']) ? $template_default['code'] : null;
        $template_name = !empty($template_default['name']) ? $template_default['name'] : null;

        // get list block
        $list_block = [];
        if(!empty($template_code)){
            $list_block = TableRegistry::get('MobileTemplateBlock')->queryListMobileBlocks([FILTER => ['template_code' => $template_code]])->toArray();    
        }        

    	$this->css_page = [
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.css',
        ];

        $this->js_page = [
        	'/assets/plugins/custom/jquery-ui/jquery-ui.bundle.js',
            '/assets/js/pages/mobile_template_customize.js'
        ];

        $this->set('template_code', $template_code);
        $this->set('template_name', $template_name);
        $this->set('list_block', $list_block);

        $this->set('path_menu', 'mobile_app');
    	$this->set('title_for_layout', __d('admin', 'cai_dat_giao_dien_mobile_app'));
    }

    public function loadStructurePage()
    {
        $this->viewBuilder()->enableAutoLayout(false);
        $data = $this->getRequest()->getData();

        $code = !empty($data['code']) ? $data['code'] : null;
        $page_info = TableRegistry::get('MobileTemplatePage')->getInfoPage([
            'template_code' => CODE_MOBILE_TEMPLATE,
            'code' => $code
        ]);
    
        if(empty($page_info))die;

        $row_table = TableRegistry::get('MobileTemplateRow');
        $block_table = TableRegistry::get('MobileTemplateBlock');
        // get structure
        $structure = [];

        $rows = TableRegistry::get('MobileTemplateRow')->find()->where([
            'MobileTemplateRow.template_code' => CODE_MOBILE_TEMPLATE,
            'MobileTemplateRow.page_code' => $code
        ])
        ->order('MobileTemplateRow.id ASC')
        ->toArray();

        if(!empty($rows)){
            foreach($rows as $row){
                $list_blocks = [];
                $blocks = !empty($row['block_code']) ? explode(',', $row['block_code']) : [];
                if(empty($blocks)) continue;

                foreach($blocks as $block_code){
                    $list_blocks[] = $block_table->getInfoBlock($block_code);
                }

                $structure[] = $list_blocks;
            }
        }

        $this->set('page_info', $page_info);
        $this->set('structure', $structure);
        $this->render('view_structure_page');
    }

    public function saveCustomize($id = null)
    {
        $this->autoRender = false;

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        if(empty(CODE_MOBILE_TEMPLATE)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_giao_dien')]);
        }

        $data = $this->getRequest()->getData();

        $template_config = !empty($data['config']) ? json_decode($data['config'], true) : [];
        $page_code = !empty($data['page']) ? $data['page'] : null;

        if (empty($page_code) || empty($template_config)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $row_table = TableRegistry::get('MobileTemplateRow');

        // format data before save
        $data_row = [];
        foreach ($template_config as $key => $blocks) {
            if(empty($blocks) || !is_array($blocks)) continue;

            $block_code = implode(',', $blocks);
            $data_row[] = [
                'template_code' => CODE_MOBILE_TEMPLATE,
                'page_code' => $page_code,
                'block_code' => $block_code
            ];
        }

        if(empty($data_row)){
            $this->responseJson([MESSAGE => __d('admin', 'trang_cau_hinh_khong_duoc_de_trang')]);
        }

        $rows_entities = $row_table->newEntities($data_row);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            // clear old data
            $clear_row = $row_table->deleteAll([
                'template_code' => CODE_MOBILE_TEMPLATE,
                'page_code' => $page_code
            ]);   

            // save data
            $save_row = $row_table->saveMany($rows_entities);
            if (empty($save_row)){
                throw new Exception();
            }

            $conn->commit();

            $this->responseJson([CODE => SUCCESS]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function loadInfoPage()
    {
    	$this->viewBuilder()->enableAutoLayout(false);

        $data = $this->getRequest()->getData();

        if (!$this->getRequest()->is('post')) die;

        $code = !empty($data['code']) ? $data['code'] : null;

        // get page info
        $page_info = [];
        if(!empty($code)){
            $page_info = TableRegistry::get('MobileTemplatePage')->find()->where([
                'MobileTemplatePage.template_code' => CODE_MOBILE_TEMPLATE,
                'MobileTemplatePage.code' => $code
            ])->group('MobileTemplatePage.id')->first();
        }

        $type_category = !empty($page_info['type']) ? $page_info['type'] : null;
        if(strpos($type_category, '_detail') > -1){
        	$type_category = substr($type_category, 0, strpos($type_category, '_detail'));
        }

        $config = !empty($page_info['config']) ? json_decode($page_info['config'], true) : null;
        $page_info['config'] = $config;

        $this->set('page_info', $page_info);
        $this->set('type_category', $type_category);
        $this->render('view_page_info');
    }

    public function loadDropdownCategory($type = null)
    {
    	$this->viewBuilder()->enableAutoLayout(false);

    	$data = $this->getRequest()->getData();

    	if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $language = !empty($data['language']) ? $data['language'] : null;

        $this->set('language', !empty($language) ? $language : $this->lang);
    	$this->set('type_category', $type);
    }

    public function loadDropdownPage()
    {
        $this->viewBuilder()->enableAutoLayout(false);

        $data = $this->getRequest()->getData();

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $code = !empty($data['code']) ? $data['code'] : null;

        $this->set('code', $code);
    }

    public function savePage()
    {
        $this->autoRender = false;

    	$data = $this->getRequest()->getData();
    	if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        if(empty(CODE_MOBILE_TEMPLATE)){
        	$this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_giao_dien')]);
        }

        $code = !empty($data['code']) ? $data['code'] : null;
        $type = !empty($data['type']) ? $data['type'] : null;
        $name = !empty($data['name']) ? $data['name'] : null;
        $configs = !empty($data['config']) ? $data['config'] : null;

        if(empty($type)){
        	$this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        if($type == HOME){
            $home_page = TableRegistry::get('MobileTemplatePage')->getHomePage();
            if(!empty($home_page['code']) && $home_page['code'] != $code){
                $this->responseJson([MESSAGE => __d('admin', 'da_ton_tai_trang_nay_tren_he_thong')]);
            }
        }

        $page_info = [];
        if(!empty($code)){
            $page_info = TableRegistry::get('MobileTemplatePage')->find()->where([
                'MobileTemplatePage.template_code' => CODE_MOBILE_TEMPLATE,
                'MobileTemplatePage.code' => $code
            ])->group('MobileTemplatePage.id')->first();

            if (empty($page_info)) {
                $this->responseJson([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_trang')]);
            }
        }
        
        $data_config = [];
        if(!empty($configs)){    
            foreach ($configs as $k_lang => $config) {
                $data_config['page_title'][$k_lang] = $config;
            }  
   
        }

        $data_save = [
        	'template_code' => CODE_MOBILE_TEMPLATE,
        	'name' => $name,
        	'type' => $type,
            'config' => !empty($data_config) ? json_encode($data_config) : null,
        	'category_id' => !empty($data['category_id']) ? $data['category_id'] : null
        ];

        // merge data with entity                
        if(empty($code)){
        	$data_save['code'] = strtolower($this->loadComponent('Utilities')->generateRandomString(7));
            $page = TableRegistry::get('MobileTemplatePage')->newEntity($data_save);
        }else{        
            $page = TableRegistry::get('MobileTemplatePage')->patchEntity($page_info, $data_save);
        }

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();
            
            $save = TableRegistry::get('MobileTemplatePage')->save($page);
            if (empty($save->id)){
                throw new Exception();
            }

            $conn->commit();
            $this->responseJson([CODE => SUCCESS, DATA => ['code' => $save->code]]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function loadConfigPage()
    {
    	$this->autoRender = false;

        $data = $this->getRequest()->getData();

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $code = !empty($data['code']) ? $data['code'] : null;
        if(empty($code)){
        	$this->responseJson([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_trang')]);
        }

        $result = TableRegistry::get('MobileTemplatePage')->find()->where([
            'MobileTemplatePage.template_code' => CODE_MOBILE_TEMPLATE,
            'MobileTemplatePage.code' => $code
        ])->group('MobileTemplatePage.id')->select(['code', 'name'])->first();

        $this->responseJson([
        	CODE => SUCCESS, 
        	DATA => $result
        ]);
    }

    public function deletePage()
    {
    	$this->autoRender = false;
    	
    	if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $data = $this->getRequest()->getData();
        $code = !empty($data['code']) ? $data['code'] : null;
        if (empty($code)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        if(empty(CODE_MOBILE_TEMPLATE)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_giao_dien')]);
        }

        $page = TableRegistry::get('MobileTemplatePage')->find()->where([
            'MobileTemplatePage.template_code' => CODE_MOBILE_TEMPLATE,
            'MobileTemplatePage.code' => $code
        ])->first();

        if(empty($page)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_trang')]);   
        }

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $delete_row = TableRegistry::get('MobileTemplateRow')->deleteAll([
                'template_code' => CODE_MOBILE_TEMPLATE,
                'page_code' => $code
            ]);

            $delete_page = TableRegistry::get('MobileTemplatePage')->delete($page);

            $conn->commit();
            $this->responseJson([CODE => SUCCESS, DATA => []]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function deleteConfigPage()
    {
    	$this->autoRender = false;
    	
    	if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $data = $this->getRequest()->getData();
        $code = !empty($data['code']) ? $data['code'] : null;
        if (empty($code)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $page = TableRegistry::get('MobileTemplatePage')->getInfoPage([
            'template_code' => CODE_MOBILE_TEMPLATE,
            'code' => $code
        ]);
        
        if(empty($page)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_trang')]);   
        }

        $rows = TableRegistry::get('MobileTemplateRow')->find()->where([
            'template_code' => CODE_MOBILE_TEMPLATE,
            'page_code' => $code
        ])->toArray();

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();
            if(!empty($rows)){
                $delete_row = TableRegistry::get('MobileTemplateRow')->deleteMany($rows);
            }
            
            $conn->commit();
            $this->responseJson([CODE => SUCCESS, DATA => []]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function config()
    {
        $template_default = TableRegistry::get('MobileTemplate')->getTemplateDefault();

        $config = !empty($template_default['config']) ? json_decode($template_default['config'], true) : [];

        $this->set('color', !empty($config['color']) ? $config['color'] : []);
        $this->set('product', !empty($config['product']) ? $config['product'] : []);
        $this->set('advanced_search', !empty($config['advanced_search']) ? $config['advanced_search'] : []);
        $this->set('link_policy', !empty($config['link_policy']) ? $config['link_policy'] : []);

        $this->css_page = '/assets/plugins/jquery-minicolors/css/jquery.minicolors.css';
        $this->js_page = [
            '/assets/plugins/jquery-minicolors/js/jquery.minicolors.min.js',
            '/assets/js/pages/config_mobile_template.js'
        ];

        $this->set('path_menu', 'mobile_app');
        $this->set('title_for_layout', __d('admin', 'cau_hinh_chung'));
    }

    public function colorConfig()
    {
        $this->autoRender = false;

        $data = $this->getRequest()->getData();    

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }
            
        $table = TableRegistry::get('MobileTemplate');

        $template_info = $table->find()->where([
            'MobileTemplate.code' => CODE_MOBILE_TEMPLATE
        ])->select(['id', 'config'])->first();
        if(empty($template_info)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_giao_dien')]);   
        }

        $config = !empty($template_info['config']) ? json_decode($template_info['config'], true) : [];
        $config['color'] = $data;

        $entity = $table->patchEntity($template_info, ['config' => json_encode($config)]);

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

    public function productConfig()
    {
        $this->autoRender = false;

        $data = $this->getRequest()->getData();    

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }
            
        $table = TableRegistry::get('MobileTemplate');

        $template_info = $table->find()->where([
            'MobileTemplate.code' => CODE_MOBILE_TEMPLATE
        ])->select(['id', 'config'])->first();
        if(empty($template_info)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_giao_dien')]);   
        }

        $config = !empty($template_info['config']) ? json_decode($template_info['config'], true) : [];
        $config['product'] = $data;

        $entity = $table->patchEntity($template_info, ['config' => json_encode($config)]);

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

    public function advancedSearchConfig()
    {
        $this->autoRender = false;

        $data = $this->getRequest()->getData();    

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }
            
        $table = TableRegistry::get('MobileTemplate');

        $template_info = $table->find()->where([
            'MobileTemplate.code' => CODE_MOBILE_TEMPLATE
        ])->select(['id', 'config'])->first();

        if(empty($template_info)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_giao_dien')]);   
        }

        $config = !empty($template_info['config']) ? json_decode($template_info['config'], true) : [];
        $config['advanced_search'] = $data;

        $entity = $table->patchEntity($template_info, ['config' => json_encode($config)]);

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

    public function linkPolicyConfig()
    {
        $this->autoRender = false;

        $data = $this->getRequest()->getData();    

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }
            
        $table = TableRegistry::get('MobileTemplate');

        $template_info = $table->find()->where([
            'MobileTemplate.code' => CODE_MOBILE_TEMPLATE
        ])->select(['id', 'config'])->first();

        if(empty($template_info)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_giao_dien')]);   
        }

        $config = !empty($template_info['config']) ? json_decode($template_info['config'], true) : [];
        $config['link_policy'] = $data;

        $entity = $table->patchEntity($template_info, ['config' => json_encode($config)]);

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

    public function media()
    {
        $template_info = TableRegistry::get('MobileTemplate')->getTemplateDefault();
        $images = !empty($template_info['images']) ? json_decode($template_info['images'], true) : [];
                
        $this->set('images', $images);
        $this->js_page = '/assets/js/pages/mobile_template_media.js';
        $this->set('path_menu', 'mobile_app');
        $this->set('title_for_layout', __d('admin', 'thu_vien_anh'));
    }

    public function saveMedia()
    {
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }
            
        $table = TableRegistry::get('MobileTemplate');

        $template_info = $table->find()->first();

        if (empty($template_info)) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_ton_tai_thong_tin_app')]);
        }

        $entity = $table->patchEntity($template_info, ['images' => json_encode($data)]);

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

    public function text()
    {
        $template_info = TableRegistry::get('MobileTemplate')->getTemplateDefault();
        $text = !empty($template_info['text']) ? json_decode($template_info['text'], true) : [];

        $this->set('text', !empty($text) ? $text : []);

        $this->js_page = [
            '/assets/js/pages/mobile_template_text.js'
        ];

        $this->set('path_menu', 'mobile_app');
        $this->set('title_for_layout', __d('admin', 'nhan_giao_dien'));
    }

    public function saveText()
    {
        $this->autoRender = false;

        $data = $this->getRequest()->getData();    

        if (!$this->getRequest()->is('post') && empty($data)) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }
            
        $table = TableRegistry::get('MobileTemplate');

        $template_info = $table->find()->where([
            'MobileTemplate.code' => CODE_MOBILE_TEMPLATE
        ])->select(['id', 'text'])->first();

        if(empty($template_info)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_giao_dien')]);   
        }

        $text = !empty($template_info['text']) ? json_decode($template_info['text'], true) : [];
        
        $data_save = [];
        foreach ($data['text'] as $key => $item_text) {
            if (!empty($item_text)) {
                $item_text = json_decode($item_text, true);
            }
            
            if (!empty($item_text['code'])) {
                $data_save[$item_text['code']] = $item_text['text'];
            }   
        }

        $entity = $table->patchEntity($template_info, ['text' => json_encode($data_save)]);

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