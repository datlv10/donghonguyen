<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\Api\AppController;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Core\Exception\Exception;

class CustomerPointController extends AppController {

    private $action_session = [        
        'infoCustomerPoint',
        'historyUsingPoint',
        'attendance',
        'attendanceTick',
        'applyPointToOrder',
        'givePoint',
        'buyPoint'
    ]; 

    public function initialize(): void
    {
        parent::initialize();

        $session = $this->request->getSession();
        $member = $session->read(MEMBER);
        
        if(in_array($this->request->getParam('action'), $this->action_session) && !empty($member['customer_id'])) {
            if($this->loadComponent('Member')->memberDoesntExistLogout($member['customer_id'])){
                $this->responseErrorApi([
                    STATUS => 403,
                    MESSAGE => __d('template', 'het_phien_lam_viec_vui_long_dang_nhap_lai_tai_khoan')
                ]);
            }
        }

        if (in_array($this->request->getParam('action'), $this->action_session) && empty($member['customer_id'])){
            $this->responseErrorApi([
                STATUS => 403,
                MESSAGE => __d('template', 'het_phien_lam_viec_vui_long_dang_nhap_lai_tai_khoan')
            ]);
        }
    }

    public function infoCustomerPoint()
    {
        $data = $this->data_bearer;
        if(!$this->getRequest()->is('post')){
           $this->responseErrorApi([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $point_info = $this->loadComponent('CustomersPointFrontend')->getInfoCustomerPoint();

        $this->responseApi([
            CODE => SUCCESS,
            DATA => $point_info
        ]);
    }

    public function historyUsingPoint()
    {
        $data = $this->data_bearer;

        $result = [
            DATA => [],
            PAGINATION => []
        ];

        if(!$this->getRequest()->is('post')){
           $this->responseErrorApi([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $result = $this->loadComponent('CustomersPointFrontend')->historyUsingPoint($data);

        if(empty($result[CODE]) || $result[CODE] == ERROR) {
            $this->responseErrorApi($result);
        }

        $history_point = !empty($result[DATA]['history_point']) ? $result[DATA]['history_point'] : [];
    
        $this->responseApi([
            DATA => $history_point,
        ]);
    }

    public function attendance()
    {
        $data = $this->data_bearer;
        if(!$this->getRequest()->is('post')){
           $this->responseErrorApi([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $attendance_info = $this->loadComponent('CustomersPointFrontend')->processAttendance();
        $this->responseApi([
            CODE => SUCCESS,
            DATA => $attendance_info
        ]);
    }

    public function attendanceTick()
    {
        $data = $this->data_bearer;
        if(!$this->getRequest()->is('post') || empty($data)){
           $this->responseErrorApi([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }
        
        $result = $this->loadComponent('CustomersPointFrontend')->attendanceTick($data);

        if(!empty($result[CODE]) && $result[CODE] == SUCCESS){
            $this->responseApi($result);
        }else{
            $this->responseErrorApi($result);
        }
    }

    public function applyPointToOrder()
    {
        $data = $this->data_bearer;
        if(!$this->getRequest()->is('post') || empty($data)){
           $this->responseErrorApi([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $result = $this->loadComponent('CustomersPointFrontend')->applyPointToOrder($data, ['api' => true]);

        if(!empty($result[CODE]) && $result[CODE] == SUCCESS){
            $this->responseApi($result);
        }else{
            $this->responseErrorApi($result);
        }
    }

    public function clearPointInOrder()
    {
        $data = $this->data_bearer;
        if(!$this->getRequest()->is('post') || empty($data)){
           $this->responseErrorApi([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $result = $this->loadComponent('CustomersPointFrontend')->clearPointInOrder($data, ['api' => true]);

        if(!empty($result[CODE]) && $result[CODE] == SUCCESS){
            $this->responseApi($result);
        }else{
            $this->responseErrorApi($result);
        }
    }

    public function givePoint()
    {
        $data = $this->data_bearer;
        if(!$this->getRequest()->is('post') || empty($data)){
           $this->responseErrorApi([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $result = $this->loadComponent('MemberWallet')->givePoint($data, ['api' => true]);

        if(!empty($result[CODE]) && $result[CODE] == SUCCESS){
            $this->responseApi($result);
        }else{
            $this->responseErrorApi($result);
        }
    }

    public function buyPoint()
    {
        $data = $this->data_bearer;
        if(!$this->getRequest()->is('post') || empty($data)){
           $this->responseErrorApi([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $result = $this->loadComponent('MemberWallet')->buyPoint($data, ['api' => true]);

        if(!empty($result[CODE]) && $result[CODE] == SUCCESS){
            $this->responseApi($result);
        }else{
            $this->responseErrorApi($result);
        }
    }
}