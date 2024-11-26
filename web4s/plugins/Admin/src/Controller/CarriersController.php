<?php

namespace Admin\Controller;

use Admin\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Http\Response;
use Cake\ORM\Query;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;
use Cake\Http\Client;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;

class CarriersController extends AppController {

    protected $prefix_name = ['Tỉnh', 'Thành phố', 'Quận', 'Huyện', 'Phường', 'Xã', 'Thị trấn', 'Thị xã', 'Thị tứ'];

    public function initialize(): void
    {
        parent::initialize();        
    }

    public function list() 
    {
        $list_carrier = TableRegistry::get('ShippingsCarrier')->find()->toArray();
        $carriers = [];
        if(!empty($list_carrier)) {
            foreach ($list_carrier as $k => $carrier) {
                $code = !empty($carrier['code']) ? $carrier['code'] : null;
                $carrier['config'] = !empty($carrier['config']) ? json_decode($carrier['config'], true) : [];
                $carriers[$code] = $carrier;
            }
        }

        $this->css_page = '/assets/css/pages/wizard/wizard-2.css';
        $this->js_page = [
            '/assets/js/pages/list_carriers.js',
        ];

        $this->set('carriers', $carriers);
        $this->set('path_menu', 'setting');
        $this->set('title_for_layout', __d('admin', 'hang_van_chuyen'));   
    }

    public function save($code = null)
    {
        $this->autoRender = false;
        $data = $this->getRequest()->getData(); 

        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        if(!in_array($code, Configure::read('LIST_SHIPPING_CARRIER'))) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_hang_van_chuyen')]);
        }

        $utilities = $this->loadComponent('Utilities');
        $table = TableRegistry::get('ShippingsCarrier');

