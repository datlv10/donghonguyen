<?php

namespace Admin\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Core\Configure;

class AppComponent extends Component
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        // kiểm tra component được call từ frontend hay ko , nếu call từ frontend thì set lại locale
        if(isset($config['enabled']) && $config['enabled'] == false){
            
            if(!defined('LANGUAGE_ADMIN')){
                define('LANGUAGE_ADMIN', 'vi');
            }

            Configure::write('App.paths.locales', [RESOURCES . 'locales' . DS]);
            Configure::write('App.defaultLocale', LANGUAGE_ADMIN);
        }
       
        
    }  
}
