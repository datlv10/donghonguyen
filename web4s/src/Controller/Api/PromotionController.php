<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\Api\AppController;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Core\Exception\Exception;

class PromotionController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }


    public function check()
    {
        $data = $this->data_bearer;
        if(!$this->getRequest()->is('post') || empty($data)){
           $this->responseErrorApi([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $coupon = !empty($data['coupon']) ? $data['coupon'] : null;
        if(empty($coupon)){
            $this->responseErrorApi([MESSAGE => __d('template', 'vui_long_nhap_ma_coupon')]);
        }

        $result = $this->loadComponent('PromotionFrontend')->check($coupon);
        if($result[CODE] == SUCCESS){
            $this->responseApi($result);
        }else{
            $this->responseErrorApi($result);
        }
    }

    public function delete()
    {
        $data = $this->data_bearer;
        if(!$this->getRequest()->is('post')){
           $this->responseErrorApi([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }
    
        $this->getRequest()->getSession()->write(COUPON, null);

        $affiliate = $this->getRequest()->getSession()->read(AFFILIATE);
        if(!empty($affiliate)){
            $this->loadComponent('AffiliateFrontend')->apply(['affiliate_code' => $affiliate['affiliate_code']]);
        }
        
        $this->responseApi([
            MESSAGE => __d('template', 'xoa_ma_coupon_thanh_cong')
        ]);
    }

    public function listCoupon()
    {
        $data = $this->data_bearer;

        $has_pagination = !empty($data[HAS_PAGINATION]) ? true : false;
        $number_record = !empty($data[NUMBER_RECORD]) ? intval($data[NUMBER_RECORD]) : 12;
        $page = !empty($data[PAGE]) ? intval($data[PAGE]) : 1;

        $sort_field = !empty($data[SORT_FIELD]) ? $data[SORT_FIELD] : null;
        $sort_type = !empty($data[SORT_TYPE]) ? $data[SORT_TYPE] : null;        

        $lang = !empty($data[LANG]) ? $data[LANG] : LANGUAGE;

        $params = [
            'check_expiry_date' => true,
            FIELD => FULL_INFO,
            SORT => [
                FIELD => $sort_field,
                SORT => $sort_type
            ],
            FILTER => [
                STATUS => 1,
                'public' => 1
            ]
        ];

        $table = TableRegistry::get('Promotions');

        if(!$has_pagination){
            $promotions = $table->queryListPromotions($params)->limit($number_record)->toArray();
        }else{
            $paginator = $this->loadComponent('PaginatorExtend');
            try {
                $promotions = $paginator->paginate($table->queryListPromotions($params), [
                    'limit' => $number_record,
                    'page' => $page
                ])->toArray();
            } catch (Exception $e) {
                $promotions = [];
            }

            $pagination_info = !empty($this->getRequest()->getAttribute('paging')['Promotions']) ? $this->getRequest()->getAttribute('paging')['Promotions'] : [];
            $pagination = $this->loadComponent('Utilities')->formatPaginationInfo($pagination_info);       
        }      
        
        $result = [];
        if(!empty($promotions)){            
            foreach ($promotions as $promotion) {
                $result[] = $table->formatDataPromotionDetail($promotion, $lang);
            }
        }

        $this->responseApi([
            CODE => SUCCESS,
            DATA => $result,
            EXTEND => !empty($result) && !empty($pagination) ? [PAGINATION => $pagination] : []
        ]);
    }

}