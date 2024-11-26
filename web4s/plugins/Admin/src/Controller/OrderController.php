<?php

namespace Admin\Controller;

use Admin\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Utility\Hash;
use Cake\Http\Response;
use Cake\ORM\Query;
use Cake\Core\Exception\Exception;
use Cake\I18n\Time;
use Cake\Datasource\ConnectionManager;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Lib\Shipping\NhShipping;

class OrderController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function list()
    {
        $this->js_page = '/assets/js/pages/list_order.js';

        $this->set('path_menu', 'order');  
        $this->set('title_for_layout', __d('admin', 'don_hang'));
    }

    public function listJson()
    {
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $table = TableRegistry::get('Orders');
        $utilities = $this->loadComponent('Utilities');

        $data = $params = $order_item = [];

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

        // other
        $params['get_contact'] = true;
        $type = !empty($params[FILTER][TYPE]) ? $params[FILTER][TYPE] : null;

        if($type == ORDER_RETURN){
            $params['get_related'] = true;
        }

        // page and limit
        $page = !empty($data[PAGINATION][PAGE]) ? intval($data[PAGINATION][PAGE]) : 1;
        $limit = !empty($data[PAGINATION][PERPAGE]) ? intval($data[PAGINATION][PERPAGE]) : PAGINATION_LIMIT_ADMIN;
        
        // sort 
        $params[SORT] = !empty($data[SORT]) ? $data[SORT] : [];
        $sort_field = !empty($params[SORT][FIELD]) ? $params[SORT][FIELD] : null;
        $sort_type = !empty($params[SORT][SORT]) ? $params[SORT][SORT] : null; 

        if (!empty($data['export'])) {
            $params['get_staff'] = !empty($data['get_staff']) ? true : false;
        }

        if(!empty($data['export']) && $data['export'] == 'all') {
            $limit = 100000;
        }

        try {
            $query = $table->queryListOrders($params);
            $orders = $this->paginate($table->queryListOrders($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();

        } catch (Exception $e) {
            $page = 1;
            $orders = $this->paginate($table->queryListOrders($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        }
        
        // parse data before output
        $result = [];
        if(!empty($orders)){
            foreach($orders as $order){
                $order_format = $table->formatDataOrderDetail($order, $this->lang);
                $result[] = $order_format;
            }
        }

        if(!empty($data['export'])) {
            return $this->exportExcelOrder($result);
        }

        $pagination_info = !empty($this->request->getAttribute('paging')['Orders']) ? $this->request->getAttribute('paging')['Orders'] : [];
        $meta_info = $utilities->formatPaginationInfo($pagination_info);

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $result, 
            META => $meta_info
        ]);
    }

    public function exportExcelOrder($data = [])
    {
        if(empty($data)) return false;

        $spreadsheet = $this->initializationExcel($data);
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
                'name' => __d('admin', 'danh_sach_don_hang')
            ]
        ]);
    }

    // khởi tạo file excel
    // Dùng để export dữ liệu excel và download file excel mẫu
    public function initializationExcel($data = [])
    {
        $list_source = Hash::combine(TableRegistry::get('Objects')->find()->where([
            'type' => ORDER_SOURCE,
            'deleted' => 0
        ])->order('is_default DESC')->toArray(), '{n}.code', '{n}.name');

        $list_source = !empty($list_source) ? $list_source : [];

        $status_order = [
            DRAFT => __d('admin', 'chua_xac_nhan'),
            NEW_ORDER => __d('admin', 'don_moi'),
            CONFIRM => __d('admin', 'xac_nhan'),
            PACKAGE => __d('admin', 'dong_goi'),
            EXPORT => __d('admin', 'xuat_kho'),
            DONE => __d('admin', 'thanh_cong'),
            CANCEL => __d('admin', 'don_huy')
        ];

        $data_dropdown = [
            'true_false' => __d('admin', 'co') .','.__d('admin', 'khong'),
            'source' => !empty($list_source) ? implode(',', $list_source) : '',
            'status' => !empty($status_order) ? implode(',', $status_order) : ''
        ];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $spreadsheet->getActiveSheet()->setTitle(__d('admin', 'thong_tin_don_hang'));

        $arr_header = [
            'id' => __d('admin', 'id'),
            'source' => __d('admin', 'kenh'),
            'code' => __d('admin', 'ma_don_hang'),
            'total' => __d('admin', 'gia_tri_don_hang'),
            'paid' => __d('admin', 'da_thanh_toan'),
            'debt' => __d('admin', 'con_no'),
            'staff_id' => __d('admin', 'nhan_vien_cham_soc'),
            'status' => __d('admin', 'tinh_trang'),
            'created' => __d('admin', 'thoi_gian_tao_don'),
            'full_name' => __d('admin', 'ho_va_ten_khach_hang'),
            'phone' => __d('admin', 'so_dien_thoai'),
            'email' => 'Email',
            'city_name' => __d('admin', 'tinh_thanh'),
            'district_name' => __d('admin', 'quan_huyen'),
            'ward_name' => __d('admin', 'phuong_xa'),
            'address' => __d('admin', 'dia_chi')
        ];

        if (empty($arr_header)) return false;

        $column = $column_end = 'A';
        $row = 1;

        foreach ($arr_header as $key => $header) {
            $sheet->setCellValue($column . $row, $header);
            $sheet->getStyle($column . $row)->getFont()->setBold(true);
            $sheet->getStyle($column . $row)->getAlignment()->setVertical('center');

            switch ($key) {
                case 'id':
                    $spreadsheet->getActiveSheet()->getColumnDimension($column)->setWidth(25, 'pt');
                    $sheet->getStyle($column . $row)->getAlignment()->setHorizontal('center');
                    break;
                case 'source':
                    $spreadsheet->getActiveSheet()->getColumnDimension($column)->setWidth(100, 'pt');
                    $sheet->getStyle($column . $row)->getAlignment()->setHorizontal('center');
                    break;
                case 'code':
                    $spreadsheet->getActiveSheet()->getColumnDimension($column)->setWidth(120, 'pt');
                    break;
                case 'total':
                    $spreadsheet->getActiveSheet()->getColumnDimension($column)->setWidth(120, 'pt');
                    $sheet->getStyle($column . $row)->getAlignment()->setHorizontal('center');
                    break;
                case 'paid':
                    $spreadsheet->getActiveSheet()->getColumnDimension($column)->setWidth(120, 'pt');
                    $sheet->getStyle($column . $row)->getAlignment()->setHorizontal('center');
                    break;
                case 'debt':
                    $spreadsheet->getActiveSheet()->getColumnDimension($column)->setWidth(120, 'pt');
                    $sheet->getStyle($column . $row)->getAlignment()->setHorizontal('center');
                    break;
                case 'staff_id':
                    $spreadsheet->getActiveSheet()->getColumnDimension($column)->setWidth(120, 'pt');
                    $sheet->getStyle($column . $row)->getAlignment()->setHorizontal('center');
                    break;
                case 'status':
                    $spreadsheet->getActiveSheet()->getColumnDimension($column)->setWidth(90, 'pt');
                    $sheet->getStyle($column . $row)->getAlignment()->setHorizontal('center');
                    break;
                case 'created':
                    $spreadsheet->getActiveSheet()->getColumnDimension($column)->setWidth(100, 'pt');
                    break;
                case 'full_name':
                    $spreadsheet->getActiveSheet()->getColumnDimension($column)->setWidth(200, 'pt');
                    $sheet->getStyle($column . $row)->getAlignment()->setHorizontal('center');
                    break;
                case 'phone':
                    $spreadsheet->getActiveSheet()->getColumnDimension($column)->setWidth(120, 'pt');
                    $sheet->getStyle($column . $row)->getAlignment()->setHorizontal('center');
                    break;
                case 'email':
                    $spreadsheet->getActiveSheet()->getColumnDimension($column)->setWidth(150, 'pt');
                    $sheet->getStyle($column . $row)->getAlignment()->setHorizontal('center');
                    $sheet->getStyle($column . $row)->getAlignment()->setHorizontal('center');
                    break;
                case 'city_name':
                    $spreadsheet->getActiveSheet()->getColumnDimension($column)->setWidth(90, 'pt');
                    $sheet->getStyle($column . $row)->getAlignment()->setHorizontal('center');
                    break;
                case 'district_name':
                    $spreadsheet->getActiveSheet()->getColumnDimension($column)->setWidth(90, 'pt');
                    $sheet->getStyle($column . $row)->getAlignment()->setHorizontal('center');
                    break;
                case 'ward_name':
                    $spreadsheet->getActiveSheet()->getColumnDimension($column)->setWidth(90, 'pt');
                    $sheet->getStyle($column . $row)->getAlignment()->setHorizontal('center');
                    break;
                case 'address':
                    $spreadsheet->getActiveSheet()->getColumnDimension($column)->setWidth(250, 'pt');
                    break;
                
                default: 
                    $spreadsheet->getActiveSheet()->getColumnDimension($column)->setAutoSize(true);
            }

            $column_end = $column;
            $column++;
        }

        // style excel
        $spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(25);
        $spreadsheet->getActiveSheet()->getStyle('A1:' . $column_end . '1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('fcb789');

        $row_excel = 2;
        foreach ($data as $key => $item) { 
            // thêm dữ liệu full vào row excel
            $colum_excel = 'A';
            foreach ($arr_header as $code => $header) {

                switch ($code) {
                    case 'source':
                        $source = !empty($item[$code]) ? $item[$code] : null;
                        $source_name = !empty($list_source[$source]) ? $list_source[$source] : '';

                        $sheet->setCellValue($colum_excel . $row_excel, $source_name);

                        $validation = $spreadsheet->getActiveSheet()->getCell($colum_excel.$row_excel)->getDataValidation();
                        $validation->setType( \PhpOffice\PhpSpreadsheet\Cell\Datavalidation::TYPE_LIST );
                        $validation->setErrorStyle( \PhpOffice\PhpSpreadsheet\Cell\Datavalidation::STYLE_INFORMATION );
                        $validation->setAllowBlank(false);
                        $validation->setShowInputMessage(true);
                        $validation->setShowErrorMessage(true);
                        $validation->setShowDropDown(true);
                        $validation->setErrorTitle('Input error');
                        $validation->setError('Value is not in list.');
                        $validation->setPromptTitle('Pick from list');
                        $validation->setPrompt('Please pick a value from the drop-down list.');
                        $validation->setFormula1('"' . $data_dropdown['source'] . '"');

                        $sheet->getStyle($colum_excel . $row_excel)->getAlignment()->setHorizontal('center');

                        break;
                    case 'total': 
                    case 'paid': 
                    case 'debt': 
                        $money = !empty($item[$code]) ? number_format(floatval($item[$code])) : '';

                        $sheet->setCellValue($colum_excel . $row_excel, !empty($money) ? $money : '');
                        $sheet->getStyle($colum_excel . $row_excel)->getAlignment()->setHorizontal('center');

                        break;
                    case 'staff_id': 
                        $staff_info = !empty($item['staff']) ? $item['staff'] : [];
                        $staff_name = !empty($staff_info['full_name']) ? $staff_info['full_name'] : '';

                        $sheet->setCellValue($colum_excel . $row_excel, !empty($staff_name) ? $staff_name : '');
                        $sheet->getStyle($colum_excel . $row_excel)->getAlignment()->setHorizontal('center');

                        break;
                    case 'status':
                        $status = !empty($item['status']) ? $item['status'] : null;
                        $status_name = !empty($status_order[$status]) ? $status_order[$status] : '';

                        $sheet->setCellValue($colum_excel . $row_excel, !empty($status_name) ? $status_name : '');

                        $validation = $spreadsheet->getActiveSheet()->getCell($colum_excel.$row_excel)->getDataValidation();
                        $validation->setType( \PhpOffice\PhpSpreadsheet\Cell\Datavalidation::TYPE_LIST );
                        $validation->setErrorStyle( \PhpOffice\PhpSpreadsheet\Cell\Datavalidation::STYLE_INFORMATION );
                        $validation->setAllowBlank(false);
                        $validation->setShowInputMessage(true);
                        $validation->setShowErrorMessage(true);
                        $validation->setShowDropDown(true);
                        $validation->setErrorTitle('Input error');
                        $validation->setError('Value is not in list.');
                        $validation->setPromptTitle('Pick from list');
                        $validation->setPrompt('Please pick a value from the drop-down list.');
                        $validation->setFormula1('"' . $data_dropdown['status'] . '"');

                        $sheet->getStyle($colum_excel . $row_excel)->getAlignment()->setHorizontal('center');

                        break;
                    case 'full_name': 
                    case 'phone': 
                    case 'email': 
                    case 'city_name': 
                    case 'district_name': 
                    case 'ward_name': 
                    case 'address': 
                        $contact = !empty($item['contact']) ? $item['contact'] : [];
                        $sheet_value = !empty($contact[$code]) ? $contact[$code] : '';

                        $sheet->setCellValue($colum_excel . $row_excel, !empty($sheet_value) ? $sheet_value : '');

                        if (!in_array($code, ['full_name', 'address'])) {
                            $sheet->getStyle($colum_excel . $row_excel)->getAlignment()->setHorizontal('center');
                        }

                        break;
                    case 'created': 
                        $created = !empty($item[$code]) ? date('H:i - d/m/Y', $item[$code]) : '';

                        $sheet->setCellValue($colum_excel . $row_excel, !empty($created) ? $created : '');
                        $sheet->getStyle($colum_excel . $row_excel)->getAlignment()->setHorizontal('center');

                        break;
                    default:
                        $sheet->setCellValue($colum_excel . $row_excel, !empty($item[$code]) ? $item[$code] : '');
                        break;
                }

                $sheet->getStyle($colum_excel . $row_excel)->getAlignment()->setVertical('center');
                $colum_excel ++;
            }

            $row_excel ++;
        }

        return $spreadsheet;
    }

    public function add()
    {
        $promotions = TableRegistry::get('Promotions')->getListPromotionActive();

        $this->set('check_promotion', !empty($promotions) ? true : false);

        $this->css_page = [
            '/assets/css/pages/wizard/wizard-1.css',
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.css',
        ];

        $this->js_page = [
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.js',
            '/assets/js/bill_staff.js',
            '/assets/js/bill_customer.js',
            '/assets/js/bill_payment_confirm.js',            
            '/assets/js/bill_form.js',
            '/assets/js/bill_shipping.js',
            '/assets/js/bill_promotion.js',
            '/assets/js/pages/order.js',
        ];

        $this->set('path_menu', 'order_add');
        $this->set('title_for_layout', __d('admin', 'tao_don_hang'));
        $this->render('update');
    }

    public function update($id = null)
    {
        $orders_table = TableRegistry::get('Orders');
        
        $order = $orders_table->getDetailOrder($id, [
            'get_items' => true, 
            'get_contact' => true,
            'get_user' => true,
            'get_staff' => true,
            'get_payment' => true
        ]);

        $order = $orders_table->formatDataOrderDetail($order, $this->lang);
        if(empty($order)){
            $this->showErrorPage();
        }

        if(empty($order['type']) || $order['type'] != ORDER){
            $this->responseJson([MESSAGE => __d('admin', 'thong_tin_don_hang_khong_hop_le')]);
        }

        if(!empty($order['status']) && !in_array($order['status'], [NEW_ORDER, CONFIRM])){
            return $this->redirect(ADMIN_PATH . '/order/detail/' . $id);
        }

        $promotions = TableRegistry::get('Promotions')->getListPromotionActive();
        $this->set('check_promotion', !empty($promotions) ? true : false);
        $this->set('order', $order);        

        $this->css_page = [
            '/assets/css/pages/wizard/wizard-1.css',
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.css'
        ];

        $this->js_page = [
            '/assets/plugins/custom/jquery-ui/jquery-ui.bundle.js',
            '/assets/js/bill_staff.js',
            '/assets/js/bill_customer.js',
            '/assets/js/bill_payment_confirm.js',
            '/assets/js/bill_form.js',
            '/assets/js/bill_shipping.js',
            '/assets/js/bill_promotion.js',
            '/assets/js/pages/order.js'
        ];

        
        $this->set('path_menu', 'order');
        $this->set('title_for_layout', __d('admin', 'cap_nhat_don_hang'));
    }

    public function detail($id = null)
    {
        $orders_table = TableRegistry::get('Orders');
        $payments_table = TableRegistry::get('Payments');
        $shippings_table = TableRegistry::get('Shippings');

        $order = $orders_table->getDetailOrder($id, [
            'get_items' => true, 
            'get_contact' => true,
            'get_staff' => true,
            'get_user' => true
        ]);

        $order = $orders_table->formatDataOrderDetail($order, $this->lang);
        if(empty($order) || $order['type'] != ORDER){
            $this->showErrorPage();
        }

        if(empty($order['type']) || $order['type'] != ORDER){
            $this->responseJson([MESSAGE => __d('admin', 'thong_tin_don_hang_khong_hop_le')]);
        }

        // payment info
        $payments = $payments_table->queryListPayments([
            FIELD => FULL_INFO,
            FILTER => [
                'foreign_id' => $id,
                'foreign_type' => ORDER,
                'type' => 1
            ],
            SORT => [
                FIELD => 'id',
                SORT => ASC
            ]
        ])->toArray();

        $can_payment = false;
        $status = !empty($order['status']) ? $order['status'] : null;
        $debt = !empty($order['debt']) ? floatval($order['debt']) : 0;
        $shipping_method = !empty($order['shipping_method']) ? $order['shipping_method'] : null;

        $paid_pending = $payments_table->getPendingPaymentOrder($id);
        if(($debt - $paid_pending) > 0 && !in_array($status, [DONE, CANCEL, DRAFT])){
            $can_payment = true;
        }

        // shipping info
        $shippings = $shippings_table->queryListShippings([
            FIELD => FULL_INFO,
            FILTER => [
                'order_id' => $id
            ],
            SORT => [
                FIELD => 'id',
                SORT => ASC
            ]
        ])->toArray();

        $shipping_info = !empty($shippings) ? end($shippings) : [];

        $can_shipping = false;
        if((empty($shipping_info) || (!empty($shipping_info['status']) && in_array($shipping_info['status'], [CANCEL_PACKAGE, CANCEL_DELIVERED]))) && !in_array($order['status'], [CANCEL, DRAFT])){
            $can_shipping = true;
        }

        $shipped = false;
        if((!empty($shipping_info['status']) && $shipping_info['status'] == DELIVERED) || (!empty($order['shipping_method']) && $order['shipping_method'] == RECEIVED_AT_STORE && in_array($order['status'], [EXPORT, DONE]))){
            $shipped = true;
        }

        // return item info
        $returned_all = false;
        $count_items = !empty($order['count_items']) ? intval($order['count_items']) : 0;

        $list_return = $orders_table->find()->where([
            'Orders.deleted' => 0,
            'Orders.type' => ORDER_RETURN,
            'Orders.related_order_id' => $id
        ])->select(['Orders.id', 'Orders.code'])->toArray();

        $order_returned = [];
        $number_returned = 0;
        if(!empty($list_return)){
            foreach ($list_return as $k => $returned) {
                $order_detail = $orders_table->getDetailOrder($returned['id'], [
                    'get_items' => true
                ]);                
                $order_returned[] = $orders_table->formatDataOrderDetail($order_detail, $this->lang);
                $number_returned += !empty($order_detail['number_items']) ? intval($order_detail['number_items']) : 0;
            }
        }

        if($number_returned >= $count_items){
            $returned_all = true;
        }


        $this->css_page = [
            '/assets/css/pages/wizard/wizard-1.css'
        ];

        $this->js_page = [
            '/assets/js/bill_customer.js',
            '/assets/js/bill_payment_confirm.js',
            '/assets/js/bill_shipping.js',
            '/assets/js/pages/order_detail.js',
        ];
        
        $this->set('id', $id);
        $this->set('order', $order);
        $this->set('payments', $payments);
        $this->set('shippings', $shippings);
        $this->set('can_payment', $can_payment);
        $this->set('debt', $debt);
        $this->set('paid_pending', $paid_pending);
        $this->set('can_shipping', $can_shipping);
        $this->set('shipped', $shipped);
        $this->set('order_returned', $order_returned);
        $this->set('returned_all', $returned_all);

        $this->set('path_menu', 'order');
        $this->set('title_for_layout', __d('admin', 'chi_tiet_don_hang'));
    }

    public function save($id = null)
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();  

        // validate data
        if(!$this->getRequest()->is('post') || empty($data)){
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        if(empty($id) && empty($data['status'])){
            $data['status'] = NEW_ORDER;
        }

        // nếu là đơn tạo mới trong admin thì gửi mail cho khách hàng
        $send_email = false;
        if(empty($id) && (empty($data['status']) || $data['status'] == NEW_ORDER)){
            $send_email = true;
        }
        
        // format data before save order
        $data['type'] = ORDER;
        $data['items'] = !empty($data['items']) ? json_decode($data['items'], true) : [];
        $data['contact'] = !empty($data['contact']) ? json_decode($data['contact'], true) : [];
        $data['created_by'] = $this->Auth->user('id');

        $result = $this->loadComponent('Admin.Order')->saveOrder($data, $id);
        
        if($result[CODE] == SUCCESS){
            //send email
            $email_contact = !empty($result[DATA]['OrdersContact']['email']) ? $result[DATA]['OrdersContact']['email'] : null;

            $result[DATA] = [
                'id' => !empty($result[DATA]->id) ? $result[DATA]->id : null
            ];
        }

        
        if($send_email && !empty($email_contact)){
            $params_email = [
                'to_email' => $email_contact,
                'code' => 'ORDER',
                'id_record' => $result[DATA]['id'],
                'send_try_content' => false,
                'from_website_template' => !empty($api) ? true : false
            ];

            $this->loadComponent('Email')->send($params_email);
        }

        exit(json_encode($result));
    }

    public function changeStatus($id = null)
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();

        // validate data
        if(empty($data) || !$this->getRequest()->is('post')){
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        if(empty($id)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_don_hang')]);
        }

        $status = !empty($data['status']) ? $data['status'] : null;

        if(empty($status) || (!empty($status) && !in_array($status, [NEW_ORDER, CONFIRM, PACKAGE, EXPORT, DONE, CANCEL]))){
            return $this->System->getResponse([MESSAGE => __d('admin', 'trang_thai_khong_hop_le')]);
        }

        $data_save = [
            'id' => $id,
            'status' => $status
        ];

        $table = TableRegistry::get('Orders');
        $settings = TableRegistry::get('Settings')->getSettingByGroup('order');

        // path entity data
        $order_info = $table->find()->where(['id' => $id, 'deleted' => 0])->select(['id', 'status'])->first();
        if(empty($order_info)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_don_hang')]);
        }

        $order = $table->patchEntity($order_info, $data_save);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $save = $table->save($order);
            if (empty($save->id)){
                throw new Exception(__d('admin', 'luu_thong_tin_don_hang_khong_thanh_cong'));
            }

            // update quantity product after update status order
            $update_quantity_available = $this->loadComponent('Admin.Order')->updateQuantityAvailableOfProduct($id);
            if(!$update_quantity_available){
                throw new Exception(__d('admin', 'cap_nhat_so_luong_san_pham_khong_thanh_cong'));
            }

            $conn->commit();
            $this->responseJson([CODE => SUCCESS, DATA => ['id' => $save->id]]);
        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);
        }
    }

    public function paymentConfirm($order_id = null)
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();

        if(empty($data) || !$this->getRequest()->is('post')){
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $orders_table = TableRegistry::get('Orders');
        $payments_table = TableRegistry::get('Payments');
        $utilities = $this->loadComponent('Utilities');
        $payment_component = $this->loadComponent('Admin.Payment');

        // validate data
        if(empty($order_id)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_don_hang')]);
        }

        $order_info = $orders_table->getDetailOrder($order_id, [
            'get_contact' => true
        ]);        
        $order_info = $orders_table->formatDataOrderDetail($order_info, $this->lang);        
        if(empty($order_info)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_don_hang')]);
        }

        if(empty($order_info['type']) || $order_info['type'] != ORDER){
            $this->responseJson([MESSAGE => __d('admin', 'thong_tin_don_hang_khong_hop_le')]);
        }

        $debt = !empty($order_info['debt']) ? floatval($order_info['debt']) : 0;
        $amount = !empty($data['amount']) ? floatval(str_replace(',', '', $data['amount'])) : 0;

        if($amount > $debt){
            $this->responseJson([MESSAGE => __d('admin', 'so_tien_thanh_toan_vuot_qua_so_tien_can_thanh_toan')]);
        }

        $data_payment = [
            'foreign_id' => $order_id,
            'foreign_type' => ORDER,
            'type' => 1, // 0 => CHI, 1 => THU
            'object_type' => CUSTOMER,
            'object_id' => !empty($order_info['contact']['customer_id']) ? intval($order_info['contact']['customer_id']) : null,
            'amount' => $amount,
            'payment_method' => !empty($data['payment_method']) ? $data['payment_method'] : null,
            'payment_time' => !empty($data['payment_time']) ? $data['payment_time'] : null,
            'reference' => !empty($data['reference']) ? $data['reference'] : null,
            'full_name' => !empty($order_info['contact']['full_name']) ? $order_info['contact']['full_name'] : null,
            'status' => 1
        ];

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $create_payment = $payment_component->savePayment($data_payment, null);
            
            if($create_payment[CODE] == ERROR){
                throw new Exception(!empty($create_payment[MESSAGE]) ? $create_payment[MESSAGE] : null);
            }

            // cộng điểm thưởng sau khi đơn hàng thành công
            $this->loadComponent('Admin.CustomersPoint')->refundPointOrder($order_id);

            // cộng điểm thưởng cho đối tác
            // check thông tin đơn hàng có áp dụng mã giới thiệu không để cộng điểm cho đối tác
            $affiliate_code = !empty($order_info['affiliate_code']) ? $order_info['affiliate_code'] : null;
            $exist_coupon = !empty($order_info['coupon_code']) ? true : false;
            
            if (!empty($affiliate_code)) {
                $this->loadComponent('Admin.CustomersPoint')->refundPointOrderPartner($order_id, $affiliate_code, $exist_coupon);
            }

            $conn->commit();            
            $this->responseJson([CODE => SUCCESS, DATA => $create_payment[DATA]]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);
        }
    }

    public function listAdresses($customer_id = null)
    {
        $this->viewBuilder()->enableAutoLayout(false);

        $customer_addresses = TableRegistry::get('CustomersAddress')->find()->contain(['Customers'])->where([
            'customer_id' => $customer_id
        ])->select(['CustomersAddress.id', 'CustomersAddress.customer_id', 'CustomersAddress.name', 'CustomersAddress.phone', 'CustomersAddress.address', 'CustomersAddress.country_id', 'CustomersAddress.city_id', 'CustomersAddress.district_id', 'CustomersAddress.ward_id', 'CustomersAddress.country_name', 'CustomersAddress.city_name', 'CustomersAddress.district_name', 'CustomersAddress.ward_name', 'CustomersAddress.full_address', 'CustomersAddress.zip_code', 'Customers.full_name', 'Customers.email'
        ])->toArray();

        $list_address = [];
        if(!empty($customer_addresses)){
            foreach ($customer_addresses as $key => $address) {
                $address['address_name'] = !empty($address->name) ? $address->name : null;
                $address['full_name'] = !empty($address['Customers']['full_name']) ? $address['Customers']['full_name'] : null;
                $address['email'] = !empty($address['Customers']['email']) ? $address['Customers']['email'] : null;
                unset($address['Customers']);
                $list_address[] = $address;
            }
        }

        $this->set('list_address', $list_address);
    }

    public function shippingConfirm($order_id = null)
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();    

        if(!$this->getRequest()->is('post') || empty($data)){
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }
        
        $orders_table = TableRegistry::get('Orders');
        $shippings_table = TableRegistry::get('Shippings');
        $utilities = $this->loadComponent('Utilities');

        // validate data
        $shipping_method = !empty($data['shipping_method']) ? $data['shipping_method'] : null;
        if(empty($shipping_method)){
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_van_chuyen_khong_hop_le')]);
        }

        if(empty($order_id)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_don_hang')]);
        }

        $order = $orders_table->getDetailOrder($order_id, [
            'get_items' => true,
            'get_contact' => true
        ]);

        $order_info = $orders_table->formatDataOrderDetail($order, $this->lang);
        if(empty($order_info)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_don_hang')]);
        }
        
        if(empty($order_info['type']) || $order_info['type'] != ORDER){
            $this->responseJson([MESSAGE => __d('admin', 'thong_tin_don_hang_khong_hop_le')]);
        }

        $contact = !empty($order_info['contact']) ? $order_info['contact'] : [];
        if(empty($contact)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_khach_hang')]);
        }

        // kiểm tra đã có đơn vận chuyển đang xử lý hay không
        $exist_shipping = $shippings_table->checkExistShippingActiveForOrder($order_id);
        if($exist_shipping){
            $this->responseJson([MESSAGE => __d('admin', 'da_ton_tai_van_don_cho_don_hang_nay')]);
        }

        $product_items = !empty($order_info['items']) ? $order_info['items'] : [];

        $data['order_id'] = $order_id;
        $data['created_by'] = $this->Auth->user('id');
        $format_result = $this->loadComponent('Admin.Shipping')->formatDataBeforeSave($data, $contact, $product_items);
        $data_shipping = [];
        if(!empty($format_result[CODE]) && $format_result[CODE] == SUCCESS){
            $data_shipping = !empty($format_result[DATA]) ? $format_result[DATA] : [];
        }
        $cod_money = !empty($data_shipping['cod_money']) ? $data_shipping['cod_money'] : 0;

        $data_payment = [];
        // tạo giao dịch chờ thu hộ
        if($cod_money > 0 && in_array($shipping_method, [NORMAL_SHIPPING, SHIPPING_CARRIER])){
            // kiểm tra nếu chưa có giao dịch chờ thu hộ thì khởi tạo data
            $exist_payment_wait_cod = TableRegistry::get('Payments')->checkExistPaymentWaitCodForOrder($order_id);
            if(!$exist_payment_wait_cod){
                $data_payment = [
                    'foreign_id' => $order_id,
                    'foreign_type' => ORDER,
                    'type' => 1, // 0 => CHI, 1 => THU
                    'object_type' => CUSTOMER,
                    'object_id' => !empty($contact['customer_id']) ? intval($contact['customer_id']) : null,
                    'amount' => $cod_money,
                    'payment_method' => COD,
                    'full_name' => !empty($contact['full_name']) ? $contact['full_name'] : null,
                    'status' => 2
                ];
            }            
        }

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            //create shipping
            $create_shipping = $this->loadComponent('Admin.Shipping')->saveShipping($data_shipping, null);

            if($create_shipping[CODE] == ERROR){
                throw new Exception(!empty($create_shipping[MESSAGE]) ? $create_shipping[MESSAGE] : null);
            }
            $shipping = !empty($create_shipping[DATA]) ? $create_shipping[DATA] : [];

            // update status and shipping_fee of order
            $order_info['status'] = PACKAGE;
            $order_info['shipping_method'] = !empty($shipping['shipping_method']) ? $shipping['shipping_method'] : null;
            $order_info['shipping_fee_customer'] = !empty($shipping['shipping_fee_customer']) ? $this->Utilities->formatToDecimal($shipping['shipping_fee_customer']) : null;
            $order_info['shipping_fee'] = !empty($shipping['shipping_fee']) ? $this->Utilities->formatToDecimal($shipping['shipping_fee']) : null;
            $order_info['shipping_note'] = !empty($shipping['note']) ? $shipping['note'] : null;

            $update_order = $this->loadComponent('Admin.Order')->saveOrder($order_info, $order_id);

            if($update_order[CODE] == ERROR){
                throw new Exception(!empty($update_order[MESSAGE]) ? $update_order[MESSAGE] : null);
            }

            // tạo giao dịch phương thức thanh toán COD chờ xác nhận
            if(!empty($data_payment)){
                $create_payment = $this->loadComponent('Admin.Payment')->savePayment($data_payment, null);
            
                if($create_payment[CODE] == ERROR){
                    throw new Exception(!empty($create_payment[MESSAGE]) ? $create_payment[MESSAGE] : null);
                }
            }

            $conn->commit();

            // gửi đơn sang hãng hãng vận chuyển
            if(!empty($create_shipping[DATA]['carrier_code'])){
                $shipping_id = !empty($create_shipping[DATA]['id']) ? $create_shipping[DATA]['id'] : null;
                $send_to_carrier = $this->loadComponent('Admin.Shipping')->sendOrderToCarrier($shipping_id);
                if(!empty($send_to_carrier[CODE]) && $send_to_carrier[CODE] == ERROR){
                    $this->responseJson([MESSAGE => $send_to_carrier[MESSAGE] ? $send_to_carrier[MESSAGE] : __d('admin', 'gui_don_sang_hang_van_chuyen_khong_thanh_cong')]);
                }

                // cập nhật thông tin vận đơn
                if(!empty($send_to_carrier[CODE]) && $send_to_carrier[CODE] == SUCCESS){
                    $shippings_table = TableRegistry::get('Shippings');
                    $shipping_info = $shippings_table->find()->where(['id' => $shipping_id])->select([
                        'id', 'carrier_order_code', 'carrier_shipping_fee'
                    ])->first();

                    $carrier_order_code = !empty($send_to_carrier[DATA]['carrier_order_code']) ? $send_to_carrier[DATA]['carrier_order_code'] : null;
                    $carrier_shipping_fee = !empty($send_to_carrier[DATA]['carrier_shipping_fee']) ? $send_to_carrier[DATA]['carrier_shipping_fee'] : null;

                    $entity = $shippings_table->patchEntity($shipping_info, [
                        'carrier_order_code' => $carrier_order_code,
                        'carrier_shipping_fee' => $carrier_shipping_fee
                    ]);
                    $update_shipping = $shippings_table->save($entity);
                }

            }

            $this->responseJson([CODE => SUCCESS, DATA => $create_shipping[DATA]]);                          

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);
        }
    }

    public function shippingChangeStatus($shipping_id = null)
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();       
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $orders_table = TableRegistry::get('Orders');
        $products_item_table = TableRegistry::get('ProductsItem');
        $orders_item_table = TableRegistry::get('OrdersItem');
        $payments_table = TableRegistry::get('Payments');
        $shippings_table = TableRegistry::get('Shippings');

        // validate data
        $status = !empty($data['status']) ? $data['status'] : null;
        if(empty($status) || !in_array($data['status'], Configure::read('LIST_STATUS_SHIPPING'))){
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $shipping = $shippings_table->find()->where([
            'Shippings.id' => $shipping_id
        ])->order('Shippings.id DESC')->first();

        if(empty($shipping)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_van_chuyen')]);
        }

        $order_id = !empty($shipping['order_id']) ? intval($shipping['order_id']) : null;
        if(empty($order_id)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_don_hang')]);
        }

        $order_info = $orders_table->getDetailOrder($order_id, ['get_contact' => true]);   
        if(empty($order_info)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_don_hang')]);
        }

        // lấy thông tin giao dịch chờ thu hộ
        $payment_wait_cod = $payments_table->find()->where([
            'Payments.foreign_id' => $order_id,
            'Payments.foreign_type' => ORDER,
            'Payments.payment_method' => COD,
            'Payments.type' => 1, // 0 => CHI, 1 => THU
            'Payments.status' => 2
        ])->first();    

        $shipping_method = !empty($shipping['shipping_method']) ? $shipping['shipping_method'] : null;
        $total_order = !empty($order_info['total']) ? floatval($order_info['total']) : 0;
        $debt = !empty($order_info['debt']) ? floatval($order_info['debt']) : 0;    

        if($status == DELIVERY && $shipping_method == RECEIVED_AT_STORE){
            $status = DELIVERED;
        }

        $order_status = null;
        $cancel_shipping_carrier = false;
        switch ($status) {
            case WAIT_DELIVER:            
                $order_status = PACKAGE;
                break;
            
            case DELIVERY:
            case CANCEL_WAIT_DELIVER:
                $order_status = EXPORT;
                break;

            case DELIVERED:
                $order_status = EXPORT;
            
                if(!empty($payment_wait_cod)){
                    $payment_entity = $payments_table->patchEntity($payment_wait_cod, [
                        'status' => 1
                    ]);
                }

                $amount_pending = !empty($payment_wait_cod['amount']) ? floatval($payment_wait_cod['amount']) : 0;

                if(($debt - $amount_pending) <= 0){
                    $order_status = DONE;
                }
                break;

            case CANCEL_PACKAGE:
            case CANCEL_DELIVERED:
                $order_status = CONFIRM;

                if(!empty($shipping['carrier_code'])){
                    $cancel_shipping_carrier = true;
                }

                // hủy đơn thu hộ
                if(!empty($payment_wait_cod)){
                    $payment_entity = $payments_table->patchEntity($payment_wait_cod, [
                        'status' => 0
                    ]);
                }
                break;
        }

        // update status shipping bill
        $data_shipping = $shippings_table->patchEntity($shipping, [
            'status' => $status
        ]);

        // hủy đơn bên hãng vận chuyển
        if($cancel_shipping_carrier){
            $shipping_id = !empty($shipping['id']) ? intval($shipping['id']) : null;
            $cancel_on_carrier = $this->loadComponent('Admin.Shipping')->cancelOrderOnCarrier($shipping_id);
        }

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();
            $update_shipping = $shippings_table->save($data_shipping);            
            if (empty($update_shipping->id)){
                throw new Exception();
            }

            // cập nhật trạng thái giao dịch phương thức COD 
            if(!empty($payment_entity)){
                $update_payment = $payments_table->save($payment_entity);            
                if (empty($update_payment->id)){
                    throw new Exception();
                }

                $update_payment_order = $orders_table->updateAfterPayment($order_id);
                if(!$update_payment_order){
                    throw new Exception();
                }                
            }

            // cập nhật lại trạng thái đơn hàng
            if(!empty($order_status)){
                $data_order = $orders_table->patchEntity($order_info, [
                    'id' => $order_id,
                    'status' => $order_status,
                ]);

                $update_order = $orders_table->save($data_order);
                if (empty($update_order->id)){
                    throw new Exception();
                }
            }

            // Cập nhật số lượng sản phẩm sau khi đổi trạng thái phiếu giao hàng
            $clear_quantity_apply = in_array($status, [CANCEL_PACKAGE, CANCEL_DELIVERED]) ? true : false;
            $update_quantity_available = $this->loadComponent('Admin.Order')->updateQuantityAvailableOfProduct($order_id, $clear_quantity_apply);
            if(!$update_quantity_available){
                throw new Exception(__d('admin', 'cap_nhat_so_luong_san_pham_khong_thanh_cong'));
            }

            // cộng điểm thưởng sau khi đơn hàng thành công
            if($order_status == DONE){

                // cộng điểm thưởng cho khách hàng
                $this->loadComponent('Admin.CustomersPoint')->refundPointOrder($order_id);

                // cộng điểm thưởng cho đối tác
                // check thông tin đơn hàng có áp dụng mã giới thiệu không để cộng điểm cho đối tác
                // cập nhật hạng cho đối tác
                $affiliate_code = !empty($order_info['affiliate_code']) ? $order_info['affiliate_code'] : null;
                $exist_coupon = !empty($order_info['coupon_code']) ? true : false;
                
                if (!empty($affiliate_code)) {
                    $this->loadComponent('Admin.CustomersPoint')->refundPointOrderPartner($order_id, $affiliate_code, $exist_coupon);
                    $this->loadComponent('Admin.Customer')->saveLevelForPartner($affiliate_code);
                }
            }

            $conn->commit();
            $this->responseJson([CODE => SUCCESS, DATA => ['id' => $update_shipping->id]]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);
        }
    }

    public function cancel($order_id = null)
    {
        $this->layout = false;
        $this->autoRender = false;

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $result = $this->loadComponent('Admin.Order')->cancelOrder($order_id);
        
        exit(json_encode($result));
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
        $value = !empty($data['value']) ? $data['value'] : 0;
        $type = !empty($data['type']) ? $data['type'] : '';

        // validate data
        if (empty($id) || empty($type)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $orders_table = TableRegistry::get('Orders');
        if ( !empty($type) && $type == 'note' ) {
            $data_save['note'] = $value;
        }
        if ( !empty($type) && $type == 'staff_note' ) {
            $data_save['staff_note'] = $value;
        }
        $orders = $orders_table->get($id);
        $orders = $orders_table->patchEntity($orders, $data_save);

        try{
            // save data
            $save = $orders_table->save($orders);

            if (empty($save->id)){
                throw new Exception();
            }
            $this->responseJson([CODE => SUCCESS, DATA => ['id' => $save->id]]);

        }catch (Exception $e) {
            $this->responseJson([MESSAGE => $e->getMessage()]);
        }
    }

    public function updateContact($order_id = null)
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        if(empty($data) || !$this->getRequest()->is('post')){
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $full_name = !empty($data['full_name']) ? $data['full_name'] : null;
        $phone = !empty($data['phone']) ? $data['phone'] : null;
        $email = !empty($data['email']) ? $data['email'] : null;

        if(empty($full_name)){
            $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_ten_khach_hang')]);
        }

        if(empty($phone)){
            $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_so_dien_thoai_khach_hang')]);
        }

        if(empty($order_id)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_don_hang')]);
        }

        $order = TableRegistry::get('Orders')->find()->where(['id' => $order_id])->first();
        if(empty($order)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_don_hang')]);
        }

        if(empty($data['contact_id'])){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_lien_he_cua_don_hang')]);
        }

        $table = TableRegistry::get('OrdersContact');
        $contact_info = $table->find()->where(['id' => $data['contact_id']])->first();

        if(empty($contact_info)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_lien_he_cua_don_hang')]);
        }

        $utilities = $this->loadComponent('Utilities');
        $location_component = $this->loadComponent('Location');

        $location = $location_component->getFullAddress([
            'city_id' => !empty($data['city_id']) ? $data['city_id'] : null,
            'district_id' => !empty($data['district_id']) ? $data['district_id'] : null,
            'ward_id' => !empty($data['ward_id']) ? $data['ward_id'] : null,
            'address' => !empty($data['address']) ? $data['address'] : null
        ]);

        $data_save = [
            'full_name' => !empty($data['full_name']) ? $data['full_name'] : null,
            'phone' => !empty($data['phone']) ? $data['phone'] : null,
            'address' => !empty($data['address']) ? $data['address'] : null,
            'country_id' => 1,
            'city_id' => !empty($data['city_id']) ? $data['city_id'] : null,
            'district_id' => !empty($data['district_id']) ? $data['district_id'] : null,
            'ward_id' => !empty($data['ward_id']) ? $data['ward_id'] : null,
            'country_name' => !empty($location['country_name']) ? $location['country_name'] : null,
            'city_name' => !empty($location['city_name']) ? $location['city_name'] : null,
            'district_name' => !empty($location['district_name']) ? $location['district_name'] : null,
            'ward_name' => !empty($location['ward_name']) ? $location['ward_name'] : null,
            'full_address' => !empty($location['full_address']) ? $location['full_address'] : null,
            'search_unicode' => strtolower($utilities->formatSearchUnicode([$full_name, $email, $phone]))
        ];

        $contact = $table->patchEntity($contact_info, $data_save);
        
        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();
            
            $save = $table->save($contact);
            if (empty($save->id)){
                throw new Exception();
            }

            $conn->commit();
            $this->responseJson([CODE => SUCCESS]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function loadCarriesForOrder()
    {
        $this->viewBuilder()->enableAutoLayout(false);

        if (!$this->getRequest()->is('post')) die;
        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        $address_info = !empty($data['address_info']) ? $data['address_info'] : [];
        $carrier_code = !empty($data['carrier_code']) ? $data['carrier_code'] : null;
        $carrier_service_code = !empty($data['carrier_service_code']) ? $data['carrier_service_code'] : null;
        $carrier_service_type_code = !empty($data['carrier_service_type_code']) ? $data['carrier_service_type_code'] : null;
        $carrier_shop_id = !empty($data['carrier_shop_id']) ? $data['carrier_shop_id'] : null;

        $params = [
            'shop_id' => $carrier_shop_id,
            'city_id' => !empty($address_info['city_id']) ? intval($address_info['city_id']) : null,
            'district_id' => !empty($address_info['district_id']) ? intval($address_info['district_id']) : null,
            'ward_id' => !empty($address_info['ward_id']) ? intval($address_info['ward_id']) : null,
            'address' => !empty($address_info['address']) ? $address_info['address'] : null,
            'weight' => !empty($data['weight']) ? $data['weight'] : null,
            'length' => !empty($data['length']) ? $data['length'] : null,
            'width' => !empty($data['width']) ? $data['width'] : null,
            'height' => !empty($data['height']) ? $data['height'] : null
        ];

        $shipping_carries = TableRegistry::get('ShippingsCarrier')->getList();
        if(empty($shipping_carries)) die;

        $shipping_fee = [];
        foreach($shipping_carries as $carrier){
            $code = !empty($carrier['code']) ? $carrier['code'] : null;

            $nh_shipping = new NhShipping($code);
            $carrier_fee = $nh_shipping->calculateFee($params);

            if(empty($carrier_fee[CODE]) || empty($carrier_fee[DATA]) || $carrier_fee[CODE] != SUCCESS) continue;
            $shipping_fee[$code] = $carrier_fee[DATA];
        }    

        $ghn_stores = !empty($shipping_carries[GIAO_HANG_NHANH]['config']['stores']) ? $shipping_carries[GIAO_HANG_NHANH]['config']['stores'] : [];
        $ghtk_stores = !empty($shipping_carries[GIAO_HANG_TIET_KIEM]['config']['stores']) ? $shipping_carries[GIAO_HANG_TIET_KIEM]['config']['stores'] : [];

        $ghn_shop = $ghtk_shop = [];
        if(!empty($ghn_stores)){
            foreach ($ghn_stores as $store_id => $store) {
                $store_phone = !empty($store['phone']) ? $store['phone'] : null;
                $store_address = !empty($store['address']) ? $store['address'] : null;

                $name = [];
                $name[] = $store_address;
                $name[] = $store_phone;
                $name = array_filter($name);
                $name = implode(' - ', $name);
                if(empty($name)) continue;

                $ghn_shop[$store_id] = $name;
            }
        }

        if(!empty($ghtk_stores)){
            foreach ($ghtk_stores as $store_id => $store) {
                $store_phone = !empty($store['phone']) ? $store['phone'] : null;
                $store_address = !empty($store['address']) ? $store['address'] : null;

                $name = [];
                $name[] = $store_address;
                $name[] = $store_phone;
                $name = array_filter($name);
                $name = implode(' - ', $name);
                if(empty($name)) continue;

                $ghtk_shop[$store_id] = $name;
            }
        }

        $this->set('carrier_code', $carrier_code);
        $this->set('carrier_service_code', $carrier_service_code);
        $this->set('carrier_service_type_code', $carrier_service_type_code);
        $this->set('carrier_shop_id', $carrier_shop_id);

        $this->set('ghn_shop', $ghn_shop);
        $this->set('ghtk_shop', $ghtk_shop);
        $this->set('shipping_carries', $shipping_carries);
        $this->set('shipping_fee', $shipping_fee);
        $this->render('element_shipping_carries');
    }
}