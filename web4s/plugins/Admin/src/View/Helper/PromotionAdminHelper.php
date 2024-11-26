<?php
declare(strict_types=1);

namespace Admin\View\Helper;

use Cake\View\Helper;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

class PromotionAdminHelper extends Helper
{
    public function getListTypePromotion()
    {
        $result = [
            DISCOUNT_ORDER => __d('admin', 'chiet_khau_don_hang'),
            DISCOUNT_PRODUCT => __d('admin', 'chiet_khau_san_pham'),
            FREE_SHIP => __d('admin', 'mien_phi_van_chuyen')
            // GIVE_PRODUCT => __d('admin', 'tang_san_pham')
        ];

        return $result;
    }

    public function getListConditionProduct()
    {
        $result = [
            PRODUCT => __d('admin', 'san_pham'),
            CATEGORY_PRODUCT => __d('admin', 'danh_muc_san_pham'),
            BRAND => __d('admin', 'thuong_hieu')
        ];

        return $result;
    }

    public function getListPromotion()
    {
        $promotions = TableRegistry::get('Promotions')->queryListPromotions([
            FIELD => LIST_INFO,
            FILTER => [
                STATUS => 1
            ],
            SORT => [FIELD => 'position', SORT => DESC]
        ])->toArray();

        $result = [];
        $result =  Hash::combine($promotions, '{n}.id', '{n}.name');
        return $result;
    }

    public function getDetail($product_id = null)
    {
        if(empty($product_id)) return [];

        // lấy thông tin chương trình khuyến mãi kể cả các chương trình đã xóa
        $promotion = TableRegistry::get('Promotions')->find()->where(['id' => $product_id])->first();

        return $promotion;
    }

}
