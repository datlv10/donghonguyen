<?php

namespace App\Lib\Shipping;

use Cake\ORM\TableRegistry;
use App\Lib\Shipping\ShippingUtilities;
use Cake\Core\Exception\Exception;
use Cake\Http\Client;

class GiaoHangTietKiem
{
    protected $base_url = '';
    protected $config = [];

    public function __construct($params)
    {
        $carries = TableRegistry::get('ShippingsCarrier')->getList();
        $ghn_info = !empty($carries[GIAO_HANG_TIET_KIEM]) ? $carries[GIAO_HANG_TIET_KIEM] : [];

        $this->config = !empty($ghn_info['config']) ? $ghn_info['config'] : [];

        $mode = !empty($this->config['mode']) ? $this->config['mode'] : null;
        if ($mode == LIVE) {
            $this->base_url = 'https://services.giaohangtietkiem.vn';
        } else {
            $this->base_url = 'https://services.ghtklab.com';
        }
    }

    private function parseDataLocation($params = [])
    {
        $stores = !empty($this->config['stores']) ? $this->config['stores'] : null;

        $shop_id = !empty($params['shop_id']) ? intval($params['shop_id']) : null;
        $city_id = !empty($params['city_id']) ? intval($params['city_id']) : null;
        $district_id = !empty($params['district_id']) ? intval($params['district_id']) : null;
        $ward_id = !empty($params['ward_id']) ? intval($params['ward_id']) : null;
        $address = !empty($params['address']) ? $params['address']  : null;
        
        $utilities = new ShippingUtilities();

        // validate data
        if(empty($city_id) || empty($district_id) || empty($ward_id) || empty($address)){
            return $utilities->getResponse([
                MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_tinh_thanh')
            ]);
        }

        if(empty($stores)){
            return $utilities->getResponse([
                MESSAGE => __d('admin', 'thong_tin_cau_hinh_chua_hop_le')
            ]);
        }

        if(empty($stores[$shop_id])) $shop_id = null;
        if(empty($shop_id)){
            $shop_id = array_key_first($stores);
        }

        $shop_info = !empty($stores[$shop_id]) ? $stores[$shop_id] : [];
        $pick_name = !empty($shop_info['name']) ? $shop_info['name'] : null;
        $pick_tel = !empty($shop_info['phone']) ? $shop_info['phone'] : null;

        $from_district_id = null;
        if(empty($shop_info)){
            $shop_info = array_shift($stores);
            $shop_id = !empty($shop_info['id']) ? intval($shop_info['id']) : null;
        }

        $shop_address = !empty($shop_info['address']) ? $shop_info['address'] : null;
        if(empty($shop_id) || empty($shop_address)){
            return $utilities->getResponse([
                MESSAGE => __d('admin', 'thong_tin_cau_hinh_chua_hop_le')
            ]);
        }

        $pick_province = $pick_district = $pick_ward = $pick_address = null;
        $shop_address_split = explode(', ', $shop_address);
        $store_address_length = count($shop_address_split);

        if($store_address_length < 3){
            return $utilities->getResponse([
                MESSAGE => __d('admin', 'thong_tin_cau_hinh_chua_hop_le')
            ]);
        }

        $pick_province = !empty($shop_address_split[$store_address_length - 1]) ? $shop_address_split[$store_address_length - 1] : null;
        $pick_district = !empty($shop_address_split[$store_address_length - 2]) ? $shop_address_split[$store_address_length - 2] : null;

        if($store_address_length > 3){
            $pick_ward = !empty($shop_address_split[$store_address_length - 3]) ? $shop_address_split[$store_address_length - 3] : null;
        }

        $pick_address = str_replace(', ' . $pick_province, '', $shop_address);
        $pick_address = str_replace(', ' . $pick_district, '', $pick_address);
        if(!empty($pick_ward)){
            $pick_address = str_replace(', ' . $pick_ward, '', $pick_address);
        }

        if(empty($pick_province) || empty($pick_district) || empty($pick_address)){
            return $utilities->getResponse([
                MESSAGE => __d('admin', 'thong_tin_cau_hinh_chua_hop_le')
            ]);
        }

        $city_info = TableRegistry::get('Cities')->find()->where(['id' => $city_id])->select(['id', 'name'])->first();
        $district_info = TableRegistry::get('Districts')->find()->where(['id' => $district_id])->select(['id', 'name'])->first();
        $ward_info = TableRegistry::get('Wards')->find()->where(['id' => $ward_id])->select(['id', 'name'])->first();

        $city_name = !empty($city_info['name']) ? $city_info['name'] : null;
        $district_name = !empty($district_info['name']) ? $district_info['name'] : null;
        $ward_name = !empty($ward_info['name']) ? $ward_info['name'] : null;
        if(empty($city_name) || empty($district_name) || empty($ward_name)){
            return $utilities->getResponse([
                MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_tinh_thanh')
            ]);
        }

        $result = [
            'pick_name' => $pick_name,
            'pick_tel' => $pick_tel,
            'pick_province' => $pick_province,
            'pick_district' => $pick_district,
            'pick_ward' => $pick_ward,
            'pick_address' => $pick_address,

            'city_name' => $city_name,
            'district_name' => $district_name,
            'ward_name' => $ward_name,
            'address' => $address
        ];

        return $utilities->getResponse([
            CODE => SUCCESS,
            DATA => $result
        ]);
    }

