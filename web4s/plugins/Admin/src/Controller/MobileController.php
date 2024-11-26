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

class MobileController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function dashboard() 
    {
        $this->set('path_menu', 'mobile_app');
        $this->set('title_for_layout', __d('admin', 'mobile_app'));
    }

    public function setting() 
    {
        $app_info = TableRegistry::get('MobileApp')->getMobileAppDefault();
        $config = !empty($app_info['config']) ? json_decode($app_info['config'], true) : [];

        $this->set('app_info', !empty($app_info) ? $app_info : []);
        $this->set('config', !empty($config) ? $config : []);

        $this->js_page = '/assets/js/pages/setting_mobile.js';
        
        $this->set('path_menu', 'mobile_app');
        $this->set('title_for_layout', __d('admin', 'cai_dat_chung'));
    }

    public function saveInfoApp() 
    {
        $this->autoRender = false;

        $data = $this->getRequest()->getData();    

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }
            
        $table = TableRegistry::get('MobileApp');
        $app_info = $table->find()->first();

        $data_save = [
            'app_name' => !empty($data['app_name']) ? $data['app_name'] : '',
            'app_id' => !empty($data['app_id']) ? $data['app_id'] : ''
        ];

        if (empty($app_info)) {
            $entity = $table->newEntity($data_save);
        } else {
            $entity = $table->patchEntity($app_info, $data_save);
        }

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();
            
            $save = $table->save($entity);
            if (empty($save->id)){
                throw new Exception();
            }
            $conn->commit();

            $this->responseJson([CODE => SUCCESS]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    } 

    public function saveInfoVphone() 
    {
        $this->autoRender = false;

        $data = $this->getRequest()->getData();    

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }
            
        $table = TableRegistry::get('MobileApp');
        $app_info = $table->find()->first();

        if (empty($app_info)) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_ton_tai_thong_tin_app')]);
        }

        $config = !empty($app_info['config']) ? json_decode($app_info['config'], true) : [];
        $config['vphone'] = $data;

        $entity = $table->patchEntity($app_info, ['config' => json_encode($config)]);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();
            
            $save = $table->save($entity);
            if (empty($save->id)){
                throw new Exception();
            }
            $conn->commit();

            $this->responseJson([CODE => SUCCESS]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    } 

    public function saveInfoComment() 
    {
        $this->autoRender = false;

        $data = $this->getRequest()->getData();    

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }
            
        $table = TableRegistry::get('MobileApp');
        $app_info = $table->find()->first();

        if (empty($app_info)) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_ton_tai_thong_tin_app')]);
        }

        $config = !empty($app_info['config']) ? json_decode($app_info['config'], true) : [];
        $config['comment'] = $data;

        $entity = $table->patchEntity($app_info, ['config' => json_encode($config)]);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();
            
            $save = $table->save($entity);
            if (empty($save->id)){
                throw new Exception();
            }
            $conn->commit();

            $this->responseJson([CODE => SUCCESS]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    } 

    public function saveInfoSocialLogin() 
    {
        $this->autoRender = false;

        $data = $this->getRequest()->getData();    

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }
            
        $table = TableRegistry::get('MobileApp');
        $app_info = $table->find()->first();

        if (empty($app_info)) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_ton_tai_thong_tin_app')]);
        }

        $config = !empty($app_info['config']) ? json_decode($app_info['config'], true) : [];
        $config['social_login'] = $data;

        $entity = $table->patchEntity($app_info, ['config' => json_encode($config)]);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();
            
            $save = $table->save($entity);
            if (empty($save->id)){
                throw new Exception();
            }
            $conn->commit();

            $this->responseJson([CODE => SUCCESS]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    } 

    public function saveInfoSocial() 
    {
        $this->autoRender = false;

        $data = $this->getRequest()->getData();    

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }
            
        $table = TableRegistry::get('MobileApp');
        $app_info = $table->find()->first();

        if (empty($app_info)) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_ton_tai_thong_tin_app')]);
        }

        $config = !empty($app_info['config']) ? json_decode($app_info['config'], true) : [];
        $config['social'] = $data;

        $entity = $table->patchEntity($app_info, ['config' => json_encode($config)]);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();
            
            $save = $table->save($entity);
            if (empty($save->id)){
                throw new Exception();
            }
            $conn->commit();

            $this->responseJson([CODE => SUCCESS]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function saveContact()
    {
        $this->autoRender = false;

        $data = $this->getRequest()->getData();    

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }
            
        $table = TableRegistry::get('MobileApp');
        $app_info = $table->find()->first();

        if (empty($app_info)) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_ton_tai_thong_tin_app')]);
        }

        $config = !empty($app_info['config']) ? json_decode($app_info['config'], true) : [];
        $config['contact'] = $data;

        $entity = $table->patchEntity($app_info, ['config' => json_encode($config)]);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();
            
            $save = $table->save($entity);
            if (empty($save->id)){
                throw new Exception();
            }
            $conn->commit();

            $this->responseJson([CODE => SUCCESS]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function saveConfigMomo()
    {
        $this->autoRender = false;

        $data = $this->getRequest()->getData();    

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }
            
        $table = TableRegistry::get('MobileApp');
        $app_info = $table->find()->first();

        if (empty($app_info)) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_ton_tai_thong_tin_app')]);
        }

        $config = !empty($app_info['config']) ? json_decode($app_info['config'], true) : [];
        $config['momo'] = $data;

        $entity = $table->patchEntity($app_info, ['config' => json_encode($config)]);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();
            
            $save = $table->save($entity);
            if (empty($save->id)){
                throw new Exception();
            }
            $conn->commit();

            $this->responseJson([CODE => SUCCESS]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }
}