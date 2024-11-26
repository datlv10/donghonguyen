<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;

class PromotionFrontendComponent extends Component
{
	public $controller = null;
    public $components = ['System', 'Utilities', 'Admin.Promotion', 'AffiliateFrontend'];

    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->controller = $this->_registry->getController();
    }

    public function check($coupon = null)
    {
        if(empty($coupon)) {
            return $this->System->getResponse([
                MESSAGE => __d('template', 'vui_long_nhap_ma_coupon')
            ]);
        }

        $session = $this->controller->getRequest()->getSession();
        $cart_info = $session->read(CART);
        $result_promotion = $this->Promotion->checkCoupon($coupon, $cart_info);

        if(!empty($result_promotion[CODE]) && $result_promotion[CODE] == ERROR){
            return $this->System->getResponse([
                MESSAGE => $result_promotion[MESSAGE]
            ]);
        }

        $coupon_info = [];
        $coupon_info = $this->applyCoupon($coupon, $result_promotion[DATA]);
        $session->write(COUPON, $coupon_info);

        if(empty($coupon_info)) {
            return $this->System->getResponse([
                MESSAGE => __d('template', 'ma_coupon_khong_duoc_ap_dung')
            ]);
        }

        $affiliate = $session->read(AFFILIATE);

        if(!empty($affiliate)){
            $this->AffiliateFrontend->apply(['affiliate_code' => $affiliate['affiliate_code']]);
        }

        return $this->System->getResponse([
            CODE => SUCCESS,
            MESSAGE => __d('template', 'xac_minh_ma_coupon_thanh_cong'),
            DATA => $coupon_info
        ]);
    }

    public function applyCoupon($coupon = null, $value = [])
    {
        if(empty($coupon) || empty($value)) return [];
        $cart_info = $this->controller->getRequest()->getSession()->read(CART);
        if(empty($cart_info)) return [];

        $type_discount = !empty($value['type_discount']) ? $value['type_discount'] : null;
        $value_discount_coupon = !empty($value['value']) ? $value['value'] : [];

        $type_value_discount = !empty($value_discount_coupon['type_value_discount']) ? $value_discount_coupon['type_value_discount'] : null;
        $value_discount = !empty($value_discount_coupon['value_discount']) ? floatval($value_discount_coupon['value_discount']) : 0;
        $max_value = !empty($value_discount_coupon['max_value']) ? floatval($value_discount_coupon['max_value']) : 0;
        $total_cart = !empty($cart_info['total']) ? floatval($cart_info['total']) : 0;

        $total = 0;
        switch($type_discount){
            case DISCOUNT_ORDER:
                if($type_value_discount == PERCENT){
                    $total = ($total_cart / 100) * $value_discount;
                }else{
                    $total = $value_discount;
                }
            break;

            case DISCOUNT_PRODUCT:
                if(!empty($cart_info['items'])){
                    foreach ($cart_info['items'] as $key => $item) {
                        $product_item_id = !empty($item['product_item_id']) ? intval($item['product_item_id']) : null;
                        $total_item = !empty($item['total_item']) ? floatval($item['total_item']) : null;
                        $quantity = !empty($item['quantity']) ? intval($item['quantity']) : 1;
                        if(!empty($value['apply_item_ids']) && !in_array($product_item_id, $value['apply_item_ids'])) continue;

                        if($type_value_discount == PERCENT){
                            $discount_item = $total_item / 100 * $value_discount;
                            $total += $discount_item;
                        } else {
                            $total += $value_discount * $quantity;
                        }
                    }
                }
            break;

            case FREE_SHIP:
            break;
        }

        $result = $value;
        $result['coupon'] = $coupon;
        $result['total'] = $total > $max_value && !empty($max_value) ? $max_value : $total;

        return $result;
    }

    public function updateUsedPromotion($coupon = null, $promotion_id = null)
    {
        if(empty($coupon) || empty($promotion_id)) return false;
        $promotion_table = TableRegistry::get('Promotions');
        $coupon_table = TableRegistry::get('PromotionsCoupon');

        $promotion_info = $promotion_table->find()->where(['id' => $promotion_id, 'deleted' => 0, 'status' => 1])->select(['id', 'used'])->first();
        if(empty($promotion_info)) return false;

        $coupon_info = $coupon_table->find()->contain(['Promotions'])->where([
            'PromotionsCoupon.code' => $coupon,
            'PromotionsCoupon.status' => 1,
            'Promotions.id' => $promotion_id,
            'Promotions.status' => 1,
            'Promotions.deleted' => 0
        ])->select(['PromotionsCoupon.id', 'PromotionsCoupon.promotion_id', 'PromotionsCoupon.used', 'PromotionsCoupon.number_use'])->first();

        $data_coupon = [];
        if(!empty($coupon_info)){
            $used = !empty($coupon_info['used']) ? intval($coupon_info['used']) : 0;
            $used += 1;
            $number_use = !empty($coupon_info['number_use']) ? intval($coupon_info['number_use']) : 0;
            $status = 1;
            if(!empty($number_use) && $used >= $number_use){
                $status = 2;
            }

            $data_coupon = [
                'id' => !empty($coupon_info['id']) ? intval($coupon_info['id']) : null,
                'used' => $used,
                'status' => $status
            ];
        }

        $used_promotion = !empty($promotion_info['used']) ? intval($promotion_info['used']) : 0;
        $used_promotion += 1;
        $data_promotion = [
            'id' => $promotion_id,
            'used' => $used_promotion
        ];

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $promotion = $promotion_table->patchEntity($promotion_info, $data_promotion);
            $update_promotion = $promotion_table->save($promotion);
            if (empty($update_promotion)){
                throw new Exception();
            }

            if(!empty($data_coupon)) {
                $coupon = $coupon_table->patchEntity($coupon_info, $data_coupon);
                $update_coupon = $coupon_table->save($coupon);
                if (empty($update_coupon)){
                    throw new Exception();
                }
            }

            $conn->commit();
            return $this->System->getResponse([CODE => SUCCESS, MESSAGE => __d('template', 'cap_nhat_thanh_cong')]);

        }catch (Exception $e) {
            $conn->rollback();
            return $this->System->getResponse([MESSAGE => $e->getMessage()]);  
        }
    }
}