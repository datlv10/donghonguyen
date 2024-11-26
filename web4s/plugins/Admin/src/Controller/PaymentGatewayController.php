<?php

namespace Admin\Controller;

use Admin\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Utility\Hash;
use Cake\Http\Response;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;

class PaymentGatewayController extends AppController {

    public function initialize(): void
    {
        parent::initialize();        
    }

    public function list() 
    {
        $list_payment_gateway = TableRegistry::get('PaymentsGateway')->find()->contain(['ContentMutiple'])->toArray();
        
        $payment_gateway = [];
        if(!empty($list_payment_gateway)) {
            foreach ($list_payment_gateway as $k => $gateway) {
                $code = !empty($gateway['code']) ? $gateway['code'] : null;
                $contents = [];
                if(!empty($gateway['ContentMutiple'])){
                    foreach ($gateway['ContentMutiple'] as $k => $content) {
                        $lang = !empty($content['lang']) ? $content['lang'] : null;
                        $contents[$lang] = $content;
                    }
                }

                $gateway['content'] = $contents;
                unset($gateway['ContentMutiple']);

                $gateway['config'] = !empty($gateway['config']) ? json_decode($gateway['config'], true) : [];
                $payment_gateway[$code] = $gateway;
            }
        }

        $this->set('payment_gateway', $payment_gateway);

        $list_banks = $this->loadComponent('QrCode')->getBanks();
        $this->set('list_banks', $list_banks);

        $this->css_page = '/assets/css/pages/wizard/wizard-2.css';
        $this->js_page = [
            '/assets/plugins/custom/tinymce6/tinymce.min.js',
            '/assets/js/pages/payment-gateway.js',
        ];

        $this->set('path_menu', 'setting');
        $this->set('title_for_layout', __d('admin', 'cong_thanh_toan'));
    }

    public function save($code = null)
    {
        $this->autoRender = false;
        $data = $this->getRequest()->getData(); 

        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        if(!in_array($code, Configure::read('LIST_PAYMENT_GATEWAY'))) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_cong_thanh_toan')]);
        }

        $utilities = $this->loadComponent('Utilities');
        $payments_gateway_table = TableRegistry::get('PaymentsGateway');

        $gateway_info = $payments_gateway_table->find()->contain(['ContentMutiple'])->where([
            'PaymentsGateway.code' => $code
        ])->first();
   
        // if (empty($gateway_info)) {
        //     $this->responseJson([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_cong_thanh_toan')]);
        // }        
       
        $config = [];
        if($code == BANK_TRANFER){
            $data_config = [];
            foreach ($data['config'] as $bank_info) {
                if(!empty($bank_info['bank_name']) || !empty($bank_info['bank_branch']) || !empty($bank_info['account_holder']) || !empty($bank_info['account_number'])) {
                    $data_config[] = $bank_info;
                }
            }

            $data['config'] = $data_config;
        }
        
        $config = !empty($data['config']) ? json_encode($data['config']) : null;

        $data_content_mutiple = [];

        // parse data content mutiple
        $content_mutiple = !empty($gateway_info['ContentMutiple']) ? $gateway_info['ContentMutiple'] : [];
        $content_mutiple = Hash::combine($content_mutiple, '{n}.lang', '{n}.id');
        
        $names = !empty($data['name']) ? $data['name'] : [];
        $contents = !empty($data['content']) ? $data['content'] : [];

        foreach ($names as $k_lang => $name) {
            $data_content_mutiple[] = [
                'id' => !empty($content_mutiple[$k_lang]) ? intval($content_mutiple[$k_lang]) : null,
                'payment_code' => $code,
                'name' => $name,
                'content' => !empty($contents[$k_lang]) ? $contents[$k_lang] : null,
                'lang' => $k_lang
            ];
        }

        $data_save = [
            'code' => $code,
            'config' => $config,
            'is_installment' => isset($data['is_installment']) ? 1 : 0,
            'status' => !empty($data['status']) ? 1 : 0,
            'ContentMutiple' => $data_content_mutiple
        ];    

        // merge data with entity                
        if(empty($gateway_info)){
            $entity = $payments_gateway_table->newEntity($data_save, ['associated' => ['ContentMutiple']]);
        }else{        
            $entity = $payments_gateway_table->patchEntity($gateway_info, $data_save);
        }

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();
            
            $save = $payments_gateway_table->save($entity);
            if (empty($save->id)){
                throw new Exception();
            }

            $conn->commit();

            $this->responseJson([CODE => SUCCESS, DATA => ['code' => $save->code]]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }
}