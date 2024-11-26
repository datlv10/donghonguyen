<?php

namespace Admin\Controller;

use Admin\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Utility\Hash;
use Cake\Http\Response;
use Cake\ORM\Query;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;

class ShipmentController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function list()
    {
        $this->js_page = '/assets/js/pages/list_shipment.js';

        $this->set('path_menu', 'shipment');
        $this->set('title_for_layout', __d('admin', 'danh_sach_don_van'));
    }

    public function listJson()
    {
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $table = TableRegistry::get('Shippings');
        $utilities = $this->loadComponent('Utilities');

        $data = $params = $shipment = [];

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

        // params         
        $params[SORT] = !empty($data[SORT]) ? $data[SORT] : [];

        // page and limit
        $page = !empty($data[PAGINATION][PAGE]) ? intval($data[PAGINATION][PAGE]) : 1;
        $limit = !empty($data[PAGINATION][PERPAGE]) ? intval($data[PAGINATION][PERPAGE]) : PAGINATION_LIMIT_ADMIN;


        // sort 
        $sort_field = !empty($params[SORT][FIELD]) ? $params[SORT][FIELD] : null;
        $sort_type = !empty($params[SORT][SORT]) ? $params[SORT][SORT] : null;

        try {
            $shipment = $this->paginate($table->queryListShippings($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        } catch (Exception $e) {
            $page = 1;
            $shipment = $this->paginate($table->queryListShippings($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        }

        $pagination_info = !empty($this->request->getAttribute('paging')['Shippings']) ? $this->request->getAttribute('paging')['Shippings'] : [];
        $meta_info = $utilities->formatPaginationInfo($pagination_info);

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $shipment, 
            META => $meta_info
        ]);
    }

    public function detail($id = null)
    {
        $shipping = TableRegistry::get('Shippings')->getDetailShippings($id);
        if(empty($shipping)){
            $this->showErrorPage();
        }

        $order = [];
        if(!empty($shipping['order_id'])){
            $orders_table = TableRegistry::get('Orders'); 
            $order = $orders_table->getDetailOrder($shipping['order_id'], [
                'get_items' => true, 
                'get_contact' => false,
                'get_staff' => false,
            ]);
            $order = $orders_table->formatDataOrderDetail($order, $this->lang);
        }
        
        
        $this->set('shipping', $shipping);
        $this->set('order', $order);

        $this->js_page = [
            '/assets/js/pages/shipment_detail.js',
        ];
        $this->set('path_menu', 'shipment');
        $this->set('title_for_layout', __d('admin', 'chi_tiet_don_van'));
    }

}