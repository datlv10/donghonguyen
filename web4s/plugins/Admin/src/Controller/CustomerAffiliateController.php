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

class CustomerAffiliateController extends AppController {

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
            '/assets/js/pages/list_affiliate.js'
        ];

        $this->set('path_menu', 'affiliate');
        $this->set('title_for_layout', __d('admin', 'danh_sach_doi_tac'));
    }

    public function listJson()
    {

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $table = TableRegistry::get('CustomersAffiliate');
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
            $affiliates = $this->paginate($table->queryListAffiliate($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();

        } catch (Exception $e) {
            $page = 1;
            $affiliates = $this->paginate($table->queryListAffiliate($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        }

        $pagination_info = !empty($this->request->getAttribute('paging')['CustomersAffiliate']) ? $this->request->getAttribute('paging')['CustomersAffiliate'] : [];
        $meta_info = $utilities->formatPaginationInfo($pagination_info);

        $result = [];
        if(!empty($affiliates)){
            foreach ($affiliates as $key => $affiliate) {
                $result[] = $table->formatDataAffiliateDetail($affiliate);
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
        $table = TableRegistry::get('Customers');

        $customer = $table->getDetailCustomer($id, [
            'get_bank' => true
        ]);
        
        $customer = $table->formatDataCustomerDetail($customer);
        if(empty($customer)){
            $this->showErrorPage();
        }

        // thông tin tk ngân hàng
        $bank_info = !empty($customer['bank']) ? $customer['bank'] : [];

        $customer_affiliate_table = TableRegistry::get('CustomersAffiliate');
        $affiliate = $customer_affiliate_table->queryListAffiliate([
            FILTER => [
                'customer_id' => $id
            ]
        ])->first();

        $customer_affiliate = $customer_affiliate_table->formatDataAffiliateDetail($affiliate);

        // lấy thông tin thứ hạng của đối tác
        $settings = TableRegistry::get('Settings')->getSettingWebsite();
        $setting_affiliate = !empty($settings['affiliate']) ? $settings['affiliate'] : [];
        $commissions = !empty($setting_affiliate['commissions']) ? json_decode($setting_affiliate['commissions'], true) : [];
        $commissions = Hash::combine($commissions, '{n}.key', '{n}');

        $level_partner = isset($customer['level_partner_affiliate']) ? $customer['level_partner_affiliate'] : 0;
        $level_partner_info = !empty($commissions[$level_partner]) ? $commissions[$level_partner] : [];

        $this->js_page = [
            '/assets/js/pages/customer_affiliate_detail.js'
        ];

        $this->set('path_menu', 'affiliate');
        $this->set('id', $id);
        $this->set('customer', $customer);
        $this->set('bank_info', $bank_info);
        $this->set('customer_affiliate', $customer_affiliate);
        $this->set('level_partner_info', $level_partner_info);
        $this->set('title_for_layout', __d('admin', 'thong_tin_doi_tac'));
    }

    public function listOrderJson($customer_id = null)
    {
        $customers_table = TableRegistry::get('Customers');
        $customer = $customers_table->getDetailCustomer($customer_id);

        if(empty($customer)){
            $this->showErrorPage();
        }

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $table = TableRegistry::get('CustomersAffiliateOrder');
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
            $affiliates_order = $this->paginate($table->queryListAffiliateOrder($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();

        } catch (Exception $e) {
            $page = 1;
            $affiliates_order = $this->paginate($table->queryListAffiliateOrder($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        }

        $pagination_info = !empty($this->request->getAttribute('paging')['CustomersAffiliateOrder']) ? $this->request->getAttribute('paging')['CustomersAffiliateOrder'] : [];
        $meta_info = $utilities->formatPaginationInfo($pagination_info);

        $result = [];
        if(!empty($affiliates_order)){
            foreach ($affiliates_order as $key => $affiliate) {
                $result[] = $table->formatDataAffiliateOrderDetail($affiliate);
            }
        }

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $result, 
            META => $meta_info
        ]);
    }

    public function loadStatisticDashboard()
    {
        $this->viewBuilder()->enableAutoLayout(false);

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        $customer_id = !empty($data['customer_id']) ? intval($data['customer_id']) : null;
        $customers_table = TableRegistry::get('Customers');
        $customer = $customers_table->getDetailCustomer($customer_id);

        if(empty($customer)){
            $this->responseJson([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_khach_hang')]);
        }

        $affiliate_order_table = TableRegistry::get('CustomersAffiliateOrder');
        $point_history_table = TableRegistry::get('CustomersPointHistory');

        $create_from = $create_to = null;
        $filter_date = !empty($data['filter_date']) ? $data['filter_date'] : 'thang_'.date('n');

        switch ($filter_date) {
            case 'thang_1':
                $create_from = strtotime(date('Y-01-01'));
                $create_to = strtotime(date('Y-02-01'));
                break;
            
            case 'thang_2':
                $create_from = strtotime(date('Y-02-01'));
                $create_to = strtotime(date('Y-03-01'));
                break;

            case 'thang_3':
                $create_from = strtotime(date('Y-03-01'));
                $create_to = strtotime(date('Y-04-01'));
                break;

            case 'thang_4':
                $create_from = strtotime(date('Y-04-01'));
                $create_to = strtotime(date('Y-05-01'));
                break;

            case 'thang_5':
                $create_from = strtotime(date('Y-05-01'));
                $create_to = strtotime(date('Y-06-01'));
                break;

            case 'thang_6':
                $create_from = strtotime(date('Y-06-01'));
                $create_to = strtotime(date('Y-07-01'));
                break;

            case 'thang_7':
                $create_from = strtotime(date('Y-07-01'));
                $create_to = strtotime(date('Y-08-01'));
                break;

            case 'thang_8':
                $create_from = strtotime(date('Y-08-01'));
                $create_to = strtotime(date('Y-09-01'));
                break;

            case 'thang_9':
                $create_from = strtotime(date('Y-09-01'));
                $create_to = strtotime(date('Y-10-01'));
                break;

            case 'thang_10':
                $create_from = strtotime(date('Y-10-01'));
                $create_to = strtotime(date('Y-11-01'));
                break;

            case 'thang_11':
                $create_from = strtotime(date('Y-11-01'));
                $create_to = strtotime(date('Y-12-01'));
                break;

            case 'thang_12':
                $create_from = strtotime(date('Y-11-01'));
                $create_to = strtotime(date('Y-01-01') ." +1 year");
                break;

            case 'year':
                $create_from = strtotime(date('Y-01-01'));
                break;
        }

        // tổng đơn hàng
        $total_order = $affiliate_order_table->countNumberOrder($customer_id, [
            'create_from' => $create_from,
            'create_to' => $create_to
        ]);

        // đơn hàng hủy
        $failed_order = $affiliate_order_table->countNumberOrder($customer_id, [
            'get_failed_order' => true,
            'create_from' => $create_from,
            'create_to' => $create_to
        ]);

        // điểm hoa hồng tạm tính của đối tác
        $profit_point = $point_history_table->sumAffiliatePointOfCustomer($customer_id, [
            FILTER => [
                'create_from' => $create_from,
                'create_to' => $create_to
            ]
        ]);
        $profit_point = !empty($profit_point) ? intval($profit_point) : 0;

        // điểm hoa hồng đối tác khi đơn hủy
        $profit_faild_point = $point_history_table->sumAffiliatePointOfCustomer($customer_id, [
            FILTER => [
                'action' => 0,
                'create_from' => $create_from,
                'create_to' => $create_to
            ]
        ]);
        $profit_faild_point = !empty($profit_faild_point) ? intval($profit_faild_point) : 0;

        // hoa hồng thực tế mà đối tác nhận được
        $profit_success_point = intval($profit_point) - intval($profit_faild_point);

        if (!empty($profit_success_point) && $profit_success_point < 0) {
            $profit_success_point = 0;
        }

        $settings = TableRegistry::get('Settings')->getSettingWebsite();
        $point_setting = !empty($settings['point']) ? $settings['point'] : [];
        $point_to_money = !empty($point_setting['point_to_money']) ? floatval($point_setting['point_to_money']) : 0;

        $profit_point_to_money = $point_to_money * $profit_point;
        $profit_success_point_to_money = $point_to_money * $profit_success_point;

        $affiliate = [
            'total_order' => !empty($total_order) ? $total_order : 0,
            'failed_order' => !empty($failed_order) ? $failed_order : 0,
            'profit_point' => $profit_point,
            'profit_point_to_money' => $profit_point_to_money,
            'profit_success_point' => $profit_success_point,
            'profit_success_point_to_money' => $profit_success_point_to_money
        ];

        $this->set('affiliate', $affiliate);
        $this->set('filter_date', $filter_date);
        
        $this->render('load_statistic_dashboard');
    }

    public function statistical()
    {
        $this->js_page = [
            '/assets/js/pages/customer_affiliate_statistical.js'
        ];
        $this->set('path_menu', 'affiliate_statistical');
        $this->set('title_for_layout', __d('admin', 'thong_ke_doi_tac'));
    }

    public function statisticsOrder()
    {
        $this->viewBuilder()->enableAutoLayout(false);
        if (!$this->getRequest()->is('post')) die;

        $data = $this->getRequest()->getData();

        $affiliate_order_table = TableRegistry::get('CustomersAffiliateOrder');
        $point_history_table = TableRegistry::get('CustomersPointHistory');
        $settings = TableRegistry::get('Settings')->getSettingWebsite();

        $create_from = $create_to = null;
        $filter_date = !empty($data['filter_date']) ? $data['filter_date'] : 'thang_'.date('n');

        switch ($filter_date) {
            case 'thang_1':
                $create_from = strtotime(date('Y-01-01'));
                $create_to = strtotime(date('Y-02-01'));
                break;
            
            case 'thang_2':
                $create_from = strtotime(date('Y-02-01'));
                $create_to = strtotime(date('Y-03-01'));
                break;

            case 'thang_3':
                $create_from = strtotime(date('Y-03-01'));
                $create_to = strtotime(date('Y-04-01'));
                break;

            case 'thang_4':
                $create_from = strtotime(date('Y-04-01'));
                $create_to = strtotime(date('Y-05-01'));
                break;

            case 'thang_5':
                $create_from = strtotime(date('Y-05-01'));
                $create_to = strtotime(date('Y-06-01'));
                break;

            case 'thang_6':
                $create_from = strtotime(date('Y-06-01'));
                $create_to = strtotime(date('Y-07-01'));
                break;

            case 'thang_7':
                $create_from = strtotime(date('Y-07-01'));
                $create_to = strtotime(date('Y-08-01'));
                break;

            case 'thang_8':
                $create_from = strtotime(date('Y-08-01'));
                $create_to = strtotime(date('Y-09-01'));
                break;

            case 'thang_9':
                $create_from = strtotime(date('Y-09-01'));
                $create_to = strtotime(date('Y-10-01'));
                break;

            case 'thang_10':
                $create_from = strtotime(date('Y-10-01'));
                $create_to = strtotime(date('Y-11-01'));
                break;

            case 'thang_11':
                $create_from = strtotime(date('Y-11-01'));
                $create_to = strtotime(date('Y-12-01'));
                break;

            case 'thang_12':
                $create_from = strtotime(date('Y-11-01'));
                $create_to = strtotime(date('Y-01-01') ." +1 year");
                break;

            case 'year':
                $create_from = strtotime(date('Y-01-01'));
                break;
        }

        // tổng đơn hàng
        $total_order = $affiliate_order_table->countNumberOrder(null, [
            'create_from' => $create_from,
            'create_to' => $create_to
        ]);

        // đơn hàng hủy
        $failed_order = $affiliate_order_table->countNumberOrder(null, [
            'get_failed_order' => true,
            'create_from' => $create_from,
            'create_to' => $create_to
        ]);

        // điểm hoa hồng tạm tính của đối tác
        $profit_point = $point_history_table->sumAffiliatePointOfCustomer(null, [
            'get_all_partner' => true,
            FILTER => [
                'create_from' => $create_from,
                'create_to' => $create_to
            ]
        ]);
        $profit_point = !empty($profit_point) ? intval($profit_point) : 0;

        // điểm hoa hồng đối tác khi đơn hủy
        $profit_faild_point = $point_history_table->sumAffiliatePointOfCustomer(null, [
            'get_all_partner' => true,
            FILTER => [
                'action' => 0,
                'create_from' => $create_from,
                'create_to' => $create_to
            ]
        ]);
        $profit_faild_point = !empty($profit_faild_point) ? intval($profit_faild_point) : 0;

        // hoa hồng thực tế mà đối tác nhận được
        $profit_success_point = intval($profit_point) - intval($profit_faild_point);

        if (!empty($profit_success_point) && $profit_success_point < 0) {
            $profit_success_point = 0;
        }

        $settings = TableRegistry::get('Settings')->getSettingWebsite();
        $point_setting = !empty($settings['point']) ? $settings['point'] : [];
        $point_to_money = !empty($point_setting['point_to_money']) ? floatval($point_setting['point_to_money']) : 0;


        $profit_point_to_money = $point_to_money * $profit_point;
        $profit_success_point_to_money = $point_to_money * $profit_success_point;

        $this->set('total_order', $total_order);
        $this->set('failed_order', $failed_order);
        $this->set('profit_point', $profit_point);
        $this->set('profit_point_to_money', $profit_point_to_money);
        $this->set('profit_success_point', $profit_success_point);
        $this->set('profit_success_point_to_money', $profit_success_point_to_money);
        $this->set('filter_date', $filter_date);
        $this->render('order_statistics');
    }

    public function chartOrder()
    {
        $this->viewBuilder()->enableAutoLayout(false);
        if (!$this->getRequest()->is('post')) die;        

        $data = $this->getRequest()->getData();
        $type = !empty($data['type']) ? $data['type'] : 'month';

        $affiliate_order_table = TableRegistry::get('CustomersAffiliateOrder');     

        $this_month = date('m');
        $previous_month = date('m', strtotime('last month'));
        $last_day = intval(date('t'));
        $last_day_previous_month = intval(date('t', strtotime('last month')));
        $max_day = $last_day > $last_day_previous_month ? $last_day : $last_day_previous_month;
        
        $labels = $point_this_month = $money_this_month = $point_previous_month = $money_previous_month = [];
        for ($i = 1; $i <= $max_day; $i++) {

            $labels[] = $i;
            $day = str_pad(strval($i), 2, '0', STR_PAD_LEFT);

            // get data for this month
            if($i <= $last_day){
                
                $start_day = strtotime(date("Y-$this_month-$day 00:00:00"));
                $end_day = strtotime(date("Y-$this_month-$day 23:59:59"));

                $query = $affiliate_order_table->find()->contain(['Customers', 'Orders'])->where([
                    'Customers.deleted' => 0,
                    'Customers.is_partner_affiliate' => 1,
                    'Orders.status NOT IN' => [DRAFT, CANCEL],
                    'Orders.created >=' => $start_day,
                    'Orders.created <=' => $end_day
                ])->select([
                    'point' => $affiliate_order_table->find()->func()->sum('CustomersAffiliateOrder.profit_point'),
                    'money' => $affiliate_order_table->find()->func()->sum('CustomersAffiliateOrder.profit_money')
                ])->first();

                $point_this_month[] = !empty($query['point']) ? intval($query['point']) : 0;
                $money_this_month[] = !empty($query['money']) ? floatval($query['money']) : 0;
            }
            
            // get data for last month
            if($i <= $last_day_previous_month){

                $start_day = strtotime(date("Y-$previous_month-$day 00:00:00"));
                $end_day = strtotime(date("Y-$previous_month-$day 23:59:59"));

                $query = $affiliate_order_table->find()->contain(['Customers', 'Orders'])->where([
                    'Customers.deleted' => 0,
                    'Customers.is_partner_affiliate' => 1,
                    'Orders.status NOT IN' => [DRAFT, CANCEL],
                    'Orders.created >=' => $start_day,
                    'Orders.created <=' => $end_day
                ])->select([
                    'point' => $affiliate_order_table->find()->func()->sum('CustomersAffiliateOrder.profit_point'),
                    'money' => $affiliate_order_table->find()->func()->sum('CustomersAffiliateOrder.profit_money')
                ])->first();

                $point_previous_month[] = !empty($query['point']) ? intval($query['point']) : 0;
                $money_previous_month[] = !empty($query['money']) ? floatval($query['money']) : 0;
            }
        }

        $chart_data = [
            'labels' => $labels,
            'point_this_month' => $point_this_month,
            'point_previous_month' => $point_previous_month,
            'money_this_month' => $money_this_month,
            'money_previous_month' => $money_previous_month
        ];

        $this->set('chart_data', $chart_data);

        $this->render('order_chart');
    }

    public function topPartner()
    {
        $this->viewBuilder()->enableAutoLayout(false);
        if (!$this->getRequest()->is('post')) die;

        $top_partner = TableRegistry::get('CustomersAffiliate')->find()->contain(['Customers'])->where([
            'Customers.deleted' => 0,
            'Customers.is_partner_affiliate' => 1
        ])->limit(3)->order('CustomersAffiliate.total_point DESC, CustomersAffiliate.number_referral DESC, CustomersAffiliate.id ASC')->toArray();

        $this->set('top_partner', $top_partner);
        $this->render('top_partner');
    }

    public function newPartner()
    {
        $this->viewBuilder()->enableAutoLayout(false);
        if (!$this->getRequest()->is('post')) die;

        $new_partner = TableRegistry::get('CustomersAffiliateRequest')->find()->contain(['Customers'])->where([
            'Customers.deleted' => 0,
            'Customers.is_partner_affiliate' => 1,
            'CustomersAffiliateRequest.status' => 1
        ])->limit(5)->order('CustomersAffiliateRequest.created DESC')->toArray();

        $this->set('new_partner', $new_partner);
        $this->render('new_partner');
    }
}