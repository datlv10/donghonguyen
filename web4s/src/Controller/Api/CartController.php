<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\Api\AppController;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Core\Exception\Exception;

class CartController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }


    public function addProduct()
    {
        $data = $this->data_bearer;

        $result = $this->loadComponent('Cart')->addProduct($data, ['api' => true]);
        if($result[CODE] == SUCCESS){
            $this->responseApi($result);
        }else{
            $this->responseErrorApi($result);
        }
    }
    
    public function infomation()
    {
        $data = $this->data_bearer;
        if(!$this->request->is('post')){
            $this->responseErrorApi([MESSAGE => __d('template', 'phuong_thuc_khong_hop_le')]);
        }

        $session = $this->request->getSession();
        $cart_info = $session->read(CART);

        // format list object to arrray
        if(!empty($cart_info['items'])){
            $cart_info['items'] = array_values($cart_info['items']);
        }

        $this->responseApi([
            DATA => !empty($cart_info) ? $cart_info : []
        ]);
    }

    public function updateQuantityProduct()
    {
        $data = $this->data_bearer;
        
        $result = $this->loadComponent('Cart')->updateQuantityProduct($data, ['api' => true]);
        $data_result = !empty($result[DATA]) ? $result[DATA] : [];

        // format list object to arrray
        if(!empty($data_result['items'])){
            $result[DATA]['items'] = array_values($data_result['items']);
        }
        
        if($result[CODE] == SUCCESS){
            $this->responseApi($result);
        }else{
            $this->responseErrorApi($result);
        }        
    }

    public function removeProduct()
    {
        $data = $this->data_bearer;

        $result = $this->loadComponent('Cart')->removeProduct($data, ['api' => true]);
        $data_result = !empty($result[DATA]) ? $result[DATA] : [];

        // format list object to arrray
        if(!empty($data_result['items'])){
            $result[DATA]['items'] = array_values($data_result['items']);
        }
        if($result[CODE] == SUCCESS){
            $this->responseApi($result);
        }else{
            $this->responseErrorApi($result);
        }
    }

}