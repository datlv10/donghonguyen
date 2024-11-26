<?php

namespace Admin\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;
use App\Lib\Shipping\NhShipping;


class ShippingComponent extends AppComponent
{
	public $controller = null;
    public $components = ['System', 'Utilities'];

    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->controller = $this->_registry->getController();
    }

    public function formatDataBeforeSave($data = [], $contact = [], $items = [])
    {
        if(empty($data) || empty($contact)) return [];

        $order_id = !empty($data['order_id']) ? intval($data['order_id']) : null;
        $shipping_method = !empty($data['shipping_method']) ? $data['shipping_method'] : null;

        $full_name = !empty($contact['full_name']) ? $contact['full_name'] : null;
        $phone = !empty($contact['phone']) ? $contact['phone'] : null;        
        $city_id = !empty($contact['city_id']) ? intval($contact['city_id']) : null;
        $district_id = !empty($contact['district_id']) ? intval($contact['district_id']) : null;
        $ward_id = !empty($contact['ward_id']) ? intval($contact['ward_id']) : null;
        $address = !empty($contact['address']) ? $contact['address'] : null;
        $full_address = !empty($contact['full_address']) ? $contact['full_address'] : null;
        
        $cod_money = !empty($data['cod_money']) ? floatval(str_replace(',', '', $data['cod_money'])) : 0;
        $weight = !empty($data['weight']) ? intval(str_replace(',', '', $data['weight'])) : null;
        $length = !empty($data['length']) ? intval(str_replace(',', '', $data['length'])) : null;
        $width = !empty($data['width']) ? intval(str_replace(',', '', $data['width'])) : null;
        $height = !empty($data['height']) ? intval(str_replace(',', '', $data['height'])) : null;
        
        $note = !empty($data['shipping_note']) ? $data['shipping_note'] : null;
        $required_note = !empty($data['required_note']) ? $data['required_note'] : null;

        $shipping_fee_customer = !empty($data['shipping_fee_customer']) ? floatval(str_replace(',', '', $data['shipping_fee_customer'])) : null;
        $shipping_fee = !empty($data['shipping_fee']) ? floatval(str_replace(',', '', $data['shipping_fee'])) : null;
        $carrier_code = !empty($data['carrier_code']) ? $data['carrier_code'] : null;

        $carrier_service_code = !empty($data['carrier_service_code']) ? $data['carrier_service_code'] : null;
        $carrier_service_type_code = !empty($data['carrier_service_type_code']) ? $data['carrier_service_type_code'] : null;        

        $carrier_shop_id = null;
        if(!empty($carrier_code) && in_array($carrier_code, Configure::read('LIST_SHIPPING_CARRIER'))) {            
            switch($carrier_code){
                case GIAO_HANG_NHANH:
                    $carrier_shop_id = !empty($data['ghn_shop']) ? intval($data['ghn_shop']) : null;
                break;

                case GIAO_HANG_TIET_KIEM:
                    $carrier_shop_id = !empty($data['ghtk_shop']) ? intval($data['ghtk_shop']) : null;
                break;
            }       
        }

        $data_shipping = [
            'order_id' => $order_id,
            'shipping_method' => $shipping_method,
            'cod_money' => $cod_money,
            'carrier_code' =>$carrier_code,
            'carrier_service_code' => $carrier_service_code,
            'carrier_service_type_code' => $carrier_service_type_code,
            'carrier_shop_id' => $carrier_shop_id,
            'carrier_order_code' => null,
            'carrier_shipping_fee' => null,
            'required_note' => $required_note,
            'shipping_fee' => $shipping_fee,
            'shipping_fee_discount' => null,
            'shipping_fee_customer' => $shipping_fee_customer,
            'cod_fee' => null,
            'cod_fee_discount' => null,
            'insurance_fee' => null,
            'extra_fee' => null,
            'estimated_pick_time' => null,
            'estimated_deliver_time' => null,
            'note' => $note,
            'full_name' => $full_name,
            'phone' => $phone,
            'address' => $address,
            'full_address' => $full_address,
            'country_id' => 1,
            'city_id' => $city_id,
            'district_id' => $district_id,
            'ward_id' => $ward_id,
            'weight' => $weight,
            'length' => $length,
            'width' => $width,
            'height' => $height,
            'status' => WAIT_DELIVER,
            'created_by' => !empty($data['created_by']) ? intval($data['created_by']) : null
        ];

        return $this->System->getResponse([CODE => SUCCESS, DATA => $data_shipping]);
    }

    public function saveShipping($data = [], $id = null)
    {
        $result = [];        
        $shipping_method = !empty($data['shipping_method']) ? $data['shipping_method'] : null;

        // validate data
        if(empty($shipping_method) || (!empty($shipping_method) && !in_array($shipping_method, [RECEIVED_AT_STORE, NORMAL_SHIPPING, SHIPPING_CARRIER]))){
            return $this->System->getResponse([MESSAGE => __d('admin', 'phuong_thuc_van_chuyen_khong_hop_le')]);
        }

        if(empty($data['full_name'])){
            return $this->System->getResponse([MESSAGE => __d('admin', 'vui_long_nhap_ten_khach_hang')]);
        }

        if(empty($data['phone'])){
            return $this->System->getResponse([MESSAGE => __d('admin', 'vui_long_nhap_so_dien_thoai_khach_hang')]);
        }

        if(empty($data['country_id']) && $shipping_method != RECEIVED_AT_STORE){
            return $this->System->getResponse([MESSAGE => __d('admin', 'thong_tin_quoc_gia_chua_chinh_xac')]);
        }

        if(empty($data['city_id']) && $shipping_method != RECEIVED_AT_STORE){
            return $this->System->getResponse([MESSAGE => __d('admin', 'vui_long_chon_thong_tin_tinh_thanh')]);
        }

        if(empty($data['district_id']) && $shipping_method != RECEIVED_AT_STORE){
            return $this->System->getResponse([MESSAGE => __d('admin', 'vui_long_chon_thong_tin_quan_huyen')]);
        }

        $shippings_table = TableRegistry::get('Shippings');

        $full_address = [];
        if(!empty($data['country_id'])){
            $countries_table = TableRegistry::get('Countries'); 
            $country_info = $countries_table->find()->where(['Countries.id' => $data['country_id']])->first();
            $data['country_name'] = !empty($country_info['name']) ? $country_info['name'] : null;            
        }

        if(!empty($data['city_id'])){
            $city_info = TableRegistry::get('Cities')->find()->where(['Cities.id' => $data['city_id']])->first();
            $data['city_name'] = !empty($city_info['name']) ? $city_info['name'] : null;
            if(!empty($data['city_name'])){
                $full_address[] = $data['city_name'];
            }
        }

        if(!empty($data['district_id'])){
            $district_info = TableRegistry::get('Districts')->find()->where(['Districts.id' => $data['district_id']])->first();
            $data['district_name'] = !empty($district_info['name']) ? $district_info['name'] : null;
            if(!empty($data['district_name'])){
                $full_address[] = $data['district_name'];
            }
        }

        if(!empty($data['ward_id'])){
            $ward_info = TableRegistry::get('Wards')->find()->where(['Wards.id' => $data['ward_id']])->first();
            $data['ward_name'] = !empty($ward_info['name']) ? $ward_info['name'] : null;
            if(!empty($data['ward_name'])){
                $full_address[] = $data['ward_name'];
            }
        }        

        $address = !empty($data['address']) ? $data['address'] : null;
        
        if(!empty($address)){
            $full_address[] = $address;
        }

        $full_address = !empty($full_address) ? implode(', ', array_reverse($full_address)) : [];
        $full_name = !empty($data['full_name']) ? $data['full_name'] : null;
        $phone = !empty($data['phone']) ? $data['phone'] : null;

        $data_save = [
            'id' => $id,
            'order_id' => !empty($data['order_id']) ? intval($data['order_id']) : null,
            'shipping_method' => !empty($data['shipping_method']) ? $data['shipping_method'] : null,
            'cod_money' => !empty($data['cod_money']) ? $this->Utilities->formatToDecimal(str_replace(',', '', $data['cod_money'])) : null,

            'carrier_code' => !empty($data['carrier_code']) ? $data['carrier_code'] : null,
            'carrier_service_code' => !empty($data['carrier_service_code']) ? $data['carrier_service_code'] : null,
            'carrier_service_type_code' => !empty($data['carrier_service_type_code']) ? $data['carrier_service_type_code'] : null,
            'carrier_shop_id' => !empty($data['carrier_shop_id']) ? $data['carrier_shop_id'] : null,
            'carrier_order_code' => !empty($data['carrier_order_code']) ? $data['carrier_order_code'] : null,
            'carrier_shipping_fee' => !empty($data['carrier_shipping_fee']) ? floatval($data['carrier_shipping_fee']) : null,
            'required_note' => !empty($data['required_note']) ? $data['required_note'] : null,

            'shipping_fee' => !empty($data['shipping_fee']) ? $this->Utilities->formatToDecimal(str_replace(',', '', $data['shipping_fee'])) : null,            
            'shipping_fee_customer' => !empty($data['shipping_fee_customer']) ? $this->Utilities->formatToDecimal(str_replace(',', '', $data['shipping_fee_customer'])) : null,

            'shipping_fee_discount' => null,
            'cod_fee' => null,
            'cod_fee_discount' => null,
            'insurance_fee' => null,
            'estimated_pick_time' => null,
            'estimated_deliver_time' => null,

            'full_name' => $full_name,
            'phone' => $phone,

            'country_id' => !empty($data['country_id']) ? intval($data['country_id']) : null,
            'city_id' => !empty($data['city_id']) ? intval($data['city_id']) : null,
            'district_id' => !empty($data['district_id']) ? intval($data['district_id']) : null,
            'ward_id' => !empty($data['ward_id']) ? intval($data['ward_id']) : null,

            'country_name' => !empty($data['country_name']) ? $data['country_name'] : null,
            'city_name' => !empty($data['city_name']) ? $data['city_name'] : null,
            'district_name' => !empty($data['district_name']) ? $data['district_name'] : null,
            'ward_name' => !empty($data['ward_name']) ? $data['ward_name'] : null,
            'address' => $address,
            'full_address' => $full_address,

            'weight' => !empty($data['weight']) ? intval(str_replace(',', '', $data['weight'])) : null,
            'length' => !empty($data['length']) ? intval(str_replace(',', '', $data['length'])) : null,
            'width' => !empty($data['width']) ? intval(str_replace(',', '', $data['width'])) : null,
            'height' => !empty($data['height']) ? intval(str_replace(',', '', $data['height'])) : null,

            'note' => !empty($data['note']) ? $data['note'] : null,
            'status' => !empty($data['status']) ? $data['status'] : null,
            'created_by' => !empty($data['created_by']) ? intval($data['created_by']) : null,
        ];

        // merge data with entity
        if(empty($id)){
            $shipping = $shippings_table->newEntity($data_save);
        }else{
            $shipping = $shippings_table->find()->where(['id' => $id])->first();
            if(empty($shipping)){
                return $this->System->getResponse([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_van_don')]);
            }
            $data_save['search_unicode'] = $this->Utilities->formatSearchUnicode([$shipping['code'], $full_name, $phone]);
            $shipping = $shippings_table->patchEntity($shipping, $data_save);
        }

        // show error validation in model
        if($shipping->hasErrors()){
            $list_errors = $this->Utilities->errorModel($shipping->getErrors());
            
            return $this->System->getResponse([
                MESSAGE => !empty($list_errors[0]) ? $list_errors[0] : null,
                DATA => $list_errors
            ]);             
        }

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            // save data
            $save = $shippings_table->save($shipping);
            if (empty($save->id)){
                throw new Exception();
            }

            if(empty($save['code'])){
                $shipping = $shippings_table->get($save->id);

                $code = 'SHIP' . str_pad($save->id, 7, '0', STR_PAD_LEFT);
                $search_unicode = strtolower($this->Utilities->formatSearchUnicode([$code, $save['full_name'], $save['phone']]));
                $shipping = $shippings_table->patchEntity($shipping, ['code' => $code, 'search_unicode' => $search_unicode], ['validate' => false]);
                $update_code = $shippings_table->save($shipping);
                if (empty($update_code->id)){
                    throw new Exception();
                }
            }
            
            $conn->commit();
            return $this->System->getResponse([CODE => SUCCESS, DATA => $save]);

        }catch (Exception $e) {
            $conn->rollback();
            return $this->System->getResponse([MESSAGE => $e->getMessage()]);
        }
    }

    public function sendOrderToCarrier($shipping_id = null)
    {
        if(empty($shipping_id)) {
            return $this->System->getResponse([MESSAGE => __d('admin', 'phuong_thuc_van_chuyen_khong_hop_le')]);
        }

        $shipping_info = TableRegistry::get('Shippings')->find()->where([
            'Shippings.id' => $shipping_id,
        ])->first();

        if(empty($shipping_info)) {
            return $this->System->getResponse([MESSAGE => __d('admin', 'phuong_thuc_van_chuyen_khong_hop_le')]);
        }

        $carrier_code = !empty($shipping_info['carrier_code']) ? $shipping_info['carrier_code'] : null;
        $order_id = !empty($shipping_info['order_id']) ? intval($shipping_info['order_id']) : null;
        if(empty($carrier_code) || empty($order_id)) {
            return $this->System->getResponse([MESSAGE => __d('admin', 'phuong_thuc_van_chuyen_khong_hop_le')]);
        }

        $lang = 'vi';
        $order_info = TableRegistry::get('Orders')->getDetailOrder($order_id, ['get_items' => true]);        
        $order_info = TableRegistry::get('Orders')->formatDataOrderDetail($order_info, $lang);

        // gửi đơn sang hãng
        $shop_id = !empty($shipping_info['carrier_shop_id']) ? intval($shipping_info['carrier_shop_id']) : null;
        $carrier_service_code = !empty($shipping_info['carrier_service_code']) ? $shipping_info['carrier_service_code'] : null;
        $carrier_service_type_code = !empty($shipping_info['carrier_service_type_code']) ? $shipping_info['carrier_service_type_code'] : null;

        $full_name = !empty($shipping_info['full_name']) ? $shipping_info['full_name'] : null;
        $phone = !empty($shipping_info['phone']) ? $shipping_info['phone'] : null;
        $address = !empty($shipping_info['address']) ? $shipping_info['address'] : null;
        $city_id = !empty($shipping_info['city_id']) ? intval($shipping_info['city_id']) : null;
        $district_id = !empty($shipping_info['district_id']) ? intval($shipping_info['district_id']) : null;
        $ward_id = !empty($shipping_info['ward_id']) ? intval($shipping_info['ward_id']) : null;

        $weight = !empty($shipping_info['weight']) ? intval($shipping_info['weight']) : null;
        $length = !empty($shipping_info['length']) ? intval($shipping_info['length']) : null;
        $width = !empty($shipping_info['width']) ? intval($shipping_info['width']) : null;
        $height = !empty($shipping_info['height']) ? intval($shipping_info['height']) : null;

        $note = !empty($shipping_info['note']) ? $shipping_info['note'] : null;
        $required_note = !empty($shipping_info['required_note']) ? $shipping_info['required_note'] : null;
        $cod_money = !empty($shipping_info['cod_money']) ? floatval($shipping_info['cod_money']) : null;

        $params = [
            'shop_id' => $shop_id,
            'service_id' => $carrier_service_code,
            'service_type_id' => $carrier_service_type_code,
            'full_name' => $full_name,
            'phone' => $phone,
            'address' => $address,
            'city_id' => $city_id,
            'district_id' => $district_id,
            'ward_id' => $ward_id,
            'weight' => $weight,
            'length' => $length,
            'width' => $width,
            'height' => $height,
            'note' => $note,
            'required_note' => $required_note,
            'cod_money' => $cod_money,

            'order_code' => !empty($order_info['code']) ? $order_info['code'] : null,
            'shipping_code' => !empty($shipping_info['code']) ? $shipping_info['code'] : null,
            'order_total' => !empty($order_info['total']) ? floatval($order_info['total']) : null,
            'items' => !empty($order_info['items']) ? $order_info['items'] : []
        ];
        
        $nh_shipping = new NhShipping($carrier_code);            
        $carrier_result = $nh_shipping->createOrder($params);

        $carrier_order_code = !empty($carrier_result[DATA]['carrier_order_code']) ? $carrier_result[DATA]['carrier_order_code'] : null;
        $carrier_shipping_fee = !empty($carrier_result[DATA]['carrier_shipping_fee']) ? floatval($carrier_result[DATA]['carrier_shipping_fee']) : null;
        if(empty($carrier_result[CODE]) || $carrier_result[CODE] != SUCCESS || empty($carrier_order_code) || empty($carrier_shipping_fee)){
            $message = !empty($carrier_result[MESSAGE]) ? $carrier_result[MESSAGE] : __d('admin', 'gui_don_sang_hang_van_chuyen_khong_thanh_cong');
            return $this->System->getResponse([MESSAGE => $message]);
        }

        return $this->System->getResponse([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'gui_don_sang_hang_van_chuyen_thanh_cong'),
            DATA => [
                'carrier_order_code' => $carrier_order_code,
                'carrier_shipping_fee' => $carrier_shipping_fee,
            ]
        ]);
    }

    public function cancelOrderOnCarrier($shipping_id = null)
    {
        if(empty($shipping_id)) {
            return $this->System->getResponse([MESSAGE => __d('admin', 'phuong_thuc_van_chuyen_khong_hop_le')]);
        }

        $shipping_info = TableRegistry::get('Shippings')->find()->where([
            'Shippings.id' => $shipping_id,
        ])->first();

        $carrier_order_code = !empty($shipping_info['carrier_order_code']) ? $shipping_info['carrier_order_code'] : null;
        $carrier_code = !empty($shipping_info['carrier_code']) ? $shipping_info['carrier_code'] : null;
        if(empty($carrier_order_code) || empty($carrier_code)) {
            return $this->System->getResponse([MESSAGE => __d('admin', 'phuong_thuc_van_chuyen_khong_hop_le')]);
        }        

        $nh_shipping = new NhShipping($carrier_code);
        $carrier_result = $nh_shipping->cancelOrder([
            'order_code' => $carrier_order_code
        ]);

        if(empty($carrier_result[CODE]) || $carrier_result[CODE] != SUCCESS){
            $message = !empty($carrier_result[MESSAGE]) ? $carrier_result[MESSAGE] : __d('admin', 'huy_don_ben_hang_van_chuyen_khong_thanh_cong');
            return $this->System->getResponse([MESSAGE => $message]);
        }

        return $this->System->getResponse([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'huy_don_ben_hang_van_chuyen_thanh_cong')
        ]);
    }
}
