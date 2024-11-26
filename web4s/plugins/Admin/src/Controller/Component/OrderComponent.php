<?php

namespace Admin\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;
use Cake\Core\Configure;
use Cake\Utility\Hash;

class OrderComponent extends AppComponent
{
	public $controller = null;
    public $components = ['System', 'Utilities', 'Admin.Payment', 'Admin.Shipping', 'Admin.Order', 'Admin.CustomersPoint', 'AffiliateFrontend', 'Admin.StoreKiotViet'];

    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->controller = $this->_registry->getController();
    }  

    public function saveOrder($data = [], $id = null)
    {

        $orders_table = TableRegistry::get('Orders');
        $orders_item_table = TableRegistry::get('OrdersItem');

        // validate data
        $validate = $this->validationData($data);
        if(!empty($validate[CODE]) && $validate[CODE] == ERROR){
            return $validate;
        }

        // format data 
        $data_format = $this->formatDataBeforeSave($data, $id);
        $data_save = $data_format['order'];

        $data_save['OrdersItem'] = $data_format['items'];
        $data_save['OrdersContact'] = $data_format['contact'];

        $data_payment = !empty($data_format['payment']) ? $data_format['payment'] : [];
        $data_payment_cod = !empty($data_format['payment_cod']) ? $data_format['payment_cod'] : [];
        $data_shipping = !empty($data_format['shipping']) ? $data_format['shipping'] : [];

        // merge data with entity
        if(empty($id)){
            $code = !empty($data['code']) ? trim(strtoupper($data['code'])) : null;

            // generate code of order
            if (empty($code)) {
                switch ($data_save['type']) {
                    case ORDER_RETURN:
                        $code = 'REC' . $this->Utilities->generateRandomNumber(7);
                        break;
                    case ORDER:
                    default:
                        $code = 'ORD' . $this->Utilities->generateRandomNumber(7);
                        break;
                }
            }
            
            $data_save['code'] = $code;
            $order = $orders_table->newEntity($data_save, [
                'associated' => ['OrdersItem', 'OrdersContact']
            ]);
        }else{
            $order = $orders_table->getDetailOrder($id, [
                'get_items' => true,
                'get_contact' => true
            ]);

            $order = $orders_table->patchEntity($order, $data_save);

            // get old order item
            $old_items_id = $orders_item_table->find()->where(['order_id' => $id])->select('id')->toArray();

            // get new order item
            $new_items_id = [];
            if(!empty($data_save['OrdersItem'])){
                foreach($data_save['OrdersItem'] as $item){
                    if(!empty($item['id'])){
                        $new_items_id[] = intval($item['id']);
                    }                    
                }
            }

            // get clear order item
            $clear_items_id = [];
            foreach($old_items_id as $old){
                if(!in_array($old->id, $new_items_id)){
                    $clear_items_id[] = $old->id;
                }
            }
        }

        // show error validation in model
        if($order->hasErrors()){
            $list_errors = $this->Utilities->errorModel($order->getErrors());            
            return $this->System->getResponse([
                MESSAGE => !empty($list_errors[0]) ? $list_errors[0] : null,
                DATA => $list_errors
            ]);
        }

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            // clear old item
            if(!empty($id) && !empty($clear_items_id)){
                $clear_order_item = $orders_item_table->deleteAll(['id IN' => $clear_items_id]);
            }

            // save data
            $save = $orders_table->save($order);
            if (empty($save->id)){
                throw new Exception(__d('admin', 'luu_thong_tin_don_hang_khong_thanh_cong'));
            }            

            // save payment
            if(empty($id) && !empty($data_payment)){
                $data_payment['foreign_id'] = $save->id;
                $data_payment['foreign_type'] = ORDER;
                $save_payment = $this->Payment->savePayment($data_payment);
 
                if($save_payment[CODE] == ERROR){
                    $message_payment = !empty($save_payment[MESSAGE]) ? $save_payment[MESSAGE] : __d('admin', 'luu_thong_tin_thanh_toan_khong_thanh_cong');
                    throw new Exception($message_payment);
                }
            }

            if(empty($id) && !empty($data_payment_cod) && empty($data_payment)){
                $data_payment_cod['foreign_id'] = $save->id;
                $data_payment_cod['foreign_type'] = ORDER;
                $save_payment_cod = $this->Payment->savePayment($data_payment_cod);
    
                if($save_payment_cod[CODE] == ERROR){
                    $message_payment = !empty($save_payment_cod[MESSAGE]) ? $save_payment_cod[MESSAGE] : __d('admin', 'luu_thong_tin_thanh_toan_khong_thanh_cong');
                    throw new Exception($message_payment);
                }
            }

            // save shipping
            if(empty($id) && !empty($data_shipping)){
                $data_shipping['order_id'] = $save->id;
                $save_shipping = $this->Shipping->saveShipping($data_shipping);
                if($save_shipping[CODE] == ERROR){
                    $message_shipping = !empty($save_shipping[MESSAGE]) ? $save_shipping[MESSAGE] : __d('admin', 'luu_thong_tin_van_chuyen_khong_thanh_cong');
                    throw new Exception($message_shipping);
                }
            }

            // update quantity product after update order
            $update_quantity_available = $this->updateQuantityAvailableOfProduct($save->id);
            if(!$update_quantity_available){
                throw new Exception(__d('admin', 'cap_nhat_so_luong_san_pham_khong_thanh_cong'));
            }

            // cập nhật số điểm khách hàng sau khi tạo đơn
            // kiểm tra trạng thái đơn hàng nếu đơn hàng là đơn mới thì ms thực hiện điều chỉnh điểm của khách hàng
            $status = !empty($save['status']) ? $save['status'] : null;
            $customer_id = !empty($save['OrdersContact']['customer_id']) ? intval($save['OrdersContact']['customer_id']) : null;
            if(!empty($save['point']) && !empty($status) && $status == NEW_ORDER){        
                $data_point = [
                    'customer_id' => $customer_id,
                    'point' => intval($save['point']),
                    'point_type' => 1, // 1-> điểm trong ví
                    'action' => 0, // 0 -> trừ điểm
                    'action_type' => ORDER
                ];

                $update_point = $this->CustomersPoint->saveCustomerPointHistory($data_point);

                if(empty($update_point[CODE]) || $update_point[CODE] != SUCCESS){
                    throw new Exception(!empty($update_point[MESSAGE]) ? $update_point[MESSAGE] : null);
                }
            }

            if(!empty($save['point_promotion'])  && !empty($status) && $status == NEW_ORDER){
                $data_point = [
                    'customer_id' => $customer_id,
                    'point' => intval($save['point_promotion']),
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

            // sau khi tạo đơn hàng nếu có tạo đơn gửi hãng vận chuyển thì tạo và cập nhật mã đơn vận
            if(!empty($save_shipping[CODE]) && $save_shipping[CODE] == SUCCESS && !empty($save_shipping[DATA]['carrier_code'])){
                $shipping_id = !empty($save_shipping[DATA]['id']) ? $save_shipping[DATA]['id'] : null;
                $send_to_carrier = $this->Shipping->sendOrderToCarrier($shipping_id);                
                if(!empty($send_to_carrier[CODE]) && $send_to_carrier[CODE] == ERROR){
                    $carrier_message = !empty($send_to_carrier[MESSAGE]) ? ' - ' . $send_to_carrier[MESSAGE] : '';
                    return $this->System->getResponse([
                        MESSAGE => __d('admin', 'gui_don_sang_hang_van_chuyen_khong_thanh_cong') . $carrier_message
                    ]);
                }
                // cập nhật thông tin vận đơn
                if(!empty($send_to_carrier[CODE]) && $send_to_carrier[CODE] == SUCCESS){
                    $shippings_table = TableRegistry::get('Shippings');
                    $shipping_info = $shippings_table->find()->where(['id' => $shipping_id])->select(['id', 'carrier_order_code', 'carrier_shipping_fee'])->first();

                    $carrier_order_code = !empty($send_to_carrier[DATA]['carrier_order_code']) ? $send_to_carrier[DATA]['carrier_order_code'] : null;
                    $carrier_shipping_fee = !empty($send_to_carrier[DATA]['carrier_shipping_fee']) ? $send_to_carrier[DATA]['carrier_shipping_fee'] : null;

                    $entity = $shippings_table->patchEntity($shipping_info, [
                        'carrier_order_code' => $carrier_order_code,
                        'carrier_shipping_fee' => $carrier_shipping_fee,
                        'shipping_fee' => $carrier_shipping_fee
                    ]);
                    $update_shipping = $shippings_table->save($entity);
                }

            }

            // kiểm tra có sử dụng kho kiotviet không
            $settings = TableRegistry::get('Settings')->getSettingWebsite();
            $store_kiotviet = !empty($settings['store_kiotviet']) ? $settings['store_kiotviet'] : [];    
            $config_kiotviet = !empty($store_kiotviet['config']) ? json_decode($store_kiotviet['config'], true) : [];
            $use_kiotviet = !empty($config_kiotviet['status']) ? 1 : 0;
            // gửi đơn sang kiotviet
            if(!empty($use_kiotviet)){
                $this->StoreKiotViet->syncOrder($code);
            }

            return $this->System->getResponse([CODE => SUCCESS, DATA => $save]);
        }catch (Exception $e) {
            $conn->rollback();
            return $this->System->getResponse([MESSAGE => $e->getMessage()]);
        }
    }

    public function validationData($data = [])
    {
        if(empty($data['type']) || (!empty($data['type']) && !in_array($data['type'], Configure::read('LIST_TYPE_ORDER')))){
            return $this->System->getResponse([MESSAGE => __d('admin', 'loai_don_khong_hop_le')]);
        }

        if($data['type'] == ORDER_RETURN && empty($data['related_order_id'])){
            return $this->System->getResponse([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_hoa_don_lien_quan')]);   
        }

        if(empty($data['status']) || (!empty($data['status']) && !in_array($data['status'], Configure::read('LIST_STATUS_ORDER')))){
            return $this->System->getResponse([MESSAGE => __d('admin', 'trang_thai_khong_hop_le')]);
        }

        if(empty($data['items'])){
            return $this->System->getResponse([MESSAGE => __d('admin', 'vui_long_chon_san_pham')]);
        }

        if(empty($data['contact'])){
            return $this->System->getResponse([MESSAGE => __d('admin', 'vui_long_chon_khach_hang')]);
        }

        foreach ($data['items'] as $key => $item) {
            $product_item_id = !empty($item['product_item_id']) ? intval($item['product_item_id']) : null;
            $product_id = !empty($item['product_id']) ? intval($item['product_id']) : null;

            if(empty($product_id) || empty($product_item_id)){
                return $this->System->getResponse([MESSAGE => __d('admin', 'id_san_pham_khong_hop_le')]);       
            }
        }

        return true;
    }

    public function formatDataBeforeSave($data = [], $id = null)
    {
        if(empty($data)) return [];
        $result = [];
        // order contact
        $orders_contact = [];

        $contact = !empty($data['contact']) ? $data['contact'] : [];        
        if(!empty($contact)){
            $full_name = !empty($contact['full_name']) ? $contact['full_name'] : null;
            $email = !empty($contact['email']) ? $contact['email'] : null;
            $phone = !empty($contact['phone']) ? $contact['phone'] : null;

            $orders_contact = [
                'customer_id' => !empty($contact['customer_id']) ? intval($contact['customer_id']) : null,
                'full_name' => $full_name,
                'address_name' => !empty($contact['address_name']) ? $contact['address_name'] : null,
                'email' => $email,
                'phone' => $phone,
                'address' => !empty($contact['address']) ? $contact['address'] : null,
                'full_address' => !empty($contact['full_address']) ? $contact['full_address'] : null,
                'country_id' => !empty($contact['country_id']) ? $contact['country_id'] : null,
                'city_id' => !empty($contact['city_id']) ? $contact['city_id'] : null,
                'district_id' => !empty($contact['district_id']) ? $contact['district_id'] : null,
                'ward_id' => !empty($contact['ward_id']) ? $contact['ward_id'] : null,
                'country_name' => !empty($contact['country_name']) ? $contact['country_name'] : null,
                'city_name' => !empty($contact['city_name']) ? $contact['city_name'] : null,
                'district_name' => !empty($contact['district_name']) ? $contact['district_name'] : null,
                'ward_name' => !empty($contact['ward_name']) ? $contact['ward_name'] : null,
                'search_unicode' => strtolower($this->Utilities->formatSearchUnicode([$full_name, $email, $phone]))
            ];
        }
        
        // orders item
        $items = !empty($data['items']) ? $data['items'] : [];

        $orders_item = [];
        $number_items = $count_items = $total_discount_items = $total_origin = $total_vat = $total_items =  0; 

        foreach($items as $k => $item){
            $product_item_id = !empty($item['product_item_id']) ? intval($item['product_item_id']) : null;
            $product_id = !empty($item['product_id']) ? intval($item['product_id']) : null;

            $quantity = !empty($item['quantity']) ? intval($item['quantity']) : 1;

            // giá ở đây là giá đã được trừ khuyến mãi và VAT rồi
            $price = !empty($item['price']) ? floatval($item['price']) : 0;
            $price_origin = $price;
            $total_item = $quantity * $price;

            // tính giá sau vat
            $total_vat_item = 0;
            $vat_value_item = !empty($item['vat_value']) ? floatval($item['vat_value']) : null;

            if(!empty($vat_value_item)){
                $vat_item = $price / (100 + $vat_value_item) * $vat_value_item;
                $price_origin = $price - $vat_item;
                $total_vat_item = $vat_item * $quantity;
            }

            $discount_value_item = !empty($item['discount_value']) ? floatval($item['discount_value']) : null;
            $discount_type_item = null;
            $total_discount_item = 0;
            // tính giá sau discount
            if(!empty($discount_value_item)){
                $discount_type_item = !empty($item['discount_type']) ? $item['discount_type'] : null;
                if($discount_type_item == PERCENT){
                    $discount_item = $price_origin / (100 - $discount_value_item) * $discount_value_item;
                }else{
                    $discount_item = $discount_value_item;
                }
                $price_origin = $price + $discount_item;
                $total_discount_item = $discount_item * $quantity;
            }

            $orders_item[] = [
                'id' => !empty($item['id']) ? intval($item['id']) : null,
                'product_id' => $product_id,
                'product_item_id' => $product_item_id,
                'quantity' => $quantity,
                'price' => $this->Utilities->formatToDecimal($price),
                'discount_type' => $discount_type_item,
                'discount_value' => $this->Utilities->formatToDecimal($discount_value_item),                                
                'total_discount' => $this->Utilities->formatToDecimal($total_discount_item),
                'vat_value' => $this->Utilities->formatToDecimal($vat_value_item),
                'total_vat' => $this->Utilities->formatToDecimal($total_vat_item),
                'total_item' => $this->Utilities->formatToDecimal($total_item)
            ];


            $total_vat += $total_vat_item;
            $total_items += $total_item;


            $number_items ++;
            $count_items += $quantity;
            $total_discount_items += $total_discount_item;
        }

        $total = $total_discount = $total_other_service = 0;

        // total discount
        $discount_type = $discount_note = null;
        $discount_value = !empty($data['discount_value']) ? floatval($data['discount_value']) : null;
        if(!empty($discount_value)){
            $discount_type = !empty($data['discount_type']) ? $data['discount_type'] : null;
            $discount_note = !empty($data['discount_note']) ? $data['discount_note'] : null;

            if($discount_type == PERCENT){
                $total_discount = $total_items  * $discount_value / 100;
            }else{
                $total_discount = $discount_value;
            }
        }

        //total coupon
        $total_coupon = !empty($data['total_coupon']) ? floatval($data['total_coupon']) : 0;

        // total affiliate
        $total_affiliate = !empty($data['total_affiliate']) ? floatval($data['total_affiliate']) : 0;
                
        // parse data shipping when create order
        $data_shipping = [];
        $shipping_fee_customer = $shipping_fee = $carrier_shipping_fee = $cod_money = $shipping_note = null;
        $carrier_code = !empty($data['carrier_code']) ? $data['carrier_code'] : null;
        $shipping_method = !empty($data['shipping_method']) ? $data['shipping_method'] : null;
        $shipping_method_id = !empty($data['shipping_method_id']) ? intval($data['shipping_method_id']) : null;
        $shipping_fee_customer = !empty($data['shipping_fee_customer']) ? floatval(str_replace(',', '', $data['shipping_fee_customer'])) : 0;
        if(!empty($shipping_method)){            
            $shipping_fee = !empty($data['shipping_fee']) ? floatval(str_replace(',', '', $data['shipping_fee'])) : 0;
            $carrier_shipping_fee = !empty($data['carrier_shipping_fee']) ? floatval(str_replace(',', '', $data['carrier_shipping_fee'])) : 0;
            $cod_money = !empty($data['cod_money']) ? floatval(str_replace(',', '', $data['cod_money'])) : 0;
            $shipping_note = !empty($data['shipping_note']) ? $data['shipping_note'] : null;
        }

        if(!empty($data['create_shipping']) && !empty($shipping_method) && empty($id)){
            $result_shipping = $this->Shipping->formatDataBeforeSave($data, $orders_contact);
            if(!empty($result_shipping[CODE]) && $result_shipping[CODE] == SUCCESS){
                $data_shipping = !empty($result_shipping[DATA]) ? $result_shipping[DATA] : [];
            }
            
            if(!empty($data['created_by'])){
                $data_shipping['created_by'] = intval($data['created_by']);
            }
        }

        // total
        $total = $total_items - $total_discount - $total_affiliate + $total_vat + $shipping_fee_customer;
        if($total < 0) $total = 0;

        // parse data payment when create order
        $paid = $debt = $cod_paid = $cash_paid = $bank_paid = $credit_paid = $gateway_paid = $voucher_paid = $point_paid = $point_promotion_paid = 0; 

        $data_payment = [];
        if(!empty($data['create_payment']) && empty($id)){
            $paid = floatval(str_replace(',', '', $data['amount']));
            $payment_method = !empty($data['payment_method']) ? $data['payment_method'] : null;

            $data_payment = [
                'type' => isset($data['payment_type']) ? intval($data['payment_type']) : 1, // 1 => THU , 0 => CHI
                'object_type' => CUSTOMER,
                'object_id' => !empty($orders_contact['customer_id']) ? intval($orders_contact['customer_id']) : null,
                'amount' => $paid,
                'payment_method' => $payment_method,
                'payment_time' => !empty($data['payment_time']) ? $data['payment_time'] : null,
                'reference' => !empty($data['reference']) ? $data['reference'] : null,
                'full_name' => !empty($orders_contact['full_name']) ? $orders_contact['full_name'] : null,
                'status' => 1
            ];

            if(!empty($data['created_by'])){
                $data_payment['created_by'] = intval($data['created_by']);
            }

            $cash_paid = !empty($payment_method) && $payment_method == CASH ? $paid : 0;
            $bank_paid = !empty($payment_method) && $payment_method == BANK ? $paid : 0;
            $credit_paid = !empty($payment_method) && $payment_method == CREDIT ? $paid : 0;
            $cod_paid = !empty($payment_method) && $payment_method == COD ? $paid : 0;
            $gateway_paid = !empty($payment_method) && $payment_method == GATEWAY ? $paid : 0;
            $voucher_paid = !empty($payment_method) && $payment_method == VOUCHER ? $paid : 0;
        }

        // parse data payment COD
        $data_payment_cod = [];
        if(!empty($cod_money) && !empty($data['create_shipping']) && !empty($shipping_method) && empty($id)){
            $data_payment_cod = [
                'type' => isset($data['payment_type']) ? intval($data['payment_type']) : 1, // 1 => THU , 0 => CHI
                'object_type' => CUSTOMER,
                'object_id' => !empty($orders_contact['customer_id']) ? intval($orders_contact['customer_id']) : null,
                'amount' => $cod_money,
                'payment_method' => COD,
                'payment_time' => !empty($data['payment_time']) ? $data['payment_time'] : null,
                'reference' => !empty($data['reference']) ? $data['reference'] : null,
                'full_name' => !empty($orders_contact['full_name']) ? $orders_contact['full_name'] : null,
                'status' => 2
            ];
        }

        // parse data payment by point
        if(!empty($data['point']) || !empty($data['point_promotion'])){
            $point_paid = !empty($data['point_paid']) ? floatval($data['point_paid']) : 0;
            $point_promotion_paid = !empty($data['point_promotion_paid']) ? floatval($data['point_promotion_paid']) : 0;
        }


        // get paid of order
        $payments_table = TableRegistry::get('Payments');
        $paid += $payments_table->getTotalPaidOrder($id);
        $cash_paid += $payments_table->getTotalPaidOrder($id, ['payment_method' => CASH]);
        $bank_paid += $payments_table->getTotalPaidOrder($id, ['payment_method' => BANK]);
        $credit_paid += $payments_table->getTotalPaidOrder($id, ['payment_method' => CREDIT]);
        $cod_paid += $payments_table->getTotalPaidOrder($id, ['payment_method' => COD]);
        $gateway_paid += $payments_table->getTotalPaidOrder($id, ['payment_method' => GATEWAY]);
        $voucher_paid += $payments_table->getTotalPaidOrder($id, ['payment_method' => VOUCHER]);

        $paid += $point_paid;
        $paid += $point_promotion_paid;

        $debt = $total - $paid;
        if($debt < 0) $debt = 0;

        if(!empty($data['create_shipping']) && $shipping_method == RECEIVED_AT_STORE && empty($id)){
            $cod_money = $debt;
            $data_shipping['cod_money'] = $debt;            
        }

        // parse data order
        $order = [
            'id' => $id,
            'type' => !empty($data['type']) ? $data['type'] : ORDER,
            'code' => !empty($data['code']) ? $data['code'] : null,
            'source' => !empty($data['source']) ? $data['source'] : null,
            'note' => !empty($data['note']) ? $data['note'] : null,
            'related_order_id' => !empty($data['related_order_id']) ? intval($data['related_order_id']) : null,
            'branch_id' => !empty($data['branch_id']) ? intval($data['branch_id']) : null,
            'staff_id' => !empty($data['staff_id']) ? intval($data['staff_id']) : null,
            'staff_note' => !empty($data['staff_note']) ? $data['staff_note'] : null,
            'number_items' => $number_items,
            'count_items' => $count_items,
            'coupon_code' => !empty($data['coupon_code']) ? $data['coupon_code'] : null,
            'voucher_code' => !empty($data['voucher_code']) ? $data['voucher_code'] : null,
            'voucher_value' => !empty($data['voucher_value']) ? $this->Utilities->formatToDecimal($data['voucher_value']) : NUMBER_EMPTY,
            'promotion_id' => !empty($data['promotion_id']) ? intval($data['promotion_id']) : null,
            'discount_type' => $discount_type,
            'discount_value' => $this->Utilities->formatToDecimal($discount_value),
            'affiliate_discount_type' => !empty($data['affiliate_discount_type']) ? $data['affiliate_discount_type'] : null,
            'affiliate_discount_value' => !empty($data['affiliate_discount_value']) ? $this->Utilities->formatToDecimal(floatval($data['affiliate_discount_value'])) : null,
            'affiliate_code' => !empty($data['affiliate_code']) ? $data['affiliate_code'] : null,
            'discount_note' => $discount_note,           
            'shipping_method_id' => $shipping_method_id,
            'shipping_method' => $shipping_method,
            'shipping_fee_customer' => $this->Utilities->formatToDecimal($shipping_fee_customer),
            'shipping_fee_partner' => $this->Utilities->formatToDecimal($carrier_shipping_fee),
            'shipping_fee' => $this->Utilities->formatToDecimal($shipping_fee),
            'shipping_note' => $shipping_note,
            'cod_money' => $this->Utilities->formatToDecimal($cod_money),
            'total_coupon' => $this->Utilities->formatToDecimal($total_coupon),
            'total_discount' => $this->Utilities->formatToDecimal($total_discount),
            'total_affiliate' => $this->Utilities->formatToDecimal($total_affiliate),
            'total_vat' => $this->Utilities->formatToDecimal($total_vat),
            'total_other_service' => $this->Utilities->formatToDecimal($total_other_service),
            'total_discount_items' => $this->Utilities->formatToDecimal($total_discount_items),
            'total' => $this->Utilities->formatToDecimal($total),
            'total_items' => $this->Utilities->formatToDecimal($total_items),
            'total_origin' => $this->Utilities->formatToDecimal($total_items),
            'paid' => $this->Utilities->formatToDecimal($paid),
            'debt' => $this->Utilities->formatToDecimal($debt),
            'cod_paid' => $this->Utilities->formatToDecimal($cod_paid),
            'cash_paid' => $this->Utilities->formatToDecimal($cash_paid),
            'bank_paid' => $this->Utilities->formatToDecimal($bank_paid),
            'credit_paid' => $this->Utilities->formatToDecimal($credit_paid),
            'gateway_paid' => $this->Utilities->formatToDecimal($gateway_paid),
            'voucher_paid' => $this->Utilities->formatToDecimal($voucher_paid),
            'point_paid' => $this->Utilities->formatToDecimal($point_paid),
            'point_promotion_paid' => $this->Utilities->formatToDecimal($point_promotion_paid),
            'point' => !empty($data['point']) ? intval($data['point']) : 0,
            'point_promotion' => !empty($data['point_promotion']) ? intval($data['point_promotion']) : 0,
            'status' => !empty($data['status']) ? $data['status'] : null
        ];

        if(!empty($data['created_by'])){
            $order['created_by'] = intval($data['created_by']);
        }

        $date_create = date('H:i - d/m/Y');
        if(isset($data['date_create']) && $this->Utilities->isDateTimeClient($data['date_create'])){
            $date_create = $data['date_create'];
        }
        $order['date_create'] = $this->Utilities->stringDateTimeClientToInt($date_create);

        if(isset($data['date_received']) && $this->Utilities->isDateTimeClient($data['date_received'])){
            $order['date_received'] = $this->Utilities->stringDateTimeClientToInt($data['date_received']);
        }

        if((empty($order['status']) || $order['status'] == NEW_ORDER) && !empty($data_payment) && empty($id)){
            $order['status'] = CONFIRM;
        }

        if((empty($order['status']) || $order['status'] == NEW_ORDER || CONFIRM) && !empty($data_shipping) && empty($id)){
            $order['status'] = PACKAGE;
        }

        $result['order'] = $order;
        $result['items'] = $orders_item;
        $result['contact'] = $orders_contact;
        $result['payment'] = $data_payment;
        $result['payment_cod'] = $data_payment_cod;
        $result['shipping'] = $data_shipping;

        return $result;
    }

    public function updateQuantityAvailableOfProduct($order_id = null, $clear = false)
    {
        $settings = TableRegistry::get('Settings')->getSettingByGroup('order');        
        if(empty($settings['minus_quantity_product'])) return true;        

        $table = TableRegistry::get('ProductsItem');
        $order_table = TableRegistry::get('Orders');
        $order_item_table = TableRegistry::get('OrdersItem');

        // validate info order
        if(empty($order_id)) return false;

        $order_info = $order_table->find()->where([
            'id' => $order_id, 
            'deleted' => 0
        ])->select(['id', 'type', 'status'])->first();
        if(empty($order_info)) return false;

        $order_status = !empty($order_info['status']) ? $order_info['status'] : null;
        if(empty($order_status)) return false;

        $orders_item = $order_item_table->find()->where([
            'order_id' => $order_id
        ])->select(['id', 'product_item_id', 'quantity'])->toArray();
        if(empty($orders_item)) return false;


        // check conditions update quantity
        $accept = false;
    
        $conditions = !empty($settings['quantity_conditions']) ? $settings['quantity_conditions'] : 'create_new';       
        if($conditions == 'create_new' && in_array($order_status, [NEW_ORDER, CONFIRM])){
            $accept = true;
        }

        if($conditions == CONFIRM && in_array($order_status, [CONFIRM, PACKAGE])){
            $accept = true;
        }

        if($conditions == EXPORT && $order_status == EXPORT){
            $accept = true;
        }

        if($order_status == CANCEL){
            $accept = true;
            $clear = true;
        }

        if(!$accept && !$clear) return true;

        foreach($orders_item as $item){
            $order_item_id = !empty($item['id']) ? intval($item['id']) : null;
            $product_item_id = !empty($item['product_item_id']) ? intval($item['product_item_id']) : null;
            $quantity = !empty($item['quantity']) ? intval($item['quantity']) : 0;

            if(empty($product_item_id) || empty($order_item_id)) return false;

            // get info order_item
            $order_item_info = $order_item_table->find()->where([ 'id' => $order_item_id])->select(['id', 'quantity_apply'])->first();
            if(empty($order_item_info)) return false;

            $quantity_apply = !empty($order_item_info['quantity_apply']) ? intval($order_item_info['quantity_apply']) : 0;

            // nếu quantity_apply > 0 và bằng quantity (tức là số lượng này đã được trừ ở quantity_available của sp rồi) thì ko cập nhật lại số lượng nữa
            if(!empty($quantity_apply) && $quantity_apply == $quantity && !$clear) return true;
            if(empty($quantity_apply) && $clear) return true;

            // get info product_item
            $item_info = $table->find()->where(['id' => $product_item_id, 'deleted' => 0])->select(['id', 'quantity_available'])->first();
            if(empty($item_info)) return false;
                        
            $quantity_available = !empty($item_info['quantity_available']) ? $item_info['quantity_available'] : 0;

            if($order_info['type'] == ORDER){                
                $quantity_available = $clear ? $quantity_available + $quantity : $quantity_available - $quantity;
            }else{
                $quantity_available = $clear ? $quantity_available - $quantity : $quantity_available + $quantity;
            }            

            // trường hợp quantity_apply > 0 và không bằng quantity (tức là cập nhật lại số lượng sp trong đơn hàng) => ta cộng lại số lượng chênh và cập nhật lại vào quantity_available
            if(!empty($quantity_apply) && $quantity_apply != $quantity && !$clear){
                $quantity_available = $quantity_available + $quantity_apply;
            }

            $item_entity = $table->patchEntity($item_info, ['quantity_available' => $quantity_available]);
            $order_item_entity = $table->patchEntity($order_item_info, ['quantity_apply' => $clear ? null : $quantity]);
            
            try{
                $table->save($item_entity);

                $order_item_table->save($order_item_entity);
            }catch (Exception $e) {
                return false;
            }
        }

        
        return true;
    }

    public function cancelOrder($order_id = null)
    {
        if(empty($order_id)) {
            return $this->System->getResponse([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_don_hang')]);
        }

        $orders_table = TableRegistry::get('Orders');
        $shippings_table = TableRegistry::get('Shippings');
        $payments_table = TableRegistry::get('Payments');
        $products_item_table = TableRegistry::get('ProductsItem');
        $orders_item_table = TableRegistry::get('OrdersItem');

        $order = $orders_table->getDetailOrder($order_id, [
            'get_items' => true,
            'get_contact' => true
        ]);

        $order_info = $orders_table->formatDataOrderDetail($order, LANGUAGE_DEFAULT);
        if(empty($order_info)){
            return $this->System->getResponse([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_don_hang')]);
        }

        if(empty($order_info['type']) || $order_info['type'] != ORDER){
            return $this->System->getResponse([MESSAGE => __d('admin', 'thong_tin_don_hang_khong_hop_le')]);
        }

        // patch entity data shipping
        $shipping = $shippings_table->find()->where([
            'Shippings.order_id' => $order_id,
            'Shippings.status !=' => CANCEL_DELIVERED
        ])->order('Shippings.id DESC')->first(); 

        $data_shipping = [];
        if(!empty($shipping)){
            $data_shipping = $shippings_table->patchEntity($shipping, [
                'status' => CANCEL_DELIVERED
            ]);            
        }

        // patch entities data payment
        $payments = $payments_table->queryListPayments([
            FILTER => [
                'foreign_id' => $order_id, 
                'foreign_type' => ORDER,
                'status' => 1
            ],
            SORT => [
                FIELD => 'id',
                SORT => ASC
            ]
        ])->toArray();

        $data_payment = [];
        if(!empty($payments)){
            $patch_payment = [];
            foreach ($payments as $key => $payment) {
                $payment_id = !empty($payment['id']) ? intval($payment['id']) : null;
                $patch_payment[] = [
                    'id' => $payment_id,
                    'status' => 0
                ];
            }
            $data_payment = $payments_table->patchEntities($payments, $patch_payment);
        }

        // parse data order
        $data_order = $order_info;
        $data_order['status'] = CANCEL;
        $data_order['shipping_method'] = null;
        $data_order['shipping_fee_customer'] = NUMBER_EMPTY;
        $data_order['shipping_fee_partner'] = NUMBER_EMPTY;
        $data_order['shipping_fee'] = NUMBER_EMPTY;
        $data_order['cod_money'] = NUMBER_EMPTY;
        $data_order['shipping_note'] = null;

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            // update shipping
            if(!empty($data_shipping)){
                $update_shipping = $shippings_table->save($data_shipping);            
                if (empty($update_shipping->id)){
                    throw new Exception();
                }
            }
            
            // update payments
            if(!empty($data_payment)){
                $update_payments = $payments_table->saveMany($data_payment);
                if (empty($update_payments)){
                    throw new Exception();
                }
            }

            // update order
            $update_order = $this->Order->saveOrder($data_order, $order_id);
            if($update_order[CODE] == ERROR){
                throw new Exception(!empty($update_order[MESSAGE]) ? $update_order[MESSAGE] : null);
            }

            // update quantity product after cancel order
            $update_quantity_available = $this->updateQuantityAvailableOfProduct($order_id, true);
            if(!$update_quantity_available){
                throw new Exception(__d('admin', 'cap_nhat_so_luong_san_pham_khong_thanh_cong'));
            }

            // cộng điểm thưởng cho đối tác
            // check thông tin đơn hàng có áp dụng mã giới thiệu không để cộng điểm cho đối tác
            $affiliate_code = !empty($order_info['affiliate_code']) ? $order_info['affiliate_code'] : null;
            $exist_coupon = !empty($order_info['coupon_code']) ? true : false;
            
            if (!empty($affiliate_code)) {
                $this->CustomersPoint->refundPointOrderPartner($order_id, $affiliate_code, $exist_coupon);
            }
            
            $conn->commit();
            return $this->System->getResponse([CODE => SUCCESS, DATA => ['id' => $order_id]]);

        }catch (Exception $e) {
            $conn->rollback();
            return $this->System->getResponse([MESSAGE => $e->getMessage()]);
        }
    }
}
