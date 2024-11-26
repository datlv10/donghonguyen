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

class CustomerPointHistoryController extends AppController {

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
            '/assets/js/pages/list_customer_point_history.js'
        ];

        $this->set('path_menu', 'customers_point_history');
        $this->set('title_for_layout', __d('admin', 'lich_su_su_dung_diem'));
    }

    public function listJson()
    {
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $table = TableRegistry::get('CustomersPointHistory');
        $utilities = $this->loadComponent('Utilities');

        $data = $params = $brands = [];

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

        $params['get_customer'] = true;
        
        try {
            $customers_point_history = $this->paginate($table->queryListCustomerPointHistory($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();

        } catch (Exception $e) {
            $page = 1;
            $customers_point_history = $this->paginate($table->queryListCustomerPointHistory($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        }
        
        $result = [];
        if(!empty($customers_point_history)){
            foreach ($customers_point_history as $k => $history) {
                $result[] = $table->formatDataPointHistoryDetail($history);
            }
        }

        $pagination_info = !empty($this->request->getAttribute('paging')['CustomersPointHistory']) ? $this->request->getAttribute('paging')['CustomersPointHistory'] : [];
        $meta_info = $utilities->formatPaginationInfo($pagination_info);

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $result, 
            META => $meta_info
        ]);
    }

    public function add() 
    {
        $this->css_page = [
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.css'
        ];

        $this->js_page = [
            '/assets/plugins/custom/tinymce6/tinymce.min.js',
            '/assets/js/pages/customer_point_history_add.js',
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.js'
        ];

        $this->set('path_menu', 'customers_point_history');
        $this->set('title_for_layout', __d('admin', 'dieu_chinh_diem'));
    }

    public function save($id = null) 
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();  

        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        if (empty($data['customer_id'])) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_khach_hang')]);
        }

        $data['action_type'] = OTHER;

        $result = $this->loadComponent('Admin.CustomersPoint')->saveCustomerPointHistory($data);
        $this->responseJson($result);
    }

    public function detail($id = null)
    {
        $table = TableRegistry::get('Customers');

        $customer = $table->getDetailCustomerPoint($id);

        $customer = $table->formatDataCustomerDetail($customer);
        if(empty($customer)){
            $this->showErrorPage();
        }

        $orders = TableRegistry::get('Orders')->find()->contain(['OrdersContact'])
        ->where([
            'OrdersContact.customer_id' => $id,
            'Orders.type' => ORDER,
            'Orders.deleted' => 0
        ])->select([
            'Orders.id', 'Orders.created', 'Orders.code', 'Orders.note', 'Orders.total', 'Orders.status'
        ])->toArray();

        $this->set('id', $id);
        $this->set('customer', $customer);
        $this->set('orders', $orders);
        $this->set('title_for_layout', __d('admin', 'chi_tiet_khach_hang'));
    }

    public function changeStatus()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        $ids = !empty($data['ids']) ? $data['ids'] : [];
        $status = !empty($data['status']) ? 1 : 0;

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        if (empty($ids) || !is_array($ids)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $customers_table = TableRegistry::get('Customers');
        try{
            $customers_table->updateAll(
                [  
                    'status' => $status
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
}