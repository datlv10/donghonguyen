<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;

class ShippingComponent extends Component
{
	public $controller = null;
    public $components = ['System' ,'Utilities'];

    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->controller = $this->_registry->getController();
    }

    public function getListShippingMethod($city_id = null, $total_cart = 0)
    {        
        $methods = TableRegistry::get('ShippingsMethod')->getList(LANGUAGE);
        if(empty($methods)) return [];

        $result = [];
        foreach($methods as $method){
            $shipping_fee_result = $this->getFeeShipping($city_id, $total_cart, $method);
            if(empty($shipping_fee_result[CODE]) || $shipping_fee_result[CODE] != SUCCESS || empty($shipping_fee_result[DATA])) continue;
            $shipping_info = $shipping_fee_result[DATA];

            $method_id = !empty($shipping_info['id']) ? intval($shipping_info['id']) : null;
            if(!empty($method_id)) $result[$method_id] = $shipping_info;            
        }

        return $result;
    }

    public function getFeeShipping($city_id = null, $total_cart = 0, $method_info = [])
    {
        $method_id = !empty($method_info['id']) ? intval($method_info['id']) : null;
        if(empty($method_info)){
            return $this->System->getResponse([
                MESSAGE => __d('template', 'phuong_thuc_van_chuyen_khong_hop_le')
            ]);
        }        

        $general_shipping_fee = !empty($method_info['general_shipping_fee']) ? intval($method_info['general_shipping_fee']) : 0;
        $custom_config = !empty($method_info['custom_config']) ? $method_info['custom_config'] : [];

        $data_result = [
            'id' => $method_id,
            'fee' => $general_shipping_fee,
            'name' => !empty($method_info['name']) ? $method_info['name'] : null,
            'description' => !empty($method_info['description']) ? $method_info['description'] : null            
        ];
        
        if(empty($custom_config)){
            return $this->System->getResponse([
                CODE => SUCCESS,
                MESSAGE => __d('template', 'xu_ly_du_lieu_thanh_cong'),
                DATA => $data_result
            ]);
        }

        foreach($custom_config as $config){
            $order_from = !empty($config['order_from']) ? intval(str_replace(',', '', $config['order_from'])) : 0;
            $order_to = !empty($config['order_to']) ? intval(str_replace(',', '', $config['order_to'])) : 0;
            $order_shipping_fee = !empty($config['order_shipping_fee']) ? intval(str_replace(',', '', $config['order_shipping_fee'])) : 0;
            $order_location = !empty($config['order_location']) ? $config['order_location'] : [];

            $accept = true;
            $fee = $general_shipping_fee;

            if(!empty($order_from) && $order_from > $total_cart) $accept = false;
            if(!empty($order_to) && $order_to < $total_cart) $accept = false;
            if(!empty($order_location) && !in_array($city_id, $order_location)) $accept = false;
            
            if(!empty($order_from) || !empty($order_to) || !empty($order_location)) $fee = $order_shipping_fee;

            if($accept){
                $data_result['fee'] = $fee;
                return $this->System->getResponse([
                    CODE => SUCCESS,
                    MESSAGE => __d('template', 'xu_ly_du_lieu_thanh_cong'),
                    DATA => $data_result
                ]);
            }
        }

        return $this->System->getResponse([
            CODE => SUCCESS,
            MESSAGE => __d('template', 'xu_ly_du_lieu_thanh_cong'),
            DATA => []
        ]);
    }
}
