<?php

namespace App\Lib\Shipping;

use Cake\ORM\TableRegistry;
use App\Lib\Shipping\ShippingUtilities;
use Cake\Core\Exception\Exception;
use Cake\Http\Client;

class GiaoHangNhanh
{
    protected $base_url = '';
    protected $config = [];

    public function __construct($params)
    {
        $carries = TableRegistry::get('ShippingsCarrier')->getList();
        $ghn_info = !empty($carries[GIAO_HANG_NHANH]) ? $carries[GIAO_HANG_NHANH] : [];

        $this->config = !empty($ghn_info['config']) ? $ghn_info['config'] : [];

        $mode = !empty($this->config['mode']) ? $this->config['mode'] : null;
        if ($mode == LIVE) {
            $this->base_url = 'https://online-gateway.ghn.vn';
        } else {
            $this->base_url = 'https://dev-online-gateway.ghn.vn';
        }
    }

    public function calculateFee($params = [])
    {
        $utilities = new ShippingUtilities();
        $http = new Client();
        $token = !empty($this->config['api_token']) ? $this->config['api_token'] : null;
        $stores = !empty($this->config['stores']) ? $this->config['stores'] : null;

        $shop_id = !empty($params['shop_id']) ? intval($params['shop_id']) : null;
        $district_id = !empty($params['district_id']) ? intval($params['district_id']) : null;
        $ward_id = !empty($params['ward_id']) ? intval($params['ward_id']) : null;

        $weight = !empty($params['weight']) ? intval($params['weight']) : 500;
        $length = !empty($params['length']) ? intval($params['length']) : 20;
        $width = !empty($params['width']) ? intval($params['width']) : 20;
        $height = !empty($params['height']) ? intval($params['height']) : 20;

        // validate data
        if(empty($token) || empty($stores)){
            return $utilities->getResponse([
                MESSAGE => __d('admin', 'thong_tin_cau_hinh_chua_hop_le')
            ]);
        }

        if(empty($stores[$shop_id])) $shop_id = null;
        if(empty($shop_id)){
            $shop_id = array_key_first($stores);
        }

        $shop_info = !empty($stores[$shop_id]) ? $stores[$shop_id] : [];

        $from_district_id = null;
        if(empty($shop_info)){
            $shop_info = array_shift($stores);
            $shop_id = !empty($shop_info['id']) ? intval($shop_info['id']) : null;
        }

        $from_district_id = !empty($shop_info['district_id']) ? intval($shop_info['district_id']) : null;

        if(empty($shop_id) || empty($from_district_id)){
            return $utilities->getResponse([
                MESSAGE => __d('admin', 'thong_tin_cau_hinh_chua_hop_le')
            ]);
        }

        $district_info = TableRegistry::get('ShippingsDistrict')->getDistrictInfo($district_id, GIAO_HANG_NHANH);
        $to_district_id = !empty($district_info['carrier_district_id']) ? $district_info['carrier_district_id'] : null;

        $ward_info = TableRegistry::get('ShippingsWard')->getWardInfo($ward_id, GIAO_HANG_NHANH);
        $to_ward_code = !empty($ward_info['carrier_ward_code']) ? $ward_info['carrier_ward_code'] : null;

        $data_get_service = [
            'from_district' => $from_district_id,
            'to_district' => $to_district_id,
            'shop_id' => $shop_id
        ];

        // lấy danh sách dịch vụ của GHN
        $ghn_services = [];
        try{
            $url = $this->base_url . '/shiip/public-api/v2/shipping-order/available-services';
            $response = $http->post($url, json_encode($data_get_service), [
                'headers' => [
                    'token' => $token
                ],
                'type' => 'json'
            ]);

            $json_response = $response->getJson();
            if(!empty($json_response[CODE]) && $json_response[CODE] == 200 && !empty($json_response[DATA])){
                $ghn_services = !empty($json_response[DATA]) ? $json_response[DATA] : [];
            }
        }catch (NetworkException $e) {
            return $utilities->getResponse([
                MESSAGE => __d('admin', 'khong_ket_noi_duoc_den_api_{0}', 'GHN')
            ]);
        }

        if(empty($ghn_services)) {
            return $utilities->getResponse([
                MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_dich_vu')
            ]);
        }

        $number = 0;
        $result = [];
        foreach($ghn_services as $ghn_service){
            $service_id = !empty($ghn_service['service_id']) ? $ghn_service['service_id'] : null;
            $service_type_id = !empty($ghn_service['service_type_id']) ? $ghn_service['service_type_id'] : null;
            $service_short_name = !empty($ghn_service['short_name']) ? $ghn_service['short_name'] : null;

            $data_get_fee = [
                'shop_id' => $shop_id,
                'service_id' => $service_id,
                'service_type_id' => $service_type_id,
                'insurance_value' => null,
                'coupon' => null,
                'from_district_id' => $from_district_id,
                'to_district_id' => $to_district_id,
                'to_ward_code' => $to_ward_code,

                'weight' => $weight,
                'length' => $length,
                'width' => $width,
                'height' => $height,
            ];

            $fee_info = [];
            try{
                $url = $this->base_url . '/shiip/public-api/v2/shipping-order/fee';
                $response = $http->post($url, json_encode($data_get_fee), [
                    'headers' => [
                        'token' => $token
                    ],
                    'type' => 'json'
                ]);

                $json_response = $response->getJson();
                if(!empty($json_response[CODE]) && $json_response[CODE] == 200 && !empty($json_response[DATA])){
                    $fee_info = !empty($json_response[DATA]) ? $json_response[DATA] : [];
                }
            }catch (NetworkException $e) {
                return $utilities->getResponse([
                    MESSAGE => __d('admin', 'khong_ket_noi_duoc_den_api_{0}', 'GHN')
                ]);
            }

            if(empty($fee_info)){
                return $utilities->getResponse([
                    MESSAGE => __d('admin', 'khong_ket_noi_duoc_den_api_{0}', 'GHN')
                ]);
            }

            $service_name = $service_short_name;
            if(empty($service_name)){
                $number ++;
                $service_name = __d('admin', 'phuong_thuc') . ' ' . str_pad($number, 2, '0', STR_PAD_LEFT);
            }

            $result[] = [
                'service_id' => $service_id,
                'service_type_id' => $service_type_id,
                'service_name' => $service_name,
                'fee' => !empty($fee_info['total']) ? intval($fee_info['total']) : 0
            ];
        }
                
        return $utilities->getResponse([
            CODE => SUCCESS,
            DATA => $result
        ]);
    }

