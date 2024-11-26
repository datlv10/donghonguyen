<?php

namespace App\Controller;

use Cake\Event\EventInterface;
use Cake\ORM\TableRegistry;

class CartController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
    }

	public function addProduct()
	{
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
    
        $result = $this->loadComponent('Cart')->addProduct($data);
        $this->responseJson($result);
    }

    public function removeProduct()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        $result = $this->loadComponent('Cart')->removeProduct($data);
        $this->responseJson($result);
    }

    public function updateCart()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $item_updated = !empty($data['items']) ? $data['items'] : [];
        if(empty($item_updated)){
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $session = $this->request->getSession();        
        $cart_info = $session->read(CART);
        $items = !empty($cart_info['items']) ? $cart_info['items'] : [];
        if(empty($items)){
            $this->responseJson([MESSAGE => __d('template', 'khong_tim_thay_thong_tin_gio_hang')]);
        }

        $settings = TableRegistry::get('Settings')->getSettingWebsite();
        $product_setting = !empty($settings['product']) ? $settings['product'] : null;

        foreach ($item_updated as $key => $item) {
            $product_item_id = !empty($item['product_item_id']) ? intval($item['product_item_id']) : null;
            $quantity = !empty($item['quantity']) ? intval($item['quantity']) : 1;
            $item_info = TableRegistry::get('ProductsItem')->getDetailProductItem($product_item_id, LANGUAGE, ['get_attribute' => true]);
            if(empty($item_info)) continue;

            $items[$product_item_id] = $item_info;
            $items[$product_item_id]['quantity'] = $quantity;

            // check quantity in store
            $check_quantity = $this->loadComponent('Cart')->checkQuantityInStore($product_item_id, $quantity);
            if(isset($check_quantity[CODE]) && $check_quantity[CODE] == ERROR){
                $this->responseJson($check_quantity);
            }
        }

        $caculate_cart = $this->loadComponent('Cart')->caculateTotalCart($items);
        $cart_info = [
            'items' => $items,
            'total' => !empty($caculate_cart['total']) ? $caculate_cart['total'] : null,
            'total_default' => !empty($caculate_cart['total_default']) ? $caculate_cart['total_default'] : null,
            'total_quantity' => !empty($caculate_cart['total_quantity']) ? $caculate_cart['total_quantity'] : null,
        ];

        $session->write(CART, $cart_info);
        $session->delete(COUPON);
        $session->delete(POINT);
        $session->delete(AFFILIATE);
        $session->delete(SHIPPING);
        
        $this->responseJson([CODE => SUCCESS, MESSAGE => __d('template', 'cap_nhat_gio_hang_thanh_cong')]);
    }

    public function reloadSidebarCart()
    {
        $this->viewBuilder()->enableAutoLayout(false);
        $this->render('content_sidebar_cart');
    }


}