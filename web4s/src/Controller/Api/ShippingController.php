<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\Api\AppController;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Core\Exception\Exception;

class ShippingController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }


    public function getListShippingMethod()
    {
        $data = $this->data_bearer;
        if(!$this->getRequest()->is('post')){
           $this->responseErrorApi([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $session = $this->request->getSession();
        $cart_info = $session->read(CART);
        $contact_info = $session->read(CONTACT);
        $member_session = $session->read(MEMBER);

        $city_id = !empty($contact_info['city_id']) ? intval($contact_info['city_id']) : null;
        if(empty($city_id) && !empty($member_session)){
            $city_id = !empty($member_session['city_id']) ? intval($member_session['city_id']) : null;
        }

        $total_cart = !empty($cart_info['total']) ? intval($cart_info['total']) : null;

        $shipping_methods = array_values($this->loadComponent('Shipping')->getListShippingMethod($city_id, $total_cart));

        $this->responseApi([
            CODE => SUCCESS,
            DATA => $shipping_methods
        ]);   
    }

    public function selectMethod()
    {
        $data = $this->data_bearer;
        if(!$this->getRequest()->is('post') || empty($data['shipping_method_id'])){
           $this->responseErrorApi([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $shipping_method_id = !empty($data['shipping_method_id']) ? intval($data['shipping_method_id']) : null;

        $methods = TableRegistry::get('ShippingsMethod')->getList(LANGUAGE);
        $method_info = !empty($methods[$shipping_method_id]) ? $methods[$shipping_method_id] : [];
        if(empty($method_info)){
            $this->responseJson([MESSAGE => __d('template', 'phuong_thuc_van_chuyen_khong_hop_le')]);
        }

        $session = $this->request->getSession();        
        $contact_info = $session->read(CONTACT);
        $cart_info = $session->read(CART);

        $city_id = !empty($data['city_id']) ? intval($data['city_id']) : null;
        if(empty($city_id)){
            $city_id = !empty($contact_info['city_id']) ? intval($contact_info['city_id']) : null;
        }    
        $total_cart = !empty($cart_info['total']) ? floatval($cart_info['total']) : 0;

        $shipping_fee_result = $this->loadComponent('Shipping')->getFeeShipping($city_id, $total_cart, $method_info);

        if(!empty($shipping_fee_result[CODE]) && $shipping_fee_result[CODE] != SUCCESS) {
            $this->responseErrorApi($shipping_fee_result);
        }

        $session->write(SHIPPING, !empty($shipping_fee_result[DATA]) ? $shipping_fee_result[DATA] : []);

        $this->responseApi([
            CODE => SUCCESS,
            DATA => $shipping_fee_result
        ]); 
    }
}