    public function createOrder($params = [])
    {
        $utilities = new ShippingUtilities();
        $http = new Client();

        // params
        $token = !empty($this->config['api_token']) ? $this->config['api_token'] : null;
        $stores = !empty($this->config['stores']) ? $this->config['stores'] : null;
        
        $shop_id = !empty($params['shop_id']) ? intval($params['shop_id']) : null;

        $full_name = !empty($params['full_name']) ? $params['full_name'] : null;
        $phone = !empty($params['phone']) ? $params['phone'] : null;
        $city_id = !empty($params['city_id']) ? intval($params['city_id']) : null;
        $district_id = !empty($params['district_id']) ? intval($params['district_id']) : null;
        $ward_id = !empty($params['ward_id']) ? intval($params['ward_id']) : null;
        $address = !empty($params['address']) ? $params['address'] : null;

        $shipping_code = !empty($params['shipping_code']) ? $params['shipping_code'] : null;
        $client_order_total = !empty($params['order_total']) ? $params['order_total'] : null;
        
        $cod_money = !empty($params['cod_money']) ? floatval($params['cod_money']) : null;
        $note = !empty($params['note']) ? $params['note'] : null;
        $required_note = !empty($params['required_note']) ? $params['required_note'] : null;
        $items = !empty($params['items']) ? $params['items'] : [];

        $weight = !empty($params['weight']) ? intval($params['weight']) : WEIGHT_PRODUCT_DEFAULT;
        $length = !empty($params['length']) ? intval($params['length']) : LENGTH_PRODUCT_DEFAULT;
        $width = !empty($params['width']) ? intval($params['width']) : WIDTH_PRODUCT_DEFAULT;
        $height = !empty($params['height']) ? intval($params['height']) : HEIGHT_PRODUCT_DEFAULT;

        $service_type_id = isset($params['service_type_id']) ? intval($params['service_type_id']) : null;
        $service_id = !empty($params['service_id']) ? intval($params['service_id']) : null;
        $payment_type_id = 1; //1: Shop/Seller, 2: Buyer/Consignee.
            

        // validate data
        if(empty($token) || empty($stores)){
            return $utilities->getResponse([
                MESSAGE => __d('admin', 'thong_tin_cau_hinh_chua_hop_le')
            ]);
        }

        if(empty($stores[$shop_id])) $shop_id = null;
        if(empty($shop_id)){
            $shop_id = array_key_first($stores);
        }

        if(empty($shop_id)){
            return $utilities->getResponse([
                MESSAGE => __d('admin', 'thong_tin_cau_hinh_chua_hop_le')
            ]);
        }

        if(empty($district_id) || empty($ward_id)){
            return $utilities->getResponse([
                MESSAGE => __d('admin', 'du_lieu_khong_hop_le')
            ]);
        }

        $district_info = TableRegistry::get('ShippingsDistrict')->getDistrictInfo($district_id, GIAO_HANG_NHANH);
        $to_district_id = !empty($district_info['carrier_district_id']) ? $district_info['carrier_district_id'] : null;

        $ward_info = TableRegistry::get('ShippingsWard')->getWardInfo($ward_id, GIAO_HANG_NHANH);
        $to_ward_code = !empty($ward_info['carrier_ward_code']) ? $ward_info['carrier_ward_code'] : null;

        $data_create_order = [
            'shop_id' => $shop_id,
            'to_name' => $full_name,
            'to_phone' => $phone,
            'to_address' => $address,
            'to_ward_code' => $to_ward_code,
            'to_district_id' => $to_district_id,
            'cod_amount' => $cod_money,
            'weight' => $weight,
            'length' => $length,
            'width' => $width,
            'height' => $height,
            'service_id' => $service_id,
            'service_type_id' => $service_type_id,
            'payment_type_id' => $payment_type_id,
            'note' => $note,
            'required_note' => $required_note,
            'client_order_code' => $shipping_code,
            'insurance_value' => $client_order_total,
            'items' => $utilities->parseDataItems($items)
        ];

        // gửi đơn sang hãng vận chuyển
        $ghn_order = [];
        try{
            $url = $this->base_url . '/shiip/public-api/v2/shipping-order/create';
            $response = $http->post($url, json_encode($data_create_order), [
                'headers' => [
                    'token' => $token
                ],
                'type' => 'json'
            ]);

            $json_response = $response->getJson();

            if(!empty($json_response[CODE]) && $json_response[CODE] == 200 && !empty($json_response[DATA])){
                $ghn_order = !empty($json_response[DATA]) ? $json_response[DATA] : [];
            }else{
                return $utilities->getResponse([
                    MESSAGE => !empty($json_response['code_message_value']) ? $json_response['code_message_value'] : __d('admin', 'gui_don_sang_hang_van_chuyen_khong_thanh_cong')
                ]);
            }
        }catch (NetworkException $e) {
            return $utilities->getResponse([
                MESSAGE => __d('admin', 'khong_ket_noi_duoc_den_api_{0}', 'GHN')
            ]);
        }

        if(empty($ghn_order)) {
            return $utilities->getResponse([
                MESSAGE => __d('admin', 'gui_don_sang_hang_van_chuyen_khong_thanh_cong')
            ]);
        }

        $result = [
            'carrier_order_code' => !empty($ghn_order['order_code']) ? $ghn_order['order_code'] : null,
            'carrier_shipping_fee' => !empty($ghn_order['total_fee']) ? intval($ghn_order['total_fee']) : null
        ];

        return $utilities->getResponse([
            CODE => SUCCESS,
            DATA => $result
        ]);
    }

