<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;
use Cake\Core\Exception\Exception;
use Cake\Cache\Cache;

class NotificationFrontendComponent extends Component
{
    public $controller = null;
    public $components = ['System', 'Utilities', 'PaginatorExtend', 'ReCaptcha'];

    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->controller = $this->_registry->getController();
    }

    public function listNotifications($data = [], $options = [])
    {
        if(empty($data)){
            return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }

        $api = !empty($options['api']) ? true : false;

        // check recaptcha
        if(!$api){
            $token = !empty($data[TOKEN_RECAPTCHA]) ? $data[TOKEN_RECAPTCHA] : null;
            $check_recaptcha = $this->ReCaptcha->check($token);
            if($check_recaptcha[CODE] != SUCCESS){
               return $this->System->getResponse([MESSAGE => $check_recaptcha[MESSAGE]]);
            }
        }

        $request = $this->controller->getRequest();

        $page = !empty($data['page']) ? intval($data['page']) : 1;
        $type = !empty($data['type']) ? $data['type'] : null;
        $group = !empty($data['group']) ? $data['group'] : null;
        if(empty($type) || !in_array($type, [ALL, WEBSITE, MOBILE_APP])){
            return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }
        
        if(!empty($group) && !in_array($group, ['sale', 'news', 'order'])){
            return $this->System->getResponse([MESSAGE => __d('template', 'du_lieu_khong_hop_le')]);
        }
    
        $notification_news = $notification_order = [];
        
        // lấy thông báo chung
        if($page == 1){
            // cache danh sách thông báo của trang đầu tiên
            $cache_key = NOTIFICATION . '_' . $type . '_list';
            $notification_news = Cache::read($cache_key);

            if(is_null($notification_news)){
                $notification_news = $this->getNotifications($type, $page);
                Cache::write($cache_key, !empty($notification_news) ? $notification_news : []);
            }
        }else{
            $notification_news = $this->getNotifications($type, $page);
        }

        // lấy thông báo về đơn hàng
        if(empty($group) || $group == 'order'){
            $member = $request->getSession()->read(MEMBER);
            $customer_id = !empty($member['customer_id']) ? intval($member['customer_id']) : null;

            if(!empty($customer_id)) {
                $params = [
                    'get_items' => true,
                    FILTER => [
                        TYPE => ORDER,
                        'customer_id' => $customer_id,
                        'status' => NEW_ORDER
                    ]
                ];

                $orders_table = TableRegistry::get('Orders');
                try {
                    $orders = $this->PaginatorExtend->paginate($orders_table->queryListOrders($params), [
                        'limit' => 5,
                        'page' => $page
                    ])->toArray();

                } catch (Exception $e) {
                    $orders = $this->PaginatorExtend->paginate($orders_table->queryListOrders($params), [
                        'limit' => 5,
                        'page' => 1
                    ])->toArray();
                }

                $notifications = [];
                if(!empty($orders)){
                    $products_item_table = TableRegistry::get('ProductsItem');
                    foreach($orders as $order){
                        $order_code = !empty($order['code']) ? $order['code'] : null;
                        $created = !empty($order['created']) ? intval($order['created']) : null;
                        $product_item_id = !empty($order['OrdersItem'][0]['product_item_id']) ? intval($order['OrdersItem'][0]['product_item_id']) : null;
                        if(empty($order_code) || empty($product_item_id)) continue;
                        $product_item_info = $products_item_table->find()->where(['id' => $product_item_id])->select(['id', 'images'])->first();
                        if(empty($product_item_info)) continue;
                        $images = !empty($product_item_info['images']) ? json_decode($product_item_info['images'], true) : [];

                        $notifications[] = [
                            'type' => $type,
                            'title' => __d('template', 'dat_hang_thanh_cong'),
                            'body' => __d('template', 'don_hang_{0}_cua_ban_da_dat_hang_thanh_cong_chung_toi_se_xac_nhan_va_gui_hang_den_cho_quy_khach_trong_thoi_gian_som_nhat', $order_code),                            
                            'image' => !empty($images[0]) ? $images[0] : null,
                            'link' => '/member/order/detail/'. $order_code,
                            'mobile_action' => null,
                            'created' => $created,
                            'status' => 1,
                            'extend' => [
                                'order_code' => $order_code
                            ]
                        ];
                    }
                }

                $pagination_info = !empty($this->controller->getRequest()->getAttribute('paging')['Orders']) ? $this->controller->getRequest()->getAttribute('paging')['Orders'] : [];
                $pagination = $this->Utilities->formatPaginationInfo($pagination_info);

                $notification_order = [
                    'notifications' => $notifications,
                    PAGINATION => $pagination
                ];
            }            
        }

        $result_data = [
            'news' => $notification_news,
            'orders' => $notification_order
        ];
        
        
        return $this->System->getResponse([
            CODE => SUCCESS,
            DATA => $result_data
        ]);
    }

    private function getNotifications($type = null, $page = 1)
    {
        $table = TableRegistry::get('Notifications');

        //param
        $limit = 10;
        $sort_field = 'id';
        $sort_type = 'DESC';

        $params = [
            SORT => [
                FIELD => $sort_field,
                SORT => $sort_type
            ],
            FILTER => [
                TYPE => $type,
                'status' => 1
            ]
        ];
        
        try {
            $notifications = $this->PaginatorExtend->paginate($table->queryListNotifications($params), [
                'limit' => $limit,
                'page' => $page
            ])->toArray();
        } catch (Exception $e) {
            $notifications = $this->PaginatorExtend->paginate($table->queryListNotifications($params), [
                'limit' => $limit,
                'page' => 1
            ])->toArray();
        }
        
        $pagination_info = !empty($this->controller->getRequest()->getAttribute('paging')['Notifications']) ? $this->controller->getRequest()->getAttribute('paging')['Notifications'] : [];
        $pagination = $this->Utilities->formatPaginationInfo($pagination_info);

        return [
            'notifications' => $notifications,
            PAGINATION => $pagination
        ];
    }
}
