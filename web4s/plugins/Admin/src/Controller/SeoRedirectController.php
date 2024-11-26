<?php

namespace Admin\Controller;

use Admin\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;

class SeoRedirectController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
    }

    public function list()
    {    
        $this->js_page = [
            '/assets/js/pages/list_seo_redirect.js'
        ];

        $this->set('title_for_layout', __d('admin', 'chuyen_huong_{0}', ['301']));
        $this->set('path_menu', 'seo_redirect');
    }

    public function listJson()
    {
        if (!$this->getRequest()->is('post')) {
            $this->responseJson([MESSAGE => __d('admin', 'phuong_thuc_khong_hop_le')]);
        }

        $table = TableRegistry::get('SeoRedirects');
        $utilities = $this->loadComponent('Utilities');

        $data = $params = $redirects = [];

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
            $redirects = $this->paginate($table->queryListSeoRedirects($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        } catch (Exception $e) {
            $page = 1;
            $redirects = $this->paginate($table->queryListSeoRedirects($params), [
                'limit' => $limit,
                'page' => $page,
                'order' => [
                    $sort_field => $sort_type
                ]
            ])->toArray();
        }

        $pagination_info = !empty($this->request->getAttribute('paging')['SeoRedirect']) ? $this->request->getAttribute('paging')['SeoRedirect'] : [];
        $meta_info = $utilities->formatPaginationInfo($pagination_info);

        $this->responseJson([
            CODE => SUCCESS,
            MESSAGE => __d('admin', 'xu_ly_du_lieu_thanh_cong'),
            DATA => $redirects, 
            META => $meta_info
        ]);
    }

    public function add()
    {
        $this->js_page = [
            '/assets/js/pages/seo_redirect.js',
        ];

        $this->set('title_for_layout', __d('admin', 'them_chuyen_huong_{0}', ['301']));
        $this->set('path_menu', 'seo_redirect');
        $this->render('update');
    }

    public function update($id = null)
    {
        $redirect = TableRegistry::get('SeoRedirects')->find()->contain(['User'])->where(['SeoRedirects.id' => $id])->first();
        if(empty($redirect)){
            $this->showErrorPage();
        }

        $this->set('id', $id);
        $this->set('redirect', $redirect);

        $this->js_page = [
            '/assets/js/pages/seo_redirect.js',
        ];

        $this->set('title_for_layout', __d('admin', 'cap_nhat_chuyen_huong_{0}', ['301']));
        $this->set('path_menu', 'seo_redirect');
        $this->render('update');
    }

    public function save($id = null)
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();

        if (!$this->getRequest()->is('post') || empty($data)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }
        
        $utilities = $this->loadComponent('Utilities');
        $table = TableRegistry::get('SeoRedirects');

        if(!empty($id)){
            $redirect = TableRegistry::get('SeoRedirects')->find()->where(['id' => $id])->first();

            if(empty($redirect)){
                $this->responseJson([MESSAGE => __d('admin', 'khong_lay_duoc_thong_tin_ban_ghi')]);
            }
        }

        $url = !empty($data['url']) ? $data['url'] : null;

        // validate data
        if(empty($url)){
            $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_duong_dan')]);
        }

        if(empty($data['redirect'])){
            $this->responseJson([MESSAGE => __d('admin', 'vui_long_nhap_duong_dan_chuyen_huong')]);
        }

        // check url exist
        $where_check = ['url' => $url];
        if(!empty($id)){
            $where_check['id !='] = $id;
        }

        $check_exist = $table->find()->where($where_check)->select(['id'])->first();
        if(!empty($check_exist)){
            $this->responseJson([MESSAGE => __d('admin', 'duong_dan_da_ton_tai_tren_he_thong')]);
        }

        $data['redirect'] = $utilities->formatToUrl(trim($data['redirect']));
        $data_save = [
            'id' => $id,
            'url' => $url,
            'redirect' => !empty($data['redirect']) ? $data['redirect'] : null,          
            'search_unicode' => strtolower($utilities->formatSearchUnicode([str_replace('-', ' ', $data['url']), str_replace('-', ' ', $data['redirect'])]))
        ];

        // merge data with entity 
        if(empty($id)){
            $data_save['created_by'] = $this->Auth->user('id');
            $redirect = $table->newEntity($data_save);
        }else{            
            $redirect = $table->patchEntity($redirect, $data_save);
        }

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();
            
            $save = $table->save($redirect);
            if (empty($save->id)){
                throw new Exception();
            }

            $conn->commit();
            $this->responseJson([CODE => SUCCESS, DATA => ['id' => $save->id]]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function delete()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        $ids = !empty($data['ids']) ? $data['ids'] : [];

        if (!$this->getRequest()->is('post') || empty($ids) || !is_array($ids)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('SeoRedirects');

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            foreach($ids as $id){
                $redirect = $table->get($id);
                $delete = $table->delete($redirect);

                if (empty($delete)){
                    throw new Exception();
                }
            }

            $conn->commit();
            $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'xoa_du_lieu_thanh_cong')]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }

    public function changeStatus()
    {
        $this->layout = false;
        $this->autoRender = false;

        $data = $this->getRequest()->getData();
        $ids = !empty($data['ids']) ? $data['ids'] : [];
        $status = !empty($data['status']) ? 1 : 0;

        if (!$this->getRequest()->is('post') || empty($ids) || !is_array($ids)) {
            $this->responseJson([MESSAGE => __d('admin', 'du_lieu_khong_hop_le')]);
        }

        $table = TableRegistry::get('SeoRedirects');

        $redirects = $table->find()->where([
            'id IN' => $ids
        ])->select(['id', 'status'])->toArray();
        
        if(empty($redirects)){
            $this->responseJson([MESSAGE => __d('admin', 'khong_tim_thay_thong_tin_bai_viet')]);
        }

        $patch_data = [];
        foreach ($ids as $k => $redirect_id) {
            $patch_data[] = [
                'id' => $redirect_id,
                'status' => $status
            ];
        }

        $data_redirects = $table->patchEntities($redirects, $patch_data);

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();

            $update = $table->saveMany($data_redirects);            
            if (empty($update)){
                throw new Exception();
            }
            
            $conn->commit();
            $this->responseJson([CODE => SUCCESS, MESSAGE => __d('admin', 'cap_nhat_thanh_cong')]);

        }catch (Exception $e) {
            $conn->rollback();
            $this->responseJson([MESSAGE => $e->getMessage()]);  
        }
    }
}
