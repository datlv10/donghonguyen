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

class CustomerPointController extends AppController {

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
            '/assets/js/pages/list_customer_point.js'
        ];

        $this->set('path_menu', 'customers_point');
        $this->set('title_for_layout', __d('admin', 'diem_khach_hang'));
    }

    public function listJson()
    {
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $table = TableRegistry::get('CustomersPoint');
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

        try {
            $customers_point = $this->paginate($table->queryListCustomersPoint($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();

        } catch (Exception $e) {
            $page = 1;
            $customers_point = $this->paginate($table->queryListCustomersPoint($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        }

        $pagination_info = !empty($this->request->getAttribute('paging')['CustomersPoint']) ? $this->request->getAttribute('paging')['CustomersPoint'] : [];
        $meta_info = $utilities->formatPaginationInfo($pagination_info);

        $result = [];
        if(!empty($customers_point)){
            foreach ($customers_point as $key => $customer_p) {
                $result[] = $table->formatDataCustomerPointDetail($customer_p);
            }
        }

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $result, 
            META => $meta_info
        ]);
    }

    public function detail($id = null)
    {
        $table = TableRegistry::get('CustomersPoint');

        $customer_point = $table->getDetailCustomerPoint($id);
        $customer_point = $table->formatDataCustomerPointDetail($customer_point);
        if(empty($customer_point)){
            $this->showErrorPage();
        }

        $this->css_page = [
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.css'
        ];

        $this->js_page = [
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.js',
            '/assets/js/pages/detail_point_history.js'
        ];

        $customers_point_history = TableRegistry::get('CustomersPointHistory')->find()->contain(['Users'])->where([
            'CustomersPointHistory.customer_id' => $customer_point['customer_id'],
            'Users.deleted' => 0
        ])->select([
            'CustomersPointHistory.id', 'CustomersPointHistory.customer_id', 'CustomersPointHistory.point', 'CustomersPointHistory.point_type', 'CustomersPointHistory.action', 'CustomersPointHistory.action_type', 'CustomersPointHistory.staff_id', 'CustomersPointHistory.note', 'CustomersPointHistory.status', 'CustomersPointHistory.created', 'CustomersPointHistory.updated', 'Users.full_name'
        ])->order('CustomersPointHistory.id DESC')->toArray();

        $this->set('id', $id);
        $this->set('customer_point', $customer_point);
        $this->set('customers_point_history', $customers_point_history);
        $this->set('path_menu', 'customers_point');
        $this->set('title_for_layout', __d('admin', 'chi_tiet_diem_khach_hang'));
    }

    public function detailListPointHistory($customer_id = null)
    {
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        if (empty($customer_id)) {
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_khach_hang')]);
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
        $params[FILTER]['customer_id'] = $customer_id;

        // params sort         
        $params[SORT] = !empty($data[SORT]) ? $data[SORT] : [];

        // page and limit
        $page = !empty($data[PAGINATION][PAGE]) ? intval($data[PAGINATION][PAGE]) : 1;
        $limit = !empty($data[PAGINATION][PERPAGE]) ? intval($data[PAGINATION][PERPAGE]) : PAGINATION_LIMIT_ADMIN;

        // sort 
        $sort_field = !empty($params[SORT][FIELD]) ? $params[SORT][FIELD] : null;
        $sort_type = !empty($params[SORT][SORT]) ? $params[SORT][SORT] : null;

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
}