        $carrier_info = $table->find()->where(['ShippingsCarrier.code' => $code])->first();
        if (empty($carrier_info)) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_hang_van_chuyen')]);
        }
        $config = !empty($carrier_info['config']) ? json_decode($carrier_info['config'], true) : [];    
        $data_config = !empty($data['config']) ? $data['config'] : null;
        $config = array_merge($config, $data_config);

        $data_save = [
            'status' => !empty($data['status']) ? 1 : 0,
            'mode' => !empty($data['mode']) ? $data['mode'] : null,
            'config' => !empty($config) ? json_encode($config) : null
        ];    

        $entity = $table->patchEntity($carrier_info, $data_save);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();
            
            $save = $table->save($entity);
            if (empty($save->id)){
                throw new Exception();
            }

            $conn->commit();
            $this->responseJson([CODE => SUCCESS, DATA => ['code' => $save->code]]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function giaohangnhanhSyncCities()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('ShippingsCarrier');

        $carrier_info = $table->find()->where(['code' => GIAO_HANG_NHANH])->first();
        if(empty($carrier_info)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_hang_van_chuyen')]);
        }
        $config = !empty($carrier_info['config']) ? json_decode($carrier_info['config'], true) : [];

        $file_database = new File(ROOT . DS . FOLDER_DATABASE_INITIALIZATION . DS . 'shippings_city.sql', false);
        if(!$file_database->exists()){
            return $this->System->getResponse([
                MESSAGE => __d('admin', 'tep_cau_truc_database_khong_ton_tai')
            ]);
        }

        $query_content = !empty($file_database->read()) ? trim($file_database->read()) : null;
        $file_database->close();

        if(!isset($config['sync_data'])) $config['sync_data'] = [];        
        $config['sync_data']['citites'] = 1;

        $entity = $table->patchEntity($carrier_info, ['config' => json_encode($config)]);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $clear = TableRegistry::get('ShippingsCity')->deleteAll(['carrier' => GIAO_HANG_NHANH]);
            $conn->execute($query_content);

            $save = $table->save($entity);
            if (empty($save->id)){
                throw new Exception();
            }

            $conn->commit();
            $this->responseJson([CODE => SUCCESS]);

        }catch (Exception $e) {
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function giaohangnhanhSyncDistricts()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('ShippingsCarrier');

        $carrier_info = $table->find()->where(['code' => GIAO_HANG_NHANH])->first();
        if(empty($carrier_info)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_hang_van_chuyen')]);
        }
        $config = !empty($carrier_info['config']) ? json_decode($carrier_info['config'], true) : [];

        $file_database = new File(ROOT . DS . FOLDER_DATABASE_INITIALIZATION . DS . 'shippings_district.sql', false);
        if(!$file_database->exists()){
            return $this->System->getResponse([
                MESSAGE => __d('admin', 'tep_cau_truc_database_khong_ton_tai')
            ]);
        }

        $query_content = !empty($file_database->read()) ? trim($file_database->read()) : null;
        $file_database->close();

        if(!isset($config['sync_data'])) $config['sync_data'] = [];
        $config['sync_data']['districts'] = 1;
        $entity = $table->patchEntity($carrier_info, ['config' => json_encode($config)]);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $clear = TableRegistry::get('ShippingsDistrict')->deleteAll(['carrier' => GIAO_HANG_NHANH]);
            $conn->execute($query_content);

            $save = $table->save($entity);
            if (empty($save->id)){
                throw new Exception();
            }

            $conn->commit();
            $this->responseJson([CODE => SUCCESS]);

        }catch (Exception $e) {
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function giaohangnhanhSyncWards()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('ShippingsCarrier');

        $carrier_info = $table->find()->where(['code' => GIAO_HANG_NHANH])->first();
        if(empty($carrier_info)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_hang_van_chuyen')]);
        }
        $config = !empty($carrier_info['config']) ? json_decode($carrier_info['config'], true) : [];

        $file_database = new File(ROOT . DS . FOLDER_DATABASE_INITIALIZATION . DS . 'shippings_ward.sql', false);
        if(!$file_database->exists()){
            return $this->System->getResponse([
                MESSAGE => __d('admin', 'tep_cau_truc_database_khong_ton_tai')
            ]);
        }

        $query_content = !empty($file_database->read()) ? trim($file_database->read()) : null;
        $file_database->close();

        if(!isset($config['sync_data'])) $config['sync_data'] = [];
        $config['sync_data']['wards'] = 1;
        $entity = $table->patchEntity($carrier_info, ['config' => json_encode($config)]);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $clear = TableRegistry::get('ShippingsWard')->deleteAll(['carrier' => GIAO_HANG_NHANH]);
            $conn->execute($query_content);

            $save = $table->save($entity);
            if (empty($save->id)){
                throw new Exception();
            }

            $conn->commit();
            $this->responseJson([CODE => SUCCESS]);

        }catch (Exception $e) {
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function giaohangnhanhSyncStores()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }
        $table = TableRegistry::get('ShippingsCarrier');
        
        $carrier_info = $table->find()->where(['code' => GIAO_HANG_NHANH])->first();
        if(empty($carrier_info)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_hang_van_chuyen')]);
        }
        $config = !empty($carrier_info['config']) ? json_decode($carrier_info['config'], true) : [];

        $mode = !empty($config['mode']) ? $config['mode'] : null;
        $base_url = 'https://dev-online-gateway.ghn.vn';
        if($mode == LIVE){
            $base_url = 'https://online-gateway.ghn.vn';
        }

        $token = !empty($config['api_token']) ? $config['api_token'] : null;
        $ghn_stores = [];
        try{
            $url = $base_url . '/shiip/public-api/v2/shop/all';
            $http = new Client();
            
            $response = $http->get($url, [], [
                'headers' => [
                    'token' => $token
                ]
            ]);
            $json_response = $response->getJson();            
            if(!empty($json_response[CODE]) && $json_response[CODE] == 200 && !empty($json_response[DATA])){
                $ghn_stores = !empty($json_response[DATA]['shops']) ? $json_response[DATA]['shops'] : [];
            }
        }catch (NetworkException $e) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_ket_noi_duoc_den_api_ghn')]);
        }

        if(empty($ghn_stores)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin')]);   
        }

        $config['stores'] = [];
        
        $stores = !empty($config['stores']) ? $config['stores'] : [];
        foreach($ghn_stores as $ghn_store){
            $store_id = !empty($ghn_store['_id']) ? intval($ghn_store['_id']) : null;
            if(empty($store_id)) continue;

            $stores[$store_id] = [
                'id' => $store_id,
                'name' => !empty($ghn_store['name']) ? $ghn_store['name'] : null,
                'phone' => !empty($ghn_store['phone']) ? $ghn_store['phone'] : null,
                'address' => !empty($ghn_store['address']) ? $ghn_store['address'] : null,
                'district_id' => !empty($ghn_store['district_id']) ? intval($ghn_store['district_id']) : null,
                'ward_code' => !empty($ghn_store['ward_code']) ? $ghn_store['ward_code'] : null,
            ];        
        }

        $config['stores'] = $stores;
        $entity = $table->patchEntity($carrier_info, ['config' => json_encode($config)]);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();          

            $save = $table->save($entity);
            if (empty($save->id)){
                throw new Exception();
            }

            $conn->commit();
            $this->responseJson([CODE => SUCCESS]);

        }catch (Exception $e) {
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function giaohangnhanhInitializationCities()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('ShippingsCarrier');
        $carriers = $table->getList();
        $carrier_info = !empty($carriers[GIAO_HANG_NHANH]) ? $carriers[GIAO_HANG_NHANH] : [];

        if(empty($carrier_info)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_hang_van_chuyen')]);
        }
        $config = !empty($carrier_info['config']) ? $carrier_info['config'] : [];

        $mode = !empty($config['mode']) ? $config['mode'] : null;
        $base_url = 'https://dev-online-gateway.ghn.vn';
        if($mode == LIVE){
            $base_url = 'https://online-gateway.ghn.vn';
        }

        $token = !empty($config['api_token']) ? $config['api_token'] : null;
        $ghn_cities = [];
        try{
            $url = $base_url . '/shiip/public-api/master-data/province';
            $http = new Client();
            
            $response = $http->post($url, [], [
                'headers' => [
                    'token' => $token
                ]
            ]);
            $json_response = $response->getJson();

            if(!empty($json_response[CODE]) && $json_response[CODE] == 200 && !empty($json_response[DATA])){
                $ghn_cities = !empty($json_response[DATA]) ? $json_response[DATA] : [];
            }
        }catch (NetworkException $e) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_ket_noi_duoc_den_api_ghn')]);
        }

        if(empty($ghn_cities)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin')]);   
        }

        $citites = TableRegistry::get('Cities')->getListCity();
        if(empty($citites) || !is_array($citites)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin')]);
        }
        $citites = array_flip($citites);
        
        $data_save = [];
        foreach($ghn_cities as $item){
            $city_id = null;
            $province_name = !empty($item['ProvinceName']) ? $item['ProvinceName'] : null;            
            if(!empty($citites[$province_name])) $city_id = intval($citites[$province_name]);

            if(empty($city_id)){
                $extensions = !empty($item['NameExtension']) ? $item['NameExtension'] : [];
                foreach($extensions as $extension_name){
                    if(!empty($citites[$extension_name])) $city_id = intval($citites[$extension_name]);
                }
            }

            if(empty($city_id)) continue;
            $data_save[] = [
                'city_id' => $city_id,
                'carrier' => GIAO_HANG_NHANH,
                'carrier_city_id' => !empty($item['ProvinceID']) ? intval($item['ProvinceID']) : null,
                'carrier_city_code' => !empty($item['Code']) ? $item['Code'] : null,
            ];
        }

        if(empty($data_save)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin')]);   
        }


        $entities = TableRegistry::get('ShippingsCity')->newEntities($data_save);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $save = TableRegistry::get('ShippingsCity')->saveMany($entities);            
            if (empty($save)){
                throw new Exception();
            }
            
            $conn->commit();
            $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'cap_nhat_thanh_cong')]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function giaohangnhanhInitializationDistricts()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('ShippingsCarrier');
        $carriers = $table->getList();
        $carrier_info = !empty($carriers[GIAO_HANG_NHANH]) ? $carriers[GIAO_HANG_NHANH] : [];
        if(empty($carrier_info)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_hang_van_chuyen')]);
        }

        $ghn_cities = TableRegistry::get('ShippingsCity')->find()->where(['carrier' => GIAO_HANG_NHANH])->select(['city_id', 'carrier_city_id'])->toArray();

        if(empty($ghn_cities)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_tinh_thanh')]);
        }

        $config = !empty($carrier_info['config']) ? $carrier_info['config'] : [];
        $mode = !empty($config['mode']) ? $config['mode'] : null;
        $base_url = 'https://dev-online-gateway.ghn.vn';
        if($mode == LIVE){
            $base_url = 'https://online-gateway.ghn.vn';
        }

        $token = !empty($config['api_token']) ? $config['api_token'] : null;
        
        $conn = ConnectionManager::get('default');

        foreach($ghn_cities as $ghn_city){
            $city_id = !empty($ghn_city['city_id']) ? intval($ghn_city['city_id']) : null;
            $carrier_city_id = !empty($ghn_city['carrier_city_id']) ? intval($ghn_city['carrier_city_id']) : null;
            if(empty($carrier_city_id) || empty($city_id)) continue;

            $ghn_districts = [];            
            try{
                $url = $base_url . '/shiip/public-api/master-data/district';
                $http = new Client();
                
                $response = $http->post($url, json_encode(['province_id' => $carrier_city_id]), [
                    'headers' => [
                        'token' => $token
                    ],
                    'type' => 'json'
                ]);
                $json_response = $response->getJson();
                
                if(!empty($json_response[CODE]) && $json_response[CODE] == 200 && !empty($json_response[DATA])){
                    $ghn_districts = !empty($json_response[DATA]) ? $json_response[DATA] : [];
                }
            }catch (NetworkException $e) {
                $this->responseJson([MESSAGE => __d('admin', 'khong_ket_noi_duoc_den_api_ghn')]);
            }

            if(empty($ghn_districts)) continue;

            $districts = TableRegistry::get('Districts')->getListDistrict($city_id);
            if(empty($districts) || !is_array($districts)) continue;
            $districts = array_flip($districts);

            $data_save = [];
            foreach($ghn_districts as $item){
                $district_id = null;
                $district_name = !empty($item['DistrictName']) ? $item['DistrictName'] : null;            
                if(!empty($districts[$district_name])) $district_id = intval($districts[$district_name]);

                if(empty($district_id)){
                    $extensions = !empty($item['NameExtension']) ? $item['NameExtension'] : [];
                    foreach($extensions as $extension_name){
                        if(!empty($districts[$extension_name])) $district_id = intval($districts[$extension_name]);
                    }
                }

                if(empty($district_id)) continue;

                $data_save[] = [
                    'district_id' => $district_id,
                    'carrier' => GIAO_HANG_NHANH,
                    'carrier_district_id' => !empty($item['DistrictID']) ? intval($item['DistrictID']) : null,
                    'carrier_district_code' => !empty($item['Code']) ? $item['Code'] : null,
                ];
            }

            if(empty($data_save)){
                $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin')]);   
            }

            
            $entities = TableRegistry::get('ShippingsDistrict')->newEntities($data_save);
            
            try{
                $conn->begin();

                $save = TableRegistry::get('ShippingsDistrict')->saveMany($entities);            
                if (empty($save)){
                    throw new Exception();
                }
                
                $conn->commit();
            }catch (Exception $e) {
                $conn->rollback();
                $this->responseJson([MESSAGE => $e->getMessage()]);  
            }
        }
        

        $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'cap_nhat_thanh_cong')]);
    }

    public function giaohangnhanhInitializationWards()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        set_time_limit(0);

        $table = TableRegistry::get('ShippingsCarrier');
        $carriers = $table->getList();
        $carrier_info = !empty($carriers[GIAO_HANG_NHANH]) ? $carriers[GIAO_HANG_NHANH] : [];
        if(empty($carrier_info)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_hang_van_chuyen')]);
        }

        $ghn_districts = TableRegistry::get('ShippingsDistrict')->find()->where(['carrier' => GIAO_HANG_NHANH])->select(['district_id', 'carrier_district_id'])->toArray();
        if(empty($ghn_districts)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_quan_huyen')]);
        }

        $config = !empty($carrier_info['config']) ? $carrier_info['config'] : [];
        $mode = !empty($config['mode']) ? $config['mode'] : null;
        $base_url = 'https://dev-online-gateway.ghn.vn';
        if($mode == LIVE){
            $base_url = 'https://online-gateway.ghn.vn';
        }
        $token = !empty($config['api_token']) ? $config['api_token'] : null;
        
        $conn = ConnectionManager::get('default');
        foreach($ghn_districts as $ghn_district){
            $district_id = !empty($ghn_district['district_id']) ? intval($ghn_district['district_id']) : null;
            $carrier_district_id = !empty($ghn_district['carrier_district_id']) ? intval($ghn_district['carrier_district_id']) : null;
            if(empty($carrier_district_id) || empty($district_id)) continue;

            $ghn_wards = [];
            try{
                $url = $base_url . '/shiip/public-api/master-data/ward';
                $http = new Client();
                
                $response = $http->post($url, json_encode(['district_id' => $carrier_district_id]), [
                    'headers' => [
                        'token' => $token
                    ],
                    'type' => 'json'
                ]);
                $json_response = $response->getJson();
                
                if(!empty($json_response[CODE]) && $json_response[CODE] == 200 && !empty($json_response[DATA])){
                    $ghn_wards = !empty($json_response[DATA]) ? $json_response[DATA] : [];
                }
            }catch (NetworkException $e) {
                $this->responseJson([MESSAGE => __d('admin', 'khong_ket_noi_duoc_den_api_ghn')]);
            }

            if(empty($ghn_wards)) continue;

            $wards = TableRegistry::get('Wards')->getListWard($district_id);
            if(empty($wards) || !is_array($wards)) continue;
            $wards = array_flip($wards);            

            $data_save = [];
            foreach($ghn_wards as $item){
                $ward_id = null;
                $ward_name = !empty($item['WardName']) ? $item['WardName'] : null;            
                if(!empty($wards[$ward_name])) $ward_id = intval($wards[$ward_name]);

                if(empty($ward_id)){
                    $extensions = !empty($item['NameExtension']) ? $item['NameExtension'] : [];
                    foreach($extensions as $extension_name){
                        if(!empty($wards[$extension_name])) $ward_id = intval($wards[$extension_name]);
                    }
                }

                if(empty($ward_id)) continue;

                $data_save[] = [
                    'ward_id' => $ward_id,
                    'carrier' => GIAO_HANG_NHANH,
                    'carrier_ward_id' => null,
                    'carrier_ward_code' => !empty($item['WardCode']) ? $item['WardCode'] : null,
                ];
            }

            if(empty($data_save)) continue;

            
            $entities = TableRegistry::get('ShippingsWard')->newEntities($data_save);
            
            try{
                $conn->begin();

                $save = TableRegistry::get('ShippingsWard')->saveMany($entities);            
                if (empty($save)){
                    throw new Exception();
                }
                
                $conn->commit();
            }catch (Exception $e) {
                $conn->rollback();
                $this->responseJson([MESSAGE => $e->getMessage()]);  
            }
        }
        

        $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'cap_nhat_thanh_cong')]);
    }

    public function giaohangtietkiemSyncStores()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }
        $table = TableRegistry::get('ShippingsCarrier');
        
        $carrier_info = $table->find()->where(['code' => GIAO_HANG_TIET_KIEM])->first();
        if(empty($carrier_info)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_hang_van_chuyen')]);
        }
        $config = !empty($carrier_info['config']) ? json_decode($carrier_info['config'], true) : [];

        $mode = !empty($config['mode']) ? $config['mode'] : null;
        $base_url = 'https://services.ghtklab.com';
        if($mode == LIVE){
            $base_url = 'https://services.giaohangtietkiem.vn';
        }

        $token = !empty($config['api_token']) ? $config['api_token'] : null;
        $ghtk_stores = [];
        try{
            $url = $base_url . '/services/shipment/list_pick_add';
            $http = new Client();
            
            $response = $http->get($url, [], [
                'headers' => [
                    'token' => $token
                ]
            ]);
            $json_response = $response->getJson();            
            if(!empty($json_response[SUCCESS]) && !empty($json_response[DATA])){
                $ghtk_stores = !empty($json_response[DATA]) ? $json_response[DATA] : [];
            }
        }catch (NetworkException $e) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_ket_noi_duoc_den_api_ghn')]);
        }

        if(empty($ghtk_stores)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin')]);   
        }

        $config['stores'] = [];
        
        $stores = !empty($config['stores']) ? $config['stores'] : [];
        foreach($ghtk_stores as $ghtk_store){
            $store_id = !empty($ghtk_store['pick_address_id']) ? $ghtk_store['pick_address_id'] : null;
            if(empty($store_id)) continue;

            $stores[$store_id] = [
                'id' => $store_id,
                'name' => !empty($ghtk_store['pick_name']) ? $ghtk_store['pick_name'] : null,
                'phone' => !empty($ghtk_store['pick_tel']) ? $ghtk_store['pick_tel'] : null,
                'address' => !empty($ghtk_store['address']) ? $ghtk_store['address'] : null
            ];        
        }

        $config['stores'] = $stores;
        $entity = $table->patchEntity($carrier_info, ['config' => json_encode($config)]);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();          

            $save = $table->save($entity);
            if (empty($save->id)){
                throw new Exception();
            }

            $conn->commit();
            $this->responseJson([CODE => SUCCESS]);

        }catch (Exception $e) {
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }
}