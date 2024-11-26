<?php
declare(strict_types=1);

namespace App\Controller\Api;

use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;
use ZipArchive;
use Zend\Diactoros\Stream;
use Cake\Http\Client;
use Cake\Database\Schema\TableSchema;
use Migrations\Migrations;
use Cake\Utility\Text;

class WebsiteController extends Controller {

    public function initialize(): void
    {
        parent::initialize();
    }

    // link chạy mirgate
    public function migrate()
    {
        $this->layout = false;
        $this->autoRender = false;

        $result = [
            CODE => ERROR,
            MESSAGE => 'Không thành công',
            DATA => []
        ];

        $params = $this->getRequest()->getQuery();

        // chạy migrate 
        $migrations = new Migrations();
        if(!empty($params['action']) && $params['action'] == 'rollback'){
            $run = $migrations->rollback();
        }elseif(!empty($params['action']) && $params['action'] == 'status'){
            $status = $migrations->status();
            $result[DATA] = $status;
            $run = true;
        }else{
            $run = $migrations->migrate();
        }
        
        if($run){            
            // xóa cache
            TableRegistry::get('App')->deleteAllCache();

            $result[CODE] = SUCCESS;
            $result[MESSAGE] = 'Migrations thành công';
        }
        
        $this->responseJson($result);
    }

