<?php
declare(strict_types=1);

namespace Admin\View\Helper;

use Cake\View\Helper;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class ShippingAdminHelper extends Helper
{   
    public function getListShippingMethod()
    {
    	return [
            RECEIVED_AT_STORE => __d('admin', 'nhan_tai_cua_hang'),
            NORMAL_SHIPPING => __d('admin', 'tu_van_chuyen'),
            SHIPPING_CARRIER => __d('admin', 'gui_qua_hang_van_chuyen')
        ];
    }

    public function getListShippingCarrier()
    {
        return TableRegistry::get('ShippingsCarrier')->getList();
    }

    public function getListRequiredNote()
    {
        return [
            'CHOTHUHANG' => __d('admin', 'cho_thu_hang'),
            'CHOXEMHANGKHONGTHU' => __d('admin', 'cho_xem_hang_khong_thu'),
            'KHONGCHOXEMHANG ' => __d('admin', 'khong_cho_xem_hang'),
        ];
    }

    public function getShippingMethodName($shipping_method_id = null, $lang = null)
    {
        if(empty($shipping_method_id) || empty($lang)) return null;

        $shipping_method = TableRegistry::get('ShippingsMethodContent')->find()->where([
            'shipping_method_id' => $shipping_method_id, 
            'lang' => $lang
        ])->select(['name'])->first();

        return !empty($shipping_method['name']) ? $shipping_method['name'] : null;
    }
}