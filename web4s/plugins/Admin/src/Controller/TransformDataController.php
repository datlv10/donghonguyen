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

class TransformDataController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function export()
    {
        // chỉ cho phép tài khoản root vào chức năng này
        $user = $this->Auth->user();
        if(empty($user['supper_admin'])){
            $this->showErrorPage('denied');
        }

        $migrate_info = $this->loadComponent('Admin.ExportData')->readMigrateDataExportInfo();
        $migrate = !empty($migrate_info['initialization']) ? $migrate_info['initialization'] : [];
        $config_data = !empty($migrate['config_data'][DATA]) ? $migrate['config_data'][DATA] : [];
        $languages = !empty($config_data['languages']) ? explode('-', $config_data['languages']) : [];
        if (!empty($languages)) {
            $migrate['config_data'][DATA]['languages'] = $languages;
        }
        
        $this->set('migrate', !empty($migrate) ? $migrate : []);

        $this->js_page = [
            '/assets/js/pages/export_data.js'
        ];

        $this->set('title_for_layout', __d('admin', 'export_du_lieu_mau'));
        $this->set('path_menu', 'setting');
        $this->render('export_data');
    }

    public function initialization()
    {
        $this->autoRender = false;

        $result = $this->loadComponent('Admin.ExportData')->initializeExportData();
        $this->responseJson($result);  
    }

    public function readDatabase()
    {
        $this->autoRender = false;

        $result = $this->loadComponent('Admin.ExportData')->readDatabase();
        $this->responseJson($result);
    }

    public function configData()
    {
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $result = $this->loadComponent('Admin.ExportData')->configDataExport($data);
        $this->responseJson($result);
    }

    public function loadConfigAdvanced()
    {
        $this->viewBuilder()->enableAutoLayout(false);

        $data = $this->getRequest()->getData();
        $type = !empty($data['type']) ? $data['type'] : null;
        if (!$this->getRequest()->is('post') || empty($type)) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $data_config = $this->loadComponent('Admin.ExportData')->loadConfigAdvanced($data, $this->lang);

        $migrate_info = $this->loadComponent('Admin.ExportData')->readMigrateDataExportInfo();
        $config_data = !empty($migrate_info['initialization']['config_data'][DATA]) ? $migrate_info['initialization']['config_data'][DATA] : [];
        $migrate_config = !empty($config_data[$type]) ? json_decode($config_data[$type], true) : [];
        
        $type_render = '';
        switch ($type) {
            case 'categories_product':
            case 'categories_article':

                $type_render = CATEGORY;
                break;

            case 'attributes_article':
            case 'attributes_product':
            case 'attributes_product_item':

                $type_render = ATTRIBUTE;
                break;
        }

        $this->set('data_config', $data_config);
        $this->set('migrate_config', $migrate_config);
        $this->set('type', $type);
        $this->render('config_advanced_' . $type_render);
    }

    public function saveConfigAdvanced()
    {
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $result = $this->loadComponent('Admin.ExportData')->saveConfigAdvanced($data);
        $this->responseJson($result);
    }

    public function configId()
    {
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $result = $this->loadComponent('Admin.ExportData')->configIdExport($data);
        $this->responseJson($result);
    }

    public function configCdn()
    {
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $result = $this->loadComponent('Admin.ExportData')->configCdnExport($data);
        $this->responseJson($result);
    }

    public function migrateCategories($type = null)
    {
        if (empty($type)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        // chỉ cho phép tài khoản root vào chức năng này
        $user = $this->Auth->user();
        if(empty($user['supper_admin'])){
            $this->showErrorPage('denied');
        }

        $migrate_info = $this->loadComponent('Admin.ExportData')->readMigrateDataExportInfo();
        $migrate = !empty($migrate_info['categories_' . $type]) ? $migrate_info['categories_' . $type] : [];

        $this->js_page = [
            '/assets/js/pages/export_data.js'
        ];

        $this->set('type', $type);
        $this->set('migrate', $migrate);
        $this->set('path_menu', 'setting');
        $this->set('title_for_layout', __d('admin', 'chuyen_doi_danh_muc_bai_viet'));

        if ($type == PRODUCT) {
            $this->set('title_for_layout', __d('admin', 'chuyen_doi_danh_muc_san_pham'));
        }

        $this->render('migrate_categories');
    }

    public function migrateArticles()
    {
        $migrate_info = $this->loadComponent('Admin.ExportData')->readMigrateDataExportInfo();
        
        $this->set('migrate', !empty($migrate_info['articles']) ? $migrate_info['articles'] : []);
        $this->js_page = [
            '/assets/js/pages/export_data.js'
        ];
        $this->set('path_menu', 'setting');
        $this->set('title_for_layout', __d('admin', 'chuyen_doi_bai_viet'));
    }

    public function migrateBrands()
    {
        $migrate_info = $this->loadComponent('Admin.ExportData')->readMigrateDataExportInfo();
        
        $this->set('migrate', !empty($migrate_info['brands']) ? $migrate_info['brands'] : []);
        $this->js_page = [
            '/assets/js/pages/export_data.js'
        ];
        $this->set('path_menu', 'setting');
        $this->set('title_for_layout', __d('admin', 'chuyen_doi_thuong_hieu'));
    }

    public function migrateProducts()
    {
        $migrate_info = $this->loadComponent('Admin.ExportData')->readMigrateDataExportInfo();
        
        $this->set('migrate', !empty($migrate_info['products']) ? $migrate_info['products'] : []);
        $this->js_page = [
            '/assets/js/pages/export_data.js'
        ];
        $this->set('path_menu', 'setting');
        $this->set('title_for_layout', __d('admin', 'chuyen_doi_san_pham'));
    }

    public function migrateAttributes()
    {
        $migrate_info = $this->loadComponent('Admin.ExportData')->readMigrateDataExportInfo();
        
        $this->set('migrate', !empty($migrate_info['attributes']) ? $migrate_info['attributes'] : []);
        $this->js_page = [
            '/assets/js/pages/export_data.js'
        ];
        $this->set('path_menu', 'setting');
        $this->set('title_for_layout', __d('admin', 'chuyen_doi_thuoc_tinh_mo_rong'));
    }

    public function migrateTags()
    {
        $migrate_info = $this->loadComponent('Admin.ExportData')->readMigrateDataExportInfo();
        
        $this->set('migrate', !empty($migrate_info['tags']) ? $migrate_info['tags'] : []);
        $this->js_page = [
            '/assets/js/pages/export_data.js'
        ];
        $this->set('path_menu', 'setting');
        $this->set('title_for_layout', __d('admin', 'chuyen_doi_the_tags'));
    }

    public function migrateData()
    {
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        $type = !empty($data['type']) ? $data['type'] : null;
        if (!$this->getRequest()->is('post') || empty($type)) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $result = $this->loadComponent('Admin.ExportData')->migrateData($type);
        $this->responseJson($result);
    }

    public function success()
    {
        $migrate_info = $this->loadComponent('Admin.ExportData')->readMigrateDataExportInfo();
        $this->set('migrate_info', $migrate_info);
        $this->js_page = [
            '/assets/js/pages/export_data.js'
        ];
        $this->set('path_menu', 'setting');
        $this->set('title_for_layout', __d('admin', 'hoan_thanh_chuyen_doi_du_lieu'));
    }

    public function exportData()
    {
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $result = $this->loadComponent('Admin.ExportData')->exportData();
        $this->responseJson($result);
    }

    public function downloadFile()
    {
        $file_sql = new File(TMP . 'export/data.zip', false);
        if(empty($file_sql->exists())){
            $this->responseJson([MESSAGE => __d('admin', 'khong_ton_tai_file_data_zip')]);
        }

        return $this->getResponse()->withFile(TMP . 'export/data.zip', [
            'download' => true,
            'name' => 'data.zip',
        ]);
    }

    public function downloadMedia()
    {
        $file_sql = new File(TMP . 'export/media.zip', false);
        if(empty($file_sql->exists())){
            $this->responseJson([MESSAGE => __d('admin', 'khong_ton_tai_file_media_zip')]);
        }

        return $this->getResponse()->withFile(TMP . 'export/media.zip', [
            'download' => true,
            'name' => 'media.zip',
        ]);
    }

    public function downloadThumb()
    {
        $file_sql = new File(TMP . 'export/thumbs.zip', false);
        if(empty($file_sql->exists())){
            $this->responseJson([MESSAGE => __d('admin', 'khong_ton_tai_file_thumbs_zip')]);
        }

        return $this->getResponse()->withFile(TMP . 'export/thumbs.zip', [
            'download' => true,
            'name' => 'thumbs.zip',
        ]);
    }
}