<?php

namespace Admin\Controller;

use Admin\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Core\Exception\Exception;
use Cake\Utility\Hash;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function report()
    {
        $this->js_page = [
            '/assets/js/pages/report_dashboard.js'
        ];

        $this->set('path_menu', 'report');
        $this->set('title_for_layout', __d('admin', 'tong_quan'));
    }

    public function loadDashboardRevenue()
    {
        $this->viewBuilder()->enableAutoLayout(false);

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $data = !empty($this->getRequest()->getData()) ? $this->getRequest()->getData() : [];
        
        $table = TableRegistry::get('Orders');

        $type_filter = !empty($data['type_filter']) ? $data['type_filter'] : 'all';
        if(!empty($type_filter)) {
            $data['create_from'] = $this->getFilterDate($type_filter);
        }

        $report_order = $table->reportOrder($data)->toArray();
        $report_order = $this->getReportOrderDoneAndCancel($report_order, $data, 'revenue');

        $report = $table->formatReportOrder($report_order);

        $this->set('report', $report);
        $this->render('element_dashboard_revenue');
    }

    public function loadDashboardSource()
    {
        $this->viewBuilder()->enableAutoLayout(false);

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $table = TableRegistry::get('Orders');

        $data = !empty($this->getRequest()->getData()) ? $this->getRequest()->getData() : [];
        $type_filter = !empty($data['type_filter']) ? $data['type_filter'] : 'all';
        if(!empty($type_filter)) {
            $data['create_from'] = $this->getFilterDate($type_filter);
        }

        $report_order = $table->reportOrder($data,'source')->toArray();
        $report = $table->formatReportOrder($report_order);

        $result = [];
        if(!empty($report['item_report'])) {
            foreach ($report['item_report'] as $k => $item_report) {
                $result[$k] = [
                    'label' => !empty($item_report['source']) ? $item_report['source'] : '',
                    'value' => !empty($item_report['total']) ? $item_report['total'] : 0
                ];
            }
        }
        
        $this->set('report', $result);
        $this->render('element_dashboard_source');
    }

    public function loadDashboardCity()
    {
        $this->viewBuilder()->enableAutoLayout(false);

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }
        $data = !empty($this->getRequest()->getData()) ? $this->getRequest()->getData() : [];
        $table = TableRegistry::get('Orders');

        $type_filter = !empty($data['type_filter']) ? $data['type_filter'] : 'all';

        $data = [
            SORT => [
                FIELD => 'total',
                SORT => 'desc',
            ],
        ];

        if(!empty($type_filter)) {
            $data['create_from'] = $this->getFilterDate($type_filter);
        }

        $report_order = $table->reportOrder($data,'city')->limit(10)->toArray();
        $report = $table->formatReportOrder($report_order);

        $chart_data = [];

        if(!empty($report['item_report'])) {
            foreach ($report['item_report'] as $k => $item_report) {
                $chart_data['labels'][] = !empty($item_report['city_name']) ? $item_report['city_name'] : null;
                $chart_data['data'][] = !empty($item_report['total']) ? $item_report['total'] : 0;
            }
        }
            
        $this->set('chart_data', $chart_data);
        $this->render('element_dashboard_city');
    }

    public function loadDashboardProduct()
    {
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $data = !empty($this->getRequest()->getData()) ? $this->getRequest()->getData() : [];

        $table = TableRegistry::get('OrdersItem');
        $utilities = $this->loadComponent('Utilities');

        $type_filter = !empty($data[QUERY]['type_filter']) ? $data[QUERY]['type_filter'] : 'all';

        if(!empty($type_filter)) {
            $data['create_from'] = $this->getFilterDate($type_filter);
        }

        $limit = PAGINATION_LIMIT_ADMIN;
        $page = 1;

        // page and limit
        $page = !empty($data[PAGINATION][PAGE]) ? intval($data[PAGINATION][PAGE]) : 1;
        $limit = !empty($data[PAGINATION][PERPAGE]) ? intval($data[PAGINATION][PERPAGE]) : PAGINATION_LIMIT_ADMIN;

        // sort 
        $sort_field = 'quantity';
        $sort_type = 'desc';

        try {
            $reports = $this->paginate($table->reportProduct($data), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        } catch (Exception $e) {
            $reports = $$this->paginate($table->reportProduct($data), [
                'limit' => $limit,
                'page' => 1,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        }

        $reports = $this->getReportProductDoneAndCancel($reports, $data);

        $reports = $table->formatReportProduct($reports, [
            LANG => $this->lang
        ]);
        
        $pagination_info = !empty($this->request->getAttribute('paging')['OrdersItem']) ? $this->request->getAttribute('paging')['OrdersItem'] : [];
        $meta_info = $utilities->formatPaginationInfo($pagination_info);

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => !empty($reports['item_report']) ? $reports['item_report'] : [], 
            META => $meta_info
        ]);
    }

    public function loadDashboardStaff()
    {
        $this->viewBuilder()->enableAutoLayout(false);

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }
        $table = TableRegistry::get('Orders');

        $data = !empty($this->getRequest()->getData()) ? $this->getRequest()->getData() : [];
        $type_filter = !empty($data['type_filter']) ? $data['type_filter'] : 'all';
        if(!empty($type_filter)) {
            $data['create_from'] = $this->getFilterDate($type_filter);
        }

        $report = $table->reportOrder($data, 'staff')->limit(3)->toArray();
        $report = $table->formatReportOrder($report);

        $this->set('report', $report);
        $this->render('element_dashboard_staff');
    }

    public function reportRevenue()
    {
        $this->js_page = [
            '/assets/js/pages/report.js',
            '/assets/plugins/custom/amcharts/js/amcharts.js',
            '/assets/plugins/custom/amcharts/js/animate.min.js',
            '/assets/plugins/custom/amcharts/js/light.js',
            '/assets/plugins/custom/amcharts/js/serial.js',
            '/assets/plugins/custom/amcharts/js/vi.js'
        ];

        $this->set('title_for_layout', __d('admin', 'bao_cao_theo_thoi_gian'));
        $this->set('path_menu', 'report_revenue');
    }

    public function loadReportRevenue()
    {
        $this->viewBuilder()->enableAutoLayout(false);

        $data = $this->getRequest()->getData();

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $table = TableRegistry::get('Orders');
        $utilities = $this->loadComponent('Utilities');

        $sort_field = !empty($data['sort_field']) ? $data['sort_field'] : null;
        $sort_type = !empty($data['sort_type']) ? $data['sort_type'] : null;

        $data_report_revenue = $this->getReportOrder($data, 'revenue');
        $report_revenue = !empty($data_report_revenue[DATA]) ? $data_report_revenue[DATA] : [];
        $pagination = !empty($data_report_revenue[PAGINATION]) ? $data_report_revenue[PAGINATION] : [];

        $chart = $this->reportChartOrder($report_revenue);

        // loại bỏ dữ liệu về sắp xếp cho biểu đồ
        if(!empty($data['sort_field']) && $data['sort_type']) {
            unset($data['sort_field']);
            unset($data['sort_type']);

            $data_report_revenue_chart = $this->getReportOrder($data, 'revenue');
            $report_revenue_chart = !empty($data_report_revenue_chart[DATA]) ? $data_report_revenue_chart[DATA] : [];
            $chart = $this->reportChartOrder($report_revenue_chart);
        }

        $result = [
            DATA => $report_revenue,
            PAGINATION => $pagination,
            'chart' => $chart,
            SORT => [
                FIELD => $sort_field,
                SORT => $sort_type
            ]
        ];


        $this->set('report_order', $result);
        $this->render('element_report_revenue');
    }

    public function reportStaff()
    {
        $this->js_page = [
            '/assets/js/pages/report.js'
        ];

        $this->set('title_for_layout', __d('admin', 'bao_cao_theo_nhan_vien'));
        $this->set('path_menu', 'report_staff');
    }

    public function loadReportStaff()
    {
        $this->viewBuilder()->enableAutoLayout(false);

        $data = $this->getRequest()->getData();

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $table = TableRegistry::get('Orders');
        $utilities = $this->loadComponent('Utilities');

        $sort_field = !empty($data['sort_field']) ? $data['sort_field'] : null;
        $sort_type = !empty($data['sort_type']) ? $data['sort_type'] : null;

        $get_data_report = $this->getReportOrder($data, 'staff');
        $data_report = !empty($get_data_report[DATA]) ? $get_data_report[DATA] : [];
        $pagination = !empty($get_data_report[PAGINATION]) ? $get_data_report[PAGINATION] : [];

        $result = [
            DATA => $data_report,
            PAGINATION => $pagination,
            SORT => [
                FIELD => $sort_field,
                SORT => $sort_type
            ]
        ];

        $this->set('report_order', $result);
        $this->render('element_report_staff');
    }

    public function reportCity()
    {
        $this->js_page = [
            '/assets/js/pages/report.js'
        ];

        $this->set('title_for_layout', __d('admin', 'bao_cao_theo_tinh_thanh'));
        $this->set('path_menu', 'report_city');
    }


    public function loadReportCity()
    {
        $this->viewBuilder()->enableAutoLayout(false);

        $data = $this->getRequest()->getData();

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $table = TableRegistry::get('Orders');
        $utilities = $this->loadComponent('Utilities');

        $sort_field = !empty($data['sort_field']) ? $data['sort_field'] : null;
        $sort_type = !empty($data['sort_type']) ? $data['sort_type'] : null;

        $get_data_report = $this->getReportOrder($data, 'city');
        $data_report = !empty($get_data_report[DATA]) ? $get_data_report[DATA] : [];
        $pagination = !empty($get_data_report[PAGINATION]) ? $get_data_report[PAGINATION] : [];

        $result = [
            DATA => $data_report,
            PAGINATION => $pagination,
            SORT => [
                FIELD => $sort_field,
                SORT => $sort_type
            ]
        ];

        $this->set('report_order', $result);
        $this->render('element_report_city');
    }

    public function reportProduct()
    {
        $this->js_page = [
            '/assets/js/pages/report.js'
        ];

        $this->set('path_menu', 'report_product');
        $this->set('title_for_layout', __d('admin', 'bao_cao_theo_san_pham'));
    }

    public function loadReportProduct()
    {
        $this->viewBuilder()->enableAutoLayout(false);

        $data = $this->getRequest()->getData();

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $table = TableRegistry::get('OrdersItem');
        $utilities = $this->loadComponent('Utilities');

        $number_record = !empty($data[NUMBER_RECORD]) ? intval($data[NUMBER_RECORD]) : 50;
        $page = !empty($data[PAGE]) ? intval($data[PAGE]) : 1;

        $data[SORT][FIELD] = !empty($data['sort_field']) ? $data['sort_field'] : null;
        $data[SORT][SORT] = !empty($data['sort_type']) ? $data['sort_type'] : null;
        $data['lang'] = $this->lang;

        if(!empty($data['export']) && $data['export'] == 'all') {
            $number_record = 1000;
            $count_eport_product = $table->reportProduct($data, 'source')->count();

            if(!empty($count_eport_product) && $count_eport_product > $number_record){
                $page_number = ceil($count_eport_product / $number_record);
                
                if($page_number < 1)  $page_number = 1;
                $page_export = 0;
                $array_export = [];

                for ($i = 0; $i < $page_number; $i++) {
                    $page_export ++;
                    $report_product_export = $table->reportProduct($data)->limit($number_record)->page($page_export)->toArray();
                    
                    if(empty($report_product_export)) continue;

                    $data_done['status'] = DONE;
                    $product_done_export  = $table->reportProduct($data_done)->limit($number_record)->page($page_export)->toArray();
                    $number_product_done = Hash::combine($product_done_export, '{n}.product_item_id', '{n}.total_quantity');
                    $product_done_export = Hash::combine($product_done_export, '{n}.product_item_id', '{n}.total_item');

                    $data_cancel['status'] = CANCEL;
                    $product_cancel_export = $table->reportProduct($data_cancel)->limit($number_record)->page($page_export)->toArray();
                    $product_cancel_export = Hash::combine($product_cancel_export, '{n}.product_item_id', '{n}.total_item');

                    if(!empty($report_product_export)){
                        foreach ($report_product_export as $k => $report) {
                            $product_item_id = !empty($report['product_item_id']) ? $report['product_item_id'] : null;

                            $report['product_done'] = !empty($product_done_export[$product_item_id]) ? $product_done_export[$product_item_id] : 0;
                            $report['product_cancel'] = !empty($product_cancel_export[$product_item_id]) ? $product_cancel_export[$product_item_id] : 0;
                            $report['number_product_done'] = !empty($number_product_done[$product_item_id]) ? $number_product_done[$product_item_id] : 0;

                            $array_export[] = $report;
                        }

                    }
                }
                $array_export = $table->formatReportProduct($array_export, [
                    LANG => $this->lang
                ]);

                return $this->exportExcelReporProduct($array_export);
            }
        }

        try {
            $reports = $this->paginate($table->reportProduct($data), [
                'limit' => $number_record,
                'page' => $page
            ])->toArray();

           
        } catch (Exception $e) {
            $reports = $this->paginate($table->reportProduct($data), [
                'limit' => $number_record,
                'page' => 1
            ])->toArray();
        }

        $reports = $this->getReportProductDoneAndCancel($reports, $data);

        $pagination = [];
        $pagination_info = !empty($this->request->getAttribute('paging')['OrdersItem']) ? $this->request->getAttribute('paging')['OrdersItem'] : [];
        $pagination = $utilities->formatPaginationInfo($pagination_info);

        $reports = $table->formatReportProduct($reports, [
            LANG => $this->lang,
            'pagination' => $pagination
        ]);

        if(!empty($data['export'])) {
            return $this->exportExcelReporProduct($reports);
        }
        
        $result = [
            DATA => $reports,
            PAGINATION => $pagination,
            SORT => [
                FIELD => !empty($data['sort_field']) ? $data['sort_field'] : null,
                SORT => !empty($data['sort_type']) ? $data['sort_type'] : null
            ]
        ];

        $this->set('report', $result);
        $this->render('element_report_product');
    }

    public function reportSource()
    {
        $this->js_page = [
            '/assets/js/pages/report.js'
        ];

        $this->set('path_menu', 'report_source');
        $this->set('title_for_layout', __d('admin', 'bao_cao_theo_nguon_don_hang'));
    }

    public function loadReportSource()
    {
        $this->viewBuilder()->enableAutoLayout(false);

        $data = $this->getRequest()->getData();

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $table = TableRegistry::get('Orders');
        $utilities = $this->loadComponent('Utilities');

        $sort_field = !empty($data['sort_field']) ? $data['sort_field'] : null;
        $sort_type = !empty($data['sort_type']) ? $data['sort_type'] : null;

        $get_data_report = $this->getReportOrder($data, 'source');
        $data_report = !empty($get_data_report[DATA]) ? $get_data_report[DATA] : [];
        $pagination = !empty($get_data_report[PAGINATION]) ? $get_data_report[PAGINATION] : [];

        $result = [
            DATA => $data_report,
            PAGINATION => $pagination,
            SORT => [
                FIELD => $sort_field,
                SORT => $sort_type
            ]
        ];

        $this->set('report_order', $result);
        $this->render('element_report_source');
    }

    public function reportChartOrder($data = [], $sort_type = null)
    {
        $this->viewBuilder()->enableAutoLayout(false); 
        $result = [];
        if(empty($data['item_report'])) return $result;

        $order_date = array_reverse(Hash::combine($data['item_report'], '{n}.the_time', '{n}.total'));

        $end_time = $data['item_report'][0]['created'];
        $start_time = end($data['item_report'])['created'];
        
        for ( $i = $start_time; $i <= $end_time; $i = $i + 86800 ) {
            $duration_date = date('d/m/Y', $i);
            $result[] = [
                'date' => $duration_date,
                'value' => !empty($order_date[$duration_date]) ? $order_date[$duration_date] : 0
            ];
        }

        return $result;
    }
    
    public function getFilterDate($type_filter = null) 
    {
        $result = null;
        if(empty($type_filter)) return $result;

        switch ($type_filter) {
            case 'week':
                $date = date('Y-m-d');
                $weekday = strtolower(date('l'));

                switch($weekday) {
                    case 'monday':
                        $result = date('Y-m-d', (strtotime($date)));
                        break;
                    case 'tuesday':
                        $result = date('Y-m-d', (strtotime('-1 day', strtotime($date))));
                        break;
                    case 'wednesday':
                        $result = date('Y-m-d', (strtotime('-2 days', strtotime($date))));
                        break;
                    case 'thursday':
                        $result = date('Y-m-d', (strtotime('-3 days', strtotime($date))));
                        break;
                    case 'friday':
                        $result = date('Y-m-d', (strtotime('-4 days', strtotime($date))));
                        break;
                    case 'saturday':
                        $result = date('Y-m-d', (strtotime('-5 days', strtotime($date))));
                        break;
                    default:
                        $result = date('Y-m-d', (strtotime('-6 days', strtotime($date))));
                        break;
                }
                break;
            case 'year':
                $result = date('Y-01-01');
                break;

            case 'all':
                $result = null;
                break;
            case 'month':
            default:
                $result = date('Y-m-01');
                break;
        }

        return $result;
    }

    public function getReportOrder($data = [], $type = null)
    {
        // $type bao gồm: revenue (theo thời gian) || staff (theo nhân viên) || city (theo tỉnh thành) || source (theo nguồn đơn hàng)
        if(empty($type)) return [];
        switch ($type) {
            case 'revenue':
                $param_type = 'the_time';
                $param_combine = '{n}.the_time';
                break;

            case 'staff':
                $param_type = 'staff';
                $param_combine = '{n}.staff_id';
                break;
            
            case 'city':
                $param_type = 'city';
                $param_combine = '{n}.OrdersContact.city_id';
                break;

            case 'source':
                $param_type = 'source';
                $param_combine = '{n}.source';
                break;
        }

        $table = TableRegistry::get('Orders');
        $utilities = $this->loadComponent('Utilities');

        $number_record = !empty($data[NUMBER_RECORD]) ? intval($data[NUMBER_RECORD]) : 50;
        $page = !empty($data[PAGE]) ? intval($data[PAGE]) : 1;

        $data[SORT][FIELD] = !empty($data['sort_field']) ? $data['sort_field'] : '';
        $data[SORT][SORT] = !empty($data['sort_type']) ? $data['sort_type'] : '';
        $report_order = [];

        if(!empty($data['export']) && $data['export'] == 'all') {
            $number_record = 1000;
            $count_eport_order = $table->reportOrder($data, $param_type)->count();

            // xuất excel trường hợp record lớn hơn 1000
            if(!empty($count_eport_order) && $count_eport_order > $number_record){
                $this->exportExcelReportOrderLarge($data, $count_eport_order, $number_record, $param_type, $param_combine, $type);
            }
        }

        try {
            $report_order = $this->paginate($table->reportOrder($data, $param_type), [
                'limit' => $number_record,
                'page' => $page
            ])->toArray();
        } catch (Exception $e) {
            $report_order = $this->paginate($table->reportOrder($data, $param_type), [
                'limit' => $number_record,
                'page' => 1
            ])->toArray();
        }

        $report_order = $this->getReportOrderDoneAndCancel($report_order, $data, $type);

        $pagination = [];
        $pagination_info = !empty($this->request->getAttribute('paging')['Orders']) ? $this->request->getAttribute('paging')['Orders'] : [];
        $pagination = $utilities->formatPaginationInfo($pagination_info);

        $report_order = $table->formatReportOrder($report_order, [
            PAGINATION => $pagination
        ]);

        if(!empty($data['export'])) {
            return $this->exportExcelReporOrder($report_order, $param_type);
        }

        return [
            DATA => $report_order,
            PAGINATION => $pagination
        ];
    }

    public function getReportProductDoneAndCancel($report_product = [], $data = [])
    {
        if(empty($report_product)) return [];

        $table = TableRegistry::get('OrdersItem');
        $data['status'] = DONE;
        $product_done = $table->reportProduct($data)->toArray();
        $number_product_done = Hash::combine($product_done, '{n}.product_item_id', '{n}.total_quantity');
        $product_done = Hash::combine($product_done, '{n}.product_item_id', '{n}.total_item');

        $data['status'] = CANCEL;
        $product_cancel = $table->reportProduct($data)->toArray();
        $product_cancel = Hash::combine($product_cancel, '{n}.product_item_id', '{n}.total_item');

        if(!empty($report_product)){
            foreach ($report_product as $k => $report) {
                $product_item_id = !empty($report['product_item_id']) ? $report['product_item_id'] : null;

                $report_product[$k]['product_done'] = !empty($product_done[$product_item_id]) ? $product_done[$product_item_id] : 0;
                $report_product[$k]['product_cancel'] = !empty($product_cancel[$product_item_id]) ? $product_cancel[$product_item_id] : 0;
                $report_product[$k]['number_product_done'] = !empty($number_product_done[$product_item_id]) ? $number_product_done[$product_item_id] : 0;
            }
        }

        return $report_product;
    }

    public function getReportOrderDoneAndCancel($report_order = null, $data = null, $type = null) 
    {
        if(empty($type) || empty($report_order)) return [];
        switch ($type) {
            case 'revenue':
                $param_type = 'the_time';
                $param_combine = '{n}.the_time';
                break;

            case 'staff':
                $param_type = 'staff';
                $param_combine = '{n}.staff_id';
                break;
            
            case 'city':
                $param_type = 'city';
                $param_combine = '{n}.OrdersContact.city_id';
                break;

            case 'source':
                $param_type = 'source';
                $param_combine = '{n}.source';
                break;
        }

        $table = TableRegistry::get('Orders');

        $data['status'] = DONE;
        $order_done = $table->reportOrder($data, $param_type)->toArray();
        $number_order_done = Hash::combine($order_done, $param_combine, '{n}.number_order');
        $order_done = Hash::combine($order_done, $param_combine, '{n}.total');

        $data['status'] = CANCEL;
        $order_cancel = $table->reportOrder($data, $param_type)->toArray();
        $order_cancel = Hash::combine($order_cancel, $param_combine, '{n}.total');

        foreach ($report_order as $k => $report) {
            $value_order = null;
            switch ($type) {
                case 'revenue':
                    $value_order = !empty($report['the_time']) ? $report['the_time'] : null;
                    break;

                case 'staff':
                    $value_order = !empty($report['staff_id']) ? $report['staff_id'] : null;
                    break;

                case 'city':
                    $value_order = !empty($report['OrdersContact']['city_id']) ? $report['OrdersContact']['city_id'] : null;
                    break;

                case 'source':
                    $value_order = !empty($report['source']) ? $report['source'] : null;
                    break;
            }
            
            if(empty($value_order)) break;

            $report_order[$k]['order_done'] = !empty($order_done[$value_order]) ? $order_done[$value_order] : 0;
            $report_order[$k]['order_cancel'] = !empty($order_cancel[$value_order]) ? $order_cancel[$value_order] : 0;
            $report_order[$k]['number_order_done'] = !empty($number_order_done[$value_order]) ? $number_order_done[$value_order] : 0;
        }

        return $report_order;
    }

    public function exportExcelReportOrderLarge($data = [], $count_eport_order = null, $number_record = null, $param_type = null, $param_combine = null, $type = null)
    {
        if(empty($data) || empty($count_eport_order) || empty($number_record) || empty($param_type) || empty($param_combine) || empty($type)) return [];
        $table = TableRegistry::get('Orders');

        $page_number = ceil($count_eport_order / $number_record);
        if($page_number < 1)  $page_number = 1;
        $page_export = 0;
        $array_export = [];

        for ($i = 0; $i < $page_number; $i++) {
            $page_export ++;
            $report_order_export = $table->reportOrder($data, $param_type)->limit($number_record)->page($page_export)->toArray();
            if(empty($report_order_export)) break;

            $data_done['status'] = DONE;
            $order_done_export  = $table->reportOrder($data_done, $param_type)->limit($number_record)->page($page_export)->toArray();
            $number_order_done = Hash::combine($order_done_export, $param_combine, '{n}.number_order');
            $order_done_export = Hash::combine($order_done_export, $param_combine, '{n}.total');

            $data_cancel['status'] = CANCEL;
            $order_cancel_export = $table->reportOrder($data_cancel, $param_type)->limit($number_record)->page($page_export)->toArray();
            $order_cancel_export = Hash::combine($order_cancel_export, $param_combine, '{n}.total');

            if(!empty($report_order_export)){
                foreach ($report_order_export as $k => $report) {
                    $value_order = null;
                    switch ($type) {
                        case 'revenue':
                            $value_order = !empty($report['the_time']) ? $report['the_time'] : null;
                            break;

                        case 'staff':
                            $value_order = !empty($report['staff_id']) ? $report['staff_id'] : null;
                            break;

                        case 'city':
                            $value_order = !empty($report['OrdersContact']['city_id']) ? $report['OrdersContact']['city_id'] : null;
                            break;

                        case 'source':
                            $value_order = !empty($report['source']) ? $report['source'] : null;
                            break;
                    }
                    
                    if(empty($value_order)) break;

                    $report['order_done'] = !empty($order_done_export[$value_order]) ? $order_done_export[$value_order] : 0;
                    $report['order_cancel'] = !empty($order_cancel_export[$value_order]) ? $order_cancel_export[$value_order] : 0;
                    $report['number_order_done'] = !empty($number_order_done[$value_order]) ? $number_order_done[$value_order] : 0;

                    $array_export[] = $report;
                }
            }
        }

        $array_export = $table->formatReportOrder($array_export);
        return $this->exportExcelReporOrder($array_export, $param_type);
    }

    public function exportExcelReporProduct($data = [])
    {
        if(empty($data)) return false;

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        foreach(range('A','M') as $columnID) {
            $spreadsheet->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
        }

        $spreadsheet->getActiveSheet()->getStyle('C2:C3000')->getNumberFormat()->setFormatCode('#,##0');
        $spreadsheet->getActiveSheet()->getStyle('D2:D3000')->getNumberFormat()->setFormatCode('#,##0');
        $spreadsheet->getActiveSheet()->getStyle('E2:E3000')->getNumberFormat()->setFormatCode('#,##0');
        $spreadsheet->getActiveSheet()->getStyle('F2:F3000')->getNumberFormat()->setFormatCode('#,##0');
        $spreadsheet->getActiveSheet()->getStyle('G2:G3000')->getNumberFormat()->setFormatCode('#,##0');
        $spreadsheet->getActiveSheet()->getStyle('H2:H3000')->getNumberFormat()->setFormatCode('#,##0');
        $spreadsheet->getActiveSheet()->getStyle('I2:I3000')->getNumberFormat()->setFormatCode('#,##0');

        $sheet->setCellValue('A1', __d('admin', 'ma'));
        $sheet->setCellValue('B1', __d('admin', 'san_pham'));
        $sheet->setCellValue('C1', __d('admin', 'gia_trung_binh'));
        $sheet->setCellValue('D1', __d('admin', 'chiet_khau'));
        $sheet->setCellValue('E1', __d('admin', 'vat'));
        $sheet->setCellValue('F1', __d('admin', 'so_luong_ban'));
        $sheet->setCellValue('G1', __d('admin', 'tam_tinh'));
        $sheet->setCellValue('H1', __d('admin', 'doanh_thu'));

        $sheet->setCellValue('I1', __d('admin', 'don_huy'));
        $sheet->setCellValue('J1', __d('admin', 'cvr'));

        $count = 2;
        if(!empty($data['item_report'])){
            foreach ($data['item_report'] as $row) {
                $code = !empty($row['code']) ? $row['code'] : '';
                $name_extend = !empty($row['name_extend']) ? $row['name_extend'] : '';
                $price = !empty($row['price']) ? $row['price'] : '';
                $discount = !empty($row['discount']) ? $row['discount'] : '';
                $vat = !empty($row['vat']) ? $row['vat'] : '';
                $quantity = !empty($row['quantity']) ? $row['quantity'] : '';
                $total = !empty($row['total']) ? $row['total'] : '';
                $product_done = !empty($row['product_done']) ? $row['product_done'] : '';
                $product_cancel = !empty($row['product_cancel']) ? $row['product_cancel'] : '';
                $order_done = !empty($row['order_done']) ? $row['order_done'] : '';
                $order_cancel = !empty($row['order_cancel']) ? $row['order_cancel'] : '';
                $cvr = !empty($row['cvr']) ? $row['cvr'] : '';


                $sheet->setCellValue('A'. $count, $code);
                $sheet->setCellValue('B'. $count, $name_extend);
                $sheet->setCellValue('C'. $count, $price);
                $sheet->setCellValue('D'. $count, $discount);
                $sheet->setCellValue('E'. $count, $vat);
                $sheet->setCellValue('F'. $count, $quantity);
                $sheet->setCellValue('G'. $count, $total);
                $sheet->setCellValue('H'. $count, $product_done);
                $sheet->setCellValue('I'. $count, $product_cancel);
                $sheet->setCellValue('J'. $count, $cvr);

                $count ++;
            }
        }

        $writer = new Xlsx($spreadsheet);

        ob_start();
        $writer->save('php://output');
        $xlsData = ob_get_contents();
        ob_end_clean();
        
        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData),
            META => [
                'name' => 'bao_cao_theo_san_pham_'. time()
            ]
        ]);
    }

    public function exportExcelReporOrder($data = null, $type = null) 
    {
        if(empty($data) || empty($type)) return false;
        $title_cell = $value_cell = $meta_excel = '';
        switch ($type) {
            case 'the_time':
                $title_cell = __d('admin', 'thoi_gian');
                $value_cell = 'the_time';
                $meta_excel = 'bao_cao_theo_thoi_gian_';
                break;

            case 'staff':
                $title_cell = __d('admin', 'nhan_vien');
                $value_cell = 'staff_name';
                $meta_excel = 'bao_cao_theo_nhan_vien_';
                break;

            case 'city':
                $title_cell = __d('admin', 'tinh_thanh');
                $value_cell = 'city_name';
                $meta_excel = 'bao_cao_theo_tinh_thanh_';
                break;

            case 'source':
                $title_cell = __d('admin', 'nguon_don_hang');
                $value_cell = 'source';
                $meta_excel = 'bao_cao_theo_nguon_don_hang_';
                break;
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        foreach(range('A','M') as $columnID) {
            $spreadsheet->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
        }

        $spreadsheet->getActiveSheet()->getStyle('B2:B3000')->getNumberFormat()->setFormatCode('#,##0');
        $spreadsheet->getActiveSheet()->getStyle('C2:C3000')->getNumberFormat()->setFormatCode('#,##0');
        $spreadsheet->getActiveSheet()->getStyle('D2:D3000')->getNumberFormat()->setFormatCode('#,##0');
        $spreadsheet->getActiveSheet()->getStyle('E2:E3000')->getNumberFormat()->setFormatCode('#,##0');
        $spreadsheet->getActiveSheet()->getStyle('F2:F3000')->getNumberFormat()->setFormatCode('#,##0');
        $spreadsheet->getActiveSheet()->getStyle('G2:G3000')->getNumberFormat()->setFormatCode('#,##0');
        $spreadsheet->getActiveSheet()->getStyle('H2:H3000')->getNumberFormat()->setFormatCode('#,##0');
        $spreadsheet->getActiveSheet()->getStyle('I2:I3000')->getNumberFormat()->setFormatCode('#,##0'); 
        $spreadsheet->getActiveSheet()->getStyle('J2:J3000')->getNumberFormat()->setFormatCode('#,##0'); 
        $spreadsheet->getActiveSheet()->getStyle('K2:K3000')->getNumberFormat()->setFormatCode('#,##0'); 

        $sheet->setCellValue('A1', $title_cell);
        $sheet->setCellValue('B1', __d('admin', 'so_don'));
        $sheet->setCellValue('C1', __d('admin', 'so_luong_san_pham'));
        $sheet->setCellValue('D1', __d('admin', 'doanh_thu_truoc_chiet_khau'));
        $sheet->setCellValue('E1', __d('admin', 'chiet_khau'));
        $sheet->setCellValue('F1', __d('admin', 'phi_van_chuyen'));
        $sheet->setCellValue('G1', __d('admin', 'vat'));
        $sheet->setCellValue('H1', __d('admin', 'tam_tinh'));

        $sheet->setCellValue('I1', __d('admin', 'con_no'));
        $sheet->setCellValue('J1', __d('admin', 'doanh_thu'));
        $sheet->setCellValue('K1', __d('admin', 'don_huy'));
        $sheet->setCellValue('L1', __d('admin', 'cvr'));

        $count = 2;
        if(!empty($data['item_report'])){
            foreach ($data['item_report'] as $row) {
                $set_value_cell = !empty($row[$value_cell]) ? $row[$value_cell] : '';
                $number_order = !empty($row['number_order']) ? $row['number_order'] : '';
                $count_items = !empty($row['count_items']) ? $row['count_items'] : '';
                $origin = !empty($row['origin']) ? $row['origin'] : '';
                $all_discount = !empty($row['all_discount']) ? $row['all_discount'] : '';
                $shipping = !empty($row['shipping']) ? $row['shipping'] : '';
                $vat = !empty($row['vat']) ? $row['vat'] : '';
                $total = !empty($row['total']) ? $row['total'] : '';
                $debt = !empty($row['debt']) ? $row['debt'] : '';
                $order_done = !empty($row['order_done']) ? $row['order_done'] : '';
                $order_cancel = !empty($row['order_cancel']) ? $row['order_cancel'] : '';
                $cvr = !empty($row['cvr']) ? $row['cvr'] : '';


                $sheet->setCellValue('A'. $count, $set_value_cell);
                $sheet->setCellValue('B'. $count, $number_order);
                $sheet->setCellValue('C'. $count, $count_items);
                $sheet->setCellValue('D'. $count, $origin);
                $sheet->setCellValue('E'. $count, $all_discount);
                $sheet->setCellValue('F'. $count, $shipping);
                $sheet->setCellValue('G'. $count, $vat);
                $sheet->setCellValue('H'. $count, $total);
                $sheet->setCellValue('I'. $count, $debt);
                $sheet->setCellValue('J'. $count, $order_done);
                $sheet->setCellValue('K'. $count, $order_cancel);
                $sheet->setCellValue('L'. $count, $cvr);

                $count ++;
            }
        }

        $writer = new Xlsx($spreadsheet);

        ob_start();
        $writer->save('php://output');
        $xlsData = ob_get_contents();
        ob_end_clean();
        
        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData),
            META => [
                'name' => $meta_excel . time()
            ]
        ]);
    }
}   