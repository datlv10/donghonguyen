<?php

namespace Admin\Controller;

use Admin\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

class FrontendController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function loadAdminBar()
    {
        $this->viewBuilder()->enableAutoLayout(false);

        $admin_name = $this->Auth->user('full_name');
        $this->set('admin_name', $admin_name);
    }

    public function clearCache()
    {
        $this->layout = false;
        $this->autoRender = false;

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        TableRegistry::get('App')->deleteAllCache();
        
        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xoa_cache_thanh_cong')
        ]);
    }
}