<?php

namespace Admin\Controller;

use Admin\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Utility\Hash;
use Cake\Http\Response;
use Cake\ORM\Query;
use Cake\Core\Exception\Exception;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\Datasource\ConnectionManager;
use Cake\Utility\Text;

class StoreKiotvietController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function syncStore()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('ProductsPartnerStore'); 
        $kiotviet_stores = $this->loadComponent('Admin.StoreKiotViet')->getBranches();

        $list_kiotviet_stores = !empty($kiotviet_stores[DATA]) ? $kiotviet_stores[DATA] : null;    
      
        if(empty($kiotviet_stores[CODE]) || $kiotviet_stores[CODE] != SUCCESS){
            $this->responseJson([
                MESSAGE => !empty($kiotviet_stores[MESSAGE]) ? $kiotviet_stores[MESSAGE] : __d('admin', 'khong_lay_duoc_thong_tin_cua_hang')
            ]);
        } 
        $exits_default = false;
        $list_store = $table->find()->where([
            'partner' => KIOTVIET
        ])->toList();
        
        $data_save = $store_id = $store = [];
        if(!empty($list_kiotviet_stores)){
            foreach($list_kiotviet_stores as $k => $kiotviet_store){
                $kiotviet_store_id = !empty($kiotviet_store['id']) ? $kiotviet_store['id'] : null;
                $store_id[] = $kiotviet_store_id;

                $name = !empty($kiotviet_store['branchName']) ? $kiotviet_store['branchName'] : null;
                if(empty($kiotviet_store_id) || empty($name)) continue;

                foreach ($list_store as $store) {
                    if($store['is_default'] == 1) {
                        $exits_default = true;
                    }
                    if($store['partner_store_id'] == $kiotviet_store_id) {
                        $stores = $store;
                    }
                }

                $address = !empty($kiotviet_store['address']) ? $kiotviet_store['address'] : null;
                $location = !empty($kiotviet_store['locationName']) ? $kiotviet_store['locationName'] : null;
                $ward = !empty($kiotviet_store['wardName']) ? $kiotviet_store['wardName'] : null;

                $full_address = [];
                if (!empty($address)) $full_address[] = $address;
                if (!empty($ward)) $full_address[] = $ward;
                if (!empty($location)) $full_address[] = $location;

                $data_save = [
                    'name' => $name,
                    'phone' => !empty($kiotviet_store['contactNumber']) ? $kiotviet_store['contactNumber'] : null,
                    'email' => !empty($kiotviet_store['email']) ? $kiotviet_store['email'] : null,
                    'address' => !empty($full_address) ? implode(' - ', $full_address) : null
                ];

                if(!$exits_default && $k == 0) {
                    $data_save['is_default'] = 1;
                }
                if (!empty($stores)) {
                    $data_save['deleted'] = 0;
                    $entity = $table->patchEntity($stores, $data_save);
                } else {
                    $data_save['partner_store_id'] = $kiotviet_store_id;
                    $data_save['partner'] = KIOTVIET;
                    $entity = $table->newEntity($data_save);
                }
                $save = $table->save($entity);
                if(empty($save->id)){
                    $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
                }
            }
        }

        $delete = TableRegistry::get('ProductsPartnerQuantity')->deleteAll(['store_id NOT IN' => $store_id, 'partner' => KIOTVIET]);

        $table->updateAll(
            [  
                'deleted' => 1
            ],
            [
                'partner' => KIOTVIET,
                'partner_store_id NOT IN' =>  $store_id
            ]
        );
        $this->responseJson(
            [
                CODE => SUCCESS,
                MESSAGE => __d('admin', 'dong_bo_danh_sach_cua_hang_thanh_cong')
            ]
        );
    }

    public function setStoreDefault()
    {
        $this->layout = false;
        $this->autoRender = false;
    
        $data = $this->getRequest()->getData();
        $id = !empty($data['id']) ? intval($data['id']) : null;

        if (!$this->getRequest()->is('post') || empty($id)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('ProductsPartnerStore');

        $store_info = $table->find()->where([
            'partner' => KIOTVIET,
            'id' => $id
        ])->first();

        if(empty($store_info)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_cua_hang')]);
        }    

        $entity = $table->patchEntity($store_info, [
            'is_default' => 1
        ]);

        try{
            // cập nhật lại giá trị mặc định của tất cả store
            $table->updateAll(
                [  
                    'is_default' => 0
                ],
                [
                    'partner' => KIOTVIET
                ]
            );
            
            $save = $table->save($entity);
            if(empty($save->id)) {
                throw new Exception();
            }

            $this->responseJson([CODE => SUCCESS, DATA => ['id' => $save->id]]);

        }catch (Exception $e) {
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function registerWebhook()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();;
        $type = !empty($data['type_webhook']) ? $data['type_webhook'] : [];
        $url = !empty($data['url_webhook']) ? $data['url_webhook'] : [];
        if(empty($type) || empty($url)){
            $this->responseJson([
                MESSAGE => __d('admin', 'du_lieu_khong_hop_le')
            ]);
        }      
        
        $register_webhook = $this->loadComponent('Admin.StoreKiotViet')->registerWebhook($type, $url);
          
        $id_webhook = !empty($register_webhook[DATA]['id']) ? $register_webhook[DATA]['id'] : null;

        if(empty($id_webhook) || empty($register_webhook[CODE]) || $register_webhook[CODE] != SUCCESS){
            $this->responseJson([
                MESSAGE => !empty($register_webhook[MESSAGE]) ? $register_webhook[MESSAGE] : __d('admin', 'dang_ky_webhook_khong_thanh_cong')
            ]);
        } 
        $table = TableRegistry::get('Settings');
        // lấy ds webooks của kiotviet
        $setting = $table->find()->where([
            'group_setting' => 'store_kiotviet',
            'code'=> 'webhook'
        ])->first();

        $value = !empty($setting['value']) ? json_decode($setting['value'], true) : [];
        $value[] = !empty($type) ? $type : [];
        $data_save['value'] = json_encode(array_unique($value,));

        $entity = $table->patchEntity($setting, $data_save);
        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();
            
            $save = $table->save($entity);
            if (empty($save->id)){
                throw new Exception();
            }

            $conn->commit();
            $this->responseJson([
                CODE => SUCCESS,
                MESSAGE => __d('admin', 'dang_ky_webhook_thanh_cong')
            ]);

        }catch (Exception $e) {
            $conn->rollback();

            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }          
    }

    public function deleteWebhooks()
    {
        $this->layout = false;
        $this->autoRender = false;

        // lấy danh sách webhook đã đăng ký từ kiotviet
        $webhooks_result = $this->loadComponent('Admin.StoreKiotViet')->listWebhooks();
        
        $webhooks = !empty($webhooks_result[DATA]) ? $webhooks_result[DATA]: [];
        if(empty($webhooks_result[CODE]) || $webhooks_result[CODE] != SUCCESS) {
            $message = !empty($webhooks_result[MESSAGE]) ? $webhooks_result[MESSAGE] : __d('admin', 'khong_lay_duoc_danh_sach_webhook_{0}',['KiotViet']);
            $this->responseJson([MESSAGE => $message]);
        }

        if(empty($webhooks)) {
            $this->responseJson([
                CODE => SUCCESS,
                MESSAGE => __d('admin', 'xoa_webhook_thanh_cong')
            ]);
        }

        $delete_result = true;
        foreach($webhooks as $webhook){
            $webhook_id = !empty($webhook['id']) ? intval($webhook['id']) : null;
            if(empty($webhook_id)) continue;

            $result = $this->loadComponent('Admin.StoreKiotViet')->deleteWebhooks($webhook_id);
            if(empty($result[CODE]) || $result[CODE] != SUCCESS) $delete_result = false;
        }

        if(!$delete_result) {
            $this->responseJson([MESSAGE => __d('admin', 'xoa_webhook_khong_thanh_cong')]);
        }

        $table = TableRegistry::get('Settings');
        $setting = $table->find()->where([
            'group_setting' => 'store_kiotviet',
            'code'=> 'webhook'
        ])->first();

        $entity = $table->patchEntity($setting, ['value' => '']);
        
        $save = $table->save($entity);
        if (empty($save->id)){
            throw new Exception();
        }
        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xoa_webhook_thanh_cong')
        ]);
    }

    public function syncProductCode()
    {
        $this->layout = false;
        $this->autoRender = false; 

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $data = $this->getRequest()->getData();

        $product_item_id = !empty($data['id']) ? intval($data['id']) : null;
        $code = !empty($data['value']) ? $data['value'] : 0;

        // validate data
        if (empty($product_item_id) || empty($code)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }
        
        $table = TableRegistry::get('ProductsItem');
        $quantity_table = TableRegistry::get('ProductsPartnerQuantity');
        $utilities = $this->loadComponent('Utilities');
        //kiểm tra mã này đã được đồng bộ về hay chưa

        $synced = $table->find()->where(['kiotviet_code' => $code])->select(['id'])->first();
        if (!empty($synced)) {
            $this->responseJson([MESSAGE => __d('admin', 'ma_san_pham_da_ton_tai_tren_he_thong')]);
        }

        // lấy thông tin phiên bản sản phẩm    
        $item_info = $table->find()->where(['id' => $product_item_id])->select(['id', 'product_id', 'kiotviet_id','kiotviet_code'])->first();
        if(empty($item_info)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
        }

        $product_id = !empty($item_info['product_id']) ? intval($item_info['product_id']) : null;

        // đọc thông tin sản phẩm bên kiotviet
        $kiotviet_product = $this->loadComponent('Admin.StoreKiotViet')->getProductByCode($code);

        if(empty($kiotviet_product[CODE]) || $kiotviet_product[CODE] != SUCCESS){
            $this->responseJson([
                MESSAGE => !empty($kiotviet_product[MESSAGE]) ? $kiotviet_product[MESSAGE] : __d('admin', 'khong_lay_duoc_thong_tin_san_pham_{0}',['KiotViet'])
            ]);
        }

        $kiotviet_product_info = !empty($kiotviet_product[DATA]) ? $kiotviet_product[DATA] : null;

        $kiotviet_product_id = !empty($kiotviet_product_info['kiotviet_product_id']) ? $kiotviet_product_info['kiotviet_product_id'] : null;
        $kiotviet_product_price = !empty($kiotviet_product_info['kiotviet_product_price']) ? $kiotviet_product_info['kiotviet_product_price'] : null;
        $inventories = !empty($kiotviet_product_info['inventories']) ? $kiotviet_product_info['inventories'] : [];
        $kiotviet_attributes = !empty($kiotviet_product_info['attributes']) ? $kiotviet_product_info['attributes'] : [];

        if(empty($kiotviet_product_id) || empty($inventories)){
            $this->responseJson([
                MESSAGE => !empty($kiotviet_product[MESSAGE]) ? $kiotviet_product[MESSAGE] : __d('admin', 'khong_lay_duoc_thong_tin_san_pham_{0}',['KiotViet'])
            ]);
        } 


        $stores_kiotviet = TableRegistry::get('ProductsPartnerStore')->getAllStore(KIOTVIET);
        $stores_kiotviet = Hash::combine($stores_kiotviet, '{n}.partner_store_id', '{n}');

        // đồng bộ số lượng sản phẩm từ kiotviet về
        $data_sync = [];
        if(!empty($inventories)){
            foreach($inventories as $item){

                $kiotviet_store_id = !empty($item['branchId']) ? intval($item['branchId']) : null;
                $quantity = !empty($item['onHand']) ? intval($item['onHand']) : 0;
                
                // kiểm tra store_id đã được đồng bộ chưa (nếu chưa thì continue)                                            
                if (empty($stores_kiotviet[$kiotviet_store_id])) continue;

                if(empty($kiotviet_store_id) || empty($stores_kiotviet[$kiotviet_store_id])) continue;

                $data_sync[] = [
                    'partner' => KIOTVIET,
                    'product_id' => $product_id,
                    'product_item_id' => $product_item_id,
                    'store_id' => $kiotviet_store_id,
                    'partner_product_id' => $kiotviet_product_id,
                    'quantity' => $quantity
                ];
            }
        }

        if(!empty($kiotviet_attributes)){
            $attributes = TableRegistry::get('Attributes')->getAll($this->lang);
            $attributes = Hash::combine($attributes, '{n}.code', '{n}');
            $attributes_options = TableRegistry::get('AttributesOptions')->getAll($this->lang);
            $attributes_options = Hash::combine($attributes_options, '{n}.code', '{n}');
            $data_attribute =[];
            foreach($kiotviet_attributes as $attribute){
                $attribute_name = !empty($attribute['attributeName']) ? $attribute['attributeName'] : null;
                $attribute_code = strtolower(Text::slug(strtolower($attribute_name), ''));

                $attribute_value = !empty($attribute['attributeValue']) ? $attribute['attributeValue'] : null;
                $attribute_option= strtolower(Text::slug(strtolower($attribute_value), ''));
                
                
                if(!empty($attribute_code)){
                    $attribute_id = !empty($attributes[$attribute_code]['id']) ? intval($attributes[$attribute_code]['id']) : null;
                }
                if(!empty($attribute_option)){
                    $attribute_option_id = !empty($attributes_options[$attribute_option]['id']) ? intval($attributes_options[$attribute_option]['id']) : null;
                }

                if (empty($attribute_id) || empty($attribute_option_id)) continue;

                $data_attribute[] = [
                    'attribute_id' => $attribute_id, 
                    'product_item_id' => $product_item_id,
                    'product_id' => $product_id, 
                    'value' => $attribute_option_id
                ];
            }
        }

        
        // cập nhật dữ liệu
        $entity = $table->patchEntity($item_info, [
            'kiotviet_code' => $code,
            'kiotviet_id' => $kiotviet_product_id,
            'price' => $utilities->formatToDecimal($kiotviet_product_price)
        ]);
        // Lưu số lượng
        $entities = $quantity_table->newEntities($data_sync);

        // Lưu phiên bản
        if(!empty($data_attribute)){
            $entities_attribute = TableRegistry::get('ProductsItemAttribute')->newEntities($data_attribute);
        }

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $save_entity = $table->save($entity);
            if (empty($save_entity->id)){
                throw new Exception();
            }

            // delete All xóa theo product_item_id và store_id của kiotviet
            $delete = TableRegistry::get('ProductsPartnerQuantity')->deleteAll(['product_item_id' => $product_item_id, 'partner' => KIOTVIET]);

            

            $save_entities = $quantity_table->saveMany($entities); 
            $delete = TableRegistry::get('ProductsItemAttribute')->deleteAll(['product_id' => $product_id,'product_item_id' => $product_item_id]);
            if(!empty($data_attribute)){
                $save_attribute_entities = TableRegistry::get('ProductsItemAttribute')->saveMany($entities_attribute);
                if (empty($save_attribute_entities)){
                    throw new Exception();
                }
            }

            if (empty($save_entities)){
                throw new Exception();
            }

            $conn->commit();
            $this->responseJson([CODE => SUCCESS]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function syncAllProduct()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();

        $page_size = 100;
        $current_item = !empty($data['current_item']) ? intval($data['current_item']) : 0;
        $allow_add_new = !empty($data['allow_add_new']) ? true : false;

        $params = [
            'pageSize' => $page_size,
            'currentItem' => $current_item,
            'includeInventory' => true
        ];

        $kiotviet = $this->loadComponent('Admin.StoreKiotViet')->listProduct($params);
        $kiotviet_products = !empty($kiotviet[DATA]) ? $kiotviet[DATA] : null;

        $total = !empty($kiotviet[META]['total']) ? intval($kiotviet[META]['total']) : null;
        $page_size = !empty($kiotviet[META]['page_size']) ? intval($kiotviet[META]['page_size']) : null;

        if(empty($kiotviet[CODE]) || $kiotviet[CODE] != SUCCESS){
            $this->responseJson([
                MESSAGE => !empty($kiotviet[MESSAGE]) ? $kiotviet[MESSAGE] : __d('admin', 'khong_lay_duoc_danh_sach_san_pham_{0}',['KiotViet'])
            ]);
        }

        // nếu products_kiotviet ko trả thêm dữ liệu
        if(empty($kiotviet_products)){
            return $this->responseJson([
                CODE => SUCCESS,
                MESSAGE => __d('admin', 'dong_bo_san_pham_{0}_thanh_cong',['KiotViet']),
                DATA => [
                    'continue' => false,
                    'total_product' => $total,
                    'current_item' => $current_item
                ]
            ]);
        }

        $products_item_table = TableRegistry::get('ProductsItem'); 
        $utilities = $this->loadComponent('Utilities');
        $kiotviet_component = $this->loadComponent('Admin.StoreKiotViet');

        $stores_kiotviet = TableRegistry::get('ProductsPartnerStore')->getAllStore(KIOTVIET);
        $stores_kiotviet = Hash::combine($stores_kiotviet, '{n}.partner_store_id', '{n}');

        // lấy danh sách thuôc tính hệ thống
        $attributes = TableRegistry::get('Attributes')->getAll($this->lang);
        $attributes = Hash::combine($attributes, '{n}.code', '{n}');
        
        $all_options = TableRegistry::get('AttributesOptions')->getAll($this->lang);
        $all_options = Hash::combine($all_options, '{n}.code', '{n}', '{n}.attribute_id');

        // kiểm tra và thực hiện đồng bộ sản phẩm
        $item_attribute_data = [];
        foreach($kiotviet_products as $k => $kiotviet_product){
            
            $kiotviet_id = !empty($kiotviet_product['id']) ? intval($kiotviet_product['id']) : null;
            $kiotviet_code = !empty($kiotviet_product['code']) ? ($kiotviet_product['code']) : null;
            $kiotviet_name = !empty($kiotviet_product['name']) ? $kiotviet_product['name'] : null;
            $kiotviet_description = !empty($kiotviet_product['description']) ? trim($kiotviet_product['description']) : null;
            $kiotviet_price = !empty($kiotviet_product['basePrice']) ? intval($kiotviet_product['basePrice']) : 0;
            $inventories = !empty($kiotviet_product['inventories']) ? $kiotviet_product['inventories'] : [];
            $kiotviet_attributes = !empty($kiotviet_product['attributes']) ? $kiotviet_product['attributes'] : null;            
            if(empty($kiotviet_id) || empty($kiotviet_code)) continue;
            
            $master_product_id = !empty($kiotviet_product['masterProductId']) ? intval($kiotviet_product['masterProductId']) : null;
            $item_info = $products_item_table->find()->where(['kiotviet_id' => $kiotviet_id])->select([
                'id', 'kiotviet_code', 'price', 'product_id','kiotviet_id'
            ])->first();

            $product_id = !empty($item_info['product_id']) ? intval($item_info['product_id']) : null;
            $product_item_id = !empty($item_info['id']) ? intval($item_info['id']) : null;
            
            // kiểm tra nếu $allow_add_new == false -> bỏ qua những sp chưa đồng bộ
            if(empty($allow_add_new) && empty($item_info)) continue;

            // đồng bộ attribute và option khi chưa có trên hệ thống
            if(!empty($kiotviet_attributes) && !empty($attributes) && !empty($all_options)){
                $kiotviet_component->syncAttributeAndOptionSingleProductKiotViet($kiotviet_attributes);
            }

            // đồng bộ master_product KiotViet
            if(!empty($master_product_id)){

                //kiểm tra sản phẩm master_product_id đã được đồng bộ chưa                
                $master_product_info = $products_item_table->find()->where(['kiotviet_id' => $master_product_id])->select(['id', 'product_id'])->first();

                if(empty($master_product_info)) {

                    //call api chi tiết để lấy thông tin của masterproduct id
                    $kiotviet_master_product = $kiotviet_component->getProductById($master_product_id);
                    $master_product = !empty($kiotviet_master_product[DATA]) ? $kiotviet_master_product[DATA] : null;
                    if(empty($master_product)) continue;

                    $new_product = $this->_createNewProductFromKiotViet([
                        'name' => !empty($master_product['kiotviet_name']) ? $master_product['kiotviet_name'] : null,
                        'content' => !empty($master_product['description']) ? trim($master_product['description']) : null,
                        'kiotviet_id' => !empty($master_product['kiotviet_id']) ? $master_product['kiotviet_id'] : null,
                        'kiotviet_code' => !empty($master_product['kiotviet_code']) ? $master_product['kiotviet_code'] : null,
                        'kiotviet_price' => !empty($master_product['kiotviet_product_price']) ? $master_product['kiotviet_product_price'] : null,
                        'kiotviet_attributes' => !empty($data['kiotviet_attributes']) ? $data['kiotviet_attributes'] : []
                    ]);

                    $master_product_id = !empty($new_product['product_id']) ? intval($new_product['product_id']) : null;
                    $master_product_item_id = !empty($new_product['product_item_id']) ? intval($new_product['product_item_id']) : null;
                    if(empty($master_product_id) || empty($master_product_item_id)) continue;

                    // Đồng bộ giá trị của thuộc tính phiên bản sp
                    if(!empty($master_product['attributes'])){
                        $kiotviet_component->syncAttributeAndOptionSingleProductKiotViet($master_product['attributes'], $master_product_id, $master_product_item_id);
                    }

                    // đồng bộ số lượng sản phấm của master
                    if(!empty($master_product['inventories'])){
                        $kiotviet_component->syncInventoriesSingleProductKiotViet($master_product['inventories'], $master_product_id, $master_product_item_id, $kiotviet_id);
                    }

                    // set lại product_id = master_product_id
                    $product_id = $master_product_id;

                }else{ 
                    $product_id = !empty($master_product_info['product_id']) ? intval($master_product_info['product_id']) : null;
                }
            } 

            if(empty($product_id)){
                $new_product = $this->_createNewProductFromKiotViet([
                    'name' => $kiotviet_name,
                    'content' => $kiotviet_description,
                    'kiotviet_id' => $kiotviet_id,
                    'kiotviet_code' => $kiotviet_code,
                    'kiotviet_price' => $kiotviet_price
                ]);

                $product_id = !empty($new_product['product_id']) ? intval($new_product['product_id']) : null;
                $product_item_id = !empty($new_product['product_item_id']) ? intval($new_product['product_item_id']) : null;
                if(empty($product_id) || empty($product_item_id)) continue;

                if(!empty($kiotviet_attributes)){
                    $kiotviet_component->syncAttributeAndOptionSingleProductKiotViet($kiotviet_attributes, $product_id, $product_item_id);
                }                

            }else{
                // thêm dữ liệu kitoviet vào item mới
                if(empty($item_info)){

                    $item_entity = $products_item_table->newEntity([
                        'product_id' => $product_id,
                        'code' => $kiotviet_code,
                        'price' => $utilities->formatToDecimal($kiotviet_price),
                        'kiotviet_id' => $kiotviet_id,
                        'kiotviet_code' => $kiotviet_code
                    ]);

                    $new_item = $products_item_table->save($item_entity);
                    $product_item_id = !empty($new_item->id) ? intval($new_item->id) : null;                
                    if (empty($product_item_id)) continue;

                    // Đồng bộ giá trị của thuộc tính phiên bản sp
                    if(!empty($kiotviet_attributes)){   
                        $kiotviet_component->syncAttributeAndOptionSingleProductKiotViet($kiotviet_attributes, $product_id, $product_item_id);                        
                    }
                }else{
                    
                    $kiotviet_component->updateProductAfterKiotVietChange($kiotviet_id, $kiotviet_code, $kiotviet_price, $kiotviet_attributes);

                }
            }            
            // die();
            // đồng bộ số lượng sản phấm
            if(!empty($inventories)){
                $kiotviet_component->syncInventoriesSingleProductKiotViet($inventories, $product_id, $product_item_id, $kiotviet_id);
            }
        }

        $current_item += count($kiotviet_products);

        if($current_item >= $total) {
            return $this->responseJson([
                CODE => SUCCESS,
                MESSAGE => __d('admin', 'dong_bo_thuoc_tinh_san_pham_{0}_thanh_cong',['KiotViet']),
                DATA => [
                    'continue' => false,
                    'total_product' => $total,
                    'current_item' => $current_item
                ]
            ]);
        }
        
        return $this->responseJson([
            CODE => SUCCESS,
            DATA => [
                'continue' => true,
                'total_product' => $total,
                'current_item' => $current_item,
                'allow_add_new' => $allow_add_new
            ]
        ]);
    }

    private function _createNewProductFromKiotViet($data = [])
    {
        if(empty($data)) return [];

        $name = !empty($data['name']) ? $data['name'] : null;
        if(empty($name)) return [];

        $content = !empty($data['content']) ? $data['content'] : null;
        $link = strtolower(Text::slug(strtolower($name), '-'));
        $link = TableRegistry::get('Links')->getUrlUnique($link);

        $kiotviet_id = !empty($data['kiotviet_id']) ? intval($data['kiotviet_id']) : null;
        $kiotviet_code = !empty($data['kiotviet_code']) ? ($data['kiotviet_code']) : null;
        $kiotviet_price = !empty($data['kiotviet_price']) ? ($data['kiotviet_price']) : null;
        $kiotviet_attributes = !empty($data['kiotviet_attributes']) ? $data['kiotviet_attributes'] : [];

        if(empty($kiotviet_id) || empty($kiotviet_code)) return [];

        $utilities = $this->loadComponent('Utilities');

        // Lưu sản phẩm
        $data_save = [
            'status' => 1,
            'ProductsContent' => [
                'name' => $name,
                'content' => $content,
                'lang' => $this->lang,
                'seo_title' => $name,
                'search_unicode' => strtolower($utilities->formatSearchUnicode([$name]))
            ],
            'Links' => [
                'type' => PRODUCT_DETAIL,
                'url' => $link,
                'lang' => $this->lang,
            ],
            'ProductsItem' => [
                [
                    'code' => $kiotviet_code,
                    'price' => $utilities->formatToDecimal($kiotviet_price),
                    'kiotviet_id' => $kiotviet_id,
                    'kiotviet_code' => $kiotviet_code
                ]
            ]
        ];
        
        $entity = TableRegistry::get('Products')->newEntity($data_save, [
            'associated' => ['ProductsContent', 'Links', 'ProductsItem']
        ]);

        $save_product = TableRegistry::get('Products')->save($entity);

        $product_id = !empty($save_product->id) ? $save_product->id : null;
        $product_item_id = !empty($save_product['ProductsItem'][0]['id']) ? intval($save_product['ProductsItem'][0]['id']) : null;
        if(empty($product_id) || empty($product_item_id)) return [];
        
        return [
            'product_id' => $product_id,
            'product_item_id' => $product_item_id
        ];  
    }

    private function _createNewItemAttributeFromKiotViet($data = [])
    {
        if(empty($data)) return [];
        
                   
        $product_id = !empty($data['product_id']) ? $data['product_id'] : null;
        $product_item_id = !empty($data['product_item_id']) ? $data['product_item_id'] : null;
        $kiotviet_attributes = !empty($data['kiotviet_attributes']) ? $data['kiotviet_attributes'] : [];
        $all_options = !empty($data['all_options']) ? ($data['all_options']) : [];
        $attributes = !empty($data['attributes']) ? ($data['attributes']) : [];

        if(empty($product_id) || empty($product_item_id) || empty($kiotviet_attributes)) return [];
                 
                
        // Đồng bộ giá trị của thuộc tính phiên bản sp
        if(!empty($kiotviet_attributes)){

            foreach($kiotviet_attributes as $kiotviet_attribute){
                
                $kiotviet_attribute_name = !empty($kiotviet_attribute['attributeName']) ? $kiotviet_attribute['attributeName'] : null;
                $kiotviet_attribute_value = !empty($kiotviet_attribute['attributeValue']) ? $kiotviet_attribute['attributeValue'] : null;
                if(empty($kiotviet_attribute_name) || empty($kiotviet_attribute_value)) continue;

                $kiotviet_attribute_code = strtolower(Text::slug(strtolower($kiotviet_attribute_name), ''));
                $kiotviet_option_code = strtolower(Text::slug(strtolower($kiotviet_attribute_value), ''));
                $attribute_id = !empty($attributes[$kiotviet_attribute_code]['id']) ? intval($attributes[$kiotviet_attribute_code]['id']) : null;
                                    
                $attribute_options = !empty($all_options[$attribute_id]) ? $all_options[$attribute_id] : [];
                $option_id = !empty($attribute_options[$kiotviet_option_code]['id']) ? $attribute_options[$kiotviet_option_code]['id'] : null;
  
                if(empty($attribute_id) || empty($option_id) || empty($product_item_id) || empty($product_id)) continue;

                $data_attribute = [
                    'attribute_id' => $attribute_id, 
                    'product_item_id' => $product_item_id,
                    'product_id' => $product_id, 
                    'value' => $option_id
                ];
                $entity_attribute = TableRegistry::get('ProductsItemAttribute')->newEntity($data_attribute);
                
                $save_attribute = TableRegistry::get('ProductsItemAttribute')->save($entity_attribute);
                if(empty($save_attribute->id)) break;
            }
        }
    }

    public function syncAttributeKiotviet()
    {
        $this->layout = false;
        $this->autoRender = false;

        $webhooks_result = $this->loadComponent('Admin.StoreKiotViet')->listAttribute();
        $kiotviet_attributes = !empty($webhooks_result[DATA]) ? $webhooks_result[DATA] : null;

        if(empty($webhooks_result[CODE]) || $webhooks_result[CODE] != SUCCESS){
            return $this->responseJson([
                MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_thuoc_tinh{0}',['KiotViet'])
            ]);
        }

        $table = TableRegistry::get('Attributes');
        $table_options = TableRegistry::get('AttributesOptions');
        
        $list_attribute = $table->find()->contain(['ContentMutiple'])->toList();
        $list_options = $table_options->find()->contain(['ContentMutiple'])->toList();
        $utilities = $this->loadComponent('Utilities');

        $data_save = [];
        if (empty($kiotviet_attributes)) {
            $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'dong_bo_thuoc_tinh_san_pham_{0}_thanh_cong', ['KiotViet'])]);
        }
        
        foreach($kiotviet_attributes as $attribute_kiotviet){
            
            $attribute_values = !empty($attribute_kiotviet['attributeValues']) ? $attribute_kiotviet['attributeValues'] : [];
            $name = !empty($attribute_kiotviet['name']) ? $attribute_kiotviet['name'] : null;  

            if(empty($name) || empty($attribute_values)) continue;

            $attribute_info = $table->getAttributeProductSpecialItemByName($name, $this->lang);
            $attribute_id = !empty($attribute_info['id']) ? $attribute_info['id'] : null;
            
            // nếu thuộc tính phiên bản có tên giống thì thêm mới
            $data_attribute = [];
            if(empty($attribute_id)){
                $code = strtolower(Text::slug(strtolower($name), ''));
                $data_attribute = [
                    'code' => $code,
                    'attribute_type' => PRODUCT_ITEM,
                    'input_type' => SPECICAL_SELECT_ITEM,
                    'has_image' => 0,
                    'required' => 0,
                    'ContentMutiple' => [
                        [
                            'name' => $name,
                            'lang' => $this->lang,
                            'search_unicode' => strtolower($utilities->formatSearchUnicode([$name]))
                        ]
                    ]
                ];
            }

            // kiểm tra options
            $data_option = 0;
            foreach($attribute_values as $key => $attribute_option){

                $name_option = !empty($attribute_option['value']) ? $attribute_option['value'] : null;
                if(empty($name_option)) continue;
                $code_option = strtolower(Text::slug(strtolower($name_option), ''));
                $exist = $table_options->checkExistOptionByName($attribute_id, $name_option, $this->lang);

                if($exist) continue;
                
                if(!empty($attribute_id)){
                    $data_option = [
                        'code' => $code_option,
                        'attribute_id' => $attribute_id,
                        'ContentMutiple' => [
                            [
                                'name' => $name_option,
                                'lang' => $this->lang,
                                'search_unicode' => strtolower($utilities->formatSearchUnicode([$name_option]))
                            ]
                        ]
                    ];                    
                }else{
                    $data_attribute['AttributesOptions'][] = [
                        'code' => $code_option,
                        'ContentMutipleOption' => [
                            [
                                'name' => $name_option,
                                'lang' => $this->lang,
                                'search_unicode' => strtolower($utilities->formatSearchUnicode([$name_option]))
                            ]
                        ]
                    ];
                }   

                if (!empty($data_option)) {
                    $entity = $table_options->newEntity($data_option);
                    $save = $table_options->save($entity);

                    if(empty($save->id)){
                        $this->responseJson([MESSAGE => __d('admin', 'cap_nhat_khong_thanh_cong')]);
                    }
                } 
            }
 
            if (!empty($data_attribute)) {                  
                $entity_attribute = $table->newEntity($data_attribute, [
                    'associated' => ['ContentMutiple', 'AttributesOptions', 'AttributesOptions.ContentMutipleOption']
                ]);
                $save_attribute = $table->save($entity_attribute);

                if(empty($save_attribute->id)){
                    $this->responseJson([MESSAGE => __d('admin', 'cap_nhat_khong_thanh_cong')]);
                }
            }
        } 

        $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'dong_bo_thuoc_tinh_san_pham_{0}_thanh_cong',['KiotViet'])]);
    }
}
