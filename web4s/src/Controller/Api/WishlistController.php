<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\Api\AppController;

class WishlistController extends AppController {

    private $action_session = [
        'addProduct', 
        'removeProduct'
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

    public function addProduct()
    {
        $data = $this->data_bearer;

        $data['type'] = PRODUCT;
        $data['record_id'] = !empty($data['product_id']) ? $data['product_id'] : null;
       
        $result = $this->loadComponent('Wishlist')->addProduct($data, ['api' => true]);
        if($result[CODE] == SUCCESS){
            $this->responseApi($result);
        }else{
            $this->responseErrorApi($result);
        }
    }

    public function removeProduct()
    {
        $data = $this->data_bearer;

        $data['type'] = PRODUCT;
        $data['record_id'] = !empty($data['product_id']) ? $data['product_id'] : null;

        $result = $this->loadComponent('Wishlist')->removeProduct($data, ['api' => true]);
        if($result[CODE] == SUCCESS){
            $this->responseApi($result);
        }else{
            $this->responseErrorApi($result);
        }
    }
}