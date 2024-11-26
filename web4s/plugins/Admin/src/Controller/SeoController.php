<?php

namespace Admin\Controller;

use Admin\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Filesystem\File;
use Cake\Datasource\ConnectionManager;

class SeoController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function pageSeoInfo()
    {
        $list_pages = TableRegistry::get('TemplatesPage')->getListPageContent(); 

        $this->css_page = [
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.css'
        ];

        $this->js_page = [
            '/assets/js/pages/page_seo_info.js',
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.js',
        ];

        $this->set('list_pages', $list_pages);

        $this->set('path_menu', 'page_seo_info');
        $this->set('title_for_layout', __d('admin', 'thong_tin_seo_cua_trang'));
        $this->render('page_seo_info');
    }

    public function savePageSeoInfo()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data_post = !empty($this->request->getData()) ? $this->request->getData() : [];
        
        if (!$this->getRequest()->is('post') || empty($data_post) || !is_array($data_post)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $all_contents = TableRegistry::get('TemplatesPageContent')->find()->where([
            'template_code' => CODE_TEMPLATE
        ])->toArray(); 

        $data_save = [];
        foreach ($data_post as $data) {
            $list_keyword = !empty($data['seo_keyword']) ? array_column(json_decode($data['seo_keyword'], true), 'value') : null;
            $seo_keyword = !empty($list_keyword) ? implode(', ', $list_keyword) : '';
            $data_save[] = [
                'id' => !empty($data['id']) ? intval($data['id']) : null,
                'seo_title' => !empty($data['seo_title']) ? trim(strip_tags($data['seo_title'])) : null,
                'seo_description' => !empty($data['seo_description']) ? trim(strip_tags($data['seo_description'])) : null,
                'seo_keyword' => !empty($seo_keyword) ? trim(strip_tags($seo_keyword)) : null,
                'seo_image' => !empty($data['seo_image']) ? $data['seo_image'] : null,
                'template_code' => !empty($data['template_code']) ? $data['template_code'] : null,
                'page_code' => !empty($data['page_code']) ? $data['page_code'] : null,
                'lang' => !empty($data['lang']) ? $data['lang'] : null
            ];
        }

        $entities = TableRegistry::get('TemplatesPageContent')->patchEntities($all_contents, $data_save); 

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $save = TableRegistry::get('TemplatesPageContent')->saveMany($entities);
            if (empty($save)){
                throw new Exception();
            }

            $conn->commit();
            $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'cap_nhat_thanh_cong')]);
        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function uploadFileRobots()
    {
        $this->layout = false;
        $this->autoRender = false;

        // validate file upload
        $file = !empty($this->request->getData()) ? $this->request->getData('file') : [];
        if(empty($file)){
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $file_name = $file->getClientFilename();
        if($file_name != 'robots.txt') {
            $this->responseJson([MESSAGE => __d('admin', 'ten_tep_khong_chinh_xac')]);
        }

        if($file->getClientMediaType() != 'text/plain'){
            $this->responseJson([MESSAGE => __d('admin', 'sai_dinh_dang')]);
        }

        $file_ext = strtolower(pathinfo($file->getClientFilename(), PATHINFO_EXTENSION));
        if($file_ext != 'txt') {
            $this->responseJson([MESSAGE => __d('admin', 'sai_dinh_dang')]);
        }

        // delete old file
        if(file_exists(WWW_ROOT. 'robots.txt')) {
            $old_file = new File(WWW_ROOT. 'robots.txt');
            $old_file->delete();
        }

        // upload file
        $upload = $this->loadComponent('Admin.UploadFile')->upload($file, WWW_ROOT, [
            'file_name' => 'robots.txt',
            'white_list' => ['txt']
        ]);

        if (empty($upload) || (!empty($upload) && $upload['code'] === ERROR)) {
            $this->responseJson([
                MESSAGE => !empty($upload['message']) ? $upload['message'] : __d('admin', 'tai_tep_len_khong_thanh_cong')
            ]);
        }

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'tai_tep_len_thanh_cong')
        ]);        
    }

    public function setting()
    {
        $table = TableRegistry::get('Settings');
        // sitemap  
        $sitemap = $table->find()->where([
            'group_setting' => 'sitemap'
        ])->toArray();  
        $sitemap = Hash::combine($sitemap, '{n}.code', '{n}.value');

        // url  
        $url = $table->find()->where([
            'group_setting' => 'url'
        ])->toArray();  
        $url = Hash::combine($url, '{n}.code', '{n}.value');
 
        // redirect 301
        $redirect = $table->find()->where([
            'group_setting' => 'redirect_301'
        ])->toArray();

        $redirect = Hash::combine($redirect, '{n}.code', '{n}.value');

        // redirect page error
        $redirect_page_error = $table->find()->where([
            'group_setting' => 'redirect_page_error'
        ])->toArray();

        $redirect_page_error = Hash::combine($redirect_page_error, '{n}.code', '{n}.value');

        // tag
        $tag = $table->find()->where([
            'group_setting' => 'tag'
        ])->toArray();

        $tag = Hash::combine($tag, '{n}.code', '{n}.value');

        // robots file
        $robots_file = new File(WWW_ROOT . 'robots.txt', false);
        
        $this->set('sitemap', $sitemap);
        $this->set('url', $url);
        $this->set('redirect', $redirect);
        $this->set('redirect_page_error', $redirect_page_error);
        $this->set('tag', $tag);
        $this->set('exist_robots_file', $robots_file->exists() ? true : false);

        $this->js_page = [
            '/assets/js/pages/seo_setting.js'
        ];

        $this->set('title_for_layout', __d('admin', 'cau_hinh_seo'));
        $this->set('path_menu', 'seo_setting');
    }

}