<?php

namespace Admin\Controller;

use Admin\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Http\Response;
use Cake\ORM\Query;
use Cake\Core\Exception\Exception;
use Cake\Utility\Hash;
use Cake\Datasource\ConnectionManager;

class CustomerAffiliateRequestController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function list() 
    {
        $this->css_page = [
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.css'
        ];

        $this->js_page = [
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.js',
            '/assets/js/pages/list_affiliate_request.js'
        ];

        $this->set('path_menu', 'affiliate_request');
        $this->set('title_for_layout', __d('admin', 'yeu_cau_hop_tac'));
    }

    public function listJson()
    {

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $table = TableRegistry::get('CustomersAffiliateRequest');
        $utilities = $this->loadComponent('Utilities');

        $data = $params = [];

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

        // params sort         
        $params[SORT] = !empty($data[SORT]) ? $data[SORT] : [];

        // page and limit
        $page = !empty($data[PAGINATION][PAGE]) ? intval($data[PAGINATION][PAGE]) : 1;
        $limit = !empty($data[PAGINATION][PERPAGE]) ? intval($data[PAGINATION][PERPAGE]) : PAGINATION_LIMIT_ADMIN;

        // sort 
        $sort_field = !empty($params[SORT][FIELD]) ? $params[SORT][FIELD] : null;
        $sort_type = !empty($params[SORT][SORT]) ? $params[SORT][SORT] : null;

        try {
            $affiliate_request = $this->paginate($table->queryListAffiliateRequest($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();

        } catch (Exception $e) {
            $page = 1;
            $affiliate_request = $this->paginate($table->queryListAffiliateRequest($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        }

        $pagination_info = !empty($this->request->getAttribute('paging')['CustomersAffiliateRequest']) ? $this->request->getAttribute('paging')['CustomersAffiliateRequest'] : [];
        $meta_info = $utilities->formatPaginationInfo($pagination_info);

        $result = [];
        if(!empty($affiliate_request)){
            foreach ($affiliate_request as $key => $affiliate) {
                $result[] = $table->formatDataAffiliateRequestDetail($affiliate);
            }
        }

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $result, 
            META => $meta_info
        ]);
    }

    public function changeStatus()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        $id = !empty($data['ids']) ? $data['ids'][0] : [];
        $status = !empty($data['status']) ? $data['status'] : 0;

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        if (empty($id)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('CustomersAffiliateRequest');
        $customers_table = TableRegistry::get('Customers');
        $customers_bank_table = TableRegistry::get('CustomersBank');
        $customers_affiliate_table = TableRegistry::get('CustomersAffiliate');

        // lay thong tin request
        $affiliate_request_info = $table->find()->where(['id' => $id])->first();
        if (empty($affiliate_request_info)) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
        }

        $bank = !empty($affiliate_request_info['bank']) ? json_decode($affiliate_request_info['bank'], true) : [];
        $identity_card = !empty($affiliate_request_info['identity_card']) ? json_decode($affiliate_request_info['identity_card'], true) : [];

        // thay doi trang thai request
        $affiliate_request = $table->patchEntity($affiliate_request_info, ['status' => $status], ['validate' => false]);

        // thong tin khach hang
        $customer_id = !empty($affiliate_request_info['customer_id']) ? $affiliate_request_info['customer_id'] : null;
        $customer_info = $customers_table->find()->where([
            'id' => $customer_id,
            'status' => 1,
            'deleted' => 0
        ])->first();

        if (empty($customer_info)) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_khach_hang')]);
        }

        // thông tin trạng thái partner trong bảng customer
        $is_partner_affiliate = isset($customer_info['is_partner_affiliate']) ? intval($customer_info['is_partner_affiliate']) : 0;

        if (!empty($is_partner_affiliate) && $is_partner_affiliate == 1) {
            $this->responseJson([MESSAGE => __d('admin', 'khach_hang_hien_tai_da_la_doi_tac')]);
        }

        // kiểm tra xem thông tin customer_id này đã lưu vào bảng customer_affiliate hay chưa
        $customers_affiliate_info = $customers_affiliate_table->find()->where([
            'customer_id' => $customer_id
        ])->first();

        $number_referral = !empty($customers_affiliate_info['number_referral']) ? intval($customers_affiliate_info['number_referral']) : 0;
        $number_order_success = !empty($customers_affiliate_info['number_order_success']) ? intval($customers_affiliate_info['number_order_success']) : 0;
        $total_order_success = !empty($customers_affiliate_info['total_order_success']) ? floatval($customers_affiliate_info['total_order_success']) : 0;
        $number_order_failed = !empty($customers_affiliate_info['number_order_failed']) ? intval($customers_affiliate_info['number_order_failed']) : 0;
        $total_order_failed = !empty($customers_affiliate_info['total_order_failed']) ? floatval($customers_affiliate_info['total_order_failed']) : 0;
        $total_point = !empty($customers_affiliate_info['total_point']) ? intval($customers_affiliate_info['total_point']) : 0;

        $data_save_customers_affiliate = [
            'customer_id' => $customer_id,
            'number_referral' => $number_referral,
            'number_order_success' => $number_order_success,
            'total_order_success' => $total_order_success,
            'number_order_failed' => $number_order_failed,
            'total_order_failed' => $total_order_failed,
            'total_point' => $total_point
        ];

        $customer = $customers_affiliate = $customers_bank = [];

        $data_save_customer_info = [
            'is_partner_affiliate' => $status
        ];

        if (!empty($status) && $status == 1) {
            // khoi tao data save thong tin cmnd/cccd cua khach hang
            if (!empty($identity_card)) {
                $data_save_customer_info['identity_card_id'] = !empty($identity_card['identity_card_id']) ? $identity_card['identity_card_id'] : null;
                $data_save_customer_info['identity_card_date'] = !empty($identity_card['identity_card_date']) ? strtotime(date('Y-m-d 00:00:00', strtotime(str_replace('/', '-', $identity_card['identity_card_date'])))) : null;
                $data_save_customer_info['identity_card_name'] = !empty($identity_card['identity_card_name']) ? $identity_card['identity_card_name'] : null;
                $data_save_customer_info['identity_card_where'] = !empty($identity_card['identity_card_where']) ? $identity_card['identity_card_where'] : null;
            }

            // khoi tao data save thong tin bank cua khach hang
            if (!empty($bank)) {
                $bank_save = [
                    'customer_id' => $customer_id,
                    'bank_key' => !empty($bank['bank_key']) ? $bank['bank_key'] : null,
                    'bank_name' => !empty($bank['bank_name']) ? $bank['bank_name'] : null,
                    'bank_branch' => !empty($bank['bank_branch']) ? $bank['bank_branch'] : null,
                    'account_number' => !empty($bank['account_number']) ? $bank['account_number'] : null,
                    'account_holder' => !empty($bank['account_holder']) ? $bank['account_holder'] : null,
                    'is_default' => 1,
                ];

                $customers_bank = $customers_bank_table->newEntity($bank_save);
            }

            // khi kich hoat partner se luu thong tin customer vao bang customer_affiliate 
            if (!empty($customers_affiliate_info)) {

                $customers_affiliate = $customers_affiliate_table->patchEntity($customers_affiliate_info, $data_save_customers_affiliate);
            } else {

                $customers_affiliate = $customers_affiliate_table->newEntity($data_save_customers_affiliate);
            }
        }

        // thay doi thong tin partner cua customer
        $customer = $customers_table->patchEntity($customer_info, $data_save_customer_info, ['validate' => false]);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            // thay doi trang thai request
            $save_affiliate_request = $table->save($affiliate_request);
            if (empty($save_affiliate_request)){
                throw new Exception();
            }

            // thay doi trang thai partner cua customer
            if (!empty($customer)) {

                $save_customer = $customers_table->save($customer);
                if (empty($save_customer)){
                    throw new Exception();
                }

            }

            // luu thong tin tai khoan ngan hang cua khach hang
            if (!empty($customers_bank)) {

                $save_bank = $customers_bank_table->save($customers_bank);
                if (empty($save_bank->id)){
                    throw new Exception();
                }

            }

            // luu thong tin tai khoan vao bang customer_affiliate
            if (!empty($customers_affiliate)) {

                $save_affiliate = $customers_affiliate_table->save($customers_affiliate);
                if (empty($save_affiliate->id)){
                    throw new Exception();
                }

            }

            $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'cap_nhat_thanh_cong')]);

        }catch (Exception $e) {
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function delete()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        $ids = !empty($data['ids']) ? $data['ids'] : [];

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        if (empty($ids) || !is_array($ids)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('CustomersAffiliateRequest');
        $customers_table = TableRegistry::get('Customers');
        $customers_bank_table = TableRegistry::get('CustomersBank');

        // lay thong tin request
        $affiliate_request_info = $table->find()->where(['id IN' => $ids])->first();

        if (empty($affiliate_request_info)) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
        }

        // thong tin khach hang
        $customer_id = !empty($affiliate_request_info['customer_id']) ? $affiliate_request_info['customer_id'] : null;
        $customer_info = $customers_table->find()->where([
            'id' => $customer_id,
            'deleted' => 0
        ])->first();

        if (empty($customer_info)) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_khach_hang')]);
        }

        $is_partner_affiliate = isset($customer_info['is_partner_affiliate']) ? intval($customer_info['is_partner_affiliate']) : 0;

        // thay doi trang thai partner cua customer
        $customer = [];
        if (!empty($is_partner_affiliate) && $is_partner_affiliate != 1) {
            $customer = $customers_table->patchEntity($customer_info, ['is_partner_affiliate' => 0], ['validate' => false]);
        }

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            // xoa request
            $clear_request = $table->deleteAll(['id IN' => $ids]);

            // thay doi trang thai partner cua customer
            if (!empty($customer)) {
                $save_customer = $customers_table->save($customer);

                if (empty($save_customer)){
                    throw new Exception();
                }
            }

            $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'cap_nhat_thanh_cong')]);

        }catch (Exception $e) {
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }
}