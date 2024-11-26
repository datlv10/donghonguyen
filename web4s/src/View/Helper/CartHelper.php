<?php
declare(strict_types=1);

namespace App\View\Helper;

use Cake\View\Helper;
use Cake\ORM\TableRegistry;

class CartHelper extends Helper
{
    /** Lấy thông tin cart
     * 
     * 
     * {assign var = cart_info value = $this->Cart->getCartInfo()}
     * 
    */
    public function getCartInfo()
    {
        $cart_info = $this->getView()->getRequest()->getSession()->read(CART);

        $currency_default = TableRegistry::get('Currencies')->getDefaultCurrency();
        $currency_default = !empty($currency_default['code']) ? $currency_default['code'] : null;    

        if($currency_default != CURRENCY_CODE){
            $cart_info = $this->formatCartByCurrency($cart_info);
        }

        return $cart_info;      
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
}
