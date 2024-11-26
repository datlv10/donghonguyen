<?php

namespace Admin\Controller;

use Admin\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Http\Response;
use Cake\ORM\Query;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;

class CurrencyController extends AppController {

    public function initialize(): void
    {
        parent::initialize();        
    }

    public function list() 
    {
        $this->js_page = '/assets/js/pages/list_currency.js';

        $this->set('path_menu', 'setting');
        $this->set('title_for_layout', __d('admin', 'tien_te'));   
    }

    public function listJson()
    {
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $table = TableRegistry::get('Currencies');
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

        // params         
        $params[SORT] = !empty($data[SORT]) ? $data[SORT] : [];

        // page and limit
        $page = !empty($data[PAGINATION][PAGE]) ? intval($data[PAGINATION][PAGE]) : 1;
        $limit = !empty($data[PAGINATION][PERPAGE]) ? intval($data[PAGINATION][PERPAGE]) : PAGINATION_LIMIT_ADMIN;

        // sort 
        $sort_field = !empty($params[SORT][FIELD]) ? $params[SORT][FIELD] : null;
        $sort_type = !empty($params[SORT][SORT]) ? $params[SORT][SORT] : null;

        try {
            $brands = $this->paginate($table->queryListCurrencies($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();

        } catch (Exception $e) {
            $page = 1;
            $brands = $this->paginate($table->queryListCurrencies($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        }

        $pagination_info = !empty($this->request->getAttribute('paging')['Currencies']) ? $this->request->getAttribute('paging')['Currencies'] : [];
        $meta_info = $utilities->formatPaginationInfo($pagination_info);

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $brands, 
            META => $meta_info
        ]);
    }

    public function add()
    {
        $this->js_page = [
            '/assets/js/pages/currency.js',
        ];

        $this->set('path_menu', 'setting');
        $this->set('title_for_layout', __d('admin', 'them_tien_te'));
        $this->render('update');
    }

    public function update($id = null)
    {
        $currency = TableRegistry::get('Currencies')->get($id);

        if(empty($currency)){
            $this->showErrorPage();
        }
    
        $this->set('id', $id);
        $this->set('currency', $currency);

        $this->js_page = [
            '/assets/js/pages/currency.js',
        ];
        $this->set('path_menu', 'setting');
        $this->set('title_for_layout', __d('admin', 'cap_nhat'));
    }

    public function save($id = null)
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }
        
        $utilities = $this->loadComponent('Utilities');
        $table = TableRegistry::get('Currencies');

        $data['exchange_rate'] = !empty($data['exchange_rate']) ? str_replace(',', '', $data['exchange_rate']) : 0;
        // merge data with entity          
        if(empty($id)){
            $currency = $table->newEntity($data);
        }else{    
            $currency = $table->get($id);
            $currency = $table->patchEntity($currency, $data);
        }

        // show error validation in model
        if($currency->hasErrors()){        
            $list_errors = $utilities->errorModel($currency->getErrors());
            $this->responseJson([
                MESSAGE => !empty($list_errors[0]) ? $list_errors[0] : null,
                DATA => $list_errors
            ]);
        }

        try{
            // save data
            $save = $table->save($currency);
            if (empty($save->id)){
                throw new Exception();
            }
            $this->responseJson([CODE => SUCCESS, DATA => ['id' => $save->id]]);

        }catch (Exception $e) {
            $this->responseJson([MESSAGE => $e->getMessage()]);
        }
    }

    public function delete()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        $ids = !empty($data['ids']) ? $data['ids'] : [];
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        if (empty($ids) || !is_array($ids)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('Currencies');

        $currencies = [];
        foreach ($ids as $k => $currency_id) {
            $currency_info = $table->get($currency_id);
            if(!empty($currency_info['is_default'])){
                $this->responseJson([MESSAGE => __d('admin', 'khong_the_xoa_loai_tien_te_mac_dinh')]);
            }

            $currencies[] = $currency_info;
        }

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $delete = $table->deleteMany($currencies);
            if (empty($delete)){
                throw new Exception();
            }
            
            $conn->commit();
            $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'cap_nhat_thanh_cong')]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function changeStatus()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = !empty($this->request->getData()) ? $this->request->getData() : [];
        $ids = !empty($data['ids']) ? $data['ids'] : [];
        $status = !empty($data['status']) ? 1 : 0;

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        if (empty($ids) || !is_array($ids)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('Currencies');

        $currencies = $table->find()->where([
            'Currencies.id IN' => $ids
        ])->select(['Currencies.id', 'Currencies.status'])->toArray();
        
        if(empty($currencies)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_ban_ghi')]);
        }

        $patch_data = [];
        foreach ($ids as $k => $currency_id) {
            $patch_data[] = [
                'id' => $currency_id,
                'status' => $status
            ];
        }

        $data_currencies = $table->patchEntities($currencies, $patch_data, ['validate' => false]);
        
        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $change_status = $table->saveMany($data_currencies);            
            if (empty($change_status)){
                throw new Exception();
            }
            
            $conn->commit();
            $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'cap_nhat_thanh_cong')]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function isDefault()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        $id = !empty($data['id']) ? intval($data['id']) : null;

        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        if (empty($id)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }
        
        $data = ['is_default' => 1];

        $table = TableRegistry::get('Currencies');    
        $currency = $table->get($id);
        $currency = $table->patchEntity($currency, $data, ['validate' => false]);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $table->updateAll(
                [  
                    'is_default' => 0
                ],
                [  
                    'is_default' => 1
                ]
            );

            $save = $table->save($currency);
            if(empty($save->id)) {
                throw new Exception();
            }
            
            $conn->commit();
            $this->responseJson([CODE => SUCCESS, DATA => ['id' => $save->id]]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }
}