    // link chạy cập nhật lại search unicode
    public function reUpdateSearchUnicode($type = null)
    {
        $this->layout = false;
        $this->autoRender = false;

        $result = [
            CODE => ERROR,
            MESSAGE => 'Không thành công',
            DATA => []
        ];

        $utilities = $this->loadComponent('Utilities');

        if (empty($type)) {
            $this->responseJson($result);
        }

        switch ($type) {
            case 'category':
                $table = TableRegistry::get('CategoriesContent');
                $data = $table->find()->select(['id', 'name'])->toArray();

                $patch_data = [];
                foreach ($data as $k => $item) {
                    if (empty($item['name'])) break;

                    $patch_data[] = [
                        'id' => $item['id'],
                        'search_unicode' => $utilities->formatSearchUnicode([Text::slug(strtolower($item['name']), ' ')])
                    ];
                }
                
                $data_entities = $table->patchEntities($data, $patch_data, ['validate' => false]);
                $conn = ConnectionManager::get('default');
                try{
                    $conn->begin();

                    $update_search_unicode = $table->saveMany($data_entities);
                    if (empty($update_search_unicode)){
                        throw new Exception();
                    }

                    $conn->commit();
                    $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'cập nhật search_unicode cho danh mục thành công')]);

                }catch (Exception $e) {
                    $conn->rollback();
                    $this->responseJson([MESSAGE => $e->getMessage()]);
                }

            break;

            case 'products':
                $table = TableRegistry::get('ProductsContent');
                $data = $table->find()->select(['id', 'name'])->toArray();
                
                $patch_data = [];
                foreach ($data as $k => $item) {
                    if (empty($item['name'])) break;

                    $patch_data[] = [
                        'id' => $item['id'],
                        'search_unicode' => $utilities->formatSearchUnicode([Text::slug(strtolower($item['name']), ' ')])
                    ];
                }
                
                $data_entities = $table->patchEntities($data, $patch_data, ['validate' => false]);
                $conn = ConnectionManager::get('default');
                try{
                    $conn->begin();
                    
                    $update_search_unicode = $table->saveMany($data_entities);  
                    if (empty($update_search_unicode)){
                        throw new Exception();
                    }

                    $conn->commit();
                    $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'cập nhật search_unicode cho sản phẩm thành công')]);

                }catch (Exception $e) {
                    $conn->rollback();
                    $this->responseJson([MESSAGE => $e->getMessage()]);  
                }

            break;

            case 'article':
                $table = TableRegistry::get('ArticlesContent');
                $data = $table->find()->select(['id', 'name'])->toArray();

                $patch_data = [];
                foreach ($data as $k => $item) {
                    if (empty($item['name'])) break;

                    $patch_data[] = [
                        'id' => $item['id'],
                        'search_unicode' => $utilities->formatSearchUnicode([Text::slug(strtolower($item['name']), ' ')])
                    ];
                }
                
                $data_entities = $table->patchEntities($data, $patch_data, ['validate' => false]);
                $conn = ConnectionManager::get('default');
                try{
                    $conn->begin();

                    $update_search_unicode = $table->saveMany($data_entities);            
                    if (empty($update_search_unicode)){
                        throw new Exception();
                    }

                    $conn->commit();
                    $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'cập nhật search_unicode cho bài viết thành công')]);

                }catch (Exception $e) {
                    $conn->rollback();
                    $this->responseJson([MESSAGE => $e->getMessage()]);  
                }

            break;
        }
    }

    // link cập nhật gia hạn
    public function updateDuration()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('Settings');
        $utilities = $this->loadComponent('Utilities');
        $end_date = !empty($data['end_date']) ? $data['end_date'] : null;
        if(empty($end_date) || !$utilities->isDateClient($end_date)){
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $end_date_integer = $utilities->stringDateClientToInt($end_date);

        $setting = $table->find()->where([
            'group_setting' => 'profile',
            'code' => 'end_date'
        ])->first();

        if(empty($setting)){
            $entity = $table->newEntity([
                'group_setting' => 'profile',
                'code' => 'end_date',
                'value' => $end_date_integer
            ]);
        }else{            
            $entity = $table->patchEntity($setting, [
                'value' => $end_date_integer
            ]);
        }

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();
            
            $save = $table->save($entity);
            if (empty($save->id)){
                throw new Exception();
            }

            $conn->commit();
            $this->responseJson([
                CODE => SUCCESS,
                MESSAGE => __d('template', 'cap_nhat_thanh_cong')
            ]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    // link cập nhật dung lượng
    public function updateSize()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('Settings');
        $size = !empty($data['size']) ? floatval($data['size']) : null;
        if(empty($size)){
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $setting = $table->find()->where([
            'group_setting' => 'profile',
            'code' => 'size'
        ])->first();

        if(empty($setting)){
            $entity = $table->newEntity([
                'group_setting' => 'profile',
                'code' => 'size',
                'value' => $size
            ]);
        }else{            
            $entity = $table->patchEntity($setting, [
                'value' => $size
            ]);
        }

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();
            
            $save = $table->save($entity);
            if (empty($save->id)){
                throw new Exception();
            }

            $conn->commit();
            $this->responseJson([
                CODE => SUCCESS,
                MESSAGE => __d('template', 'cap_nhat_thanh_cong')
            ]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    // link nhận thông báo
    public function newNotification()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();

        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('NhNotifications');

        $method = !empty($data['method']) ? $data['method'] : 'send';
        $id = !empty($data['id']) ? intval($data['id']) : null;
        $type = !empty($data['type']) ? $data['type'] : null;
        $title = !empty($data['title']) ? $data['title'] : null;
        $link = !empty($data['link']) ? $data['link'] : null;

        $nh_notification = [];
        if(!empty($id)){
            $nh_notification = $table->find()->where(['crm_notification_id' => $id])->first();
        }

        // xoá thông báo
        if($method == 'delete'){
            if (empty($nh_notification)) {
                $this->responseJson([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_ban_ghi')]);
            }

            try{
                $delete = $table->delete($nh_notification);
                $this->responseJson([
                    CODE => SUCCESS,
                    MESSAGE => __d('template', 'cap_nhat_thanh_cong')
                ]);
            }catch (Exception $e) {
                $this->responseJson([MESSAGE => __d('admin', 'cap_nhat_khong_thanh_cong')]);
            }
        }
        
        // thêm hoặc cập nhật thông báo
        if(empty($type) || empty($title) || empty($link)){
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $new = empty($nh_notification) ? true : false;
        
        if($new){
            $entity = $table->newEntity([
                'type' => $type,
                'group_notification' => 'general',
                'title' => $title,
                'link' => $link,
                'crm_notification_id' => $id
            ]);
        }else{
            $entity = $table->patchEntity($nh_notification, [
                'type' => $type,
                'title' => $title,
                'link' => $link,
                'crm_notification_id' => $id
            ]);
        }

        try{
            $table->save($entity);

            $this->responseJson([
                CODE => SUCCESS,
                MESSAGE => __d('template', 'cap_nhat_thanh_cong')
            ]);

        }catch (Exception $e) {
            $this->responseJson([MESSAGE => $e->getMessage()]);
        }
    }

    public function initializationTemplate()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }
        $template_file = !empty($data['template_file']) ? $data['template_file'] : null;
        if(empty($template_file)){
            $this->responseJson([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_tep_khoi_tao')]);
        }

        $result = $this->loadComponent('Admin.Template')->installationTemplate($template_file, [
            'set_default' => true
        ]);

        $this->responseJson($result);
    }

    public function initializationConfig()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        // website info
        $website_info = !empty($data['website']) ? $data['website'] : [];
        $company_info = !empty($data['company']) ? $data['company'] : [];

        $size = !empty($website_info['size']) ? floatval($website_info['size']) : null;
        $end_date = !empty($website_info['end_date']) ? intval($website_info['end_date']) : null;
        $website_id = !empty($website_info['website_id']) ? intval($website_info['website_id']) : null;
        $cdn_url = !empty($website_info['cdn_url']) ? $website_info['cdn_url'] : null;

        if(empty($website_id) || empty($size) || empty($end_date)){
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('Settings');
        $data_settings = [
            [
                'group_setting' => 'profile',
                'code' => 'website_id',
                'value' => $website_id
            ],
            [
                'group_setting' => 'profile',
                'code' => 'person',
                'value' => !empty($website_info['person']) ? $website_info['person'] : null
            ],
            [
                'group_setting' => 'profile',
                'code' => 'phone',
                'value' => !empty($website_info['phone']) ? $website_info['phone'] : null
            ],
            [
                'group_setting' => 'profile',
                'code' => 'address',
                'value' => !empty($website_info['address']) ? $website_info['address'] : null
            ],
            [
                'group_setting' => 'profile',
                'code' => 'size',
                'value' => $size
            ],
            [
                'group_setting' => 'profile',
                'code' => 'end_date',
                'value' => $end_date
            ],
            [
                'group_setting' => 'profile',
                'code' => 'cdn_url',
                'value' => $cdn_url
            ],
            

            [
                'group_setting' => 'website_info',
                'code' => 'company_name',
                'value' => !empty($company_info['company_name']) ? $company_info['company_name'] : null
            ],
            [
                'group_setting' => 'website_info',
                'code' => 'hotline',
                'value' => !empty($company_info['company_hotline']) ? $company_info['company_hotline'] : null
            ],
            [
                'group_setting' => 'website_info',
                'code' => 'phone',
                'value' => !empty($company_info['phone']) ? $company_info['phone'] : null
            ],
            [
                'group_setting' => 'website_info',
                'code' => 'email',
                'value' => !empty($company_info['company_email']) ? $company_info['company_email'] : null
            ],
            [
                'group_setting' => 'website_info',
                'code' => 'address',
                'value' => !empty($company_info['company_address']) ? $company_info['company_address'] : null
            ],
            [
                'group_setting' => 'website_info',
                'code' => 'website_name',
                'value' => !empty($company_info['website_name']) ? $company_info['website_name'] : null
            ]            
        ];

        $data_settings = $table->newEntities($data_settings);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $clear_profile = $table->deleteAll(['group_setting' => 'profile']);
            $clear_website_info = $table->deleteAll(['group_setting' => 'website_info']);

            $save_config = $table->saveMany($data_settings);
            if (empty($save_config)){
                throw new Exception();
            }

            $conn->commit();
            $this->responseJson([CODE => SUCCESS, MESSAGE => __d('template', 'cai_dat_cau_hinh_khoi_tao_website_thanh_cong')]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function updateFileConfigDatabase()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $database_name = !empty($data['database_name']) ? trim($data['database_name']) : null;
        $database_account = !empty($data['database_account']) ? trim($data['database_account']) : null;
        $database_password = !empty($data['database_password']) ? trim($data['database_password']) : null;

        if(empty($database_name) || empty($database_account) || empty($database_password)){
            $this->responseJson([MESSAGE => __d('template', 'thong_tin_cau_hinh_database_khong_hop_le')]);
        }

        // write config database of website
        $file_config = new File(SOURCE_DOMAIN . DS . 'config_database.php', true);
        if(!$file_config->exists()){
            $this->responseJson([MESSAGE => __d('template', 'khong_tim_thay_tep_cau_hinh_database_cua_website')]);
        }

        if(!$file_config->writable()){
            $this->responseJson([MESSAGE => __d('template', 'khong_co_quyen_sua_tep_cau_hinh_database')]);   
        }

        $text_config = "<?php
            define('DB_HOST', 'localhost');
            define('DB_NAME', '$database_name');
            define('DB_USERNAME', '$database_account');
            define('DB_PASSWORD', '$database_password');";

        $file_config->write($text_config);
        $file_config->close();

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('template', 'cap_nhat_file_cau_hinh_database_thanh_cong')
        ]);
    }

    public function importStructureDatabase()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }
        
        $structure_file_response = !empty($data['file_structure']) ? $data['file_structure'] : null;       
        
        // check file structure
        if(empty($structure_file_response)){
            $this->responseJson([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_tep_cau_truc_database')]);
        }

        $file_error = $structure_file_response->getError();
        $structure_file_tmp = $structure_file_response->getStream()->getMetadata('uri');
        $file_name = $structure_file_response->getClientFilename();
        $file_type = $structure_file_response->getClientMediaType();

        if(!empty($file_error) || empty($structure_file_tmp) || empty($file_name) || $file_type != 'text/plain'){
            $this->responseJson([MESSAGE => __d('template', 'tep_khoi_tao_du_lieu_ban_dau_khong_hop_le')]);
        }        

        // read content file structure
        $file_structure = new File($structure_file_tmp, false);
        $query_structure_content = !empty($file_structure->read()) ? trim($file_structure->read()) : null;
        if(empty($query_structure_content)){
            $this->responseJson([MESSAGE => __d('template', 'khong_doc_duoc_noi_dung_tep_cau_truc_database')]);
        }
        $file_structure->close();
        
        try{
            ConnectionManager::get('default')->execute($query_structure_content);
            $this->responseJson([CODE => SUCCESS, MESSAGE => __d('template', 'cai_dat_cau_truc_du_lieu_thanh_cong')]);

        }catch (Exception $e) {
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function importInitializationDatabase()
    {
    	$this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }
        
        $data_file_response = !empty($data['file_data']) ? $data['file_data'] : null;

        // check file data
        if(empty($data_file_response)){
            $this->responseJson([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_tep_du_lieu_database')]);
        }
        $file_error = $data_file_response->getError();
        $data_file_tmp = $data_file_response->getStream()->getMetadata('uri');
        $file_name = $data_file_response->getClientFilename();
        $file_type = $data_file_response->getClientMediaType();

        if(!empty($file_error) || empty($data_file_tmp) || empty($file_name) || $file_type != 'text/plain'){
            $this->responseJson([MESSAGE => __d('template', 'tep_khoi_tao_du_lieu_ban_dau_khong_hop_le')]);
        }      

        // read content file data
        $file_data = new File($data_file_tmp, false);
        $query_data_content = !empty($file_data->read()) ? trim($file_data->read()) : null;
        if(empty($query_data_content)){
            $this->responseJson([MESSAGE => __d('template', 'khong_doc_duoc_noi_dung_tep_du_lieu_khoi_tao')]);
        }
        $file_data->close();        


        try{
            ConnectionManager::get('default')->execute($query_data_content);

            $this->responseJson([CODE => SUCCESS, MESSAGE => __d('template', 'cai_dat_du_lieu_ban_dau_thanh_cong')]);

        }catch (Exception $e) {
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    // hàm test
    public function transferCore()
    {
        $this->layout = false;
        $this->autoRender = false;

        // $data = $this->getRequest()->getData();
        // if (!$this->getRequest()->is('post') || empty($data)) {
        //     $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        // }

        // export file database
        $export_database = $this->exportDatabase();
        if(!$export_database){
            $this->responseJson([MESSAGE => __d('template', 'xuat_tep_database_khong_thanh_cong')]);
        }

        // zip folder source
        $file_name = 'core_' . time() . '.zip';

        $zip = new ZipArchive();
        $open_zip = $zip->open(TMP . $file_name, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        if($open_zip !== true){
            $this->responseJson([MESSAGE => __d('template', 'nen_thu_muc_core_khong_thanh_cong')]);
        }

        $source_folder = new Folder(SOURCE_DOMAIN, false);
        if(empty($source_folder->path)){
            $this->responseJson([MESSAGE => __d('template', 'nen_thu_muc_core_khong_thanh_cong')]);
        }

        $files = $source_folder->findRecursive();
        if(empty($files)){
            $this->responseJson([MESSAGE => __d('template', 'nen_thu_muc_core_khong_thanh_cong')]);
        }
        
        foreach ($files as $key => $file) {
            if(strpos($file, 'config_database.php') || strpos($file, '\tmp\\')) continue;
            $zip->addFile($file, str_replace(SOURCE_DOMAIN, '', $file));
        }
        $zip->close();

        // check file zip created
        $file_zip = new File(TMP . $file_name, false);
        if(empty($file_zip->path)){
            $this->responseJson([MESSAGE => __d('template', 'nen_thu_muc_core_khong_thanh_cong')]);
        }
        $file_zip->close();

        // post file to live domain
        $http = new Client();
        $url = 'http://thichthidi.local/api/website/install-core';

        $response = $http->post($url, [
            'source_file' => fopen(TMP . $file_name, 'r')
        ]);

        $result = $response->getStringBody();
        debug($result);
        die;
        if(!$this->loadComponent('Utilities')->isJson($result)){
            $this->responseJson([MESSAGE => __d('template', 'cai_dat_core_thanh_cong')]);
        }

        return $result;
    }

    public function installCore()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $source_file = !empty($data['source_file']) ? $data['source_file'] : null;
        if(empty($source_file)){
            $this->responseJson([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_tep')]);
        }

        // check file source
        $file_error = $source_file->getError();
        $tmp_name = $source_file->getStream()->getMetadata('uri');
        $file_name = $source_file->getClientFilename();
        $file_type = $source_file->getClientMediaType();

        $list_type_zip = ['application/x-zip-compressed', 'application/zip'];
        if(!empty($file_error) || empty($tmp_name) || empty($file_name) || !in_array($file_type, $list_type_zip)){
            $this->responseJson([MESSAGE => __d('template', 'tep_khoi_tao_cua_giao_dien_khong_hop_le')]);

        }

        // move file to folder source domain
        $source_file->moveTo(SOURCE_DOMAIN . DS . $file_name);

        //check file source 
        $zip_archive = new ZipArchive();
        $open_file = $zip_archive->open(SOURCE_DOMAIN . DS . $file_name);
        if(!$open_file){
            $this->responseJson([MESSAGE => __d('template', 'tai_xuong_tep_khong_thanh_cong')]);
        }

        // clear old source
        $source_folder = new Folder(SOURCE_DOMAIN . DS, false);
        $files = $source_folder->read();
       
        if(!empty($files)){
            if(!empty($files[0])){
                foreach ($files[0] as $key => $folder) {
                    $sub_folder = new Folder($folder, false);
                    if (!$sub_folder->delete()) {
                        $this->responseJson([MESSAGE => __d('template', 'cai_dat_core_thanh_cong')]);
                    }
                }
            }

            if(!empty($files[1])){
                foreach ($files[1] as $key => $file) {
                    if(in_array($file, ['index.php', '.htaccess', 'config.php', 'config_database.php'])) continue;

                    $sub_file = new File(SOURCE_DOMAIN . DS .$file, false);
                    if($sub_file->exists()){
                        @$sub_file->delete();
                    }
                }
            }            
        }

        // unzip file source
        $unzip = $zip_archive->extractTo(SOURCE_DOMAIN);
        if(!$unzip){
            $this->responseJson([MESSAGE => __d('template', 'giai_nen_tep_khong_thanh_cong')]);
        }
        $zip_archive->close();

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('template', 'cai_dat_core_thanh_cong')
        ]);

        // create folder tmp session
        $session_folder = new Folder(TMP . 'sessions', true);
                
        $this->responseJson([CODE => SUCCESS, MESSAGE => __d('template', 'cai_dat_core_thanh_cong')]);
    }

    private function exportDatabase()
    {
        $this->layout = false;
        $this->autoRender = false;

        // if (!$this->getRequest()->is('post') || empty($data)) {
        //     $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        // }

        $conn = ConnectionManager::get('default');        
        $tables = $conn->getSchemaCollection()->listTables();

        $content = '';
        foreach ($tables as $table) {
            $query_create = $conn->execute("show create table `" . $table . "`")->fetchColumn(1);
            if(empty($query_create)) continue;

            $content .= "DROP TABLE IF EXISTS `" . $table . "`;\n\n";
            $content .= $query_create . ";\n\n";


            $rows = $conn->execute("SELECT * FROM `" . $table . "`")->fetchAll('assoc');
            foreach ($rows as $item) {                
                $content .= "INSERT INTO " . $table . " (";

                foreach ($item as $field => $value) {
                    $content .= "`" . addslashes($field) . "`";

                    if(array_key_last($item) != $field) $content .= ", ";
                }


                $content .= ") VALUES (";

                foreach ($item as $field => $value) {                    
                    if(!is_null ($value)){
                        $content .= "'" . addslashes($value) . "'";    
                    }else{
                        $content .= "NULL";        
                    }

                    if(array_key_last($item) != $field) $content .= ", ";
                }
                $content .= ");\n";
            }

            $content .= "\n\n";            
        }

        $content.= "SET FOREIGN_KEY_CHECKS = 1;";

        $file_sql = new File(SOURCE_DOMAIN . DS . 'database.sql', true);
        $file_sql->write($content);

        return true;        
    }

    public function importDatabase()
    {
        $this->layout = false;
        $this->autoRender = false;

        // $data = $this->getRequest()->getData();
        // if (!$this->getRequest()->is('post') || empty($data)) {
        //     $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        // }

        $database_file = new File(SOURCE_DOMAIN . DS . 'database.sql', false);
        if(!$database_file->exists()){
            $this->responseJson([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_tep_database')]);
        }

        $query_data = !empty($database_file->read()) ? trim($database_file->read()) : null;
        if(empty($query_data)){
            $this->responseJson([MESSAGE => __d('template', 'khong_doc_duoc_thong_tin_tep_database')]);
        }
        $database_file->close();

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $execute_query = $conn->execute($query_data);
            $conn->commit();

        }catch (Exception $e) {
            $conn->rollback();
            return $this->System->getResponse([MESSAGE => $e->getMessage()]);
        }

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('template', 'cai_dat_du_lieu_thanh_cong')
        ]);
    }

    protected function responseJson($params = []) 
    {
        $code = ERROR;
        if(!empty($params[CODE]) && in_array($params[CODE], [SUCCESS, ERROR])){
            $code = $params[CODE];
        }

        $message = !empty($params[MESSAGE]) ? $params[MESSAGE] : null;
        if(empty($params[MESSAGE]) && $code == ERROR){
            $message = __d('template', 'xu_ly_du_lieu_khong_thanh_cong');
        }

        if(empty($params[MESSAGE]) && $code == SUCCESS){
            $message = __d('template', 'xu_ly_du_lieu_thanh_cong');
        }
        
        $result = [
            CODE => $code,
            STATUS => !empty($params[STATUS]) ? intval($params[STATUS]) : 200,
            MESSAGE => $message
        ];

        if(isset($params[DATA])){
            $result[DATA] = !empty($params[DATA]) ? $params[DATA] : [];
        }

        if(isset($params[META])){
            $result[META] = !empty($params[META]) ? $params[META] : [];
        }

        exit(json_encode($result));
    }
}