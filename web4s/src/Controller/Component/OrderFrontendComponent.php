<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;
use App\Lib\Payment\NhPayment;

class OrderFrontendComponent extends Component
{
    public $controller = null;
    public $currency_default = CURRENCY_CODE;

    public $components = ['System', 'Utilities', 'Location', 'Checkout', 'Email', 'PromotionFrontend', 'AffiliateFrontend', 'Shipping', 'SendMessage', 'Admin.Customer', 'Admin.Order', 'Admin.Payment', 'Admin.CustomersPoint'];

    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->controller = $this->_registry->getController();

        $currency_default = TableRegistry::get('Currencies')->getDefaultCurrency();
        $this->currency_default = !empty($currency_default['code']) ? $currency_default['code'] : null;

    }

    // lấy thông tin đơn hàng trước khi khởi tạo
    public function confirmOrderInfomation()
    {
        $session = $this->controller->getRequest()->getSession();

        $cart_info = $session->read(CART);
        $contact_info = $session->read(CONTACT);
        $member_session = $session->read(MEMBER);        
        $coupon_info = $session->read(COUPON);
        $shipping_info = $session->read(SHIPPING);
        $point_info = $session->read(POINT);
        $affiliate_info = $session->read(AFFILIATE);

        if(empty($contact_info['full_name']) && !empty($member_session)) {
            $contact_info = $member_session;
            $session->write(CONTACT, $member_session);
        }

        $total_items = !empty($cart_info['total']) ? floatval($cart_info['total']) : 0;
        $total_coupon = !empty($coupon_info['total']) ? round(floatval($coupon_info['total']), 2) : 0;
        $total_affiliate = !empty($affiliate_info['total_affiliate']) ? round(floatval($affiliate_info['total_affiliate']), 2) : 0;

        $shipping_fee_customer = !empty($shipping_info['fee']) ? floatval($shipping_info['fee']) : 0;

        $total_by_point = !empty($point_info['total_by_point']) ? floatval($point_info['total_by_point']) : 0;
        $total_by_point_promotion = !empty($point_info['total_by_point_promotion']) ? floatval($point_info['total_by_point_promotion']) : 0;

        if(!empty($coupon_info['type_discount']) && $coupon_info['type_discount'] == FREE_SHIP){
            $total_coupon = $shipping_fee_customer;
        }

        $total = $total_items + $shipping_fee_customer - $total_coupon - $total_affiliate;
        if($total < 0) $total = 0;

        $debt = $total - $total_by_point - $total_by_point_promotion;
        if($debt < 0) $debt = 0;
        
        $order_info = [
            'items' => !empty($cart_info['items']) ? $cart_info['items'] : [],
            'contact' => $contact_info,
            'coupon' => $coupon_info,
            'point' => $point_info,
            'affiliate' => $affiliate_info,
            'total_items' => $total_items,
            'total_coupon' => $total_coupon,
            'total_affiliate' => $total_affiliate,
            'shipping_method_id' => !empty($shipping_info['id']) ? intval($shipping_info['id']) : 0,
            'shipping_fee_customer' => $shipping_fee_customer,
            'point_paid' => !empty($point_info['total_by_point']) ? floatval($point_info['total_by_point']) : 0,
            'point_promotion_paid' => !empty($point_info['total_by_point_promotion']) ? floatval($point_info['total_by_point_promotion']) : 0,
            'total' => $total,
            'debt' => $debt
        ];

        if($this->currency_default != CURRENCY_CODE){
            $order_info = $this->formatOrderByCurrency($order_info);
        }

        return $order_info;
    }

    public function formatOrderByCurrency($order_info = [])
    {
        if(empty($order_info)) return [];

        if($this->currency_default == CURRENCY_CODE || empty($order_info['items'])) return $order_info;

        $items = $order_info['items'];
        foreach($items as $k => $item){
            $items[$k]['default_price'] = !empty($item['price']) ? floatval($item['price']) : 0;
            $items[$k]['default_total_item'] = !empty($item['total_item']) ? floatval($item['total_item']) : 0;

            $items[$k]['price'] = $this->formatNumberByCurrentRate($item['price']);
            $items[$k]['total_item'] = $this->formatNumberByCurrentRate($item['total_item']);
            $items[$k][CURRENCY_PARAM] = CURRENCY_CODE;
        }

        $order_info['items'] = $items;
                
        $order_info['total_default'] = !empty($order_info['total']) ? floatval($order_info['total']) : 0;
        $order_info['total'] = !empty($order_info['total']) ? $this->formatNumberByCurrentRate($order_info['total']) : 0;

        $order_info['total_items_default'] = !empty($order_info['total_items']) ? floatval($order_info['total_items']) : 0;
        $order_info['total_items'] = !empty($order_info['total_items']) ? $this->formatNumberByCurrentRate($order_info['total_items']) : 0;

        $order_info['total_coupon_default'] = !empty($order_info['total_coupon']) ? floatval($order_info['total_coupon']) : 0;
        $order_info['total_coupon'] = !empty($order_info['total_coupon']) ? $this->formatNumberByCurrentRate($order_info['total_coupon']) : 0;

        $order_info['total_affiliate_default'] = !empty($order_info['total_affiliate']) ? floatval($order_info['total_affiliate']) : 0;
        $order_info['total_affiliate'] = !empty($order_info['total_affiliate']) ? $this->formatNumberByCurrentRate($order_info['total_affiliate']) : 0;

        $order_info['shipping_fee_customer_default'] = !empty($order_info['shipping_fee_customer']) ? floatval($order_info['shipping_fee_customer']) : 0;
        $order_info['shipping_fee_customer'] = !empty($order_info['shipping_fee_customer']) ? $this->formatNumberByCurrentRate($order_info['shipping_fee_customer']) : 0;

        $order_info['total_vat_default'] = !empty($order_info['total_vat']) ? floatval($order_info['total_vat']) : 0;
        $order_info['total_vat'] = !empty($order_info['total_vat']) ? $this->formatNumberByCurrentRate($order_info['total_vat']) : 0;

        $order_info['point_paid_default'] = !empty($order_info['point_paid']) ? floatval($order_info['point_paid']) : 0;
        $order_info['point_paid'] = !empty($order_info['point_paid']) ? $this->formatNumberByCurrentRate($order_info['point_paid']) : 0;

        $order_info['point_promotion_paid_default'] = !empty($order_info['point_promotion_paid']) ? floatval($order_info['point_promotion_paid']) : 0;
        $order_info['point_promotion_paid'] = !empty($order_info['point_promotion_paid']) ? $this->formatNumberByCurrentRate($order_info['point_paid']) : 0;

        $order_info['debt_default'] = !empty($order_info['debt']) ? floatval($order_info['debt']) : 0;
        $order_info['debt'] = !empty($order_info['debt']) ? $this->formatNumberByCurrentRate($order_info['debt']) : 0;

        return $order_info;
    }

    private function formatNumberByCurrentRate($value = null)
    {
        return !empty($value) ? round(floatval($value / CURRENCY_RATE), 2) : 0;
    }

    // khởi tạo đơn hàng
    public function create($data = [], $options = [])
    {
        if(!$this->controller->getRequest()->is('post')){
           return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $api = !empty($options['api']) ? true : false;

        $session = $this->controller->getRequest()->getSession();

        $contact_info = $session->read(CONTACT);
        $cart_info = $session->read(CART);
        $coupon_info = $session->read(COUPON);
        $shipping_info = $session->read(SHIPPING);
        $point_info = $session->read(POINT);
        $affiliate_info = $session->read(AFFILIATE);

        $city_id = !empty($contact_info['city_id']) ? $contact_info['city_id'] : null;
        if(empty($data['full_name']) && !empty($contact_info)) {
            $data['full_name'] = !empty($contact_info['full_name']) ? $contact_info['full_name'] : null;
            $data['phone'] = !empty($contact_info['phone']) ? $contact_info['phone'] : null;
            $data['address_id'] = !empty($contact_info['address_id']) ? $contact_info['address_id'] : null;
            $data['email'] = !empty($contact_info['email']) ? $contact_info['email'] : null;
            $data['city_id'] = $city_id;
            $data['district_id'] = !empty($contact_info['district_id']) ? $contact_info['district_id'] : null;
            $data['ward_id'] = !empty($contact_info['ward_id']) ? $contact_info['ward_id'] : null;
            $data['address'] = !empty($contact_info['address']) ? $contact_info['address'] : null;
        }

        $full_name = !empty($data['full_name']) ? trim($data['full_name']) : null;
        $email = !empty($data['email']) ? trim($data['email']) : null;
        $phone = !empty($data['phone']) ? trim($data['phone']) : null;
        
        // validate data
        if(empty($full_name)){
            return $this->System->getResponse([MESSAGE => __d('template', 'thong_tin_khach_hang_khong_hop_le')]);
        }
                        
        if(empty($cart_info['items'])){
            return $this->System->getResponse([
                STATUS => 511, 
                MESSAGE => __d('template', 'da_het_phien_lam_viec_vui_long_chon_lai_san_pham')
            ]);
        }
        

        $data_order = [];

        // parse data contact
        $location = $this->Location->getFullAddress([
            'city_id' => !empty($data['city_id']) ? $data['city_id'] : null,
            'district_id' => !empty($data['district_id']) ? $data['district_id'] : null,
            'ward_id' => !empty($data['ward_id']) ? $data['ward_id'] : null,
            'address' => !empty($data['address']) ? $data['address'] : null
        ]);

        $customer_id = !empty($contact_info['customer_id']) ? intval($contact_info['customer_id']) : null;
        $data_order['contact'] = [
            'customer_id' => $customer_id,
            'full_name' => $full_name,
            'email' => $email,
            'phone' => $phone,
            'email' => $email,
            'address_id' => !empty($data['address_id']) ? $data['address_id'] : null,
            'phone' => !empty($data['phone']) ? trim($data['phone']) : null,
            'city_id' => !empty($data['city_id']) ? intval($data['city_id']) : null,
            'district_id' => !empty($data['district_id']) ? intval($data['district_id']) : null,
            'ward_id' => !empty($data['ward_id']) ? intval($data['ward_id']) : null,
            'address' => !empty($data['address']) ? trim($data['address']) : null,
            'country_name' => !empty($location['country_name']) ? $location['country_name'] : null,
            'city_name' => !empty($location['city_name']) ? $location['city_name'] : null,
            'district_name' => !empty($location['district_name']) ? $location['district_name'] : null,
            'ward_name' => !empty($location['ward_name']) ? $location['ward_name'] : null,
            'full_address' => !empty($location['full_address']) ? $location['full_address'] : null
        ];

        // nếu chưa đăng nhập thì kiểm tra email hoặc số điện thoại của khách hàng
        if(empty($customer_id) && !empty($data['phone'])){
            $customer_info = TableRegistry::get('Customers')->find()->where([
                'Customers.deleted' => 0,
                'Customers.phone' => $data['phone']
            ])->select(['id'])->first();
            $customer_id = !empty($customer_info['id']) ? intval($customer_info['id']) : null;
        }

        if(empty($customer_id) && !empty($data['email'])){
            $customer_info = TableRegistry::get('Customers')->find()->where([
                'email' => $data['email'],
                'deleted' => 0
            ])->select(['id'])->first();

            $customer_id = !empty($customer_info['id']) ? intval($customer_info['id']) : null;
        }

        $data_order['contact']['customer_id'] = $customer_id;
        
        // rewrite session contact
        $session->write(CONTACT, $data_order['contact']);


        // thêm khách hàng mới nếu không có customer_id
        $data_customer = [];
        if(empty($customer_id)){
            $data_customer = [
                'full_name' => $full_name,
                'email' => $email,
                'phone' => $phone,
                'Addresses' => 
                [
                    [
                        'name' => __d('template', 'mac_dinh'),
                        'address_name' => __d('template', 'mac_dinh'),
                        'phone' => $phone,
                        'address' => !empty($data['address']) ? $data['address'] : null,
                        'country_id' => !empty($data['country_id']) ? $data['country_id'] : null,
                        'city_id' => !empty($data['city_id']) ? $data['city_id'] : null,
                        'district_id' => !empty($data['district_id']) ? $data['district_id'] : null,
                        'ward_id' => !empty($data['ward_id']) ? $data['ward_id'] : null,                
                        'country_name' => !empty($location['country_name']) ? $location['country_name'] : null,
                        'city_name' => !empty($location['city_name']) ? $location['city_name'] : null,
                        'district_name' => !empty($location['district_name']) ? $location['district_name'] : null,
                        'ward_name' => !empty($location['ward_name']) ? $location['ward_name'] : null,
                        'full_address' => !empty($location['full_address']) ? $location['full_address'] : null,
                        'is_default' => 1,
                        'search_unicode' => strtolower($this->Utilities->formatSearchUnicode([$phone]))
                    ]
                ]
            ];
        }

        // parse data items
        $items = [];
        foreach ($cart_info['items'] as $product_item_id => $item) {
            $item_info = TableRegistry::get('ProductsItem')->getDetailProductItem($product_item_id, LANGUAGE, ['get_attribute' => true]);
            if(empty($item_info)) {
                return $this->System->getResponse([MESSAGE => __d('template', 'khong_tim_thay_thong_tin_san_pham')]);
            }
            
            $price_item = $price = !empty($item_info['price']) ? floatval($item_info['price']) : 0;
            $price_special = !empty($item_info['price_special']) ? floatval($item_info['price_special']) : 0;
            $apply_special = !empty($item_info['apply_special']) ? true : false;
            $discount_value = 0;
            $discount_type = null;

            if($apply_special){
                $price_item = $price_special;
                $discount_value = $price - $price_special;
                $discount_type = MONEY;
            }

            $items[] = [
                'product_item_id' => !empty($item_info['product_item_id']) ? intval($item_info['product_item_id']) : null,
                'product_id' => !empty($item_info['product_id']) ? intval($item_info['product_id']) : null,
                'quantity' => !empty($item['quantity']) ? intval($item['quantity']) : 1, // lưu ý giá trị quantity phải lấy từ $item
                'price' => $price_item,
                'discount_type' => $discount_type,
                'vat_value' => 0
            ];
        }

        $data_order['items'] = $items;
        $data_order['type'] = ORDER;
        $data_order['source'] = WEBSITE;
        $data_order['shipping_method_id'] = !empty($data['shipping_method_id']) ? intval($data['shipping_method_id']) : null;
        $data_order['note'] = !empty($data['note']) ? $data['note'] : null;
        $data_order['coupon_code'] = !empty($data['coupon_code']) ? $data['coupon_code'] : null;
        $data_order['status'] = DRAFT;


        //shipping
        $shipping_method_id = !empty($shipping_info['id']) ? intval($shipping_info['id']) : 0;
        $total_cart = !empty($cart_info['total']) ? intval($cart_info['total']) : null;
        $shipping_methods = $this->Shipping->getListShippingMethod($city_id, $total_cart);
        // kiểm tra phí vận chuyển có thay đổi hay không?
        if((!empty($shipping_info['fee']) && !empty($shipping_methods[$shipping_method_id ]['fee']) && $shipping_info['fee'] != $shipping_methods[$shipping_method_id ]['fee']) || (empty($shipping_methods) && !empty($shipping_info))) {
            return $this->System->getResponse([MESSAGE => __d('template', 'phuong_thuc_van_chuyen_da_thay_doi_vui_long_tai_lai_trang_de_cap_nhap_lai_phuong_thuc_van_chuyen')]);
        }

        if(!empty($shipping_info)){
            $data_order['shipping_method_id'] = !empty($shipping_info['id']) ? intval($shipping_info['id']) : null;
            $data_order['shipping_fee_customer'] = !empty($shipping_info['fee']) ? intval($shipping_info['fee']) : null;
        }

        // coupon
        if(!empty($coupon_info)) {
            $max_value = !empty($coupon_info['value']['max_value']) ? $coupon_info['value']['max_value'] : null;
            $discount_type = !empty($coupon_info['value']['type_value_discount']) ? $coupon_info['value']['type_value_discount'] : null;
            $discount_value = !empty($coupon_info['value']['value_discount']) ? $coupon_info['value']['value_discount'] : null;
            switch($coupon_info['type_discount']){
                case DISCOUNT_PRODUCT:
                    $total_discount = 0;
                    foreach($data_order['items'] as $key => $item){
                        $product_item_id = !empty($item['product_item_id']) ? intval($item['product_item_id']) : null;
                        if(!in_array($product_item_id, $coupon_info['apply_item_ids'])) continue;

                        $item['discount_value'] = $discount_value;
                        $item['discount_type'] = $discount_type;

                        $quantity = !empty($item['quantity']) ? intval($item['quantity']) : 1;
                        $price = !empty($item['price']) ? floatval($item['price']) : 0;

                        $item_total_origin = $quantity * $price;  
                        $total_discount_item = 0;
                        if(!empty($discount_type) && $discount_type == PERCENT){
                            $total_discount_item = ($item_total_origin / 100) * $discount_value;
                        }else{
                            $total_discount_item = $discount_value;
                        }

                        if(!empty($max_value) && $max_value < $total_discount_item){
                            $total_discount_item = $max_value;

                            $item['discount_value'] = $max_value;
                            $item['discount_type'] = MONEY;
                        }

                        $data_order['items'][$key] = $item;

                        $total_discount += $total_discount_item;
                    }

                    $data_order['discount_value'] = $total_discount;
                    $data_order['discount_type'] = MONEY;
                break;

                case DISCOUNT_ORDER:                
                    $data_order['discount_value'] = $discount_value;
                    $data_order['discount_type'] = $discount_type;

                    $total_items = 0;
                    foreach($data_order['items'] as $key => $item){
                        $quantity = !empty($item['quantity']) ? intval($item['quantity']) : 1;
                        $price = !empty($item['price']) ? floatval($item['price']) : 0;

                        $total_item = $quantity * $price;  
                        $total_items += $total_item;
                    }

                    $total_discount = 0;
                    if($discount_type == PERCENT){
                        $total_discount = $total_items  * $discount_value / 100;
                    }else{
                        $total_discount = $discount_value;
                    }

                    if(!empty($max_value) && $max_value < $total_discount){
                        $item['discount_value'] = $max_value;
                        $item['discount_type'] = MONEY;
                    }
                break;

                case FREE_SHIP:
                    $shipping_fee_customer = !empty($data_order['shipping_fee_customer']) ? floatval($data_order['shipping_fee_customer']) : null;

                    if(!empty($max_value) && $max_value < $shipping_fee_customer){
                        $shipping_fee_customer = $shipping_fee_customer - $max_value;
                    }else{
                        $shipping_fee_customer = 0;
                    }
                    $data_order['shipping_fee_customer'] = $shipping_fee_customer;
                break;
            }

            $data_order['promotion_id'] = !empty($coupon_info['promotion_id']) ? intval($coupon_info['promotion_id']) : null;
            $data_order['coupon_code'] = !empty($coupon_info['coupon']) ? $coupon_info['coupon'] : null;
            $data_order['total_coupon'] = !empty($coupon_info['total']) ? $coupon_info['total'] : null;
        }
        
        // affiliate_info
        if(!empty($affiliate_info)) {
            $data_order['affiliate_discount_type'] = !empty($affiliate_info['affiliate_discount_type']) ? $affiliate_info['affiliate_discount_type'] : null;
            $data_order['affiliate_discount_value'] = !empty($affiliate_info['affiliate_discount_value']) ? $affiliate_info['affiliate_discount_value'] : null;
            $data_order['affiliate_code'] = !empty($affiliate_info['affiliate_code']) ? $affiliate_info['affiliate_code'] : null;
            $data_order['total_affiliate'] = !empty($affiliate_info['total_affiliate']) ? $affiliate_info['total_affiliate'] : null;
        }

        // point
        if(!empty($point_info)){
            $data_order['point'] = !empty($point_info['point']) ? intval($point_info['point']) : null;
            $data_order['point_promotion'] = !empty($point_info['point_promotion']) ? intval($point_info['point_promotion']) : null;
            $data_order['point_paid'] = !empty($point_info['total_by_point']) ? floatval($point_info['total_by_point']) : null;
            $data_order['point_promotion_paid'] = !empty($point_info['total_by_point_promotion']) ? floatval($point_info['total_by_point_promotion']) : null;
        }
        
        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            // create customer before create order
            if(!empty($data_customer)){                
                $create_customer = $this->Customer->saveCustomer($data_customer);
                if(empty($create_customer[CODE]) || $create_customer[CODE] == ERROR){
                    return $this->System->getResponse([MESSAGE => !empty($create_customer[MESSAGE]) ? $create_customer[MESSAGE] : null]);
                }
                $customer_info = !empty($create_customer[DATA]) ? $create_customer[DATA] : [];
                $data_order['contact']['customer_id'] = !empty($customer_info['id']) ? intval($customer_info['id']) : null;
            }

            $create_order = $this->Order->saveOrder($data_order);
            if(empty($create_order[CODE]) || $create_order[CODE] == ERROR){
                return $this->System->getResponse([MESSAGE => !empty($create_order[MESSAGE]) ? $create_order[MESSAGE] : null]);
            }   
            
            $order_info = !empty($create_order[DATA]) ? $create_order[DATA] : [];
            $order_id = !empty($order_info['id']) ? intval($order_info['id']) : null;
            $total = !empty($order_info['total']) ? floatval($order_info['total']) : 0;
            $paid = !empty($order_info['paid']) ? floatval($order_info['paid']) : 0;
            $debt = !empty($order_info['debt']) ? floatval($order_info['debt']) : 0;

            // cập nhật thông tin coupon
            if(!empty($coupon_info)) {
                $update_coupon = $this->PromotionFrontend->updateUsedPromotion($data_order['coupon_code'], $data_order['promotion_id']);
                if(empty($update_coupon[CODE]) || $update_coupon[CODE] != SUCCESS){
                    throw new Exception(!empty($update_coupon[MESSAGE]) ? $update_coupon[MESSAGE] : null);
                }
            }

            if(!empty($order_id) && !empty($order_info['affiliate_code'])){
                //cập nhật thông tin number_referral trong bảng customer affiliate
                $save_affiliate = $this->AffiliateFrontend->saveAffiliate();
                
                if(empty($save_affiliate[CODE]) || $save_affiliate[CODE] != SUCCESS){
                    throw new Exception(__d('template', 'khong_luu_duoc_thong_tin_doi_tac'));
                }
           

                //cập nhật thông tin affiliate order table
                $save_affiliate_order = $this->AffiliateFrontend->saveAffiliateOrder($order_id, $total);
                if(empty($save_affiliate_order[CODE]) || $save_affiliate_order[CODE] != SUCCESS){
                    throw new Exception(__d('template', 'khong_luu_duoc_thong_tin_doi_tac'));
                }
      
                //cập nhật hạng cho đối tác
                $save_level_for_partner =  $this->Customer->saveLevelForPartner($order_info['affiliate_code']);
                if(empty($save_level_for_partner[CODE]) || $save_level_for_partner[CODE] != SUCCESS){
                    throw new Exception(__d('template', 'khong_luu_duoc_hang_cua_doi_tac'));
                }
            }

            //cập nhật số điểm khách hàng sau khi tạo đơn
            $customer_id = !empty($order_info['OrdersContact']['customer_id']) ? intval($order_info['OrdersContact']['customer_id']) : null;
            if(!empty($order_info['point'])){        
                $data_point = [
                    'customer_id' => $customer_id,
                    'point' => intval($order_info['point']),
                    'point_type' => 1, // 1-> điểm trong ví
                    'action' => 0, // 0 -> trừ điểm
                    'action_type' => ORDER
                ];

                $update_point = $this->CustomersPoint->saveCustomerPointHistory($data_point);

                if(empty($update_point[CODE]) || $update_point[CODE] != SUCCESS){
                    throw new Exception(!empty($update_point[MESSAGE]) ? $update_point[MESSAGE] : null);
                }
            }

            if(!empty($order_info['point_promotion'])){
                $data_point = [
                    'customer_id' => $customer_id,
                    'point' => intval($order_info['point_promotion']),
                    'point_type' => 0, // 0-> điểm thưởng
                    'action' => 0, // 0 -> trừ điểm
                    'action_type' => ORDER
                ];
                $update_point = $this->CustomersPoint->saveCustomerPointHistory($data_point);

                if(empty($update_point[CODE]) || $update_point[CODE] != SUCCESS){
                    throw new Exception(!empty($update_point[MESSAGE]) ? $update_point[MESSAGE] : null);
                }
            }

            $conn->commit();

            // nếu đơn hàng đã được thanh toán hết thì thực hiện cập nhật luôn trạng thái đơn hàng -> new
            // trường hợp này xảy ra khi áp dụng thanh toán bằng điểm
            if($total == $paid && empty($debt)){
                $table = TableRegistry::get('Orders');
                // update status order
                $order = $table->find()->where(['id' => $order_id])->select(['id', 'code', 'status'])->first();
                $entity_order = $table->patchEntity($order, [
                    'status' => NEW_ORDER
                ]);

                $update_order = $table->save($entity_order);
                if (empty($update_order->id)){
                    throw new Exception(__d('template', 'khong_cap_nhat_duoc_thong_tin_don_hang'));
                }

                // cập nhật lại số lượng sản phẩm sau khi tạo đơn hàng mới thành công
                $update_quantity_available = $this->Order->updateQuantityAvailableOfProduct($order_id);
                if(!$update_quantity_available){
                    throw new Exception(__d('template', 'khong_cap_nhat_duoc_thong_tin_don_hang'));
                }

                //send email
                $email_contact = !empty($order_info['OrdersContact']['email']) ? $order_info['OrdersContact']['email'] : null;
                if(!empty($email_contact)){
                    $params_email = [
                        'to_email' => $email_contact,
                        'code' => 'ORDER',
                        'id_record' => $order_id,
                        'send_try_content' => false,
                        'from_website_template' => !empty($api) ? true : false
                    ];

                    $this->Email->send($params_email);
                }
            }
            
            $session->delete(CART);
            $session->delete(CONTACT);
            $session->delete(COUPON);
            $session->delete(SHIPPING);
            $session->delete(POINT);
            $session->delete(AFFILIATE);

            return $this->System->getResponse([
                CODE => SUCCESS,
                MESSAGE => __d('template', 'tao_don_hang_thanh_cong'),
                DATA => [
                    'id' => $order_id,
                    'code' => !empty($order_info['code']) ? $order_info['code'] : null,
                    'total' => $total,
                    'paid' => $paid,
                    'debt' => $debt
                ]                
            ]);

        }catch (Exception $e) {
            $conn->rollback();
            return $this->System->getResponse([MESSAGE => $e->getMessage()]);  
        }
    }

    public function checkout($data = [], $options = [])
    {
        if(!$this->controller->getRequest()->is('post') || empty($data)){
           return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $api = !empty($options['api']) ? true : false;

        $code = !empty($data['code']) ? $data['code'] : null;
        $type_os = !empty($data['type_os']) ? $data['type_os'] : null;
        if(empty($code)){
            return $this->System->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_don_hang')]);
        }

        $payment_gateway = !empty($data['payment_gateway']) ? $data['payment_gateway'] : null;
        if(empty($payment_gateway)){
            return $this->System->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_cong_thanh_toan')]);   
        }

        // kiểm tra riêng với cổng azpay
        $sub_method = null;
        if(strpos($payment_gateway, AZPAY) > -1){
            $split = explode('_', $payment_gateway);
            $sub_method = !empty($split[1]) ? $split[1] : null;
            $payment_gateway = !empty($split[0]) ? $split[0] : null;
        }

        $table = TableRegistry::get('Orders');

        $order = $table->findByCode($code)->select('id')->first();        
        $order_id = !empty($order['id']) ? intval($order['id']) : null;
        if(empty($order_id)){
            return $this->System->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_don_hang')]);
        }

        $order_info = $table->getDetailOrder($order_id, [
            'get_contact' => true
        ]);
        $order_info = $table->formatDataOrderDetail($order_info, LANGUAGE);
        if(empty($order_info)){
            return $this->System->getResponse([MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_don_hang')]);
        }

        $status = !empty($order_info['status']) ? $order_info['status'] : null;
        if($status != DRAFT){
            return $this->System->getResponse([MESSAGE => __d('template', 'don_hang_nay_da_xac_nhan_thanh_toan')]);
        }

        $data_payment = [
            'foreign_id' => $order_id,
            'foreign_type' => ORDER,
            'type' => 1, // 0 => CHI, 1 => THU
            'object_type' => CUSTOMER,
            'payment_method' => $payment_gateway == COD ? COD : BANK,
            'sub_method' => $sub_method,
            'object_id' => !empty($order_info['contact']['customer_id']) ? intval($order_info['contact']['customer_id']) : null,
            'amount' => !empty($order_info['debt']) ? floatval($order_info['debt']) : 0,                    
            'full_name' => !empty($order_info['contact']['full_name']) ? $order_info['contact']['full_name'] : null,
            'status' => 2
        ];

        if(!in_array($payment_gateway, [COD, BANK])){
            $data_payment['payment_gateway_code'] = $payment_gateway;
        }
        
        $send_email = false;
        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            // create payment transaction for order
            $create_payment = $this->Payment->savePayment($data_payment, null);
            if($create_payment[CODE] == ERROR){
                throw new Exception(!empty($create_payment[MESSAGE]) ? $create_payment[MESSAGE] : null);
            }

            $payment_info = !empty($create_payment[DATA]) ? $create_payment[DATA] : [];
            $payment_code = !empty($payment_info[CODE]) ? $payment_info[CODE] : null;

            // send to gateway payment
            $url = $app_pay_url = null;
            if(!in_array($payment_gateway, [COD, BANK])){
                $checkout = $this->Checkout->checkoutByGateway($payment_code, [
                    'api' => !empty($options['api']) ? true : false,
                    'type_os' => $type_os
                ]);
                
                if($checkout[CODE] == ERROR){
                    throw new Exception(!empty($checkout[MESSAGE]) ? $checkout[MESSAGE] : null);
                }
                $url = !empty($checkout[DATA]['url']) ? $checkout[DATA]['url'] : null;
                $app_pay_url = !empty($checkout[DATA]['app_pay_url']) ? $checkout[DATA]['app_pay_url'] : null;
            }
            else{
                // update status order
                $entity_order = $table->patchEntity($order, [
                    'status' => NEW_ORDER
                ]);

                $update_order = $table->save($entity_order);
                if (empty($update_order->id)){
                    throw new Exception(__d('template', 'khong_cap_nhat_duoc_thong_tin_don_hang'));
                }

                // update quantity product after create order
                $update_quantity_available = $this->Order->updateQuantityAvailableOfProduct($update_order->id);
                if(!$update_quantity_available){
                    throw new Exception(__d('template', 'khong_cap_nhat_duoc_thong_tin_don_hang'));
                }

                $send_email = true;
            }

            $conn->commit();

            //send email
            if($send_email){
                $email_contact = !empty($order_info['contact']['email']) ? $order_info['contact']['email'] : null;
                if(!empty($email_contact)){
                    $params_email = [
                        'to_email' => $email_contact,
                        'code' => 'ORDER',
                        'id_record' => $order_info['id'],
                        'send_try_content' => false,
                        'from_website_template' => $api
                    ];

                    $this->Email->send($params_email);
                }
            } 

            //send message
            $this->SendMessage->send(ORDER, $order_info['id'], LANGUAGE);           
            

            $session = $this->controller->getRequest()->getSession();
            $session->delete(CART);
            $session->delete(CONTACT);
            $session->delete(COUPON);
            $session->delete(SHIPPING);
            $session->delete(POINT);
            $session->delete(AFFILIATE);

            return $this->System->getResponse([
                CODE => SUCCESS, 
                MESSAGE => __d('template', 'xu_ly_thanh_toan_don_hang_thanh_cong'), 
                DATA => [
                    'code' => $code,
                    'url' => $url,
                    'app_pay_url' => $app_pay_url
                ]
            ]);
        }catch (Exception $e) {
            $conn->rollback();
            return $this->System->getResponse([MESSAGE => $e->getMessage()]);
        }
    }

    public function chooseAddress($data = [], $options = [])
    {
        if(!$this->controller->getRequest()->is('post') || empty($data)){
           return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $address_id = !empty($data['address_id']) ? intval($data['address_id']) : null;
        if(empty($address_id)){
            return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $session = $this->controller->getRequest()->getSession();
        $member = $session->read(MEMBER);

        $customer_id = !empty($member['id']) ? $member['id'] : null;
        if(empty($member) || empty($customer_id)){
            return $this->System->getResponse([
                STATUS => 511, 
                MESSAGE => __d('template', 'da_het_phien_lam_viec_vui_long_dang_nhap_lai')
            ]);
        }
                
        $address_info = TableRegistry::get('CustomersAddress')->find()->where(['id' => $address_id])->select(['customer_id'])->first();
        if(empty($address_info['customer_id']) || intval($address_info['customer_id']) != $customer_id){
            return $this->System->getResponse([
                MESSAGE => __d('template', 'khong_lay_duoc_thong_tin_dia_chi')
            ]);
        }
        
        $member_info = TableRegistry::get('Customers')->getDetailCustomer($customer_id, ['address_id' => $address_id]);
        $contact_info = TableRegistry::get('Customers')->formatDataCustomerDetail($member_info);

        $session->write(CONTACT, $contact_info);

        return $this->System->getResponse([
            CODE => SUCCESS, 
            MESSAGE => __d('template', 'cap_nhat_dia_chi_don_hang_thanh_cong')
        ]);
    }

}
