<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\Api\AppController;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Core\Exception\Exception;

class OrderController extends AppController {

    private $action_session = [
        'confirmInfomation', 
        'create',
        'checkout',
        'chooseAddress'
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

    public function confirmInfomation()
    {
        $data = $this->data_bearer;

        $order_info = $this->loadComponent('OrderFrontend')->confirmOrderInfomation();
        $this->responseApi([
            CODE => SUCCESS,
            DATA => $order_info
        ]);    
    }

    public function create()
    {
        $data = $this->data_bearer;

        $session = $this->request->getSession();
        $member_info = $session->read(MEMBER);
        $customer_id = !empty($member_info['customer_id']) ? intval($member_info['customer_id']) : null;

        $contact_info = $session->read(CONTACT);
        if(empty($contact_info['customer_id']) || $contact_info['customer_id'] != $customer_id){            
            $member_info = TableRegistry::get('Customers')->getDetailCustomer($customer_id, [
                'get_list_address' => true
            ]);
            $member_info = TableRegistry::get('Customers')->formatDataCustomerDetail($member_info);
            $session->write(CONTACT, $member_info);
        }    

        $result = $this->loadComponent('OrderFrontend')->create($data, ['api' => true]);
        if($result[CODE] == SUCCESS){
            $this->responseApi($result);
        }else{
            $this->responseErrorApi($result);
        }
    }

    public function checkout()
    {
        $data = $this->data_bearer;

        if(empty($data)){
            $this->responseErrorApi([
                MESSAGE => __d('template', 'du_lieu_khong_hop_le')
            ]);
        }
        
        $result = $this->loadComponent('OrderFrontend')->checkout($data, ['api' => true]);
        if($result[CODE] == SUCCESS){
            $this->responseApi($result);
        }else{
            $this->responseErrorApi($result);
        }
    }

    public function chooseAddress()
    {
        $data = $this->data_bearer;

        $result = $this->loadComponent('OrderFrontend')->chooseAddress($data, ['api' => true]);
        if($result[CODE] == SUCCESS){
            $this->responseApi($result);
        }else{
            $this->responseErrorApi($result);
        }
    }
}