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

class TemplateController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function dashboard() 
    {
        $this->set('path_menu', 'template');
        $this->set('title_for_layout', __d('admin', 'quan_ly_giao_dien'));
        $this->render('dashboard');
    }

    public function list()
    {
        // chỉ cho phép tài khoản root vào chức năng này
        $user = $this->Auth->user();
        if(empty($user['supper_admin'])){
            $this->showErrorPage('denied');
        }

        $template = TableRegistry::get('Templates')->find()->where()->toArray();

        $this->js_page = '/assets/js/pages/template_list.js';

        $this->set('template', $template);        

        $this->set('path_menu', 'template');
        $this->set('title_for_layout', __d('admin', 'danh_sach_giao_dien'));
    }

    public function loadFormExportTemplate()
    {
        $this->viewBuilder()->enableAutoLayout(false);

        $data = $this->getRequest()->getData();

        $template_id = !empty($data['template_id']) ? intval($data['template_id']) : null;
        if (!$this->getRequest()->is('post') || empty($template_id)) exit;

        $template = TableRegistry::get('Templates')->find()->where(['Templates.id' => $template_id])->first();
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

        $result = $this->loadComponent('Admin.Template')->exportTemplate($data);
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

        $result = $this->loadComponent('Admin.Template')->installationTemplate($template_file, [
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

        $template_info = TableRegistry::get('Templates')->find()->where(['code' => $template_code])->first();

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

        $table = TableRegistry::get('Templates');   
        
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

        $template_info = TableRegistry::get('Templates')->find()->where(['id' => $id])->first();
        $code = !empty($template_info['code']) ? $template_info['code'] : null;
        if(empty($code)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_giao_dien')]);
        }    

        $result = $this->loadComponent('Admin.Template')->deleteTemplate($code);

        $this->responseJson($result);
    }

    public function customize()
    {
        // get info template default
        $template_default = TableRegistry::get('Templates')->getTemplateDefault();
        $template_code = !empty($template_default['code']) ? $template_default['code'] : null;
        $template_name = !empty($template_default['name']) ? $template_default['name'] : null;

        // get list block
        $list_block = [];
        if(!empty($template_code)){
            $list_block = TableRegistry::get('TemplatesBlock')->queryListBlocks([FILTER => ['template_code' => $template_code]])->toArray();    
        }        

    	$this->css_page = [
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.css',
        ];

        $this->js_page = [
        	'/assets/plugins/custom/jquery-ui/jquery-ui.bundle.js',
            '/assets/js/pages/template_customize.js'
        ];

        $this->set('template_code', $template_code);
        $this->set('template_name', $template_name);
        $this->set('list_block', $list_block);

        $this->set('path_menu', 'template');
    	$this->set('title_for_layout', __d('admin', 'cai_dat_giao_dien'));
    }

    public function loadStructurePage()
    {
        $this->viewBuilder()->enableAutoLayout(false);
        $data = $this->getRequest()->getData();

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $code = !empty($data['code']) ? $data['code'] : null;
        $device = isset($data['device']) ? intval($data['device']) : 0;
        if(empty($code)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_trang')]);
        }

        $row_table = TableRegistry::get('TemplatesRow');
        $block_table = TableRegistry::get('TemplatesBlock');

        // get structure of page
        $structure_page = $row_table->getStructureRowOfPage($code, $device);
        $structure = !empty($structure_page['structure']) ? $structure_page['structure'] : [];

        // get info list block
        $blocks = !empty($structure_page['blocks']) ? $structure_page['blocks'] : [];        

        // get info layout
        $layout_info = [];
        $layout_code = !empty($structure_page['layout_code']) ? $structure_page['layout_code'] : null;     
        if(!empty($layout_code)){
            $layout_info = TableRegistry::get('TemplatesPage')->find()->where([
                'TemplatesPage.template_code' => CODE_TEMPLATE,
                'TemplatesPage.code' => $layout_code,
            ])->first();
        }
        // get url of page

        $page_url = TableRegistry::get('TemplatesPageContent')->find()->where([
            'template_code' => CODE_TEMPLATE,
            'page_code' => $code,
            'lang' => $this->lang
        ])->select(['url'])->first();

        $this->set('url', !empty($page_url['url']) ? $page_url['url'] : null);
        $this->set('structure', $structure);
        $this->set('blocks', $blocks);
        $this->set('layout_info', $layout_info);
        $this->render('view_structure_page');
    }

    public function save($id = null)
    {
        $this->autoRender = false;

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $data = $this->getRequest()->getData();
        $template_config = !empty($data['config']) ? json_decode($data['config'], true) : [];
        $page_code = !empty($data['page']) ? $data['page'] : null;
        $device = isset($data['device']) ? intval($data['device']) : 0;

        if (empty($page_code) || empty($template_config)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        if(empty(CODE_TEMPLATE)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_giao_dien')]);
        }

        $table = TableRegistry::get('TemplatesPage');
        $row_table = TableRegistry::get('TemplatesRow');
        $column_table = TableRegistry::get('TemplatesColumn');
        $block_table = TableRegistry::get('TemplatesBlock');
        $utilities = $this->loadComponent('Utilities');

        // format data before save
        $data_row = [];
        $data_column = [];
        foreach ($template_config as $type => $rows) {
            if(empty($rows)) continue;
            
            foreach ($rows as $k_row => $row) {
                $row_code = !empty($row['code']) ? $row['code'] : strtolower($utilities->generateRandomString(7));
                $row_item = [
                    'template_code' => CODE_TEMPLATE,
                    'page_code' => $page_code,
                    'code' => $row_code,
                    'type' => $type,
                    'config' => !empty($row['config']) ? $row['config'] : null,
                    'device' => $device
                ];
                $data_row[] = $row_item;

                $columns = !empty($row['columns']) ? $row['columns'] : [];
                if(empty($columns)) continue;

                foreach ($columns as $k_column => $column) {
                    $column_item = [
                        'template_code' => CODE_TEMPLATE,
                        'page_code' => $page_code,
                        'row_code' => $row_code,
                        'column_value' => !empty($column['column_value']) ? intval($column['column_value']) : null,
                        'block_code' => !empty($column['block']) ? implode(',', $column['block']) : null,
                        'device' => $device
                    ];

                    $data_column[] = $column_item;
                }
            }
        }

        if(empty($data_row)){
            $this->responseJson([MESSAGE => __d('admin', 'trang_cau_hinh_khong_duoc_de_trang')]);
        }

        $rows_entities = $row_table->newEntities($data_row);
        $columns_entities = $column_table->newEntities($data_column);

        // lấy thông tin page trước khi cập nhật để lưu vào log
        $page_info = TableRegistry::get('TemplatesPage')->find()->contain([
            'TemplatesRow', 
            'TemplatesColumn'
        ])->where([
            'code' => $page_code
        ])->first();

        if(empty($page_info)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_trang')]);
        }

        $data_save = [
            'TemplatesRow' => $data_row,
            'TemplatesColumn' => $data_column
        ];
        $entity = $table->patchEntity($page_info, $data_save);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            // clear old data
            $clear_row = $row_table->deleteAll([
                'template_code' => CODE_TEMPLATE,
                'page_code' => $page_code,
                'device' => $device
            ]);
            
            $clear_column = $column_table->deleteAll([
                'template_code' => CODE_TEMPLATE,
                'page_code' => $page_code,
                'device' => $device
            ]);    

            // save data
            $save = $table->save($entity);
            if(empty($save['id'])){
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

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $code = !empty($data['code']) ? $data['code'] : null;

        // get page info
        $page_info = [];
        if(!empty($code)){
            $page_info = TableRegistry::get('TemplatesPage')->find()->contain(['ContentMutiple'])
            ->where([
                'TemplatesPage.template_code' => CODE_TEMPLATE,
                'TemplatesPage.code' => $code
            ])->group('TemplatesPage.id')->first();

            $links = [];
            if(!empty($page_info['ContentMutiple'])){
                foreach ($page_info['ContentMutiple'] as $k => $content) {
                    $links[$content['lang']] = $content['url'];
                }
            }

            unset($page_info['ContentMutiple']);
            $page_info['url'] = $links;
        }

        $type_category = !empty($page_info['type']) ? $page_info['type'] : '';
        if(strpos($type_category, '_detail') > -1){
        	$type_category = substr($type_category, 0, strpos($type_category, '_detail'));
        }

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

        if(empty(CODE_TEMPLATE)){
        	$this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_giao_dien')]);
        }

        $code = !empty($data['code']) ? $data['code'] : null;
        $type = !empty($data['type']) ? $data['type'] : null;
        $name = !empty($data['name']) ? $data['name'] : null;
        $links = !empty($data['link']) ? $data['link'] : [];
        $layout_code = !empty($data['layout_code']) ? $data['layout_code'] : null;

        if(empty($type)){
        	$this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $page_info = [];
        if(!empty($code)){
            $page_info = TableRegistry::get('TemplatesPage')->find()->contain(['ContentMutiple'])->where([
                'TemplatesPage.template_code' => CODE_TEMPLATE,
                'TemplatesPage.code' => $code,
                'TemplatesPage.page_type' => PAGE
            ])->group('TemplatesPage.id')->first();

            if (empty($page_info)) {
                $this->responseJson([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_trang')]);
            }
        }

        // check link
        $page_content = [];
        $content = !empty($page_info['ContentMutiple']) ? Hash::combine($page_info['ContentMutiple'], '{n}.lang', '{n}.id') : [];

        if(in_array($type, [MEMBER, ORDER, TAG])){
            $where = [
                'TemplatesPage.template_code' => CODE_TEMPLATE,
                'TemplatesPage.type' => $type
            ];
            if(!empty($code)){
                $where['TemplatesPage.code <>'] = $code;
            }
            $exist_page = TableRegistry::get('TemplatesPage')->find()->where($where)->first();
        	if(!empty($exist_page)){
        		$this->responseJson([MESSAGE => __d('admin', 'da_ton_tai_trang_nay_tren_he_thong')]);
        	}

            $languages = TableRegistry::get('Languages')->getList();
            foreach ($languages as $k_lang => $language) {
                $item = [
                    'id' => !empty($content[$k_lang]) ? intval($content[$k_lang]) : null,
                    'template_code' => CODE_TEMPLATE,
                    'url' => null,
                    'lang' => $k_lang
                ];

                if(empty($code)){
                    $item['seo_title'] = $name;
                }

                $page_content[] = $item;
            }

        }else{
        	if(empty($links)){
	        	$this->responseJson([MESSAGE => __d('admin', 'duong_dan_khong_hop_le')]);
	        }

            // parse data page content            
            foreach ($links as $k_lang => $link) {
                $check_link = TableRegistry::get('TemplatesPageContent')->checkExistUrl($link, $code);
                if($check_link){
                    $this->responseJson([MESSAGE => __d('admin', 'duong_dan_da_ton_tai_vui_long_nhap_lai')]);
                }

                $item = [
                    'id' => !empty($content[$k_lang]) ? intval($content[$k_lang]) : null,
                    'template_code' => CODE_TEMPLATE,
                    'url' => $link,
                    'lang' => $k_lang
                ];

                if(empty($code)){
                    $item['seo_title'] = $name;
                }

                $page_content[] = $item;
            }	        
        }

        $data_save = [
        	'template_code' => CODE_TEMPLATE,
        	'layout_code' => $layout_code,
        	'name' => $name,
        	'page_type' => PAGE,
        	'type' => $type,
        	'category_id' => !empty($data['category_id']) ? $data['category_id'] : null,
        	'ContentMutiple' => $page_content
        ];

        // merge data with entity                
        if(empty($code)){
        	$data_save['code'] = strtolower($this->loadComponent('Utilities')->generateRandomString(7));
            $page = TableRegistry::get('TemplatesPage')->newEntity($data_save, ['associated' => ['ContentMutiple']]);
        }else{        
            $page = TableRegistry::get('TemplatesPage')->patchEntity($page_info, $data_save);
        }

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();
            
            $save = TableRegistry::get('TemplatesPage')->save($page);
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

    public function loadInfoLayout()
    {
    	$this->viewBuilder()->enableAutoLayout(false);
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $data = $this->getRequest()->getData();    
        $code = !empty($data['code']) ? $data['code'] : null;
        $page_info = [];
        if(!empty($code)){
            $page_info = TableRegistry::get('TemplatesPage')->getInfoPage([
                'template_code' => CODE_TEMPLATE,
                'code' => $code, 
                'lang' => $this->lang
            ]);
        }

        $this->set('page_info', $page_info);
        $this->render('view_layout_info');
    }

    public function saveLayoutPage()
    {
        $this->autoRender = false;

    	$data = $this->getRequest()->getData();
    	if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        if (empty($data)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        if(empty(CODE_TEMPLATE)){
        	$this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_giao_dien')]);
        }

        $code = !empty($data['code']) ? $data['code'] : null;
        $name = !empty($data['name']) ? $data['name'] : null;
        if(empty($name)){
        	$this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $data_save = [
        	'template_code' => CODE_TEMPLATE,
        	'layout_code' => null,
        	'code' => $code,
        	'name' => $name,
        	'page_type' => LAYOUT,
        	'type' => LAYOUT,        	
        ];
        
        // merge data with entity                
        if(empty($code)){
        	$data_save['code'] = strtolower($this->loadComponent('Utilities')->generateRandomString(7));
            $page = TableRegistry::get('TemplatesPage')->newEntity($data_save);
        }else{
        	$page = TableRegistry::get('TemplatesPage')->find()->where([
                'TemplatesPage.template_code' => CODE_TEMPLATE,
				'TemplatesPage.code' => $code,
				'TemplatesPage.page_type' => LAYOUT
			])->group('TemplatesPage.id')->first();

            $page = TableRegistry::get('TemplatesPage')->patchEntity($page, $data_save);
        }

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();
            
            $save = TableRegistry::get('TemplatesPage')->save($page);
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

    public function getType()
    {
    	$this->autoRender = false;

        $data = $this->getRequest()->getData();

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $code = !empty($data['code']) ? $data['code'] : null;
        if(empty($code)){
        	$this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $result = TableRegistry::get('TemplatesPage')->find()->where([
            'TemplatesPage.template_code' => CODE_TEMPLATE,
            'TemplatesPage.code' => $code
        ])->group('TemplatesPage.id')->select(['code', 'page_type'])->first();

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

        if(empty(CODE_TEMPLATE)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_giao_dien')]);
        }

        $page = TableRegistry::get('TemplatesPage')->find()->where([
            'TemplatesPage.template_code' => CODE_TEMPLATE,
            'TemplatesPage.code' => $code
        ])->first();

        if(empty($page)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_trang')]);   
        }

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            // delete config of page
            $delete_column = TableRegistry::get('TemplatesColumn')->deleteAll([
                'template_code' => CODE_TEMPLATE,
                'page_code' => $code
            ]);

            $delete_row = TableRegistry::get('TemplatesRow')->deleteAll([
                'template_code' => CODE_TEMPLATE,
                'page_code' => $code
            ]);

            // delete page
            $delete_page_content = TableRegistry::get('TemplatesPageContent')->deleteAll([
                'template_code' => CODE_TEMPLATE,
                'page_code' => $code
            ]);
            $delete_page = TableRegistry::get('TemplatesPage')->delete($page);

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
        $device = !empty($data['device']) ? 1 : 0;
        if (empty($code)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $page = TableRegistry::get('TemplatesPage')->getInfoPage([
            'template_code' => CODE_TEMPLATE,
            'code' => $code,
            'lang' => $this->lang
        ]);
        
        if(empty($page)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_trang')]);   
        }

        $columns = TableRegistry::get('TemplatesColumn')->find()->where([
            'template_code' => CODE_TEMPLATE,
            'page_code' => $code, 
            'device' => $device
        ])->toArray();

        $rows = TableRegistry::get('TemplatesRow')->find()->where([
            'template_code' => CODE_TEMPLATE,
            'page_code' => $code, 
            'device' => $device
        ])->toArray();

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            // delete config of page
            if(!empty($columns)){
                $delete_column = TableRegistry::get('TemplatesColumn')->deleteMany($columns);
            }

            if(!empty($rows)){
                $delete_row = TableRegistry::get('TemplatesRow')->deleteMany($rows);
            }
            
            $conn->commit();
            $this->responseJson([CODE => SUCCESS, DATA => []]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function duplicatePage()
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
        
        $page_table = TableRegistry::get('TemplatesPage');
        $row_table = TableRegistry::get('TemplatesRow');
        $utilities = $this->loadComponent('Utilities');

        // get page info
        $page = $page_table->find()->contain(['ContentMutiple'])->where([
            'TemplatesPage.template_code' => CODE_TEMPLATE,
            'TemplatesPage.code' => $code,
        ])->first()->toArray();
        
        if(empty($page)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_trang')]);
        }

        if(!empty($page['type']) && $page['type'] == HOME){
            $this->responseJson([MESSAGE => __d('admin', 'khong_the_nhan_ban_trang_chu')]);   
        }
        
        // get config in page
        $rows = $row_table->find()->contain(['TemplatesColumn'])->where([
            'TemplatesRow.template_code' => CODE_TEMPLATE,
            'TemplatesRow.page_code' => $code
        ])->order('TemplatesRow.id ASC')->toArray();
        
        $page_code = strtolower($utilities->generateRandomString(7));
        $name = !empty($page['name']) ? $page['name'] : null;
        $page_name = $this->loadComponent('System')->getNameUnique('TemplatesPage', $name, 1);
        
        // format data before mere entity
        $page['id'] = null;
        $page['code'] = $page_code;
        $page['name'] = $page_name;

        if(!empty($page['ContentMutiple'])){
            foreach($page['ContentMutiple'] as $k_content => $content){
                $page['ContentMutiple'][$k_content]['url'] = $this->getUrlUniquePage($content['url'], 1);
                $page['ContentMutiple'][$k_content]['page_code'] = $page_code;
                $page['ContentMutiple'][$k_content]['id'] = null;
            }
        }

        $data_rows = [];
        if(!empty($rows)){
            foreach ($rows as $k_row => $row) {
                $data_rows[$k_row] = $row->toArray();

                $row_code = strtolower($utilities->generateRandomString(7));
                $data_rows[$k_row]['page_code'] = $page_code;
                $data_rows[$k_row]['code'] = $row_code;                
                $data_rows[$k_row]['id'] = null;

                if(empty($row['TemplatesColumn'])) continue;
                foreach ($row['TemplatesColumn'] as $k_column => $column) {
                    $data_rows[$k_row]['TemplatesColumn'][$k_column]['page_code'] = $page_code;
                    $data_rows[$k_row]['TemplatesColumn'][$k_column]['row_code'] = $row_code;
                    $data_rows[$k_row]['TemplatesColumn'][$k_column]['id'] = null;
                }
            }
        }

        $page_entity = $page_table->newEntity($page, ['associated' => ['ContentMutiple']]);
        $rows_entity = $row_table->newEntities($data_rows, ['associated' => ['TemplatesColumn']]);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            // save new page
            $dulicate_page = $page_table->save($page_entity);
            if(empty($dulicate_page)){
                throw new Exception();
            }

            // save config page
            $dulicate_config = $row_table->saveMany($rows_entity, ['associated' => ['TemplatesColumn']]);
            if (empty($dulicate_config)){
                throw new Exception();
            }

            $conn->commit();
            $this->responseJson([CODE => SUCCESS, DATA => ['code' => $page_code]]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);
        }
    }

    private function getUrlUniquePage($url = null, $index = 1)
    {
        $url_check = $url . '-'. $index;
        if($index == 100){
            return $url_check;
        }

        $check = TableRegistry::get('TemplatesPageContent')->checkExistUrl($url_check);

        if($check){
            $index ++;
            $url_check = $this->getUrlUniquePage($url, $index);
        }
        return $url_check;
    }
}