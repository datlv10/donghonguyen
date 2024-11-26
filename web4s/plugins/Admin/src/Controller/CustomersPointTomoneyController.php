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

class CustomersPointTomoneyController extends AppController {

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
            '/assets/js/pages/list_customer_tomoney.js'
        ];

        $this->set('path_menu', 'customer_tomoney');
        $this->set('title_for_layout', __d('admin', 'yeu_cau_rut_tien'));
    }

    public function listJson()
    {

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $table = TableRegistry::get('CustomersPointTomoney');
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
        $params['get_customer'] = true;
        $params['get_bank'] = true;

        // params sort         
        $params[SORT] = !empty($data[SORT]) ? $data[SORT] : [];

        // page and limit
        $page = !empty($data[PAGINATION][PAGE]) ? intval($data[PAGINATION][PAGE]) : 1;
        $limit = !empty($data[PAGINATION][PERPAGE]) ? intval($data[PAGINATION][PERPAGE]) : PAGINATION_LIMIT_ADMIN;

        // sort 
        $sort_field = !empty($params[SORT][FIELD]) ? $params[SORT][FIELD] : null;
        $sort_type = !empty($params[SORT][SORT]) ? $params[SORT][SORT] : null;

        try {
            $customers_point_tomoney = $this->paginate($table->queryListPointTomoney($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();

        } catch (Exception $e) {
            $page = 1;
            $customers_point_tomoney = $this->paginate($table->queryListPointTomoney($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        }

        $pagination_info = !empty($this->request->getAttribute('paging')['CustomersPointTomoney']) ? $this->request->getAttribute('paging')['CustomersPointTomoney'] : [];
        $meta_info = $utilities->formatPaginationInfo($pagination_info);

        $result = [];
        if(!empty($customers_point_tomoney)){
            foreach ($customers_point_tomoney as $key => $point_tomoney) {
                $result[] = $table->formatDataPointTomoneyDetail($point_tomoney);
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
        $ids = !empty($data['ids']) ? $data['ids'] : [];
        $status = !empty($data['status']) ? $data['status'] : 0;

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        if (empty($ids) || !is_array($ids)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('CustomersPointTomoney');
        $customers_table = TableRegistry::get('Customers');
        $customers_point_history_table = TableRegistry::get('CustomersPointHistory');

        // lay thong tin request
        $point_tomoney_info = $table->find()->where(['id IN' => $ids])->first();

        if (empty($point_tomoney_info)) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
        }

        // check nếu trạng thái của yêu cầu đã thành công thì dừng lại gửi thông báo
        if (!empty($point_tomoney_info['status']) && $point_tomoney_info['status'] == 1) {
            $this->responseJson([MESSAGE => __d('admin', 'yeu_cau_rut_tien_nay_da_duoc_thuc_hien')]);
        }

        // thong tin khach hang
        $customer_id = !empty($point_tomoney_info['customer_id']) ? $point_tomoney_info['customer_id'] : null;
        $customer_info = $customers_table->find()->where([
            'id' => $customer_id,
            'deleted' => 0
        ])->first();

        if (empty($customer_info)) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_khach_hang')]);
        }

        // cập nhật lịch sử điểm cho khách hàng
        if (!empty($status) && $status == 1) {
            $data_point_history = [
                'customer_id' => $customer_id,
                'point' => !empty($point_tomoney_info['point']) ? $point_tomoney_info['point'] : 0,
                'point_type' => 1, // 1 -> điểm mặc định
                'action' => 0, // 0 -> trừ điểm
                'action_type' => WITHDRAW,
                'status' => 1
            ];
        }

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            // cập nhật lịch sử điểm cho khách hàng
            if (!empty($data_point_history) && $status == 1) {
                $update_point = $this->loadComponent('Admin.CustomersPoint')->saveCustomerPointHistory($data_point_history);

                if (empty($update_point[CODE]) || (!empty($update_point[CODE]) && $update_point[CODE] != SUCCESS)) {
                    $this->responseJson($update_point);
                }
            }

            // thay doi trang thai request
            $table->updateAll(
                [  
                    'status' => $status,
                    'time_confirm' => time()
                ],
                [  
                    'id IN' => $ids
                ]
            );

            $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'cap_nhat_thanh_cong')]);

        }catch (Exception $e) {
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function changeNote()
    {
        $this->layout = false;
        $this->autoRender = false;

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        $id = !empty($data['id']) ? intval($data['id']) : null;
        $value = !empty($data['value']) ? $data['value'] : null;

        // validate data
        if (empty($id)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('CustomersPointTomoney');
        $customers_point_tomoney = $table->get($id);
        $customers_point_tomoney = $table->patchEntity($customers_point_tomoney, [
            'note_admin' => $value
        ]);

        try{
            // save data
            $save = $table->save($customers_point_tomoney);

            if (empty($save->id)){
                throw new Exception();
            }
            $this->responseJson([CODE => SUCCESS, DATA => ['id' => $save->id]]);

        }catch (Exception $e) {
            $this->responseJson([MESSAGE => $e->getMessage()]);
        }
    }

    public function delete()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        $id = !empty($data['ids'][0]) ? $data['ids'][0] : null;

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        if (empty($id)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('CustomersPointTomoney');

        // lay thong tin request
        $customers_point_tomoney = $table->find()->where(['id' => $id])->first();

        if (empty($customers_point_tomoney)) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
        }

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            // xoa request
            $delete = $table->delete($customers_point_tomoney);
            if (empty($delete)){
                throw new Exception();
            }

            $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'cap_nhat_thanh_cong')]);

        }catch (Exception $e) {
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function listBankJson($id = null)
    {   
        $this->viewBuilder()->enableAutoLayout(false);
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $table = TableRegistry::get('CustomersBank');
        $utilities = $this->loadComponent('Utilities');
      
        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        
        // params query
        $params[QUERY] = !empty($data[QUERY]) ? $data[QUERY] : [];

        // params filter
        $params[FILTER] = !empty($data[DATA_FILTER]) ? $data[DATA_FILTER] : [];
        if(!empty($params[QUERY])){
            $params[FILTER] = array_merge($params[FILTER], $params[QUERY]);
        }
        $params[FILTER]['customer_id'] = $id;

        // params sort
        $params[SORT] = !empty($data[SORT]) ? $data[SORT] : [];

        // page and limit
        $page = !empty($data[PAGINATION][PAGE]) ? intval($data[PAGINATION][PAGE]) : 1;
        $limit = !empty($data[PAGINATION][PERPAGE]) ? intval($data[PAGINATION][PERPAGE]) : PAGINATION_LIMIT_ADMIN;

        // sort 
        $sort_field = !empty($params[SORT][FIELD]) ? $params[SORT][FIELD] : null;
        $sort_type = !empty($params[SORT][SORT]) ? $params[SORT][SORT] : null;
      
        try {
            $list_banks = $this->paginate($table->queryListCustomersBank($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        } catch (Exception $e) {
            $page = 1;
            $list_banks = $this->paginate($table->queryListCustomersBank($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        }

        $pagination_info = !empty($this->request->getAttribute('paging')['CustomersBank']) ? $this->request->getAttribute('paging')['CustomersBank'] : [];
        $meta_info = $utilities->formatPaginationInfo($pagination_info);

        

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $list_banks, 
            META => $meta_info
        ]);
    }

    public function save() 
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();  

        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $customer_id = !empty($data['customer_id']) ? intval($data['customer_id']) : null;
        $bank_id = !empty($data['bank_id']) ? intval($data['bank_id']) : null;
        $point = !empty($data['point']) ? intval(str_replace(',', '', $data['point'])) : null;
        $note = !empty($data['note']) ? trim($data['note']) : null;
        $money = !empty($data['money']) ? floatval($data['money']) : null;
        $type = isset($data['type']) ? intval($data['type']) : null;

        if (empty($data['customer_id'])) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_khach_hang')]);
        }

        if(empty($bank_id)){
            $this->responseJson([MESSAGE => __d('template', 'vui_long_chon_ngan_hang')]);
        }

        if(empty($point)){
            $this->responseJson([MESSAGE => __d('template', 'vui_long_nhap_so_diem_de_rut')]);
        }

        $customer_info = TableRegistry::get('Customers')->getDetailCustomer($customer_id, [
            'get_point' => true
        ]);
        $customer_info = TableRegistry::get('Customers')->formatDataCustomerDetail($customer_info);
        $poin_max = !empty($customer_info['point']) ? intval($customer_info['point']) : 0;

        if($point > $poin_max) {
            $this->responseJson([MESSAGE => __d('template', 'so_diem_khong_du_de_rut')]);
        }

        $settings = TableRegistry::get('Settings')->getSettingWebsite();
        $rule_point_to_money = !empty($settings['point']['point_to_money']) ? intval($settings['point']['point_to_money']) : null;
        if(empty($rule_point_to_money)){
            $this->responseJson([MESSAGE => __d('template', 'chua_thiet_lap_ti_le_quy_doi')]);
        }

        $table = TableRegistry::get('CustomersPointTomoney');
        $data_save = [
            'customer_id' => $customer_id,
            'bank_id' => $bank_id,
            'point' => $point,
            'money' => $money,
            'type' => $type,
            'note' => $note,
            'status' => 2
        ];

        $point_tomoney = $table->newEntity($data_save);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $save = $table->save($point_tomoney);
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
}