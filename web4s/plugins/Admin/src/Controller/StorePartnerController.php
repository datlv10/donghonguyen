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

class StorePartnerController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function settingStorePartner()
    {
        $settings = TableRegistry::get('Settings')->getSettingWebsite();

        // cấu hình của kiotviet
        $store_kiotviet = !empty($settings['store_kiotviet']) ? $settings['store_kiotviet'] : [];

        $config_kiotviet = !empty($store_kiotviet['config']) ? json_decode($store_kiotviet['config'], true) : [];
        $webhooks_kiotviet = !empty($store_kiotviet['webhook']) ? json_decode($store_kiotviet['webhook'], true) : [];
        $stores_kiotviet = TableRegistry::get('ProductsPartnerStore')->queryListStorePartner([
            FIELD => FULL_INFO
        ])->toArray();  
        $webhooks_result = $this->loadComponent('Admin.StoreKiotViet')->listWebhooks();
        $webhooks_result = !empty($webhooks_result['data']) ? $webhooks_result['data'] : [];

        $list_webhook = [];
        if(!empty($webhooks_result)) {
            foreach ($webhooks_result as $item_webhook) {
                $type = !empty($item_webhook['type']) ? str_replace('.', '_', $item_webhook['type']) : null;
                $url = !empty($item_webhook['url']) ? $item_webhook['url'] : null;
                $list_webhook[$type] = [
                    'url' => $url,
                    'type' => $type
                ];
            }
        }   

        $webhooks_kiotviet_format = [];
        if(!empty($webhooks_kiotviet)) {
            foreach ($webhooks_kiotviet as $webhooks_kiotviet) {
                $type = !empty($webhooks_kiotviet) ? str_replace('.', '_', $webhooks_kiotviet) : null;

                $webhooks_kiotviet_format[$type] = true;
            }
        }   
        
        $this->set('config_kiotviet', $config_kiotviet);
        $this->set('list_webhook', $list_webhook);
        $this->set('webhooks_kiotviet_format', $webhooks_kiotviet_format);
        $this->set('stores_kiotviet', $stores_kiotviet);
        $this->set('title_for_layout', __d('admin', 'doi_tac_quan_ly_kho_hang'));
        $this->css_page = [
            '/assets/css/pages/wizard/wizard-2.css'
        ];

        $this->js_page = [
            '/assets/js/pages/store_kiotviet.js',
        ];
    }

}