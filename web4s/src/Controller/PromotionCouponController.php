<?php

namespace App\Controller;

use Cake\ORM\TableRegistry;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;

class PromotionCouponController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

	public function check() 
	{
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        if(!$this->getRequest()->is('post') || empty($data)){
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $coupon = !empty($data['coupon']) ? $data['coupon'] : null;
        if(empty($coupon)){
            $this->responseJson([MESSAGE => __d('template', 'vui_long_nhap_ma_coupon')]);
        }

        $check_promotion = $this->loadComponent('PromotionFrontend')->check($coupon);

        $message = !empty($check_promotion[MESSAGE]) ? $check_promotion[MESSAGE] : __d('template', 'xac_minh_ma_coupon_thanh_cong');
        if(isset($check_promotion[CODE]) && $check_promotion[CODE] == ERROR){
            $message = !empty($check_promotion[MESSAGE]) ? $check_promotion[MESSAGE] : __d('template', 'ma_coupon_khong_duoc_ap_dung');
        }

        $this->responseJson([
            CODE => SUCCESS,
            DATA => !empty($check_promotion[DATA]) ? $check_promotion[DATA] : [],
            MESSAGE => $message
        ]);
    }

    public function delete() 
    {
        $this->layout = false;
        $this->autoRender = false;

        $data_request = !empty($this->request->getData()) ? $this->request->getData() : [];
        if(!$this->getRequest()->is('post')){
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $this->getRequest()->getSession()->write(COUPON, null);

        $affiliate = $this->getRequest()->getSession()->read(AFFILIATE);
        if(!empty($affiliate)){
            $this->loadComponent('AffiliateFrontend')->apply(['affiliate_code' => $affiliate['affiliate_code']]);
        }
        
        $this->responseJson([CODE => SUCCESS, MESSAGE => __d('template', 'xoa_ma_coupon_thanh_cong')]);
    }

}