    public function cancelOrder($params = [])
    {
        $utilities = new ShippingUtilities();
        $http = new Client();

        $token = !empty($this->config['api_token']) ? $this->config['api_token'] : null;
        $order_code = !empty($params['order_code']) ? $params['order_code'] : null;

        // validate data
        if(empty($token)){
            return $utilities->getResponse([
                MESSAGE => __d('admin', 'thong_tin_cau_hinh_chua_hop_le')
            ]);
        }

        if(empty($order_code)){
            return $utilities->getResponse([
                MESSAGE => __d('admin', 'du_lieu_khong_hop_le')
            ]);
        }

        try{
            $url = $this->base_url . '/shiip/public-api/v2/switch-status/cancel';
            $response = $http->post($url, json_encode([
                'order_codes' => [$order_code]
            ]), [
                'headers' => [
                    'token' => $token
                ],
                'type' => 'json'
            ]);

            $json_response = $response->getJson();
            if(empty($json_response[CODE]) || $json_response[CODE] != 200){
                return $utilities->getResponse([
                    MESSAGE => !empty($json_response['message']) ? $json_response['message'] : __d('admin', 'huy_don_ben_hang_van_chuyen_khong_thanh_cong')
                ]);
            }
        }catch (NetworkException $e) {
            return $utilities->getResponse([
                MESSAGE => __d('admin', 'khong_ket_noi_duoc_den_api_{0}', 'GHN')
            ]);
        }

        return $utilities->getResponse([
            CODE => SUCCESS
        ]);
    }

    public function webhooks($params = []) 
    {
        $utilities = new PaymentUtilities();       
    }
}

?>