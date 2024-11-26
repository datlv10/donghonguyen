<?php

namespace App\Controller;

use Cake\ORM\TableRegistry;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;
use Cake\Event\EventInterface;

class CustomerPointController extends AppController {

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $action_check = [
            'applyPointToOrder'
        ];

        $session = $this->request->getSession();  
        $member = $session->read(MEMBER);

        if(in_array($this->request->getParam('action'), $action_check) && !empty($member['customer_id'])) {
            if($this->loadComponent('Member')->memberDoesntExistLogout($member['customer_id'])){
                if($this->request->is('ajax')){
                    $this->responseJson([
                        STATUS => 403,
                        MESSAGE => __d('template', 'het_phien_lam_viec_vui_long_dang_nhap_lai_tai_khoan')
                    ]);
                }else{
                    return $this->redirect('/member/login?redirect=' . urlencode($this->request->getPath()), 303);
                }
            }
        }

        if (in_array($this->request->getParam('action'), $action_check) && empty($member['customer_id'])){
            if($this->request->is('ajax')){
                $this->responseJson([
                    STATUS => 403,
                    MESSAGE => __d('template', 'het_phien_lam_viec_vui_long_dang_nhap_lai_tai_khoan')
                ]);
            }else{
                return $this->redirect('/member/login?redirect=' . urlencode($this->request->getPath()), 303);
            }
        }

    }

	public function applyPointToOrder() 
	{
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        if(!$this->getRequest()->is('post') || empty($data)){
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $result = $this->loadComponent('CustomersPointFrontend')->applyPointToOrder($data);

        $this->responseJson($result);
    }

    public function clearPointInOrder()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        if(!$this->getRequest()->is('post') || empty($data)){
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $result = $this->loadComponent('CustomersPointFrontend')->clearPointInOrder($data);

        $this->responseJson($result);
    }
}