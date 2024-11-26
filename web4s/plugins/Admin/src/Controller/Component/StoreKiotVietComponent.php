<?php

namespace Admin\Controller\Component;

use Cake\Controller\Component;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Cache\Cache;
use Cake\Http\Client;
use Cake\Utility\Text;
use Cake\Http\Client\Request as ClientRequest;

class StoreKiotVietComponent extends AppComponent
{   
    public $controller = null;
    public $components = ['System', 'Utilities'];
    public $client_name = null;
    public $client_id = null;
    public $client_secret = null;
    public $domain = 'https://id.kiotviet.vn';
    public $domain_api = 'https://public.kiotapi.com';


    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->controller = $this->_registry->getController();
        
        $settings = TableRegistry::get('Settings')->getSettingWebsite();
        $info_kiotviet = !empty($settings['store_kiotviet']) ? $settings['store_kiotviet'] : [];
        $config = !empty($info_kiotviet['config']) ? json_decode($info_kiotviet['config'], true) : [];

        $this->client_name = !empty($config['name']) ? $config['name'] : [];
        $this->client_id = !empty($config['client_id']) ? $config['client_id'] : [];
        $this->client_secret = !empty($config['code']) ? $config['code'] : [];
    }  

    public function getToken()
    {
        $cache_key = 'kiot_token';
        $token_info = Cache::read($cache_key);

        $token = !empty($token_info['token']) ? $token_info['token'] : null;
        $expires = !empty($token_info['expires']) ? $token_info['expires'] : null;
        $curren_time = time();
        if(is_null($token_info) || (!empty($expires) && $curren_time > $expires)){
            $http = new Client();
            $result = $http->post($this->domain . '/connect/token', 
                [
                    'client_id' => $this->client_id,
                    'client_secret' => $this->client_secret,
                    'grant_type' => 'client_credentials',
                    'scopes' => 'PublicApi.Access'
                ], 
                [
                    'headers' => [
                        'Content-Type' => 'application/x-www-form-urlencoded'
                    ]
                ]
            );

            $result = $result->getJson();
            $token = !empty($result['access_token']) ? $result['access_token'] : null;
            $expires_in = !empty($result['expires_in']) ? intval($result['expires_in']) : 86400;
            if(empty($token)) return null;

            $expires = $curren_time + $expires_in;
            Cache::write($cache_key, ['expires' => $expires, 'token' => $token]);
        }

        return $token;
    }
    
    public function getBranches()
    {
        $token = $this->getToken();
        $http = new Client();
        $result = $http->get($this->domain_api . '/branches', [], 
            [
                'headers' => [
                    'Retailer' => $this->client_name,
                    'Authorization' => "Bearer $token"
                ]
            ]
        );

        $result = $result->getJson();
        $data = !empty($result['data']) ? $result['data'] : null;
        $error = !empty($result['responseStatus']['errorCode']) ? true : false;
        if(!empty($error) || empty($data)){
            $message = !empty($result['responseStatus']['message']) ? $result['responseStatus']['message'] : __d('admin', 'khong_lay_duoc_thong_tin_cua_hang');
            return $this->System->getResponse([
                MESSAGE => $message
            ]);
        }

        return $this->System->getResponse([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'dong_bo_danh_sach_cua_hang_thanh_cong'),
            DATA => $data
        ]);
    }  

    public function listWebhooks()
    {
        $token = $this->getToken();
        $http = new Client();
        $result = $http->get($this->domain_api .'/webhooks', [], 
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Retailer' => $this->client_name,
                    'Authorization' => "Bearer $token"
                ]
            ]
        );

        $result = $result->getJson();
        $data = !empty($result['data']) ? $result['data'] : null;
        $error = !empty($result['responseStatus']['errorCode']) ? true : false;
        if(!empty($error)){
            $message = !empty($result['responseStatus']['message']) ? $result['responseStatus']['message'] : __d('admin', 'khong_lay_duoc_danh_sach_webhook_{0}',['KiotViet']);
            return $this->System->getResponse([
                MESSAGE => $message
            ]);
        }
        return $this->System->getResponse([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'lay_thong_tin_webhook_thanh_cong'),
            DATA => $data
        ]);
    }

    public function registerWebhook($type = null, $url = null)
    {
        if(empty($type) || empty($url)) {
            return $this->System->getResponse([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $token = $this->getToken();
        $http = new Client();

        $data = [
            'Webhook' => [
                'Type' => $type,
                'Url' => $url,
                'IsActive' => true
            ]
        ];

        $result = $http->post($this->domain_api .'/webhooks', json_encode($data), 
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Retailer' => $this->client_name,
                    'Authorization' => "Bearer $token"
                ]
            ]
        );                    
        $result = $result->getJson();

        $id = !empty($result['id']) ? $result['id'] : null;
        $error = !empty($result['responseStatus']['errorCode']) ? true : false;
        if(!empty($error) || empty($id)){
            $message = !empty($result['responseStatus']['message']) ? $result['responseStatus']['message'] : __d('admin', 'dang_ky_webhook_khong_thanh_cong');
            return $this->System->getResponse([
                MESSAGE => $message,
                DATA => [
                    'id' => $id
                ]
            ]);
        }

        return $this->System->getResponse([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'dang_ky_webhook_thanh_cong'),
            DATA  => [
                'id' => $id
            ]
        ]);
    }

    public function deleteWebhooks($id = null)
    {
        if(empty($id)) return [];

        $token = $this->getToken();
        $url = $this->domain_api ."/webhooks/$id";

        // Set up cURL
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Retailer: $this->client_name",
            "Authorization: Bearer $token",
            "Content-type: application/json"
        ]);

        $response = curl_exec($curl);
        if ($response === false) {
          die(curl_error($curl));
        }

        $result_api = json_decode($response, true);
        
        if(empty($result_api)) return $this->System->getResponse([MESSAGE => __d('admin', 'xoa_webhook_khong_thanh_cong')]);
        return $this->System->getResponse([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xoa_webhook_thanh_cong')
        ]);
        
    }

    public function webhook($data = [])
    {
        if(empty($data['Notifications']) || !is_array($data['Notifications'])) return;

        foreach($data['Notifications'] as $item){

            $action = !empty($item['Action']) ? $item['Action'] : null;
            $data_hook = !empty($item['Data']) ? $item['Data'] : [];
            if(empty($action)) continue;
            
            if(strpos($action, 'stock.update') !== false){
                foreach($data_hook as $data){
                    $this->_stockUpdate($data);
                }
            }

            if(strpos($action, 'product.update') !== false){
                foreach($data_hook as $data){
                    $kiotviet_id = !empty($data['Id']) ? $data['Id'] : null;
                    $kiotviet_code = !empty($data['Code']) ? $data['Code'] : null;
                    $kiotviet_price = !empty($data['BasePrice']) ? $data['BasePrice'] : null;
                    $kiotviet_attributes = !empty($data['Attributes']) ? $data['Attributes'] : [];

                    $this->updateProductAfterKiotVietChange($kiotviet_id, $kiotviet_code, $kiotviet_price, $kiotviet_attributes);
                }
            }
        }        
    }

    private function _stockUpdate($data = [])
    {
        $kiotviet_id = !empty($data['ProductId']) ? $data['ProductId'] : null;
        $kiotviet_code = !empty($data['ProductCode']) ? $data['ProductCode'] : null;
        $store_id = !empty($data['BranchId']) ? $data['BranchId'] : null;
        $quantity = !empty($data['OnHand']) ? intval($data['OnHand']) : 0;

        if(empty($kiotviet_id) || empty($kiotviet_code)) return false;

        // kiểm tra store_id đã đồng bộ chưa (có tồn tại trên store_partner)
        $product_item = TableRegistry::get('ProductsItem');
        $product_item_info = $product_item->find()->where(['kiotviet_code' => $kiotviet_code])->select(['id','product_id'])->first();
        if(empty($product_item_info)) return false;

        $partner_quantity = TableRegistry::get('ProductsPartnerQuantity');
        $quantity_info = $partner_quantity->find()->where([
            'store_id' => $store_id,
            'partner_product_id' => $kiotviet_id,

        ])->select(['id', 'product_item_id', 'quantity', 'store_id'])->first();

        if (!empty($quantity_info)) {
            $entity = $partner_quantity->patchEntity($quantity_info, [
                'quantity' => $quantity
            ]);
        } else {
             $data_save = [
                'partner' => KIOTVIET,
                'product_id' => !empty($product_item_info['product_id']) ? $product_item_info['product_id'] : null,
                'product_item_id' => !empty($product_item_info['id']) ? $product_item_info['id'] : null,
                'partner_product_id' => $kiotviet_id,
                'store_id' => $store_id,
                'quantity' => $quantity,
            ];
            $entity = $partner_quantity->newEntity($data_save);
        }
        
        $save = $partner_quantity->save($entity);
        if (empty($save->id)) return false;

        return true; 
    }

    // chỉ cập nhật khi bên kiotviet thay đổi mã, giá, mã thuộc tính
    public function updateProductAfterKiotVietChange($kiotviet_id = null, $kiotviet_code = null, $kiotviet_price = null, $kiotviet_attributes = [])
    {
        if(empty($kiotviet_id) || empty($kiotviet_code)) return false;

        $table = TableRegistry::get('ProductsItem');
        
        $item_info = $table->find()->where(['kiotviet_id' => $kiotviet_id])->select([
            'id', 'kiotviet_code', 'price', 'product_id'
        ])->first();

        $product_id = !empty($item_info['product_id']) ? intval($item_info['product_id']) : null;
        $product_item_id = !empty($item_info['id']) ? intval($item_info['id']) : null;

        if(empty($product_id) || empty($product_item_id)) return true;
        
        // kiểm tra giá, mã hoặc thuộc tính ko đồng bộ vs hệ thống thì thực hiện đồng bộ
        $sync = false;

        if(empty($item_info['kiotviet_code']) || $item_info['kiotviet_code'] != $kiotviet_code) $sync = true;
        if(isset($item_info['price']) && floatval($item_info['price']) != floatval($kiotviet_price)) $sync = true;

        // kiểm tra thuộc tính đã đồng bộ chưa
        $attributes = TableRegistry::get('Attributes')->getAll(LANGUAGE_DEFAULT);
        $attributes = Hash::combine($attributes, '{n}.code', '{n}');

        $all_options = TableRegistry::get('AttributesOptions')->getAll(LANGUAGE_DEFAULT);
        $all_options = Hash::combine($all_options, '{n}.code', '{n}', '{n}.attribute_id');

        if(!empty($kiotviet_attributes)){
            foreach($kiotviet_attributes as $kiotviet_attribute){
                
                $kiotviet_attribute_name = !empty($kiotviet_attribute['AttributeName']) ? $kiotviet_attribute['AttributeName'] : null;
                if(empty($kiotviet_attribute_name)) {
                    $kiotviet_attribute_name = !empty($kiotviet_attribute['attributeName']) ? $kiotviet_attribute['attributeName'] : null;
                }
                
                $kiotviet_attribute_value = !empty($kiotviet_attribute['AttributeValue']) ? $kiotviet_attribute['AttributeValue'] : null;
                if(empty($kiotviet_attribute_value)) {
                    $kiotviet_attribute_value = !empty($kiotviet_attribute['attributeValue']) ? $kiotviet_attribute['attributeValue'] : null;
                }

                if(empty($kiotviet_attribute_name) || empty($kiotviet_attribute_value)) continue;

                $kiotviet_attribute_code = strtolower(Text::slug(strtolower($kiotviet_attribute_name), ''));
                $kiotviet_option_code = strtolower(Text::slug(strtolower($kiotviet_attribute_value), ''));

                $attribute_id = !empty($attributes[$kiotviet_attribute_code]['id']) ? intval($attributes[$kiotviet_attribute_code]['id']) : null;
                
                // thuộc tính trên kiotviet chưa đồng bộ về hệ thống

                if(empty($attribute_id)) {
                    $sync = true;
                    break;
                }

                $attribute_options = !empty($all_options[$attribute_id]) ? $all_options[$attribute_id] : [];
                if(empty($attribute_options[$kiotviet_option_code])){
                    $sync = true;
                    break;
                }
                if(!empty($attribute_options[$kiotviet_option_code]['id'] || !empty($attribute_id) || !empty($product_id) || !empty($product_item_id))){
                    $item_attribute = TableRegistry::get('ProductsItemAttribute')->find()
                    ->where([
                        'attribute_id' => $attribute_id,
                        'value' => $attribute_options[$kiotviet_option_code]['id'],
                        'product_id' => $product_id,
                        'product_item_id' => $product_item_id
                    ])->first();

                    if(empty($item_attribute)){
                        $sync = true;
                        break;
                    }
                }
            }
        }

        if(!$sync) return true;

        // đồng bộ thuộc tính
        $this->syncAttributeAndOptionSingleProductKiotViet($kiotviet_attributes, $product_id, $product_item_id);

        // cập nhật giá và mã
        $data_item = [
            'kiotviet_code' => $kiotviet_code,
            'price' => $this->Utilities->formatToDecimal($kiotviet_price)
        ];

        $entity = $table->patchEntity($item_info, $data_item);
        $save = $table->save($entity);

        if (empty($save->id)) return false;

        return true;          
    }

    // đồng bộ thuộc tính (nếu có thêm tham số product_id thì sẽ cập nhật thông tin thuộc tính sản phẩm)
    public function syncAttributeAndOptionSingleProductKiotViet($kiotviet_attributes = [], $product_id = null, $product_item_id = null)
    {
        if(empty($kiotviet_attributes)) return true;

        $table_attributes = TableRegistry::get('Attributes');
        $table_options = TableRegistry::get('AttributesOptions');

        // lấy danh sách thuôc tính hệ thống
        $attributes = $table_attributes->getAll(LANGUAGE_DEFAULT);
        $attributes = Hash::combine($attributes, '{n}.code', '{n}');
        
        $all_options = $table_options->getAll(LANGUAGE_DEFAULT);
        $all_options = Hash::combine($all_options, '{n}.code', '{n}', '{n}.attribute_id');
        $data_update = [];
        foreach($kiotviet_attributes as $kiotviet_attribute){
            // do KiotViet trả về ở API get-all-attribute và webhook field name và value khác nhau nên phải kiểm tra 2 trường hợp này
            $name = !empty($kiotviet_attribute['AttributeName']) ? $kiotviet_attribute['AttributeName'] : null;
            if(empty($name)) {
                $name = !empty($kiotviet_attribute['attributeName']) ? $kiotviet_attribute['attributeName'] : null;
            }
            
            $value = !empty($kiotviet_attribute['AttributeValue']) ? $kiotviet_attribute['AttributeValue'] : null;
            if(empty($value)) {
                $value = !empty($kiotviet_attribute['attributeValue']) ? $kiotviet_attribute['attributeValue'] : null;
            }
            if(empty($name) || empty($value)) continue;

            $attribute_code = strtolower(Text::slug(strtolower($name), ''));
            $option_code = strtolower(Text::slug(strtolower($value), ''));

            $attribute_id = !empty($attributes[$attribute_code]['id']) ? intval($attributes[$attribute_code]['id']) : null;

            $attribute_options = !empty($all_options[$attribute_id]) ? $all_options[$attribute_id] : [];
            $option_id = !empty($attribute_options[$option_code]['id']) ? $attribute_options[$option_code]['id'] : null;

            // nếu chưa có attribute thì thêm mới
            if(empty($attribute_id)){
                $data_attribute = [
                    'code' => $attribute_code,
                    'attribute_type' => PRODUCT_ITEM,
                    'input_type' => SPECICAL_SELECT_ITEM,
                    'has_image' => 0,
                    'required' => 0,
                    'ContentMutiple' => [
                        [
                            'name' => $name,
                            'lang' => LANGUAGE_DEFAULT,
                            'search_unicode' => strtolower($this->Utilities->formatSearchUnicode([$name]))
                        ]
                    ],
                    'AttributesOptions' => [
                        [
                            'code' => $option_code,
                            'ContentMutipleOption' => [
                                [
                                    'name' => $value,
                                    'lang' => LANGUAGE_DEFAULT,
                                    'search_unicode' => strtolower($this->Utilities->formatSearchUnicode([$value]))
                                ]
                            ]
                        ]
                    ],
                ];

                $entity_attribute = $table_attributes->newEntity($data_attribute, [
                    'associated' => ['ContentMutiple', 'AttributesOptions', 'AttributesOptions.ContentMutipleOption']
                ]);
                
                
                $save_attribute = $table_attributes->save($entity_attribute);
                $attribute_id = !empty($save_attribute->id) ? $save_attribute->id : null;
                $option_id = !empty($save_attribute['AttributesOptions'][0]['id']) ? intval($save_attribute['AttributesOptions'][0]['id']) : null;

                if(empty($attribute_id) || empty($option_id)) continue;
            }

            // nếu chưa có option thì thêm mới
            if(empty($option_id)){
                $data_option = [
                    'attribute_id' => $attribute_id,
                    'code' => $option_code,
                    'ContentMutipleOption' => [
                        [
                            'name' => $value,
                            'lang' => LANGUAGE_DEFAULT,
                            'search_unicode' => strtolower($this->Utilities->formatSearchUnicode([$value]))
                        ]
                    ]
                ];
                
                $entity_option = $table_options->newEntity($data_option, [
                    'associated' => ['ContentMutipleOption']
                ]);

                $new_option = $table_options->save($entity_option);
                $option_id = !empty($new_option['id']) ? intval($new_option['id']) : null;
                if (empty($option_id)) continue;                
            }
            
            // nếu có product_id và product_item_id -> thì cập nhật lại tất cả thuộc tính trên cho sản phẩm đó
            if(!empty($product_id) && !empty($product_item_id)){
                $data_update[] = [
                    'product_id' => $product_id,
                    'product_item_id' => $product_item_id,
                    'attribute_id' => $attribute_id,
                    'value' => $option_id
                ];
            }
        }

    
        // cập nhật lại thuộc tính phiên bản cho sản phẩm
        if(!empty($data_update)){

            $table = TableRegistry::get('ProductsItemAttribute');

            $clear = $table->deleteAll([
                'product_id' => $product_id, 
                'product_item_id' => $product_item_id
            ]);

            $entities = $table->newEntities($data_update);
            $update = $table->saveMany($entities);
        }

        return true;

    }

    public function syncInventoriesSingleProductKiotViet($kiotviet_inventories = [], $product_id = null, $product_item_id = null, $kiotviet_id = null)
    {
        if(empty($kiotviet_inventories) || empty($product_id) || empty($product_item_id) || empty($kiotviet_id)) return false;

        $stores = TableRegistry::get('ProductsPartnerStore')->getAllStore(KIOTVIET);
        $stores = Hash::combine($stores, '{n}.partner_store_id', '{n}');
        if(empty($stores)) return false;

        $data_update = [];
        foreach($kiotviet_inventories as $inventory){            
            // kiểm tra store_id đã được đồng bộ chưa (nếu chưa thì continue)
            $store_id = !empty($inventory['branchId']) ? intval($inventory['branchId']) : 0;
            if (empty($stores[$store_id])) continue;

            $data_update[] = [
                'partner' => KIOTVIET,
                'product_id' => $product_id, 
                'product_item_id' => $product_item_id,
                'store_id' => $store_id,
                'partner_product_id' => $kiotviet_id, 
                'quantity' => !empty($inventory['onHand']) ? intval($inventory['onHand']) : 0 
            ];
        }

        if(empty($data_update)) return false;

        $table = TableRegistry::get('ProductsPartnerQuantity');
        $clear_old = $table->deleteAll([
            'partner_product_id' => $kiotviet_id, 
            'partner' => KIOTVIET
        ]);

        $entities = $table->newEntities($data_update);        
        $update = $table->saveMany($entities);

        return true;
    }

    public function getProductByCode(string $code)
    {
        if(empty($code)) return $this->System->getResponse([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);

        $token = $this->getToken();
        $http = new Client();
        $result = $http->get($this->domain_api . '/products/code/' . $code, [], 
            [
                'headers' => [
                    'Retailer' => $this->client_name,
                    'Authorization' => "Bearer $token"
                ]
            ]
        );

        $result = $result->getJson();
        
        $id = !empty($result['id']) ? $result['id'] : null;
        $code = !empty($result['code']) ? $result['code'] : null;
        $name = !empty($result['name']) ? $result['name'] : null;
        $price = !empty($result['basePrice']) ? intval($result['basePrice']) : null;
        $inventories = !empty($result['inventories']) ? $result['inventories'] : [];
        $attributes = !empty($result['attributes']) ? $result['attributes'] : [];
        $description = !empty($result['description']) ? $result['description'] : null;
        $error = !empty($result['responseStatus']['errorCode']) ? true : false;
        
        if(!empty($error) || empty($id) || empty($code)){
            $message = !empty($result['responseStatus']['message']) ? $result['responseStatus']['message'] : __d('admin', 'khong_lay_duoc_thong_tin_san_pham_{0}',['KiotViet']);
            return $this->System->getResponse([
                MESSAGE => $message,
                DATA  => [
                    'kiotviet_code' => $id
                ]
            ]);
        }

        return $this->System->getResponse([
            CODE => SUCCESS,
            DATA  => [
                'kiotviet_product_id' => $id,
                'name' => $name,
                'kiotviet_product_price' => $price,
                'inventories' => $inventories,
                'attributes' => $attributes,
                'description' => $description
            ]
        ]);
    }
    
    public function getProductById($id)
    {
        if(empty($id)) return $this->System->getResponse([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);

        $token = $this->getToken();
        $http = new Client();
        $result = $http->get($this->domain_api . '/products/' .$id, [], 
            [
                'headers' => [
                    'Retailer' => $this->client_name,
                    'Authorization' => "Bearer $token"
                ]
            ]
        );

        $result = $result->getJson();
        
        $id = !empty($result['id']) ? $result['id'] : null;
        $code = !empty($result['code']) ? $result['code'] : null;
        $name = !empty($result['name']) ? $result['name'] : null;
        $price = !empty($result['basePrice']) ? intval($result['basePrice']) : null;
        $inventories = !empty($result['inventories']) ? $result['inventories'] : [];
        $attributes = !empty($result['attributes']) ? $result['attributes'] : [];
        $description = !empty($result['description']) ? $result['description'] : null;
        $error = !empty($result['responseStatus']['errorCode']) ? true : false;
        
        if(!empty($error) || empty($id) || empty($code)){
            $message = !empty($result['responseStatus']['message']) ? $result['responseStatus']['message'] : __d('admin', 'khong_lay_duoc_thong_tin_san_pham_{0}',['KiotViet']);
            return $this->System->getResponse([
                MESSAGE => $message,
                DATA  => [
                    'kiotviet_code' => $id
                ]
            ]);
        }

        return $this->System->getResponse([
            CODE => SUCCESS,
            DATA  => [
                'kiotviet_id' => $id,
                'kiotviet_code' => $code,
                'kiotviet_name' => $name,
                'kiotviet_price' => $price,
                'inventories' => $inventories,
                'attributes' => $attributes,
                'description' => $description
            ]
        ]);
    }

    public function syncOrder($code = null)
    {
        if(empty($code)) return [];

        // lấy thông tin đơn hàng
        $table = TableRegistry::get('Orders');
        $item_table = TableRegistry::get('ProductsItem');
        $order_info = $table->find()->where(['code' => $code])->contain(['OrdersItem', 'OrdersContact'])->first();
        
        $kiotviet_code = !empty($order_info['kiotviet_code']) ? $order_info['kiotviet_code'] : null;

        // nếu đã có mã kiotviet_code -> đã đồng bộ đơn rồi
        if(!empty($kiotviet_code)) return [];
        if(empty($order_info['OrdersItem']) || empty($order_info['OrdersContact'])) return [];
    
        $paid = !empty($order_info['paid']) ? floatval($order_info['paid']) : null;
        $total_discount = !empty($order_info['total_discount']) ? floatval($order_info['total_discount']) : null;

        $full_name = !empty($order_info['OrdersContact']['full_name']) ? $order_info['OrdersContact']['full_name'] : null;
        $phone = !empty($order_info['OrdersContact']['phone']) ? $order_info['OrdersContact']['phone'] : null;
        $address = !empty($order_info['OrdersContact']['full_address']) ? $order_info['OrdersContact']['full_address'] : null;

        $branch_id = TableRegistry::get('ProductsPartnerStore')->getKiotVietDefaultBranchId();

        $data_order = [
            'branchId' => $branch_id,
            'description' => "Đơn hàng từ Website - Web4s: $code",
            'discount' => $total_discount,
            'makeInvoice' => false,
            'orderDetails' => [],
            // 'totalPayment' => $paid,
            'orderDelivery' => [
                'receiver' => $full_name,
                'contactNumber' => $phone,
                'address' => $address,
                'status' => 1
            ],
            'customer' => [
                'name' => $full_name
            ]
        ];
        
        $order_items = [];
        $validate_product = true;
        foreach($order_info['OrdersItem'] as $item){
            $product_item_id = !empty($item['product_item_id']) ? intval($item['product_item_id']) : null;
            $quantity = !empty($item['quantity']) ? intval($item['quantity']) : 1;
            $price = !empty($item['price']) ? floatval($item['price']) : 0;
            if(empty($product_item_id)) continue;

            // kiểm tra sản phẩm đã được đồng bộ kiot chưa
            
            $item_info = $item_table->find()->where(['id' => $product_item_id])->select(['id', 'kiotviet_id', 'kiotviet_code'])->first();

            $kiotviet_id = !empty($item_info['kiotviet_id']) ? intval($item_info['kiotviet_id']) : null;
            $kiotviet_code = !empty($item_info['kiotviet_code']) ? $item_info['kiotviet_code'] : null;

            if(empty($kiotviet_id) || empty($kiotviet_code)) continue;

            $order_items[] = [
                'productId' => $kiotviet_id,
                'productCode' => $kiotviet_code,
                'quantity' => $quantity,
                'price' => $price
            ];
        }

        if(!$validate_product) return [];

        $data_order['orderDetails'] = $order_items;

        $token = $this->getToken();

        $http = new Client();
        $result = $http->post($this->domain_api . '/orders', json_encode($data_order), 
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Retailer' => $this->client_name,
                    'Authorization' => "Bearer $token"
                ]
            ]
        );

        $result = $result->getJson();
        $error = !empty($result['responseStatus']['errorCode']) ? true : false;
        if(!empty($error)){
            $message = !empty($result['responseStatus']['message']) ? $result['responseStatus']['message'] : __d('admin', 'dong_bo_don_hang_{0}_khong_thanh_cong',['KiotViet']);
            return $this->System->getResponse([
                MESSAGE => $message
            ]);
        }

        $kiotviet_code = !empty($result['code']) ? $result['code'] : null;
        
        if(empty($kiotviet_code)) return [];

        // cập nhật mã đơn hàng kiot
        $entity = $table->patchEntity($order_info, [
            'kiotviet_code' => $kiotviet_code
        ]);
        
        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $save = $table->save($entity);
            if (empty($save->id)){
                throw new Exception();
            }

            $conn->commit();
            return $this->System->getResponse([CODE => SUCCESS, DATA => $save]);
        }catch (Exception $e) {
            $conn->rollback();
            return [];
        }

        return ['kiotviet_code' => $kiotviet_code];
    }

    public function listProduct($params = [])
    {
        if(empty($params)) return [];

        $token = $this->getToken();
        $http = new Client();
        $result = $http->get($this->domain_api . '/products/', $params, 
            [
                'headers' => [
                    'Retailer' => $this->client_name,
                    'Authorization' => "Bearer $token"
                ],            
            ]
        );

        $result = $result->getJson();
        $data = !empty($result['data']) ? $result['data'] : [];
        $total = !empty($result['total']) ? intval($result['total']) : null;
        $page_size = !empty($result['pageSize']) ? intval($result['pageSize']) : null;

        $error = !empty($result['responseStatus']['errorCode']) ? true : false;
        if(!empty($error)){
            $message = !empty($result['responseStatus']['message']) ? $result['responseStatus']['message'] : __d('admin', 'khong_lay_duoc_danh_sach_san_pham_{0}',['KiotViet']);
            return $this->System->getResponse([
                MESSAGE => $message
            ]);
        }
        return $this->System->getResponse([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'lay_danh_sach_san_pham_{0}_thanh_cong',['KiotViet']),
            DATA  => $data,
            META => [
                'total' => $total,
                'page_size' => $page_size
            ]
        ]);
    }

    public function listAttribute()
    {
        $token = $this->getToken();
        $http = new Client();
        $result = $http->get($this->domain_api . '/attributes/allwithdistinctvalue', [],
            [
                'headers' => [
                    'Retailer' => $this->client_name,
                    'Authorization' => "Bearer $token"
                ],            
            ]
        );
        
        $result = $result->getJson();

        $error = !empty($result['responseStatus']['errorCode']) ? true : false;
        if(!empty($error)){
            $message = !empty($result['responseStatus']['message']) ? $result['responseStatus']['message'] : __d('admin', 'khong_lay_duoc_thong_tin_thuoc_tinh_{0}',['KiotViet']);
            return $this->System->getResponse([
                MESSAGE => $message
            ]);
        }
        return $this->System->getResponse([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'lay_danh_sach_thuoc_tinh_{0}_thanh_cong',['KiotViet']),
            DATA  => $result
        ]);
    }
}

