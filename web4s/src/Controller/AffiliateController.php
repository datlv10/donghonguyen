<?php

namespace App\Controller;

use Cake\ORM\TableRegistry;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;
use Cake\Event\EventInterface;
use Cake\Utility\Hash;

class AffiliateController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    //áp dụng mã giới thiệu vào đơn hàng
	public function apply() 
	{
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        if(!$this->getRequest()->is('post') || empty($data)){
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $apply_affiliate = $this->loadComponent('AffiliateFrontend')->apply($data);

        $this->responseJson($apply_affiliate);
    }

    //xóa mã giới thiệu được áp dụng cho đơn hàng
    public function delete() 
    {
        $this->layout = false;
        $this->autoRender = false;

        $data_request = !empty($this->request->getData()) ? $this->request->getData() : [];
        if(!$this->getRequest()->is('post')){
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $this->getRequest()->getSession()->write(AFFILIATE, null);
        $this->responseJson([CODE => SUCCESS, MESSAGE => __d('template', 'cap_nhat_thanh_cong')]);
    }

}