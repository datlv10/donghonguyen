<?php

namespace Admin\Controller;

use Cake\Controller\Controller;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Utility\Hash;
use Cake\Http\Response;
use Cake\ORM\Query;
use Cake\Core\Exception\Exception;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;


class PrintController extends Controller {

    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Auth', [
            'loginAction' => [
                'controller' => 'User',
                'action' => 'login',
                'plugin' => 'Admin'
            ],
            'authenticate' => [
                'Form' => [
                    'finder' => 'auth'
                ]
            ],
            'storage' => 'Session',
            'unauthorizedRedirect' => $this->referer()
        ]);

        if(!defined('LANGUAGE_ADMIN')){
            define('LANGUAGE_ADMIN', 'vi');
        }

        if(LANGUAGE_ADMIN != LANGUAGE_DEFAULT_ADMIN){
            Configure::write('App.paths.locales', [RESOURCES . 'locales' . DS]);
            Configure::write('App.defaultLocale', LANGUAGE_ADMIN);
            I18n::setLocale(LANGUAGE_ADMIN);
        }

        $this->lang = $this->loadComponent('System')->getLanguageAdmin();

        // kiểm tra 1 số hằng biến hệ thống nếu không tồn tại thì sẽ khai báo lại, tránh bị lỗi khi call đến các component trong admin bị lỗi
        if(!defined('CODE_TEMPLATE')){
            $template = TableRegistry::get('Templates')->getTemplateDefault();
            $template_code = !empty($template['code']) ? $template['code'] : null;

            $path_template = SOURCE_DOMAIN  . DS . 'templates' . DS;
            $url_template = '/templates/';

            if(!empty($template_code)){
                $path_template = SOURCE_DOMAIN  . DS . 'templates' . DS . $template_code . DS;
                $url_template = '/templates/' . $template_code . '/';
            }

            define('CODE_TEMPLATE', $template_code);
            define('PATH_TEMPLATE', $path_template);
            define('URL_TEMPLATE', $url_template);
        } 

        if(!defined('CDN_URL')){
            $settings = TableRegistry::get('Settings')->getSettingWebsite();
            if(!empty($settings['profile']['cdn_url'])){
                define('CDN_URL', $settings['profile']['cdn_url']);
            }else{
                define('CDN_URL', $this->request->scheme() . '://cdn.' . $this->request->host());
            }
        }
    }

    public function print()
    {
        $view_builder = $this->viewBuilder();
        $view_builder->enableAutoLayout(false);
        $view_builder->setClassName('Smarty');

        $params = $this->request->getQuery();

        $code = !empty($params['code']) ? $params['code'] : null;
        $id_record = !empty($params['id_record']) ? intval($params['id_record']) : null;

        $template_info = TableRegistry::get('PrintTemplates')->getPrintTemplateBycode($code);

        $view = !empty($template_info['template']) ? str_replace('.tpl', '', $template_info['template']) : null;
        $name = !empty($template_info['name']) ? $template_info['name'] : null;
        $title_print = !empty($template_info['title_print']) ? $template_info['title_print'] : null;
        if(empty($name)) die(__d('admin', 'khong_lay_duoc_thong_tin_mau_in'));

        $this->set('id_record', $id_record);
        $this->set('view', $view);
        $this->set('name', $name);
        $this->set('title_for_layout', $title_print);
    }

    public function getContent()
    {
        $view_builder = $this->viewBuilder();
        $view_builder->enableAutoLayout(false);
        $view_builder->setClassName('Smarty');

        Configure::write('App.paths.templates', PATH_TEMPLATE);
        Configure::write('App.paths.locales', PATH_TEMPLATE . 'locales' . DS);

        $data = $this->getRequest()->getData();

        $view = !empty($data['view']) ? $data['view'] : null;
        $id_record = !empty($data['id_record']) ? intval($data['id_record']) : null;
        
        $file = new File(PATH_TEMPLATE . 'Print' . DS . $view . '.tpl', false);
        if(!$file->exists()) die(__d('admin', 'khong_ton_tai_file_mau_in'));

        $this->set('id_record', $id_record);
        $this->render($view);
    }
    
}