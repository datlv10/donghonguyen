<?php

namespace App\Lib\Shipping;

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

class ShippingUtilities
{
    public function getResponse($params = []) 
    {
        $code = ERROR;
        if(!empty($params[CODE]) && in_array($params[CODE], [SUCCESS, ERROR])){
            $code = $params[CODE];
        }

        $message = !empty($params[MESSAGE]) ? $params[MESSAGE] : null;
        if(empty($params[MESSAGE]) && $code == ERROR){
            $message = __d('template', 'xu_ly_du_lieu_khong_thanh_cong');
        }

        if(empty($params[MESSAGE]) && $code == SUCCESS){
            $message = __d('template', 'xu_ly_du_lieu_thanh_cong');
        }
        
        $result = [
            CODE => $code,
            MESSAGE => $message
        ];

        if(isset($params[DATA])){
            $result[DATA] = !empty($params[DATA]) ? $params[DATA] : [];
        }

        return $result;
    }

    public function exchangeLengthUnit($value = null, $current_unit = null, $to_unit = 'cm')
    {
        if(empty($value)) return 0;

        $list_unit = Configure::read('LENGTH_UNIT');
        if(empty($list_unit[$current_unit]))  return $value;

        $result = $value;

        switch($current_unit){
            case 'mm':
                switch($to_unit){
                    // mm -> mm
                    case 'mm':
                        $result = $value;
                    break;

                    // mm -> cm
                    case 'cm':
                        $result = $value / 10;
                    break;

                    // mm -> m
                    case 'm':
                    $result = $value / 1000;
                    break;
                }
            break;

            case 'cm':
                switch($to_unit){
                    // cm -> mm
                    case 'mm':
                        $result = $value * 10;
                    break;

                    // cm -> cm
                    case 'cm':
                        $result = $value;
                    break;

                    // cm -> m
                    case 'm':
                        $result = $value / 100;
                    break;
                }
            break;

            case 'm':
                switch($to_unit){
                    // m -> mm
                    case 'mm':
                        $result = $value * 1000;
                    break;

                    // m -> cm
                    case 'cm':
                        $result = $value * 100;
                    break;

                    // m -> m
                    case 'm':
                        $result = $value;
                    break;
                }
            break;
        }

        return $result;
    }

    public function exchangeWeightUnit($value = null, $current_unit = null, $to_unit = 'g')
    {
        if(empty($value)) return 0;

        $list_unit = Configure::read('WEIGTH_UNIT');
        if(empty($list_unit[$current_unit]))  return $value;

        $result = $value;
        switch($current_unit){
            case 'g':
                switch($to_unit){
                    // g -> g
                    case 'g':
                        $result = $value;
                    break;

                    // g -> kg
                    case 'kg':
                        $result = $value / 1000;
                    break;
                }
            break;

            case 'kg':
                switch($to_unit){
                    // kg -> g
                    case 'g':
                        $result = $value * 1000;
                    break;

                    // kg -> kg
                    case 'kg':
                        $result = $value;
                    break;
                }
            break;
        }

        return $result;
    }

    public function parseDataItems($items = [], $to_weight_unit = 'g', $to_length_unit = 'cm')
    {
        if(empty($items)) return [];

        $lang = 'vi';

        $result = [];
        foreach($items as $item){
            $product_item_id = !empty($item['product_item_id']) ? intval($item['product_item_id']) : null;
            if(empty($product_item_id)) continue;
        
            $item_info = TableRegistry::get('ProductsItem')->getDetailProductItem($product_item_id, $lang);
            
            if(empty($item_info)) continue;

            $price = !empty($item['price']) ? floatval($item['price']) : null;
            $quantity = !empty($item['quantity']) ? floatval($item['quantity']) : null;

            $width_unit = !empty($item_info['width_unit']) ? $item_info['width_unit'] : null;
            $width_unit = !empty($item_info['width_unit']) ? $item_info['width_unit'] : null;
            $length_unit = !empty($item_info['length_unit']) ? $item_info['length_unit'] : null;
            $weight_unit = !empty($item_info['weight_unit']) ? $item_info['weight_unit'] : null;

            $length = !empty($item_info['length']) ? $this->exchangeLengthUnit(intval($item_info['length']), $width_unit, $to_length_unit) : null;
            $width = !empty($item_info['width']) ? $this->exchangeLengthUnit(intval($item_info['width']), $width_unit, $to_length_unit) : null;
            $height = !empty($item_info['height']) ? $this->exchangeLengthUnit(intval($item_info['height']), $width_unit, $to_length_unit) : null;
            $weight = !empty($item_info['weight']) ? $this->exchangeWeightUnit(intval($item_info['weight']), $weight_unit, $to_weight_unit) : null;

            $result[] = [
                'name' => !empty($item_info['name_extend']) ? $item_info['name_extend'] : null,
                'code' => !empty($item_info['code']) ? $item_info['code'] : null,
                'quantity' => $quantity,
                'price' => $price,
                'weight' => $weight,
                'length' => $length,
                'width' => $width,
                'height' => $height
            ];
        }

        return $result;
    }

    public function isJson($json_str = null)
    {
        return is_string($json_str) && is_array(json_decode($json_str, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }

}

?>