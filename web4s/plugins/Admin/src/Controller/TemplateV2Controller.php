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

class TemplateV2Controller extends AppController {

    public function initialize(): void
    {
        parent::initialize();
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


        $this->viewBuilder()->setLayout('template_v2');
    }

    public function getElements()
    {
        $this->viewBuilder()->enableAutoLayout(false);
        $categories_element = Configure::read('CATEGORIES_ELEMENT');

        $this->set('categories_element', $categories_element);
    }

}