<?php

namespace Admin\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;
use Cake\I18n\Date;
use Cake\I18n\Time;

class PromotionComponent extends Component
{
	public $controller = null;
    public $components = ['System', 'Utilities'];

    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->controller = $this->_registry->getController();
    }
  	
    public function checkCoupon($coupon = null, $order_info = [])
    {
        $result = [
            CODE => ERROR,
            MESSAGE => __d('admin', 'ma_khuyen_mai_khong_hop_le'),
            DATA => []
        ];

        if(empty($coupon)) return $result;
        
        $promotion = TableRegistry::get('Promotions')->find()->where([
            'deleted' => 0,
            'status' => 1,
            'code' => $coupon
        ])->select(['id'])->first();

        $promotion_id = !empty($promotion['id']) ? intval($promotion['id']) : null;
        
        if(empty($promotion_id)) {
            $coupon_info = TableRegistry::get('PromotionsCoupon')->find()->contain(['Promotions'])->where([
                'PromotionsCoupon.code' => $coupon,
                'Promotions.status' => 1,
                'Promotions.deleted' => 0
            ])->select(['PromotionsCoupon.promotion_id', 'PromotionsCoupon.status'])->first();

            if(empty($coupon_info['status'])) return $result;
            if(!empty($coupon_info['status']) && $coupon_info['status'] == 2){
                $result[MESSAGE] = __d('admin', 'ma_coupon_da_duoc_su_dung');
                return $result;
            }
            $promotion_id = !empty($coupon_info['promotion_id']) ? intval($coupon_info['promotion_id']) : null;
        }
        if(empty($promotion_id)) return $result;

        $coupon_value = $this->checkConditionPromotion($promotion_id, $order_info);

        $result = [
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xac_minh_ma_coupon_thanh_cong'),
            DATA => $coupon_value
        ];
       
        return $result;
    }

    public function saveCoupon($data = [])
    {
        if (empty($data)) {
            return $this->System->getResponse([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $promotion_id = !empty($data['promotion_id']) ? intval($data['promotion_id']) : null;
        if (empty($promotion_id)) {
            return $this->System->getResponse([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_chuong_trinh')]);
        }
    
        // type = 1 gen coupon ngau nhien | type = 0 là gen coupon tu dong
        // neu gen ngau nhien thi k can check exit con tu dong thi check exit
        $type = !empty($data['type']) ? $data['type'] : 0;

        if ($type == 1) {
            $data['code'] = $this->formatCoupon($data);
        }
        
        $table = TableRegistry::get('PromotionsCoupon');  

        // validate data
        $data_code = !empty($data['code']) ? $data['code'] : null;
        if($type == 0 && $table->checkExist($data_code)){
            return $this->System->getResponse([MESSAGE => __d('admin', 'ma_coupon_da_ton_tai_tren_he_thong')]);
        }

        $data_save = [];
        foreach ($data_code as $key => $code) {            
            $data_save[$key]['promotion_id'] = $promotion_id;
            $data_save[$key]['code'] = !empty($code) ? strtoupper($code) : null;
            $data_save[$key]['used'] = 0;
            $data_save[$key]['number_use'] = !empty($data['number_use']) ? $data['number_use'] : 0;
            $data_save[$key]['status'] = 1;
        }

        $promotion_coupon = $table->newEntities($data_save);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();
            
            $save_all = $table->saveMany($promotion_coupon);
            if (empty($save_all)){
                throw new Exception();
            }

            // luu tong ma coupon cho bang promotion
            $save_promotion = $this->saveTotalCodeCoupon($promotion_id);
            if (empty($save_promotion)){
                throw new Exception();
            }

            $conn->commit();
            return $this->System->getResponse([CODE => SUCCESS, DATA => $save_all]);

        }catch (Exception $e) {
            $conn->rollback();
            return $this->System->getResponse([MESSAGE => $e->getMessage()]);  
        }
    }

    public function formatCoupon($data = [])
    {
        $total = !empty($data['total_code']) ? $data['total_code'] : 1;
        $length = !empty($data['length_code']) ? $data['length_code'] : 6;

        $prefix = !empty($data['prefix']) ? $data['prefix'] : '';
        $suffixes = !empty($data['suffixes']) ? $data['suffixes'] : '';

        // generated array code
        $arr_code = [];
        for ($i = 0; $i < 2000; $i++) {
            // break this loop for if item arr_code = total
            if (count($arr_code) >= $total) {
                break;
            }

            $code = $prefix . $this->Utilities->generateRandomString($length) . $suffixes;
            // check new generated code  has exist in arr_code
            if (!in_array($code, $arr_code)) {
                array_push($arr_code, $code);
            }
        }
        return $arr_code;
    }

    public function saveTotalCodeCoupon($promotion_id = null)
    {
        $table = TableRegistry::get('PromotionsCoupon');
        $promotion_table = TableRegistry::get('Promotions'); 

        if (empty($promotion_id)) {
            $this->System->getResponse([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $total_coupon = $table->find()->where([
            'promotion_id' => $promotion_id
        ])->count();

        $promotion = $promotion_table->getDetailPromotion($promotion_id);

        if(empty($promotion)){
            $this->System->getResponse([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_chuong_trinh_khuyen_mai')]);
        }

        $promotion = $promotion_table->patchEntity($promotion, [
            'id' => $promotion_id,
            'number_coupon' => floatval($total_coupon)
        ]);
        
        $save_total_coupon = $promotion_table->save($promotion);

        return $save_total_coupon;
    }

    public function checkPromotionApply($order_info = [])
    {
        $promotions = TableRegistry::get('Promotions')->getListPromotionActive();
        if(empty($promotions)) return [];

        foreach($promotions as $promotion){
            $promotion_id = !empty($promotion['id']) ? intval($promotion['id']) : null;

            $result = $this->checkConditionPromotion($promotion_id, $order_info);
            if(!empty($result)) return $result;
        }

        return [];
    }

    public function checkConditionPromotion($promotion_id = null, $order_info = [])
    {
        // kiểm tra thông tin đặt hàng
        if(empty($order_info)) return [];
        $items_order = !empty($order_info['items']) ? $order_info['items'] : [];
        $number_product_order = !empty($items_order) ? count($items_order) : 1;
        $total_order = !empty($order_info['total']) ? intval($order_info['total']) : 0;
        $city_id_order = !empty($order_info['city_id']) ? intval($order_info['city_id']) : null;
        if(empty($items_order)) return [];


        // lấy thông tin chương trình khuyến mãi
        $promotions = TableRegistry::get('Promotions')->getListPromotionActive();
        $promotion_info = !empty($promotions[$promotion_id]) ? $promotions[$promotion_id] : [];
        $type_discount = !empty($promotion_info['type_discount']) ? $promotion_info['type_discount'] : null;
        $value = !empty($promotion_info['value']) ? json_decode($promotion_info['value'], true) : [];
        if(empty($promotion_info) || empty($type_discount) || empty($value)) return [];

        $now_date = strtotime(date('Y-m-d H:i:s'));
        $start_time = !empty($promotion_info['start_time']) ? intval($promotion_info['start_time']) : null;
        $end_time = !empty($promotion_info['end_time']) ? intval($promotion_info['end_time']) : null;
        
        $condition_order = !empty($promotion_info['condition_order']) ? json_decode($promotion_info['condition_order'], true) : [];
        $condition_product = !empty($promotion_info['condition_product']) ? json_decode($promotion_info['condition_product'], true) : [];
        $condition_location = !empty($promotion_info['condition_location']) ? json_decode($promotion_info['condition_location'], true) : [];

        // kiểm tra điều kiện của đơn hàng
        if(!empty($start_time) && $now_date < $start_time) return [];
        if(!empty($end_time) && $now_date > $end_time) return [];

        // điều kiện đơn hàng
        if(!empty($condition_order)){
            $min_value = !empty($condition_order['min_value']) ? intval(str_replace(',', '', $condition_order['min_value'])) : 0;
            $max_value = !empty($condition_order['max_value']) ? intval(str_replace(',', '', $condition_order['max_value'])) : 0;
            $number_product = !empty($condition_order['number_product']) ? intval(str_replace(',', '', $condition_order['number_product'])) : 0;

            if($min_value > $total_order) return [];
            if(!empty($max_value) && $max_value < $total_order) return [];
            if($number_product > $number_product_order) return [];
        }

        // điều kiện sản phẩm
        if(!empty($condition_product) && !empty($condition_product['ids'])){
            $condition_ids = !empty($condition_product['ids']) ? $condition_product['ids'] : [];

            $type_condition_product = !empty($condition_product['type']) ? $condition_product['type'] : null;
            if(empty($type_condition_product) || !in_array($type_condition_product, [PRODUCT, CATEGORY_PRODUCT, BRAND])) return [];

            $check = false;
            $apply_ids = [];
            switch($type_condition_product){
                case PRODUCT:
                    foreach($items_order as $item){
                        $product_item_id = !empty($item['product_item_id']) ? intval($item['product_item_id']) : null;

                        if(in_array($product_item_id, $condition_ids)){
                            $check = true;
                            if($type_discount == 'discount_product'){
                                $apply_ids[] = $product_item_id;
                            }
                        }
                    }
                break;

                case CATEGORY_PRODUCT:
                    $all_category = [];
                    foreach($condition_ids as $category_id){
                        $category_child = TableRegistry::get('Categories')->getAllChildCategoryId($category_id);
                        $all_category = array_merge($all_category, $category_child);
                    }
                    $all_category = array_unique(array_filter($all_category));

                    foreach($items_order as $item){
                        $product_item_id = !empty($item['product_item_id']) ? intval($item['product_item_id']) : null;
                        $product_id = !empty($item['product_id']) ? intval($item['product_id']) : null;
                        if(empty($product_id) || empty($product_item_id)) continue;

                        $category_ids = TableRegistry::get('CategoriesProduct')->getListCategoryIds($product_id);
                        if(empty($category_ids)) continue;

                        foreach($category_ids as $category_id){
                            if(in_array($category_id, $all_category)){
                                $check = true;

                                if($type_discount == 'discount_product'){
                                    $apply_ids[] = $product_item_id;
                                }
                            }
                        }
                    }
                break;

                case BRAND:
                    foreach($items_order as $item){
                        $product_item_id = !empty($item['product_item_id']) ? intval($item['product_item_id']) : null;
                        $product_id = !empty($item['product_id']) ? intval($item['product_id']) : null;
                        if(empty($product_id) || empty($product_item_id)) continue;

                        $product = TableRegistry::get('Products')->find()->where(['id' => $product_id])->first();
                        $brand_id = !empty($product['brand_id']) ? intval($product['brand_id']) : null;
                        if(empty($brand_id)) continue;

                        if(in_array($brand_id, $condition_ids)){
                            $check = true;

                            if($type_discount == 'discount_product'){
                                $apply_ids[] = $product_item_id;
                            }
                        }
                    }
                break;
            }

            if(!$check) return [];

        }

        // điều kiện tỉnh thành
        if(!empty($condition_location) && !empty($condition_product['ids'])){
            $condition_location_ids = !empty($condition_location['ids']) ? $condition_location['ids'] : [];

            if(!in_array($city_id_order, $condition_location_ids)) return [];
        }

        return [
            'promotion_id' => $promotion_id,
            'type_discount' => $type_discount,
            'value' => $value,
            'apply_item_ids' => !empty($apply_ids) ? array_unique($apply_ids) : []
        ];
    }
}
