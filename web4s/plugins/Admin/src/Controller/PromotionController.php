<?php

namespace Admin\Controller;

use Admin\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Http\Response;
use Cake\ORM\Query;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;


class PromotionController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function list()
    {
        $this->css_page = [
            '/assets/plugins/global/lightbox/lightbox.css',
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.css'
        ];
        $this->js_page = [
            '/assets/js/pages/list_promotion.js',
            '/assets/plugins/global/lightbox/lightbox.min.js',
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.js'
        ];

        $this->set('path_menu', 'promotion');
        $this->set('title_for_layout', __d('admin', 'chuong_trinh_khuyen_mai'));
    }

    public function listJson()
    {
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $table = TableRegistry::get('Promotions');
        $utilities = $this->loadComponent('Utilities');

        $data = $params = $promotions = [];

        $limit = PAGINATION_LIMIT_ADMIN;
        $page = 1;
        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        // params query
        $params[QUERY] = !empty($data[QUERY]) ? $data[QUERY] : [];

        // params filter
        $params[FILTER] = !empty($data[DATA_FILTER]) ? $data[DATA_FILTER] : [];
        if(!empty($params[QUERY])){
            $params[FILTER] = array_merge($params[FILTER], $params[QUERY]);
        }

        $params[FILTER][LANG] = !empty($params[FILTER][LANG]) ? $params[FILTER][LANG] : TableRegistry::get('Languages')->getDefaultLanguage();

        // params         
        $params[SORT] = !empty($data[SORT]) ? $data[SORT] : [];
        $params['get_user'] = true;
        $params['get_empty_name'] = true;

        
        // page and limit
        $page = !empty($data[PAGINATION][PAGE]) ? intval($data[PAGINATION][PAGE]) : 1;
        $limit = !empty($data[PAGINATION][PERPAGE]) ? intval($data[PAGINATION][PERPAGE]) : PAGINATION_LIMIT_ADMIN;


        // sort 
        $sort_field = !empty($params[SORT][FIELD]) ? $params[SORT][FIELD] : null;
        $sort_type = !empty($params[SORT][SORT]) ? $params[SORT][SORT] : null;

        try {
            $promotions = $this->paginate($table->queryListPromotions($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        } catch (Exception $e) {
            $page = 1;
            $promotions = $this->paginate($table->queryListPromotions($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        }

        $pagination_info = !empty($this->request->getAttribute('paging')['Promotions']) ? $this->request->getAttribute('paging')['Promotions'] : [];
        $meta_info = $utilities->formatPaginationInfo($pagination_info);

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $promotions, 
            META => $meta_info
        ]);
    }

    public function add()
    {
        $max_record = TableRegistry::get('Promotions')->find()->select('id')->max('id');

        $this->set('position', !empty($max_record->id) ? $max_record->id + 1 : 1);

        $this->css_page = [
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.css'
        ];

        $this->js_page = [
            '/assets/plugins/custom/tinymce6/tinymce.min.js',
            '/assets/js/pages/promotion.js',
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.js'
        ];

        $this->set('path_menu', 'promotion_add');
        $this->set('title_for_layout', __d('admin', 'them_chuong_trinh_khuyen_mai'));
        $this->render('update');
    }

    public function update($id = null)
    {
        $table = TableRegistry::get('Promotions');
        $promotion = $table->getDetailPromotion($id, $this->lang, [
            'get_user' => true
        ]);

        $promotion = $table->formatDataPromotionDetail($promotion, $this->lang);
        if(empty($promotion)){
            $this->showErrorPage();
        }

        $this->set('path_menu', 'promotion');
        $this->set('id', $id);
        $this->set('promotion', $promotion);
        
        $this->css_page = [
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.css'
        ];

        $this->js_page = [
            '/assets/plugins/custom/tinymce6/tinymce.min.js',
            '/assets/js/pages/promotion.js',
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.js'
        ];
        $this->set('title_for_layout', __d('admin', 'cap_nhat_chuong_trinh_khuyen_mai'));
    }

    public function save($id = null)
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }
        
        $utilities = $this->loadComponent('Utilities');
        $table = TableRegistry::get('Promotions');        
        if(!empty($id)){
            $promotion = $table->getDetailPromotion($id, [
                'get_user' => false
            ]);

            if(empty($promotion)){
                $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
            }
        }

        // validate data
        if(empty($data['name'])){
            $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_ten_chuong_trinh_khuyen_mai')]);
        }

        if(empty($data['type_discount'])){
            $this->responseJson([MESSAGE => __d('admin', 'vui_long_chon_loai_khuyen_mai')]);
        }

        $value = !empty($data['value']) && $utilities->isJson($data['value']) ? json_decode($data['value']) : [];
        if(empty($value)){
            $this->responseJson([MESSAGE => __d('admin', 'vui_long_cau_hinh_gia_tri_khuyen_mai')]);
        }

        if(isset($data['check_condition']['order'])){
            $condition_order = !empty($data['condition_order']) ? $data['condition_order'] : [];
        }

        if(isset($data['check_condition']['product'])){
            $condition_product = !empty($data['condition_product']) ? $data['condition_product'] : [];
        }

        if(isset($data['check_condition']['location'])){
            $condition_location = !empty($data['condition_location']) ? $data['condition_location'] : [];
        }

        $code = !empty($data['code']) ? trim($data['code']) : $utilities->generateRandomString(8);
        $name = !empty($data['name']) ? $data['name'] : null;
        $data_save = [
            'name' => $name,
            'code' => $code,
            'public' => !empty($data['public']) ? 1 : 0,
            'type_discount' => !empty($data['type_discount']) ? $data['type_discount'] : null,
            'value' => json_encode($value),
            'condition_product' => !empty($condition_product) ? json_encode($condition_product): null,
            'condition_order' => !empty($condition_order) ? json_encode($condition_order): null,
            'condition_location' => !empty($condition_location) ? json_encode($condition_location): null,
            'start_time' => !empty($data['start_time']) ? $utilities->stringDateTimeClientToInt('00:00 - ' . $data['start_time']) : null,
            'end_time' => !empty($data['end_time']) ? $utilities->stringDateTimeClientToInt('23:59 - ' . $data['end_time']) : null,
            'search_unicode' => strtolower($utilities->formatSearchUnicode([$name, $code]))
        ];
        
        // merge data with entity 
        if(empty($id)){
            $data_save['created_by'] = $this->Auth->user('id');
            $promotion = $table->newEntity($data_save);
        }else{            
            $promotion = $table->patchEntity($promotion, $data_save);
        }

        // show error validation in model
        if($promotion->hasErrors()){
            $list_errors = $utilities->errorModel($promotion->getErrors());            
            $this->responseJson([
                MESSAGE => !empty($list_errors[0]) ? $list_errors[0] : null,
                DATA => $list_errors
            ]);
        }

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $save = $table->save($promotion);
            if (empty($save->id)){
                throw new Exception();
            }

            $conn->commit();
            $this->responseJson([CODE => SUCCESS, DATA => ['id' => $save->id]]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function delete()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        $ids = !empty($data['ids']) ? $data['ids'] : [];

        if (!$this->getRequest()->is('post') || empty($ids) || !is_array($ids)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('Promotions');

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            foreach($ids as $id){

                // delete promotion
                $promotion = $table->get($id);
                if (empty($promotion)) {
                    throw new Exception(__d('admin', 'khong_tim_thay_thong_tin_bai_viet'));
                }

                $promotion = $table->patchEntity($promotion, ['id' => $id, 'deleted' => 1], ['validate' => false]);
                $delete = $table->save($promotion);
                if (empty($delete)){
                    throw new Exception();
                }

                // delete link
                $delete_link = TableRegistry::get('Links')->updateAll(
                    [  
                        'deleted' => 1
                    ],
                    [  
                        'foreign_id' => $id,
                        'type' => PROMOTION_DETAIL
                    ]
                );

                $delete_coupon = TableRegistry::get('PromotionsCoupon')->deleteAll([  
                    'promotion_id' => $id
                ]);

                if (empty($delete_coupon)) {
                    throw new Exception(__d('admin', 'khong_the_xoa_ma_coupon'));
                }
            }

            $conn->commit();
            $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'xoa_du_lieu_thanh_cong')]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function changeStatus()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        $ids = !empty($data['ids']) ? $data['ids'] : [];
        $status = !empty($data['status']) ? 1 : 0;

        if (!$this->getRequest()->is('post') || empty($ids) || !is_array($ids)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('Promotions');

        $promotions = $table->find()->where([
            'Promotions.id IN' => $ids,
            'Promotions.deleted' => 0
        ])->select(['Promotions.id', 'Promotions.status'])->toArray();
        
        if(empty($promotions)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_bai_viet')]);
        }

        $patch_data = [];
        foreach ($ids as $k => $promotion_id) {
            $patch_data[] = [
                'id' => $promotion_id,
                'status' => $status,
                'draft' => 0
            ];
        }

        $entities = $table->patchEntities($promotions, $patch_data, ['validate' => false]);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $change_status = $table->saveMany($entities);            
            if (empty($change_status)){
                throw new Exception();
            }
            
            $conn->commit();
            $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'cap_nhat_thanh_cong')]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function changePosition()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        $id = !empty($data['id']) ? intval($data['id']) : null;
        $value = !empty($data['value']) ? $data['value'] : 0;

        if(!$this->getRequest()->is('post') || empty($id)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('Promotions');
        $promotion = $table->get($id);
        if(empty($promotion)) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
        }

        $promotion = $table->patchEntity($promotion, ['position' => $value], ['validate' => false]);

        try{
            $save = $table->save($promotion);

            if (empty($save->id)){
                throw new Exception();
            }
            $this->responseJson([CODE => SUCCESS, DATA => ['id' => $save->id]]);

        }catch (Exception $e) {
            $this->responseJson([MESSAGE => $e->getMessage()]);
        }
    }

    public function loadListPromotionInvalid()
    {        
        $this->viewBuilder()->enableAutoLayout(false);
        $data = $this->getRequest()->getData();

        $order = !empty($data['order']) ? $data['order'] : [];

        $promotion_component = $this->loadComponent('Admin.Promotion');
        $promotions = TableRegistry::get('Promotions')->getListPromotionActive();

        $promotion_invalid = [];
        if(!empty($promotions)){
            foreach ($promotions as $promotion_id => $promotion) {
                $invalid = $promotion_component->checkConditionPromotion($promotion_id, $order);                
                if(empty($invalid)) continue;

                $invalid['id'] = $promotion_id;
                $invalid['name'] = !empty($promotion['name']) ? $promotion['name'] : null;

                $promotion_invalid[$promotion_id] = $invalid;
            }
        }

        $this->set('promotion_invalid', $promotion_invalid);
    }

    public function autoSuggest()
    {
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('Promotions');
        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        $filter = !empty($data[FILTER]) ? $data[FILTER] : [];
        
        $promotions = $table->queryListPromotions([
            FILTER => $filter,
            FIELD => FULL_INFO
        ])->limit(10)->toArray();
        

        $result = [];
        if(!empty($promotions)){
            foreach($promotions as $k => $promotion){
                $result[$k] = $table->formatDataPromotionDetail($promotion, $this->lang);
            }
        }

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $result, 
        ]);
    }
}