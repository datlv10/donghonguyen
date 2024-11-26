<?php

namespace App\Controller;

use Cake\Event\EventInterface;
use Cake\ORM\TableRegistry;

class ShippingController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
    }

    public function selectMethod()
    {
        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        $shipping_method_id = !empty($data['shipping_method_id']) ? intval($data['shipping_method_id']) : null;

        if (!$this->getRequest()->is('post')){
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $methods = TableRegistry::get('ShippingsMethod')->getList(LANGUAGE);
        $method_info = !empty($methods[$shipping_method_id]) ? $methods[$shipping_method_id] : [];
        if(empty($method_info)){
            $this->responseJson([CODE => SUCCESS]);
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
        if(!empty($shipping_fee_result[CODE]) && $shipping_fee_result[CODE] == SUCCESS){
            $session->write(SHIPPING, !empty($shipping_fee_result[DATA]) ? $shipping_fee_result[DATA] : []);
        }

        $this->responseJson($shipping_fee_result);
    }
}