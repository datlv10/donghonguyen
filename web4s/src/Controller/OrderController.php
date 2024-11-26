<?php

namespace App\Controller;

use Cake\Event\EventInterface;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;
use App\Lib\Payment\NhPayment;

class OrderController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
    }

	public function cartInfo() 
	{
        $session = $this->request->getSession();

        $cart_info = $session->read(CART);
        if(CURRENCY_CODE_DEFAULT != CURRENCY_CODE){
            $cart_info = $this->loadComponent('OrderFrontend')->formatOrderByCurrency($cart_info);
        }

        $this->set('cart_info', $cart_info);
        $this->set('title_for_layout', __d('template', 'thong_tin_gio_hang'));
    }

    public function orderInfo() 
    {
        $post_ajax = false;
        if ($this->request->is('ajax') && $this->getRequest()->is('post')) $post_ajax = true;

        $session = $this->request->getSession();
        $member_session = $session->read(MEMBER);
        $cart_info = $session->read(CART);
        $shipping_info = $session->read(SHIPPING);

        if($post_ajax){
            $data = !empty($this->request->getData()) ? $this->request->getData() : [];
            $view_reload = !empty($data['view_reload']) ? $data['view_reload'] : null;

            // nếu chưa đăng nhập tài khoản thì cập nhật lại thông tin contact theo data truyền vào
            if(empty($member_session)){
                $data_contact = [
                    'full_name' => !empty($data['full_name']) ? trim($data['full_name']) : null,
                    'email' => !empty($data['email']) ? trim($data['email']) : null,
                    'phone' => !empty($data['phone']) ? trim($data['phone']) : null,
                    'address' => !empty($data['address']) ? trim($data['address']) : null,
                    'city_id' => !empty($data['city_id']) ? intval($data['city_id']) : null,
                    'district_id' => !empty($data['district_id']) ? intval($data['district_id']) : null,
                    'ward_id' => !empty($data['ward_id']) ? intval($data['ward_id']) : null,
                    'note' => !empty($data['note']) ? trim($data['note']) : null
                ];

                $session->write(CONTACT, $data_contact);
            }
        }

        $contact_info = $session->read(CONTACT);

        if(empty($cart_info['items'])){
            return $this->showErrorPage([
                STATUS => 511,
                MESSAGE => __d('template', 'da_het_phien_lam_viec_vui_long_chon_lai_san_pham'),
                'title' => __d('template', 'thong_tin_don_hang')
            ]);
        }        

        $member_info = [];
        if(!empty($member_session['id'])) {
            $member_info = TableRegistry::get('Customers')->getDetailCustomer($member_session['id'], [
                'get_list_address' => true,
                'get_point' => true
            ]);
            $member_info = TableRegistry::get('Customers')->formatDataCustomerDetail($member_info);
        }


        // áp dụng 1 phương thức vận chuyển nếu chưa chọn phương thức nào
        $city_id = !empty($contact_info['city_id']) ? intval($contact_info['city_id']) : null;
        if(empty($city_id) && !empty($member_session)){
            $city_id = !empty($member_session['city_id']) ? intval($member_session['city_id']) : null;
        }
        $total_cart = !empty($cart_info['total']) ? intval($cart_info['total']) : null;

        $shipping_methods = $this->loadComponent('Shipping')->getListShippingMethod($city_id, $total_cart);

        $shipping_method_id = !empty($shipping_info['id']) ? $shipping_info['id'] : null;  
        
        // Cập nhật lại phương thức vận chuyển
        // TH1: có phương thức vận chuyển nhưng chưa chọn || có phương thức vận chuyển và lựa chọn phương thức vận chuyển
        // TH2: khi thay đổi số tiền trong phương thức vận chuyển quản trị cập nhập lại số tiền
        // TH3: xóa phương thức vận chuyển trong quản trị
        if((!empty($shipping_methods) && (empty($shipping_method_id) || empty($shipping_methods[$shipping_method_id]))) || (!empty($shipping_info['fee']) && !empty($shipping_methods[$shipping_method_id]) && $shipping_info['fee'] != $shipping_methods[$shipping_method_id]['fee']) || empty($shipping_methods)){
            $shipping_info = reset($shipping_methods);
            $session->write(SHIPPING, $shipping_info);
        }

        $show_shipping = !empty(TableRegistry::get('ShippingsMethod')->getList(LANGUAGE)) ? true : false;

        $order_info = $this->loadComponent('OrderFrontend')->confirmOrderInfomation();

        $this->set('member_info', $member_info);
        $this->set('order_info', $order_info);
        $this->set('shipping_methods', $shipping_methods);
        $this->set('show_shipping', $show_shipping);

        $this->set('title_for_layout', __d('template', 'thong_tin_don_hang'));

        if ($post_ajax) {
            $this->viewBuilder()->enableAutoLayout(false);
            if(!empty($view_reload)) $this->render($view_reload);
        }
    }

    public function create()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('template', 'phuong_thuc_khong_hop_le')]);
        }

        $result = $this->loadComponent('OrderFrontend')->create($data);
        $this->responseJson($result);        
    }

    public function checkout() 
    {
        $params = $this->request->getQueryParams();

        $code = !empty($params['code']) ? $params['code'] : null;
        if(empty($code)){
            return $this->showErrorPage([
                MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_don_hang'),
                'title' => __d('template', 'thong_tin_don_hang')
            ]);
        }

        // get info order
        $table = TableRegistry::get('Orders');
        $order = $table->findByCode($code)->select('id')->first();
        $order_info = $table->getDetailOrder($order['id'], [
            'get_items' => true,
            'get_contact' => true
        ]);

        $order_info = $table->formatDataOrderDetail($order_info, LANGUAGE);
        if(empty($order_info)){
            return $this->showErrorPage([
                MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_don_hang'),
                'title' => __d('template', 'thong_tin_don_hang')
            ]);
        }

        // format currency
        if(CURRENCY_CODE_DEFAULT != CURRENCY_CODE){
            $order_info = $this->loadComponent('OrderFrontend')->formatOrderByCurrency($order_info);
        }

        // get list payment gateway
        $payment_gateway = TableRegistry::get('PaymentsGateway')->getList(LANGUAGE);
        if(empty($payment_gateway)){
            return $this->showErrorPage([
                MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_cong_thanh_toan'),
                'title' => __d('template', 'thong_tin_don_hang')
            ]);
        }

        // thêm danh sách cổng azpay
        if(!empty($payment_gateway[AZPAY])){
            // one pay
            $payment_gateway[AZPAY . '_' . 1] = [
                'code' => AZPAY . '_' . 1,
                'is_installment' => false,
                'name' => __d('template', 'ten_cong_azpay_01'),
                'name' => __d('template', 'thong_tin_cong_azpay_01'),
            ];

            // onepay trả góp
            // $payment_gateway[AZPAY . '_' . 3] = [
            //     'code' => AZPAY . '_' . 3,
            //     'is_installment' => false,
            //     'name' => __d('template', 'ten_cong_azpay_03'),
            //     'name' => __d('template', 'thong_tin_cong_azpay_03'),
            // ];

            // azpay - momo
            $payment_gateway[AZPAY . '_' . 4] = [
                'code' => AZPAY . '_' . 4,
                'is_installment' => false,
                'name' => __d('template', 'ten_cong_azpay_04'),
                'name' => __d('template', 'thong_tin_cong_azpay_04'),
            ];

            unset($payment_gateway[AZPAY]);
        }

        // lấy mã qr show thanh toán chuyển khoản
        $total = !empty($order_info['total']) ? $order_info['total'] : 0;
        $code = !empty($order_info['code']) ? $order_info['code'] : null;
        if(!empty($payment_gateway[BANK]['config'])) {
            foreach($payment_gateway[BANK]['config'] as $key => $bank) {
                $bank_code = !empty($bank['bank_name']) ? $bank['bank_name'] : null;
                $fields = [
                    'bank' => !empty($bank['bank']) ? $bank['bank'] : null,
                    'bank_name' => !empty($bank['bank_name']) ? $bank['bank_name'] : null,
                    'account' => !empty($bank['account_number']) ? $bank['account_number'] : null,
                    'account_name' => !empty($bank['account_holder']) ? $bank['account_holder'] : null,
                    'amount' => strval($total),
                    'info' => 'Thanh toán đơn hàng ' . $code
                ];

                $result_qr = $this->loadComponent('QrCode')->generateQrCode($fields, BANK_TRANSACTION);
                if(!empty($result_qr[CODE]) && $result_qr[CODE] == SUCCESS && !empty($result_qr[DATA]['url'])) {
                    $payment_gateway[BANK]['config'][$key]['qr'] = $result_qr[DATA]['url'];
                }
            }
        }

        $this->set('order_info', $order_info);
        $this->set('payment_gateway', $payment_gateway);
        $this->set('title_for_layout', __d('template', 'thanh_toan_don_hang'));
    }

    public function processCheckout()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        $result = $this->loadComponent('OrderFrontend')->checkout($data);
        $this->responseJson($result);
    }

    public function success() 
    {
        $params = $this->request->getQueryParams();

        $code = !empty($params['code']) ? $params['code'] : null;
        if(empty($code)){
            return $this->showErrorPage([
                MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_don_hang'),
                'title' => __d('template', 'thong_tin_don_hang')
            ]);
        }
    
        // get info order
        $table = TableRegistry::get('Orders');
        $order = $table->findByCode($code)->select('id')->first();
        if(empty($order['id'])) {
            return $this->showErrorPage([
                MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_don_hang'),
                'title' => __d('template', 'thong_tin_don_hang')
            ]);
        }

        $order_info = $table->getDetailOrder($order['id'], [
            'get_items' => true,
            'get_contact' => true
        ]);

        $order_info = $table->formatDataOrderDetail($order_info, LANGUAGE);
        if(empty($order_info)){
            return $this->showErrorPage([
                MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_don_hang'),
                'title' => __d('template', 'thong_tin_don_hang')
            ]);
        }

        // format currency
        if(CURRENCY_CODE_DEFAULT != CURRENCY_CODE){
            $order_info = $this->loadComponent('OrderFrontend')->formatOrderByCurrency($order_info);
        }

        $this->set('order_info', $order_info);
        $this->set('code', $code);
        $this->set('title_for_layout', __d('template', 'don_hang_thanh_cong'));   
    }

    public function chooseAddress() 
    {
        $this->viewBuilder()->enableAutoLayout(false);

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $result = $this->loadComponent('OrderFrontend')->chooseAddress($data);
        $this->responseJson($result);        
    }

    public function error()
    {
        return $this->showErrorPage([
            STATUS => 511,
            MESSAGE => __d('template', 'da_het_phien_lam_viec_vui_long_chon_lai_san_pham'),
            'title' => __d('template', 'thong_tin_don_hang')
        ]);
    }
}