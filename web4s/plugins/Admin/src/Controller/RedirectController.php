<?php

namespace Admin\Controller;

use Admin\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

class RedirectController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function setting()
    {
        $table = TableRegistry::get('Settings');
 
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


        $this->set('redirect', $redirect);
        $this->set('redirect_page_error', $redirect_page_error);

        $this->js_page = [
            '/assets/js/pages/redirect.js'
        ];

        $this->set('title_for_layout', __d('admin', 'cau_hinh_chuyen_huong'));
        $this->set('path_menu', 'setting');
    }
}