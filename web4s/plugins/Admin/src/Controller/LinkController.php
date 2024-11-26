<?php

namespace Admin\Controller;

use Admin\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Utility\Hash;
use Cake\Http\Response;
use Cake\ORM\Query;
use Cake\Core\Exception\Exception;

class LinkController extends AppController {

    public function initialize(): void
    {
        parent::initialize();        
    }    

    public function checkExist()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $url = !empty($data['url']) ? trim($data['url']) : null;
        $id = !empty($data['id']) ? intval($data['id']) : null;

        if (empty($url)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $exist = TableRegistry::get('Links')->checkExist($url, $id);
        
        if($exist){
            $this->responseJson([CODE => SUCCESS, DATA => ['exist' => true], MESSAGE => __d('admin', 'duong_dan_da_ton_tai_tren_he_thong')]);
        }else{
            $this->responseJson([CODE => SUCCESS, DATA => ['exist' => false], MESSAGE => __d('admin', 'co_the_su_dung_duong_dan_nay')]);
        }
    }
}