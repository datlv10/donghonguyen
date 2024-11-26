<?php

namespace Admin\Controller;

use Admin\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;
use Cake\Utility\Hash;

class SeoSiteMapController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function index()
    {
        $group = 'sitemap';
        $setting_info = TableRegistry::get('Settings')->find()->where([
            'group_setting' => $group
        ])->toArray();  
        $setting_info = Hash::combine($setting_info, '{n}.code', '{n}.value');


        $this->js_page = [
            '/assets/js/pages/seo_sitemap.js'
        ];

        $this->set('sitemap', $setting_info);
        $this->set('group', $group);
        $this->set('title_for_layout', __d('admin', 'cau_hinh_sitemap'));
        $this->set('path_menu', 'seo_site_map');
    }

    public function saveConfigSitemap()
    {

    }

}