    public function calculateFee($params = [])
    {
        $utilities = new ShippingUtilities();

        $token = !empty($this->config['api_token']) ? $this->config['api_token'] : null;
        if(empty($token)){
            return $utilities->getResponse([
                MESSAGE => __d('admin', 'thong_tin_cau_hinh_chua_hop_le')
            ]);
        }        

        $location_result = $this->parseDataLocation($params);
        $data_location = !empty($location_result[DATA]) ? $location_result[DATA] : [];
        if(empty($location_result[CODE]) || empty($data_location) || $location_result[CODE] != SUCCESS){
            return $utilities->getResponse([
                MESSAGE => !empty($location_result[MESSAGE]) ? $location_result[MESSAGE] : __d('admin', 'tham_so_api_chua_hop_le')
            ]);
        }

        $weight = !empty($params['weight']) ? intval($params['weight']) : WEIGHT_PRODUCT_DEFAULT;
        
        $result = [];
        $ghtk_services = [
            [
                'deliver_option' => 'xteam',
                'service_name' => __d('admin', 'giao_nhanh')
            ],
            [
                'deliver_option' => 'none',
                'service_name' => __d('admin', 'giao_thuong')
            ]
        ];
        foreach($ghtk_services as $ghtk_service){
            $deliver_option = !empty($ghtk_service['deliver_option']) ? $ghtk_service['deliver_option'] : null;
            $service_name = !empty($ghtk_service['service_name']) ? $ghtk_service['service_name'] : null;

            $data_get_fee = [
                'pick_province' => !empty($data_location['pick_province']) ? $data_location['pick_province'] : null,
                'pick_district' => !empty($data_location['pick_district']) ? $data_location['pick_district'] : null,
                'pick_ward' => !empty($data_location['pick_ward']) ? $data_location['pick_ward'] : null,
                'pick_address' => !empty($data_location['pick_address']) ? $data_location['pick_address'] : null,

                'province' => !empty($data_location['city_name']) ? $data_location['city_name'] : null,
                'district' => !empty($data_location['district_name']) ? $data_location['district_name'] : null,
                'ward' => !empty($data_location['ward_name']) ? $data_location['ward_name'] : null,
                'address' => !empty($data_location['address']) ? $data_location['address'] : null,

                'weight' => $weight,
                'value' => 0,
                'deliver_option' => $deliver_option
            ];

            $fee_info = [];
            try{            
                $url = $this->base_url . '/services/shipment/fee';
                $http = new Client();
                $response = $http->get($url, $data_get_fee, [
                    'headers' => [
                        'token' => $token
                    ]
                ]);

                $json_response = $response->getJson();
                if(!empty($json_response['success']) && !empty($json_response['fee'])){
                    $fee_info = !empty($json_response['fee']) ? $json_response['fee'] : [];
                }
            }catch (NetworkException $e) {
                return $utilities->getResponse([
                    MESSAGE => __d('admin', 'khong_ket_noi_duoc_den_api_{0}', 'GHTK')
                ]);
            }

            if(empty($fee_info)){
                return $utilities->getResponse([
                    MESSAGE => __d('admin', 'khong_ket_noi_duoc_den_api_{0}', 'GHTK')
                ]);
            }

            $result[] = [
                'service_id' => $deliver_option,
                'service_type_id' => null,
                'service_name' => $service_name,
                'fee' => !empty($fee_info['fee']) ? intval($fee_info['fee']) : 0
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

        // params
        $token = !empty($this->config['api_token']) ? $this->config['api_token'] : null;
        $stores = !empty($this->config['stores']) ? $this->config['stores'] : null;
        
        $shop_id = !empty($params['shop_id']) ? intval($params['shop_id']) : null;

        $full_name = !empty($params['full_name']) ? $params['full_name'] : null;
        $phone = !empty($params['phone']) ? $params['phone'] : null;  


        $client_order_code = !empty($params['shipping_code']) ? $params['shipping_code'] : null;
        $client_order_total = !empty($params['order_total']) ? $params['order_total'] : null;

        $cod_money = !empty($params['cod_money']) ? floatval($params['cod_money']) : null;
        $note = !empty($params['note']) ? $params['note'] : null;
        $required_note = !empty($params['required_note']) ? $params['required_note'] : null;
        $items = !empty($params['items']) ? $params['items'] : [];

        $weight = !empty($params['weight']) ? intval($params['weight']) : WEIGHT_PRODUCT_DEFAULT;
        $service_id = !empty($params['service_id']) ? $params['service_id'] : null;

        if(empty($token)){
            return $utilities->getResponse([
                MESSAGE => __d('admin', 'thong_tin_cau_hinh_chua_hop_le')
            ]);
        }

        $location_result = $this->parseDataLocation($params);
        $data_location = !empty($location_result[DATA]) ? $location_result[DATA] : [];
        if(empty($location_result[CODE]) || empty($data_location) || $location_result[CODE] != SUCCESS){
            return $utilities->getResponse([
                MESSAGE => !empty($location_result[MESSAGE]) ? $location_result[MESSAGE] : __d('admin', 'tham_so_api_chua_hop_le')
            ]);
        }

        $data_create_order = [
            'products' => $utilities->parseDataItems($items, 'g', 'kg'),
            'order' => [
                'id' => $client_order_code,
                'pick_money' => $cod_money,

                'pick_name' => !empty($data_location['pick_name']) ? $data_location['pick_name'] : null,
                'pick_tel' => !empty($data_location['pick_tel']) ? $data_location['pick_tel'] : null,
                'pick_address_id' => $shop_id,
                'pick_province' => !empty($data_location['pick_province']) ? $data_location['pick_province'] : null,
                'pick_district' => !empty($data_location['pick_district']) ? $data_location['pick_district'] : null,
                'pick_ward' => !empty($data_location['pick_ward']) ? $data_location['pick_ward'] : null,
                'pick_address' => !empty($data_location['pick_address']) ? $data_location['pick_address'] : null,

                'name' => $full_name,
                'tel' => $phone,
                'province' => !empty($data_location['city_name']) ? $data_location['city_name'] : null,
                'district' => !empty($data_location['district_name']) ? $data_location['district_name'] : null,
                'ward' => !empty($data_location['ward_name']) ? $data_location['ward_name'] : null,
                'address' => !empty($data_location['address']) ? $data_location['address'] : null,
                'hamlet' => 'Khác',

                'is_freeship' => 1, // luôn luôn = 1, không + thêm giá vận chuyển từ ghtk vì đã được tính vào cod_money rồi
                'deliver_option' => $service_id,
                'note' => $note,
                'value' => $client_order_total,
                'weight_option' => 'gram',
                'total_weight' => $weight
            ]
        ];

        // gửi đơn sang hãng vận chuyển
        $ghtk_order = [];
        try{
            $url = $this->base_url . '/services/shipment/order';
            $http = new Client();
            $response = $http->post($url, json_encode($data_create_order), [
                'headers' => [
                    'token' => $token
                ],
                'type' => 'json'
            ]);            
            $json_response = $response->getJson();            
            if(!empty($json_response['success'])){
                $ghtk_order = !empty($json_response['order']) ? $json_response['order'] : [];
            }else{
                return $utilities->getResponse([
                    MESSAGE => !empty($json_response['message']) ? $json_response['message'] : __d('admin', 'gui_don_sang_hang_van_chuyen_khong_thanh_cong')
                ]);
            }
        }catch (NetworkException $e) {
            return $utilities->getResponse([
                MESSAGE => __d('admin', 'khong_ket_noi_duoc_den_api_{0}', 'GHTK')
            ]);
        }

        if(empty($ghtk_order)) {
            return $utilities->getResponse([
                MESSAGE => __d('admin', 'gui_don_sang_hang_van_chuyen_khong_thanh_cong')
            ]);
        }

        $result = [
            'carrier_order_code' => !empty($ghtk_order['label']) ? $ghtk_order['label'] : null,
            'carrier_shipping_fee' => !empty($ghtk_order['fee']) ? intval($ghtk_order['fee']) : null
        ];        

        return $utilities->getResponse([
            CODE => SUCCESS,
            DATA => $result
        ]);
    }

    public function cancelOrder($params = [])
    {
        $utilities = new ShippingUtilities();

        // params
        $token = !empty($this->config['api_token']) ? $this->config['api_token'] : null;
        $order_code = !empty($params['order_code']) ? $params['order_code'] : null;

        if(empty($token)){
            return $utilities->getResponse([
                MESSAGE => __d('admin', 'thong_tin_cau_hinh_chua_hop_le')
            ]);
        }

        try{
            $url = $this->base_url . '/services/shipment/cancel/' . $order_code;
            $http = new Client();
            $response = $http->post($url, json_encode($order_code), [
                'headers' => [
                    'token' => $token
                ],
                'type' => 'json'
            ]);

            $json_response = $response->getJson();

            if(empty($json_response['success'])){
                return $utilities->getResponse([
                    MESSAGE => !empty($json_response['message']) ? $json_response['message'] : __d('admin', 'huy_don_ben_hang_van_chuyen_khong_thanh_cong')
                ]);
            }
        }catch (NetworkException $e) {
            return $utilities->getResponse([
                MESSAGE => __d('admin', 'khong_ket_noi_duoc_den_api_{0}', 'GHTK')
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