<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Core\Exception\Exception;

class CartComponent extends Component
{
    public $currency_default = null;
	public $controller = null;
    public $components = ['System', 'Utilities'];

    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->controller = $this->_registry->getController();

        $currency_default = TableRegistry::get('Currencies')->getDefaultCurrency();
        $this->currency_default = !empty($currency_default['code']) ? $currency_default['code'] : null;
    }

    public function caculateTotalCart(&$items = [])
    {
        if(empty($items)) return [];        

        $total = $total_quantity = $total_default = $default_total_item = 0;
        foreach ($items as $item_id => $item) {
            $quantity = !empty($item['quantity']) ? intval($item['quantity']) : 1;
            $price = !empty($item['price']) ? floatval($item['price']) : 0;

            // cập nhật lại giá sản phẩm theo giá khuyến mãi
            if(!empty($item['apply_special'])){
                $price = !empty($item['price_special']) ? floatval($item['price_special']) : 0;
            }

            $total_item = $price * $quantity;
            $total += $total_item;
            $total_quantity += $quantity;

            $items[$item_id]['total_item'] = $total_item;
            $items[$item_id]['price'] = $price;
        }

        $result = [
            'total' => $total,
            'total_quantity' => $total_quantity
        ];

        return $result;
    }

    public function checkQuantityInStore($product_item_id = null, $quantity_add = null)
    {
        $result = $this->System->getResponse([CODE => SUCCESS]);
        if(empty($product_item_id) || empty($quantity_add)) return $result;

        $settings = TableRegistry::get('Settings')->getSettingWebsite();
        $product_setting = !empty($settings['product']) ? $settings['product'] : null;

        if(empty($product_setting['check_quantity'])) return $result;

        $item_info = TableRegistry::get('ProductsItem')->getDetailProductItem($product_item_id, LANGUAGE, ['get_attribute' => true]);
        if(empty($item_info)) {
            return $this->System->getResponse([
                MESSAGE => __d('template', 'khong_tim_thay_thong_tin_san_pham')
            ]);
        }

        $quantity_available = !empty($item_info['quantity_available']) ? intval($item_info['quantity_available']) : 0;
        if($quantity_available >= intval($quantity_add)) return $result;

        $name_extend = !empty($item_info['name_extend']) ? $item_info['name_extend'] : null;
        return $this->System->getResponse([
            MESSAGE => __d('template', 'so_luong_san_pham_{0}_hien_khong_con_du_so_luong_de_dat_hang', $name_extend)
        ]);
    }

    private function formatCartByCurrency($cart_info = [], $currency_code = null)
    {
        if(empty($cart_info)) return [];

        if($currency_code == CURRENCY_CODE || empty($cart_info['items'])) return $cart_info;

        $items = $cart_info['items'];
        foreach($items as $k => $item){
            $items[$k]['default_price'] = !empty($item['price']) ? floatval($item['price']) : 0;
            $items[$k]['default_total_item'] = !empty($item['total_item']) ? floatval($item['total_item']) : 0;

            $items[$k]['price'] = $this->formatNumberByCurrentRate($item['price']);
            $items[$k]['total_item'] = $this->formatNumberByCurrentRate($item['total_item']);
            $items[$k][CURRENCY_PARAM] = CURRENCY_CODE;
        }

        $cart_info['items'] = $items;
                
        $cart_info['total_default'] = !empty($cart_info['total']) ? floatval($cart_info['total']) : 0;
        $cart_info['total'] = !empty($cart_info['total']) ? $this->formatNumberByCurrentRate($cart_info['total']) : 0;

        $cart_info['total_items_default'] = !empty($cart_info['total_items']) ? floatval($cart_info['total_items']) : 0;
        $cart_info['total_items'] = !empty($cart_info['total_items']) ? $this->formatNumberByCurrentRate($cart_info['total_items']) : 0;

        return $cart_info;
    }

    private function formatNumberByCurrentRate($value = null)
    {
        return !empty($value) ? round(floatval($value / CURRENCY_RATE), 2) : 0;
    }

    public function resetSessionCart($currency = null)
    {
        $session = $this->controller->getRequest()->getSession();
        $cart_info = $session->read(CART);

        if(empty($cart_info['items'])) return true;

        $cart = $items = [];
        foreach($cart_info['items'] as $product_item_id => $item){
            $quantity = !empty($item['quantity']) ? intval($item['quantity']) : 1;
            $item_info = TableRegistry::get('ProductsItem')->getDetailProductItem($product_item_id, LANGUAGE, ['get_attribute' => true]);
            if(empty($item_info)) continue;

            $items[$product_item_id] = $item_info;
            $items[$product_item_id]['quantity'] = $quantity;
        }

        $caculate_cart = $this->caculateTotalCart($items);
        $cart_info = [
            'items' => $items,
            'total_quantity' => !empty($caculate_cart['total_quantity']) ? intval($caculate_cart['total_quantity']) : null,
            'total' => !empty($caculate_cart['total']) ? $caculate_cart['total'] : null
        ];

        $session->write(CART, $cart_info);

        if($this->currency_default != CURRENCY_CODE){
            $cart_info = $this->formatCartByCurrency($cart_info);
        }        
        return true;
    }

    public function addProduct($data = [], $options = [])
    {
        if(!$this->controller->getRequest()->is('post') || empty($data)){
           return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $quantity = !empty($data['quantity']) ? intval($data['quantity']) : 1;
        $product_item_id = !empty($data['product_item_id']) ? intval($data['product_item_id']) : null;
        if(empty($product_item_id)){
            return $this->System->getResponse([MESSAGE => __d('template', 'khong_tim_thay_thong_tin_san_pham')]);   
        }
        
        $item_info = TableRegistry::get('ProductsItem')->getDetailProductItem($product_item_id, LANGUAGE, ['get_attribute' => true]);    
        if(empty($item_info)){
            return $this->System->getResponse([MESSAGE => __d('template', 'khong_tim_thay_thong_tin_san_pham')]);
        }

        $session = $this->controller->getRequest()->getSession();
        $cart_info = $session->read(CART);
        $items = !empty($cart_info['items']) ? $cart_info['items'] : [];
  
        if(empty($items[$product_item_id])){
            $items[$product_item_id] = $item_info;
            $items[$product_item_id]['quantity'] = $quantity;
        }else{
            $items[$product_item_id]['quantity'] = intval($items[$product_item_id]['quantity']) + $quantity;
        }
    
        // check quantity in store
        $check_quantity = $this->checkQuantityInStore($product_item_id, $items[$product_item_id]['quantity']);
        if(isset($check_quantity[CODE]) && $check_quantity[CODE] == ERROR){
            return $check_quantity;
        }

        $caculate_cart = $this->caculateTotalCart($items);
        $cart_info = [
            'items' => $items,
            'total_quantity' => !empty($caculate_cart['total_quantity']) ? intval($caculate_cart['total_quantity']) : null,
            'total' => !empty($caculate_cart['total']) ? $caculate_cart['total'] : null
        ];        

        $session->write(CART, $cart_info);
        $session->delete(COUPON);
        $session->delete(POINT);
        $session->delete(SHIPPING);

        if($this->currency_default != CURRENCY_CODE){
            $cart_info = $this->formatCartByCurrency($cart_info);
        }        
        
        return $this->System->getResponse([CODE => SUCCESS, MESSAGE => __d('template', 'cap_nhat_gio_hang_thanh_cong')]);
    }

    // this function work on api
    public function updateQuantityProduct($data = [], $options = [])
    {
        if(!$this->controller->getRequest()->is('post') || empty($data)){
           return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $session = $this->controller->getRequest()->getSession();
        $cart_info = $session->read(CART);
        if(empty($cart_info)){
            return $this->System->getResponse([
                STATUS => 511,
                MESSAGE => __d('template', 'da_het_phien_lam_viec_vui_long_chon_lai_san_pham')
            ]);
        }

        $items = !empty($cart_info['items']) ? $cart_info['items'] : [];
        $quantity = !empty($data['quantity']) ? intval($data['quantity']) : 1;
        $product_item_id = !empty($data['product_item_id']) ? intval($data['product_item_id']) : null;
        if(empty($product_item_id)){
            return $this->System->getResponse([MESSAGE => __d('template', 'khong_tim_thay_thong_tin_san_pham')]);
        }

        if(empty($items[$product_item_id])){
            return $this->System->getResponse([MESSAGE => __d('template', 'san_pham_chua_duoc_them_vao_gio_hang')]);
        }

        $item_info = TableRegistry::get('ProductsItem')->getDetailProductItem($product_item_id, LANGUAGE, ['get_attribute' => true]);    
        if(empty($item_info)){
            return $this->System->getResponse([MESSAGE => __d('template', 'khong_tim_thay_thong_tin_san_pham')]);
        }

        $items[$product_item_id]['quantity'] = $quantity;

        // check quantity in store
        $check_quantity = $this->checkQuantityInStore($product_item_id, $quantity);
        if(isset($check_quantity[CODE]) && $check_quantity[CODE] == ERROR){
            return $check_quantity;
        }

        $caculate_cart = $this->caculateTotalCart($items);
        $cart_info = [
            'items' => $items,
            'total_quantity' => !empty($caculate_cart['total_quantity']) ? intval($caculate_cart['total_quantity']) : null,
            'total' => !empty($caculate_cart['total']) ? $caculate_cart['total'] : null         
        ];

        $session->write(CART, $cart_info);
        $session->delete(COUPON);
        $session->delete(POINT);
        $session->delete(AFFILIATE);
        $session->delete(SHIPPING);

        if($this->currency_default != CURRENCY_CODE){
            $cart_info = $this->formatCartByCurrency($cart_info);
        }        

        return $this->System->getResponse([
            CODE => SUCCESS,
            MESSAGE => __d('template', 'cap_nhat_gio_hang_thanh_cong'),
            DATA => $cart_info
        ]);
    }

    public function removeProduct($data = [], $options = [])
    {
        if(!$this->controller->getRequest()->is('post') || empty($data)){
           return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $session = $this->controller->getRequest()->getSession();
        $cart_info = $session->read(CART);
        if(empty($cart_info)){
            return $this->System->getResponse([
                STATUS => 511,
                MESSAGE => __d('template', 'da_het_phien_lam_viec_vui_long_chon_lai_san_pham')
            ]);
        }

        $items = !empty($cart_info['items']) ? $cart_info['items'] : [];
        $product_item_id = !empty($data['product_item_id']) ? intval($data['product_item_id']) : null;
        if(empty($product_item_id)){
            return $this->System->getResponse([MESSAGE => __d('template', 'khong_tim_thay_thong_tin_san_pham')]);
        }

        if(!empty($items[$product_item_id])){
            unset($items[$product_item_id]);
        }

        $caculate_cart = $this->caculateTotalCart($items);

        $cart_info = [
            'items' => $items,
            'total_quantity' => !empty($caculate_cart['total_quantity']) ? intval($caculate_cart['total_quantity']) : null,
            'total' => !empty($caculate_cart['total']) ? $caculate_cart['total'] : null     
        ];

        $session->write(CART, $cart_info);
        $session->delete(COUPON);
        $session->delete(POINT);
        $session->delete(SHIPPING);
        
        if($this->currency_default != CURRENCY_CODE){
            $cart_info = $this->formatCartByCurrency($cart_info);
        }        

        return $this->System->getResponse([
            CODE => SUCCESS,
            MESSAGE => __d('template', 'cap_nhat_gio_hang_thanh_cong'),
            DATA => $cart_info
        ]);
    }

}
